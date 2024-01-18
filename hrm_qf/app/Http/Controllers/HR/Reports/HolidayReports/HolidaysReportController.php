<?php

namespace App\Http\Controllers\HR\Reports\HolidayReports;

use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Http\Request;
use App\Model\HR\EmployeeLeave;
use App\Http\Controllers\Controller;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;

class HolidaysReportController extends Controller
{
    public function getHoliday(){
        return view('HR.Reports.HolidayReports.show');
    }


    public function loadHolidays(Request $request){

        // if ($request->startDate == '') return '';
        // if ($request->startDate == '') return '';

        $companyId = (empty($request->company_id)) ? null : $request->company_id;
        $zoneId = (empty($request->zone_id)) ? null : $request->zone_id;
        $regionId = (empty($request->region_id)) ? null : $request->region_id;
        $areaId = (empty($request->area_id)) ? null : $request->area_id;
        $branchId = (empty($request->branch_id)) ? null : $request->branch_id;
        // $designationId = (empty($request->designation_id)) ? null : $request->designation_id;
        // $departmentId = (empty($request->department_id)) ? null : $request->department_id;
        // $employeeId = (empty($request->employee_id)) ? null : $request->employee_id;

        $startDate = (empty($request->startDate)) ? null : (new DateTime($request->startDate));
        $endDate = (empty($request->endDate)) ? null : (new DateTime($request->endDate));
        $monthStartDate = ($startDate)->format('Y-m-d');
        $monthEndDate = ($endDate)->format('Y-m-d');

        $shortStartDate = ($startDate)->format('d-m');
        $shortEndDate = ($endDate)->format('d-m');

        ## Date And Day Calculation Start
        $monthDates = array();
        $tempDate = clone $startDate;

        $monthDates[$tempDate->format('Y-m-d')] = $tempDate->format('D');
        while( $tempDate <= $endDate){
            $date = $tempDate->format('Y-m-d');
            $day = $tempDate->format('d-m');
            $monthDates[$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
            $tempDate++;
        }
        ## Date And Day Calculation End


        // Fixed Govt Holiday Query
        $govtHolidays = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
        ->where(function($query) use ($companyId){
            if(!empty($companyId)){
                $query->where('company_id', $companyId);
            }
        })
        ->select('id', 'gh_title', 'gh_date')
        ->get();
        $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();
        // ss($govtHolidays);
        // Fixed Govt Holiday Query


        // Special Holiday for Organization Query
        $specialHolidayORGQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
        ->where(function($query) use ($companyId, $branchId){
            if(!empty($companyId)){
                $query->where('company_id', $companyId);
            }
            if(!empty($branchId)){
                $query->where('branch_id', $branchId);
            }
        })
        ->orWhere(function($query) use ($monthStartDate, $monthEndDate){
           if(!empty($monthStartDate)){
                $query->where('sh_date_from', '>=' ,$monthStartDate);
            }
            if(!empty($monthEndDate)){
                $query->where('sh_date_to', '<=' , $monthEndDate);
            }
        })
        ->select('id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
        ->get();

        $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();
        // Special Holiday for Organization Query


        $systemHolidays = HRS::systemHolidays($companyId, $branchId, null, $monthStartDate, $monthEndDate);
 

        $holidayDataSet = array();
        foreach($monthDates as $key => $value){

            $holidayDataSet[$key] = array();

            if(count($fixedGovtHoliday) > 0){
                for($i = 0; $i < count($fixedGovtHoliday); $i++){

                    if($value == $fixedGovtHoliday[$i]->gh_date){
                        $holidayDataSet[$key]['title'] = $fixedGovtHoliday[$i]->gh_title;
                        $holidayDataSet[$key]['from_date'] = $key;
                        $holidayDataSet[$key]['to_date'] = $key;
                        $holidayDataSet[$key]['status'] = "Govt. Holiday";
                    }
                }
            }

            if(count($sHolidaysORG) > 0){
                for($i = 0; $i < count($sHolidaysORG); $i++){

                    if($key == $sHolidaysORG[$i]->sh_date_from){
                        $holidayDataSet[$key]['title'] = $sHolidaysORG[$i]->sh_title;
                        $holidayDataSet[$key]['from_date'] = $sHolidaysORG[$i]->sh_date_from;
                        $holidayDataSet[$key]['to_date'] = $sHolidaysORG[$i]->sh_date_to;
                        $holidayDataSet[$key]['status'] = "Special Holiday";
                    }
                }
            }

            // if( in_array($key, $systemHolidays) ){
            //     $holidayDataSet[$key]['title'] = "Company Holiday";
            //     $holidayDataSet[$key]['from_date'] = $key;
            //     $holidayDataSet[$key]['to_date'] = $key;
            // }

        }

        // dd($holidayDataSet);
        $filteredHoliday = array_filter($holidayDataSet);



        return view('HR.Reports.HolidayReports.body', compact('filteredHoliday'));
    }


}
