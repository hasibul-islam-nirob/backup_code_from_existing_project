<?php

namespace App\Http\Controllers\HR\Holiday;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\HR\GovtHoliday;
use Redirect;
use App\Services\HrService;

class CalendarController extends Controller
{

    public function __construct()
    {
        // $this->middleware(['auth', 'permission']);
        parent::__construct();
    }

    // List of Calendar
    public function index(Request $request)
    {
        // if($request->ajax()) {  
        //     $govtHData =DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
        //         ->select('efft_start_date', 'efft_end_date','gh_title')
        //         ->get();
        //     return response()->json($govtHData);
        // }
        
        if($request->ajax()){
            // dd($request->year);
            $holidays = HrService::systemHolidays($companyID = null, $branchID = null, $samityID = null, $startDate = $request->year.'-01-01', $endDate = $request->year.'-12-31', $period = null);
            $holidayDataSource = [];
            foreach ($holidays as $key => $holiday) {
                $holidayObj = new \stdClass();
                $holidayObj->id = $key;
                $holidayObj->name = 'Holiday';
                $holidayObj->location = 'Bangladesh';
                $holidayObj->startDate = $holiday;
                $holidayObj->endDate = $holiday;
                $holidayObj->color = 'red';
                array_push($holidayDataSource, $holidayObj);
            }
            
            $holidays = $this->getHolidayList($startDate = $request->year.'-01-01', $endDate = $request->year.'-12-31');
    
            $data = array(
                'holidayDataSource' => $holidays,
                // 'holidayDataSource' => $holidayDataSource,
            );

            return response()->json($data);
        }
        else{
            return view('HR.Holiday.HolidayCal.index');
        }
    }

    public function getHolidayList($fromDate, $toDate)
    {
        $govtHolidayModel    = 'App\\Model\\GNL\\GovtHoliday';
        $comapnyHolidayModel = 'App\\Model\\GNL\\CompanyHoliday';
        $specialHolidayModel = 'App\\Model\\GNL\\SpecialHoliday';
        $rescheduleHolidayModel = 'App\\Model\\HR\\RescheduleHoliday';

        $holiDays = array();

        // Fixed Govt Holiday Query
        $govtHolidays = $govtHolidayModel::where([['is_delete', 0], ['is_active', 1]])
            ->select('id', 'gh_title', 'gh_date', 'gh_description')
            ->get();
        $holidayNo=0;
        $year = strtotime($fromDate);
        $year = date("Y", $year);
        
        foreach($govtHolidays as $govHoliday){
            $date =  strtotime($govHoliday->gh_date.'-'.$year);
            $date =  date('Y-m-d', $date);

            $holidayObj = new \stdClass();
            $holidayObj->id = $holidayNo;
            $holidayObj->name = 'Holiday';
            $holidayObj->location = $govHoliday->gh_title;
            $holidayObj->startDate = $date;
            $holidayObj->endDate = $date;
            $holidayObj->color = 'red';

            $holidayNo++;
            array_push($holiDays, $holidayObj);
        }

        // Special Holiday for Organization Query
        $specialHolidayORGQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
            ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
            ->get();

        foreach($specialHolidayORGQuery as $spHoliday){
            $holidayObj = new \stdClass();
            $holidayObj->id = $holidayNo;
            $holidayObj->name = "Holiday";
            $holidayObj->location = $spHoliday->sh_title;
            $holidayObj->startDate = date('Y-m-d',strtotime($spHoliday->sh_date_from));
            $holidayObj->endDate = date('Y-m-d',strtotime($spHoliday->sh_date_to));
            $holidayObj->color = 'blue';

            $holidayNo++;
            array_push($holiDays, $holidayObj);
        }

        
        // Company Holiday Query
        $companyHoliday = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
            ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
            ->get();
        
        // if($companyHoliday->count() > 0){
        //     $companyHoliday= $companyHoliday->first();
        //     $days = explode(",",$companyHoliday->ch_day);

        //     foreach($days as $day){
        //         $start_date = date('Y-m-d', strtotime('-1 day', strtotime($fromDate)));
        //         $start_date=strtotime($start_date);
        //         $end_date=strtotime($toDate);
        //         while(1){
        //             $start_date=strtotime('next '.$day, $start_date);
                    
        //             if($start_date>$end_date)
        //                 break;
                    
        //             $holidayObj = new \stdClass();
        //             $holidayObj->id = $holidayNo;
        //             $holidayObj->name = "Weekly holiday";
        //             $holidayObj->location = "";
        //             $holidayObj->startDate = date('Y-m-d',$start_date);
        //             $holidayObj->endDate = date('Y-m-d',$start_date);
        //             $holidayObj->color = 'skyblue';
        
        //             $holidayNo++;
        //             array_push($holiDays, $holidayObj);
                
        //         }
        //     }
        // }

        $allow = false;
        $tempLoopDate = $fromDate;
        $startingPoint= $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])->where('ch_eff_date','<', $fromDate)->orderBy('ch_eff_date', 'desc');
        if($startingPoint->count() > 0){
            $days = explode(",",$startingPoint->first()->ch_day);
            $allow = true;
        }
        else if($companyHoliday->count() > 0){
            $days = explode(",",$companyHoliday->first()->ch_day);
            $allow = false; //if value does not exist in starting of year
            //then do not consider until effective date arrives
        }
            
        while ($tempLoopDate <= $toDate) {
            $check = $companyHoliday->where('ch_eff_date',$tempLoopDate);
            if($check->count() > 0){
                $days = explode(",",$check->first()->ch_day);
                $allow = true;
            }
            $weekday = date('l', strtotime($tempLoopDate));
            // dd($weekday, $tempLoopDate);
            if(in_array($weekday, $days) && $allow){
                
                $holidayObj = new \stdClass();
                $holidayObj->id = $holidayNo;
                $holidayObj->name = "Weekly holiday";
                $holidayObj->location = "";
                $holidayObj->startDate = date('Y-m-d',strtotime($tempLoopDate));
                $holidayObj->endDate = date('Y-m-d',strtotime($tempLoopDate));
                $holidayObj->color = 'skyblue';
    
                $holidayNo++;
                array_push($holiDays, $holidayObj);
            }
            $tempLoopDate = date('Y-m-d', strtotime('+1 day', strtotime($tempLoopDate)));
        }


        // Reschedule Holiday 
        $rescheduleHolidays = $rescheduleHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                            ->select('id', 'title as gh_title', 'working_date as gh_date', 'description as gh_description')
                            ->get();
        foreach($rescheduleHolidays as $rescheduleHoliday){
            $holidayObj = new \stdClass();
            $holidayObj->id = $holidayNo;
            $holidayObj->name = 'Reschedule Holiday';
            $holidayObj->location = $rescheduleHoliday->gh_title;
            $holidayObj->startDate = $rescheduleHoliday->gh_date;
            $holidayObj->endDate = $rescheduleHoliday->gh_date;
            $holidayObj->color = '#b36b00';

            $holidayNo++;
            array_push($holiDays, $holidayObj);
        }
        // dd($rescheduleHoliday, $holiDays);

        $today = new \stdClass(); 
        $today->id = $holidayNo;
        $today->name = 'Today';
        $today->location = '';
        $today->startDate = (new DateTime())->format('Y-m-d');
        $today->endDate = (new DateTime())->format('Y-m-d');
        $today->color = 'green';
        array_push($holiDays, $today);

        
        return $holiDays;
    }
}
