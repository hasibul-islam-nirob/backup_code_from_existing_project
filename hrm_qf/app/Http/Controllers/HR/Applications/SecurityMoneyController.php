<?php

namespace App\Http\Controllers\HR\Applications;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Others\CommonController;
use App\Http\Controllers\HR\Process\ApplicationProcessController;
use App\Model\HR\Employee;
use App\Model\HR\AppSecurityMoney;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use App\Services\RoleService as Role;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;;

class SecurityMoneyController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null){

        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'branch_id'     => 'required',
                'project_id'     => 'required',
                'employee_id'     => 'required',

                'collection_amount'     => 'required',
                'collection_month'     => 'required',
                'collection_type'     => 'required',
                'collection_by'     => 'required',

                'application_date'     => 'required',
                'exp_effective_date'     => 'required'
            );

            $attributes = array(
                'branch_id'     => 'Branch',
                'project_id'     => 'Project',
                'employee_id'     => 'Employee',

                'collection_amount'     => '>Collection Amount',
                'collection_month'     => 'Collection Month',
                'collection_type'     => 'Collection Type',
                'collection_by'     => 'Collection By',

                'application_date'     => 'Application date',
                'exp_effective_date'     => 'Expected effective date',
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

            $end_date            = (empty($request->end_date)) ? null : $request->end_date;
            $start_date            = (empty($request->start_date)) ? null : $request->start_date;
            $employee_id            = (empty($request->employee_id)) ? null : $request->employee_id;
            $designation_id            = (empty($request->designation_id)) ? null : $request->designation_id;
            $department_id            = (empty($request->department_id)) ? null : $request->department_id;
            $appl_code            = (empty($request->appl_code)) ? null : $request->appl_code;
            $appl_status            = (empty($request->appl_status)) ? null : $request->appl_status;

            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchTo          = (empty($request->branch_to)) ? null : $request->branch_to;

            $selBranchArr = Common::getBranchIdsForAllSection([
                // 'companyId'     => $companyId,
                // 'projectId'     => $projectId,
                // 'projectTypeId' => $projectTypeId,
                'branchId'      => $branchTo,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData  = AppSecurityMoney::from('hr_app_security_money as asm')
                ->where('asm.is_delete', 0)
                ->whereIn('asm.branch_id', $selBranchArr)
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
                            // $query->whereIn('asm.branch_id', $selBranchArr)
                        }
                        elseif(in_array(102, $statusArray)){
                            ## All Branch Data Without HO
                            $perQuery->where('asm.branch_id' , '<>' ,1);
                        }
                        elseif(in_array(103, $statusArray)){
                            ## All Data Only HO
                            $perQuery->where('asm.branch_id', 1);
                        }
                        elseif(in_array(104, $statusArray)){
                            ## All data for own department of permitted branches
                            // $perQuery->whereIn('asm.branch_id', $tmpBranch);
                            $perQuery->where('asm.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(105, $statusArray)){
                            ## All data for own department of all branches without HO
                            $perQuery->where('asm.branch_id', '<>' , 1);
                            $perQuery->where('asm.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(106, $statusArray)){
                            ## All data for own department only HO
                            $perQuery->where([['asm.branch_id', 1],['asm.department_id', $loginUserDeptId]]);
                        }
                        else{
                            $perQuery->where('asm.created_by', $userInfo['id']);
                            // $perQuery->orWhere('asm.emp_id', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('asm.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })*/
                ->when(true, function ($query) use ($columnName, $columnSortOrder, $request) { //Ordering

                    if ($columnName == "security_money_code") {
                        $query->orderBy("asm." . $columnName, $columnSortOrder);

                    } elseif ($columnName == "employee_name") {

                        if (empty($request->department_id) && empty($request->designation_id)) {

                            $query->join('hr_employees as e', function ($join) {

                                $join->on('asm.emp_id', '=', 'e.id');
                            });
                        }
                        $query->orderBy('e.emp_name', $columnSortOrder);

                    } elseif ($columnName == "branch_id") {

                        $query->join('gnl_branchs as b', function ($join) {

                            $join->on('asm.branch_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);

                    } elseif ($columnName == "application_date") {
                        $query->orderBy("asm." . $columnName, $columnSortOrder);

                    } elseif ($columnName == "status") {
                        $query->orderBy('asm.is_active', $columnSortOrder);

                    } elseif ($columnName == "id") {
                        $query->orderBy('asm.id', 'desc');
                    }
                })
                ->when(true, function ($query) use ($request, $userInfo, $searchValue, $selBranchArr) {

                    

                    if (!empty($request->designation_id) && !empty($request->department_id)) {

                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('asm.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
                        });
                    } elseif (!empty($request->designation_id)) {

                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('asm.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    } elseif (!empty($request->department_id)) {

                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('asm.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    }

                    if (!empty($request->employee_id)) {
                        $query->where('asm.emp_id', $request->employee_id);
                    }

                    if (!empty($request->appl_code)) {

                        $query->where('asm.security_money_code', 'LIKE', "%{$request->appl_code}%");
                    }

                    if ($request->appl_status == "0" || !empty($request->appl_status)) {

                        $query->where('asm.is_active', $request->appl_status);
                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {

                        $query->whereBetween('asm.application_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    } elseif (!empty($request->start_date)) {

                        $query->where('asm.application_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    } elseif (!empty($request->end_date)) {

                        $query->where('asm.application_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }

                })
                ->where(function($query2) use ($searchValue){
                    if (!empty($searchValue)) {
                        $query2->where('security_money_code', 'like', '%' . $searchValue . '%');
                    }
                });

            $totalRecordswithFilter = AppSecurityMoney::where([['is_delete', 0], ['is_active', 1]])->count();

            $totalRecords = $totalRecordswithFilter;

            $tempQueryData = clone $allData;
            if (!empty($start_date) || !empty($end_date) || !empty($employee_id) || !empty($designation_id) || !empty($department_id) || !empty($appl_code) || !empty($appl_status) || !empty($searchValue) || !empty($appl_status) ) {
                $totalRecords = $tempQueryData->count();
            }

            $allData = $allData->skip($start)->take($rowperpage)->select('asm.*')->get();
            $data      = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {


                $IgnoreArray = array();
                $passport = $this->getPassport($row, 'index');
                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['application_date']      = (new DateTime($row->application_date))->format('d-m-Y');
                $data[$key]['security_money_code']        = $row->security_money_code;
                $data[$key]['effective_date']        =(new DateTime($row->effective_date))->format('d-m-Y');;

                $data[$key]['collection_amount']        = $row->collection_amount;
                $data[$key]['collection_type']        = $row->collection_type;

                // $data[$key]['employee_name']      = $row->emp_id;
                $data[$key]['employee_name']      = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                $data[$key]['designation_name']      = $row->designation['short_name'];
                $data[$key]['branch']        = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";

                $data[$key]['collection_by']        = $row->collectionBy['emp_name'] . " [" . $row->collectionBy['emp_code'] . "]";

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
                "recordsTotal"    => intval($totalRecordswithFilter),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data,
            );

            return response()->json($json_data);
        }
    }

    public function insert(Request $request, $status){


        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";
        $passport = $this->getPassport($request, 'store');

        $empInfo = HRS::fnForGetEmployees()->where('id', $request['employee_id'])->first();
        $designation_id = $empInfo->designation_id;

        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                       = new AppSecurityMoney();
                $appl->branch_id            = $request['branch_id'];
                $appl->security_money_code  = HRS::generateAppSecurityMoneyCode($request['branch_id']);
                $appl->collection_code      = HRS::generateAppSecurityMoneyCode($request['branch_id']);
                $appl->emp_id               = $request['employee_id'];
                $appl->designation_id       = $designation_id;
                $appl->collection_by        = $request['collection_by'];

                $appl->project_id           = $request['project_id'];
                $appl->collection_amount    = $request['collection_amount'];
                $appl->collection_month     = $request['collection_month'];
                $appl->collection_type      = $request['collection_type'];

                $appl->application_date     = (new DateTime($request['application_date']))->format('Y-m-d');
                $appl->exp_effective_date   = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date       = (new DateTime($request['exp_effective_date']))->format('Y-m-d');

                $appl->description          = $request['description'];
                $appl->is_active            = ($status == 'send') ? 3 : 0;
                $appl->company_id           = Common::getCompanyId();

                if ($status == 'send') {

                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(16, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 16);

                        if (isset($request->attachment) && count($request->attachment) > 0) {
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

                if (isset($request->attachment) && count($request->attachment) > 0) {
                    $this->uploadFiles($request->attachment);
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

    public function update(Request $request, $status)
    {

        // ss($request->all());
        $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";
        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;
        $departmentId = !empty($request['department_id']) ? $request['department_id'] : 0;

        $empInfo = HRS::fnForGetEmployees()->where('id', $request['employee_id'])->first();
        $designation_id = $empInfo->designation_id;

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                       = AppSecurityMoney::find(decrypt($request['security_money_id']));
                $appl->branch_id            = $request['branch_id'];
                // $appl->security_money_code  = HRS::generateAppSecurityMoneyCode($request['branch_id']);
                // $appl->collection_code      = HRS::generateAppSecurityMoneyCode($request['branch_id']);
                $appl->emp_id               = $request['employee_id'];
                $appl->designation_id       = $designation_id;
                $appl->collection_by        = $request['collection_by'];

                $appl->project_id           = $request['project_id'];
                $appl->collection_amount    = $request['collection_amount'];
                $appl->collection_month     = $request['collection_month'];
                $appl->collection_type      = $request['collection_type'];

                $appl->application_date     = (new DateTime($request['application_date']))->format('Y-m-d');
                $appl->exp_effective_date   = (new DateTime($request['exp_effective_date']))->format('Y-m-d');
                $appl->effective_date       = (new DateTime($request['exp_effective_date']))->format('Y-m-d');

                $appl->description          = $request['description'];
                $appl->is_active            = ($status == 'send') ? 3 : 0;
                $appl->company_id           = Common::getCompanyId();

                if (isset($request->fileIds) && count($request->fileIds) > 0) {
                    DB::table('hr_attachments')->where('foreign_key', decrypt($request['movement_id']))->where('ref_table_name', 'hr_app_movements')
                        ->whereNotIn('id', $request->fileIds)->delete();
                }

                if ($status == 'send') {

                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode' => 400,
                            'result_data' => ''
                        ], 400);
                    }


                    if($request['employee_id'] > 0){

                        $applicant = Employee::find($request['employee_id']);

                        $first_approval = CommonController::get_first_approval(16, $permission_for, $applicant);

                        if (empty($first_approval)) {
                            (new ApplicationProcessController)->approve($appl, 16);

                            if (isset($request->attachment) && count($request->attachment) > 0) {
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

                    }else{

                        (new ApplicationProcessController)->approve($appl, 16);
                        $appl->current_stage = null;

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);

                    }

                } else {
                    $appl->current_stage = null;
                }

                $appl->save();

                if (isset($request->attachment) && count($request->attachment) > 0) {
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

    public function uploadFiles($attachments, $fk = null)
    {

        foreach ($attachments as $key => $file) {

            DB::table('hr_attachments')->insert([
                'path' => Common::fileUpload($file, 'employee_movement', ''),
                'foreign_key' => (!empty($fk)) ? $fk : AppSecurityMoney::latest()->first()->id,
                'ref_table_name' => 'hr_app_movements',
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function get($id)
    {
        return CommonController::get_application('\\App\\Model\\HR\\AppSecurityMoney', $id, ['branch', 'employee', 'attachments']);
    }

    public function send($id)
    {
        return CommonController::send_application('\\App\\Model\\HR\\AppSecurityMoney', $id, 16);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\AppSecurityMoney', $id);
    }

}
