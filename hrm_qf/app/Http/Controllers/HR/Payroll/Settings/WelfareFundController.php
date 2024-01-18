<?php

namespace App\Http\Controllers\HR\Payroll\Settings;

use App\Http\Controllers\Controller;
use App\Model\HR\WelfareFund;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;


class WelfareFundController extends Controller
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


            $totalRecords = DB::table('hr_payroll_settings_wf')->select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = DB::table('hr_payroll_settings_wf')->select('count(*) as allcount')->where('is_delete', 0)->count();

            $allData  = WelfareFund::where('is_delete', 0)
                ->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = [];
                $systemDate = new DateTime(Common::systemCurrentDate());
                $dataEffDate = new DateTime($row->effective_date);

                $countExistPfInSS= DB::table('hr_payroll_salary_structure')->where([['is_active', 1],['is_delete', 0],['wf_id', $row->id]])->count();

                if ($systemDate > $dataEffDate || $countExistPfInSS > 0) {
                    $IgnoreArray = ['delete', 'edit', 'send', 'isActive','message' => "Permission Denied", 'btnHide' => true];
                }

                $data[$key]['id']          = $sno;
                // $data[$key]['group']        = $row->group['group_name'];
                $data[$key]['company']        = $row->company['comp_name'];
                $data[$key]['project']        = $row->project()->project_name;
                $data[$key]['rec_type']        = $row->recruitment_type();
                $data[$key]['interest_rate'] = $row->interest_rate;
                $data[$key]['method'] = ($row->method == 'decline' ? 'Decline' : 'Flat');
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

                $id = DB::table('hr_payroll_settings_wf')->insertGetId([
                    // 'group_id' => $request->group_id,
                    'company_id' => $request->company_id,
                    'project_id' => $request->project_id,
                    'rec_type_ids' => implode(',', $request->rec_type_id),

                    'interest_rate' => $request->interest_rate,
                    'method' => $request->method,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),

                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                ]);

                

                foreach($request->type as $key => $val){

                    if($request->type[$key] != '' && $request->grade[$key] != '' && $request->level[$key] != '' && $request->calculation_type[$key] != '' && $request->amount[$key] != ''){
                        DB::table('hr_payroll_settings_wf_details')->insert([
                            'wf_id' => $id,
                            'type' => $request->type[$key],
                            'grade' => $request->grade[$key],
                            'level' => $request->level[$key],
                            'calculation_type' => $request->calculation_type[$key],
                            'amount' => $request->amount[$key],
                            'data_type' => 'calculation',
                        ]);
                    }
                    
                }

                if (!empty($request->don_sector[0])) {
                    $don_sector = $request->don_sector;
                    $don_amount = $request->don_amount;
                    $newDonationArr = array_combine($don_sector, array_filter($don_amount));

                    foreach($newDonationArr as $key => $val){
                        DB::table('hr_payroll_settings_wf_details')->insert([
                            'wf_id' => $id,
                            'don_sector' => $key,
                            'amount' => $val,
                            'data_type' => 'donation',
                        ]);
                        
                    }
                }

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

                DB::table('hr_payroll_settings_wf')->where('id', $id)->update([
                    // 'group_id' => $request->group_id,
                    'company_id' => $request->company_id,
                    'project_id' => $request->project_id,
                    'rec_type_ids' => implode(',', $request->rec_type_id),

                    'interest_rate' => $request->interest_rate,
                    'method' => $request->method,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),

                    'is_delete' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                ]);

                DB::table('hr_payroll_settings_wf_details')->where('wf_id', $id)->delete();

                foreach($request->type as $key => $val){
                    if($request->type[$key] != '' && $request->grade[$key] != '' && $request->level[$key] != '' && $request->calculation_type[$key] != '' && $request->amount[$key] != ''){
                        DB::table('hr_payroll_settings_wf_details')->insert([
                            'wf_id' => $id,
                            'type' => $request->type[$key],
                            'grade' => $request->grade[$key],
                            'level' => $request->level[$key],
                            'calculation_type' => $request->calculation_type[$key],
                            'amount' => $request->amount[$key],
                            'data_type' => 'calculation',
                        ]);
                    }
                }

                if (!empty($request->don_sector[0])) {
                    $don_sector = $request->don_sector;
                    $don_amount = $request->don_amount;
                    $newDonationArr = array_combine($don_sector, array_filter($don_amount));

                    foreach($newDonationArr as $key => $val){
                        DB::table('hr_payroll_settings_wf_details')->insert([
                            'wf_id' => $id,
                            'don_sector' => $key,
                            'amount' => $val,
                            'data_type' => 'donation',
                        ]);
                        
                    }
                }


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
        $delete = DB::table('hr_payroll_settings_wf')->where('id', decrypt($id))->update(['is_delete' => 1]);
        //DB::table('hr_payroll_settings_wf_details')->where('wf_id', decrypt($id))->delete();

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
        $targetRow            = WelfareFund::where('id', decrypt($id))->first();

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
