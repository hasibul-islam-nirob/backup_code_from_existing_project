<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use App\Model\HR\EmployeeLeaveCategory;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeLeaveCategoryController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {

        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store') {

            $rules = array(
                'name' => 'required',
                'short_form' => 'required',
                'leave_type_uid' => 'required',
            );

            if ($requestData['leave_type_uid'] == '1') {
                $rules['pay_allocated_leave'] = 'required|array|min:1';
                $rules['pay_allocated_leave.*'] = 'required';
            } elseif ($requestData['leave_type_uid'] == '3') {
                $rules['earn_allocated_leave'] = 'required|array|min:1';
                $rules['earn_allocated_leave.*'] = 'required';

                $rules['earn_max_leave_entitle'] = 'required|array|min:1';
                $rules['earn_max_leave_entitle.*'] = 'required';

                $rules['earn_consume_after'] = 'required|array|min:1';
                $rules['earn_consume_after.*'] = 'required';
            } elseif ($requestData['leave_type_uid'] == '3') {
                $rules['parental_allocated_leave'] = 'required|array|min:1';
                $rules['parental_allocated_leave.*'] = 'required';

                $rules['parental_times_of_leave'] = 'required|array|min:1';
                $rules['parental_times_of_leave.*'] = 'required';
            }

            $attributes = array(
                'name'     => 'Category name',
                'short_form'     => 'Short form',
                'leave_type_uid'        => 'Leave type',
                'pay_allocated_leave.*'        => 'Yearly allocated leave',
                'earn_allocated_leave.*'        => 'Yearly allocated leave',
                'earn_max_leave_entitle.*'        => 'Maximum leave entitle',
                'earn_consume_after.*'        => 'Consume after',
                'parental_allocated_leave.*'        => 'Yearly allocated leave',
                'parental_times_of_leave.*'        => 'Times of leaves',
            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }
        elseif($operationType == 'update'){

            $rules = array(
                'name' => 'required',
                'short_form' => 'required',
                'leave_type_uid' => 'required',
            );

            /* if ($requestData['leave_type_uid'] == '1') {
                $rules['pay_allocated_leave'] = 'required|array|min:1';
                $rules['pay_allocated_leave.*'] = 'required';
            } elseif ($requestData['leave_type_uid'] == '3') {
                $rules['earn_allocated_leave'] = 'required|array|min:1';
                $rules['earn_allocated_leave.*'] = 'required';

                $rules['earn_max_leave_entitle'] = 'required|array|min:1';
                $rules['earn_max_leave_entitle.*'] = 'required';

                $rules['earn_consume_after'] = 'required|array|min:1';
                $rules['earn_consume_after.*'] = 'required';
            } elseif ($requestData['leave_type_uid'] == '3') {
                $rules['parental_allocated_leave'] = 'required|array|min:1';
                $rules['parental_allocated_leave.*'] = 'required';

                $rules['parental_times_of_leave'] = 'required|array|min:1';
                $rules['parental_times_of_leave.*'] = 'required';
            } */

            $attributes = array(
                'name'     => 'Category name',
                'short_form'     => 'Short form',
                'leave_type_uid'        => 'Leave type',
                'pay_allocated_leave.*'        => 'Yearly allocated leave',
                'earn_allocated_leave.*'        => 'Yearly allocated leave',
                'earn_max_leave_entitle.*'        => 'Maximum leave entitle',
                'earn_consume_after.*'        => 'Consume after',
                'parental_allocated_leave.*'        => 'Yearly allocated leave',
                'parental_times_of_leave.*'        => 'Times of leaves',
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


            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');


            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];


            $totalRecords = EmployeeLeaveCategory::count();
            $totalRecordswithFilter = EmployeeLeaveCategory::where('name', 'like', '%' . $searchValue . '%')->count();

            $allData  = EmployeeLeaveCategory::where('is_delete', 0)

                ->where(function ($query) use ($columnName, $columnSortOrder) { //Ordering

                    if ($columnName == "name") {

                        $query->orderBy($columnName, $columnSortOrder);
                    } elseif ($columnName == "leave_type") {

                        $query->orderBy('leave_type_uid', $columnSortOrder);
                    } elseif ($columnName == "status") {

                        $query->orderBy('is_active', $columnSortOrder);
                    }

                    if (!empty($searchValue)) {

                        $query->where('name', 'like', '%' . $searchValue . '%');
                    }
                })->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = [];

                $data[$key]['id']                 = $sno;
                $data[$key]['name']             = $row->name;
                $data[$key]['short_form']             = $row->short_form;
                $data[$key]['leave_type']      = $row->leave_type()->name;

                $statusFlag = '<span style="color: #0fdb50"><i class="fas fa-check mr-2"></i>Active</span>';

                if ($row->is_active == 0) {
                    $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>In-active</span>';
                }

                $data[$key]['status'] = $statusFlag;
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
        // ss($request->all());
        // dd($request->all());
        $passport = $this->getPassport($request, 'store');

        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $leave_type_uid = $request['leave_type_uid'];
                $data = [
                    'name' => $request['name'],
                    'short_form' => $request['short_form'],
                    'leave_type_uid' => $request['leave_type_uid'],
                    'is_active' => 1,
                    'is_delete' => 0,
                    'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                if ($leave_type_uid == '1') { //Pay
                    $leave_cat_id = DB::table('hr_leave_category')->insertGetId($data);

                    if ($request->rec_type_id_1[0] == 'all') {
                        unset($request->rec_type_id_1);
                        $request->rec_type_id_1 = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get()->pluck('id');
                    }
                    foreach ($request->rec_type_id_1 as $key => $rec_type_id) {
                        $con_data = [];
                        $con_data['leave_cat_id'] = $leave_cat_id;
                        $con_data['rec_type_id'] = $rec_type_id;

                        $con_data['consume_policy'] = (isset($request->pay_consume_policy[$key])) ? $request->pay_consume_policy[$key] : $request->pay_consume_policy[0];
                        $con_data['remaining_leave_policy'] = (isset($request->pay_remaining_leave_policy[$key])) ? $request->pay_remaining_leave_policy[$key] : $request->pay_remaining_leave_policy[0];
                        $con_data['app_submit_policy'] = (isset($request->pay_app_submit_policy[$key])) ? $request->pay_app_submit_policy[$key] : $request->pay_app_submit_policy[0];
                        $con_data['capable_of_provision'] = (isset($request->pay_capable_of_provision[$key])) ? $request->pay_capable_of_provision[$key] : $request->pay_capable_of_provision[0];
                        $con_data['allocated_leave'] = (isset($request->pay_allocated_leave[$key])) ? $request->pay_allocated_leave[$key] : $request->pay_allocated_leave[0];
                        $con_data['effective_date_from'] = (isset($request->pay_effective_date_from[$key])) ? (new DateTime($request->pay_effective_date_from[$key]))->format('Y-m-d') : (new DateTime($request->pay_effective_date_from[0]))->format('Y-m-d');
                        $con_data['effective_date_to'] = (isset($request->earn_effective_date_to[$key])) ? (new DateTime($request->earn_effective_date_to[$key]))->format('Y-m-d') : (new DateTime($request->earn_effective_date_to[0]))->format('Y-m-d');

                        DB::table('hr_leave_category_details')->insert($con_data);
                    }
                } elseif ($leave_type_uid == '2') { //Non-Pay
                    DB::table('hr_leave_category')->insert($data);
                } elseif ($leave_type_uid == '3') { //Earned
                    $leave_cat_id = DB::table('hr_leave_category')->insertGetId($data);

                    if ($request->rec_type_id_3[0] == 'all') {
                        unset($request->rec_type_id_3);
                        $request->rec_type_id_3 = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get()->pluck('id');
                    }

                    foreach ($request->rec_type_id_3 as $key => $rec_type_id) {
                        $con_data = [];
                        $con_data['leave_cat_id'] = $leave_cat_id;
                        $con_data['rec_type_id'] = $rec_type_id;

                        $con_data['consume_policy'] = 'eligible';
                        $con_data['remaining_leave_policy'] = 'add_next_year';
                        $con_data['app_submit_policy'] = 'before';
                        $con_data['capable_of_provision'] = 0;

                        $con_data['eligibility_counting_from'] = (isset($request->earn_eligibility_counting_from[$key])) ? $request->earn_eligibility_counting_from[$key] : $request->earn_eligibility_counting_from[0];
                        $con_data['allocated_leave'] = (isset($request->earn_allocated_leave[$key])) ? $request->earn_allocated_leave[$key] : $request->earn_allocated_leave[0];
                        $con_data['max_leave_entitle'] = (isset($request->earn_max_leave_entitle[$key])) ? $request->earn_max_leave_entitle[$key] : $request->earn_max_leave_entitle[0];
                        $con_data['consume_after'] = (isset($request->earn_consume_after[$key])) ? $request->earn_consume_after[$key] : $request->earn_consume_after[0];
                        $con_data['leave_withdrawal_policy'] = (isset($request->earn_leave_withdrawal_policy[$key])) ? $request->earn_leave_withdrawal_policy[$key] : $request->earn_leave_withdrawal_policy[0];
                        $con_data['effective_date_from'] = (isset($request->earn_effective_date_from[$key])) ? (new DateTime($request->earn_effective_date_from[$key]))->format('Y-m-d') : (new DateTime($request->earn_effective_date_from[0]))->format('Y-m-d');
                        $con_data['effective_date_to'] = (isset($request->earn_effective_date_to[$key])) ? (new DateTime($request->earn_effective_date_to[$key]))->format('Y-m-d') : (new DateTime($request->earn_effective_date_to[0]))->format('Y-m-d');

                        DB::table('hr_leave_category_details')->insert($con_data);
                    }
                } elseif ($leave_type_uid == '4') { //Parental
                    $leave_cat_id = DB::table('hr_leave_category')->insertGetId($data);

                    if ($request->rec_type_id_4[0] == 'all') {
                        unset($request->rec_type_id_4);
                        $request->rec_type_id_4 = DB::table('hr_recruitment_types')->where([['is_delete', 0], ['is_active', 1]])->get()->pluck('id');
                    }

                    foreach ($request->rec_type_id_4 as $key => $rec_type_id) {
                        $con_data = [];
                        $con_data['leave_cat_id'] = $leave_cat_id;
                        $con_data['rec_type_id'] = $rec_type_id;

                        $con_data['consume_policy'] = 'yearly_allocated';
                        $con_data['remaining_leave_policy'] = 'add_next_year';
                        $con_data['app_submit_policy'] = 'before';
                        $con_data['capable_of_provision'] = 0;

                        $con_data['allocated_leave'] = (isset($request->parental_allocated_leave[$key])) ? $request->parental_allocated_leave[$key] : $request->parental_allocated_leave[0];
                        $con_data['times_of_leave'] = (isset($request->parental_times_of_leave[$key])) ? $request->parental_times_of_leave[$key] : $request->parental_times_of_leave[0];
                        $con_data['effective_date_from'] = (isset($request->parental_effective_date_from[$key])) ? (new DateTime($request->parental_effective_date_from[$key]))->format('Y-m-d') : (new DateTime($request->parental_effective_date_from[0]))->format('Y-m-d');
                        $con_data['effective_date_to'] = (isset($request->earn_effective_date_to[$key])) ? (new DateTime($request->earn_effective_date_to[$key]))->format('Y-m-d') : (new DateTime($request->earn_effective_date_to[0]))->format('Y-m-d');

                        DB::table('hr_leave_category_details')->insert($con_data);
                    }
                }

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => 'success',
                    'statusCode' => 200,
                    'result_data' => '',
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => 'error',
                    'statusCode' => 500,
                    'result_data' => '',
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
    }

    public function update(Request $request)
    {

        $passport = $this->getPassport($request, 'update');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $leave_cat_id = $request->leave_cat_id;

                $leave_type_uid = $request['leave_type_uid'];
                $data = [
                    'name' => $request['name'],
                    'short_form' => $request['short_form'],
                    'leave_type_uid' => $request['leave_type_uid'],
                    'is_active' => 1,
                    'is_delete' => 0,
                    'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                if ($leave_type_uid == '1') { //Pay
                    DB::table('hr_leave_category')->where('id', $leave_cat_id)->update($data);
                    DB::table('hr_leave_category_details')->where('leave_cat_id', $leave_cat_id)->delete();

                    foreach($request->data[$leave_type_uid] as $rec_type_id => $data_arr){
                        foreach($data_arr['allocated_leave'] as $key => $val){
                            $con_data = [];
                            $con_data['leave_cat_id'] = $leave_cat_id;
                            $con_data['rec_type_id'] = $rec_type_id;

                            $con_data['consume_policy'] = $data_arr['consume_policy'][$key];
                            $con_data['remaining_leave_policy'] = $data_arr['remaining_leave_policy'][$key];
                            $con_data['app_submit_policy'] = $data_arr['app_submit_policy'][$key];
                            $con_data['capable_of_provision'] = $data_arr['capable_of_provision'][$key];
                            $con_data['allocated_leave'] = $val;
                            $con_data['effective_date_from'] = (!empty($data_arr['effective_date_from'][$key])) ? (new DateTime($data_arr['effective_date_from'][$key]))->format('Y-m-d') : null;
                            $con_data['effective_date_to'] = (!empty($data_arr['effective_date_to'][$key])) ? (new DateTime($data_arr['effective_date_to'][$key]))->format('Y-m-d') : null;

                            DB::table('hr_leave_category_details')->insert($con_data);
                        }
                    }
                    
                } elseif ($leave_type_uid == '2') { //Non-Pay
                    DB::table('hr_leave_category')->where('id', $leave_cat_id)->update($data);
                } elseif ($leave_type_uid == '3') { //Earned
                    DB::table('hr_leave_category')->where('id', $leave_cat_id)->update($data);
                    DB::table('hr_leave_category_details')->where('leave_cat_id', $leave_cat_id)->delete();

                    foreach($request->data[$leave_type_uid] as $rec_type_id => $data_arr){
                        foreach($data_arr['allocated_leave'] as $key => $val){
                            $con_data = [];
                            $con_data['leave_cat_id'] = $leave_cat_id;
                            $con_data['rec_type_id'] = $rec_type_id;

                            $con_data['consume_policy'] = 'eligible';
                            $con_data['remaining_leave_policy'] = 'add_next_year';
                            $con_data['app_submit_policy'] = 'before';
                            $con_data['capable_of_provision'] = 0;

                            $con_data['eligibility_counting_from'] = $data_arr['eligibility_counting_from'][$key];
                            $con_data['max_leave_entitle'] = $data_arr['max_leave_entitle'][$key];
                            $con_data['consume_after'] = $data_arr['consume_after'][$key];
                            $con_data['leave_withdrawal_policy'] = $data_arr['leave_withdrawal_policy'][$key];
                            $con_data['allocated_leave'] = $val;
                            $con_data['effective_date_from'] = (!empty($data_arr['effective_date_from'][$key])) ? (new DateTime($data_arr['effective_date_from'][$key]))->format('Y-m-d') : null;
                            $con_data['effective_date_to'] = (!empty($data_arr['effective_date_to'][$key])) ? (new DateTime($data_arr['effective_date_to'][$key]))->format('Y-m-d') : null;
                            
                            DB::table('hr_leave_category_details')->insert($con_data);
                        }
                    }

                } elseif ($leave_type_uid == '4') { //Parental
                    DB::table('hr_leave_category')->where('id', $leave_cat_id)->update($data);
                    DB::table('hr_leave_category_details')->where('leave_cat_id', $leave_cat_id)->delete();

                    foreach($request->data[$leave_type_uid] as $rec_type_id => $data_arr){
                        foreach($data_arr['allocated_leave'] as $key => $val){
                            $con_data = [];
                            $con_data['leave_cat_id'] = $leave_cat_id;
                            $con_data['rec_type_id'] = $rec_type_id;

                            $con_data['consume_policy'] = 'yearly_allocated';
                            $con_data['remaining_leave_policy'] = 'add_next_year';
                            $con_data['app_submit_policy'] = 'before';
                            $con_data['capable_of_provision'] = 0;

                            $con_data['times_of_leave'] = $data_arr['times_of_leave'][$key];
                            $con_data['allocated_leave'] = $val;
                            $con_data['effective_date_from'] = (!empty($data_arr['effective_date_from'][$key])) ? (new DateTime($data_arr['effective_date_from'][$key]))->format('Y-m-d') : null;
                            $con_data['effective_date_to'] = (!empty($data_arr['effective_date_to'][$key])) ? (new DateTime($data_arr['effective_date_to'][$key]))->format('Y-m-d') : null;
                            
                            DB::table('hr_leave_category_details')->insert($con_data);
                        }
                    }

                }

                DB::commit();
                return response()->json([
                    'message'    => $passport['message'],
                    'status' => true,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message'    => "Internal Server Error. Try Again!!",
                    'status' => true,
                    'error'  => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message'    => $passport['message'],
                'status' => false,
            ], 400);
        }
    }

    public function get($id)
    {
        $leaveCategory    = EmployeeLeaveCategory::where('id', decrypt($id))->with('leave_details')->first();
        $leaveCategory->leave_type = $leaveCategory->leave_type();
        foreach ($leaveCategory->leave_details as $ld) {
            $ld->rec_type;
        }

        if ($leaveCategory) {
            $responseData = [
                'status' => 'success',
                'statusCode' => 200,
                'result_data' => $leaveCategory
            ];
            return response()->json($responseData, 200);
        } else {
            $responseData = [
                'status' => 'error',
                'statusCode' => 500,
                'result_data' => ''
            ];
            return response()->json($responseData, 500);
        }
    }

    public function delete($id)
    {
        $targetRow            = EmployeeLeaveCategory::where('id', decrypt($id))->first();
        $targetRow->is_delete = 1;
        $delete               = $targetRow->save();
        DB::table('hr_leave_category_details')->where('leave_cat_id', decrypt($id))->delete();

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
        $targetRow            = EmployeeLeaveCategory::where('id', decrypt($id))->first();

        if ($targetRow->is_active == 1) {
            $targetRow->is_active = 0;
        } else {
            $targetRow->is_active = 1;
        }
        $delete               = $targetRow->save();

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
