<?php

namespace App\Http\Controllers\HR\Payroll\Settings;

use DateTime;
use App\Http\Controllers\Controller;
use App\Model\HR\PayrollDonationSectorModel;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\HrService as HRS;
use App\Http\Controllers\HR\Others\CommonController;

class PayrollDonationSectorController extends Controller
{
    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'group_id' => 'required',
                'company_id' => 'required',
                'project_id' => 'required',

                'sector_name' => 'required',
            );

            $attributes = array(
                // 'group_id' => 'Group',
                'company_id' => 'Company',
                'project_id' => 'Project',

                'sector_name' => 'Rectuitment Type',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        $isValid = $errorMsg == null ? true : false;

        $passport = array(
            'isValid'  => $isValid,
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

            $totalRecords = PayrollDonationSectorModel::where('is_delete', 0)->select('count(*) as allcount')->count();
            $totalRecordswithFilter = PayrollDonationSectorModel::where('is_delete', 0)->select('count(*) as allcount')->count();
            $userInfo = Auth::user();

            $allData = PayrollDonationSectorModel::from('hr_payroll_settings_donation AS hpcm')
                ->where('is_delete', 0)
                ->select('hpcm.*')
                ->get();

            $data = array();
            $sno = $start + 1;
            
            foreach ($allData as $key => $row) {

                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');
                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $wfDetData = DB::table('hr_payroll_settings_wf_details')->where('don_sector', $row->id)->count();
                if ($wfDetData > 0) {
                    $IgnoreArray = ['delete'];
                }

                $data[$key]['id']                 = $sno;

                // $data[$key]['group']                = $row->group['group_name'];
                $data[$key]['company']              = $row->company['comp_name'];
                $data[$key]['project']              = $row->project()->project_name;

                $data[$key]['sector_name']          = $row->sector_name;

                $data[$key]['action']             = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row['id']), $IgnoreArray);

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

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();

            try {


                $sectorName = !empty($request->sector_name) ? $request->sector_name : null;

                
                $checkDuplicate = DB::table('hr_payroll_settings_donation')->where([['is_active', 1], ['is_delete', 0],['sector_name', $sectorName]])->count();

                if ($checkDuplicate > 0) {
                    return response()->json(1);
                }
                

                $payrollDonation = new PayrollDonationSectorModel();

                $payrollDonation->branch_id = $request->branch_id;
                // $payrollDonation->group_id = $request->group_id;
                $payrollDonation->company_id = $request->company_id;
                $payrollDonation->project_id = $request->project_id;

                $payrollDonation->sector_name = $request->sector_name;

                $payrollDonation->save();
    
                DB::commit();
                
                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);

            } catch (\Exception $e) {

                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function update(Request $request)
    {

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();

            try {

                $sectorName = !empty($request->sector_name) ? $request->sector_name : null;
                $checkDuplicate = DB::table('hr_payroll_settings_donation')->where([['is_active', 1], ['is_delete', 0],['sector_name', $sectorName]])->count();

                if ($checkDuplicate > 0) {
                    return response()->json(1);
                }
                

                $payrollDonation = PayrollDonationSectorModel::where('id', $request['edit_id'])->first();;

                $payrollDonation->branch_id = $request->branch_id;
                // $payrollDonation->group_id = $request->group_id;
                $payrollDonation->company_id = $request->company_id;
                $payrollDonation->project_id = $request->project_id;

                $payrollDonation->sector_name = $request->sector_name;

                $payrollDonation->save();
    
                DB::commit();
                
                return response()->json([
                    'message' => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);

            } catch (\Exception $e) {

                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode'=> 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode'=> 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function get($id)
    {
        $data =  DB::table('hr_payroll_settings_donation')->where('id', $id)->first();
        return response()->json($data);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\PayrollDonationSectorModel', $id);
    }
}
