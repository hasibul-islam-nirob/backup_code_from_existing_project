<?php

namespace App\Http\Controllers\HR\Reports\AttendanceReports;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
// use Illuminate\Support\Arr;
// use PhpParser\Node\Expr\Print_;

class AttendanceReportsController extends Controller
{

    public function getStatus()
    {
        return view('HR.Reports.AttendanceReports.StatusReport.index');
    }

    public function loadstatus(Request $request)
    {
        // dd($request->all());
        // if ($request->monthYear == '') return '';


        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        // $monthYear = (empty($request->monthYear)) ? null : $request->monthYear;

        $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        $endDate = (empty($request->endDate)) ? null : (new DateTime($request->endDate));



       // specify the start and end dates as strings in the "YYYY-MM-DD" format
        $start_date_str = $startDate->format('Y-m-d');
        $end_date_str = $endDate->format('Y-m-d');
        $start_timestamp = strtotime($start_date_str);
        $end_timestamp = strtotime($end_date_str);
        $total_range =  (int) (($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;

        if ($total_range > 31 ){
            return response()->json([
                'message' => "Date Range Over Than 31 Days",
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
        // specify the start and end dates as strings in the "YYYY-MM-DD" format



        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        $withHoliday = (empty($request->withHoliday)) ? 'yes' : $request->withHoliday;

        // $monthYear = new DateTime($monthYear);
        // $monthYear = new DateTime($startDate);
        // $DateDayCounting = new DateTime($startDate);
        // $monthYearEnd = new DateTime($endDate);

        // $monthStartDate = $monthEndDate = clone $monthYear;
        // $monthStartDateTime = clone $monthStartDate;
        // $monthEndDateTime = clone $monthEndDate;

        // $monthStartDate = $startDate;
        // $monthEndDate = $endDate;
        // $monthStartDateTime =  $monthStartDate;
        // $monthEndDateTime =  $monthEndDate;

        // $monthStartDate = ($monthStartDate->modify('first day of this month'))->format('Y-m-d');
        // $monthEndDate = $monthEndDate->modify('last day of this month');
        // $lastDate = $monthEndDate->format('d');
        // $monthEndDate = ($monthEndDate->modify('last day of this month'))->format('Y-m-d');

        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $startDate;

        $monthDates[$tempDate->format('Y-m-d')] = $tempDate->format('D');
        while( $tempDate <= $endDate){
            $date = $tempDate->format('Y-m-d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
            $tempDate++;
        }
        ## Date And Day Calculation End


        /*
        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $monthYear;

        $monthDates[$tempDate->format('d')] = $tempDate->format('D');
        for ($i = 0; $i < $lastDate; $i++) {
            // $tempDate = (($tempDate))->modify('+1 day');
            $date = $tempDate->format('d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
        }
        ## Date And Day Calculation End
        */

        /*
        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $monthYear;

        $monthDates[$tempDate->format('d')] = $tempDate->format('D');
        for ($i = 0; $i < $lastDate; $i++) {
            // $tempDate = (($tempDate))->modify('+1 day');
            $date = $tempDate->format('d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
        }
        ## Date And Day Calculation End
        */

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

        ## Employee Query Start
        $statusArray = array_column($this->GlobalRole, 'set_status');
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            'joinDateTo' => $monthEndDate,
            'statusArray' => $statusArray,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id'
        ]);

        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
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
            ->groupBy(['emp_id', 'date'])
            ->selectRaw('emp_id, time_and_date, DATE(time_and_date) AS date, TIME(time_and_date) AS time')
            // ->orderBy('branch_id', 'ASC')
            ->orderBy('emp_id', 'ASC')
            ->orderBy('time_and_date', 'ASC')
            ->get();

        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        ## Attendance Query End

        ## Movement Query Start
        $movementData = DB::table('hr_app_movements')
            ->where([['is_delete', 0], ['is_active', 1], ['reason', 'official']])
            ->whereIn('application_for', ['late', 'absent'])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['movement_date', '>=', $monthStartDate], ['movement_date', '<=', $monthEndDate]]);
                }
            })
            ->selectRaw('emp_id, reason, application_for, movement_date, start_time, end_time')
            ->get();

        $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        ## Movement Query End

        
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
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');
        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'short_name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);

        ## Holiday Query
        // $holidays = array();
        // if($withHoliday == 'yes'){
        //     $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
        // }
        ## Holiday Query
        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);

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
            ->selectRaw('start_time, end_time, ext_start_time, eff_date_start, eff_date_end')
            ->get();
        ## Attendance Rules Query End

        foreach ($employeeData as $key => $row) {

            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";

            $employeeData[$key]->totalAL = 0;
            $employeeData[$key]->totalCL = 0;
            $employeeData[$key]->totalSL = 0;

            ## Attendance Calculation Start
            $employeeData[$key]->attendance = [];
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

            if (count($empAttendanceData) > 0) {
                foreach ($empAttendanceData as $date => $rowData) {

                    $rowFirst = $rowData[0];

                    $employeeData[$key]->attendance[$date] = "-";

                    if (count($attendanceRules) == 1) {

                        //date('h:ia', strtotime($rowData[0]->time));
                        if ($rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->ext_start_time)) ) {
                            // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: #ff9900;'>LP</span>";
                            $employeeData[$key]->attendance[$date] = "LP";
                        } else {
                            // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: green;'>P</span>";
                            $employeeData[$key]->attendance[$date] = "P";
                        }
                    } elseif (count($attendanceRules) > 1) {
                        foreach ($attendanceRules as $attRule) {

                            if (($attRule->eff_date_start <= $date) && (empty($attRule->eff_date_end) || (!empty($attRule->eff_date_end) && $attRule->eff_date_end >= $date))) {

                                if ( date('h:i:59', strtotime($attRule->ext_start_time)) < $rowFirst->time) {
                                    // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: #ff9900;'>LP</span>";
                                    $employeeData[$key]->attendance[$date] = "LP";
                                } else {
                                    // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: green;'>P</span>";
                                    $employeeData[$key]->attendance[$date] = "P";
                                }
                            }
                        }
                    }
                }
            } else {
                if ($withAbsent == 'no') {
                    continue;
                }
            }
            ## Attendance Calculation End

            ## Movement Calculation Start
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();

            if (count($empMovementData) > 0) {
                $employeeData[$key]->attendance = array_merge($employeeData[$key]->attendance, array_flip(array_keys($empMovementData)));
            }
            ## Movement Calculation End

            ## Leave Calculation Start
            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();

            if (count($empLeaveData) > 0) {

                foreach ($empLeaveData as $rowLeave) {

                    $startDate = $rowLeave->date_from;
                    $endDate = $rowLeave->date_to;
                    $leaveCatId = $rowLeave->leave_cat_id;

                    $tempDate = $startDate;

                    while (($tempDate <= $endDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                        if (isset($employeeData[$key]->attendance[$tempDate])) {
                            // $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? "<span style='font-weight:500; color:blue;'>" . $leaveCategoryData[$leaveCatId] . "/P</span>": "NaN";

                            $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] . "/P" : "NaN";
                        } else {
                            $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            if ($employeeData[$key]->attendance[$tempDate] == "AL") {
                                $employeeData[$key]->totalAL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate] == "CL") {
                                $employeeData[$key]->totalCL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate] == "SL") {
                                $employeeData[$key]->totalSL++;
                            }
                        }

                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
            }
            ## Leave Calculation end
        }

        return view('HR.Reports.AttendanceReports.StatusReport.report_body', compact('employeeData', 'withHoliday', 'withAbsent', 'monthDates', 'holidays'));
    }



    //==========================================


    public function getInOut()
    {
        return view('HR.Reports.AttendanceReports.InOutReport.index');
    }

    public function loadInOut(Request $request)
    {
        if ($request->startDate == '') return '';
        if ($request->endDate == '') return '';

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        // dd($branchId);
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;
        $monthYear = (empty($request->monthYear)) ? null : $request->monthYear;

        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        $withHoliday = (empty($request->withHoliday)) ? 'no' : $request->withHoliday;

        $isTime = (empty($request->withTime)) ? 'no' : $request->withTime;

        // $monthYear = new DateTime($monthYear);
        $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        $endDate = (empty($request->endDate)) ? null : (new DateTime($request->endDate));

        // specify the start and end dates as strings in the "YYYY-MM-DD" format
        $start_date_str = $startDate->format('Y-m-d');
        $end_date_str = $endDate->format('Y-m-d');
        $start_timestamp = strtotime($start_date_str);
        $end_timestamp = strtotime($end_date_str);
        $total_range =  (int) (($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;

        if ($total_range > 31 ){
            return response()->json([
                'message' => "Date Range Over Than 31 Days",
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
        // specify the start and end dates as strings in the "YYYY-MM-DD" format


        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        ## Date And Day Calculation Start
        $monthDates = HRS::getDateAndDays($startDate, $endDate);
        ## Date And Day Calculation End

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
        // if ($withHoliday == 'yes') {
            $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
        // }

        ## Employee Query Start
        $statusArray = array_column($this->GlobalRole, 'set_status');
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            'joinDateTo' => $monthEndDate,
            'statusArray' => $statusArray,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date, closing_date'
        ]);
       
        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        ## Employee Query End

        ## Attendance Query Start
        if ("get_attendance_query") {
            $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
            $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
            $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
        }
        ## Attendance Query End

        // dd($attendanceData);
        ## Movement Query Start
        if ("get_movement_query") {
            $reasonArr = HRS::getValidReasonIds(14);
            $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
           
            // dd($movementData,   $movementArr, $reasonArr);  
        }

        ## Movement Query End

        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'short_name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);


        ## Leave Query Start
        if ("get_leave_query") {
            $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            $leaveArr = $leaveData->groupBy('emp_id')->toArray();
            $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
        }
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');



        ## Attendance Rules Query Start
        if ("get_attendance_rules_query") {
            $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
            $action_for_lp = $attendanceRules->pluck('action_for_lp');

            $lpAccept = 0;
            $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
        }
        ## Attendance Rules Query End

        ## Attendance Late Rules Query Start
        if ("get_attendance_late_rules_query") {
            $attendanceLateRules = HRS::queryGetAttendanceLateRulesData($monthStartDate, $monthEndDate);

            $lateBypassAcceptDesigID = !empty($attendanceLateRules[0]->late_bypass) ? (explode(",",$attendanceLateRules[0]->late_bypass)) : [];

            $isLateDesigID  = HRS::fnForDesignationData($lateBypassAcceptDesigID, 'name');

            // ss($attendanceLateRules,  $lateBypassAcceptDesigID, $isLateDesigID);
        }
        ## Attendance Late Rules Query End

        // $employeeData[$key]->attendance = [];
        foreach ($employeeData as $key => $row) {

            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $employeeData[$key]->emp_designation_id = $row->designation_id;

            $employeeData[$key]->emp_join_date = $employeeData[$key]->join_date;
            $employeeData[$key]->emp_resign_date = $employeeData[$key]->closing_date;

            $empResignDate =  new DateTime($employeeData[$key]->closing_date);
            $empJoinDate = new DateTime($employeeData[$key]->join_date);


            $employeeData[$key]->totalAL = 0;
            $employeeData[$key]->totalCL = 0;
            $employeeData[$key]->totalSL = 0;

            ## Attendance Calculation Start
            $employeeData[$key]->attendance = [];
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();
            // dd($employeeWiseAttendanceData[$empId]);
            if (count($empAttendanceData) > 0) {
                foreach ($empAttendanceData as $date => $rowData) {

                    $firstDate = new DateTime($date);
                    $lastDate = new DateTime(last(array_keys($empAttendanceData)));
                    // dd($firstDate, $lastDate, $empAttendanceData);
                    if ($empResignDate <= $firstDate || $empJoinDate > $lastDate) {
                        continue;
                    }

                    $rowFirst = $rowData[0];

                    $employeeData[$key]->attendance[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
                    $employeeData[$key]->attendance[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));


                    ## Calculate Late Time Diffrent Start
                    $inTime = $rowFirst->time;
                    $dutyStart = $attendanceRules[0]->start_time;
                    $dutyEnd = $attendanceRules[0]->end_time;
                    // dd($inTime);
                    $time1 = new DateTime($inTime);
                    $time2 = new DateTime($dutyStart);
                    $acceptedTime = $time1->diff($time2);

                    $hour = $acceptedTime->format('%h');
                    $minit = $acceptedTime->format('%i');
                    $sec = $acceptedTime->format('%s');
                    ## Calculate Late Time Diffrent End

                    if (count($attendanceRules) == 1) {
                        if (
                            ($minit > $attendanceRules[0]->late_accept_minute  || $hour > 0) &&
                            $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time))

                        ) {

                            $employeeData[$key]->attendance[$date]['status'] = "LP";
                        } else {

                            $employeeData[$key]->attendance[$date]['status'] = "P";
                        }
                        // $lpAccept = $attendanceRules[0]->lp_accept;

                        $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
                        $late_bypass_arr = !empty($attendanceRules[0]->late_bypass) ? explode(",", $attendanceRules[0]->late_bypass) : [];


                    } elseif (count($attendanceRules) > 1) {
                        foreach ($attendanceRules as $attRule) {

                            if (($attRule->eff_date_start <= $date) && (empty($attRule->eff_date_end) || (!empty($attRule->eff_date_end) && $attRule->eff_date_end >= $date))) {
                                if (
                                    ($minit > $attendanceRules[0]->late_accept_minute  || $hour > 0) &&
                                    $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time))
                                 ) {
                                    $employeeData[$key]->attendance[$date]['status'] = "LP";
                                } else {

                                    $employeeData[$key]->attendance[$date]['status'] = "P";
                                }
                            }

                            $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
                            $late_bypass_arr = !empty($attendanceRules[0]->late_bypass) ? explode(",", $attendanceRules[0]->late_bypass) : [];
                            // ss($attendanceRules[0], $late_bypass_arr);
                            // $lpAccept = $attRule->lp_accept;
                        }
                    }


                    ##**************************
                    ## Movement Calculation Start

                    // $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
                    $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
                    $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
                    $empMovementData = array_merge($empMovementData, $allEmpMovementData);
                  
                    /*
                    if( count($empMovementData) > 0 ){
                        foreach($empMovementData as $movDate => $movData){

                            if($movData[0]->movement_date == $date){

                                if($movData[0]->emp_id == 0 ){

                                    if(isset($empAttendanceData[$movDate])){

                                        if ( ($inTime >= $movData[0]->start_time && $inTime <= $movData[0]->end_time) ) {
                                            $employeeData[$key]->attendance[$movDate]['status'] = "MP";
                                        }else{

                                            if($inTime <= $movData[0]->end_time){
                                                $employeeData[$key]->attendance[$movDate]['status'] = "MP";
                                            }else{
                                                $employeeData[$key]->attendance[$movDate]['status'] = "LP";
                                            }
                                            
                                        }
                                    }else{
                                        $employeeData[$key]->attendance[$movDate]['status'] = "A";
                                    }
                                    
                                }else{
                                    $employeeData[$key]->attendance[$movDate]['status'] = "MP";
                                }
                            }

                            if ($movData[0]->movement_date == $date) {
                                if ($movData[0]->application_for == 'absent') {
                                    $employeeData[$key]->attendance[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
                                    $employeeData[$key]->attendance[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));
                                }
                            }
                            
                        }
                    }
                    */
                    




                /*
                if (count($empMovementData) > 0) {
                    foreach ($empMovementData as $movData) {
                        if ($movData[0]->movement_date == $date) {
                            if ($movData[0]->emp_id == 0) {
                                $attendanceStatus = "MP";
                
                                if (isset($empAttendanceData[$date])) {
                                    if ($inTime > $movData[0]->end_time) {
                                        $attendanceStatus = "LP";
                                    }
                                } else {
                                    $attendanceStatus = "A";
                                }
                
                                $employeeData[$key]->attendance[$date]['status'] = $attendanceStatus;
                            } else {
                                $employeeData[$key]->attendance[$date]['status'] = "MP";
                            }
                
                            if ($movData[0]->application_for == 'absent') {
                                $employeeData[$key]->attendance[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
                                $employeeData[$key]->attendance[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));
                            }
                        }
                    }
                }
                */
               
                if (count($empMovementData) > 0) {
                    foreach ($empMovementData as $movDate => $movData) {

                        if (($movData[0]->application_for === 'absent' || $movData[0]->application_for === 'late') && !isset($empAttendanceData[$movDate]) ) {
                            $attendance = &$employeeData[$key]->attendance[$movDate];
                            $attendance['status'] = "MP";
                        }
                    
                        if ($movData[0]->movement_date !== $date) {
                            continue;
                        }
                
                        $attendanceStatus = ($movData[0]->emp_id === 0) ? "MP" : "MP";
                
                        if (isset($empAttendanceData[$date]) && $inTime > $movData[0]->end_time) {
                            $attendanceStatus = "LP";
                        } elseif (!isset($empAttendanceData[$date])) {
                            $attendanceStatus = "A";
                        } 

                        $attendance = &$employeeData[$key]->attendance[$date];
                        $attendance['status'] = $attendanceStatus;
                
                        if ($movData[0]->application_for === 'absent') {
                            $attendance['in'] = date('h:ia', strtotime($rowData[0]->time));
                            $attendance['out'] = date('h:ia', strtotime(end($rowData)->time));
                        }
                    }
                }

                  
                    /*
                    if( count($empMovementData) > 0 ){
                        // dd($empMovementData, $allEmpMovementData, $empAttendanceData);
                        foreach($empMovementData as $movDate => $movData){
                            // dd($empMovementData, $movData, $empAttendanceData);
                            if($movDate == "2023-11-18"){
                                dd(1, $movData[0]->emp_id, 2, $movData[0]->end_time, 3, $inTime, 4, (new DateTime($movData[0]->end_time) < $inTime));
                            }
                            if($movData[0]->emp_id = 0){
                                if (isset($empAttendanceData[$movDate])) {
                                    if(new DateTime($movData[0]->end_time) < $inTime){
                                        $employeeData[$key]->attendance[$movDate]['status'] = "LP";
                                    }
                                    else{
                                        $employeeData[$key]->attendance[$movDate]['status'] = "MP";
                                    }
                                }
                                else{
                                    $employeeData[$key]->attendance[$movDate]['status'] = "A";
                                }
                            }
                            else{
                                if (isset($empAttendanceData[$movDate])) {
                                    if(new DateTime($movData[0]->end_time) < $inTime){
                                        $employeeData[$key]->attendance[$movDate]['status'] = "LP";
                                    }
                                    else{
                                        $employeeData[$key]->attendance[$movDate]['status'] = "MP";
                                    }
                                }
                                else{
                                    $employeeData[$key]->attendance[$movDate]['status'] = "A";
                                }
                            }


                            if ($movData[0]->movement_date == $date) {
                                // dd( $empMovementData, $movData[0]->end_time);
                                if ($movData[0]->application_for == 'absent') {
                                    $employeeData[$key]->attendance[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
                                    $employeeData[$key]->attendance[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));
                                }
                            }
                            
                        }
                    }*/
               
               
                    ## Movement Calculation End
                    ##**************************

                }

            } else {
                if ($withAbsent == 'no') {
                    continue;
                }
            }
            ## Attendance Calculation End


            ## Leave Calculation Start

            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
            $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
            $empLeaveData = array_merge($empLeaveData, $allLeaveData);
            if (count($empLeaveData) > 0) {

                foreach ($empLeaveData as $rowLeave) {

                    $startDate = $rowLeave->date_from;
                    $endDate = $rowLeave->date_to;
                    $leaveCatId = $rowLeave->leave_cat_id;

                    $tempDate = $startDate;

                    while (($tempDate <= $endDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                        if (isset($employeeData[$key]->attendance[$tempDate])) {

                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] . "/P" : "NaN";

                            $employeeData[$key]->attendance[$tempDate]['in'] = date('h:ia', strtotime($rowData[0]->time));
                            $employeeData[$key]->attendance[$tempDate]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));


                        } else {
                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            if ($employeeData[$key]->attendance[$tempDate]['status'] == "AL") {
                                $employeeData[$key]->totalAL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate]['status'] == "CL") {
                                $employeeData[$key]->totalCL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate]['status'] == "SL") {
                                $employeeData[$key]->totalSL++;
                            }
                        }

                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
            }

            ## Leave Calculation end

        }

        // Error Handaling Start
        if (empty($empMovementData)) { $empMovementData = []; }
        if (empty($attendance_bypass_arr)) { $attendance_bypass_arr = []; }
        if (empty($late_bypass_arr)) { $late_bypass_arr = []; }
        if (empty($dutyStart)) { $dutyStart = []; }
        if (empty($dutyEnd)) { $dutyEnd = []; }
        // Error Handaling End
        // dd($employeeData);
        return view('HR.Reports.AttendanceReports.InOutReport.report_body', compact('employeeData', 'attendanceDates', 'monthDates', 'withAbsent', 'monthYear', 'holidays', 'withHoliday', 'empMovementData', 'movementArr', 'attendanceRules','isTime', 'lpAccept','attendance_bypass_arr', 'late_bypass_arr','dutyStart','dutyEnd'));
    }





    //=================================================

    public function Backup2_loadInOut_way2(Request $request)
    {
        if ($request->monthYear == '') return '';


        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;

        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $monthYear = (empty($request->monthYear)) ? null : $request->monthYear;

        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        $withHoliday = (empty($request->withHoliday)) ? 'yes' : $request->withHoliday;

        $monthYear = new DateTime($monthYear);

        $monthStartDate = $monthEndDate = clone $monthYear;
        $monthStartDateTime = clone $monthStartDate;
        $monthEndDateTime = clone $monthEndDate;

        $monthStartDate = ($monthStartDate->modify('first day of this month'))->format('Y-m-d');
        $monthEndDate = $monthEndDate->modify('last day of this month');
        $lastDate = $monthEndDate->format('d');
        $monthEndDate = ($monthEndDate->modify('last day of this month'))->format('Y-m-d');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $monthYear;

        $monthDates[$tempDate->format('d')] = $tempDate->format('D');
        for ($i = 0; $i < $lastDate; $i++) {
            // $tempDate = (($tempDate))->modify('+1 day');
            $date = $tempDate->format('d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
        }
        ## Date And Day Calculation End

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

        ## Employee Query Start
        $employeeData = DB::table('hr_employees')
            ->where([['is_delete', 0]])
            ->whereIn('branch_id', $selBranchArr)
            ->where(function ($query) use ($monthEndDate, $departmentId, $designationId, $employeeId) {
                if (!empty($monthEndDate)) {
                    $query->where('join_date', '<=', $monthEndDate);
                }
                if (!empty($designationId)) {
                    $query->where('designation_id', $designationId);
                }
                if (!empty($departmentId)) {
                    $query->where('department_id', $departmentId);
                }
                if (!empty($employeeId)) {
                    $query->where('id', $employeeId);
                }

            })
            ->selectRaw('id, emp_name, emp_code, branch_id, designation_id, department_id')
            ->orderBy('branch_id', 'ASC')
            ->orderBy('emp_code', 'ASC')
            ->get();

        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        ## Employee Query End

        ## Attendance Query Start
        $attendanceData = DB::table('hr_attendance')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)
            ->whereIn('designation_id', $designationIdArr)
            ->whereIn('department_id', $departmentIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where('time_and_date', '>=', $monthStartDate)
                        ->where('time_and_date', '<=', $monthEndDate . ' 23:59:59');
                }
            })
            ->groupBy(['emp_id', 'date'])
            ->selectRaw('emp_id, time_and_date, DATE(time_and_date) AS date, TIME(time_and_date) AS time')
            ->orderBy('branch_id', 'ASC')
            ->orderBy('emp_id', 'ASC')
            ->orderBy('time_and_date', 'ASC')
            ->get();

        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        ## Attendance Query End

        ## Movement Query Start
        $movementData = DB::table('hr_app_movements')
            ->where([['is_delete', 0], ['is_active', 1], ['reason', 'official']])
            ->whereIn('application_for', ['late', 'absent'])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['movement_date', '>=', $monthStartDate], ['movement_date', '<=', $monthEndDate]]);
                }
            })
            ->selectRaw('emp_id, reason, application_for, movement_date, start_time, end_time')
            ->get();

        $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        ## Movement Query End

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
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');
        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'short_name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);

        ## Holiday Query
        // $holidays = array();
        // if($withHoliday == 'yes'){
        //     $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
        // }

        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);

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
            ->selectRaw('start_time, end_time, ext_start_time, eff_date_start, eff_date_end')
            ->get();
        ## Attendance Rules Query End

        foreach ($employeeData as $key => $row) {

            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";

            $employeeData[$key]->totalAL = 0;
            $employeeData[$key]->totalCL = 0;
            $employeeData[$key]->totalSL = 0;

            ## Attendance Calculation Start
            $employeeData[$key]->attendance = [];
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

            if (count($empAttendanceData) > 0) {
                foreach ($empAttendanceData as $date => $rowData) {

                    $rowFirst = $rowData[0];

                    $employeeData[$key]->attendance[$date] = date('h:ia', strtotime($rowData[0]->time));
                    $employeeData[$key]->attendance[$date] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));
                }
            } else {
                if ($withAbsent == 'no') {
                    continue;
                }
            }
            ## Attendance Calculation End

            ## Movement Calculation Start
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            if (count($empMovementData) > 0) {
                $employeeData[$key]->attendance = array_merge($employeeData[$key]->attendance, array_flip(array_keys($empMovementData)));
            }
            ## Movement Calculation End

            ## Leave Calculation Start
            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();

            if (count($empLeaveData) > 0) {

                foreach ($empLeaveData as $rowLeave) {

                    $startDate = $rowLeave->date_from;
                    $endDate = $rowLeave->date_to;
                    $leaveCatId = $rowLeave->leave_cat_id;

                    $tempDate = $startDate;

                    while (($tempDate <= $endDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                        if (isset($employeeData[$key]->attendance[$tempDate])) {
                            // $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? "<span style='font-weight:500; color:blue;'>" . $leaveCategoryData[$leaveCatId] . "/P</span>": "NaN";

                            $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] . "/P" : "NaN";
                        } else {
                            $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            if ($leaveCategoryData[$leaveCatId] == "AL") {
                                $employeeData[$key]->totalAL++;
                            } elseif ($leaveCategoryData[$leaveCatId] == "CL") {
                                $employeeData[$key]->totalCL++;
                            } elseif ($leaveCategoryData[$leaveCatId] == "SL") {
                                $employeeData[$key]->totalSL++;
                            }
                        }

                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
            }
            ## Leave Calculation end
        }

        return view('HR.Reports.AttendanceReports.InOutReport.report_body', compact('employeeData', 'withHoliday', 'withAbsent', 'monthDates', 'holidays', 'monthYear'));
    }




    public function loadInOut_Backup(Request $request)
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
        $monthYear = (empty($request->monthYear)) ? null : $request->monthYear;

        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        $withHoliday = (empty($request->withHoliday)) ? 'no' : $request->withHoliday;

        $isTime = (empty($request->withTime)) ? 'no' : $request->withTime;

        // $monthYear = new DateTime($monthYear);
        $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        $endDate = (empty($request->endDate)) ? null : (new DateTime($request->endDate));

        // specify the start and end dates as strings in the "YYYY-MM-DD" format
        $start_date_str = $startDate->format('Y-m-d');
        $end_date_str = $endDate->format('Y-m-d');
        $start_timestamp = strtotime($start_date_str);
        $end_timestamp = strtotime($end_date_str);
        $total_range =  (int) (($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;

        // if ($total_range > 31 ){
        //     return response()->json([
        //         'message' => "Date Range Over Than 31 Days",
        //         'status' => 'error',
        //         'statusCode' => 400,
        //         'result_data' => ''
        //     ], 400);
        // }
        // specify the start and end dates as strings in the "YYYY-MM-DD" format

        /*
        $monthStartDate = $monthEndDate = clone $monthYear;
        $monthStartDateTime = clone $monthStartDate;
        $monthEndDateTime = clone $monthEndDate;

        $monthStartDate = ($monthStartDate->modify('first day of this month'))->format('Y-m-d');
        $monthEndDate = $monthEndDate->modify('last day of this month');
        $lastDate = $monthEndDate->format('d');
        $monthEndDate = ($monthEndDate->modify('last day of this month'))->format('Y-m-d');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $monthYear;

        $monthDates[$tempDate->format('d')] = $tempDate->format('D');
        for ($i = 0; $i < $lastDate; $i++) {
            // $tempDate = (($tempDate))->modify('+1 day');
            $date = $tempDate->format('d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
        }
        ## Date And Day Calculation End
        */

        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $startDate;

        $monthDates[$tempDate->format('Y-m-d')] = $tempDate->format('D');
        while( $tempDate <= $endDate){
            $date = $tempDate->format('Y-m-d');
            $day = $tempDate->format('D');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
            $tempDate++;
        }

        ## Date And Day Calculation End

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
        // $holidays = array();
        // if ($withHoliday == 'yes') {
            $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
        // }

        ## Employee Query Start
        // $employeeData = DB::table('hr_employees')
        //     ->where([['is_delete', 0]])
        //     ->whereIn('branch_id', $selBranchArr)
        //     ->where(function ($query) use ($monthEndDate, $departmentId, $designationId, $employeeId) {
        //         if (!empty($monthEndDate)) {
        //             $query->where('join_date', '<=', $monthEndDate);
        //         }
        //         if (!empty($designationId)) {
        //             $query->where('designation_id', $designationId);
        //         }
        //         if (!empty($departmentId)) {
        //             $query->where('department_id', $departmentId);
        //         }
        //         if (!empty($employeeId)) {
        //             $query->where('id', $employeeId);
        //         }
        //     })
        //     ->selectRaw('id, emp_name, emp_code, branch_id, designation_id, department_id')
        //     ->orderBy('branch_id', 'ASC')
        //     ->orderBy('emp_code', 'ASC')
        //     ->get();

        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            'joinDateTo' => $monthEndDate,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id'
        ]);

        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
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
                    $query->where([['movement_date', '>=', $monthStartDate. ' 23:59:59'], ['movement_date', '<=', $monthEndDate. ' 23:59:59']]);
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
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');



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
            ->selectRaw('start_time, end_time, ext_start_time, eff_date_start, eff_date_end')
            ->get();
        ## Attendance Rules Query End

        // $employeeData[$key]->attendance = [];
        foreach ($employeeData as $key => $row) {

            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";

            $employeeData[$key]->totalAL = 0;
            $employeeData[$key]->totalCL = 0;
            $employeeData[$key]->totalSL = 0;

            ## Attendance Calculation Start
            $employeeData[$key]->attendance = [];
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

            if (count($empAttendanceData) > 0) {
                foreach ($empAttendanceData as $date => $rowData) {

                    $rowFirst = $rowData[0];

                    $employeeData[$key]->attendance[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
                    $employeeData[$key]->attendance[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));

                    if (count($attendanceRules) == 1) {

                        if ($rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->ext_start_time)) ) {
                            // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: #ff9900;'>LP</span>";
                            $employeeData[$key]->attendance[$date]['status'] = "LP";
                        } else {
                            // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: green;'>P</span>";
                            $employeeData[$key]->attendance[$date]['status'] = "P";
                        }
                    } elseif (count($attendanceRules) > 1) {
                        foreach ($attendanceRules as $attRule) {

                            if (($attRule->eff_date_start <= $date) && (empty($attRule->eff_date_end) || (!empty($attRule->eff_date_end) && $attRule->eff_date_end >= $date))) {

                                if ( date('h:i:59', strtotime($attRule->ext_start_time)) < $rowFirst->time) {
                                    // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: #ff9900;'>LP</span>";
                                    $employeeData[$key]->attendance[$date]['status'] = "LP";
                                } else {
                                    // $employeeData[$key]->attendance[$date] = "<span style='font-weight:500; color: green;'>P</span>";
                                    $employeeData[$key]->attendance[$date]['status'] = "P";
                                }
                            }
                        }
                    }

                }
            } else {
                if ($withAbsent == 'no') {
                    continue;
                }
            }
            ## Attendance Calculation End

            ## Movement Calculation Start
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            if (count($empMovementData) > 0) {
                $employeeData[$key]->attendance = array_merge($employeeData[$key]->attendance, array_flip(array_keys($empMovementData)));
            }
            ## Movement Calculation End



            // ## Attendance Time Calculation Start
            // $employeeData[$key]->attendanceTime = [];
            // $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

            // if (count($empAttendanceData) > 0) {

            //     foreach ($empAttendanceData as $date => $rowData) {
            //         // $employeeData[$key]->attendanceTime[$date]['in'] = "-";
            //         // $employeeData[$key]->attendanceTime[$date]['out'] = "-";

            //         if (count($rowData) > 0) {
            //             $employeeData[$key]->attendanceTime[$date]['in'] = date('h:ia', strtotime($rowData[0]->time));
            //             $employeeData[$key]->attendanceTime[$date]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));
            //         }
            //     }
            // } else {
            //     // if($withAbsent == 'no'){
            //     //     continue;
            //     // }
            // }
            // ## end att


            ## Leave Calculation Start
            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();

            if (count($empLeaveData) > 0) {

                foreach ($empLeaveData as $rowLeave) {

                    $startDate = $rowLeave->date_from;
                    $endDate = $rowLeave->date_to;
                    $leaveCatId = $rowLeave->leave_cat_id;

                    $tempDate = $startDate;

                    while (($tempDate <= $endDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                        if (isset($employeeData[$key]->attendance[$tempDate])) {
                            // $employeeData[$key]->attendance[$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? "<span style='font-weight:500; color:blue;'>" . $leaveCategoryData[$leaveCatId] . "/P</span>": "NaN";

                            // $employeeData[$key]->attendanceTime[$tempDate]['in'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] . "/P" : "NaN";
                            // $employeeData[$key]->attendanceTime[$tempDate]['out'] = '';

                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] . "/P" : "NaN";

                            $employeeData[$key]->attendance[$tempDate]['in'] = date('h:ia', strtotime($rowData[0]->time));
                            $employeeData[$key]->attendance[$tempDate]['out'] = date('h:ia', strtotime($rowData[count($rowData) - 1]->time));


                        } else {
                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            if ($employeeData[$key]->attendance[$tempDate]['status'] == "AL") {
                                $employeeData[$key]->totalAL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate]['status'] == "CL") {
                                $employeeData[$key]->totalCL++;
                            } elseif ($employeeData[$key]->attendance[$tempDate]['status'] == "SL") {
                                $employeeData[$key]->totalSL++;
                            }
                        }

                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
            }
            ## Leave Calculation end

        }
// dd($employeeData);
// dd($employeeData, $empMovementData, $movementArr);
        return view('HR.Reports.AttendanceReports.InOutReport.report_body', compact('employeeData', 'attendanceDates', 'monthDates', 'withAbsent', 'monthYear', 'holidays', 'withHoliday', 'empMovementData', 'movementArr', 'attendanceRules','isTime'));
    }


}
