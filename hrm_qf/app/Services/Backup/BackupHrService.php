<?php

namespace App\Services;

use App\Services\CommonService as Common;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackupHrService
{
    /**
     * This function returns an array having branch ids which the logedin user have access according to HR policy.
     *
     * @return array
     */
    public static function getUserAccesableBranchIds()
    {
        // $userBranchId = \Common::getBranchId();
        // $branchIds = [];
        // if ($userBranchId == 1) {
        //     $branchIds = DB::table('gnl_branchs')
        //     ->where([
        //         ['is_delete', 0],
        //         ['is_approve', 1],
        //         ['is_active', 1]
        //     ])
        //     ->pluck('id')
        //     ->toArray();
        // }
        // else{
        //     $branchIds = [$userBranchId];
        // }

        // return $branchIds;

        $branchIds = [];

        if (Auth::user()->branch_id == 1) {

            $branchIds = DB::table('gnl_branchs')
                ->where([
                    ['is_delete', 0],
                    ['is_approve', 1],
                    ['is_active', 1],
                ])
                ->pluck('id')
                ->toArray();

        } else {

            $userInfo = DB::table('gnl_sys_users as gsu')
                ->where([
                    ['gsu.id', Auth::user()->id],
                    ['gsu.branch_id', Auth::user()->branch_id],
                    ['gsu.is_delete', 0],
                    ['gsu.is_active', 1],
                ])
                ->leftjoin('hr_employees as he', function ($query) {
                    if (Common::getDBConnection() == "sqlite") {
                        $query->on('he.employee_no', 'gsu.employee_no')
                            ->where([
                                ['he.is_delete', 0],
                                ['he.is_active', 1],
                            ]);
                    } else {
                        $query->on('he.id', 'gsu.emp_id')
                            ->where([
                                ['he.is_delete', 0],
                                ['he.is_active', 1],
                            ]);

                    }

                })
                ->leftjoin('hr_designations as hd', function ($query) {
                    $query->on('hd.id', 'he.designation_id')
                        ->where([
                            ['hd.is_delete', 0],
                            ['hd.is_active', 1],
                        ]);
                })
                ->select('he.id', 'gsu.branch_id as branchId', 'hd.name as designation')
                ->first();

            if ($userInfo->designation == "Zonal Manager") {

                $branchIds = explode(',', DB::table('gnl_zones')
                        ->where('branch_arr', 'like', '%' . Auth::user()->branch_id . '%')
                        ->select('branch_arr')
                        ->first()->branch_arr);

            } elseif ($userInfo->designation == "Area Manager") {

                $branchIds = explode(',', DB::table('gnl_areas')
                        ->where('branch_arr', 'like', '%' . Auth::user()->branch_id . '%')
                        ->select('branch_arr')
                        ->first()->branch_arr);

            } else {

                $branchIds = [Auth::user()->branch_id];
            }
        }

        //This condition provide for safety
        //Becasue if somehow branchIds getting empty
        if (empty($branchIds)) {
            $branchIds = [Auth::user()->branch_id];
        }

        return $branchIds;
    }

    /**
     * Get system Holidays
     * @param companyID @type int
     * @param branchID @type int
     * @param somityID @type int
     * @param startDate @type string '02-02-2020' or '2020-02-02'
     * @param endDate @type string '02-02-2020' or '2020-02-02'
     * @param period @type string '2 day' or '2 month' or '2 year'
     *
     * @condition
     * startDate != null && endDate != null && period == null
     * startDate != null && endDate == null && period != null  (Auto calculate last day depend on period(+))
     * startDate == null && endDate != null && period != null (Auto calculate first day depend on period(-))
     * startDate == null && endDate == null && period != null (Get System Current date as first day & last day calculate depend on period(+))
     * Calling: Common::systemHolidays(companyID,branchID,somityID,startDate,endDate, period)
     */

    public static function systemHolidays($companyID = null, $branchID = null, $samityID = null, $startDate = null, $endDate = null, $period = null)
    {
        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchID  = (!empty($branchID)) ? $branchID : Common::getBranchId();
        $samityID  = (!empty($samityID)) ? $samityID : 1;

        $companyID = (!empty($companyID)) ? $companyID : 1;

        $govtHolidayModel    = 'App\\Model\\GNL\\GovtHoliday';
        $comapnyHolidayModel = 'App\\Model\\GNL\\CompanyHoliday';
        $specialHolidayModel = 'App\\Model\\GNL\\SpecialHoliday';

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if ($period == '') {
            $period = null;
        }
        
        if (!empty($startDate) && !empty($endDate)) {
            $fromDate = new DateTime($startDate);
            $toDate   = new DateTime($endDate);
        } elseif (!empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime($startDate);
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && !empty($endDate) && !empty($period)) {
            $toDate   = new DateTime($endDate);
            $tempDate = clone $toDate;
            $fromDate = $tempDate->modify('-' . $period);
        } elseif (empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime(Common::systemCurrentDate());
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        }

        $holiDays = array();

        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $govtHolidays = $govtHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            // $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            // $companyHolidayQuery = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
            //     ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
            //     ->where([$companyArr])
            //     ->get();
            // $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Company Holiday Query
            $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            $companyHolidayQuery = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
                ->where([$companyArr])
                ->where('ch_eff_date', '<=', $fromDate->format('Y-m-d'))
                ->orderBy('ch_eff_date','DESC')
                ->first();
            $companyHolidays = (count(array($companyHolidayQuery)) > 0) ? array($companyHolidayQuery) : array();

            // Special Holiday for Organization Query
            $specialHolidayORGQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            // Special Holiday for Samity Query
            $specialHolidaySQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'samity']])
                ->where('samity_id', '=', $samityID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysSamity = (count($specialHolidaySQuery->toarray()) > 0) ? $specialHolidaySQuery->toarray() : array();

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $holiDayFlag = false;

                // Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $holiDayFlag = true;
                    }
                }

                // Company Holiday Check
                if ($holiDayFlag == false) {
                    foreach ($companyHolidays as $RowC) {
                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        // This is Full day name
                        $dayName = $tempLoopDate->format('l');

                        if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                // Special Holiday Org check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysORG as $RowO) {
                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                // Special Holiday Branch check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysBr as $RowB) {
                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                // Special Holiday Samity check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysSamity as $RowB) {
                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                if ($holiDayFlag == true) {
                    array_push($holiDays, $tempLoopDate->format('Y-m-d'));
                }
                $tempLoopDate = $tempLoopDate->modify('+1 day');
            }
        }
        return $holiDays;
    }

    /**
     * Get system Month Working Days
     * @param companyID @type int
     * @param branchID @type int
     * @param somityID @type int
     * @param startDate @type string '02-02-2020' or '2020-02-02'
     * @param endDate @type string '02-02-2020' or '2020-02-02'
     * @param period @type string '2 day' or '2 month' or '2 year'
     *
     * @Condition
     * startDate != null && endDate != null && period == null
     * startDate != null && endDate == null && period == null (Auto Calculate last day of month)
     * startDate != null && endDate == null && period != null  (Auto calculate last day depend on period(+))
     * startDate == null && endDate != null && period == null  (Auto calculate first day of month is 01)
     * startDate == null && endDate != null && period != null (Auto calculate first day depend on period(-))
     * startDate == null && endDate == null && period != null (Get System Current date as first day & last day calculate depend on period(+))
     * startDate == null && endDate == null && period == null (Get System Current date as first day & last day calculate depend on month)
     *
     * Calling: Common::systemMonthWorkingDay(companyID,branchID,somityID,startDate,endDate, period)
     */

    public static function systemMonthWorkingDay($companyID = null, $branchID = null, $somityID = null, $startDate = null, $endDate = null, $period = null)
    {
        $companyID = (!empty($companyID)) ? $companyID : Common::getBranchId();
        $branchID  = (!empty($branchID)) ? $branchID : Common::getBranchId();
        $somityID  = (!empty($somityID)) ? $somityID : 1;

        $companyID = (!empty($companyID)) ? $companyID : 1;

        $govtHolidayModel    = 'App\\Model\\GNL\\HR\\GovtHoliday';
        $comapnyHolidayModel = 'App\\Model\\GNL\\HR\\CompanyHoliday';
        $specialHolidayModel = 'App\\Model\\GNL\\HR\\SpecialHoliday';

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if ($period == '') {
            $period = null;
        }

        if (!empty($startDate) && !empty($endDate)) {
            $fromDate = new DateTime($startDate);
            $toDate   = new DateTime($endDate);
        } elseif (!empty($startDate) && empty($endDate) && empty($period)) {
            $fromDate = new DateTime($startDate);
            // Count day of curent month
            $lastday = cal_days_in_month(CAL_GREGORIAN, $fromDate->format('m'), $fromDate->format('Y'));
            $toDate  = new DateTime($lastday . "-" . $fromDate->format('m') . "-" . $fromDate->format('Y'));
        } elseif (!empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime($startDate);
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && !empty($endDate) && empty($period)) {
            $fromDate = new DateTime("01-" . $startDate->format('m') . "-" . $endDate->format('Y'));
            $toDate   = new DateTime($endDate);
        } elseif (empty($startDate) && !empty($endDate) && !empty($period)) {
            $toDate   = new DateTime($endDate);
            $tempDate = clone $toDate;
            $fromDate = $tempDate->modify('-' . $period);
        } elseif (empty($startDate) && empty($endDate) && !empty($period)) {
            $fromDate = new DateTime(Common::systemCurrentDate());
            $tempDate = clone $fromDate;
            $toDate   = $tempDate->modify('+' . $period);
        } elseif (empty($startDate) && empty($endDate) && empty($period)) {
            $fromDate = new DateTime(Common::systemCurrentDate());
            // Count day of curent month
            $lastday = cal_days_in_month(CAL_GREGORIAN, $fromDate->format('m'), $fromDate->format('Y'));
            $toDate  = new DateTime($lastday . "-" . $fromDate->format('m') . "-" . $fromDate->format('Y'));
        }

        $workingDays = array();

        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $govtHolidays = $govtHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            // $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            // $companyHolidayQuery = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
            //     ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
            //     ->where([$companyArr])
            //     ->get();

            // $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();


            ## New Company Holiday Query
            $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            $companyHolidayQuery = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
                ->where([$companyArr])
                ->where('ch_eff_date', '<=', $fromDate->format('Y-m-d'))
                ->orderBy('ch_eff_date', 'DESC')
                ->first();

            $companyHolidays = (count(array($companyHolidayQuery)) > 0) ? array($companyHolidayQuery) : array();


            // Special Holiday for Organization Query
            $specialHolidayORGQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $workdayFlag = true;

                // Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $workdayFlag = false;
                    }
                }

                // Company Holiday Check
                if ($workdayFlag == true) {
                    foreach ($companyHolidays as $RowC) {
                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        // This is Full day name
                        $dayName = $tempLoopDate->format('l');

                        if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                // Special Holiday Org check
                if ($workdayFlag == true) {
                    foreach ($sHolidaysORG as $RowO) {
                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                // Special Holiday Branch check
                if ($workdayFlag == true) {
                    foreach ($sHolidaysBr as $RowB) {
                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                if ($workdayFlag == true) {
                    array_push($workingDays, $tempLoopDate->format('Y-m-d'));

                }
                $tempLoopDate = $tempLoopDate->modify('+1 day');
            }
        }
        return $workingDays;
    }

    /**
     * Get Next Working Date in System
     */
    public static function systemNextWorkingDay($currentDate = null, $branchID = null, $companyID = null, $samityID = null)
    {
        // $branchID = self::getBranchId();
        // $companyID = self::getCompanyId();
        // dd('test ');

        // $samityID  = (!empty($samityID)) ? $samityID : 1;

        $GovtHolidayModel    = 'App\\Model\\GNL\\HR\\GovtHoliday';
        $ComapnyHolidayModel = 'App\\Model\\GNL\\HR\\CompanyHoliday';
        $SpecialHolidayModel = 'App\\Model\\GNL\\HR\\SpecialHoliday';

        $TempCurrentDate = new DateTime($currentDate);
        $TempNextDate    = $TempCurrentDate->modify('+1 day');

        $HolidayFlag = true;

        while ($HolidayFlag == true) {
            $HolidayFlag = false;

            $TempNext = $TempNextDate->format('d-m');
            // This is for Half Day Name
            // $DayName = strtolower($TempNextDate->format('D'));

            // This is Full day name
            $DayName = $TempNextDate->format('l');

            $TempNextD = $TempNextDate->format('Y-m-d');
            // dd($TempNextD);
            $GovtHoliday = $GovtHolidayModel::where(['gh_date' => $TempNext, 'is_delete' => 0])->count();

            if ($GovtHoliday > 0) {
                $HolidayFlag = true;
            } else {
                $CompanyArr = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];

                $CompanyHolidayID = DB::table('hr_holidays_comp')->where(['is_delete' => 0])
                    ->where('ch_eff_date', '<=', $TempNextD)
                    ->where([$CompanyArr])
                    ->orderBy('ch_eff_date', 'DESC')
                    ->first();
                $CompanyHoliday = null;
                if(!empty($CompanyHolidayID)){
                    $CompanyHoliday = DB::table('hr_holidays_comp')->where('id', $CompanyHolidayID->id)
                                        ->where(function ($CompanyHoliday) use ($DayName) {
                                            $CompanyHoliday->where('ch_day', 'LIKE', "{$DayName}")
                                                ->orWhere('ch_day', 'LIKE', "%,{$DayName},%")
                                                ->orWhere('ch_day', 'LIKE', "%,{$DayName}%")
                                                ->orWhere('ch_day', 'LIKE', "%{$DayName},%");
                                        })
                                ->first();

                }

                     ## previous code heeree backup for  for doubt why gose in above query  
                    // $CompanyHoliday = $ComapnyHolidayModel::where(['is_delete' => 0])
                    // ->where('ch_eff_date', '<=', $TempNextD)
                    // ->where([$CompanyArr])
                    // ->where(function ($CompanyHoliday) use ($DayName) {
                    //     $CompanyHoliday->where('ch_day', 'LIKE', "{$DayName}")
                    //         ->orWhere('ch_day', 'LIKE', "%,{$DayName},%")
                    //         ->orWhere('ch_day', 'LIKE', "%,{$DayName}%")
                    //         ->orWhere('ch_day', 'LIKE', "%{$DayName},%");
                    // })
                    // ->count();
                    // if ($CompanyHoliday > 0) {
                

                if (!empty($CompanyHoliday)) {
                    $HolidayFlag = true;
                } else {
                    $SpecialHolidayORG = $SpecialHolidayModel::where(['sh_app_for' => 'org', 'is_delete' => 0])
                        ->where('sh_date_from', '<=', $TempNextD)
                        ->where('sh_date_to', '>=', $TempNextD)
                        ->count();

                    if ($SpecialHolidayORG > 0) {
                        $HolidayFlag = true;
                    } else {
                        $SpecialHolidayBranch = $SpecialHolidayModel::where(['sh_app_for' => 'branch', 'is_delete' => 0])
                            ->where('branch_id', '=', $branchID)
                            ->where('sh_date_from', '<=', $TempNextD)
                            ->where('sh_date_to', '>=', $TempNextD)
                            ->count();

                        if ($SpecialHolidayBranch > 0) {
                            $HolidayFlag = true;
                        }

                        if(!empty($samityID)){
                            $SpecialHolidaySamity = $SpecialHolidayModel::where(['sh_app_for' => 'samity', 'is_delete' => 0])
                                ->where('samity_id', '=', $branchID)
                                ->where('sh_date_from', '<=', $TempNextD)
                                ->where('sh_date_to', '>=', $TempNextD)
                                ->count();

                            if ($SpecialHolidaySamity > 0) {
                                $HolidayFlag = true;
                            }
                        }
                        
                    }
                }
            }

            if ($HolidayFlag == true) {
                $TempNextDate = $TempNextDate->modify('+1 day');
            }
        }

        $currentDate = $TempNextDate->format('Y-m-d');

        return $currentDate;
    }

    public static function systemWorkingDay($checkFor, $parameter = [])
    {
        ## $checkFor = branch || somity
        // $period = null
        ## startDate == null then branch opening date
        ## startDate == not null but less then branch open date then branch opening date
        ## endDate == null then branch current date

        ## period = null or "-2 day" or '-2 month' or '-2 year" or "+1 day" or '+1 month' or '+1 year"

        $workingDayData = array();

        $startDate       = (isset($parameter['startDate'])) ? $parameter['startDate'] : null;
        $endDate         = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;
        $companyId       = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $branchId        = (isset($parameter['branchId'])) ? $parameter['branchId'] : array();
        $somityId        = (isset($parameter['somityId'])) ? $parameter['somityId'] : null;
        $moduleShortName = (isset($parameter['moduleShortName'])) ? $parameter['moduleShortName'] : null;
        $period          = (isset($parameter['period'])) ? $parameter['period'] : null;

        $selBranchArr = array();
        $selSomityArr = array();

        if ($checkFor == 'branch') {
            $branch_var_type = gettype($branchId);

            if ($branch_var_type == 'array') {
                if (count($branchId) > 0) {
                    $selBranchArr = $branchId;
                } else {
                    $selBranchArr = Common::fnForBranchZoneAreaWise();
                }
            } else {
                $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);
            }

        } elseif ($checkFor == 'somity') {
            $selSomityArr = array();
        }

        if (count($selBranchArr) > 0 || count($selSomityArr) > 0) {
            ## Fixed Govt Holiday Query
            $govtHolidays = DB::table('hr_holidays_govt')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date', 'efft_start_date', 'efft_end_date')
                ->get();

            $fixedGovtHoliday = $govtHolidays;

            ## Company Holiday Query
            $companyHolidayQuery = DB::table('hr_holidays_comp')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($companyHolidayQuery) use ($companyId) {
                    if (!empty($companyId)) {
                        $companyHolidayQuery->where('company_id', $companyId);
                    }
                })
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
                ->get();

            $companyHolidays = $companyHolidayQuery;

            ## Special Holiday for Organization Query
            $specialHolidayORGQuery = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = $specialHolidayORGQuery;

            ## Special Holiday for Branch Query
            $specialHolidayBrQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where(function ($specialHolidayBrQuery) use ($selBranchArr, $selSomityArr) {
                    if (count($selBranchArr) > 0) {
                        $specialHolidayBrQuery->whereIn('branch_id', $selBranchArr);
                    }

                    if (count($selSomityArr) > 0) {
                        $specialHolidayBrQuery->whereIn('samity_id', $selSomityArr);
                    }
                })
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = $specialHolidayBrQuery;
        }

        if (!empty($startDate)) {
            $startDate = new DateTime($startDate);
        }

        if (!empty($endDate)) {
            $endDate = new DateTime($endDate);
        }

        foreach ($selBranchArr as $rowBranch) {

            if (empty($endDate)) {
                $toDate = new DateTime(Common::systemCurrentDate($rowBranch, $moduleShortName));
            } else {
                $toDate = $endDate;
            }

            if (empty($period)) {
                $branchOpenDate = new DateTime(Common::getBranchSoftwareStartDate($rowBranch, $moduleShortName));

                if (empty($startDate)) {
                    $fromDate = $branchOpenDate;
                } elseif ($startDate <= $branchOpenDate) {
                    $fromDate = $branchOpenDate;
                } else {
                    $fromDate = $startDate;
                }
            } else {
                $tempDate = clone $toDate;
                $fromDate = $tempDate->modify($period);
            }

            ///////////////////////////
            $tempWorkDay   = array();
            $tempWorkMonth = array();

            if (!empty($fromDate) && !empty($toDate)) {

                $tempLoopDate = clone $fromDate;
                while ($tempLoopDate <= $toDate) {
                    $workdayFlag = true;
                    ## Fixed Govt Holiday Check
                    foreach ($fixedGovtHoliday as $RowFG) {
                        $RowFG = (array) $RowFG;

                        if ($workdayFlag == true) {
                            if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {

                                if (empty($RowFG['efft_start_date']) && empty($RowFG['efft_end_date'])) {
                                    $workdayFlag = false;
                                } elseif (!empty($RowFG['efft_start_date'])
                                    && ($RowFG['efft_start_date'] <= $tempLoopDate->format('Y-m-d'))
                                    && !empty($RowFG['efft_end_date'])
                                    && ($RowFG['efft_end_date'] >= $tempLoopDate->format('Y-m-d'))) {

                                    $workdayFlag = false;
                                } elseif (!empty($RowFG['efft_start_date'])
                                    && ($RowFG['efft_start_date'] <= $tempLoopDate->format('Y-m-d'))
                                    && empty($RowFG['efft_end_date'])) {

                                    $workdayFlag = false;
                                } elseif (empty($RowFG['efft_start_date'])
                                    && !empty($RowFG['efft_end_date'])
                                    && ($RowFG['efft_end_date'] >= $tempLoopDate->format('Y-m-d'))) {

                                    $workdayFlag = false;
                                }
                            }
                        } else {
                            continue;
                        }
                    }

                    ## Company Holiday Check
                    if ($workdayFlag == true) {
                        foreach ($companyHolidays as $RowC) {
                            $RowC = (array) $RowC;

                            if ($workdayFlag == true) {
                                $ch_day = $RowC['ch_day'];

                                $ch_day_arr  = explode(',', $RowC['ch_day']);
                                $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                                // This is Full day name
                                $dayName = $tempLoopDate->format('l');

                                if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                                    $workdayFlag = false;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                    ## Special Holiday Org check
                    if ($workdayFlag == true) {
                        foreach ($sHolidaysORG as $RowO) {
                            $RowO = (array) $RowO;

                            if ($workdayFlag == true) {
                                $sh_date_from = new DateTime($RowO['sh_date_from']);
                                $sh_date_to   = new DateTime($RowO['sh_date_to']);

                                if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                                    $workdayFlag = false;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                    ## Special Holiday Branch check
                    if ($workdayFlag == true) {

                        $sHolidaysBrData = $sHolidaysBr->where('branch_id', $rowBranch)->toArray();

                        foreach ($sHolidaysBrData as $RowB) {
                            $RowB = (array) $RowB;

                            if ($workdayFlag == true) {
                                $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                                $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                                if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                                    $workdayFlag = false;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($workdayFlag == true) {

                        // array_push($workingDays, $tempLoopDate->format('Y-m-d'));

                        array_push($tempWorkDay, $tempLoopDate->format('d-m-Y'));
                        array_push($tempWorkMonth, $tempLoopDate->format('m-Y'));
                    }
                    $tempLoopDate = $tempLoopDate->modify('+1 day');
                }
            }

            $tempWorkDay   = array_unique($tempWorkDay);
            $tempWorkMonth = array_unique($tempWorkMonth);

            $workingDayData[$rowBranch]['working_day']   = $tempWorkDay;
            $workingDayData[$rowBranch]['working_month'] = $tempWorkMonth;
        }

        return $workingDayData;
    }

    /*Generate Resign Code Start*/
    public static function generateResignCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "RES." . $br_code . ".";
            $currentSl = DB::table('hr_app_resigns')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Resign Code End*/

    /*Generate Promotion Code Start*/
    public static function generatePromotionCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "PROM." . $br_code . ".";
            $currentSl = DB::table('hr_app_promotions')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Promotion Code End*/

    /*Generate Demotion Code Start*/
    public static function generateDemotionCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "DEMO." . $br_code . ".";
            $currentSl = DB::table('hr_app_demotions')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Demotion Code End*/

    /*Generate Terminate Code Start*/
    public static function generateTerminateCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "TER." . $br_code . ".";
            $currentSl = DB::table('hr_app_terminates')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Terminate Code End*/

    /*Generate Dismiss Code Start*/
    public static function generateDismissCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "DIS." . $br_code . ".";
            $currentSl = DB::table('hr_app_dismisses')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Dismiss Code End*/

    /*Generate Retirement Code Start*/
    public static function generateRetirementCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "RET." . $br_code . ".";
            $currentSl = DB::table('hr_app_retirements')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Retirement Code End*/

    /*Generate ContractConclude Code Start*/
    public static function generateContractConcludeCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "CONC." . $br_code . ".";
            $currentSl = DB::table('hr_app_contract_concludes')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate ContractConclude Code End*/

    /*Generate Active Responsibilities Code Start*/
    public static function generateActiveResponsibilityCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "ACR." . $br_code . ".";
            $currentSl = DB::table('hr_app_active_responsibilities')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Active Responsibilities Code End*/

    /*Generate Transfer Code Start*/
    public static function generateTransferCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "TRN." . $br_code . ".";
            $currentSl = DB::table('hr_app_transfers')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Transfer Code End*/

    /*Generate Leave Code Start*/
    public static function generateLeaveCode($leave_cat_id, $applicantBranchId)
    {

        $lv_type = DB::table('hr_leave_category')->find($leave_cat_id);
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;

        $lt = $lv_type->short_form;
        
        if(!empty($applicantBranchId)){
            $prefix = $lt . "." . $br_code . ".";
            $currentSl = DB::table('hr_app_leaves')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Leave Code End*/

    /*Generate Movement Code Start*/
    public static function generateMovementCode($applicantBranchId)
    {

        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if(!empty($applicantBranchId)){
            $prefix = "MOV." . $br_code . ".";
            $currentSl = DB::table('hr_app_movements')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));
            return $newCode;
        }
        else{
            throw new Exception("Invalid branch_id in application code generation.");
        }

    }
    /*Generate Movement Code End*/
}
