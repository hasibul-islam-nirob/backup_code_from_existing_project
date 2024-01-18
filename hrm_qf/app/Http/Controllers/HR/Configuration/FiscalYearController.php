<?php

namespace App\Http\Controllers\HR\Configuration;

use DateTime;
use App\Model\HR\FiscalYear;
use App\Http\Controllers\Controller;
use App\Services\RoleService as Role;
use App\Http\Controllers\HR\Others\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\CommonService as Common;

class FiscalYearController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'fy_name'     => 'required',
                'fy_for'       => 'required',
                'fy_start_date' => 'required',
            );

            $attributes = array(
                'fy_name'     => 'Fiscal Year Name',
                'fy_for'       => 'Fiscal Year For',
                'fy_start_date' => 'Fiscal Year Start Date',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'store' || $operationType == 'update') {

            $StartDate = new DateTime($requestData['fy_start_date']);
            $StartDate = $StartDate->format('Y-m-d');

            $fy_for = $requestData['fy_for'];
            $if_exist = FiscalYear::where([['fy_name',$requestData['fy_name']], ['fy_for',$fy_for], ['fy_start_date',$StartDate]])->exists();

            if($if_exist) {
                $errorMsg = "Date Already Exist";
            }
        }

        if ($operationType == 'index') {

            $IgnoreArray = array();
            $CurrentSystemDate = (new DateTime())->format('Y-m-d');

            if ($requestData->is_active == 1 && $requestData->fy_start_date <= $CurrentSystemDate) { // only view
                $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit', 'send', 'btnHide' => true];
            }

            $errorMsg = $IgnoreArray;
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid' => $isValid,
            'message' => $errorMsg,
        );

        return $passport;
    }

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $totalRecords = FiscalYear::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = FiscalYear::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = FiscalYear::where('is_delete', 0)->orderBy('id', 'DESC')->get();

            $data = array();
            $DataSet = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');

                $fy_for = '';
                if($row->fy_for == 'LFY'){
                    $fy_for = 'Leave Fiscal Year';

                }elseif($row->fy_for == 'FFY'){
                    $fy_for = 'Finance Fiscal Year';

                }elseif($row->fy_for == 'BOTH'){
                    $fy_for = 'Both';
                }

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                = $sno;
                $data[$key]['fy_name']           = $row->fy_name;
                $data[$key]['fy_for']            = $fy_for;
                $data[$key]['fy_start_date']     = Common::viewDateFormat($row->fy_start_date);
                $data[$key]['fy_end_date']       = Common::viewDateFormat($row->fy_end_date);
                $data[$key]['company_id']        = $row->company['comp_name']; //name come from belongsTo company table
                $data[$key]['action']            = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row['id']), $IgnoreArray);

                $sno++;
            }

            $json_data = array(
                "draw"            => intval($draw),
                "recordsTotal"    => intval($totalRecords),
                "recordsFiltered" => intval($totalRecordswithFilter),
                "data"            => $data,
            );

            return response()->json($json_data);
        }
    }

    public function insert(Request $request)
    {

        $passport = $this->getPassport($request, $operationType = 'store');
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
            );
            return response()->json($notification);
        }

        DB::beginTransaction();
        try {

            $RequestData = $request->all();

                $StartDate                      = new DateTime($RequestData['fy_start_date']);
                $RequestData['fy_start_date']   = $StartDate->format('Y-m-d');
                $EndDate                        = $StartDate;
                $EndDate                        = $EndDate->modify('+1 year, -1 Day');
                $RequestData['fy_end_date']     = $EndDate->format('Y-m-d');

                FiscalYear::create($RequestData);

                DB::commit();

                return response()->json(
                    [
                        'message'    => "Data inserted successfully!!",
                        'status' => 'success',
                        'statusCode' => 200,
                        'result_data' => '',
                    ],
                    200
                );

        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            return response()->json([
                'message' => "Internal Server Error. Try Again!!",
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
                'error' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {

        $FiscalYear = FiscalYear::where('id', decrypt($request['fiscal_year_id']))->first();

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid'] == false) {
            $notification = array(
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
            );
            return response()->json($notification);
        }

        DB::beginTransaction();

        try {
            $RequestData = $request->all();
            $StartDate                      = new DateTime($RequestData['fy_start_date']);
            $RequestData['fy_start_date']   = $StartDate->format('Y-m-d');
            $EndDate                        = $StartDate;
            $EndDate                        = $EndDate->modify('+1 year, -1 Day');
            $RequestData['fy_end_date']     = $EndDate->format('Y-m-d');

            $FiscalYear->update($RequestData);

            DB::commit();

            return response()->json(
                [
                    'message'    => "Data updated successfully!!",
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ],
                200
            );

        } catch (\Exception $e) {
            // dd($e);
            DB::rollback();
            return response()->json([
                'message' => "Internal Server Error. Try Again!!",
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => '',
                'error' => $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage(),
            ], 500);
        }
    }

    public function view($id = null)
    {

        $FiscalYear = FiscalYear::where('id', decrypt($id))->first();

        // $FiscalYear->fy_name         = $FiscalYear->fy_name;
        // $FiscalYear->fy_for          = $FiscalYear->fy_for == 'LFY' ? 'Leave Fiscal Year': 'Finance Fiscal Year';
        $FiscalYear->fy_start_date   = (new DateTime($FiscalYear->fy_start_date ))->format('d-m-Y');

        if($FiscalYear->fy_end_date != null){
            $end_date = (new DateTime($FiscalYear->fy_end_date))->format('d-m-Y');
            $FiscalYear->fy_end_date = $end_date;
        } else {
            $FiscalYear->fy_end_date = null;
        }

        return response()->json($FiscalYear);
    }

    public function delete($id = null)
    {

        return CommonController::delete_application('\\App\\Model\\HR\\FiscalYear', $id);
    }

    public function isactive($id = null)
    {
        $FiscalYear = FiscalYear::where('id', $id)->first();

        if ($FiscalYear->is_active == 1) {
            $FiscalYear->is_active = 0;
        } else {
            $FiscalYear->is_active = 1;
        }

        $Status = $FiscalYear->save();

        if ($Status) {
            $notification = array(
                'message' => 'Successfully Updated',
                'alert-type' => 'success',
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Unsuccessful to Update',
                'alert-type' => 'error',
            );
            return redirect()->back()->with($notification);
        }
    }

}
