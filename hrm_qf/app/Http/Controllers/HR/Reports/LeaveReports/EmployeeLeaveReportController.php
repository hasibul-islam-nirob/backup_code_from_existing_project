<?php

namespace App\Http\Controllers\HR\Reports\LeaveReports;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use Illuminate\Support\Arr;
use Symfony\Component\VarDumper\Cloner\Data;

class EmployeeLeaveReportController extends Controller
{

    function LeaveAllocated($leaveCategoryDetails, $allAcclocMonth){
        $leaveItmes = [];
        $al = $sl = $cl = 0;
        foreach ($leaveCategoryDetails as $LeaveData) {

            if($LeaveData->short_form == "AL"){
                $monthL = $LeaveData->allocated_leave / $allAcclocMonth;
                $al = $monthL;
            }
            if($LeaveData->short_form == "SL"){
                $monthL = $LeaveData->allocated_leave / $allAcclocMonth;
                $sl = $monthL;
            }
            if($LeaveData->short_form == "CL"){
                $monthL = $LeaveData->allocated_leave / $allAcclocMonth;
                $cl = $monthL;
            }
        }
        $leaveItmes = [
            "AL" => $al,
            "SL" => $sl,
            "CL" => $cl,
        ];

        return $leaveItmes;
    }

    public function index()
    {

        return view('HR.Reports.LeaveReports.EmployeeLeaveReport.index');
    }


    public function reportBody(Request $request)
    {

        // if ($request->monthYear == '') return '';

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $searchByLeaveType = (empty($request->leave_type_id)) ? null : $request->leave_type_id;
        $searchBy = (empty($request->search_by)) ? null : $request->search_by;
        if ($searchBy == 1) {
            $flt_start_date = (empty($request->start_date_fy)) ? null : (new DateTime($request->start_date_fy))->format('Y-m-d');
            $flt_end_date = (empty($request->end_date_fy)) ? null : (new DateTime($request->end_date_fy))->format('Y-m-d');

        }elseif($searchBy == 2 || $searchBy == 5){
            // $flt_start_date = (empty($request->start_date_fy)) ? null : (new DateTime($request->start_date_fy))->format('Y-m-d');
            $flt_start_date = (empty($request->start_date_cy)) ? null : (new DateTime($request->start_date_cy))->format('Y-m-d');
            $flt_end_date = (empty($request->end_date_cy)) ? null : (new DateTime($request->end_date_cy))->format('Y-m-d');

        }elseif ($searchBy == 3) {
            $flt_start_date = (empty($request->start_date_dr)) ? null : (new DateTime($request->start_date_dr))->format('Y-m-d');
            $flt_end_date = (empty($request->end_date_dr)) ? null : (new DateTime($request->end_date_dr))->format('Y-m-d');
        }
        // dd($request->all(), $searchBy, $flt_start_date, $flt_end_date);


        $startDate = new DateTime($flt_start_date);
        $endDate = new DateTime($flt_end_date);

        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        $getMonthDate = HRS::getMonthNameDatesDays($startDate, $endDate);
        ## Date And Day Calculation Start

        ## Branch Array Query Start
        $selBranchArr = Common::getBranchIdsForAllSection([
            'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);
        ## Branch Array Query End

        ## Holiday Query
        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);

        
        ## Attendance Rules Query Start
        if ("get_attendance_rules_query") {
            $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
            // $action_for_lp = $attendanceRules->pluck('action_for_lp');

            $lateBypassDesignation = array_column($attendanceRules->toArray(), 'attendance_bypass');
            if(count($lateBypassDesignation) > 1){
                $lateBypassDesignation = explode(",", implode("", call_user_func_array('array_intersect', array_map('str_split', $lateBypassDesignation))));
                // array_pop($lateBypassDesignation);
            }else{
                $lateBypassDesignation = !empty($lateBypassDesignation) ? explode(",", $lateBypassDesignation[0]) : [];
            }

            // dd($lateBypassDesignation);


        }
        ## Attendance Rules Query End

        ## Employee Query Start
        if ("get_employee_query") {
            
            $statusArray = array_column($this->GlobalRole, 'set_status');
            $employeeData = HRS::fnForGetEmployees([
                'branchIds' => $selBranchArr,
                'departmentId' => $departmentId,
                'designationId' => $designationId,
                'employeeId' => $employeeId,
                'joinDateTo' => $monthEndDate,
                'fromDate' => $monthStartDate,
                'ignoreDesignations' => $lateBypassDesignation,
                'statusArray' => $statusArray,
                'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
                'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, gender, join_date, closing_date'
            ]);

            $employeeIdArr = $employeeData->pluck('id')->toArray();
            $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
            $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
            $empJoinDate = $employeeData->pluck('join_date')->first();
            // dd($employeeData, $empJoinDate);
        }
        ## Employee Query End

        ## Attendance Query Start
        if ("get_attendance_query") {
            $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
            $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
            $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
        }
        ## Attendance Query End

        ## Movement Query Start
        if ("get_movement_query") {
            $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        }
        ## Movement Query End

        ## Leave Query Start
        if ("get_leave_query") {
            $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            $leaveArr = $leaveData->groupBy('emp_id')->toArray();
            $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
        }
        ## Leave Query End
        $leaveCategoryData = DB::table("hr_leave_category")
            ->where([['is_delete', 0], ['is_active', 1]])
            ->get();

        $leaveCategoryDetails = HRS::queryGetLeaveCategoryDetails();
        $allocatedLeave = $leaveCategoryDetails->pluck('allocated_leave', 'short_form');


        ## Attendance Rules Query Start
        if ("get_attendance_Late_rules_query") {
            $getAttendanceLateRules = HRS::queryGetAttendanceLateRulesData($monthStartDate, $monthEndDate);
            $getFromLpBreakdown = $getAttendanceLateRules->pluck('lp_breakdown');
            $leave_bypass_arr = !empty($getFromLpBreakdown[0]) ? explode(",", $getFromLpBreakdown[0]) : [];
        }
        ## Attendance Rules Query End

        ## Get Leave Adjustment Query Start
        $leaveAdjustmentData = HRS::getLeaveAdjustmentData($startDate, $endDate);
        // dd($leaveAdjustmentData);
        ## Get Leave Adjustment Query End

        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'short_name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);

        // $systemCurrentDate = date("Y-m-d", strtotime(Common::systemCurrentDate()));
        $systemCurrentDate = date("Y-m-d");

        $late_bypass_arr = array();
        $attendance_bypass_arr = array();
        foreach ($employeeData as $key => $row) {
            $empId = $row->id;

            ## Leave Adjustment Data
            $leaveAdjustmentDataByEmp = $leaveAdjustmentData->get()->toArray();

            // dd($leaveAdjustmentDataByEmp);
            ## Attendance Data
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
            $empMovementData = array_merge($empMovementData, $allEmpMovementData);



            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $employeeData[$key]->emp_designation_id = $row->designation_id;
            $employeeData[$key]->emp_resign_date = $employeeData[$key]->closing_date;

            ## ai data company configuration theke asbe

            $employeeData[$key]->attendance = array();

            ## Employee Resign Date
            $empResignDate =  new DateTime($employeeData[$key]->closing_date);
            $empWiseJoinDate = $employeeData[$key]->join_date;

            $employeeData[$key]->leave_allocated_month = 0;
            $allAcclocMonth = count(array_keys($getMonthDate));

            if ($empWiseJoinDate <= $monthStartDate) {
                $employeeData[$key]->leave_allocated_month = count(array_keys($getMonthDate));
            }

            $totalLPLeave = $totalLWP = $totalLP = $totalAL = $totalLL = 0;
            $totalLeave = array();

            foreach ($getMonthDate as $month => $dateArray) {

                $leave  = $this->LeaveAllocated($leaveCategoryDetails, $allAcclocMonth);

                $countForLPMonthWise = $monthLWP = 0;
                $lpToLwp = 0;
                $monthLP = 0;
                $monthLeave = array();

                $mEndDate = last(array_keys($dateArray));

                if (($empWiseJoinDate > $mEndDate) || ($systemCurrentDate < $mEndDate)) {
                    continue;
                }

                if ($employeeData[$key]->leave_allocated_month != count(array_keys($getMonthDate))) {
                    $employeeData[$key]->leave_allocated_month++;
                }

                foreach ($dateArray as $monthInDate => $val) {

                    if($empResignDate < (new DateTime($monthInDate)) || ($empWiseJoinDate > $monthInDate) || $monthInDate > date('Y-m-d')){
                        continue;
                    }
                    if ( ($systemCurrentDate < $monthInDate) || in_array($monthInDate, $holidays)) {
                        continue;
                    }

                    ## Absent Calculation Start
                    $inTime = null;
                    ksort($empMovementData);

                    if (isset($empAttendanceData[$monthInDate])) {
                        $rowFirst = $empAttendanceData[$monthInDate][0];
                        $inTime = $rowFirst->time;

                        // if (isset($empMovementData[$monthInDate])) {
                        //     $rowFirst = $empMovementData[$monthInDate][0];
                        //     $inTime = $rowFirst->start_time;
                        // }
                    } elseif (isset($empMovementData[$monthInDate])) {
                        $rowFirst = $empMovementData[$monthInDate][0];
                        $inTime = $rowFirst->start_time;
                    }

                    if (isset($empAttendanceData[$monthInDate])) {
                        $rowFirst = $empAttendanceData[$monthInDate][0];
                        $inTime = $rowFirst->time;
                    }

                    $empLeaveData = $leaveData->whereIn('emp_id', [$empId, 0])
                        ->where('date_from', '<=', $monthInDate)
                        ->where('date_to', '>=', $monthInDate)
                        ->first();
                    if (!empty($empLeaveData)) {
                        $tmpLeaveStartDate = $empLeaveData->date_from;
                        if( ($empLeaveData->emp_id == $empId || $empLeaveData->emp_id == 0) && $tmpLeaveStartDate == $monthInDate){
                            $inTime = null;
                        }
                    }

                    if ($inTime != null) {

                        $attendanceRule1 = $attendanceRules->where('eff_date_start', '<=', $monthInDate)->where('eff_date_end', '>=', $monthInDate)->first();
                        $attendanceRule2 = $attendanceRules->where('eff_date_start', '<=', $monthInDate)->whereNull('eff_date_end')->first();

                        $attendanceRule = null;

                        if($attendanceRule1 == null && $attendanceRule2 == null){
                            continue;
                        }
                        else {
                            if($attendanceRule1 == null){
                                $attendanceRule = $attendanceRule2;
                            }
                            else {
                                $attendanceRule = $attendanceRule1;
                            }
                        }

                        if($attendanceRule != null){

                            $dutyStart = $attendanceRule->start_time;
                            $time1 = $inTime;
                            $time1 = new DateTime($time1);
                            $time2 = new DateTime($dutyStart);
                            $acceptedTime = $time1->diff($time2);

                            $hour = $acceptedTime->format('%h');
                            $minit = $acceptedTime->format('%i');
                            $sec = $acceptedTime->format('%s');

                            $totalOTMinutes = ($hour * 60);
                            $totalOTMinutes += $minit;
                            ## Calculate Late Time Diffrent End

                            if (($inTime > date('h:i:59', strtotime($attendanceRule->start_time)))
                                && ($totalOTMinutes > $attendanceRule->late_accept_minute)
                            ) {

                                if (count($empMovementData) > 0) {
                                    foreach ($empMovementData as $movDate => $movData) {
                                        if ($movData[0]->movement_date == $monthInDate && $movData[0]->emp_id == 0) {
                                            if ($inTime > $movData[0]->end_time) {
                                                $totalLP++;
                                                $monthLP++;
                                                $countForLPMonthWise++;
                                            } elseif ($inTime >= $movData[0]->start_time) {
                                                // Nothing to do.
                                            }
                                        }
                                    }
                                }
                                
                                if (!isset($empMovementData[$monthInDate])) {
                                    $totalLP++;
                                    $monthLP++;
                                    $countForLPMonthWise++;
                                }
                                // $totalLP++;
                                // $monthLP++;
                                // $countForLPMonthWise++;
                            }

                            $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
                            $late_bypass_arr = !empty($attendanceRules[0]->late_bypass) ? explode(",", $attendanceRules[0]->late_bypass) : [];

                            $leave_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];

                            // dd($attendance_bypass_arr, $late_bypass_arr);
                        }

                        // if( $countForLPMonthWise >= $attendanceRule->lp_accept ){
                        //     $lpToLwp = intval($countForLPMonthWise / intval($attendanceRule->lp_accept));
                        // }
                    }

                    if ($inTime == null) {
                        // $empLeaveData = $leaveData->whereIn('emp_id', [$empId, 0])
                        //     ->where('date_from', '<=', $monthInDate)
                        //     ->where('date_to', '>=', $monthInDate)
                        //     ->first();

                        if ($empLeaveData != null) {

                            $leavCat = $leaveCategoryData->where('id', $empLeaveData->leave_cat_id)->pluck('short_form')->first();

                            if (isset($monthLeave[$leavCat])) {
                                $monthLeave[$leavCat] += 1;
                            } else {
                                $monthLeave[$leavCat] = 1;
                            }

                            if (isset($totalLeave[$leavCat])) {
                                $totalLeave[$leavCat] += 1;
                            } else {
                                $totalLeave[$leavCat] = 1;
                            }
                        } else {
                            $monthInDate = new DateTime($monthInDate);
                            if( $monthInDate < $empResignDate || $empResignDate == null){
                                $monthLWP++;
                            }
                            // $totalLWP ++;
                        }
                    }

                }

                if ($monthLP > 1) {
                    if(count($getFromLpBreakdown) > 0){
                        $returnLL = HRS::getDataFromUpdatedAttendanceRules($getFromLpBreakdown[0], $monthLP);
                    }else{
                        if($monthLP >= 3){
                            $returnLL = ($monthLP / 3);
                        }else{
                            $returnLL = 0;
                        }
                    }
                }else{
                    $returnLL = 0;
                }
              
                $employeeData[$key]->attendance[$month]['LP'] = $monthLP;
                $employeeData[$key]->attendance[$month]['LL'] = $returnLL;
                $employeeData[$key]->attendance[$month]['LWP'] = $monthLWP;
                $employeeData[$key]->attendance[$month]['Adj'] = 0;
                $employeeData[$key]->attendance[$month]['leave'] = $monthLeave;
                $employeeData[$key]->attendance[$month]['adj_for'] = null;
                $employeeData[$key]->assigned_leave = $leave;
                $totalLL += $returnLL;
                $totalLWP += $monthLWP;

                ## set leave adjustment start
                if(count($leaveAdjustmentDataByEmp) > 0){
                    foreach ($leaveAdjustmentDataByEmp as $adjData) {
                        if ($adjData->month_name == $month && $empId == $adjData->emp_id) {
                            $employeeData[$key]->attendance[$adjData->month_name]['Adj'] = intval($adjData->adjustment_value);

                            $employeeData[$key]->attendance[$month]['adj_for'] = $adjData->adjustment_for;
                        }
                    }
                }
                ## set leave adjustment end
                
            }



            $employeeData[$key]->attendance['total_consume']['LP'] = $totalLP;
            $employeeData[$key]->attendance['total_consume']['LL'] = $totalLL;
            $employeeData[$key]->attendance['total_consume']['LWP'] = $totalLWP;
            $employeeData[$key]->attendance['total_consume']['leave'] = $totalLeave;
            $employeeData[$key]->attendance['total']['lp_leave'] = $totalLPLeave;

            // dd($employeeData[$key]);

        }

        // Error Handaling Start
        if (empty($attendance_bypass_arr)) { $attendance_bypass_arr = []; }
        if (empty($late_bypass_arr)) { $late_bypass_arr = []; }
        if (empty($leave_bypass_arr)) { $leave_bypass_arr = []; }
        // Error Handaling End

        // dd($attendance_bypass_arr, $late_bypass_arr, $leave_bypass_arr);
        // dd($employeeData);

        return view('HR.Reports.LeaveReports.EmployeeLeaveReport.report_body', compact('employeeData', 'startDate','getMonthDate', 'leaveCategoryData', 'leaveCategoryDetails', 'allocatedLeave', 'late_bypass_arr', 'attendance_bypass_arr', 'leave_bypass_arr'));
    }




    //============================
    public function Backup_reportBody(Request $request)
    {

        // if ($request->monthYear == '') return '';

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $targetYear = (empty($request->monthYear)) ? null : date('Y', strtotime($request->monthYear));
        $startDate = new DateTime("$targetYear-01-01");
        $endDate = new DateTime("$targetYear-12-31");

        // specify the start and end dates as strings in the "YYYY-MM-DD" format
        $start_date_str = $startDate->format('Y-m-d');
        $end_date_str = $endDate->format('Y-m-d');
        $start_timestamp = strtotime($start_date_str);
        $end_timestamp = strtotime($end_date_str);
        $total_range =  (int) (($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;


        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $startDate;
        $tempDateTwo = $startDate;

        $monthDates[$tempDate->format('Y-m-d')] = $tempDate->format('D');
        while ($tempDate <= $endDate) {
            $date = $tempDate->format('Y-m-d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
            $tempDate++;
        }

        $const31DayForAllMonth = array();
        for ($d = 1; $d <= 31; $d++) {
            $dayTmp = strval($d);
            if ($d < 10) {
                $dayTmp = '0' . $d;
            }
            $const31DayForAllMonth[$d] = $dayTmp;
        }

        ## Branch Array Query Start
        $selBranchArr = Common::getBranchIdsForAllSection([
            'companyId'     => $companyId,
            // 'projectId'     => $projectId,
            // 'projectTypeId' => $projectTypeId,
            'branchId'      => $branchId,
            'zoneId'      => $zoneId,
            'regionId'      => $regionId,
            'areaId'      => $areaId,
        ]);
        ## Branch Array Query End

        ## Holiday Query
        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);

        ## Employee Query Start
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            'joinDateTo' => $monthEndDate,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date'
        ]);

        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        $empJoinDate = $employeeData->pluck('join_date')->first();
        // dd($employeeData, $empJoinDate);
        ## Employee Query End

        ## Attendance Query Start
        $attendanceData = DB::table('hr_attendance')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('emp_id', $employeeIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where('time_and_date', '>=', $monthStartDate)
                        ->where('time_and_date', '<=', $monthEndDate . ' 23:59:59');
                }
            })
            // ->groupBy(['emp_id', 'date'])
            ->selectRaw('emp_id, time_and_date, DATE(time_and_date) AS date, TIME(time_and_date) AS time')
            // ->orderBy('branch_id', 'ASC')
            ->orderBy('emp_id', 'ASC')
            ->orderBy('time_and_date', 'ASC')
            ->get();

        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));

        // $attendanceDates = array_values(array_unique(array_merge($attendanceDates, $holidays)));

        ## Attendance Query End


        ## Movement Query Start
        $movementData = DB::table('hr_app_movements')
            ->where([['is_delete', 0], ['is_active', 1], ['reason', 'official']])
            ->whereIn('application_for', ['late', 'absent'])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['movement_date', '>=', $monthStartDate . ' 23:59:59'], ['movement_date', '<=', $monthEndDate . ' 23:59:59']]);
                }
            })
            ->selectRaw('emp_id, reason, application_for, movement_date, start_time, end_time')
            ->get();

        $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();

        // dd($movementArr, $movementData);
        ## Movement Query End

        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'short_name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);


        ## Leave Query Start
        $leaveData = DB::table('hr_app_leaves')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['date_from', '>=', $monthStartDate], ['date_from', '<=', $monthEndDate]]);
                    $query->orWhere([['date_to', '>=', $monthStartDate], ['date_to', '<=', $monthEndDate]]);
                }
            })
            ->selectRaw('emp_id, date_from, date_to, leave_cat_id, reason')
            ->get();

        $leaveArr = $leaveData->groupBy('emp_id')->toArray();
        $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));

        $leaveAllocatedData = DB::table('hr_leave_category_details')
            ->where('effective_date_from', '<', $monthStartDate)
            ->groupBy(['leave_cat_id'])
            ->pluck('allocated_leave', 'leave_cat_id')->toArray();
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');

        $leaveCatAndDataArr = array();
        foreach ($leaveAllocatedData as $AloKey => $aloVal) {
            foreach ($leaveCategoryData as $catKey => $catVal) {
                if ($AloKey == $catKey) {
                    $leaveItem = [
                        $catVal => $aloVal,
                        $AloKey => $catVal,
                    ];
                    array_push($leaveCatAndDataArr, $leaveItem);
                }
            }
        }
        ## Leave Query End



        ## Attendance Rules Query Start
        $attendanceRules = DB::table('hr_attendance_rules')
            ->where([['is_delete', 0], ['is_active', 1]])
            // ->whereIn('branch_id', $selBranchArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['eff_date_start', '<=', $monthEndDate]]);
                    $query->where(function ($query2) use ($monthStartDate) {
                        $query2->whereNull('eff_date_end');
                        $query2->orWhere([['eff_date_end', '>=', $monthStartDate]]);
                    });
                }
            })
            ->selectRaw('start_time, end_time, ext_start_time, late_accept_minute, eff_date_start, eff_date_end')
            ->get();
        ## Attendance Rules Query End

        foreach ($employeeData as $key => $row) {
            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $employeeData[$key]->yearlyAttendance = array();

            ## Month & Date Show Start
            for ($m = 01; $m <= 12; $m++) {
                $employeeData[$key]->yearlyAttendance[$startDate->format('F')] = array();
                $runingMonthMain = $startDate->format('F');

                // $totalAL = $totalSL = $totalCL = $totalLP = $totalLWP = $totaTL = 0;

                $totalLP = $totalLWP = 0;

                $tmpMonth = $m;
                if ($m < 10) {
                    $tmpMonth = '0' . $m;
                }

                $const31Days = array();
                for ($i = 01; $i <= 31; $i++) {
                    $tmpDay = $i;
                    if ($i < 10) {
                        $tmpDay = '0' . $i;
                    }

                    $keyDate = $targetYear . '-' . $tmpMonth . '-' . $tmpDay;
                    $const31Days[$keyDate] = '';
                    $const31Days['totalLWP'] = 0;
                    $const31Days['totalAL'] = 0;
                    $const31Days['totalSL'] = 0;
                    $const31Days['totalCL'] = 0;
                    $const31Days['totalLP'] = $totalLP;
                    $const31Days['totalLWP'] = $totalLWP;

                    ## Attendance Array
                    $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

                    ## Attendance Calculation Start

                    ## Absent Calculation Start
                    $systemCurrentDate = date('Y-m-d', strtotime(Common::systemCurrentDate()));
                    $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();

                    if (!in_array($keyDate, $holidays) && !in_array($keyDate, $empAttendanceData) && !in_array($keyDate, $empMovementData)) {
                        $const31Days[$keyDate] = "A";
                        // $totalLWP++;
                    }

                    if (date("Y-m-d") < $keyDate || $empJoinDate > $keyDate) {
                        $const31Days[$keyDate] = " ";
                    }
                    ## Absent Calculation End

                    if (count($empAttendanceData) > 0) {

                        foreach ($empAttendanceData as $date => $rowData) {
                            $rowFirst = $rowData[0];

                            ## Calculate Late Time Diffrent Start
                            $inTime = $rowFirst->time;
                            $dutyStart = $attendanceRules[0]->start_time;

                            $time1 = new DateTime($inTime);
                            $time2 = new DateTime($dutyStart);
                            $acceptedTime = $time1->diff($time2);

                            $hour = $acceptedTime->format('%h');
                            $minit = $acceptedTime->format('%i');
                            $sec = $acceptedTime->format('%s');

                            $totalOTMinutes = ($hour * 60);
                            $totalOTMinutes += $minit;

                            if ($keyDate == $date) {

                                if (count($attendanceRules) == 1) {

                                    if (
                                        ($totalOTMinutes > $attendanceRules[0]->late_accept_minute) &&
                                        $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time))

                                    ) {
                                        $const31Days[$date] = "LP";
                                        $totalLP++;
                                    } else {

                                        $const31Days[$date] = "P";
                                    }
                                } elseif (count($attendanceRules) > 1) {

                                    foreach ($attendanceRules as $attRule) {

                                        if (($attRule->eff_date_start <= $date) && (empty($attRule->eff_date_end) || (!empty($attRule->eff_date_end) && $attRule->eff_date_end >= $date))) {

                                            if (
                                                ($totalOTMinutes > $attendanceRules[0]->late_accept_minute) &&
                                                $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time))
                                            ) {
                                                $const31Days[$date] = "LP";
                                                $totalLP++;
                                            } else {

                                                $const31Days[$date] = "P";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ## Attendance Calculation End


                    ## Leave Calculation Start
                    $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();

                    if (count($empLeaveData) > 0) {

                        foreach ($empLeaveData as $rowLeave) {

                            $startLeaveDate = $rowLeave->date_from;
                            $endLeaveDate = $rowLeave->date_to;
                            $leaveCatId = $rowLeave->leave_cat_id;

                            $tempDate = $startLeaveDate;

                            $leaveMonth = (new DateTime($rowLeave->date_from))->format('F');

                            if ($leaveMonth == $runingMonthMain) {

                                while (($tempDate <= $endLeaveDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                                    if (isset($employeeData[$key]->yearlyAttendance[$leaveMonth][$tempDate])) {

                                        $const31Days[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";
                                    } else {
                                        $const31Days[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";


                                        if ($const31Days[$tempDate] == "AL") {
                                            // $totalAL++;
                                            $const31Days['totalAL']++;
                                        }
                                        if ($const31Days[$tempDate] == "CL") {
                                            // $totalCL++;
                                            $const31Days['totalCL']++;
                                        }
                                        if ($const31Days[$tempDate] == "SL") {
                                            // $totalSL++;
                                            $const31Days['totalSL']++;
                                        }
                                    }

                                    $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                }
                            }
                        }
                    }
                    ## Leave Calculation end

                    ## Holiday Start
                    if (in_array($keyDate, $holidays) == true) {
                        $const31Days[$keyDate] = "H";
                    }
                    ## Holiday End

                    ## Movement Calculation Start
                    $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
                    if (count($empMovementData) > 0) {
                        foreach ($empMovementData as $movDate => $value) {

                            if ($keyDate == $movDate && date("F", strtotime($keyDate)) == date("F", strtotime($movDate))) {

                                if ($const31Days[$movDate] == "LP" && $keyDate == $movDate) {
                                    $totalLP--;
                                }
                                $const31Days[$movDate] = "MP";
                            }
                        }
                    }
                    ## Movement Calculation End


                }

                $keyMonthForSort = $startDate->format('F');
                $employeeData[$key]->yearlyAttendance[$startDate->format('F')] = $const31Days;
                $startDate->modify('+1 month');
                ksort($employeeData[$key]->yearlyAttendance[$keyMonthForSort]);
            }
            ## Month & Date Show End



        }

        // dd($employeeData,$employeeData[2], $employeeData[3], $employeeData[4], $employeeData[5], $employeeData[6], $employeeData[7], $employeeData[8], $employeeData[9], $employeeData[10]);


        return view('HR.Reports.LeaveReports.EmployeeLeaveReport.report_body', compact('leaveCatAndDataArr', 'employeeData', 'targetYear'));
    }
}
