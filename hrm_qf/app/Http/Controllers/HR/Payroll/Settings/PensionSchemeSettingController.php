<?php

namespace App\Http\Controllers\HR\Payroll\Settings;

use App\Http\Controllers\Controller;
use App\Model\HR\PensionSchemeSetting;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;


class PensionSchemeSettingController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                
            );

            $attributes = array(
                
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


            $totalRecords = DB::table('hr_payroll_settings_pension_setting')->select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = DB::table('hr_payroll_settings_pension_setting')->select('count(*) as allcount')->where('is_delete', 0)->count();

            $allData  = PensionSchemeSetting::where('is_delete', 0)
                ->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = [];
                $systemDate = new DateTime(Common::systemCurrentDate());
                $dataEffDate = new DateTime($row->effective_date);

                $countExistPsInSS= DB::table('hr_payroll_salary_structure')->where([['is_active', 1],['is_delete', 0],['ps_id', $row->id]])->count();

                if ($systemDate > $dataEffDate || $countExistPsInSS > 0) {
                    $IgnoreArray = ['delete', 'edit', 'send', 'isActive','message' => "Permission Denied", 'btnHide' => true];
                }

                $data[$key]['id']          = $sno;
                // $data[$key]['group']        = $row->group['group_name'];
                $data[$key]['company']        = $row->company['comp_name'];
                $data[$key]['project']        = $row->project()->project_name;
                $data[$key]['rec_type']        = $row->recruitment_type();
                $data[$key]['grade'] = $row->grade;
                $data[$key]['amount'] = $row->amount;
                $data[$key]['effective_date'] = (new DateTime($row->effective_date))->format('d-m-Y');
                
                $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>Inactive</span>';
                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Active</span>';
                }
                $data[$key]['status']  = $statusFlag;
                
                $data[$key]['action'] = Role::roleWiseArrayPopup($this->GlobalRole, encrypt($row->id), $IgnoreArray, $row->is_active);

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
        $passport = $this->getPassport($request, 'storex');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = DB::table('hr_payroll_settings_pension_setting')->insertGetId([
                    // 'group_id' => $request->group_id,
                    'company_id' => $request->company_id,
                    'project_id' => $request->project_id,
                    'rec_type_ids' => implode(',', $request->rec_type_id),

                    'grade' => $request->grade,
                    'amount' => $request->amount,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),

                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                ]);

                DB::commit();

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
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
        $passport = $this->getPassport($request, 'storex');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = $request->edit_id;

                DB::table('hr_payroll_settings_pension_setting')->where('id', $id)->update([
                    // 'group_id' => $request->group_id,
                    'company_id' => $request->company_id,
                    'project_id' => $request->project_id,
                    'rec_type_ids' => implode(',', $request->rec_type_id),

                    'grade' => $request->grade,
                    'amount' => $request->amount,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),

                    'is_delete' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ]);

                DB::commit();

                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode'=> 200,
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

    public function delete($id)
    {
        $delete = DB::table('hr_payroll_settings_pension_setting')->where('id', decrypt($id))->update(['is_delete' => 1]);
        //DB::table('hr_payroll_settings_pension_setting_details')->where('pension_setting_id', decrypt($id))->delete();

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

    public function change_status($id)
    {
        $targetRow            = PensionSchemeSetting::where('id', decrypt($id))->first();

        if ($targetRow->is_active == 1) {
            $targetRow->is_active = 0;
        } else {
            $targetRow->is_active = 1;
        }
        $delete               = $targetRow->save();

        if ($delete) {
            return response()->json([
                'message'    => 'Status changed',
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => ''
            ], 200);
        } else {
            return response()->json([
                'statusCode' => 500,
                'status' => 'error',
                'message'    => 'Failed!!',
                'result_data' => ''
            ], 500);
        }
    }

}
