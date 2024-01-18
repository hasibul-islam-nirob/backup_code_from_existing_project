<?php

namespace App\Http\Controllers\HR\Applications;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use App\Model\HR\FiscalYear;
use App\Model\HR\EmployeeLeave;
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
use Illuminate\Support\Facades\Date;
use Symfony\Component\VarDumper\Cloner\Data;

class EmployeeLeaveController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                'leave_cat_id' => 'required',
                // 'employee_id' => 'required',
                // 'resp_employee_id' => 'required',
                // 'branch_id' => 'required',
                'date_from'        => 'required',
                'date_to'     => 'required',
                // 'description'     => 'required',
                'leave_date'     => 'required',

            );

            $attributes = array(
                'leave_cat_id' => 'Leave category',
                // 'employee_id' => 'Employee',
                // 'resp_employee_id' => 'Responsible employee',
                // 'branch_id' => 'Branch',
                'date_from'        => 'Date from',
                'date_to'     => 'Date to',
                'description'     => 'Description',
                'leave_date'     => 'Application date',
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
                $IgnoreArray = ['delete', 'edit', 'send', 'btnHide' => true];
            }

            // 'view',

            $errorMsg = $IgnoreArray;

            //dd($errorMsg);
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

            $totalRecords = EmployeeLeave::select('count(*) as allcount')->where('is_delete', 0)->count();
            $totalRecordswithFilter = EmployeeLeave::select('count(*) as allcount')->where('is_delete', 0)->where('leave_code', 'like', '%' . $searchValue . '%')->count();

            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchId          = (empty($request->branch_id)) ? null : $request->branch_id;

            $selBranchArr = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData  = EmployeeLeave::from('hr_app_leaves AS apl')
                ->where('apl.is_delete', 0)
                ->whereIn('apl.branch_id', $selBranchArr)
                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId){
                    if(Common::isSuperUser() == true || Common::isDeveloperUser() == true){
                        ## nothing to do
                    }
                    else {

                        $tmpBranch = HRS::getUserAccesableBranchIds();
                        if(in_array(101, $statusArray)){
                            ## All Data for Permitted Branches
                            ## nothing to do
                            // $query->whereIn('apl.branch_id', $selBranchArr)
                        }
                        elseif(in_array(102, $statusArray)){
                            ## All Branch Data Without HO
                            $perQuery->where('apl.branch_id' , '<>' ,1);
                        }
                        elseif(in_array(103, $statusArray)){
                            ## All Data Only HO
                            $perQuery->where('apl.branch_id', 1);
                        }
                        elseif(in_array(104, $statusArray)){
                            ## All data for own department of permitted branches
                            // $perQuery->whereIn('apl.branch_id', $tmpBranch);
                            $perQuery->where('apl.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(105, $statusArray)){
                            ## All data for own department of all branches without HO
                            $perQuery->where('apl.branch_id', '<>' , 1);
                            $perQuery->where('apl.department_id', $loginUserDeptId);
                        }
                        elseif(in_array(106, $statusArray)){
                            ## All data for own department only HO
                            $perQuery->where([['apl.branch_id', 1],['apl.department_id', $loginUserDeptId]]);
                        }
                        else{
                            $perQuery->where('apl.created_by', $userInfo['id']);
                            if (!empty($userInfo['emp_id'])) {
                                $perQuery->orWhere('apl.emp_id', $userInfo['emp_id']);
                            }
                        }

                    }
                })
                ->when(true, function ($query) use ($columnName, $columnSortOrder, $request) { //Ordering
                    if ($columnName == "leave_code") {
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "leave_cat") {
                        if (empty($request->leave_type_cat_id)) {
                            $query->join('hr_leave_category as lc', function ($join) {
                                $join->on('apl.leave_cat_id', '=', 'lc.id');
                            });
                        }
                        $query->orderBy('lc.name', $columnSortOrder);
                    } elseif ($columnName == "employee_name") {
                        if (empty($request->department_id) && empty($request->designation_id)) {
                            $query->join('hr_employees as e', function ($join) {
                                $join->on('apl.emp_id', '=', 'e.id');
                            });
                        }
                        $query->orderBy('e.emp_name', $columnSortOrder);
                    } elseif ($columnName == "branch") {
                        $query->join('gnl_branchs as b', function ($join) {
                            $join->on('apl.branch_id', '=', 'b.id');
                        });
                        $query->orderBy('b.branch_name', $columnSortOrder);
                    } elseif ($columnName == "leave_date") {
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "date_from") {
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "date_to") {
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "status") {
                        $query->orderBy('apl.is_active', $columnSortOrder);
                    } elseif ($columnName == "id") {
                        $query->orderBy('apl.id', 'desc');
                    }
                })
                ->when(true, function ($query) use ($request, $userInfo, $searchValue, $selBranchArr) {

                    if (!empty($request->designation_id) && !empty($request->department_id) && !empty($request->emp_code)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id)
                                ->where('e.emp_code', 'LIKE', "%{$request->emp_code}%");
                        });
                    } elseif (!empty($request->designation_id) && !empty($request->emp_code)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.emp_code', 'LIKE', "%{$request->emp_code}%");
                        });
                    } elseif (!empty($request->department_id) && !empty($request->emp_code)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id)
                                ->where('e.emp_code', 'LIKE', "%{$request->emp_code}%");
                        });
                    } elseif (!empty($request->designation_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id);
                        });
                    } elseif (!empty($request->department_id)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.department_id', $request->department_id);
                        });
                    } elseif (!empty($request->emp_code)) {
                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.emp_code', 'LIKE', "%{$request->emp_code}%");
                        });

                    }elseif (!empty($request->leave_cat_id)) {
                        $query->join('hr_leave_category as hlc', function ($join) use ($request) {
                            $join->on('apl.leave_cat_id', '=', 'hlc.id')
                                ->where('hlc.id', $request->leave_cat_id);
                        });
                    }


                    if (!empty($request->employee_id)) {
                        $query->where('apl.emp_id', $request->employee_id);
                    }
                    if (!empty($request->appl_code)) {
                        $query->where('apl.leave_code', 'LIKE', "%{$request->appl_code}%");
                    }
                    if ($request->appl_status == "0" || !empty($request->appl_status)) {
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    if (!empty($request->start_date) && !empty($request->end_date)) {
                        $query->where('apl.date_from', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                        $query->where('apl.date_to', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    } elseif (!empty($request->start_date)) {
                        $query->where('apl.date_from', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    } elseif (!empty($request->end_date)) {
                        $query->where('apl.date_to', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }

                    // if (Common::isSuperUser() == false && Common::isDeveloperUser() == false) {
                    //     ## for all data action
                    //     if (Common::isActionPermitedForThisUser($actionAllData = 21) == false) {
                    //         if (!empty($userInfo['emp_id'])) {
                    //             $query->where('apl.emp_id', $userInfo['emp_id']);
                    //         } else if (!empty($userInfo['id'])) {
                    //             $query->where('apl.created_by', $userInfo['id']);
                    //         }

                    //         ## for all without ho == 22
                    //         if (Common::isActionPermitedForThisUser($actionAllData = 22) == true && Common::isHeadOffice() == true) {
                    //             $query->orWhere('apl.branch_id', '<>', $this->hoId);
                    //         }
                    //         ## for only ho == 23
                    //         elseif (Common::isActionPermitedForThisUser($actionAllData = 23) == true && Common::isHeadOffice() == true) {
                    //             $query->orWhere('apl.branch_id', $this->hoId);
                    //         }
                    //     }
                    // }
                })
                ->where(function($query2) use ($searchValue){
                    if (!empty($searchValue)) {
                        $query2->where('leave_code', 'like', '%' . $searchValue . '%');
                    }
                });

            $totalRecords = $allData->count();
            $totalRecordswithFilter = $totalRecords;

            $allData = $allData->skip($start)->take($rowperpage)->select('apl.*')->get();

            $data      = array();
            $sno = $start + 1;

            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                if (!empty($row->employee['emp_name'])) {
                    $empName = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                }else{
                    $empName = 'All Employee';
                }

                if (!empty($row->resp_employee['emp_name'])) {
                    $respEmpName = $row->resp_employee['emp_name'] . " (" . $row->resp_employee['emp_code'] . ")";
                }else{
                    $respEmpName = '-';
                }

                $data[$key]['id']                 = $sno;
                $data[$key]['branch']             = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $empName;
                $data[$key]['resp_employee_name']      = $respEmpName;
                $data[$key]['leave_date']      = (new DateTime($row->leave_date))->format('d-m-Y');
                $data[$key]['leave_code']        = $row->leave_code;
                $data[$key]['date_from']        = (new DateTime($row->date_from))->format('d-m-Y');
                $data[$key]['date_to']        = (new DateTime($row->date_to))->format('d-m-Y');
                $data[$key]['leave_cat']        = $row->leave_category->name;
                $data[$key]['reason']             = $row->reasons['reason'];

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

            // dd($data);

            $json_data = array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totalRecords),
                "recordsFiltered"   => intval($totalRecordswithFilter),
                "data"            => $data,
            );
            return response()->json($json_data);
        }
    }

    public function insert(Request $request, $status)
    {


        // ss($request->all());
        // $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";
        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        // ss($request->all());
        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;
        $departmentId = !empty($request['department_id']) ? $request['department_id'] : 0;

        
        ## Start Check Leave Elezable Or Not
        $dateFrom = (new DateTime($request['date_from']));
        $dateTo = (new DateTime($request['date_to']));
        $dateDiff = $dateFrom->diff($dateTo);
        $newAppForLeave = $dateDiff->d + 1;

        $leaveCategoryDetails = HRS::queryGetLeaveCategoryDetails();
        $allocatedLeave = $leaveCategoryDetails->where('leave_cat_id', $request['leave_cat_id'])->first();
        $allocatedLeave = optional($allocatedLeave)->allocated_leave;
        // $allocatedLeave = $leaveCategoryDetails->where('leave_cat_id', $request['leave_cat_id'])->pluck('allocated_leave');


        $app_date = (new DateTime($request['leave_date']))->format('Y-m-d');
        $fy = DB::table('gnl_fiscal_year')
            ->where('fy_start_date', '<=', $app_date)
            ->where('fy_end_date', '>=', $app_date)
            ->whereIn('fy_for', ['LFY', 'BOTH'])
            ->first();
        $totalLeaveUsed = $this->getConsumedLeave($employeeId, $request['leave_cat_id'], $fy); 

        $haveRemainLeave = $allocatedLeave - $totalLeaveUsed;
        $elizableForNewLeave = $haveRemainLeave - $newAppForLeave;

        if ($elizableForNewLeave < 0) {
            return response()->json([
                'message'    => "Your application not elizable for ".$newAppForLeave." leave, You applicable for ".($haveRemainLeave < 0 ? 0 : $haveRemainLeave)." leave !!",
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => '',
            ], 400);
        }
        ## End Check Leave Elezable Or Not

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $appl                   = new EmployeeLeave();
                $appl->branch_id        = $branchId;
                $appl->department_id        = $departmentId;
                // $appl->branch_id        = $request['branch_id'];
                $appl->leave_cat_id     = $request['leave_cat_id'];
                $appl->reason           = $request['reason'];
                $appl->leave_code       = HRS::generateLeaveCode($request['leave_cat_id'], $request['branch_id']);
                $appl->emp_id           = $employeeId;
                // $appl->emp_id           = $request['employee_id'];
                $appl->resp_emp_id      = $request['resp_employee_id'];
                $appl->description      = $request['description'];
                $appl->leave_date       = (new DateTime($request['leave_date']))->format('Y-m-d');
                $appl->effective_date        = (new DateTime($request['leave_date']))->format('Y-m-d');
                $appl->date_from = (new DateTime($request['date_from']))->format('Y-m-d');
                $appl->date_to     = (new DateTime($request['date_to']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = Common::getCompanyId();
                //$appl->attachment = ($request->hasFile('attachment')) ? Common::fileUpload($request->file('attachment'), 'employee_leave', '') : null;

                if ($status == 'send') {

                    try {

                        if ($request['employee_id'] > 0) {
                            if ($this->has_required_leave($appl->emp_id, $appl->leave_cat_id, $appl->date_from, $appl->date_to, $appl->leave_date)) {

                                $applicant = Employee::find($request['employee_id']);

                                $first_approval = CommonController::get_first_approval(5, $permission_for, $applicant);

                                if (empty($first_approval)) {
                                    (new ApplicationProcessController)->approve($appl, 5);

                                    DB::commit();

                                    return response()->json([
                                        'message'    => "Application Sent and Approved!!",
                                        'status' => 'success',
                                        'statusCode' => 200,
                                        'result_data' => '',
                                    ], 200);
                                }

                                $appl->current_stage = CommonController::get_stage($first_approval);
                                $appl->save();
                                if (isset($request->attachment) && count($request->attachment) > 0) {
                                    $this->uploadFiles($request->attachment);
                                }
                            } else {
                                ## error message show
                                return response()->json([
                                    'message'    => "Application Send Failed !!",
                                    'status' => 'error',
                                    'statusCode' => 400,
                                    'result_data' => '',
                                ], 400);
                            }
                        }else{
                            $appl->current_stage = null;
                            (new ApplicationProcessController)->approve($appl, 5);

                            if (isset($request->attachment) && count($request->attachment) > 0) {
                                $this->uploadFiles($request->attachment);
                            }

                        }


                    } catch (\Exception $e) {
                        return response()->json([
                            'message'    => $e->getMessage(),
                            'status' => 'error',
                            'statusCode' => 400,
                            'result_data' => '',
                        ], 400);
                    }
                } else {
                    // ss($request->all(), $employeeId);
                    $appl->current_stage = null;


                    if (isset($request->attachment) && count($request->attachment) > 0) {
                        $this->uploadFiles($request->attachment);
                    }
                }
                $appl->save();

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

    public function uploadFiles($attachments, $fk = null)
    {

        foreach ($attachments as $key => $file) {

            DB::table('hr_attachments')->insert([
                'path' => Common::fileUpload($file, 'employee_leave', ''),
                'foreign_key' => (!empty($fk)) ? $fk : EmployeeLeave::latest()->first()->id,
                'ref_table_name' => 'hr_app_leaves',
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function update(Request $request, $status)
    {

        // ss($request->all());
        $permission_for = ($request['branch_id'] == 1) ? "ho" : "bo";

        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;
        $departmentId = !empty($request['department_id']) ? $request['department_id'] : 0;

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {

            DB::beginTransaction();
            try {
                $appl = EmployeeLeave::find(decrypt($request['leave_id']));

                // $appl->branch_id          = $request['branch_id'];
                $appl->branch_id        = $branchId;
                $appl->department_id        = $departmentId;
                $appl->emp_id           = $employeeId;
                $appl->leave_cat_id          = $request['leave_cat_id'];
                $appl->reason          = $request['reason'];
                $appl->leave_code        = HRS::generateLeaveCode($request['leave_cat_id'], $request['branch_id']);
                // $appl->emp_id             = $request['employee_id'];
                $appl->resp_emp_id             = $request['resp_employee_id'];
                $appl->description        = $request['description'];
                $appl->leave_date        = (new DateTime($request['leave_date']))->format('Y-m-d');
                $appl->effective_date        = (new DateTime($request['leave_date']))->format('Y-m-d');
                $appl->date_from = (new DateTime($request['date_from']))->format('Y-m-d');
                $appl->date_to     = (new DateTime($request['date_to']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = Common::getCompanyId();

                if (isset($request->fileIds) && count($request->fileIds) > 0) {
                    DB::table('hr_attachments')->where('foreign_key', decrypt($request['leave_id']))->where('ref_table_name', 'hr_app_leaves')
                        ->whereNotIn('id', $request->fileIds)->delete();
                }

                if ($status == 'send') {

                    try {

                        if ($request['employee_id'] > 0) {

                            if ($this->has_required_leave($appl->emp_id, $appl->leave_cat_id, $appl->date_from, $appl->date_to, $appl->leave_date)) {

                                $applicant = Employee::find($request['employee_id']);

                                $first_approval = CommonController::get_first_approval(5, $permission_for, $applicant);

                                if (empty($first_approval)) {
                                    (new ApplicationProcessController)->approve($appl, 5);

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
                            }

                        }else{
                            $appl->current_stage = null;
                            (new ApplicationProcessController)->approve($appl, 5);

                            if (isset($request->attachment) && count($request->attachment) > 0) {
                                $this->uploadFiles($request->attachment);
                            }
                        }


                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json([
                            'message'    => $e->getMessage(),
                            'status' => 'error',
                            'statusCode' => 400,
                            'result_data' => '',
                        ], 400);
                    }
                } else {

                    $appl->current_stage = null;

                    if (isset($request->attachment) && count($request->attachment) > 0) {
                        $this->uploadFiles($request->attachment, $appl->id);
                    }
                }

                $appl->save();

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
        return CommonController::get_application('\\App\\Model\\HR\\EmployeeLeave', $id, ['branch', 'employee', 'resp_employee', 'leave_category', 'attachments']);
    }

    public function send($id)
    {
        try {
            $appl = EmployeeLeave::find(decrypt($id));

            if ($this->has_required_leave($appl->emp_id, $appl->leave_cat_id, $appl->date_from, $appl->date_to, $appl->leave_date)) {
                return CommonController::send_application('\\App\\Model\\HR\\EmployeeLeave', $id, 5);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message'    => $e->getMessage(),
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => '',
            ], 400);
        }
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\EmployeeLeave', $id);
    }

    public function finish($appl, $exe_date)
    {
        //DB::beginTransaction();
        try {
            //1) Employee Table [branch] Change.
            //DB::table('hr_employees')->where('id', $appl->emp_id)->update(['branch_id' => $appl->branch_to_id]);
            //2) User Table [branch] Change.
            //DB::table('gnl_sys_users')->where('emp_id', $appl->emp_id)->update(['branch_id' => $appl->branch_to_id]);
        } catch (\Exception $e) {
            //DB::rollback();
        }
        /* DB::table('hr_approval_queries')->insert([
            [
                'query' => "update `hr_employees` set `branch_id` = ". $appl->branch_to_id ." where `id` = ". $appl->emp_id,
                'appl_name' => 'leave',
                'execution_date' => $exe_date,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s')
            ],
            [
                'query' => "update `gnl_sys_users` set `branch_id` = ". $appl->branch_to_id ." where `emp_id` = ". $appl->emp_id,
                'appl_name' => 'leave',
                'execution_date' => ($exe_date == null) ? $appl->exp_effective_date : $exe_date,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s')
            ],
        ]); */

        //DB::commit();
    }

    public function has_required_leave($emp_id, $lv_cat_id, $lv_date_from, $lv_date_to, $lv_appl_date)
    {

        $emp = Employee::with('organizationData')->where('id', $emp_id)->first();

        if (empty($emp->organizationData)) {
            throw new \Exception('Organization data for this employee is not assigned!!');
        }

        $emp_rec_type_id = $emp->organizationData->rec_type_id;

        if (empty($emp_rec_type_id)) { //Employee recruitment type is not set-up
            throw new \Exception('Recruitment type for this employee is not assigned!!');
        }

        $lv_cat_config = [];
        $lv_cat = [];

        if (!empty($lv_cat_id)) {
            $lv_cat_config = DB::table('hr_leave_category_details')
                ->where([['leave_cat_id', $lv_cat_id], ['rec_type_id', $emp_rec_type_id]])
                ->where([['effective_date_from', '<=', date('Y-m-d')]])
                ->orderBy('effective_date_from', 'desc')
                ->first();
            $lv_cat = DB::table('hr_leave_category')->where('is_active', 1)->where('is_delete', 0)->find($lv_cat_id);
        } else {
            throw new \Exception('Error!!');
        }

        if (empty($lv_cat_config)) { //Non-Pay leave
            return true;
        } else {

            /* Check application sumbit policy */

            $app_submit_policy = $lv_cat_config->app_submit_policy;

            if ($app_submit_policy == 'after') {
                if (new DateTime($lv_date_from) >= new DateTime($lv_appl_date) || new DateTime($lv_date_to) >= new DateTime($lv_appl_date)) {
                    throw new \Exception($lv_cat->name . ' application can only be apply after leave consumed!!');
                }
            } elseif ($app_submit_policy == 'before') {
                if (new DateTime($lv_date_from) <= new DateTime($lv_appl_date) || new DateTime($lv_date_to) <= new DateTime($lv_appl_date)) {
                    throw new \Exception($lv_cat->name . ' application can only be apply before leave consumed!!');
                }
            } else {
                throw new \Exception('Error!!');
            }
            /* Check application sumbit policy */


            /* Check for probation period */
            if ($emp->permanent_date != null) {

                if ($lv_cat_config->capable_of_provision == '0' && (new DateTime($emp->permanent_date) > new DateTime($lv_appl_date))) {
                    throw new \Exception('Probationary employee can not apply for ' . $lv_cat->name);
                }
            } else { //Will work on later
                throw new \Exception('Parmanent date is not assigned!!');
            }
            /* Check for probation period */


            /* Application date should be less than current date */
            if (new DateTime($lv_appl_date) > new DateTime()) {
                throw new \Exception('Application date should be lower than or equal to current date');
            }
            /* Application date should be less than current date */


            /* Can't apply for multiple leave a day */

            /* Can't apply for multiple leave a day */


            $fy = DB::table('gnl_fiscal_year')->where('fy_start_date', '<=', date('Y-m-d'))->where('fy_end_date', '>=', date('Y-m-d'))->first();


            //$lv_applied = date_diff(date_create($lv_date_to), date_create($lv_date_from))->d + 1;
            //$lv_applied = (new DateTime($lv_date_from))->diff(new DateTime($lv_date_to));
            $lv_applied = (int)floor(abs(strtotime($lv_date_to) - strtotime($lv_date_from))) / (60 * 60 * 24) + 1;

            //dd($lv_date_to, $lv_date_from);
            //dd($lv_applied);

            /* Count consumed leaves (fiscal year wise)*/
            /* $appl_s = DB::table('hr_app_leaves')
                        ->where('leave_cat_id', $lv_cat_id)
                        ->where('is_active', 1)
                        ->orWhere('is_active', 3)
                        ->whereDate('leave_date', '>=', (new DateTime($fy->fy_start_date))->format('Y-m-d'))
                        ->whereDate('leave_date', '<=', (new DateTime($fy->fy_end_date))->format('Y-m-d'))
                        ->get();

            foreach($appl_s as $apl){
                //$dd = date_diff(date_create($apl->date_from), date_create($apl->date_to));
                $dd = ((int)floor(abs(strtotime($apl->date_to) - strtotime($apl->date_from)))/(60*60*24)) + 1;
                $lv_consumed += $dd;
            } */
            /* Count consumed leaves (fiscal year wise)*/



            /* Count elligible leaves (fiscal year wise)*/
            /* if($lv_cat->leave_type_uid == 1){//Pay
                if($lv_cat_config->consume_policy == 'eligible'){

                    $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                    if((new DateTime($lv_appl_date))->format('d') >= 22){ //After 22th day of the month, an employee can acquire a leave
                        $month_elapsed ++;
                    }
                    $lv_acquired = (int) floor(($lv_total/12) * $month_elapsed);

                }
                elseif($lv_cat_config->consume_policy == 'yearly_allocated'){
                    $lv_acquired = $lv_total;
                }
            }
            elseif($lv_cat->leave_type_uid == 3){//Earn

                if(empty($emp->join_date)){
                    throw new \Exception('Joining date is not assigned for this employee!!');
                }

                $lv_count_from = ($lv_cat_config->eligibility_counting_from == 'joining_date') ? $emp->join_date : $emp->parmanent_date;

                //dd($lv_cat_config->consume_after, date_diff(date_create($lv_count_from), date_create(date('Y-m-d')))->y);

                if((int)floor(abs(strtotime($lv_count_from) - strtotime($lv_appl_date)))/(365*60*60*24) < $lv_cat_config->consume_after){
                    throw new \Exception('You can\'t apply for earn leaves before '. $lv_cat_config->consume_after . ' years of employeement!');
                }

                if($lv_consumed + $lv_applied > $lv_cat_config->max_leave_entitle){
                    throw new \Exception('You can not consume more than ' . $lv_cat_config->max_leave_entitle . ' earn leaves in a year.');
                }

                if($lv_cat_config->consume_policy == 'eligible'){

                    $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                    if((new DateTime($lv_appl_date))->format('d') >= 22){ //After 22th day of the month, an employee can acquire a leave
                        $month_elapsed ++;
                    }
                    $lv_acquired = (int) floor(($lv_total/12) * $month_elapsed);

                }

            }
            elseif($lv_cat->leave_type_uid == 4){//Parental

                if(count($appl_s) >= $lv_cat_config->times_of_leave){
                    throw new \Exception('You can not apply for parental leave more than ' . $lv_cat_config->times_of_leave . ' times!!');
                }

                if($lv_cat_config->consume_policy == 'yearly_allocated'){
                    $lv_acquired = $lv_total;
                }

            } */
            /* Count elligible leaves (fiscal year wise)*/


            //dd($lv_acquired, $lv_consumed, $lv_applied);
            $lv_eligible = $this->getEligibleLeaev($emp, $lv_cat, $lv_cat_config, $fy);
            $lv_consumed = $this->getConsumedLeave($emp->id, $lv_cat->id, $fy);

            if ($lv_eligible >= ($lv_consumed + $lv_applied)) {
                //dd($lv_eligible, $lv_consumed, $lv_applied);
                return true;
            } elseif ($lv_eligible > $lv_consumed) {
                throw new \Exception('You can not apply for more than ' . ($lv_eligible - $lv_consumed) . ' days!! Your remaining ' . $lv_cat->name . ' is ' . ($lv_eligible - $lv_consumed) . ' days untill now.');
            } else {
                //dd($lv_eligible, $lv_consumed, $lv_applied);
                throw new \Exception('You can not apply!! You have alredy consumed all ' . $lv_eligible . ' of your elligible leaves untill now.');
            }
        }
    }

    public function getEligibleLeaev($emp, $lv_cat, $lv_cat_config, $fy)
    {

        $lv_total = $lv_cat_config->allocated_leave;
        $lv_acquired = 0;

        /* Count elligible leaves (fiscal year wise)*/
        if ($lv_cat->leave_type_uid == 1) { //Pay
            if ($lv_cat_config->consume_policy == 'eligible') {

                $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                if ((new DateTime(date('Y-m-d')))->format('d') >= 22) { //After 22th day of the month, an employee can acquire a leave
                    $month_elapsed++;
                }

                if (date('Y-m-d') > $fy->fy_end_date) {
                    $month_elapsed = 12;
                }

                $lv_acquired = (int) floor(($lv_total / 12) * $month_elapsed);
            } elseif ($lv_cat_config->consume_policy == 'yearly_allocated') {
                $lv_acquired = $lv_total;
            }
        } elseif ($lv_cat->leave_type_uid == 3) { //Earn

            if (empty($emp->join_date)) {
                throw new \Exception('Joining date is not assigned for this employee!!');
            }

            if (empty($emp->permanent_date)) {
                throw new \Exception('Permanent date is not assigned for this employee!!');
            }

            $lv_count_from = ($lv_cat_config->eligibility_counting_from == 'joining_date') ? $emp->join_date : $emp->permanent_date;

            if ((int)floor(abs(strtotime($lv_count_from) - strtotime(date('Y-m-d'))) / (365 * 60 * 60 * 24)) < $lv_cat_config->consume_after) {
                return 0;
            }

            if ($lv_cat_config->consume_policy == 'eligible') {

                $month_elapsed = abs(date('m') - (new DateTime($fy->fy_start_date))->format('m'));

                if ((new DateTime(date('Y-m-d')))->format('d') >= 22) { //After 22th day of the month, an employee can acquire a leave
                    $month_elapsed++;
                }

                if (date('Y-m-d') > $fy->fy_end_date) {
                    $month_elapsed = 12;
                }

                $lv_acquired = (int) floor(($lv_total / 12) * $month_elapsed);
            }

            return $lv_acquired;
        } elseif ($lv_cat->leave_type_uid == 4) { //Parental

            /* if(count($appl_s) >= $lv_cat_config->times_of_leave){
                throw new \Exception('You can not apply for parental leave more than ' . $lv_cat_config->times_of_leave . ' times!!');
            } */

            if ($lv_cat_config->consume_policy == 'yearly_allocated') {
                $lv_acquired = $lv_total;
            }
        }
        /* Count elligible leaves (fiscal year wise)*/
        return $lv_acquired;
    }

    public function getConsumedLeave($emp_id, $lv_cat_id, $fy)
    {
        $startDate = (new DateTime($fy->fy_start_date))->format('Y-m-d');
        $endDate = (new DateTime($fy->fy_end_date))->format('Y-m-d');

            ## Main <Old> Code
        /*
            $appl_s = DB::table('hr_app_leaves')
                ->where(['is_delete', 0])
                ->where('leave_cat_id', $lv_cat_id)
                ->whereIn('emp_id', [$emp_id, 0])
                ->where(function ($q) {
                    $q->where('is_active', 1);
                    $q->orWhere('is_active', 3);
                })
                ->whereDate('date_to', '>=', (new DateTime($fy->fy_start_date))->format('Y-m-d'))
                ->whereDate('date_from', '<=', (new DateTime($fy->fy_end_date))->format('Y-m-d'))
                ->select(DB::raw('SUM(DATEDIFF(date_to, date_from) +1 ) AS consumed'))
                ->first();
        */
        $empInfo = DB::table('hr_employees')->where('id', $emp_id)->first();
        $empJoinDate = optional($empInfo)->join_date;

        $appl_s = DB::table('hr_app_leaves')
        ->where('is_delete', 0)
        ->where('leave_cat_id', $lv_cat_id)
        ->whereIn('emp_id', [$emp_id, 0])
        ->where(function ($q) {
            $q->where('is_active', 1);
            $q->orWhere('is_active', 3);
        })
        ->where('leave_date','>',$empJoinDate)
        ->whereDate('date_to', '>=', (new DateTime($fy->fy_start_date))->format('Y-m-d'))
        ->whereDate('date_from', '<=', (new DateTime($fy->fy_end_date))->format('Y-m-d'))
        ->get();

        // ss($appl_s, $fy);

        $leaveCounter = 0;
        if( count($appl_s) > 0){
            foreach($appl_s as $rowLeave){
                $startDate = $rowLeave->date_from;
                $endDate = $rowLeave->date_to;
                $tempDate = $startDate;
    
                while ($tempDate <= $endDate) {
                    $leaveCounter += 1;
                    $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                }
            }
        }
        // dd($leaveCounter);

        return $leaveCounter;

        // return ((isset($appl_s->consumed)) ? $appl_s->consumed : 0);
    }

    public function getLeaveInfo(Request $request, $emp_id, $app_date)
    {

        try {
            $app_date = (new DateTime($app_date))->format('Y-m-d');

            $emp = Employee::with('organizationData')->where('id', $emp_id)->first();
            $lv_cat = DB::table('hr_leave_category')->where('is_active', 1)->where('is_delete', 0)->get();

            if (empty($emp->organizationData->rec_type_id)) {
                throw new \Exception('Recruitment type is not assigned.');
            }

            $emp_rec_type_id = $emp->organizationData->rec_type_id;

            $fy = DB::table('gnl_fiscal_year')
            ->where('fy_start_date', '<=', $app_date)
            ->where('fy_end_date', '>=', $app_date)
            ->whereIn('fy_for', ['LFY', 'BOTH'])
            ->first();

            $lv_info = [];

            // dd($fy);

            foreach ($lv_cat as $lvc) {

                // dd($lv_cat, $lvc->leave_type_uid);

                $lv_cat_config = DB::table('hr_leave_category_details')
                    ->where([['leave_cat_id', $lvc->id], ['rec_type_id', $emp_rec_type_id]])
                    ->where('effective_date_from', '<=', $app_date)
                    ->orderBy('effective_date_from', 'desc')
                    ->first();

                if (!empty($lv_cat_config) && $lvc->leave_type_uid != 2) {
                    $lv_info['consumed'][$lvc->name] = $this->getConsumedLeave($emp->id, $lvc->id, $fy);
                    $lv_info['eligible'][$lvc->name] = $this->getEligibleLeaev($emp, $lvc, $lv_cat_config, $fy);
                    $lv_info['allocated'][$lvc->name] = $lv_cat_config->allocated_leave;
                } else if ($lvc->leave_type_uid != 2) {
                    $lv_info['consumed'][$lvc->name] = 0;
                    $lv_info['eligible'][$lvc->name] = 0;
                    $lv_info['allocated'][$lvc->name] = 0;
                }
            }

            // dd($lv_info);

            return response()->json([
                'lv_info' => $lv_info,
                'emp' => $emp,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'    => $e->getMessage(),
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => '',
            ], 400);
        }
    }


    
}
