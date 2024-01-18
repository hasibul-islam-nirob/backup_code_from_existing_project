<?php

namespace App\Http\Controllers\HR\Payroll;

use App\Http\Controllers\Controller;
use App\Model\HR\PayrollAllowance;
use App\Services\RoleService as Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AllowanceSetupController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'name' => 'required',
                'short_name' => 'required',
                'benifit_type_uid' => 'required',
            );

            $attributes = array(
                'name'     => 'Allowance name',
                'short_name'        => 'Short name',
                'benifit_type_uid'        => 'Benefit Type',
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

            $search_arr = $request->get('search');

            $searchValue = $search_arr['value'];


            $totalRecords = DB::table('hr_payroll_allowance')->select('count(*) as allcount')->where('is_delete', 0)->where('is_active', 1)->count();
            $totalRecordswithFilter = DB::table('hr_payroll_allowance')->select('count(*) as allcount')->where('is_delete', 0)->where('is_active', 1)->where('name', 'like', '%' .$searchValue . '%')->count();

            $allData  = PayrollAllowance::where('is_delete', 0)
                ->where('is_active', 1)
                ->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $bf = $row->benifit();

                $IgnoreArray = ['view'];

                $data[$key]['id']                 = $sno;
                $data[$key]['name']        = $row->name;
                $data[$key]['short_name']          = $row->short_name;
                $data[$key]['benefit']          = (isset($bf->name) ? $bf->name : '');
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray);

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
            
            try {

                DB::table('hr_payroll_allowance')->insert([
                    'name' => $request->name,
                    'short_name' => $request->short_name,
                    'benifit_type_uid' => $request->benifit_type_uid,
                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
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
            
            try {

                DB::table('hr_payroll_allowance')->where('id', $request->edit_id)->update([
                    'name' => $request->name,
                    'short_name' => $request->short_name,
                    'benifit_type_uid' => $request->benifit_type_uid,
                    'is_delete' => 0,
                    'is_active' => 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
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

    public function delete($id)
    {
        
        $delete = DB::table('hr_payroll_allowance')->where('id', decrypt($id))->update(['is_delete' => 1]);

        if ($delete) {
            return response()->json([
                'message'    => 'Successfully deleted',
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 500,
                'status' => 'error',
                'message'    => 'Failed to delete',
                'result_data' => ''
            ], 500);
        }
    }

}
