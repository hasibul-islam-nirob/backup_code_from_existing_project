<?php

namespace App\Http\Controllers\HR\Payroll;

use App\Http\Controllers\Controller;
use App\Model\HR\SecurityMoney;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CommonService as Common;

class SecurityMoneyController extends Controller
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


            $totalRecords = DB::table('hr_payroll_settings_security_money')->select('count(*) as allcount')->where('is_delete', 0)->count();
            // $totalRecordswithFilter = DB::table('hr_payroll_settings_security_money')->select('count(*) as allcount')->where('is_delete', 0)->count();

            $allData  = SecurityMoney::where('is_delete', 0)
                        ->where( function($query) use ($searchValue){
                            if(!empty($searchValue)){
                                $query->where('grade_id', 'like', '%' .$searchValue . '%');
                                $query->orWhere('level_id', 'like', '%' .$searchValue . '%');
                                $query->orWhere('amount', 'like', '%' .$searchValue . '%');
                                $query->orWhere('effective_date', 'like', '%' .$searchValue . '%');
                            }
                        })
                        ->skip($start)->take($rowperpage)->get();

            $totalRecordswithFilter = count($allData);

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {
                
                $IgnoreArray = [];
                $systemDate = new DateTime(Common::systemCurrentDate());
                $dataEffDate = new DateTime($row->effective_date);
                if ($systemDate > $dataEffDate) {
                    $IgnoreArray = ['delete', 'edit', 'send', 'isActive','message' => "Permission Denied", 'btnHide' => true];
                }

                $data[$key]['id']          = $sno;
                $data[$key]['grade']        = $row->grade_id;
                $data[$key]['level']        = $row->level_id;
                $data[$key]['amount']        = $row->amount;
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
        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = DB::table('hr_payroll_settings_security_money')->insertGetId([
                    'grade_id' => $request->grade,
                    'level_id' => $request->level,
                    'amount' => $request->amount,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),
                    'is_delete' => 0,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id
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
        
        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            
            try {
                DB::beginTransaction();

                $id = $request->edit_id;
                $id = DB::table('hr_payroll_settings_security_money')->where('id', $id)->update([
                    'grade_id' => $request->grade,
                    'level_id' => $request->level,
                    'amount' => $request->amount,
                    'effective_date' => (new DateTime($request->effective_date))->format('Y-m-d'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
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

    public function get(Request $request)
    {
        $bankData = DB::table('hr_payroll_settings_security_money')->where('id', decrypt($request->id))->where('is_delete', 0)->first();

        dd($bankData);
        return response()->json($bankData);
    }


    public function delete($id)
    {
        $delete = DB::table('hr_payroll_settings_security_money')->where('id', decrypt($id))->update(['is_delete' => 1]);
        //DB::table('hr_payroll_settings_osf_details')->where('osf_id', decrypt($id))->delete();

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
