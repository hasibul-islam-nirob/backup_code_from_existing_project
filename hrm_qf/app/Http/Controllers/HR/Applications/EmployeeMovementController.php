<?php

namespace App\Http\Controllers\HR\Applications;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use App\Model\HR\EmployeeMovement;
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

class EmployeeMovementController extends Controller
{

    public function getPassport($requestData, $operationType, $Data = null)
    {
        $errorMsg = null;
        $rules    = array();

        if ($operationType == 'store' || $operationType == 'update') {

            $rules = array(
                // 'branch_id' => 'required',
                'movement_date' => 'required',
                // 'employee_id' => 'required',
                'start_time'     => 'required',
                'end_time'     => 'required',
                'location_to'  => 'required',
            );

            $attributes = array(
                'start_time'     => 'Start Time',
                'end_time'        => 'End Time',
                // 'branch_id' => 'Branch',
                'movement_date' => 'Movement Date',
                // 'employee_id' => 'Employee',
                'location_to'   => 'Area'

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
                $IgnoreArray = [ 'delete', 'edit', 'send', 'btnHide' => true];
            }

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

    public function index(Request $request){



        if ($request->isMethod('post')) {

            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            // $search = (empty($request->input('search.value'))) ? null : $request->input('search.value');

            $end_date            = (empty($request->end_date)) ? null : $request->end_date;
            $start_date            = (empty($request->start_date)) ? null : $request->start_date;
            $employee_id            = (empty($request->employee_id)) ? null : $request->employee_id;
            $designation_id            = (empty($request->designation_id)) ? null : $request->designation_id;
            $department_id            = (empty($request->department_id)) ? null : $request->department_id;
            $appl_code            = (empty($request->appl_code)) ? null : $request->appl_code;
            $appl_status            = (empty($request->appl_status)) ? null : $request->appl_status;
            $branch_to              = (empty($request->branch_to)) ? null : $request->branch_to;
            $movement_area          = (empty($request->movement_area)) ? null : $request->movement_area;
            $purpose                = (empty($request->purpose)) ? null : $request->purpose;
            $application_for        = (empty($request->application_for)) ? null : $request->application_for;
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $columnSortOrder = $order_arr[0]['dir'];
            $searchValue = $search_arr['value'];

            // $userEmpID = Auth::user()->emp_id;
            // ss($userEmpID);
            $zoneId            = (empty($request->zone_id)) ? null : $request->zone_id;
            $regionId            = (empty($request->region_id)) ? null : $request->region_id;
            $areaId            = (empty($request->area_id)) ? null : $request->area_id;
            $branchId          = (empty($request->branch_id)) ? null : $request->branch_id;

            $accessAbleBranchIds = Common::getBranchIdsForAllSection([
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);

            // dd($accessAbleBranchIds);

            $userInfo = Auth::user();
            $loginUserDeptId = HRS::getUserDepartmentId($userInfo['emp_id']);
            $statusArray = array_column($this->GlobalRole, 'set_status');

            $allData = EmployeeMovement::from('hr_app_movements AS apl')
                ->where('apl.is_delete', 0)
                ->where(function ($query3) use ($purpose) {
                    if (!empty($purpose)) {
                        $query3->where('apl.reason', 'like', '%' . $purpose . '%');
                    }
                })
                ->where(function ($query4) use ($application_for) {
                    if (!empty($application_for)) {
                        $query4->where('apl.application_for', '=', $application_for);
                    }
                })
                ->whereIn('apl.branch_id', $accessAbleBranchIds)

                ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $accessAbleBranchIds){
                    ## Calling Permission Query Function
                    HRS::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $accessAbleBranchIds, $alies = 'apl');

                })

                ->when(true, function ($query) use ($columnName, $columnSortOrder, $request) { //Ordering

                    if ($columnName == "movement_code") {
                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "employee_name") {

                        if (empty($request->department_id) && empty($request->designation_id)) {

                            $query->join('hr_employees as e', function ($join) {

                                $join->on('apl.emp_id', '=', 'e.id');
                            });
                        }

                        $query->orderBy('e.emp_name', $columnSortOrder);
                    } elseif ($columnName == "branch_id") {

                        $query->join('gnl_branchs as b', function ($join) {

                            $join->on('apl.branch_id', '=', 'b.id');
                        });

                        $query->orderBy('b.branch_name', $columnSortOrder);
                    } elseif ($columnName == "movement_date") {

                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "status") {

                        $query->orderBy('apl.is_active', $columnSortOrder);
                    } elseif ($columnName == "reason") {

                        $query->orderBy("apl." . $columnName, $columnSortOrder);
                    } elseif ($columnName == "id") {

                        $query->orderBy('apl.movement_date', 'desc');
                    }
                })
                ->when(true, function ($query) use ($request) {


                    if (!empty($request->designation_id) && !empty($request->department_id)) {

                        $query->join('hr_employees as e', function ($join) use ($request) {
                            $join->on('apl.emp_id', '=', 'e.id')
                                ->where('e.designation_id', $request->designation_id)
                                ->where('e.department_id', $request->department_id);
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
                    }

                    // if (!empty($request->employee_id)) {
                    //     $query->where('apl.emp_id', $request->employee_id);
                    //     $query->orWhere('apl.emp_id', 0);
                    // }

                    if (!empty($request->appl_code)) {

                        $query->where('apl.movement_code', 'LIKE', "%{$request->appl_code}%");
                    }

                    // if ($request->appl_status == "0" || !empty($request->appl_status)) {

                    //     $query->where('apl.is_active', $request->appl_status);
                    // }
                    if (!empty($request->appl_status) && !empty($request->employee_id)) {
                        $query->where('apl.emp_id', $request->employee_id);
                        $query->where('apl.is_active', $request->appl_status);
                    }
                    elseif (!empty($request->appl_status) && empty($request->employee_id)) {
                        $query->where('apl.is_active', $request->appl_status);

                    }elseif (empty($request->appl_status) && !empty($request->employee_id)) {
                        $query->where('apl.emp_id', $request->employee_id);
                        $query->orWhere('apl.emp_id', 0);
                    }

                    if (!empty($request->start_date) && !empty($request->end_date)) {

                        $query->whereBetween('apl.movement_date', [(new DateTime($request->start_date))->format('Y-m-d'), (new DateTime($request->end_date))->format('Y-m-d')]);
                    } elseif (!empty($request->start_date)) {

                        $query->where('apl.movement_date', '>=', (new DateTime($request->start_date))->format('Y-m-d'));
                    } elseif (!empty($request->end_date)) {

                        $query->where('apl.movement_date', '<=', (new DateTime($request->end_date))->format('Y-m-d'));
                    }
                })
                ->where(function ($query2) use ($searchValue) {
                    if (!empty($searchValue)) {
                        $query2->where('movement_code', 'like', '%' . $searchValue . '%');
                    }
                })
                ->where(function ($query3) use ($movement_area, $branch_to) {

                    if(!empty($branch_to) && !empty($movement_area)){
                        $query3->where('apl.location_to', 'like', '%' . $movement_area . '%');
                        $query3->where('apl.location_to_branch', '=', $branch_to);
                    }
                    elseif( !empty($branch_to) && empty($movement_area)){
                        $query3->where('apl.location_to_branch', '=', $branch_to);
                    }
                    elseif( empty($branch_to) && !empty($movement_area)){
                        $query3->where('apl.location_to', 'like', '%' . $movement_area . '%');
                    }

                    if($branch_to == 1){ ## TTL like company
                        if(count(HRS::getUserAccesableBranchIds()) == 1){
                            $query3->orWhereNull('apl.location_to_branch');
                        }
                    }

                });


            // $totalRecords = EmployeeMovement::where('is_delete', 0)->count();
            $totalRecords = $allData->count();
            $totalRecordswithFilter = $totalRecords;

            $tempQueryData = clone $allData;
            if (!empty($start_date) || !empty($end_date) || !empty($employee_id) || !empty($designation_id) || !empty($department_id) || !empty($appl_code) || !empty($appl_status) || !empty($searchValue) || !empty($appl_status)) {
                $totalRecordswithFilter = $tempQueryData->count();
            }

            // dd($totalRecordswithFilter,$totalRecords );

            $allData = $allData->skip($start)->take($rowperpage)->select('apl.*')->get();

            $branchToAfterQuery = array_filter($allData->pluck("location_to_branch")->unique()->toArray());

            $branchData = Common::getBranchIdsForAllSection([
                'branchArr' => $branchToAfterQuery,
                'fnReturn' => 'array2D'
            ]);

            $data      = array();
            $sno = $start + 1;

            // dd($allData, count($allData));
            // $totalRecords = EmployeeMovement::select('count(*) as allcount')->where('is_delete', 0)->count();
            // $totalRecordswithFilter = EmployeeMovement::select('count(*) as allcount')->where('is_delete', 0)
            // ->where('movement_code', 'like', '%' . $searchValue . '%')->count();


            foreach ($allData as $key => $row) {

                $IgnoreArray = array();

                $passport = $this->getPassport($row, 'index');

                if ($passport['isValid'] == false) {
                    $IgnoreArray = $passport['message'];
                }

                $reasonData = $appl_for = '';
                if (!empty($row->reason) && $row->reason == 'official') {
                    $reasonData = 'Official';
                } elseif (!empty($row->reason) && $row->reason == 'personal') {
                    $reasonData = 'Personal';
                } else {
                    $reasonData = 'Others';
                }

                if (!empty($row->application_for) && $row->application_for == 'early') {
                    $appl_for = 'Early';
                } elseif (!empty($row->application_for) && $row->application_for == 'late') {
                    $appl_for = 'Late';
                } 
                elseif (!empty($row->application_for) && $row->application_for == 'absent') {
                    $appl_for = 'Absent';
                } else {
                    $appl_for = '-';
                }

                if (!empty($row->employee['emp_name'])) {
                    $empName = $row->employee['emp_name'] . " [" . $row->employee['emp_code'] . "]";
                } else {
                    $empName = 'All Employee';
                }


                $data[$key]['id']                 = $sno;
                $data[$key]['branch']        = $row->branch['branch_name'] . " [" . $row->branch['branch_code'] . "]";
                $data[$key]['employee_name']      = $empName;
                $data[$key]['movement_date']      = (new DateTime($row->movement_date))->format('d-m-Y');
                $data[$key]['appl_date']      = (new DateTime($row->appl_date))->format('d-m-Y');
                $data[$key]['movement_code']        = $row->movement_code;
                $data[$key]['start_time']        = date("h:i a", strtotime($row->start_time));
                $data[$key]['end_time']        = date("h:i a", strtotime($row->end_time));
                $data[$key]['reason']        = $reasonData;
                $data[$key]['appl_for']      = $appl_for;
                $data[$key]['location_to']   = $row->location_to;
                // $data[$key]['location_to_branch']   = "";

                if (isset($branchData[$row->location_to_branch])) {
                    $data[$key]['location_to_branch'] = $branchData[$row->location_to_branch]  . " - ". $row->location_to;
                } else {
                    $data[$key]['location_to_branch'] = $row->location_to;
                }

                // $data[$key]['application_for']        = !empty($row->application_for) ? $row->application_for : '';


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
        // ss($request->all());

        $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        $companyId = Common::getCompanyId();
        $branchId  = $request['branch_id'];

        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;
        $departmentId = !empty($request['department_id']) ? $request['department_id'] : 0;
        $location_to  = !empty($request['location_to']) ? $request['location_to'] : null;
        $location_to_branch = !empty($request['location_to_branch']) ? $request['location_to_branch']: null;

        if($employeeId != 0){
            $departmentId = HRS::getUserDepartmentId($employeeId);
        }

        ## Attendance Rules Query Start
        $attendanceRules = DB::table('hr_attendance_rules')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->selectRaw('start_time, end_time, ext_start_time, late_accept_minute, early_accept_minute, ot_cycle_minute, eff_date_start, eff_date_end')
            ->orderBy('id', 'desc')
            ->first();
        // dd($departmentId);
        // ss($attendanceRules, $attendanceRules->start_time);
        ## Attendance Rules Query End


        // getUserDepartmentId
        ## Set Application For Start


        $applStartTime  = $request['start_time'];
        $applEndTime    = $request['end_time'];
        $applReason     = $request['reason'];
        $applicationFor = '';

        $onDuty = date("H:s", strtotime($attendanceRules->start_time));
        $offDuty = date("H:s", strtotime($attendanceRules->end_time));
        $lateAccept = $attendanceRules->late_accept_minute;
        $earlyAccept = $attendanceRules->early_accept_minute;

        $tempAppStartTime = new DateTime($request['start_time']);
        $tempAppEndTime = new DateTime($request['end_time']);
        $tempOnDuty = new DateTime($attendanceRules->start_time);
        $tempOffDuty = new DateTime($attendanceRules->end_time);


        ## Start Time
        $startTimeDiff = $tempAppStartTime->diff($tempOnDuty);
        $startHour   = (int) $startTimeDiff->format('%h');
        $startMinit  = (int) $startTimeDiff->format('%i');

        $totalStartMinutes = ($startHour * 60);
        $totalStartMinutes += $startMinit;
        ## Start Time

        ##End Time
        $endTimeDiff = $tempAppEndTime->diff($tempOffDuty);
        $endHour   = (int) $endTimeDiff->format('%h');
        $endMinit  = (int) $endTimeDiff->format('%i');

        $totalEndMinutes = ($endHour * 60);
        $totalEndMinutes += $endMinit;

        ##End Time

        $applicationFor = '';
        if (($applStartTime <= $onDuty || $totalStartMinutes <= $lateAccept)  && $applEndTime >= $offDuty) {
            $applicationFor = 'absent';
        } elseif ($applStartTime <= $onDuty  && $applStartTime < $offDuty) {
            $applicationFor = 'late';
        } elseif (($applStartTime > $onDuty && $totalStartMinutes > $lateAccept)) {
            $applicationFor = 'early';
        }
        // else{
        //     $applicationFor = 'Others';
        // }
        // ss($applicationFor, $onDuty, $offDuty, $applStartTime, $applEndTime);
        ## Set Application For End

        $passport = $this->getPassport($request, 'store');
        // ss($passport, $request->all());
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                     = new EmployeeMovement();
                $appl->branch_id          = $branchId;
                $appl->department_id      = $departmentId;
                $appl->movement_code      = HRS::generateMovementCode($request['branch_id']);
                // $appl->emp_id             = $request['employee_id'];
                $appl->emp_id             = $employeeId;
                $appl->description        = $request['description'];
                $appl->start_time         = $applStartTime;
                $appl->end_time           = $applEndTime;
                $appl->location_to        = $location_to;
                $appl->location_to_branch = $location_to_branch;
                $appl->reason             = $applReason;
                $appl->application_for    = $applicationFor;
                $appl->movement_date        = (new DateTime($request['movement_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->appl_date     = (new DateTime($request['appl_date']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = $companyId;

                if ($status == 'send') {
                    // ss($status);
                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode' => 400,
                            'result_data' => ''
                        ], 400);
                    }



                    if ($request['employee_id'] > 0) {

                        $applicant = Employee::find($request['employee_id']);

                        $first_approval = CommonController::get_first_approval(14, $permission_for, $applicant);

                        if (empty($first_approval)) {
                            (new ApplicationProcessController)->approve($appl, 14);

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
                        (new ApplicationProcessController)->approve($appl, 14);
                        $appl->current_stage = null;
                    }

                    // $applicant = Employee::find($request['employee_id']);

                    // $first_approval = CommonController::get_first_approval(14, $permission_for, $applicant);

                    // if (empty($first_approval)) {
                    //     (new ApplicationProcessController)->approve($appl, 14);

                    //     if (isset($request->attachment) && count($request->attachment) > 0) {
                    //         $this->uploadFiles($request->attachment);
                    //     }

                    //     DB::commit();

                    //     return response()->json([
                    //         'message'    => "Application Sent and Approved!!",
                    //         'status' => 'success',
                    //         'statusCode' => 200,
                    //         'result_data' => '',
                    //     ], 200);
                    // }
                    // $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    // ss($request->all(), $employeeId);
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



    //================================

    public function Backup_insert(Request $request, $status)
    {
        $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        ## Attendance Rules Query Start
        $attendanceRules = DB::table('hr_attendance_rules')
            ->where([['is_delete', 0], ['is_active', 1]])
            // ->whereIn('branch_id', $accessAbleBranchIds)
            // ->where(function ($query) use ($monthStartDate, $monthEndDate) {
            //     if (!empty($monthStartDate) && !empty($monthEndDate)) {
            //         $query->where([['eff_date_start', '<=', $monthEndDate]]);
            //         $query->where(function ($query2) use ($monthStartDate) {
            //             $query2->whereNull('eff_date_end');
            //             $query2->orWhere([['eff_date_end', '>=', $monthStartDate]]);
            //         });
            //     }
            // })
            ->selectRaw('start_time, end_time, ext_start_time, late_accept_minute, early_accept_minute, ot_cycle_minute, eff_date_start, eff_date_end')
            ->get();
        ss($attendanceRules);
        ## Attendance Rules Query End

        $passport = $this->getPassport($request, 'store');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl                     = new EmployeeMovement();
                $appl->branch_id          = $request['branch_id'];
                $appl->movement_code        = HRS::generateMovementCode($request['branch_id']);
                $appl->emp_id             = $request['employee_id'];
                $appl->description        = $request['description'];
                $appl->start_time        = $request['start_time'];
                $appl->end_time        = $request['end_time'];
                $appl->reason        = $request['reason'];
                $appl->application_for        = $request['application_for'];
                $appl->movement_date        = (new DateTime($request['movement_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->appl_date     = (new DateTime($request['appl_date']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;
                $appl->company_id = Common::getCompanyId();

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

                    $applicant = Employee::find($request['employee_id']);

                    $first_approval = CommonController::get_first_approval(14, $permission_for, $applicant);

                    if (empty($first_approval)) {
                        (new ApplicationProcessController)->approve($appl, 14);

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

    //================================

    public function uploadFiles($attachments, $fk = null)
    {

        foreach ($attachments as $key => $file) {

            DB::table('hr_attachments')->insert([
                'path' => Common::fileUpload($file, 'employee_movement', ''),
                'foreign_key' => (!empty($fk)) ? $fk : EmployeeMovement::latest()->first()->id,
                'ref_table_name' => 'hr_app_movements',
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function update(Request $request, $status)
    {

        // ss($request->all());
        $permission_for = ($request['branch_from_id'] == 1) ? "ho" : "bo";

        $branchId = !empty($request['branch_id']) ? $request['branch_id'] : 1;
        $employeeId = !empty($request['employee_id']) ? $request['employee_id'] : 0;
        $departmentId = !empty($request['department_id']) ? $request['department_id'] : 0;
        $location_to  = !empty($request['location_to']) ? $request['location_to'] : null;
        $location_to_branch = !empty($request['location_to_branch']) ? $request['location_to_branch']: null;

        // ss($request->all(),  $branchId, $employeeId, $departmentId);

        $passport = $this->getPassport($request, 'update');
        if ($passport['isValid']) {
            DB::beginTransaction();
            try {
                $appl = EmployeeMovement::find(decrypt($request['movement_id']));

                $appl->branch_id          = $branchId;
                // $appl->branch_id          = $request['branch_id'];
                $appl->movement_code        = HRS::generateMovementCode($request['branch_id']);
                $appl->emp_id             = $employeeId;
                // $appl->emp_id             = $request['employee_id'];
                $appl->department_id             = $departmentId;
                $appl->description        = $request['description'];
                $appl->start_time        = $request['start_time'];
                $appl->end_time        = $request['end_time'];
                $appl->location_to        = $location_to;
                $appl->location_to_branch = $location_to_branch;
                $appl->reason        = $request['reason'];
                $appl->application_for        = $request['application_for'];
                $appl->movement_date        = (new DateTime($request['movement_date']))->format('Y-m-d');
                $appl->exp_effective_date = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->effective_date     = (new DateTime($request['appl_date']))->format('Y-m-d');
                $appl->appl_date     = (new DateTime($request['appl_date']))->format('Y-m-d');

                $appl->is_active  = ($status == 'send') ? 3 : 0;

                if (isset($request->fileIds) && count($request->fileIds) > 0) {
                    DB::table('hr_attachments')->where('foreign_key', decrypt($request['movement_id']))->where('ref_table_name', 'hr_app_movements')
                        ->whereNotIn('id', $request->fileIds)->delete();
                }

                if ($status == 'send') {
                    // ss($request->all(),  $branchId, $employeeId, $departmentId);

                    $passport = $this->getPassport($request, 'send');

                    if (!$passport['isValid']) {
                        return response()->json([
                            'message'    => $passport['message'],
                            'status' => 'error',
                            'statusCode' => 400,
                            'result_data' => ''
                        ], 400);
                    }


                    if ($request['employee_id'] > 0) {

                        $applicant = Employee::find($request['employee_id']);

                        $first_approval = CommonController::get_first_approval(14, $permission_for, $applicant);

                        if (empty($first_approval)) {
                            (new ApplicationProcessController)->approve($appl, 14);

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
                    } else {

                        (new ApplicationProcessController)->approve($appl, 14);
                        $appl->current_stage = null;

                        return response()->json([
                            'message'    => "Application Sent and Approved!!",
                            'status' => 'success',
                            'statusCode' => 200,
                            'result_data' => '',
                        ], 200);
                    }
                    // $applicant = Employee::find($request['employee_id']);

                    // $first_approval = CommonController::get_first_approval(14, $permission_for, $applicant);

                    // if (empty($first_approval)) {
                    //     (new ApplicationProcessController)->approve($appl, 14);

                    //     if (isset($request->attachment) && count($request->attachment) > 0) {
                    //         $this->uploadFiles($request->attachment, $appl->id);
                    //     }

                    //     DB::commit();

                    //     return response()->json([
                    //         'message'    => "Application Sent and Approved!!",
                    //         'status' => 'success',
                    //         'statusCode' => 200,
                    //         'result_data' => '',
                    //     ], 200);
                    // }
                    // $appl->current_stage = CommonController::get_stage($first_approval);
                } else {
                    // ss($request->all(), $employeeId);
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

    public function get($id)
    {
        return CommonController::get_application('\\App\\Model\\HR\\EmployeeMovement', $id, ['branch', 'branch_to', 'employee', 'attachments', 'reasons', 'created_by', 'approve_by']);
    }

    public function send($id)
    {
        return CommonController::send_application('\\App\\Model\\HR\\EmployeeMovement', $id, 14);
    }

    public function delete($id)
    {
        return CommonController::delete_application('\\App\\Model\\HR\\EmployeeMovement', $id);
    }

    public function finish($appl, $exe_date)
    {
        DB::beginTransaction();
        try {
            $appl->approved_by = Auth::user()->id;
            $appl->save();
            DB::commit();
        } catch (\Exception $e) {

            DB::rollback();
        }
    }


    // {create}/{approved}
    public function getEmpInfoFromSysEmpID($create = null, $approved = null){
        // dd($create, $approved);
        $employeeData = DB::table('hr_employees')
            ->where([['is_delete', 0]])
            ->whereIn('id', [$create, $approved])
            ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
            ->pluck('emp_name', 'id')
            ->toArray();
        // dd($employeeData);
        return response()->json($employeeData);
    }
}
