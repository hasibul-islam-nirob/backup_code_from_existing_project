<?php

namespace App\Http\Controllers\HR\Reports\AttendanceReports;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\VarDumper\Cloner\Data;

class EmpWiseAttendanceReportsController extends Controller
{
    public function index()
    {

        return view('HR.Reports.AttendanceReports.EmpWiseAttSheet.index');
    }

    public function reportBody(Request $request)
    {
        // if ($request->monthYear == '') return '';

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? 1 : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;


        $startDate = (empty($request->fromMonthYear)) ? null : (new DateTime($request->fromMonthYear));
        $endDateTmp = (empty($request->toMonthYear)) ? null : (new DateTime($request->toMonthYear));
        $endDate = ($endDateTmp->modify('last day of this month'));

        if ($endDateTmp->format("Y-m-d") > date("Y-m-d")) {
            $tmpEndDate = new DateTime();
            $endDate = $tmpEndDate->modify('last day of this month');
        }

        $targetYear = (empty($startDate)) ? null : $startDate->format("Y");

        if (($startDate->format("Y")) == ($endDate->format("Y"))) {
            $targetYear = (empty($request->startDate)) ? null : date('Y', strtotime($request->startDate));
        } elseif ($startDate > $endDate) {
            return response()->json([
                'message'    => "Start Date must be smallest..",
                'status' => 'error',
                'statusCode' => 400,
                'result_data' => ''
            ], 400);
        }
        // elseif( ($startDate->format("Y")) != ($endDate->format("Y")) ){

        //     return response()->json([
        //         'message'    => "From and To year are not same...",
        //         'status' => 'error',
        //         'statusCode'=> 400,
        //         'result_data' => ''
        //     ], 400);
        // }

        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        $getMonthDate = HRS::getMonthNameDatesDays($startDate, $endDate);
        ## Date And Day Calculation End

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

        ## Employee Query Start
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
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date, closing_date'
        ]);

        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $designationIdArr = array_values(array_unique($employeeData->pluck('designation_id')->toArray()));
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        $empJoinDate = $employeeData->pluck('join_date')->first();
        ## Employee Query End

        ## Attendance Query Start
        $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
        ## Attendance Query End

        ## Movement Query Start
        $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
        // dd($movementData);
        $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        ## Movement Query End

        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);

        ## Leave Query Start
        $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
        $leaveArr = $leaveData->groupBy('emp_id')->toArray();
        $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
        // dd($leaveData, $leaveArr, $leaveCatIdArr);
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');

        $currentDate = date("Y-m-d");

        foreach ($employeeData as $key => $row) {
            $empId = $row->id;

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_designation = (isset($designationData[$row->designation_id])) ? $designationData[$row->designation_id] : "";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";
            $employeeData[$key]->emp_branch = (isset($branchData[$row->branch_id])) ? $branchData[$row->branch_id] : "";
            $employeeData[$key]->yearlyAttendance = array();
            $employeeData[$key]->emp_designation_id = $row->designation_id;
            $employeeData[$key]->emp_resign_date = $employeeData[$key]->closing_date;

            $empResignDate =  new DateTime($employeeData[$key]->closing_date);
            $empWiseJoinDate = new DateTime($employeeData[$key]->join_date);

            ################
            $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();
            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
            $empMovementData = array_merge($empMovementData, $allEmpMovementData);

            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
            $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
            $empLeaveData = array_merge($empLeaveData, $allLeaveData);
            ###############

            ## Month & Date Show Start
            if (count($empAttendanceData) < 1 && count($empMovementData) < 1 && count($empLeaveData) < 1) {
                continue;
            }

            foreach ($getMonthDate as $month => $dateArray) {

                $firstDate = new DateTime(current(array_keys($dateArray)));
                $lastDate = new DateTime(last(array_keys($dateArray)));

                if ($empResignDate <= $firstDate || $empWiseJoinDate > $lastDate) {
                    continue;
                }

                $monthlyAL = $monthlyCL = $monthlySL = $monthlyLP = $monthlyLWP = 0;
                $totalLP = $totalLWP = 0;
                $employeeData[$key]->yearlyAttendance[$month] = array();
                $al = $cl = $sl = $lp = $lwp = 0;

                foreach ($dateArray as $monthInDate => $val) {

                    if (!in_array($monthInDate, $holidays) && !in_array($monthInDate, $empAttendanceData) && !in_array($monthInDate, $empMovementData) && !in_array($monthInDate, $empLeaveData)) {
                        $employeeData[$key]->yearlyAttendance[$month][$monthInDate] = "A";
                    }

                    if (date("Y-m-d") < $monthInDate || (($empWiseJoinDate) > (new DateTime($monthInDate)))) {
                        $employeeData[$key]->yearlyAttendance[$month][$monthInDate] = " ";
                    }

                    if (in_array($monthInDate, $holidays)) {
                        $employeeData[$key]->yearlyAttendance[$month][$monthInDate] = "H";
                    }


                    ## Attendance Calculation Start
                    if (count($empAttendanceData) > 0) {

                        if (isset($empAttendanceData[$monthInDate])) {

                            $toalRow = count($empAttendanceData[$monthInDate]) - 1;
                            $rowFirst = $empAttendanceData[$monthInDate][0];
                            $inTime = $rowFirst->time;
                            $outTime = $empAttendanceData[$monthInDate][$toalRow]->time;

                            if (count($attendanceRules) > 0) {

                                $dutyOnTime = '';
                                $dutyOutTime = '';
                                $lateAccepted = 0;
                                $lpAccepted = 0;
                                foreach ($attendanceRules as $attkey => $attRule) {

                                    if (!empty($attRule->eff_date_end)) {
                                        if (($attRule->eff_date_start <= $monthInDate) && ($attRule->eff_date_end >= $monthInDate)) {
                                            $dutyOnTime = $attRule->start_time;
                                            $dutyOutTime = $attRule->end_time;
                                            $lateAccepted = $attRule->late_accept_minute;
                                        }
                                    } else {
                                        $dutyOnTime = $attRule->start_time;
                                        $dutyOutTime = $attRule->end_time;
                                        $lateAccepted = $attRule->late_accept_minute;
                                    }

                                    ## Calculate Late Time Diffrent Start
                                    $time1 = new DateTime($inTime);
                                    $time2 = new DateTime($dutyOnTime);
                                    $acceptedTime = $time1->diff($time2);

                                    $hour = $acceptedTime->format('%h');
                                    $minit = $acceptedTime->format('%i');
                                    $sec = $acceptedTime->format('%s');

                                    $totalOTMinutes = ($hour * 60);
                                    $totalOTMinutes += $minit;
                                    ## Calculate Late Time Diffrent End

                                    if (
                                        ($totalOTMinutes > $lateAccepted  || $hour > 0) &&
                                        $rowFirst->time > date('h:i:59', strtotime($dutyOnTime))
                                    ) {

                                        $employeeData[$key]->yearlyAttendance[$month][$monthInDate] = "LP";
                                        $lp++;
                                    } else {

                                        $employeeData[$key]->yearlyAttendance[$month][$monthInDate] = "P";
                                    }

                                    $attendance_bypass_arr = !empty($attendanceRules[0]->attendance_bypass) ? explode(",", $attendanceRules[0]->attendance_bypass) : [];
                                    $late_bypass_arr = !empty($attendanceRules[0]->late_bypass) ? explode(",", $attendanceRules[0]->late_bypass) : [];
                                    // ss($attendance_bypass_arr, $late_bypass_arr);


                                }
                            }
                        }
                    }
                    ## Attendance Calculation End

                    ## Movement Calculation Start
                    /*
                    if (count($empMovementData) > 0) {
                        foreach ($empMovementData as $movDate => $movData) {

                            $tmpMoveDate = new DateTime($movDate);
                            if($tmpMoveDate >= $startDate && $tmpMoveDate <= $endDate){
                                if ($movDate == $monthInDate) {
                                    
                                    if($movData[0]->emp_id == 0 ){
                                        if(isset($empAttendanceData[$movDate])){

                                            if ($inTime >= $movData[0]->start_time && $inTime <= $movData[0]->end_time) {
                                                $employeeData[$key]->yearlyAttendance[$month][$movDate] = "MP";
                                            }else{

                                                if($inTime <= $movData[0]->end_time){
                                                    $employeeData[$key]->yearlyAttendance[$month][$movDate] = "MP";
                                                }else{
                                                    $employeeData[$key]->yearlyAttendance[$month][$movDate] = "LP";
                                                }
                                                // $employeeData[$key]->yearlyAttendance[$month][$movDate] = "LP";
                                            }

                                        }else{
                                            $employeeData[$key]->yearlyAttendance[$month][$movDate] = "A";
                                        }
                                    }elseif(isset($empAttendanceData[$movDate]) || isset($empMovementData[$movDate])){
                                        // dd($movData);
                                        $employeeData[$key]->yearlyAttendance[$month][$movDate] = "MP";
                                    }else{
                                        $employeeData[$key]->yearlyAttendance[$month][$movDate] = "A";
                                    }
                                }
                            }
                            
                        }
                    }
                    */

                    if (count($empMovementData) > 0) {
                        foreach ($empMovementData as $movDate => $movData) {
                            $tmpMoveDate = new DateTime($movDate);
                    
                            if ($tmpMoveDate >= $startDate && $tmpMoveDate <= $endDate && $movDate == $monthInDate) {
                                if ($movData[0]->emp_id == 0) {
                                    if (isset($empAttendanceData[$movDate])) {
                                        if ($inTime >= $movData[0]->start_time && $inTime <= $movData[0]->end_time) {
                                            $employeeData[$key]->yearlyAttendance[$month][$movDate] = "MP";
                                        } else {
                                            $employeeData[$key]->yearlyAttendance[$month][$movDate] = ($inTime <= $movData[0]->end_time) ? "MP" : "LP";
                                        }
                                    } else {
                                        $employeeData[$key]->yearlyAttendance[$month][$movDate] = "A";
                                    }
                                } elseif (isset($empAttendanceData[$movDate]) || isset($empMovementData[$movDate])) {
                                    $employeeData[$key]->yearlyAttendance[$month][$movDate] = "MP";
                                } else {
                                    $employeeData[$key]->yearlyAttendance[$month][$movDate] = "A";
                                }
                            }
                        }
                    }
                    ## Movement Calculation End


                    ## Leave Calculation Start
                    if (count($empLeaveData) > 0) {

                        foreach ($empLeaveData as $rowLeave) {

                            $startLeaveDate = $rowLeave->date_from;
                            $endLeaveDate = $rowLeave->date_to;
                            $leaveCatId = $rowLeave->leave_cat_id;

                            $tempDate = $startLeaveDate;

                            $leaveMonth = (new DateTime($rowLeave->date_from))->format('F');


                            if ($leaveMonth == $month) {

                                while (($tempDate <= $endLeaveDate) && ($tempDate <= $monthEndDate) && ($tempDate >= $monthStartDate)) {

                                    if (isset($employeeData[$key]->yearlyAttendance[$leaveMonth][$tempDate])) {

                                        $employeeData[$key]->yearlyAttendance[$month][$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";
                                    } else {
                                        $employeeData[$key]->yearlyAttendance[$month][$tempDate] = isset($leaveCategoryData[$leaveCatId]) ? $leaveCategoryData[$leaveCatId] : "NaN";
                                    }

                                    $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                }
                            }
                        }
                    }
                    ## Leave Calculation end

                }

                $employeeData[$key]->yearlyAttendance[$month]['totalLP'] = $monthlyLP;
                $employeeData[$key]->yearlyAttendance[$month]['totalLWP'] = $monthlyLWP;


                ksort($employeeData[$key]->yearlyAttendance[$month]);
            }
        }
        // dd($employeeData);

        // Error Handaling Start
        if (empty($attendance_bypass_arr)) {
            $attendance_bypass_arr = [];
        }
        if (empty($late_bypass_arr)) {
            $late_bypass_arr = [];
        }
        // Error Handaling End
        // dd($employeeData);

        return view('HR.Reports.AttendanceReports.EmpWiseAttSheet.report_body', compact('const31DayForAllMonth', 'employeeData', 'getMonthDate', 'attendance_bypass_arr', 'late_bypass_arr', 'holidays', 'targetYear', 'startDate'));
    }

    public function Backup2___reportBody(Request $request)
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


        $startDate = (empty($request->fromMonthYear)) ? null : (new DateTime($request->fromMonthYear));
        $endDateTmp = (empty($request->toMonthYear)) ? null : (new DateTime($request->toMonthYear));
        $endDate = ($endDateTmp->modify('last day of this month'));


        if (($startDate->format("Y")) >= ($endDate->format("Y"))) {
            $targetYear = (empty($request->startDate)) ? null : date('Y', strtotime($request->startDate));
        } elseif (($startDate->format("Y")) <= ($endDate->format("Y"))) {
            $targetYear = (empty($request->$endDate)) ? null : date('Y', strtotime($request->$endDate));
        }

        ## Date And Day Calculation Start
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');
        $lastDate = $endDate->format('d');

        $getMonthDate = HRS::getMonthNameDatesDays($startDate, $endDate);
        ## Date And Day Calculation End

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
        ## Employee Query End

        ## Attendance Query Start
        $attendanceData = HRS::queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate);
        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        $attendanceDates = array_values(array_unique($attendanceData->pluck('date')->toArray()));
        ## Attendance Query End


        ## Movement Query Start
        $movementData = HRS::queryGetMovementData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
        $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();
        ## Movement Query End

        ## Branch Query
        $branchData = Common::fnForBranchData($selBranchArr);
        ## Designation Query
        $designationData = HRS::fnForDesignationData($designationIdArr, 'name');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);


        ## Leave Query Start
        $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate);
        $leaveArr = $leaveData->groupBy('emp_id')->toArray();
        $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
        ## Leave Query End

        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');

        ## Attendance Rules Query Start
        $attendanceRules = HRS::queryGetAttendanceRulesData($monthStartDate, $monthEndDate);
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

                    // if (date("Y-m-d") < $keyDate) {
                    //     continue;
                    // }

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

                    if (date("Y-m-d") < $keyDate || $empJoinDate < $keyDate) {
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
            //---------------------------------==========================
            /*
                ## Movement Calculation Start
                $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
                if (count($empMovementData) > 0) {
                    $employeeData[$key]->attendance = array_merge($employeeData[$key]->attendance, array_flip(array_keys($empMovementData)));
                }
                ## Movement Calculation End
            */
        }

        ss($employeeData);
        // dd($employeeData,$employeeData[2], $employeeData[3], $employeeData[4], $employeeData[5], $employeeData[6], $employeeData[7], $employeeData[8], $employeeData[9], $employeeData[10]);

        return view('HR.Reports.AttendanceReports.EmpWiseAttSheet.report_body', compact('const31DayForAllMonth', 'employeeData', 'targetYear', 'getMonthDate'));
    }

    public function BackUp_reportBody(Request $request)
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

        $withAbsent = (empty($request->withAbsent)) ? 'no' : $request->withAbsent;
        // $withHoliday = (empty($request->withHoliday)) ? 'no' : $request->withHoliday;
        // $isTime = (empty($request->withTime)) ? 'no' : $request->withTime;

        // $monthYear = new DateTime($monthYear);
        // $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        // $endDate = (empty($request->endDate)) ? null : (new DateTime($request->endDate));

        $targetYear = (empty($request->monthYear)) ? null : date('Y', strtotime($request->monthYear));
        $startDate = new DateTime("$targetYear-01-01");
        $endDate = new DateTime("$targetYear-12-31");

        // $startYear = $startDate->format('Y');
        // $endYear = $endDate->format('Y');



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
        // $holidays = array();
        // if ($withHoliday == 'yes') {
        $holidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
        // }

        ## Employee Query Start

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
        $designationData = HRS::fnForDesignationData($designationIdArr, 'name');
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
            ->selectRaw('start_time, end_time, ext_start_time, late_accept_minute, eff_date_start, eff_date_end')
            ->get();
        ## Attendance Rules Query End

        // $employeeData[$key]->attendance = [];
        // $employeeData->month = array();

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

                $totalLP = 0;

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


                    ## Attendance Array
                    $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();

                    ## Attendance Calculation Start
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

                            // dd($acceptedTime, $totalOTMinutes, $attendanceRules[0]->late_accept_minute);
                            ## Calculate Late Time Diffrent End

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
                    } else {
                        if ($withAbsent == 'no') {
                            if (count($empAttendanceData) < 1) {
                                continue;
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
                                $const31Days[$keyDate] = "MP";
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


        // ksort($employeeData[5]->yearlyAttendance['January']);
        // dd($employeeData[5]->yearlyAttendance['January']);


        dd($employeeData);


        return view('HR.Reports.AttendanceReports.EmpWiseAttSheet.report_body', compact('const31DayForAllMonth', 'employeeData', 'withAbsent'));
    }
}
