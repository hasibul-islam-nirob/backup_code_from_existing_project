<?php
namespace App\Http\Controllers\HR\Reports;

use App\Http\Controllers\Controller;
use App\Model\HR\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;

class InOutReportController extends Controller{

    public function index(){
        return view('HR.Reports.InOutReport.index');
    }

    public function loadData(Request $request)
    {
        if ($request->month_year == '') return '';

        $zoneId   = (empty($request->zone_id)) ? null : $request->zone_id;
        $areaId   = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        $monthYear = (empty($request->month_year)) ? null : $request->month_year;
        

        $monthStartDate = (new DateTime($monthYear))->format('Y-m-01');
        $tempMonthEndDate = (new DateTime($monthYear))->modify('last day of this month');
        $monthEndDate = $tempMonthEndDate->format('Y-m-d');
        $tempDate = (new DateTime($monthYear));

        $countDays = (new DateTime($monthYear))->format('t');
        $month = (new DateTime($monthYear))->format('m');
        
        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId);
        $allData = Employee::from('hr_employees as emp')
                ->where([['emp.is_delete', 0],['emp.status', 1]])
                ->join('hr_attendance as atd', 'emp.id', 'atd.emp_id')
                ->when(true, function ($query) use ($request, $selBranchArr, $monthStartDate, $monthEndDate) {
                    if (!empty($selBranchArr)) {
                        $query->whereIn('emp.branch_id', $selBranchArr);
                    }
                    if (!empty($monthStartDate) && !empty($monthEndDate)) {
                        $query->where('atd.time_and_date', '>=' ,(new DateTime($monthStartDate))->format('Y-m-d H:i:s'))
                             ->where('atd.time_and_date', '<=' ,(new DateTime($monthEndDate))->format('Y-m-d H:i:s'));
                    }
                    if (!empty($designationId)) {
                        $query->where('emp.designation_id', $designationId);
                    }
                    if (!empty($departmentId)) {
                        $query->where('emp.department_id', $departmentId);
                    }
                })
                ->select(DB::raw('emp.id, emp.emp_name, emp.designation_id, emp.emp_code, emp.branch_id,
                    DATE(atd.time_and_date) AS date, TIME(atd.time_and_date) AS time'))
                ->orderBy('time_and_date','ASC')
                ->get();
                //Does not Exist Table hr_app_movements
        $movementData = DB::table('hr_app_movements')
                        ->where([['is_delete',0], ['is_active',1]])
                        ->whereIn('branch_id', $selBranchArr)
                        ->select('emp_id','reason','movement_date')
                        ->get();

        $leaveData = DB::table('hr_app_leaves')
                        ->where([['is_delete',0], ['is_active',1],])
                        ->select('emp_id','leave_date')
                        ->get();

        $branchData = DB::table('gnl_branchs')
                        ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                        ->whereIn('id', $selBranchArr)
                        ->selectRaw('CONCAT(branch_name, " [", branch_code, "]") AS branch_name, id')
                        ->pluck('branch_name', 'id')
                        ->toArray();

        $designationData = DB::table('hr_designations')
                        ->where([['is_delete', 0], ['is_active', 1]])
                        ->select('id','name')
                        ->pluck('name', 'id')
                        ->toArray();

        $personalDetailsData = DB::table('hr_emp_personal_details')
                        ->select('emp_id','mobile_no')
                        ->pluck('mobile_no', 'emp_id')
                        ->toArray();

        $holidays = HRS::systemHolidays( null, $branchId, null, $monthStartDate, $monthEndDate);


        $extendedTime = DB::table('hr_attendance_rules')->select('ext_start_time')->first();
        
        if($extendedTime) {
            $extendedTime = $extendedTime->ext_start_time;
        }
        
        $dataSet = array();
        $sl = 0;
        
        $empCode = $allData->pluck('emp_code','id')->unique();
        $tempDate = $monthStartDate;

        $monthDates = [$monthStartDate];
        $tempDate = new DateTime($monthStartDate);
        for($i = 1; $i < $countDays; $i++){
            $tempDate = (($tempDate))->modify('+1 day');
            $date = clone $tempDate;
            $date = $date->format('Y-m-d');
            $monthDates[$i] = $date;
        }

        foreach($empCode as $key=>$emp_code){
            
            $empData = $allData->where('emp_code',$emp_code);
            
            $employeeId = $empData->pluck('id')->first();
            $employeeName = $empData->pluck('emp_name')->first();
            $employeeCode = $empData->pluck('emp_code')->first();
            $branchId = $empData->pluck('branch_id')->first();
            $designationId = $empData->pluck('designation_id')->first();

            $tempSet = [
                'sl' => ++$sl,
                'emp_name' => $employeeName . '<br>[' . $employeeCode . ']' . '<br>Mobile:'. $personalDetailsData[$employeeId],
                'branch' => $branchData[$branchId], 
                'designation' => $designationData[$designationId],
            ];

            foreach($monthDates as $date){

                ## If it is holiday
                if(in_array($date, $holidays)){
                    $in_time = '';
                    $out_time = '';
                }
                else {
                    $singleDayData = $empData->where('date', (new DateTime($date))->format('Y-m-d'));

                    $in_time = $singleDayData->pluck('time')->min();
                    $out_time = $singleDayData->pluck('time')->max();

                }
                $tempSet[$date]['in'] = $in_time;
                $tempSet[$date]['out'] = $out_time;
            }
            
            $dataSet[] = $tempSet;
        }

        $data = array();

        return view('HR.Reports.InOutReport.report_body', compact('dataSet','countDays','monthStartDate','monthDates','holidays'));
    }
}