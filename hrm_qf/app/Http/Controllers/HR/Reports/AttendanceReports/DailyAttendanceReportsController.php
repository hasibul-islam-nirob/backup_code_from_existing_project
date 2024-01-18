<?php

namespace App\Http\Controllers\HR\Reports\AttendanceReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
class DailyAttendanceReportsController extends Controller
{
    //
    public function getStatus()
    {
        return view('HR.Reports.AttendanceReports.DailyAttendanceReports.index');
    }
    public function loadstatus(Request $request)
    {


        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $orderStatus = (empty($request->status)) ? null : $request->status;

        $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        $endDate = $startDate;

        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        $withHoliday = (empty($request->withHoliday)) ? 'yes' : $request->withHoliday;

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


        ## Attendance Rules Query Start
        $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
        $attendanceBypassDesignation = array_column($attendanceRules->toArray(), 'attendance_bypass');

        if(count($attendanceBypassDesignation) == 1){
            $attendanceBypassDesignation = explode(",",$attendanceBypassDesignation[0]);
        }
        elseif(count($attendanceBypassDesignation) > 1){
            $attendanceBypassDesignation = explode(",", implode("", call_user_func_array('array_intersect', array_map('str_split', $attendanceBypassDesignation))));
        }

        // array_pop($attendanceBypassDesignation);
        ## Attendance Rules Query End

        ## Branch Array Query End
        $statusArray = array_column($this->GlobalRole, 'set_status');
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            'joinDateTo' => $monthEndDate,
            'fromDate' => $monthStartDate,
            'ignoreDesignations' => $attendanceBypassDesignation,
            'statusArray' => $statusArray,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id'
        ]);
        
        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        ## Employee Query End

        ## Attendance Query Start
        if ("get_attendance_query") {
            $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
            $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
            // dd($employeeWiseAttendanceData);
            $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
        }
        ## Attendance Query End

        ## Movement Query Start
        if ("get_movement_query") {
            $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            // dd($movementData);
            $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        }

        // dd($movementArr);
        ## Movement Query End

        ## Leave Query Start
        if ("get_leave_query") {
            $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
            $leaveArr = $leaveData->groupBy('emp_id')->toArray();
            $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
            
        }
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'name');

        $allLeaveCategoryData = DB::table("hr_leave_category")
            ->where([['is_delete', 0], ['is_active', 1], ['short_form','<>','LWP']])
            ->get();
        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);


        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);

        ## Attendance Rules Query Start
        if ("get_attendance_rules_query") {
            $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
            $action_for_lp = $attendanceRules->pluck('action_for_lp');
        }

        ## Status Count Array
        $statusCountArray = [
            'all' => 0,
            'p' => 0,
            'lp' => 0,
            'mp' => 0,
            // 'pl' => 0,
            'a' => 0,
            'al' => 0,
            'sl' => 0,
            'cl' => 0,
            'pl' => 0,
            'ml' => 0,
        ];

        // $allBranchData = DB::table('gnl_branchs')->where([['is_active', 1],['is_delete', 0]])->select('branch_name','branch_code','id')->get()->toArray();
        // // Extract 'branch_name' and 'branch_code' columns from the collection
        // $branchNames = array_column($allBranchData, 'branch_name');
        // $branchCodes = array_column($allBranchData, 'branch_code');

        // // Combine 'branch_name' and 'branch_code' into the desired format
        // $allBranchData = array_combine(array_column($allBranchData, 'id'), array_map(function ($name, $code) {
        //     return "$name - [$code]";
        // }, $branchNames, $branchCodes));

        ## Attendance Rules Query End
        foreach ($employeeData as $key => $row) {
            $empId = $row->id;
            
            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            // $employeeData[$key]->emp_branch = (isset($allBranchData[$row->branch_id])) ? $allBranchData[$row->branch_id] : "";
            $employeeData[$key]->emp_designation_id = $row->designation_id;

            $employeeData[$key]->attendance = array();

            ## Attendance Calculation Start
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();
            ksort($empAttendanceData);
            // dd($empAttendanceData);


            $onDuty = $offDuty = $inTime = $outTime = $commonDutyEndTime= $commonClockInTime= $commonClockOutTime= "";
            // dd($employeeData);
            // dd($empAttendanceData);
            $onDuty = $offDuty = '-';
            if(count($empAttendanceData) > 0){
                foreach ($empAttendanceData as $date => $rowData) {
                    $rowFirst = $rowData[0];
                    $on_Duty = $off_Duty = '-';
                    
                    // dd($date, $monthEndDate,$monthStartDate);
                    if (($date > $monthEndDate) || ($date<$monthStartDate)) {
                        continue;
                    }
                    // if(($date != $startDate)){
                    //     continue;
                    // }
                    
                    if (count($attendanceRules) > 0) {
                        foreach($attendanceRules as $attValue){

                            if (!empty($attValue->eff_date_end)) {

                                if( ($attValue->eff_date_start <= $date) && ($attValue->eff_date_end >= $date) ){

                                    $onDuty = date('h:i a', strtotime($attValue->start_time));
                                    $offDuty = date('h:i a', strtotime($attValue->end_time));

                                    $on_Duty = date('h:i a', strtotime($attValue->start_time));
                                    $off_Duty = date('h:i a', strtotime($attValue->end_time));

                                    $inTime = date('h:i a', strtotime($rowData[0]->time));
                                    $outTime = date('h:i a', strtotime($rowData[count($rowData) - 1]->time));
                                    $startExtTime = date('h:i a', strtotime($attValue->ext_start_time));

                                    $commonDutyEndTime = new DateTime($attValue->end_time);
                                    $commonClockInTime = new DateTime($rowData[0]->time);
                                    $commonClockOutTime = new DateTime($rowData[count($rowData) - 1]->time);

                                    break;
                                }else{
                                    continue;
                                }

                            }elseif(empty($attValue->eff_date_end)){

                                $onDuty = date('h:i a', strtotime($attValue->start_time));
                                $offDuty = date('h:i a', strtotime($attValue->end_time));

                                $on_Duty = $onDuty;
                                $off_Duty = $offDuty;

                                $inTime = date('h:i a', strtotime($rowData[0]->time));
                                $outTime = date('h:i a', strtotime($rowData[count($rowData) - 1]->time));
                                $startExtTime = date('h:i a', strtotime($attValue->ext_start_time));

                                $commonDutyEndTime = new DateTime($attValue->end_time);
                                $commonClockInTime = new DateTime($rowData[0]->time);
                                $commonClockOutTime = new DateTime($rowData[count($rowData) - 1]->time);
                            }
                        }

                    }
                    
                    foreach ($monthDates as $keyDate => $value) {
                        if(!isset($empAttendanceData[$date])){
                            $employeeData[$key]->attendance[$keyDate]['on_duty'] = $onDuty;
                            $employeeData[$key]->attendance[$keyDate]['off_duty'] = $offDuty;
                            $employeeData[$key]->attendance[$keyDate]['clock_in'] = $inTime;
                            $employeeData[$key]->attendance[$keyDate]['clock_out'] = '-';
                            $employeeData[$key]->attendance[$keyDate]['late_time'] = '-';
                            $employeeData[$key]->attendance[$keyDate]['ot_time'] = '-';
                            $employeeData[$key]->attendance[$keyDate]['early_out'] = '-';
                            $employeeData[$key]->attendance[$keyDate]['work_time'] = '-';
                            $employeeData[$key]->attendance[$keyDate]['status'] = 'Absent';

                            $statusCountArray['a'] += 1;
                        }
                    }

                    $employeeData[$key]->attendance[$date]['on_duty'] = $onDuty;
                    $employeeData[$key]->attendance[$date]['off_duty'] = $offDuty;
                    $employeeData[$key]->attendance[$date]['clock_in'] = $inTime;
                    $employeeData[$key]->attendance[$date]['clock_out'] = '-';
                    $employeeData[$key]->attendance[$date]['late_time'] = '-';
                    $employeeData[$key]->attendance[$date]['ot_time'] = '-';
                    $employeeData[$key]->attendance[$date]['early_out'] = '-';
                    $employeeData[$key]->attendance[$date]['status'] = '';

                    ## Early Out Start
                    if($outTime < $offDuty){
                        $endTime1 = $commonDutyEndTime;
                        $outTime2 = $commonClockOutTime;
                        $outTimediff = $endTime1->diff($outTime2);

                        if($outTimediff->format('%i') > $attendanceRules[0]->early_accept_minute){

                            $employeeData[$key]->attendance[$date]['early_out'] =  $outTimediff->format('%h : %i');
                        }
                    }
                    ## Early Out End


                    ## Clock Out / Work Start
                    $workTimediff = '';
                    if(($rowData[0]->time) == ($rowData[count($rowData) - 1]->time)){

                        $employeeData[$key]->attendance[$date]['clock_out'] = "-";

                        $time1 = $commonClockInTime;
                        $time2 = $commonDutyEndTime;
                        if(!empty($time1) || !empty($time2)){
                            $workTimediff = $time1->diff($time2);
                        }
                        

                    }else{

                        $employeeData[$key]->attendance[$date]['clock_out'] = $outTime;
                        $time1 = $commonClockInTime;
                        $time2 = $commonClockOutTime;
                    
                        if(!empty($time1) || !empty($time2)){
                            $workTimediff = $time1->diff($time2);
                        }
                    }

                    if(!empty($workTimediff)){
                        $employeeData[$key]->attendance[$date]['work_time'] = $workTimediff->format('%h : %i');
                    }
                
                    ## Clock Out / Work End


                    ## OT Time Start
                    if($outTime == $inTime || $outTime <= $offDuty){
                        $employeeData[$key]->attendance[$date]['ot_time'] = '-';

                    }elseif( $outTime > $offDuty ){

                        $otTime1 = $commonDutyEndTime;
                        $otTime2 = $commonClockOutTime;
                        $otTimediff = $otTime1->diff($otTime2);

                        $hour = $otTimediff->format('%h');
                        $minit = $otTimediff->format('%i');
                        $sec = $otTimediff->format('%s');

                        $totalOTMinutes = ($hour * 60);
                        $totalOTMinutes += $minit;

                        if( $totalOTMinutes >= $attendanceRules[0]->ot_cycle_minute && $outTime > $offDuty){
                            $employeeData[$key]->attendance[$date]['ot_time'] =  $otTimediff->format('%h : %i');
                        }

                    }
                    ## OT Time End

                    ## Late Time Start
                    ## Calculate Late Time Diffrent Start
                    $inTime = $rowFirst->time;
                    $dutyStart = $attendanceRules[0]->start_time;

                    $time1 = new DateTime($inTime);
                    $time2 = new DateTime($dutyStart);
                    $acceptedTime = $time1->diff($time2);

                    $hour = $acceptedTime->format('%h');
                    $minit = $acceptedTime->format('%i');
                    $sec = $acceptedTime->format('%s');
                    ## Calculate Late Time Diffrent End

                    if(
                        ($minit > $attendanceRules[0]->late_accept_minute  || $hour > 0) &&
                        $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time))
                    ){

                        $employeeData[$key]->attendance[$date]['late_time'] = $acceptedTime->format('%h : %i');
                    }
                    ## Late Time End


                    ## Calculate Late Time Diffrent Start
                    $inTime = $rowFirst->time;
                    $dutyStart = $attendanceRules[0]->start_time;

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
                            $attendance_bypass_arr = explode(",", $attendanceRules[0]->attendance_bypass);
                            $late_bypass_arr = explode(",", $attendanceRules[0]->late_bypass);
                            if ( in_array($row->designation_id, $late_bypass_arr) ) {
                                $employeeData[$key]->attendance[$date]['status'] = "Present (Regular)";
                                $statusCountArray['p'] += 1;

                            }else{
                                $employeeData[$key]->attendance[$date]['status'] = "Present (Late)";
                                $statusCountArray['lp'] += 1;
                            }


                        } else {
                            $employeeData[$key]->attendance[$date]['status'] = "Present (Regular)";
                            $statusCountArray['p'] += 1;
                        }

                    } elseif (count($attendanceRules) > 1) {
                        foreach ($attendanceRules as $attRule) {

                            if (($attRule->eff_date_start <= $date) && (empty($attRule->eff_date_end) || (!empty($attRule->eff_date_end) && $attRule->eff_date_end >= $date))) {

                                if ( ($minit > $attendanceRules[0]->late_accept_minute  || $hour > 0) && $rowFirst->time > date('h:i:59', strtotime($attendanceRules[0]->start_time)) ) {
                                    $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
                                    $late_bypass_arr = !empty($attendanceRules[0]->late_bypass) ? explode(",", $attendanceRules[0]->late_bypass) : [];
                                    if ( in_array($row->designation_id, $late_bypass_arr) ) {
                                        $employeeData[$key]->attendance[$date]['status'] = "Present (Regular)";
                                        $statusCountArray['p'] += 1;

                                    }else{
                                        $employeeData[$key]->attendance[$date]['status'] = "Present (Late)";
                                        $statusCountArray['lp'] += 1;
                                    }
                                } else {

                                    $employeeData[$key]->attendance[$date]['status'] = "Present (Regular)";
                                    $statusCountArray['p'] += 1;
                                }
                            }
                        }
                    }

                }
            }else{

                if (count($attendanceRules) > 0) {
                    foreach($attendanceRules as $attValue){
                        if (!empty($attValue->eff_date_end)) {
                            if( ($attValue->eff_date_start <= $date) && ($attValue->eff_date_end >= $date) ){
                                $onDuty = date('h:i a', strtotime($attValue->start_time));
                                $offDuty = date('h:i a', strtotime($attValue->end_time));
                                break;
                            }else{
                                continue;
                            }
                        }elseif(empty($attValue->eff_date_end)){
                            $onDuty = date('h:i a', strtotime($attValue->start_time));
                            $offDuty = date('h:i a', strtotime($attValue->end_time));
                        }
                    }

                }

                foreach ($monthDates as $keyDate => $value) {
                    
                    $employeeData[$key]->attendance[$keyDate]['on_duty'] = $onDuty;
                    $employeeData[$key]->attendance[$keyDate]['off_duty'] = $offDuty;
                    $employeeData[$key]->attendance[$keyDate]['clock_in'] = $inTime;
                    $employeeData[$key]->attendance[$keyDate]['clock_out'] = '-';
                    $employeeData[$key]->attendance[$keyDate]['late_time'] = '-';
                    $employeeData[$key]->attendance[$keyDate]['ot_time'] = '-';
                    $employeeData[$key]->attendance[$keyDate]['early_out'] = '-';
                    $employeeData[$key]->attendance[$keyDate]['work_time'] = '-';
                    $employeeData[$key]->attendance[$keyDate]['status'] = 'Absent';

                    $statusCountArray['a'] += 1;
                    
                }
            }
            // dd($empAttendanceData);
            ## Attendance Calculation End

            
            ## Movement Calculation Start
          
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
            $empMovementData = array_merge($empMovementData, $allEmpMovementData);
            
            $att_and_move_arr_marge = array_merge($empAttendanceData, $empMovementData);
            ksort($att_and_move_arr_marge);

            if (count($empMovementData) > 0) {

                foreach($empMovementData as $moveDate => $moveData){

                    if ($moveDate > $monthEndDate) {
                        continue;
                    }
                    if (($moveDate > $monthEndDate) || ($moveDate<$monthStartDate)) {
                        continue;
                    }
                    
                    $moveAppFor = $moveData[0]->application_for;
                    // dd($moveAppFor,$moveData);
                    if (count($attendanceRules) > 0) {
                        foreach($attendanceRules as $attValue){
                            // dd($attValue, $onDuty, $offDuty);
                            if (!empty($attValue->eff_date_end)) {
                                if( ($attValue->eff_date_start <= $moveDate) && ($attValue->eff_date_end >= $moveDate) ){

                                    $onDuty = date('h:i a', strtotime($attValue->start_time));
                                    $offDuty = date('h:i a', strtotime($attValue->end_time));
                                    $employeeData[$key]->attendance[$moveDate]['on_duty'] = $onDuty;
                                    $employeeData[$key]->attendance[$moveDate]['off_duty'] = $offDuty;
                                    break;

                                }else{
                                    continue;
                                }

                            }elseif(empty($attValue->eff_date_end)){

                                $onDuty = date('h:i a', strtotime($attValue->start_time));
                                $offDuty = date('h:i a', strtotime($attValue->end_time));
                                $employeeData[$key]->attendance[$moveDate]['on_duty'] = $onDuty;
                                $employeeData[$key]->attendance[$moveDate]['off_duty'] = $offDuty;
                            }
                        }
                    }

                    if ($moveAppFor == "absent") {
                        if($moveData[0]->movement_date == $date){

                            if($moveData[0]->emp_id == 0 ){
    
                                if(isset($empAttendanceData[$moveDate])){
    
                                    if ($inTime >= $moveData[0]->start_time && $inTime <= $moveData[0]->end_time) {
                                        $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                                    }else{
                                        if($inTime <= $moveData[0]->end_time){
                                            $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                                        }else{
                                            $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Late)";
                                        }
                                    }
                                }else{
                                    $employeeData[$key]->attendance[$moveDate]['status'] = "Absent (Movement)";
                                }
                                
                            }else{
                                
                                $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                            }
                        }
                        $employeeData[$key]->attendance[$moveDate]['clock_in'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['clock_out'] = '-';
                        // $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                        $employeeData[$key]->attendance[$moveDate]['late_time'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['ot_time'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['early_out'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['work_time'] = '-';
                        
                        $statusCountArray['mp'] += 1;
                    }else{
                        if($moveData[0]->movement_date == $date){

                            if($moveData[0]->emp_id == 0 ){
    
                                if(isset($empAttendanceData[$moveDate])){
    
                                    if ($inTime >= $moveData[0]->start_time && $inTime <= $moveData[0]->end_time) {
                                        $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                                    }else{
                                        if($inTime <= $moveData[0]->end_time){
                                            $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                                        }else{
                                            $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Late)";
                                        }
                                    }
                                }else{
                                    $employeeData[$key]->attendance[$moveDate]['status'] = "Absent (Movement)";
                                }
                                
                            }else{
                                // dd($movData);
                                $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                            }
                        }
                        $employeeData[$key]->attendance[$moveDate]['clock_in'] = empty($inTime) ? "-": date("h:i a", strtotime($inTime));
                        $employeeData[$key]->attendance[$moveDate]['clock_out'] = empty($outTime) ? "-": $outTime;
                        // $employeeData[$key]->attendance[$moveDate]['status'] = "Present (Movement)";
                        $employeeData[$key]->attendance[$moveDate]['late_time'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['ot_time'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['early_out'] = '-';
                        $employeeData[$key]->attendance[$moveDate]['work_time'] = '-';
                       
                        $statusCountArray['mp'] += 1;
                    }

                }
            }
            ## Movement Calculation End

            ## Leave Calculation Start
            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
            $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
            $empLeaveData = array_merge($empLeaveData, $allLeaveData);
            if (count($empLeaveData) > 0) {
               
                foreach ($empLeaveData as $rowLeave) {

                    $startLeaveDate = $rowLeave->date_from;
                    $endDate = $rowLeave->date_to;
                    $leaveCatId = $rowLeave->leave_cat_id;

                    $tempDate = $startLeaveDate;
                    if ($tempDate > $monthEndDate) {
                        continue;
                    }

                    while (($tempDate <= $endDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                        if (count($attendanceRules) > 0) {
                            foreach($attendanceRules as $attValue){
                                if (!empty($attValue->eff_date_end)) {
                                    if( ($attValue->eff_date_start <= $tempDate) && ($attValue->eff_date_end >= $tempDate) ){

                                        $onDuty = date('h:i a', strtotime($attValue->start_time));
                                        $offDuty = date('h:i a', strtotime($attValue->end_time));
                                        $employeeData[$key]->attendance[$tempDate]['on_duty'] = $onDuty;
                                        $employeeData[$key]->attendance[$tempDate]['off_duty'] = $offDuty;
                                        break;

                                    }else{
                                        continue;
                                    }

                                }elseif(empty($attValue->eff_date_end)){

                                    $onDuty = date('h:i a', strtotime($attValue->start_time));
                                    $offDuty = date('h:i a', strtotime($attValue->end_time));
                                    $employeeData[$key]->attendance[$tempDate]['on_duty'] = $onDuty;
                                    $employeeData[$key]->attendance[$tempDate]['off_duty'] = $offDuty;
                                }
                            }

                        }

                        $employeeData[$key]->attendance[$tempDate]['clock_in'] = "-";
                        $employeeData[$key]->attendance[$tempDate]['clock_out'] = "-";
                        $employeeData[$key]->attendance[$tempDate]['late_time'] = '-';
                        $employeeData[$key]->attendance[$tempDate]['ot_time'] = '-';
                        $employeeData[$key]->attendance[$tempDate]['early_out'] = '-';
                        $employeeData[$key]->attendance[$tempDate]['work_time'] = '-';

                        if (isset($employeeData[$key]->attendance[$tempDate])) {

                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            foreach($allLeaveCategoryData as $allLeaveCat){
                                if($leaveCatId == $allLeaveCat->id){
                                    $lowercaseString = strtolower($allLeaveCat->short_form);
                                    if(isset($statusCountArray[$lowercaseString])){
                                        $statusCountArray[$lowercaseString] += 1;
                                    }
                                }
                            }
                            
                        } else {
                            $employeeData[$key]->attendance[$tempDate]['status'] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";

                            foreach($allLeaveCategoryData as $allLeaveCat){
                                if($leaveCatId == $allLeaveCat->id){
                                    $lowercaseString = strtolower($allLeaveCat->short_form);
                                    if(isset($statusCountArray[$lowercaseString])){
                                        $statusCountArray[$lowercaseString] += 1;
                                    }
                                }
                            }
                        }

                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
                
            }
            ## Leave Calculation end

        

            if (empty($attendance_bypass_arr)) { $attendance_bypass_arr = []; }
            if (empty($late_bypass_arr)) { $late_bypass_arr = []; }

            $empAttendanceData = !empty($empAttendanceData) ? $empAttendanceData : [];
            $newemployeelist = array();

        
        }
        if (!isset($empAttendanceData)) {$empAttendanceData = [];}
        if (!isset($attendance_bypass_arr)) {$attendance_bypass_arr = [];}
        // dd($employeeData);
        // dd($employeeData, $empAttendanceData);
        return view('HR.Reports.AttendanceReports.DailyAttendanceReports.report_body', compact('employeeData', 'orderStatus', 'empAttendanceData','withHoliday', 'withAbsent', 'holidays','attendance_bypass_arr', 'statusCountArray','allLeaveCategoryData'));
    }
}
