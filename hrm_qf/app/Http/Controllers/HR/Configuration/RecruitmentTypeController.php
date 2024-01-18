<?php

namespace App\Http\Controllers\HR\Configuration;

use App\Http\Controllers\Controller;
use App\Model\HR\RecruitmentType;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecruitmentTypeController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'title' => 'required',
            );

            $attributes = array(
                'title'     => 'Title',
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


            $totalRecords = RecruitmentType::count();
            $totalRecordswithFilter = RecruitmentType::where('title', 'like', '%' .$searchValue . '%')->count();

            $allData  = RecruitmentType::where('is_delete', 0)

                ->where(function($query) use ($columnName, $columnSortOrder){

                    if($columnName == "title"){

                        $query->orderBy($columnName, $columnSortOrder);

                    }

                    elseif($columnName == "prov_days"){

                        $query->orderBy('prov_days', $columnSortOrder);

                    }

                    elseif($columnName == "status"){

                        $query->orderBy('is_active', $columnSortOrder);

                    }

                    if(!empty($searchValue)){

                        $query->where('name', 'like', '%' .$searchValue . '%');

                    }

                })->skip($start)->take($rowperpage)->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = ['view'];
                $countExistRecInHremp = DB::table('hr_emp_organization_details')->where('rec_type_id', $row->id)->count();
                $countExistRecInSS= DB::table('hr_payroll_salary_structure')->where([['is_active', 1],['is_delete', 0],['recruitment_type_id', $row->id]])->count();

                $pfData = DB::table('hr_payroll_settings_pf')->where([['is_active', 1],['is_delete', 0]])->get();
                $pfData = array_column($pfData->toArray(), 'rec_type_ids');
                if(!empty($pfData)){
                    $pfData = array_unique(call_user_func_array('array_merge',array_map('str_split', $pfData)));
                }else{
                    $pfData = [];
                }

                $wfData = DB::table('hr_payroll_settings_wf')->where([['is_active', 1],['is_delete', 0]])->get();
                $wfData = array_column($wfData->toArray(), 'rec_type_ids');
                if(!empty($wfData)){
                    $wfData = array_unique(call_user_func_array('array_merge',array_map('str_split', $wfData)));
                }else{
                    $wfData = [];
                }
                
                $epsData = DB::table('hr_payroll_settings_pension_setting')->where([['is_active', 1],['is_delete', 0]])->get();
                $epsData = array_column($epsData->toArray(), 'rec_type_ids');
                if(!empty($epsData)){
                    $epsData = array_unique(call_user_func_array('array_merge',array_map('str_split', $epsData)));
                }else{
                    $epsData = [];
                }
                
                $osfData = DB::table('hr_payroll_settings_osf')->where([['is_active', 1],['is_delete', 0]])->get();
                $osfData = array_column($osfData->toArray(), 'rec_type_ids');
                if(!empty($osfData)){
                    $osfData = array_unique(call_user_func_array('array_merge',array_map('str_split', $osfData)));
                }else{
                    $osfData = [];
                }

                $incData = DB::table('hr_payroll_settings_insurance')->where([['is_active', 1],['is_delete', 0]])->get();
                $incData = array_column($incData->toArray(), 'rec_type_ids');
                if(!empty($incData)){
                    $incData = array_unique(call_user_func_array('array_merge',array_map('str_split', $incData)));
                }else{
                    $incData = [];
                }

                $bonusData = DB::table('hr_payroll_settings_bonus')->where([['is_active', 1],['is_delete', 0]])->get();
                $bonusData = array_column($bonusData->toArray(), 'rec_type_ids');
                if(!empty($bonusData)){
                    $bonusData = array_unique(call_user_func_array('array_merge',array_map('str_split', $bonusData)));
                }else{
                    $bonusData = [];
                }

                if ($countExistRecInHremp > 0 || $countExistRecInSS > 0 || in_array($row->id ,$pfData) || in_array($row->id ,$wfData) || in_array($row->id ,$epsData) || in_array($row->id ,$osfData) || in_array($row->id ,$incData) || in_array($row->id ,$bonusData)) {
                    $IgnoreArray = ['delete', 'view'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['title']             = $row->title;
                $data[$key]['employee_type']             = ($row->employee_type == 'permanent') ? 'Permanent' : 'Non Permanent';
                $data[$key]['salary_method']             = $row->salary_method;

                $statusFlag = '<span style="color: #0fdb50"><i class="fas fa-check mr-2"></i>Active</span>';

                if ($row->is_active == 0) {
                    $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>Inactive</span>';
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

        $passport = $this->getPassport($request, 'store');

        if ($passport['isValid']) {

            DB::beginTransaction();
            try {

                if ($request['employee_type'] == 'permanent') {
                    $checkData = RecruitmentType::where([['is_active', 1],['is_delete', 0], ['employee_type', $request['employee_type']]])->count();
                }else{
                    $checkData = 0;
                }

                if ($checkData > 0) {
                    return response()->json(1);
                }else{

                    $recType = new RecruitmentType();
                    $recType->title = $request['title'];
                    $recType->employee_type = $request['employee_type'];
                    $recType->salary_method = $request['salary_method'];
                    $recType->save();

                    DB::commit();
                    return response()->json([
                        'message'    => $passport['message'],
                        'status' => 'success',
                        'statusCode'=> 200,
                        'result_data' => '',
                    ], 200);

                }
                
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

        $passport = $this->getPassport($request, 'update');

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {

                if ($request['employee_type'] == 'permanent') {
                    $checkData = RecruitmentType::where([['id', decrypt($request['rec_type_id'])],['is_active', 1],['is_delete', 0], ['employee_type', $request['employee_type']]])->count();
                }else{
                    $checkData = 0;
                }

                if ($checkData > 0) {
                    return response()->json(1);
                }else{

                    $recType = RecruitmentType::find( decrypt($request['rec_type_id']) );
                    $recType->title = $request['title'];
                    $recType->employee_type = $request['employee_type'];
                    $recType->salary_method = $request['salary_method'];
                    $recType->save();

                    DB::commit();
                    return response()->json([
                        'message'    => $passport['message'],
                        'status' => 'success',
                        'statusCode'=> 200,
                        'result_data' => '',
                    ], 200);

                }

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


    public function getData(Request $request)
    {
        $data = array();

        // dd(1, $request->all());

        if ($request->context == 'RecruitmentData') {

            $sm = DB::table('gnl_company_config')->where('form_id', 23)->where('module_id', 13)->where('company_id', 1)->first();
            $is_sm_both = false;
            if(!empty($sm) && $sm->form_value == 'both'){
                $is_sm_both = true;
            }

            $data = array(
                'is_sm_both'          => $is_sm_both
            );
        }

        return response()->json($data);
    }


    // public function get($id)
    // {
    //     $leaveCategory    = RecruitmentType::find(decrypt($id));

    //     if ($leaveCategory) {
    //         $responseData = [
    //             'status' => 'success',
    //             'statusCode' => 200,
    //             'result_data' => $leaveCategory
    //         ];
    //         return response()->json($responseData, 200);
    //     } else {
    //         $responseData = [
    //             'status' => 'error',
    //             'statusCode' => 500,
    //             'result_data' => ''
    //         ];
    //         return response()->json($responseData, 500);
    //     }
    // }

    public function get($id){
        $data = array();

        $leaveCategory    = RecruitmentType::find(decrypt($id));
        $sm = DB::table('gnl_company_config')->where('form_id', 23)->where('module_id', 13)->where('company_id', 1)->first();
            $is_sm_both = false;
            if(!empty($sm) && $sm->form_value == 'both'){
                $is_sm_both = true;
            }

        $data = array(
        'is_sm_both'    => $is_sm_both,
        'responseData'  => $leaveCategory,
        );
        return response()->json($data);
        
    }




    public function delete($id)
    {
        $targetRow            = RecruitmentType::where('id', decrypt($id))->first();
        $targetRow->is_delete = 1;
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

    public function change_status($id)
    {
        $targetRow            = RecruitmentType::where('id', decrypt($id))->first();

        if($targetRow->is_active == 1){
            $targetRow->is_active = 0;
        }
        else{
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
