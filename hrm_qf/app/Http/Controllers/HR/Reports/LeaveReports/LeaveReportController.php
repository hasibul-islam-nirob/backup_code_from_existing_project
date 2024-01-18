<?php

namespace App\Http\Controllers\HR\Reports\LeaveReports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Applications\EmployeeLeaveController;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use Ds\Set;

class LeaveReportController extends Controller
{

    

    public function getConsume()
    {
        return view('HR.Reports.LeaveReports.ConsumeReports.consume');
    }

    public function loadConsume(Request $request)
    {
        // dd($request->all());

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

        if ("Leave_Type") {
            $leave_type = HRS::fnForLeaveTypeData($request->leave_type_id);
            // dd($leave_type);
        }

        if ("Leave_Category") {
            $leave_cat = HRS::fnForLeaveCategoryByTypeData($request->leave_type_id);
            // dd($leave_cat);
        }


        ## Leave Query Start
        $allLeaveCategoryData = DB::table('hr_leave_category as hlc')
            ->where([['hlc.is_active', 1],['hlc.is_delete', 0]])
            ->join('gnl_dynamic_form_value as gdfv', 'hlc.leave_type_uid', '=', 'gdfv.uid')
            ->where([['gdfv.type_id', 3], ['gdfv.form_id', 1]])
            ->get();
        ## Leave Query End

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


        ## Attendance Rules Query Start
        if ("get_attendance_rules_query") {
            $attendanceRulesGetBypass = HRS::queryGetAttendanceRulesData($flt_start_date, $flt_end_date);
            $attendanceBypassDesignation = array_column($attendanceRulesGetBypass->toArray(), 'attendance_bypass');
            if(count($attendanceBypassDesignation) > 1){
                $attendanceBypassDesignation = explode(",", implode("", call_user_func_array('array_intersect', array_map('str_split', $attendanceBypassDesignation))));
            }else{
                $attendanceBypassDesignation = count($attendanceBypassDesignation) > 0 ? explode(",", $attendanceBypassDesignation[0]) : [];
            }

            $getFromLpBreakdown = $attendanceRulesGetBypass->pluck('attendance_bypass');
            $leave_bypass_arr = !empty($getFromLpBreakdown[count($getFromLpBreakdown) - 1]) ? explode(",", $getFromLpBreakdown[count($getFromLpBreakdown) - 1]) : [];
            // dd($attendanceRulesGetBypass, $getFromLpBreakdown, $leave_bypass_arr);
        }
        ## Attendance Rules Query End

        ## Employee Query Start
        // dd($flt_start_date, $flt_end_date);
        $statusArray = array_column($this->GlobalRole, 'set_status');
        $employeeData = HRS::fnForGetEmployees([
            'branchIds' => $selBranchArr,
            'departmentId' => $departmentId,
            'designationId' => $designationId,
            'employeeId' => $employeeId,
            // 'joinDateTo' => $flt_end_date,
            'fromDate' => $flt_start_date,
            'ignoreDesignations' => $attendanceBypassDesignation,
            'statusArray' => $statusArray,
            'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
            'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date, closing_date'
        ]);
        // dd($statusArray, $employeeData);
        $employeeIdArr = $employeeData->pluck('id')->toArray();
        $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
        ## Employee Query End

        ## Leave Query Start
        if ("get_leave_query") {
            $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $flt_start_date, $flt_end_date);
            $leaveArr = $leaveData->groupBy('emp_id')->toArray();
            $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
        }
        ## Leave Query End


        ## Leave Query
        $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');
        ## Department Query
        $departmentData = HRS::fnForDepartmentData($departmentIdArr);
        // dd($leaveCategoryData);

        foreach ($employeeData as $key => $row) {
            $empId = $row->id;

            // if (in_array($row->designation_id, $attendance_bypass_arr)) {
            //     continue;
            // }

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";

            $empJoinDate = $employeeData[$key]->join_date;
            $empResignDate =  new DateTime($employeeData[$key]->closing_date);

            if ($empResignDate <= $flt_start_date || $empJoinDate > $flt_end_date) {
                continue;
            }

            ## Employee Resign Date
            $empResignDate =  $employeeData[$key]->closing_date;

            $employeeData[$key]->opening = [];
            $employeeData[$key]->during = [];
            $employeeData[$key]->closing= [];

            /*
                Make Common Array for Opening, During and Closing
            */
            foreach($leave_type as $leaveTypeData){
                $item = [];
                foreach($leave_cat as $leaveCatData){
                    if ( $leaveTypeData->uid == $leaveCatData->leave_type_uid ) {
                        $leaveName = $leaveCatData->short_form;
                        $newArray = array_push($item, $leaveCatData->short_form);
                    }
                }
                $flippedArray = array_flip($item);
                $outputArray = array_fill_keys(array_keys($flippedArray), 0);

                $employeeData[$key]->opening[$leaveTypeData->name] = $outputArray;
                $employeeData[$key]->during[$leaveTypeData->name]  = $outputArray;
                $employeeData[$key]->closing[$leaveTypeData->name] = $outputArray;
            }
            
            // For Opening Data
            if ( !empty($flt_start_date) && !empty($flt_end_date)) {

                $openingTable = HRS::fnForCombineLeaveData($flt_start_date, $flt_end_date, 'opening');
                $leaveData = $openingTable->groupBy('emp_id')->toArray();
                $leaveDataByEmpId = (isset($leaveData[$empId])) ? $leaveData[$empId] : array();
                $leaveDataByEmtEnp = (isset($leaveData[0])) ? $leaveData[0] : array();
                $leaveDataByEmpId = array_merge($leaveDataByEmpId, $leaveDataByEmtEnp);

                foreach($leave_type as $leaveTypeData){

                    foreach($leave_cat as $leaveCatData){
                        if ( $leaveTypeData->uid == $leaveCatData->leave_type_uid ) {
                            $leaveName = $leaveCatData->short_form;

                            $leaveCount = array();
                            ## Leave Calculation For Opening Data
                            foreach ($leaveDataByEmpId as $item) {

                                if ( ($item->date_from != $item->date_to) &&
                                    ($item->date_from < $flt_start_date &&  $item->date_from < $flt_end_date)
                                ) {

                                    $tempDate = $item->date_from;
                                    while($tempDate <= $item->date_to ){

                                        if ((date('Y', strtotime($tempDate)) <= date('Y',strtotime($item->date_from))) ) {
                                            $leaveShortName = $item->leave_short_name;
                                            if (!isset($leaveCount[$leaveShortName])) {
                                                $leaveCount[$leaveShortName] = 0;
                                            }
                                            $leaveCount[$leaveShortName]++;
                                        }
                                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                    }

                                }elseif($item->date_from == $item->date_to){

                                    $leaveShortName = $item->leave_short_name;
                                    if (!isset($leaveCount[$leaveShortName])) {
                                        $leaveCount[$leaveShortName] = 0;
                                    }
                                    $leaveCount[$leaveShortName]++;

                                }
                            }


                            ## Set a condition for when search by is Date range
                            if($searchBy == 3){

                                foreach($leaveDataByEmpId as $empLeave){
                                    if( ($empLeave->leave_short_name == $leaveName) && ($leaveTypeData->name == $empLeave->type_name) ){

                                        $employeeData[$key]->opening[$leaveTypeData->name][$leaveName] = $leaveCount[$leaveName];
                                    }
                                }
                            }else{
                                foreach($leaveDataByEmpId as $empLeave){
                                    $isFlash = HRS::checkLeaveType($empLeave->leave_cat_id);
                                    if($isFlash == 1){
                                        if( ($empLeave->leave_short_name == $leaveName) && ($leaveTypeData->name == $empLeave->type_name) ){

                                            $employeeData[$key]->opening[$leaveTypeData->name][$leaveName] = 0;
                                        }
                                    }else{
                                        if( ($empLeave->leave_short_name == $leaveName) && ($leaveTypeData->name == $empLeave->type_name) ){
                                            
                                            $employeeData[$key]->opening[$leaveTypeData->name][$leaveName] = $leaveCount[$leaveName];
                                        }
                                    }
                                }
                            }

                            if($leaveTypeData->value_field == "nonpay" && $leaveCatData->short_form == "LWP"){
                                ##  LWP Counter
                                $lwpCountValue = HRS::lwpCountForLeaveReport([
                                    'selBranchArr' => $selBranchArr,
                                    'companyId' => $companyId,
                                    'branchId' => $branchId,
                                    'employeeIdArr' => $employeeIdArr,
                                    'empId' => $empId,
                                    'flt_start_date' => $flt_start_date,
                                    'flt_end_date' => $flt_end_date,
                                    'empResignDate' => $empResignDate,
                                    'empJoinDate' => $empJoinDate,
                                    'type' => 'opening'
                                ]);

                                // dd($lwpCountValue);

                                $employeeData[$key]->opening[$leaveTypeData->name]['LWP'] = $lwpCountValue;
                            }

                        }
                    }
                }
                // dd($leaveCount);

            }

            // For During Time Data
            if( !empty($flt_start_date) && !empty($flt_end_date)){

                $duringTable = HRS::fnForCombineLeaveData($flt_start_date, $flt_end_date, 'during');
                $leaveData = $duringTable->groupBy('emp_id')->toArray();
                $leaveDataByEmpId = (isset($leaveData[$empId])) ? $leaveData[$empId] : array();
                $leaveDataByEmtEnp = (isset($leaveData[0])) ? $leaveData[0] : array();
                $leaveDataByEmpId = array_merge($leaveDataByEmpId, $leaveDataByEmtEnp);

                foreach($leave_type as $leaveTypeData){

                    foreach($leave_cat as $leaveCatData){
                        if ( $leaveTypeData->uid == $leaveCatData->leave_type_uid ) {
                            $leaveName = $leaveCatData->short_form;

                            $leaveCount = array();
                            if(count($leaveDataByEmpId) > 0){
                                foreach ($leaveDataByEmpId as $item) {

                                    if (($item->date_from != $item->date_to) &&
                                        ($item->date_from >= $flt_start_date ||  $item->date_to <= $flt_end_date) ) {
    
                                        $tempDateTwo = $item->date_from;
                                        while($tempDateTwo <= $item->date_to ){
    
                                            if ( ($tempDateTwo <= $item->date_to) &&
                                                (($flt_start_date <= $tempDateTwo)  || ($flt_end_date <= $item->date_to)) ) {
                                                $leaveShortName = $item->leave_short_name;
                                                if (!isset($leaveCount[$leaveShortName])) {
                                                    $leaveCount[$leaveShortName] = 0;
                                                }
                                                $leaveCount[$leaveShortName]++;
                                            }
    
                                            $tempDateTwo = date("Y-m-d", strtotime("+1 day", strtotime($tempDateTwo)));
                                        }
    
                                    }elseif($item->date_from == $item->date_to){
    
                                        $leaveShortName = $item->leave_short_name;
                                        if (!isset($leaveCount[$leaveShortName])) {
                                            $leaveCount[$leaveShortName] = 0;
                                        }
                                        $leaveCount[$leaveShortName]++;
                                    }
    
    
                                }
    
                                foreach($leaveDataByEmpId as $empLeave){
                                    if( ($empLeave->leave_short_name == $leaveName) && ($leaveTypeData->name == $empLeave->type_name) ){
    
                                        if ( !isset($leaveCount[$leaveName])  ) {
                                            array_push($leaveCount, $leaveName);
                                        }
                                        $employeeData[$key]->during[$leaveTypeData->name][$leaveName] = $leaveCount[$leaveName];
                                    }
    
                                    if($leaveTypeData->value_field == "nonpay" && $leaveCatData->short_form == "LWP"){
                                        ##  LWP Counter
                                        $lwpCountValue = HRS::lwpCountForLeaveReport([
                                            'selBranchArr' => $selBranchArr,
                                            'companyId' => $companyId,
                                            'branchId' => $branchId,
                                            'employeeIdArr' => $employeeIdArr,
                                            'empId' => $empId,
                                            'flt_start_date' => $flt_start_date,
                                            'flt_end_date' => $flt_end_date,
                                            'empResignDate' => $empResignDate,
                                            'empJoinDate' => $empJoinDate,
                                            'type' => 'during'
                                        ]);
        
                                        $employeeData[$key]->during[$leaveTypeData->name]["LWP"] = $lwpCountValue;
                                    }
    
                                }
                            }else{
                                if($leaveTypeData->value_field == "nonpay" && $leaveCatData->short_form == "LWP"){
                                    ##  LWP Counter
                                    // $lwpCountValue = HRS::lwpCountForLeaveReport([
                                    //     'selBranchArr' => $selBranchArr,
                                    //     'companyId' => $companyId,
                                    //     'branchId' => $branchId,
                                    //     'employeeIdArr' => $employeeIdArr,
                                    //     'empId' => $empId,
                                    //     'flt_start_date' => $flt_start_date,
                                    //     'flt_end_date' => $flt_end_date,
                                    //     'empResignDate' => $empResignDate,
                                    //     'empJoinDate' => $empJoinDate,
                                    //     'type' => 'during'
                                    // ]);
    
                                    // $employeeData[$key]->during[$leaveTypeData->name]["LWP"] = $lwpCountValue;
                                }
                            }
                            

                        }
                    }
                }

            }

            // For Closing Data
            if ("closing") {

                $openingData =  [];
                $duringData =  [];
                foreach($leave_type as $leaveTypeData){

                    $duringData = $employeeData[$key]->during[$leaveTypeData->name];
                    $openingData = $employeeData[$key]->opening[$leaveTypeData->name];

                    $result = [];
                    foreach($openingData as $one => $oneVal){
                        foreach($duringData as $two => $twoVal){

                            if ($one == $two) {
                                $result[$one] = $oneVal + $twoVal;
                            }
                        }
                    }

                    $employeeData[$key]->closing[$leaveTypeData->name] = $result;
                }

            }

        }
        // dd($employeeData);
        $consume_info = $format = 0;
        return view('HR.Reports.LeaveReports.ConsumeReports.consume_body', compact('employeeData','leave_type', 'consume_info', 'leave_cat', 'format', 'leave_bypass_arr','searchByLeaveType','allLeaveCategoryData'));
    }
    

    public function getBalance()
    {
        return view('HR.Reports.LeaveReports.BalanceReports.balance');
    }

    public function getBalance2()
    {
        return view('HR.Reports.LeaveReports.BalanceReports2.balance_2');
    }

    public function loadBalance(Request $request)
    {

        // dd($request->all());
        if (1) {

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


            if ("Leave_Type") {
                $leave_type = HRS::fnForLeaveTypeData($request->leave_type_id);
            }

            if ("Leave_Category") {
                $leave_cat = HRS::fnForLeaveCategoryByTypeData($request->leave_type_id);
            }

            if ("Common_Leave_Query") {
                $tableForLeaveAlocated = DB::table('gnl_dynamic_form_value as gdfv')
                ->where([['type_id', 3], ['form_id', 1]])
                ->join('hr_leave_category as hrlc', function ($join) {
                    $join->on('gdfv.uid', '=', 'hrlc.leave_type_uid')
                        ->where('hrlc.is_delete', 0)
                        ->where('hrlc.is_active', 1);
                })
                ->join('hr_leave_category_details as hrlcd', 'hrlc.id', '=', 'hrlcd.leave_cat_id')
                ->select(
                    'gdfv.name as leave_type_name',
                    'hrlc.id as id',
                    'hrlc.short_form as leave_short_name',
                    'hrlc.name as leave_full_name',
                    'hrlcd.allocated_leave',
                    'hrlcd.capable_of_provision',
                    'hrlcd.max_leave_entitle',
                    'hrlcd.consume_after',
                    'hrlcd.eligibility_counting_from',
                    'hrlcd.consume_policy'
                )
                ->groupBy('hrlc.id')
                ->get();
            }

            ## Leave Query Start
            $allLeaveCategoryData = DB::table('hr_leave_category as hlc')
                ->where([['hlc.is_active', 1],['hlc.is_delete', 0]])
                ->join('gnl_dynamic_form_value as gdfv', 'hlc.leave_type_uid', '=', 'gdfv.uid')
                ->where([['gdfv.type_id', 3], ['gdfv.form_id', 1]])
                ->get();
            ## Leave Query End

            // dd($tableForLeaveAlocated, $allLeaveCategoryData);


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

            ## Attendance Rules Query Start
            if ("get_attendance_rules_query") {
                $attendanceRulesGetBypass = HRS::queryGetAttendanceRulesData($flt_start_date, $flt_end_date);

                $attendanceBypassDesignation = array_column($attendanceRulesGetBypass->toArray(), 'attendance_bypass');
                if(count($attendanceBypassDesignation) > 1){
                    $attendanceBypassDesignation = explode(",", implode("", call_user_func_array('array_intersect', array_map('str_split', $attendanceBypassDesignation))));
                }else{
                    $attendanceBypassDesignation = !empty($attendanceBypassDesignation) ? explode(",", $attendanceBypassDesignation[0]) : [];
                }

                $getFromLpBreakdown = $attendanceRulesGetBypass->pluck('attendance_bypass');
                $leave_bypass_arr = !empty($getFromLpBreakdown[count($getFromLpBreakdown) - 1]) ? explode(",", $getFromLpBreakdown[count($getFromLpBreakdown) - 1]) : [];
                // dd($attendanceRulesGetBypass, $getFromLpBreakdown, $leave_bypass_arr);
            }
            ## Attendance Rules Query End

            ## Employee Query Start
            $statusArray = array_column($this->GlobalRole, 'set_status');
            $employeeData = HRS::fnForGetEmployees([
                'branchIds' => $selBranchArr,
                'departmentId' => $departmentId,
                'designationId' => $designationId,
                'employeeId' => $employeeId,
                // 'joinDateTo' => $flt_end_date,
                'fromDate' => $flt_start_date,
                'ignoreDesignations' => $attendanceBypassDesignation,
                'statusArray' => $statusArray,
                'orderBy' => [['branch_id', 'ASC'], ['emp_code', 'ASC']],
                'selectRaw' => 'id, emp_name, emp_code, branch_id, designation_id, department_id, join_date, gender, closing_date'
            ]);
            $employeeIdArr = $employeeData->pluck('id')->toArray();
            $departmentIdArr = array_values(array_unique($employeeData->pluck('department_id')->toArray()));
            ## Employee Query End

            ## Leave Query Start
            if ("get_leave_query") {
                $leaveData = HRS::queryGetLeaveData($selBranchArr, $employeeIdArr, $flt_start_date, $flt_end_date);
                $leaveArr = $leaveData->groupBy('emp_id')->toArray();
                $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));
            }
            // dd($leaveData, $selBranchArr, $employeeIdArr, $flt_start_date, $flt_end_date);
            ## Leave Query End

            ## Leave Query
            $leaveCategoryData = HRS::fnForLeaveCategoryData($leaveCatIdArr, 'short_form');
            ## Department Query
            $departmentData = HRS::fnForDepartmentData($departmentIdArr);
            // dd($leaveCategoryData);
        }

        if($searchByLeaveType == null || empty($allLeaveCategoryData)){
            $allLeaveCategoryData = $allLeaveCategoryData;
        }elseif ($searchByLeaveType == 1) {
            $allLeaveCategoryData = $allLeaveCategoryData->where('leave_type_uid', 1);

        }elseif ($searchByLeaveType == 2) {
            $allLeaveCategoryData = $allLeaveCategoryData->where('leave_type_uid', 2);
        }
        elseif ($searchByLeaveType == 3) {
            $allLeaveCategoryData = $allLeaveCategoryData->where('leave_type_uid', 3);
        }
        elseif ($searchByLeaveType == 4) {
            $allLeaveCategoryData = $allLeaveCategoryData->where('leave_type_uid', 4);
        }

        foreach ($employeeData as $key => $row) {
            $empId = $row->id;

            //Employee Leave Data
            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
            $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
            $empLeaveData = array_merge($empLeaveData, $allLeaveData);
            // dd($empLeaveData, $leaveData);

            $employeeData[$key]->emp_name = $row->emp_name . " [" . $row->emp_code . "]";
            $employeeData[$key]->emp_department = (isset($departmentData[$row->department_id])) ? $departmentData[$row->department_id] : "";

            ## Employee Resign Date
            $empJoinDate = $employeeData[$key]->join_date;
            $empGender = $employeeData[$key]->gender;
            $empResignDate =  new DateTime($employeeData[$key]->closing_date);

            if ($empResignDate <= $flt_start_date || $empJoinDate > $flt_end_date) {
                // continue;
            }
            ## Employee Resign Date
            $empResignDate =  $employeeData[$key]->closing_date;

            $employeeData[$key]->leaveConsumeData = array();

            foreach($allLeaveCategoryData as $leaveData){
                
                // dd($leaveData);
                if($leaveData->value_field == 'nonpay'){

                    $tempArr = [
                        'Consumed' => 0,
                    ];
                    $employeeData[$key]->leaveConsumeData[$leaveData->name][$leaveData->short_form] = $tempArr;

                }else{
                    $tempArr = [
                        'Allocated' => 0,
                        'Eligible' => 0,
                        'Consumed' => 0,
                        'Balance' => 0,
                    ];

                    $employeeData[$key]->leaveConsumeData[$leaveData->name][$leaveData->short_form] = $tempArr;
                }

            }

            foreach($allLeaveCategoryData as $leaveData){

                foreach($leave_type as $leaveTypeData){

                    foreach($leave_cat as $leaveCatData){
                        if ( $leaveTypeData->uid == $leaveCatData->leave_type_uid ) {
                            $leaveName = $leaveCatData->short_form;
    
                            ## Leave Counter Start
                            $leaveCountArray = array();
                            foreach ($empLeaveData as $item) {
                                $leaveCatId = $item->leave_cat_id;
                                $shortForm = $item->short_form;
    
                                ##===========================
                                if (($item->date_from != $item->date_to) ) {
    
                                    $tempDate = $item->date_from;
                                    while($tempDate <= $item->date_to ){
    
                                        if ( (date('Y', strtotime($tempDate)) <= date('Y',strtotime($item->date_from))) &&
                                            ($tempDate <= $item->date_to) ) {
    
                                            if (isset($leaveCountArray[$shortForm])) {
                                                $leaveCountArray[$shortForm]++;
                                            } else {
                                                $leaveCountArray[$shortForm] = 1;
                                            }
                                        }
    
                                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                                    }
                                }elseif (($item->date_from == $item->date_to) ) {
    
                                    if (isset($leaveCountArray[$shortForm])) {
                                        $leaveCountArray[$shortForm]++;
                                    } else {
                                        $leaveCountArray[$shortForm] = 1;
                                    }
    
                                }
                                ##===========================
                            }
                            ## Leave Counter End
    
                            foreach($tableForLeaveAlocated as $leaveInfo){

                                ######################################
                                $start = new DateTime($flt_start_date);
                                $end = new DateTime($flt_end_date);
                                // Calculate the difference in days
                                $interval = $start->diff($end);
                                $daysDifference = $interval->days;
                                // Calculate the difference in months
                                $monthsDifference = ($interval->m + ($interval->y * 12)) + 1;
                                ######################################

                                // dd($tableForLeaveAlocated, $leaveInfo);
                                $elig = $cons = $bala = 0;
                                if($leaveInfo->consume_policy == 'yearly_allocated'){

                                    if( ($leaveInfo->leave_short_name == $leaveName) && ($leaveTypeData->name == $leaveInfo->leave_type_name) ){
    
                                        $alloc = ($leaveInfo->allocated_leave == null) ? 0 : $leaveInfo->allocated_leave;
                                        // $elig = ($leaveInfo->allocated_leave == null) ? 0 : $leaveInfo->allocated_leave;
                                        
                                        if($leaveInfo->leave_short_name == 'PL' || $leaveInfo->leave_short_name == "ML"){
                                            $elig = ($leaveInfo->allocated_leave == null) ? 0 : $leaveInfo->allocated_leave;
                                        }else{
                                            $elig = ($leaveInfo->allocated_leave == null) ? 0 : (int) floor(($leaveInfo->allocated_leave / 12) * $monthsDifference);
                                        }
                                        $cons = !empty($leaveCountArray[$leaveName]) ? $leaveCountArray[$leaveName] : 0;
                                        $bala = $elig - $cons;
        
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Allocated'] = $alloc;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Eligible'] = $elig;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Consumed'] = $cons;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Balance'] = $bala;
                                    }
                                    
                                }else{

                                    if( ($leaveInfo->leave_short_name == $leaveName) && ($leaveTypeData->name == $leaveInfo->leave_type_name) ){
                                        
                                        ## 31 -09 - 2023 Start
                                        // $alloc = ($leaveInfo->allocated_leave == null) ? 0 : (int) floor(($leaveInfo->allocated_leave / 12) * $monthsDifference);
                                        $alloc = ($leaveInfo->allocated_leave == null) ? 0 : $leaveInfo->allocated_leave;
                                        $elig = ($leaveInfo->allocated_leave == null) ? 0 : (int) floor(($leaveInfo->allocated_leave / 12) * $monthsDifference);
                                        $cons = empty($leaveCountArray[$leaveName]) ? 0 : $leaveCountArray[$leaveName];
                                        ## 31 -09 - 2023 End
                                        $bala = $elig - $cons;
        
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Allocated'] = $alloc;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Eligible'] = $elig;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Consumed'] = $cons;
                                        $employeeData[$key]->leaveConsumeData[$leaveTypeData->name][$leaveName]['Balance'] = $bala;
                                    }

                                }

                                
                                


                            }
    
    
                        }
                    }
                }

                if($leaveData->value_field == "nonpay" && $leaveData->short_form == "LWP"){
                    ##  LWP Counter
                    $lwpCountValue = HRS::lwpCountForLeaveReport(
                        $selBranchArr,$companyId, $branchId, $employeeIdArr,
                        $empId,$flt_start_date,$flt_end_date,
                        $empResignDate, $empJoinDate);

                    

                    $employeeData[$key]->leaveConsumeData[$leaveData->name][$leaveData->short_form]['Consumed'] = $lwpCountValue;
                }
            }

        }

        if(count($employeeData) > 0){
            foreach($employeeData as $tmpKey => $empData){
                if(isset($empData->leaveConsumeData, $employeeData)){
                    $leaveConsumeTable = !empty($employeeData) ? $employeeData[$tmpKey]->leaveConsumeData : [];
                    break;
                }else{
                    continue;
                }
            }
            // $leaveConsumeTable = !empty($employeeData) ? $employeeData[count($employeeData)-1]->leaveConsumeData : [];
        }else{
            $leaveConsumeTable = [];
        }
        // dd($employeeData, $leaveConsumeTable);
        // dd($leaveConsumeTable);

        return view('HR.Reports.LeaveReports.BalanceReports.balance_body', compact('employeeData','leave_cat','leave_bypass_arr','searchByLeaveType','leaveConsumeTable'));
    }


    public function loadBalance2(Request $request)
    {

        $format = '';
        if (empty($request->leave_cat_id)) {
            $format = 'aa';
        } else {
            $format = '11';
        }

        $fy = [];

        if ($request->search_by == 1) {
            $fy['fy_start_date'] = (new DateTime($request->start_date_fy))->format('Y-m-d');
            $fy['fy_end_date'] = (new DateTime($request->end_date_fy))->format('Y-m-d');
        } elseif ($request->search_by == 2) {
            $fy['fy_start_date'] = (new DateTime($request->start_date_cy))->format('Y-m-d');
            $fy['fy_end_date'] = (new DateTime($request->end_date_cy))->format('Y-m-d');
        } else {
            $f = DB::table('gnl_fiscal_year')->where('fy_start_date', '<=', date('Y-m-d'))->where('fy_end_date', '>=', date('Y-m-d'))->first();
            $fy['fy_start_date'] = $f->fy_start_date;
            $fy['fy_end_date'] = $f->fy_end_date;
        }

        $leave_cat = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0], ['leave_type_uid', 1]])
            ->when(!empty($request->leave_cat_id), function ($query) use ($request) {
                $query->where('id', $request->leave_cat_id);
            })
            ->get();

        if(count($leave_cat->toArray()) < 1){
            return '<div class="row pt-10 text-center">
                        <div class="col-lg-12">
                            <h3>Leave Category Not Found !!</h3>
                            <p>Please configure the leave category.</p>
                        </div>
                    </div>';
        }

        $leaveBal = DB::table('hr_leave_category as lc')

            ->leftJoin('hr_app_leaves as lv', function ($join) {
                $join->on('lc.id', '=', 'lv.leave_cat_id');
            })

            ->rightJoin('hr_employees as emp', function ($join) {
                $join->on('emp.id', '=', 'lv.emp_id');
            })

            ->leftJoin('hr_emp_organization_details as eod', function ($join) {
                $join->on('emp.id', '=', 'eod.emp_id');
            })

            ->leftJoin('hr_designations as des', function ($join) {
                $join->on('emp.designation_id', '=', 'des.id');
            })

            ->leftJoin('hr_leave_category_details as lcd', function ($join) {
                $join->on('lc.id', '=', 'lcd.leave_cat_id');
                $join->on('lcd.rec_type_id', '=', 'eod.rec_type_id');
            })

            ->when(true, function ($query) use ($request) {

                if (!empty($request->designation_id)) {
                    $query->where('emp.designation_id', $request->designation_id);
                }
                if (!empty($request->department_id)) {
                    $query->where('emp.department_id', $request->department_id);
                }
                if (!empty($request->emp_code)) {
                    $query->where('emp.emp_code', $request->emp_code);
                }
                if (!empty($request->leave_cat_id)) {
                    $query->where(function ($q1) use ($request) {
                        $q1->where('lv.leave_cat_id', '=', $request->leave_cat_id);
                        $q1->orWhere('lv.leave_cat_id', '=', null);
                    });
                }

                $selBranchArr = Common::getBranchIdsForAllSection([
                    // 'companyId'     => $companyId,
                    // 'projectId'     => $projectId,
                    // 'projectTypeId' => $projectTypeId,
                    'branchId'      => $request->branch_id,
                    'zoneId'      => $request->zone_id,
                    // 'regionId'      => $regionId,
                    'areaId'      => $request->area_id,
                ]);

                $query->whereIn('emp.branch_id', $selBranchArr);
            })

            ->whereRaw('(lcd.effective_date_from =
                    (SELECT effective_date_from FROM hr_leave_category_details
                        WHERE
                            rec_type_id = lcd.rec_type_id
                        AND
                            leave_cat_id = lcd.leave_cat_id
                        AND
                            effective_date_from <= "' . $fy['fy_end_date'] . '"
                        ORDER BY
                            effective_date_from DESC LIMIT 1
                    ) OR lcd.effective_date_from IS NULL)')

            ->where(function ($query) {
                $query->where([['lc.is_active', 1], ['lc.is_delete', 0], ['lv.is_active', '=', 1], ['leave_type_uid', 1]]);
                $query->orWhere([['lc.is_active', null], ['lc.is_delete', null], ['lc.leave_type_uid', null]]);
            })

            ->where('emp.is_delete', 0)
            ->where('emp.is_active', 1)

            ->where(function ($query) use ($fy) {
                $query->where([['lv.date_from', '>=', $fy['fy_start_date']], ['lv.date_to', '>=', $fy['fy_start_date']], ['lv.date_from', '<=', $fy['fy_end_date']], ['lv.date_to', '<=', $fy['fy_end_date']]]);
                $query->orWhere([['lv.date_from', null], ['lv.date_to', null]]);
            })

            ->select(
                'emp.id as employee_id',
                'emp.emp_name',
                'emp.emp_code',
                'emp.join_date',
                'emp.permanent_date',

                'eod.rec_type_id as rec_type_id',

                'des.name as designation_name',

                'lc.id as leave_cat_id',
                'lc.leave_type_uid',

                'lcd.id as leave_cat_details_id',
                'lcd.consume_policy',
                'lcd.allocated_leave',
                'lcd.eligibility_counting_from',
                'lcd.consume_after',

                'lc.name as lv_cat_name',
                'lcd.effective_date_from',
                'lv.date_to',
                'lv.date_from'

                //DB::raw('SUM(DATEDIFF(lv.date_to, lv.date_from)+1) AS consume'),
            )
            ->when(true, function ($query) use ($request) {
                if ($request->zero_balance == 2) {
                    $query->where('lv.id', '<>', null);
                }
            })

            ->get();

        $groupData = $leaveBal->groupBy(['employee_id', function ($item) {
            return (int)(new DateTime($item->date_from))->format('m');
        }, 'leave_cat_id']);


        $max_leave = DB::table('hr_leave_category as lc')->where([['is_active', 1], ['is_delete', 0], ['leave_type_uid', 1]])

            ->leftJoin('hr_leave_category_details as lcd', function ($join) {
                $join->on('lc.id', '=', 'lcd.leave_cat_id');
            })
            ->whereRaw('(lcd.effective_date_from =
                (SELECT effective_date_from FROM hr_leave_category_details
                    WHERE
                        rec_type_id = lcd.rec_type_id
                    AND
                        leave_cat_id = lcd.leave_cat_id
                    AND
                        effective_date_from <= "' . $fy['fy_end_date'] . '"
                    ORDER BY
                        effective_date_from DESC LIMIT 1
                ) OR lcd.effective_date_from IS NULL)')
            ->select(
                'lc.id as lv_cat_id',
                'lcd.allocated_leave',
                'lcd.rec_type_id'
            )
            ->get()->groupBy(['lv_cat_id', 'rec_type_id']);

        $data = [];

        foreach ($groupData as $emp_id => $mCol) {
            foreach ($mCol as $monNo => $lvCatCol) {
                foreach ($lvCatCol as $lv_cat_id => $info) {

                    $data[$emp_id]['emp_info']['name'] = $info[0]->emp_name . ' [' . $info[0]->emp_code . ']';
                    $data[$emp_id]['emp_info']['designation_name'] = $info[0]->designation_name;
                    $data[$emp_id]['emp_info']['rec_type_id'] = $info[0]->rec_type_id;

                    $consumed = 0;
                    foreach ($info as $d) {

                        $consumed += ((new DateTime($d->date_from))->diff(new DateTime($d->date_to))->format('%a') + 1);
                    }

                    $data[$emp_id]['consume_info'][$monNo][$lv_cat_id] = $consumed;
                }
            }
        }

        $m_start = (int)(new DateTime($fy['fy_start_date']))->format('m');
        $m_end = (int)(new DateTime($fy['fy_end_date']))->format('m');

        return view('HR.Reports.LeaveReports.BalanceReports2.balance_body_2', compact('leave_cat', 'data', 'max_leave', 'format', 'm_start', 'm_end'));
    }



    
    public function __loadConsume(Request $request)
    {

        $format = '';
        if (empty($request->leave_type_id) && empty($request->leave_cat_id)) {
            $format = 'aa';
        } elseif (!empty($request->leave_type_id) && empty($request->leave_cat_id)) {
            $format = '1a';
        } elseif (empty($request->leave_type_id) && !empty($request->leave_cat_id)) {
            $format = 'a1';
        } elseif (!empty($request->leave_type_id) && !empty($request->leave_cat_id)) {
            $format = '11';
        }

        $flt_start_date = $flt_end_date = date('Y-m-d');

        if ($request->search_by == 1) {
            $flt_start_date = (new DateTime($request->start_date_fy))->format('Y-m-d');
            $flt_end_date = (new DateTime($request->end_date_fy))->format('Y-m-d');
        } elseif ($request->search_by == 2) {
            $flt_start_date = (new DateTime($request->start_date_cy))->format('Y-m-d');
            $flt_end_date = (new DateTime($request->end_date_cy))->format('Y-m-d');
        } else {
            $flt_start_date = (new DateTime($request->start_date_dr))->format('Y-m-d');
            $flt_end_date = (new DateTime($request->end_date_dr))->format('Y-m-d');
        }

        $leave_type = DB::table('gnl_dynamic_form_value')->where([['type_id', 3], ['form_id', 1]])
            ->when(!empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('uid', $request->leave_type_id);
            })
            ->orderByRaw(
                "CASE WHEN uid=2 THEN 0 ELSE 1 END DESC"
            )
            ->get();

        $leave_cat = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])
            ->when(!empty($request->leave_cat_id) && empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('id', $request->leave_cat_id);
            })
            ->when(!empty($request->leave_cat_id) && !empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('id', $request->leave_cat_id);
                $query->where('leave_type_uid', $request->leave_type_id);
            })
            ->get();

        if(count($leave_cat->toArray()) < 1){
            return '<div class="row pt-10 text-center">
                        <div class="col-lg-12">
                            <h3>Leave Category Not Found !!</h3>
                            <p>Please configure the leave category.</p>
                        </div>
                    </div>';
        }

        $consume_info = DB::table('hr_leave_category as lc')
            ->join('hr_app_leaves as lv', function ($join) {
                $join->on('lc.id', '=', 'lv.leave_cat_id');
            })

            ->join('gnl_dynamic_form_value as dfv', function ($join) {
                $join->on('lc.leave_type_uid', '=', 'dfv.uid');
            })
            ->rightJoin('hr_employees as emp', function ($join) {
                $join->on('emp.id', '=', 'lv.emp_id');
            })
            ->when(true, function ($query) use ($request) {

                if (!empty($request->designation_id)) {
                    $query->where('emp.designation_id', $request->designation_id);
                }
                if (!empty($request->department_id)) {
                    $query->where('emp.department_id', $request->department_id);
                }
                if (!empty($request->emp_code)) {
                    $query->where('emp.emp_code', $request->emp_code);
                }
                if (!empty($request->leave_cat_id)) {
                    $query->where(function ($q1) use ($request) {
                        $q1->where('lv.leave_cat_id', '=', $request->leave_cat_id);
                        $q1->orWhere('lv.leave_cat_id', '=', null);
                    });
                }
                if (!empty($request->leave_type_id)) {
                    $query->where(function ($q1) use ($request) {
                        $q1->where('lc.leave_type_uid', '=', $request->leave_type_id);
                        $q1->orWhere('lc.leave_type_uid', '=', null);
                    });
                }

                $selBranchArr = Common::getBranchIdsForAllSection([
                    // 'companyId'     => $companyId,
                    // 'projectId'     => $projectId,
                    // 'projectTypeId' => $projectTypeId,
                    'branchId'      => $request->branch_id,
                    'zoneId'      => $request->zone_id,
                    // 'regionId'      => $regionId,
                    'areaId'      => $request->area_id,
                ]);
                $query->whereIn('emp.branch_id', $selBranchArr);
            })
            ->select('emp.*','lv.emp_id','lv.leave_code','lv.date_from as lv_date_from','lv.date_to as lv_date_to','emp.emp_name',
                'emp.emp_code','lc.id as lv_cat_id','lc.leave_type_uid as lv_type_uid','dfv.name as lv_type_name',
                'lc.short_form as lv_cat_short_form','lc.name as lv_cat_name','lv.is_active as lv_status',
                DB::raw(
                    '(CASE WHEN
                            lv.date_from >= "' . $flt_start_date . '"
                            AND
                            lv.date_to >= "' . $flt_start_date . '"
                            AND
                            lv.date_from <= "' . $flt_end_date . '"
                            AND
                            lv.date_from <= "' . $flt_end_date . '"
                        THEN "during"
                        WHEN
                            lv.date_from < "' . $flt_start_date . '"
                            AND
                            lv.date_to < "' . $flt_start_date . '"
                        THEN "opening"
                        END) AS period'
                ),
                DB::raw('SUM(DATEDIFF(lv.date_to, lv.date_from)+1) AS consumed')
            )
            ->whereRaw(
                '(lc.is_active = 1 OR lc.is_active IS NULL)
                    AND
                    (lc.is_delete = 0 OR lc.is_delete IS NULL)
                    AND
                    (lv.is_active <> 0 OR lv.is_active IS NULL)
                    AND
                    (emp.is_delete = 0 AND emp.is_active = 1)'
            )
            ->when(true, function ($query) use ($request) {
                if ($request->zero_balance == 2) {
                    $query->where('lv.id', '<>', null);
                }
            })
            ->groupBy('emp.id', 'lc.id', 'lv.is_active', 'period')
            ->havingRaw('lv.is_active = 1 OR lv.is_active = 3 OR lv.is_active IS NULL')
            ->orderBy('emp.id')
            ->get();

        $consume_info = $consume_info->groupBy([function ($item) {
            return $item->emp_name . ' [' . $item->emp_code . ']';
        }, 'period', 'lv_type_uid', 'lv_cat_id', 'lv_status']);

        //dd($consume_info);
        return view('HR.Reports.LeaveReports.ConsumeReports.consume_body', compact('leave_type', 'consume_info', 'leave_cat', 'format'));
    }

    
    public function __loadBalance(Request $request)
    {

        $format = '';
        if (empty($request->leave_type_id) && empty($request->leave_cat_id)) {
            $format = 'aa';
        } elseif (!empty($request->leave_type_id) && empty($request->leave_cat_id)) {
            $format = '1a';
        } elseif (empty($request->leave_type_id) && !empty($request->leave_cat_id)) {
            $format = 'a1';
        } elseif (!empty($request->leave_type_id) && !empty($request->leave_cat_id)) {
            $format = '11';
        }

        $fy = [];

        if ($request->search_by == 1) {
            $fy['fy_start_date'] = (new DateTime($request->start_date_fy))->format('Y-m-d');
            $fy['fy_end_date'] = (new DateTime($request->end_date_fy))->format('Y-m-d');
        } elseif ($request->search_by == 2) {
            $fy['fy_start_date'] = (new DateTime($request->start_date_cy))->format('Y-m-d');
            $fy['fy_end_date'] = (new DateTime($request->end_date_cy))->format('Y-m-d');
        } else {
            $f = DB::table('gnl_fiscal_year')->where('fy_start_date', '<=', date('Y-m-d'))->where('fy_end_date', '>=', date('Y-m-d'))->first();
            $fy['fy_start_date'] = $f->fy_start_date;
            $fy['fy_end_date'] = $f->fy_end_date;
        }

        $leave_type = DB::table('gnl_dynamic_form_value')->where([['type_id', 3], ['form_id', 1], ['uid', '<>', 2]])
            ->when(!empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('uid', $request->leave_type_id);
            })
            ->get();

        $leave_cat = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0], ['leave_type_uid', '<>', 2]])
            ->when(!empty($request->leave_cat_id) && empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('id', $request->leave_cat_id);
            })
            ->when(!empty($request->leave_cat_id) && !empty($request->leave_type_id), function ($query) use ($request) {
                $query->where('id', $request->leave_cat_id);
                $query->where('leave_type_uid', $request->leave_type_id);
            })
            ->get();

        if(count($leave_cat->toArray()) < 1){
            return '<div class="row pt-10 text-center">
                        <div class="col-lg-12">
                            <h3>Leave Category Not Found !!</h3>
                            <p>Please configure the leave category.</p>
                        </div>
                    </div>';
        }

        $balance_info = DB::table('hr_leave_category as lc')

            ->leftJoin('hr_app_leaves as lv', function ($join) {
                $join->on('lc.id', '=', 'lv.leave_cat_id');
            })

            ->rightJoin('hr_employees as emp', function ($join) {
                $join->on('emp.id', '=', 'lv.emp_id');
            })

            ->leftJoin('hr_emp_organization_details as eod', function ($join) {
                $join->on('emp.id', '=', 'eod.emp_id');
            })


            ->leftJoin('hr_leave_category_details as lcd', function ($join) {
                $join->on('lc.id', '=', 'lcd.leave_cat_id');
                $join->on('lcd.rec_type_id', '=', 'eod.rec_type_id');
            })


            /* ->leftjoin('gnl_dynamic_form_value as dfv', function($join){
                    $join->on('lc.leave_type_uid', '=', 'dfv.uid');
                }) */

            ->when(true, function ($query) use ($request) {

                if (!empty($request->designation_id)) {
                    $query->where('emp.designation_id', $request->designation_id);
                }
                if (!empty($request->department_id)) {
                    $query->where('emp.department_id', $request->department_id);
                }
                if (!empty($request->emp_code)) {
                    $query->where('emp.emp_code', $request->emp_code);
                }
                if (!empty($request->leave_cat_id)) {
                    $query->where(function ($q1) use ($request) {
                        $q1->where('lv.leave_cat_id', '=', $request->leave_cat_id);
                        $q1->orWhere('lv.leave_cat_id', '=', null);
                    });
                }
                if (!empty($request->leave_type_id)) {
                    $query->where(function ($q1) use ($request) {
                        $q1->where('lc.leave_type_uid', '=', $request->leave_type_id);
                        $q1->orWhere('lc.leave_type_uid', '=', null);
                    });
                }

                $selBranchArr = Common::getBranchIdsForAllSection([
                    // 'companyId'     => $companyId,
                    // 'projectId'     => $projectId,
                    // 'projectTypeId' => $projectTypeId,
                    'branchId'      => $request->branch_id,
                    'zoneId'      => $request->zone_id,
                    // 'regionId'      => $regionId,
                    'areaId'      => $request->area_id,
                ]);

                $query->whereIn('emp.branch_id', $selBranchArr);
            })

            ->whereRaw('(lcd.effective_date_from =
                            (SELECT effective_date_from FROM hr_leave_category_details
                                WHERE
                                    rec_type_id = lcd.rec_type_id
                                AND
                                    leave_cat_id = lcd.leave_cat_id
                                AND
                                    effective_date_from <= "' . $fy['fy_end_date'] . '"
                                ORDER BY
                                 effective_date_from DESC LIMIT 1
                            ) OR lcd.effective_date_from IS NULL)')

            ->where(function ($query) {
                $query->where([['lc.is_active', 1], ['lc.is_delete', 0], ['lv.is_active', '=', 1], ['leave_type_uid', '<>', 2]]);
                $query->orWhere([['lc.is_active', null], ['lc.is_delete', null], ['lc.leave_type_uid', null]]);
            })

            ->where('emp.is_delete', 0)
            ->where('emp.is_active', 1)

            ->where(function ($query) use ($fy) {
                $query->where([['lv.date_from', '>=', $fy['fy_start_date']], ['lv.date_to', '>=', $fy['fy_start_date']], ['lv.date_from', '<=', $fy['fy_end_date']], ['lv.date_to', '<=', $fy['fy_end_date']]]);
                $query->orWhere([['lv.date_from', null], ['lv.date_to', null]]);
            })

            ->select(
                'emp.id as employee_id',
                'emp.emp_name',
                'emp.emp_code',
                'emp.join_date',
                'emp.permanent_date',

                'lc.id as leave_cat_id',
                'lc.leave_type_uid',

                'lcd.id as leave_cat_details_id',
                'lcd.consume_policy',
                'lcd.allocated_leave',
                'lcd.eligibility_counting_from',
                'lcd.consume_after',

                'lc.name as lv_cat_name',
                'lcd.effective_date_from',
                'lv.date_to',
                'lv.date_from',

                DB::raw('SUM(DATEDIFF(lv.date_to, lv.date_from)+1) AS consume')
            )
            ->when(true, function ($query) use ($request) {
                if ($request->zero_balance == 2) {
                    $query->where('lv.id', '<>', null);
                }
            })
            ->groupBy('emp.id', 'lc.id')
            ->orderBy('emp.id')
            ->get();

        //  dd($balance_info);

        $balance_info = $balance_info->groupBy([function ($item) {
            return $item->emp_name . ' [' . $item->emp_code . ']';
        }, 'leave_type_uid', 'leave_cat_id']);

        foreach ($balance_info as $emp) {
            foreach ($emp as $lt) {
                foreach ($lt as $lc) {
                    foreach ($lc as $bi) {

                        //dd($bi);

                        if (isset($bi->employee_id) && isset($bi->leave_cat_id) && isset($bi->leave_cat_details_id)) {

                            $empl['id'] = $bi->employee_id;
                            $empl['join_date'] = $bi->join_date;
                            $empl['permanent_date'] = $bi->permanent_date;

                            $lv_cat['id'] = $bi->leave_cat_id;
                            $lv_cat['leave_type_uid'] = $bi->leave_type_uid;

                            $lv_cat_config['id'] = $bi->leave_cat_details_id;
                            $lv_cat_config['consume_policy'] = $bi->consume_policy;
                            $lv_cat_config['allocated_leave'] = $bi->allocated_leave;
                            $lv_cat_config['eligibility_counting_from'] = $bi->eligibility_counting_from;
                            $lv_cat_config['consume_after'] = $bi->consume_after;

                            $eligible = (new EmployeeLeaveController())->getEligibleLeaev((object)$empl, (object)$lv_cat, (object)$lv_cat_config, (object)$fy);
                            $bi->eligible = $eligible;
                        } else {
                            $bi->eligible = 0;
                        }
                    }
                }
            }
        }

        // dd($balance_info);

        return view('HR.Reports.LeaveReports.BalanceReports.balance_body', compact('leave_type', 'balance_info', 'leave_cat', 'format'));
    }
}
