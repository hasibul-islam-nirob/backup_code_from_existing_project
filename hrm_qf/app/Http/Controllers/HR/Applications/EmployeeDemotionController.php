<?php

namespace App\Http\Controllers\HR\Applications;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use App\Model\HR\EmployeeDemotion;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\HR\Others\CommonController;
use App\Http\Controllers\HR\Process\ApplicationProcessController;

class EmployeeDemotionController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'branch_id' => 'required',
                'employee_id' => 'required',
                'current_designation_id'        => 'required',
                'designation_to_demote_id'        => 'required',
                'demotion_date'        => 'required',
                'exp_effective_date'     => 'required',
                // 'description' => 'required',

            );

            $attributes = array(
                'exp_effective_date'     => 'Expected effective date',
                'demotion_date'        => 'Demotion Date',
                'current_designation_id'        => 'Current Designation',
                'designation_to_demote_id'        => 'Designation to demote',
                'description' => 'Description',
                'branch_id' => 'Branch',
                'employee_id' => 'Employee',

            );

            $validator = Validator::make($requestData->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                $errorMsg = implode(' || ', $validator->errors()->all());
            }
        }

        if ($operationType == 'index') {
            $IgnoreArray = array();

            if ($requestData->is_active == 1) { // only view
                $IgnoreArray = ['delete', 'edit', 'send', 'message' => "Permission Denied", 'btnHide' => true];
            } elseif ($requestData->is_active > 1) {
                $IgnoreArray = ['view', 'delete', 'edit', 'send', 'btnHide' => true];
            }

            $errorMsg = $IgnoreArray;

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


            $totalRecords = EmployeeDemotion::select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = EmployeeDemotion::select('count(*) as allcount')->where('is_delete', 0)->where('demotion_code', 'like', '%' .$searchValue . '%')->count();

            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchId          = (empty($request->branch_id)) ? null : $request->branch_id;

            $selBranchArr = Common::getBranchIdsForAllSection([
                // 'companyId'     => $companyId,
                // 'projectId'     => $projectId,
                // 'projectTypeId' => $projectTypeId,
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData  = EmployeeDemotion::from('hr_app_demotions AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $selBranchArr){
                    ## Calling Permission Query Function
                    HRS::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $selBranchArr);
                })
                /* Old Permission Code
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId){
                    if(Common::isSuperUser() == true || Common::isDeveloperUser() == true){
                        ## nothing to do
                    }
                    else {

                        if(in_array(101, $statusArray)){
                            ## All Data for Permitted Branches
                            ## nothing to do
                            // $query->whereIn('ads.branch_id', $selBranchArr)
                        }
                        elseif(in_array(102, $statusArray)){
                            ## All Branch Data Without HO
                            $perQuery->where('ads.branch_id' , '<>' ,1);
                        }
                        elseif(in_array(103, $statusArray)){
                            ## All Data Only HO
                            $perQuery->where('ads.branch_id', 1);
                        }
                        elseif(in_array(104, $statusArray)){
                            ## All data for own department of permitted branches
                            // $perQuery->whereIn('ads.branch_id', $tmpBranch);
                            $perQuery->where('ads.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(105, $statusArray)){
                            ## All data for own department of all branches without HO
                            $perQuery->where('ads.branch_id', '<>' , 1);
                            $perQuery->where('ads.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(106, $statusArray)){
                            ## All data for own department only HO
                            $perQuery->where([['ads.branch_id', 1],['ads.department_id', $loginUserDeptId]]);
                        }
                        else{
                            $perQuery->where('ads.created_by', $userInfo['id']);
                            // $perQuery->orWhere('ads.emp_id', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('ads.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })
                */
                ->when(true, function($query) use ($columnName, $columnSortOrder, $request){ //Ordering
                    if($columnName == "demotion_code"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "employee_name"){
                        if(empty($request->department_id) && empty($request->designation_id)){
                            $query->join('hr_employees as e', function ($join){
                                $join->on('apl.emp_id', '=', 'e.id');
                            });
                        }
                        $query->orderBy('e.emp_name', $columnSortOrder);
                    }
                    elseif($columnName == "branch"){
                        $query->join('gnl_branchs as b', function ($join){
                            $join->on('apl.branch_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);
                    }
                    elseif($columnName == "current_designation"){
                        $query->join('hr_designations as d', function ($join){
                            $join->on('apl.current_designation_id', '=', 'd.id');

                        });
                        $query->orderBy('d.name', $columnSortOrder);
                    }
                    elseif($columnName == "designation_to_demote"){
                        $query->join('hr_designations as d', function ($join){
                            $join->on('apl.designation_to_demote_id', '=', 'd.id');
                        });
                        $query->orderBy('d.name', $columnSortOrder);
                    }
                    elseif($columnName == "demotion_date"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "effective_date"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "status"){
                        $query->orderBy('apl.is_active', $columnSortOrder);
                    }
                    elseif($columnName == "reason"){
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    }
                    elseif($columnName == "id"){
                        $query->orderBy('apl.id', 'desc');
                    }
                })
                ->when(true, function ($query) use ($request, $userInfo, $searchValue, $selBranchArr) { //Searching
                    
                    if(!empty($request->designation_id) && !empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    elseif(!empty($request->designation_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    }
                    elseif(!empty($request->department_id)){
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }
                    if(!empty($request->employee_id)){
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->appl_code)) {
                        $query->where('apl.demotion_code', 'LIKE', "%{$request->appl_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->start_date) && !empty($request->end_date)) {
                        $query->whereBetween('apl.demotion_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    }
                    elseif(!empty($request->start_date)){
                        $query->where('apl.demotion_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    }
                    elseif(!empty($request->end_date)){
                        $query->where('apl.demotion_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }
                    // if (Common::isSuperUser() == false && Common::isDeveloperUser() == false) {
                    //     ## for all data action
                    //     if(Common::isActionPermitedForThisUser($actionAllData = 21) == false){

                    //         if(!empty($userInfo['emp_id'])){
                    //             $query->where('apl.emp_id', $userInfo['emp_id']);
                    //         }
                    //         else if(!empty($userInfo['id'])){
                    //             $query->where('apl.created_by', $userInfo['id']);
                    //         }

                    //         ## for all without ho == 22
                    //         if(Common::isActionPermitedForThisUser($actionAllData = 22) == true && Common::isHeadOffice() == true){
                    //             $query->orWhere('apl.branch_id', '<>', $this->hoId);
                    //         }
                    //         ## for only ho == 23
                    //         elseif(Common::isActionPermitedForThisUser($actionAllData = 23) == true && Common::isHeadOffice() == true){
                    //             $query->orWhere('apl.branch_id', $this->hoId);
                    //         }
                    //     }

                    // }

                })->where(function($query2) use ($searchValue){
                    if (!empty($searchValue)) {
                        $query2->where('demotion_code', 'like', '%' . $searchValue . '%');
                    }
                })
                ->skip($start)->take($rowperpage)->select('apl.*')->get();

            $data      = array();
            $sno = $start+1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['description']        = $row->description;
                $data[$key]['current_designation'] = $row->current_designation['name'];
                $data[$key]['designation_to_demote'] = $row->designation_to_demote['name'];
                $data[$key]['demotion_date']      = (new DateTime($row->demotion_date))->format('d-m-Y');
                $data[$key]['exp_effective_date'] = (new DateTime($row->exp_effective_date))->format('d-m-Y');
                $data[$key]['effective_date']     = (new DateTime($row->effective_date))->format('d-m-Y');
                $data[$key]['demotion_code']        = $row->demotion_code;

                $statusFlag = "<span>Draft</span>";

                if ($row->is_active == 1) {
                    $statusFlag = '<span style="color: #0cf041"><i class="fas fa-check mr-2"></i>Approved</span>';
                }

                if ($row->is_active == 2) {
                    $statusFlag = '<span style="color: #d40f0f"><i class="fas fa-times mr-2"></i>Rejected</span>';
                }

                if ($row->is_active == 3) {
                    $statusFlag = '<span style="color: #0c10f0"><i class="fas fa-hourglass-end mr-2"></i>Processing</span>';
                }

                $data[$key]['status'] = $statusFlag;
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

    public function insert(Request $request, $status)
    {
        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                     = new EmployeeDemotion();
                $appl->branch_id          = $request['branch_id'];
                $appl->demotion_code        = HRS::generateDemotionCode($request['branch_id']);
                $appl->emp_id             = $request['employee_id'];
                $appl->designation_to_demote_id             = $request['designation_to_demote_id'];
                $appl->current_designation_id             = $request['current_designation_id'];
                $appl->description        = $request['description'];
                $appl->demotion_date        = (new DateTime($request['demotion_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['exp_effective_date']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = Common::getCompanyId();

                if ($status == 'send') {
                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(9, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 9);

                        if(isset($request->attachment) && count($request->attachment) > 0){
                            $this->uploadFiles($request->attachment);
                        }

                        DB::commit();

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }
                    $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    $appl->current_stage = null;
                }

                $appl->save();

                if(isset($request->attachment) && count($request->attachment) > 0){
                    $this->uploadFiles($request->attachment);
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

    public function uploadFiles($attachments, $fk = null){

        foreach($attachments as $key => $file){

            DB::table('hr_attachments')->insert([
                'path' => Common::fileUpload($file, 'employee_demotion', ''),
                'foreign_key' => (!empty($fk)) ? $fk : EmployeeDemotion::latest()->first()->id,
                'ref_table_name' => 'hr_app_demotions',
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);

        }
    }

    public function update(Request $request, $status)
    {

        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl = EmployeeDemotion::find(decrypt($request['demotion_id']));

                $appl->branch_id          = $request['branch_id'];
                $appl->emp_id             = $request['employee_id'];
                $appl->designation_to_demote_id            = $request['designation_to_demote_id'];
                $appl->current_designation_id           = $request['current_designation_id'];
                $appl->description        = $request['description'];
                $appl->demotion_date        = (new DateTime($request['demotion_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->is_active          = ($status == 'send') ? 3 : 0;

                if(isset($request->fileIds) && count($request->fileIds) > 0){
                    DB::table('hr_attachments')->where('foreign_key', decrypt($request['demotion_id']))->where('ref_table_name', 'hr_app_demotions')
                        ->whereNotIn('id', $request->fileIds)->delete();
                }

                if ($status == 'send') {
                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(9, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 9);

                        if(isset($request->attachment) && count($request->attachment) > 0){
                            $this->uploadFiles($request->attachment, $appl->id);
                        }

                        DB::commit();

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }
                    $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    $appl->current_stage = null;
                }

                $appl->save();

                if(isset($request->attachment) && count($request->attachment) > 0){
                    $this->uploadFiles($request->attachment, $appl->id);
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
        return CommonController::get_application('\\App\\Model\\HR\\EmployeeDemotion', $id,['employee', 'branch', 'current_designation', 'designation_to_demote', 'attachments']);
    }

    public function send($id)
    {
        return CommonController::send_application('\\App\\Model\\HR\\EmployeeDemotion', $id, 9);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\EmployeeDemotion', $id);
    }

    public function finish($appl, $exe_date){

        DB::beginTransaction();

        try{
            //1) Employee Table [Designation] Change.
            //2) Employee Table [PayRoll] Change.
            DB::table('hr_employees')->where('id', $appl->emp_id)->update(['designation_id' => $appl->designation_to_demote_id]);

            //1) User Table [PayRoll] Change.
        }
        catch(\Exception $e){
            DB::rollback();
        }

        /* DB::table('hr_approval_queries')->insert([
            'query' => "update `hr_employees` set `designation_id` = ". $appl->designation_to_demote_id ." where `id` = ". $appl->emp_id,
            'appl_name' => 'demotion',
            'execution_date' => ($exe_date == null) ? $appl->exp_effective_date : $exe_date,
            'created_at' => date("Y-m-d H:i:s")
        ]); */

        DB::commit();

    }

}
