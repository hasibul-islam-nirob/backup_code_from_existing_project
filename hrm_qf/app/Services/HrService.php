<?php

namespace App\Services;

use DateTime;
use Exception;
use DateInterval;
use function PHPSTORM_META\type;
use Illuminate\Support\Facades\DB;
use App\Model\HR\SalaryStructure;

use Illuminate\Support\Facades\Auth;
use App\Services\CommonService as Common;

class HrService
{
    /**
     * This function returns an array having branch ids which the logedin user have access according to HR policy.
     *
     * @return array
     */

    public static function getUserAccesableBranchIds()
    {
        $branchIds = [];

        if (Auth::user()->branch_id == 1) {

            $branchIds = DB::table('gnl_branchs')
                ->where([
                    ['is_delete', 0],
                    ['is_approve', 1],
                    // ['is_active', 1],
                ])
                ->pluck('id')
                ->toArray();

        } else {

            $empId = Auth::user()->emp_id;

            // $branchIds = [Auth::user()->branch_id];

            if (!empty($empId)) {
                $employeeData = DB::table("hr_employees")->where([['is_active', 1], ['is_delete', 0], ['id', $empId]])->selectRaw('id, branch_id, designation_id')->first();

                if (!empty($employeeData)) {

                    $designationId = $employeeData->designation_id;
                    $branchId = $employeeData->branch_id;

                    $branchIds = [$branchId];

                    $designationRoleMap = DB::table('hr_designation_role_mapping')
                        ->where(function ($query) use ($designationId) {
                            if (!empty($designationId)) {
                                $query->where('designation_ids', 'LIKE', "{$designationId}")
                                    ->orWhere('designation_ids', 'LIKE', "{$designationId},%")
                                    ->orWhere('designation_ids', 'LIKE', "%,{$designationId},%")
                                    ->orWhere('designation_ids', 'LIKE', "%,{$designationId}");
                            }
                        })
                        ->first();

                    if (!empty($designationRoleMap)) {
                        $positionId = $designationRoleMap->position_id;

                        $branchDataObj = Common::getBranchIdsForAllSection([
                            'branchArr'    => [$branchId],
                            'fnReturn'     => "dataObject"
                        ]);

                        // dd($branchDataObj->toArray());

                        $zoneId = $branchDataObj->pluck('zone_id')->first();
                        $regionId = $branchDataObj->pluck('region_id')->first();
                        $areaId = $branchDataObj->pluck('area_id')->first();

                        if ($positionId == 5) { # Zonal Manager

                            // ['is_active', 1],
                            $branchIds = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['zone_id', $zoneId]])
                                ->pluck('id')
                                ->toArray();
                        }

                        if ($positionId == 6) { # Regional manager

                            // ['is_active', 1],
                            $branchIds = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['region_id', $regionId]])
                                ->pluck('id')
                                ->toArray();
                        }

                        if ($positionId == 4) { # Area manager

                            // ['is_active', 1],
                            $branchIds = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['area_id', $areaId]])
                                ->pluck('id')
                                ->toArray();
                        }
                    }
                }
            }
        }

        //This condition provide for safety
        //Becasue if somehow branchIds getting empty
        if (empty($branchIds)) {
            $branchIds = [Auth::user()->branch_id];
        }

        return $branchIds;
    }

    public static function back_getUserAccesableBranchIds()
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

            $empId = Auth::user()->emp_id;

            dd(Auth::user()->emp_id);

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
                ->select('he.id', 'gsu.branch_id as branchId', 'he.designation_id', 'sys_user_role_id')
                // 'hd.id as designation',
                ->first();

            dd($userInfo);

            if ($userInfo->branchId > 1) {

                $roleId             = $userInfo->sys_user_role_id;

                $designationRoleMap = DB::table('hr_designation_role_mapping')
                    ->where(function ($query) use ($roleId) {
                        if (!empty($roleId)) {
                            $query->where('role_id', 'LIKE', "{$roleId}")
                                ->orWhere('role_id', 'LIKE', "{$roleId},%")
                                ->orWhere('role_id', 'LIKE', "%,{$roleId},%")
                                ->orWhere('role_id', 'LIKE', "%,{$roleId}");
                        }
                    })
                    ->first();

                if ($designationRoleMap) {
                    // $designation_value  = (int) $designationRoleMap->position_id;
                    // ZM = 34, RM = 63, AM = 35 [designation id]
                    $designation =  $designationRoleMap->designation_ids;
                    $designationArr = explode(',', $designation);

                    if (in_array(34, $designationArr)) { ##

                        $branchDataObj = Common::getBranchIdsForAllSection([
                            'branchArr'    => [Auth::user()->branch_id],
                            'fnReturn'     => "dataObject"
                        ]);

                        $branchIds = DB::table('gnl_branchs')
                            ->whereIn('region_id', $branchDataObj->pluck('region_id')->toarray())
                            ->pluck('id')
                            ->toarray();
                    } elseif (in_array(35, $designationArr)) {
                        $branchIds = explode(',', DB::table('gnl_areas')
                            ->where('branch_arr', 'like', '%' . Auth::user()->branch_id . '%')
                            ->select('branch_arr')
                            ->first()->branch_arr);
                    }
                } else {
                    $designation_value = 1;
                    $branchIds = [Auth::user()->branch_id];
                }
            }

            // if ($userInfo->designation == "Zonal Manager") {

            //     /**
            //      * This is temporary solution. We are assuming that zonal manager is regional manager
            //      * and getting accessible branch Ids as before
            //      */

            //     $branchDataObj = Common::getBranchIdsForAllSection([
            //         'branchArr'    => [Auth::user()->branch_id],
            //         'fnReturn'     => "dataObject"
            //     ]);

            //     $branchIds = DB::table('gnl_branchs')
            //         ->whereIn('region_id', $branchDataObj->pluck('region_id')->toarray())
            //         ->pluck('id')
            //         ->toarray();

            //     // $branchIds = explode(',', DB::table('gnl_zones')
            //     //     ->where('branch_arr', 'like', '%' . Auth::user()->branch_id . '%')
            //     //     ->select('branch_arr')
            //     //     ->first()->branch_arr);

            // } elseif ($userInfo->designation == "Area Manager") {

            //     $branchIds = explode(',', DB::table('gnl_areas')
            //         ->where('branch_arr', 'like', '%' . Auth::user()->branch_id . '%')
            //         ->select('branch_arr')
            //         ->first()->branch_arr);
            // } else {

            //     $branchIds = [Auth::user()->branch_id];
            // }
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
     * Calling: self::systemHolidays(companyID,branchID,somityID,startDate,endDate, period)
     */

    public static function systemHolidays($companyId = null, $branchId = null, $somityId = null, $startDate = null, $endDate = null, $period = null)
    {
        $companyId = (!empty($companyId)) ? $companyId : Common::getCompanyId();
        $branchId  = (!empty($branchId)) ? $branchId : Common::getBranchId();
        $somityId  = (!empty($somityId)) ? $somityId : 1;

        $companyId = (!empty($companyId)) ? $companyId : 1;

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
            $govtHolidays = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            $companyArr          = (!empty($companyId)) ? ['company_id', '=', $companyId] : ['company_id', '<>', ''];
            $companyHolidayQuery = DB::table('hr_holidays_comp')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where([$companyArr])
                ->orderBy('ch_eff_date', 'DESC')
                ->get();
            $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Special Holiday for Organization Query
            $specialHolidayORGQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchId)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            ## Special Holiday for Samity Query
            $specialHolidaySQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'samity']])
                ->where('samity_id', '=', $somityId)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysSamity = (count($specialHolidaySQuery->toarray()) > 0) ? $specialHolidaySQuery->toarray() : array();


            ## Reschedule Holiday Query
            $rescheduleHoliday = array();
            if (DB::getSchemaBuilder()->hasTable('hr_holiday_reschedule')) {
                $rescheduleHolidayQuery = DB::table('hr_holiday_reschedule')->where([['is_delete', 0], ['is_active', 1]])
                    ->where(function ($query) use ($companyId, $branchId, $somityId, $startDate, $endDate) {
                        $query->where('company_id', '=', $companyId);
                        $query->orWhere('branch_id', '=', $branchId);
                        // $query->orWhere('samity_id', '=', $somityId);

                        if (!empty($startDate) && !empty($endDate)) {
                            $query->where([['working_date', '>=', $startDate], ['working_date', '<=', $endDate]]);
                            $query->orWhere([['reschedule_date', '>=', $startDate], ['reschedule_date', '<=', $endDate]]);
                        }
                    })
                    ->select('id', 'working_date', 'reschedule_date')
                    ->get();

                $rescheduleHoliday = (count($rescheduleHolidayQuery->toarray()) > 0) ? $rescheduleHolidayQuery->toarray() : array();

                if (!empty($rescheduleHoliday)) {
                    foreach ($rescheduleHoliday as $key => $value) {
                        array_push($holiDays, $value->working_date);
                    }
                }
            }

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $holiDayFlag = false;

                ## Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    $RowFG = (array) $RowFG;
                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $holiDayFlag = true;
                    }
                }

                ## Company Holiday Check
                if ($holiDayFlag == false) {
                    foreach ($companyHolidays as $RowC) {
                        $RowC = (array) $RowC;

                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        $ch_eff_date_end = $RowC['ch_eff_date_end'];
                        if (!empty($ch_eff_date_end)) {
                            $ch_eff_date_end = new DateTime($ch_eff_date_end);
                        }

                        ## This is Full day name
                        $dayName = $tempLoopDate->format('l');
                        // if($tempLoopDate->format("Y-m-d") == "2022-03-12"){
                        //     print_r('<pre>');
                        //     print_r($tempLoopDate);
                        //     print_r($RowC);
                        //     print_r('-----------------------');
                        //     print_r('<br>');
                        //     print_r($dayName);
                        //     print_r('<br>');
                        //     print_r($ch_day_arr);
                        //     print_r($ch_eff_date);
                        //     print_r('///////////////////////////////');
                        //     print_r('<br>');
                        // }

                        if (
                            !empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                            ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) &&
                            ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))
                        ) {

                            // if($tempLoopDate->format("Y-m-d") == "2022-03-12"){
                            //     print_r('true');
                            //     print_r('<br>');
                            //     // dd($tempLoopDate);
                            // }

                            $holiDayFlag = true;
                        } else if (
                            $ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) &&
                            ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))
                        ) {
                            $holiDayFlag = true;
                        }

                        // if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                        //     $holiDayFlag = true;
                        // }
                    }
                }

                ## Special Holiday Org check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysORG as $RowO) {
                        $RowO = (array) $RowO;

                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                ## Special Holiday Branch check
                if ($holiDayFlag == false) {
                    foreach ($sHolidaysBr as $RowB) {
                        $RowB = (array) $RowB;

                        $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                        $sh_date_to_b   = new DateTime($RowB['sh_date_to']);

                        if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                            $holiDayFlag = true;
                        }
                    }
                }

                ## Special Holiday Samity check
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

            ## Reschedule Holiday check
            foreach ($rescheduleHoliday as $reHoliday) {

                array_push($holiDays, $reHoliday->working_date);

                $rescheduleDate = $reHoliday->reschedule_date;

                if (in_array($rescheduleDate, $holiDays)) {
                    $key = array_search($rescheduleDate, $holiDays);
                    unset($holiDays[$key]);
                } else {
                    continue;
                }
            }
        }
        // dd($holiDays, $rescheduleHoliday);
        sort($holiDays);
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
        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId(); ### Changed by tuli on 05/07/23
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
            // $fromDate = new DateTime("01-" . $startDate->format('m') . "-" . $endDate->format('Y'));
            $toDate   = new DateTime($endDate);
            $endDate = $toDate;
            $fromDate = new DateTime("01-" . $endDate->format('m') . "-" . $endDate->format('Y'));
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

            ## Fixed Govt Holiday Query
            $govtHolidays = $govtHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            ## Company Holiday Query
            $companyArr          = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            $companyHolidayQuery = $comapnyHolidayModel::where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
                ->where([$companyArr])
                ->orderBy('ch_eff_date', 'DESC')
                ->get();

            $companyHolidays = (count($companyHolidayQuery->toArray()) > 0) ? $companyHolidayQuery->toArray() : array();


            ## Special Holiday for Organization Query
            $specialHolidayORGQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            ## Special Holiday for Branch Query
            $specialHolidayBrQuery = $specialHolidayModel::where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();

            $tempLoopDate = clone $fromDate;
            while ($tempLoopDate <= $toDate) {
                $workdayFlag = true;

                ## Fixed Govt Holiday Check
                foreach ($fixedGovtHoliday as $RowFG) {
                    if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                        $workdayFlag = false;
                    }
                }

                ## Company Holiday Check
                // if ($workdayFlag == true) {
                //     foreach ($companyHolidays as $RowC) {
                //         $ch_day = $RowC['ch_day'];

                //         $ch_day_arr  = explode(',', $RowC['ch_day']);
                //         $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                //         ## This is Full day name
                //         $dayName = $tempLoopDate->format('l');

                //         if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                //             $workdayFlag = false;
                //         }
                //     }
                // }

                ## Company Holiday Check
                if ($workdayFlag == true) {
                    foreach ($companyHolidays as $RowC) {

                        $ch_day = $RowC['ch_day'];

                        $ch_day_arr  = explode(',', $RowC['ch_day']);
                        $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                        $ch_eff_date_end = $RowC['ch_eff_date_end'];
                        if (!empty($ch_eff_date_end)) {
                            $ch_eff_date_end = new DateTime($ch_eff_date_end);
                        }


                        ## This is Full day name
                        $dayName = $tempLoopDate->format('l');

                        if (
                            !empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                            ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) &&
                            ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))
                        ) {

                            $workdayFlag = false;
                        } else if (
                            $ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) &&
                            ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))
                        ) {
                            $workdayFlag = false;
                        }
                    }
                }

                ## Special Holiday Org check
                if ($workdayFlag == true) {
                    foreach ($sHolidaysORG as $RowO) {
                        $sh_date_from = new DateTime($RowO['sh_date_from']);
                        $sh_date_to   = new DateTime($RowO['sh_date_to']);

                        if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                            $workdayFlag = false;
                        }
                    }
                }

                ## Special Holiday Branch check
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
            ## This is for Half Day Name
            // $DayName = strtolower($TempNextDate->format('D'));

            ## This is Full day name
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
                if (!empty($CompanyHolidayID)) {
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

                        if (!empty($samityID)) {
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

    /**
     * Get the previous working day based on the given date, branch ID, company ID, and somity ID.
     *
     * @param string|null $currentDate The current date.
     * @param int|null    $branchID     The branch ID.
     * @param int|null    $companyID    The company ID.
     * @param int|null    $samityID     The somity ID.
     *
     * @return string The previous working day.
     */
    public static function systemPreviousWorkingDay($currentDate = null, $branchID = null, $companyID = null, $samityID = null)
    {
        // $branchID = self::getBranchId();
        // $companyID = self::getCompanyId();
        // dd('test ');

        // $samityID  = (!empty($samityID)) ? $samityID : 1;

        $GovtHolidayModel    = 'App\\Model\\GNL\\HR\\GovtHoliday';
        $ComapnyHolidayModel = 'App\\Model\\GNL\\HR\\CompanyHoliday';
        $SpecialHolidayModel = 'App\\Model\\GNL\\HR\\SpecialHoliday';

        $TempCurrentDate = new DateTime($currentDate);
        $TempNextDate    = $TempCurrentDate->modify('-1 day');

        $HolidayFlag = true;

        while ($HolidayFlag == true) {
            $HolidayFlag = false;

            $TempNext = $TempNextDate->format('d-m');
            ## This is for Half Day Name
            // $DayName = strtolower($TempNextDate->format('D'));

            ## This is Full day name
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
                if (!empty($CompanyHolidayID)) {
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

                        if (!empty($samityID)) {
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
                $TempNextDate = $TempNextDate->modify('-1 day');
            }
        }

        $currentDate = $TempNextDate->format('Y-m-d');

        return $currentDate;
    }

    /**
     * Get working day data for a specific branch or somity.
     *
     * @param string $checkFor        Branch or somity.
     * @param array  $parameter       Additional parameters.
     *                                - startDate: Start date (optional).
     *                                - endDate: End date (optional).
     *                                - companyId: Company ID (optional).
     *                                - branchId: Array of branch IDs or somity ID (depending on $checkFor).
     *                                - somityId: Somity ID (optional).
     *                                - moduleShortName: Module short name (optional).
     *                                - period: Date period (optional).
     *
     * @return array Working day data.
     */
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
                    $selBranchArr = Common::getBranchIdsForAllSection(['companyId' => $companyId]);
                }
            } else {
                $selBranchArr = [$branchId];
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
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date', 'ch_eff_date_end')
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
                                } elseif (
                                    !empty($RowFG['efft_start_date'])
                                    && ($RowFG['efft_start_date'] <= $tempLoopDate->format('Y-m-d'))
                                    && !empty($RowFG['efft_end_date'])
                                    && ($RowFG['efft_end_date'] >= $tempLoopDate->format('Y-m-d'))
                                ) {

                                    $workdayFlag = false;
                                } elseif (
                                    !empty($RowFG['efft_start_date'])
                                    && ($RowFG['efft_start_date'] <= $tempLoopDate->format('Y-m-d'))
                                    && empty($RowFG['efft_end_date'])
                                ) {

                                    $workdayFlag = false;
                                } elseif (
                                    empty($RowFG['efft_start_date'])
                                    && !empty($RowFG['efft_end_date'])
                                    && ($RowFG['efft_end_date'] >= $tempLoopDate->format('Y-m-d'))
                                ) {

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

                                $ch_eff_date_end = $RowC['ch_eff_date_end'];
                                if (!empty($ch_eff_date_end)) {
                                    $ch_eff_date_end = new DateTime($ch_eff_date_end);
                                }

                                // This is Full day name
                                $dayName = $tempLoopDate->format('l');

                                if (
                                    !empty($ch_eff_date_end) && in_array($dayName, $ch_day_arr) &&
                                    ($tempLoopDate->format('Y-m-d') >= $ch_eff_date->format('Y-m-d')) &&
                                    ($tempLoopDate->format('Y-m-d') <= $ch_eff_date_end->format('Y-m-d'))
                                ) {

                                    $workdayFlag = false;
                                } else if (
                                    $ch_eff_date_end == '' && in_array($dayName, $ch_day_arr) &&
                                    ($ch_eff_date->format('Y-m-d') <= $tempLoopDate->format('Y-m-d'))
                                ) {
                                    $workdayFlag = false;
                                }

                                // if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                                //     $workdayFlag = false;
                                // }
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
        if (!empty($applicantBranchId)) {
            $prefix = "RES." . $br_code . ".";
            $currentSl = DB::table('hr_app_resigns')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_resigns')
                ->where('resign_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'resign_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->resign_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }

            return $newCode;
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Resign Code End*/

    /*Generate Promotion Code Start*/
    public static function generatePromotionCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "PROM." . $br_code . ".";
            $currentSl = DB::table('hr_app_promotions')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_promotions')
                ->where('promotion_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'promotion_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->promotion_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Promotion Code End*/


    /*Advance Salary Code Start*/
    public static function generateAdvanceSalaryCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "ADS." . $br_code . ".";
            $currentSl = DB::table('hr_app_advance_salary')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_advance_salary')
                ->where('advance_salary_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'advance_salary_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->advance_salary_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Advance Salary Code End*/

    /* App Security Money Code Start*/
    public static function generateAppSecurityMoneyCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "SEM." . $br_code . ".";
            $currentSl = DB::table('hr_app_security_money')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_security_money')
                ->where('security_money_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'security_money_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->security_money_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /* App Security Money Code End*/

    /*Advance Salary Code Start*/
    public static function generatePfLoanCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "PFL." . $br_code . ".";
            $currentSl = DB::table('hr_app_loan')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_loan')
                ->where('loan_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'loan_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->loan_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Advance Salary Code End*/

    /*Vehicle Loan Code Start*/
    public static function generateVehicleLoanCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "VEL." . $br_code . ".";
            $currentSl = DB::table('hr_app_vehicle_loan')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_vehicle_loan')
                ->where('loan_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'loan_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->loan_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Vehicle Loan Code End*/


    /*Generate Demotion Code Start*/
    public static function generateDemotionCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "DEMO." . $br_code . ".";
            $currentSl = DB::table('hr_app_demotions')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_demotions')
                ->where('demotion_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'demotion_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->demotion_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Demotion Code End*/

    /*Generate Terminate Code Start*/
    public static function generateTerminateCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "TER." . $br_code . ".";
            $currentSl = DB::table('hr_app_terminates')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_terminates')
                ->where('terminate_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'terminate_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->terminate_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Terminate Code End*/

    /*Generate Dismiss Code Start*/
    public static function generateDismissCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "DIS." . $br_code . ".";
            $currentSl = DB::table('hr_app_dismisses')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_dismisses')
                ->where('dismiss_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'dismiss_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->dismiss_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Dismiss Code End*/

    /*Generate Retirement Code Start*/
    public static function generateRetirementCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "RET." . $br_code . ".";
            $currentSl = DB::table('hr_app_retirements')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_retirements')
                ->where('retirement_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'retirement_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->retirement_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Retirement Code End*/


    /*Generate Increment Code Start*/
    public static function generateIncrementCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "INC." . $br_code . ".";
            $currentSl = DB::table('hr_app_increment')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_increment')
                ->where('increment_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'increment_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->increment_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Increment Code End*/


    /*Generate ContractConclude Code Start*/
    public static function generateContractConcludeCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "CONC." . $br_code . ".";
            $currentSl = DB::table('hr_app_contract_concludes')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_contract_concludes')
                ->where('contract_conclude_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'contract_conclude_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->contract_conclude_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate ContractConclude Code End*/

    /*Generate Active Responsibilities Code Start*/
    public static function generateActiveResponsibilityCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "ACR." . $br_code . ".";
            $currentSl = DB::table('hr_app_active_responsibilities')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_active_responsibilities')
                ->where('active_responsibility_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'active_responsibility_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->active_responsibility_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Active Responsibilities Code End*/

    /*Generate Transfer Code Start*/
    public static function generateTransferCode($applicantBranchId)
    {
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "TRN." . $br_code . ".";
            $currentSl = DB::table('hr_app_transfers')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_transfers')
                ->where('transfer_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'transfer_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->transfer_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }
        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Transfer Code End*/

    /*Generate Leave Code Start*/
    public static function generateLeaveCode($leave_cat_id, $applicantBranchId)
    {
        if ($applicantBranchId == 0) {
            $applicantBranchId = 1;
        }

        $lv_type = DB::table('hr_leave_category')->find($leave_cat_id);
        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;

        $lt = $lv_type->short_form;

        if (!empty($applicantBranchId)) {
            $prefix = $lt . "." . $br_code . ".";
            $currentSl = DB::table('hr_app_leaves')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_leaves')
                ->where('leave_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'leave_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->leave_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }

        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Leave Code End*/

    /*Generate Movement Code Start*/
    public static function generateMovementCode($applicantBranchId)
    {
        if ($applicantBranchId == 0) {
            $applicantBranchId = 1;
        }

        $br_code = DB::table('gnl_branchs')->find($applicantBranchId)->branch_code;
        if (!empty($applicantBranchId)) {
            $prefix = "MOV." . $br_code . ".";
            $currentSl = DB::table('hr_app_movements')->count() + 1;
            $newCode  = $prefix . sprintf("%05d", ($currentSl + 1));

            $record = DB::table('hr_app_movements')
                ->where('movement_code', 'LIKE', '%' . $newCode . '%')
                ->select(['id', 'movement_code'])
                ->first();

            if ($record) {
                $OldCodeNo = explode('.', $record->movement_code);
                $NewCodeNo = $prefix . sprintf("%05d", ($OldCodeNo[2] + 1));
                return $NewCodeNo;

            } else {
                return $newCode;
            }

        } else {
            throw new Exception("Invalid branch_id in application code generation.");
        }
    }
    /*Generate Movement Code End*/

    /**
     * Get designation data based on the provided array of IDs.
     *
     * @param array  $requestArr Array of designation IDs.
     * @param string $feildName  Field name to select from the database.
     *
     * @return array Designation data.
     */
    public static function fnForDesignationData($requestArr, $feildName = 'name')
    {
        $returnData = array();

        if (count($requestArr) > 0) {

            $returnData = DB::table('hr_designations')
                // ->where([['is_delete', 0], ['is_active', 1]])
                ->where([['is_delete', 0]])
                ->whereIn('id', $requestArr)
                ->selectRaw($feildName . ' as name, id')
                ->pluck('name', 'id')
                ->toArray();
        }

        return $returnData;
    }
    /**
     * Get department data based on the provided array of IDs.
     *
     * @param array $requestArr Array of department IDs.
     *
     * @return array Department data.
     */
    public static function fnForDepartmentData($requestArr)
    {
        $returnData = array();

        if (count($requestArr) > 0) {

            $returnData = DB::table('hr_departments')
                // ->where([['is_delete', 0], ['is_active', 1]])
                ->where([['is_delete', 0]])
                ->whereIn('id', $requestArr)
                ->selectRaw('dept_name, id')
                ->pluck('dept_name', 'id')
                ->toArray();
        }

        return $returnData;
    }


    /**
     * Get leave data based on the provided start and end dates.
     *
     * @param string|null $startDate Start date.
     * @param string|null $endDate   End date.
     * @param string      $type      Type of leave data ('during' or 'opening').
     *
     * @return \Illuminate\Support\Collection Leave data.
     */
    public static function fnForCombineLeaveData($startDate = null, $endDate = null, $type = 'during')
    {

        $year = date("Y", strtotime($startDate));
        $firstDateOfYear = date("$year-01-01");

        if ($type == 'during') {
            return $commonTable_two = DB::table('gnl_dynamic_form_value')
                ->where([['type_id', 3], ['form_id', 1]])
                ->join('hr_leave_category', function ($join) {
                    $join->on('gnl_dynamic_form_value.uid', '=', 'hr_leave_category.leave_type_uid')
                        ->where('hr_leave_category.is_delete', 0)
                        ->where('hr_leave_category.is_active', 1);
                })
                ->join('hr_app_leaves', function ($join) {
                    $join->on('hr_leave_category.id', '=', 'hr_app_leaves.leave_cat_id')
                        ->where('hr_app_leaves.is_delete', 0)
                        ->where('hr_app_leaves.is_active', 1);
                })
                ->select(
                    'hr_app_leaves.leave_cat_id as leave_cat_id',
                    'hr_app_leaves.emp_id',
                    'hr_app_leaves.date_from',
                    'hr_app_leaves.date_to',
                    'gnl_dynamic_form_value.name as type_name',
                    'hr_leave_category.short_form as leave_short_name',
                    'hr_leave_category.name as leave_name'
                )
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('hr_app_leaves.date_from', [$startDate, $endDate]);
                    $query->orWhereBetween('hr_app_leaves.date_to', [$startDate, $endDate]);
                })
                ->get();
        } elseif ($type == 'opening') {
            return $commonTable = DB::table('gnl_dynamic_form_value')
                ->where([['type_id', 3], ['form_id', 1]])
                ->join('hr_leave_category', function ($join) {
                    $join->on('gnl_dynamic_form_value.uid', '=', 'hr_leave_category.leave_type_uid')
                        ->where('hr_leave_category.is_delete', 0)
                        ->where('hr_leave_category.is_active', 1);
                })
                ->join('hr_app_leaves', function ($join) {
                    $join->on('hr_leave_category.id', '=', 'hr_app_leaves.leave_cat_id')
                        ->where('hr_app_leaves.is_delete', 0)
                        ->where('hr_app_leaves.is_active', 1);
                })
                ->select(
                    'hr_app_leaves.leave_cat_id as leave_cat_id',
                    'hr_app_leaves.emp_id',
                    'hr_app_leaves.date_from',
                    'hr_app_leaves.date_to',
                    'gnl_dynamic_form_value.name as type_name',
                    'hr_leave_category.short_form as leave_short_name',
                    'hr_leave_category.name as leave_name'
                )
                // ->where('hr_app_leaves.date_from', '<', $startDate)
                ->where([['hr_app_leaves.date_from', '>', $firstDateOfYear], ['hr_app_leaves.date_from', '<', $startDate]])
                ->where('hr_app_leaves.date_to', '<', $endDate)
                ->get();
        }
        // else{
        //     return $commonTable = DB::table('gnl_dynamic_form_value')
        //         ->where([['type_id', 3],['form_id', 1]])
        //         ->join('hr_leave_category', function ($join) {
        //             $join->on('gnl_dynamic_form_value.uid', '=', 'hr_leave_category.leave_type_uid')
        //                 ->where('hr_leave_category.is_delete', 0)
        //                 ->where('hr_leave_category.is_active', 1);
        //         })
        //         ->join('hr_app_leaves', function ($join) {
        //             $join->on('hr_leave_category.id', '=', 'hr_app_leaves.leave_cat_id')
        //                 ->where('hr_app_leaves.is_delete', 0)
        //                 ->where('hr_app_leaves.is_active', 1);
        //         })
        //         ->select('hr_app_leaves.leave_cat_id as leave_cat_id',
        //                     'hr_app_leaves.emp_id',
        //                     'hr_app_leaves.date_from',
        //                     'hr_app_leaves.date_to',
        //                     'gnl_dynamic_form_value.name as type_name',
        //                     'hr_leave_category.short_form as leave_short_name',
        //                     'hr_leave_category.name as leave_name'
        //         )
        //         ->get();
        // }

    }



    /**
     * Get leave type data based on the leave type UID.
     *
     * @param string $leaveTypeId Leave type UID.
     *
     * @return \Illuminate\Support\Collection Leave type data.
     */
    public static function fnForLeaveTypeData($leave_type_id)
    {
        return $leave_type = DB::table('gnl_dynamic_form_value')->where([['type_id', 3], ['form_id', 1]])
            ->when(!empty($leave_type_id), function ($query) use ($leave_type_id) {
                $query->where('uid', $leave_type_id);
            })
            ->orderByRaw("CASE WHEN uid=2 THEN 0 ELSE 1 END DESC")
            ->get();
    }

    /**
     * Get leave category data based on leave type ID or leave type UID.
     *
     * @param int|string $leaveTypeId Leave type ID or leave type UID.
     *
     * @return \Illuminate\Support\Collection Leave category data.
     */
    public static function fnForLeaveCategoryByTypeData($leave_type_id)
    {
        return $leave_cat2 = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])
            ->when(!empty($leave_type_id) && empty($leave_type_id), function ($query) use ($leave_type_id) {
                $query->where('id', $leave_type_id);
            })
            ->when(!empty($leave_type_id) && !empty($leave_type_id->leave_type_id), function ($query) use ($leave_type_id) {
                $query->where('id', $leave_type_id);
                $query->where('leave_type_uid', $leave_type_id);
            })
            ->get();
    }


    /**
     * Get leave category data based on provided leave category IDs.
     *
     * @param array  $requestArr Array of leave category IDs.
     * @param string $fieldName  Field name to retrieve (default: 'name').
     *
     * @return array Leave category data formatted as [ID => Field Value].
     */
    public static function fnForLeaveCategoryData($requestArr, $feildName = 'name')
    {
        $returnData = array();

        if (count($requestArr) > 0) {

            $returnData = DB::table('hr_leave_category')->where([['is_active', 1], ['is_delete', 0]])
                ->whereIn('id', $requestArr)
                ->selectRaw($feildName . ' as name, id')
                ->pluck('name', 'id')
                ->toArray();
        }

        return $returnData;
    }

    /**
     * Get employee data based on provided employee array.
     *
     * @param array   $employeeArr Array of employee numbers, employee IDs, or user IDs.
     * @param bool    $posModule   Flag to determine whether the Point of Sale (POS) module is used (default: false).
     * @param bool    $userFlag    Flag to determine whether the provided IDs are user IDs (default: false).
     *
     * @return array Employee data formatted as [ID/Number => Employee Name].
     */
    public static function fnForEmployeeData($employeeArr, $posModule = false, $userFlag = false)
    {
        $employeeData = array();
        if (count($employeeArr) > 0) {

            if (Common::getDBConnection() == "sqlite") {
                $employeeData = DB::table('hr_employees')
                    // ->where([['is_delete', 0], ['is_active', 1]])
                    ->where([['is_delete', 0]])
                    ->whereIn('employee_no', $employeeArr)
                    ->orderBy('branch_id')
                    ->orderBy('emp_code')
                    ->selectRaw('(emp_name || " [" || emp_code || "]") AS emp_name, employee_no')
                    ->pluck('emp_name', 'employee_no')
                    ->toArray();
            } else {
                if ($posModule) {
                    $employeeData = DB::table('hr_employees')
                        // ->where([['is_delete', 0], ['is_active', 1]])
                        ->where([['is_delete', 0]])
                        ->whereIn('employee_no', $employeeArr)
                        ->orderBy('branch_id')
                        ->orderBy('emp_code')
                        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, employee_no')
                        ->pluck('emp_name', 'employee_no')
                        ->toArray();
                    // dd($employeeData,$employeeArr);
                } else {

                    if ($userFlag == false) {
                        ## employee data by employee id
                        $employeeData = DB::table('hr_employees')
                            // ->where([['is_delete', 0], ['is_active', 1]])
                            ->where([['is_delete', 0]])
                            ->whereIn('id', $employeeArr)
                            ->orderBy('branch_id')
                            ->orderBy('emp_code')
                            ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
                            ->pluck('emp_name', 'id')
                            ->toArray();
                    } else {
                        ## employee data by user_id
                        $employeeData = DB::table('hr_employees')
                            // ->where([['is_delete', 0], ['is_active', 1]])
                            ->where([['is_delete', 0]])
                            ->whereIn('user_id', $employeeArr)
                            ->orderBy('branch_id')
                            ->orderBy('emp_code')
                            ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, user_id')
                            ->pluck('emp_name', 'user_id')
                            ->toArray();
                    }
                }
            }
        }

        return $employeeData;
    }

    /**
     * Get employee options based on provided parameters.
     *
     * @param array $parameters [
     *     'branchId'      => (int) Branch ID (optional),
     *     'departmentId'  => (int) Department ID (optional),
     *     'designationId' => (int) Designation ID (optional),
     *     'isActive'      => (bool) Flag to filter active employees (default: true),
     *     'isStatus'      => (int) Employee status (default: 1),
     * ]
     *
     * @return array Employee options formatted for dropdowns.
     */
    public static function getOptionsForEmployee($parameter = [])
    {

        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;
        $departmentId = (isset($parameter['departmentId'])) ? $parameter['departmentId'] : null;
        $designationId = (isset($parameter['designationId'])) ? $parameter['designationId'] : null;
        $isActive = (isset($parameter['isActive'])) ? $parameter['isActive'] : true;
        $isStatus = (isset($parameter['isStatus'])) ? $parameter['isStatus'] : 1;

        $emp = DB::table('hr_employees')
            ->where('is_delete', 0)
            ->where(function ($query) use ($branchId, $departmentId, $designationId, $isActive, $isStatus) {
                if ($isActive == true) {
                    $query->where('is_active', 1);
                }

                if (!empty($isStatus)) {
                    $query->where('status', $isStatus);
                }

                if (!empty($branchId)) {
                    $query->where('branch_id', $branchId);
                }

                if (!empty($departmentId)) {
                    $query->where('department_id', $departmentId);
                }

                if (!empty($designationId)) {
                    $query->where('designation_id', $designationId);
                }
            })
            ->orderBy('emp_code', 'asc')
            ->get();

        if (count($emp->toArray()) > 0) {
            $designationIdArr = array_values(array_unique($emp->pluck('designation_id')->toArray()));
            $departmentIdArr = array_values(array_unique($emp->pluck('department_id')->toArray()));

            $designationData = self::fnForDesignationData($designationIdArr);
            $departmentData = self::fnForDepartmentData($departmentIdArr);
        } else {
            $designationData = array();
            $departmentData = array();
        }


        $empArr            = [];
        $empArr[0]['id']   = '';
        // $empArr[0]['text'] = '<div>Select employee</div>';
        $empArr[0]['text'] = 'Select employee';
        $empArr[0]['html'] = 'Select employee';
        $empArr[0]['title'] = 'Select employee';

        foreach ($emp as $key => $item) {

            $designationText = (isset($designationData[$item->designation_id])) ? $designationData[$item->designation_id] : "";
            $departmentText = (isset($departmentData[$item->department_id])) ? $departmentData[$item->department_id] : "";

            $empArr[$key + 1]['id']    = $item->id;
            $empArr[$key + 1]['text']  = $item->emp_name . ' [' . $item->emp_code . ']';
            $empArr[$key + 1]['html']  = '<strong style="color: #804739">' . $item->emp_name . ' [' . $item->emp_code . ']</strong>';
            $empArr[$key + 1]['html']  .= '<br><small><b>Designation:</b> ' . $designationText . '</small>';
            $empArr[$key + 1]['html']  .= '<br><small><b>Department:</b> ' . $departmentText . '</small>';


            $empArr[$key + 1]['title'] = $item->emp_name . ' [' . $item->emp_code . ']';
        }

        return $empArr;
    }

    /**
     * Get Employee Ids
     * @param parameter @type array
     * @param ['companyId', 'zoneId', 'regionId', 'areaId', 'branchId', 'designationId', 'departmentId', 'employeeId'],
     * @param ['branchIds', 'designationIds', 'departmentIds', 'employeeIds']
     * @param ['gender', 'status', 'isActive', 'roleId', 'userId']
     * @param closingDate (Resign / Terminate / Dismiss / Retirement Date)
     */

    /**
     * Retrieve employee data based on specified parameters.
     *
     * @param array $parameter An associative array of parameters for filtering employee data.
     *                         Possible parameters include:
     *                         - employeeId: Retrieve data for a specific employee.
     *                         - employeeIds: Retrieve data for multiple employees.
     *                         - companyId: Filter by company ID.
     *                         - zoneId: Filter by zone ID.
     *                         - regionId: Filter by region ID.
     *                         - areaId: Filter by area ID.
     *                         - branchId: Filter by branch ID.
     *                         - designationId: Filter by designation ID.
     *                         - departmentId: Filter by department ID.
     *                         - branchIds: Retrieve data for multiple branches.
     *                         - designationIds: Retrieve data for multiple designations.
     *                         - departmentIds: Retrieve data for multiple departments.
     *                         - gender: Filter by gender.
     *                         - status: Filter by employment status.
     *                         - isActive: Filter by active/inactive status.
     *                         - roleId: Filter by user role ID.
     *                         - userId: Filter by user ID.
     *                         - joinDateFrom: Filter employees joined on or after a specific date.
     *                         - joinDateTo: Filter employees joined on or before a specific date.
     *                         - joinDate: Filter employees joined on a specific date.
     *                         - fromDate: Filter employees based on start date.
     *                         - toDate: Filter employees based on end date.
     *                         - orderBy: Specify ordering for the result set.
     *                         - selectRaw: Specify raw SQL for the SELECT clause.
     *                         - ignoreIds: Exclude employees with specified IDs.
     *                         - ignoreDesignations: Exclude employees with specified designations.
     *                         - ignoreDepartments: Exclude employees with specified departments.
     *
     * @return Illuminate\Support\Collection Returns a collection of employee data based on the provided parameters.
     */
    public static function fnForGetEmployees($parameter = [])
    {

        $userInfo = Auth::user();
        $loginUserDeptId = self::getUserDepartmentId($userInfo['emp_id']);
        $statusArray = (isset($parameter['statusArray'])) ? $parameter['statusArray'] : array();

        // dd($userInfo, $loginUserDeptId);

        $selectEmployee = array();

        $employeeId = (isset($parameter['employeeId'])) ? $parameter['employeeId'] : null;
        $employeeIdArr = (isset($parameter['employeeIds'])) ? $parameter['employeeIds'] : array();

        // if(!empty($employeeId)){
        //     return [$employeeId];
        // }

        // if(count($employeeIdArr) > 0){
        //     return $employeeIdArr;
        // }

        $companyId = (isset($parameter['companyId'])) ? $parameter['companyId'] : null;
        $zoneId = (isset($parameter['zoneId'])) ? $parameter['zoneId'] : null;
        $regionId = (isset($parameter['regionId'])) ? $parameter['regionId'] : null;
        $areaId = (isset($parameter['areaId'])) ? $parameter['areaId'] : null;
        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;

        $designationId = (isset($parameter['designationId'])) ? $parameter['designationId'] : null;
        $departmentId = (isset($parameter['departmentId'])) ? $parameter['departmentId'] : null;

        $branchIdArr = (isset($parameter['branchIds'])) ? $parameter['branchIds'] : array();
        $designationIdArr = (isset($parameter['designationIds'])) ? $parameter['designationIds'] : array();
        $departmentIdArr = (isset($parameter['departmentIds'])) ? $parameter['departmentIds'] : array();

        $gender = (isset($parameter['gender'])) ? $parameter['gender'] : null;

        $status = (isset($parameter['status'])) ? $parameter['status'] : null;
        $isActive = (isset($parameter['isActive'])) ? $parameter['isActive'] : null;

        $roleId = (isset($parameter['roleId'])) ? $parameter['roleId'] : null;
        $userId = (isset($parameter['userId'])) ? $parameter['userId'] : null;

        $joinDateFrom = (isset($parameter['joinDateFrom'])) ? $parameter['joinDateFrom'] : null;
        $joinDateTo = (isset($parameter['joinDateTo'])) ? $parameter['joinDateTo'] : null;
        $joinDate = (isset($parameter['joinDate'])) ? $parameter['joinDate'] : null;

        $fromDate = (isset($parameter['fromDate'])) ? $parameter['fromDate'] : null;
        $toDate = (isset($parameter['toDate'])) ? $parameter['toDate'] : null;

        $orderByArr = (isset($parameter['orderBy'])) ? $parameter['orderBy'] : array();
        $selectRaw = (isset($parameter['selectRaw'])) ? $parameter['selectRaw'] : null;

        $ignoreIdArr = (isset($parameter['ignoreIds'])) ? $parameter['ignoreIds'] : array();
        $ignoreDesignationArr = (isset($parameter['ignoreDesignations'])) ? $parameter['ignoreDesignations'] : array();
        $ignoreDepartmentArr = (isset($parameter['ignoreDepartments'])) ? $parameter['ignoreDepartments'] : array();
        $alies = (isset($parameter['alies'])) ? $parameter['alies'] : '';

        if (count($branchIdArr) > 0) {
            $selectBranchArr = $branchIdArr;
        } else {
            $selectBranchArr = Common::getBranchIdsForAllSection([
                'companyId'     => $companyId,
                'branchId'      => $branchId,
                'zoneId'      => $zoneId,
                'regionId'      => $regionId,
                'areaId'      => $areaId,
            ]);
        }

        // dd($userInfo, $statusArray, $loginUserDeptId);
        $employeeQuery = DB::table('hr_employees')
            ->where('is_delete', 0)
            ->whereIn('branch_id', $selectBranchArr)

            ->where(function($perQuery) use ($userInfo, $statusArray, $loginUserDeptId, $selectBranchArr, $alies){
                ## Calling Permission Query Function
                self::permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $selectBranchArr, $alies);
            })

            ->where(function ($query1) use ($designationId, $departmentId, $gender, $designationIdArr, $departmentIdArr, $status, $employeeId, $employeeIdArr) {

                if (!empty($employeeId)) {
                    $query1->where('id', $employeeId);
                }

                if (count($employeeIdArr) > 0) {
                    $query1->whereIn('id', $employeeIdArr);
                }

                if (!empty($designationId)) {
                    $query1->where('designation_id', $designationId);
                }

                if (count($designationIdArr) > 0) {
                    $query1->whereIn('designation_id', $designationIdArr);
                }

                if (!empty($departmentId)) {
                    $query1->where('department_id', $departmentId);
                }

                if (count($departmentIdArr) > 0) {
                    $query1->whereIn('department_id', $departmentIdArr);
                }

                if (!empty($gender)) {
                    $query1->where('gender', $gender);
                }

                if (!empty($status)) {
                    $query1->where('status', $status);
                }
            })
            ->where(function ($query2) use ($isActive, $roleId, $userId) {
                if ($isActive != null) {
                    $query2->where('is_active', $isActive);
                }

                if ($roleId != null) {
                    $userIds = DB::table('gnl_sys_users')->where([['is_delete', 0], ['is_active', 1], ['sys_user_role_id', $roleId]])->pluck('emp_id')->toArray();

                    if (count($userIds) > 0) {
                        $query2->whereIn('id', $userIds);
                    }
                }

                if (!empty($userId)) {
                    $query2->where('user_id', $userId);
                }
            })
            ->where(function ($query3) use ($joinDate, $joinDateFrom, $joinDateTo) {
                if (!empty($joinDateFrom) && !empty($joinDateTo)) {
                    $query3->orWhereBetween('join_date', [$joinDateFrom, $joinDateTo]);
                }

                if (!empty($joinDateFrom) && empty($joinDateTo)) {
                    $query3->where('join_date', '>=', $joinDateFrom);
                }

                if (empty($joinDateFrom) && !empty($joinDateTo)) {
                    $query3->where('join_date', '<=', $joinDateTo);
                }

                if (!empty($joinDate)) {
                    $query3->where('join_date', $joinDate);
                }
            })
            ->where(function ($query4) use ($fromDate, $toDate) {
                if (!empty($fromDate) && !empty($toDate)) { ## During period a closing employee anbe ai condition diye
                    $query4->whereBetween('closing_date', [$fromDate, $toDate]);
                }

                ## active emoloyee ante hole only fromDate dite hobe, formDate & toDate both dile closing employee asbe

                if (!empty($fromDate)) { ## active employee anbe and
                    $query4->whereNull('closing_date');
                    $query4->orWhere('closing_date', '>=', $fromDate);
                }
            })
            ->where(function ($query5) use ($ignoreIdArr, $ignoreDesignationArr, $ignoreDepartmentArr) {

                if (count($ignoreIdArr) > 0) {
                    $query5->whereNotIn('id', $ignoreIdArr);
                }

                if (count($ignoreDesignationArr) > 0) {
                    $query5->whereNotIn('designation_id', $ignoreDesignationArr);
                }

                if (count($ignoreDepartmentArr) > 0) {
                    $query5->whereNotIn('department_id', $ignoreDepartmentArr);
                }
            })
            ->when(true, function ($query6) use ($orderByArr, $selectRaw) {
                if (count($orderByArr) > 0) {
                    foreach ($orderByArr as $orderBy) {
                        $query6->orderBy($orderBy[0], $orderBy[1]);
                    }
                } else {
                    $query6->orderBy('emp_code', 'ASC');
                }

                if (!empty($selectRaw)) {
                    $query6->selectRaw($selectRaw);
                } else {
                    $query6->selectRaw('*');
                }
            })
            ->get();

        /*
        $employeeQuery = DB::table('hr_employees')
            ->where('is_delete', 0)
            ->whereIn('branch_id', $selectBranchArr)
            ->where(function ($query) use ($designationId, $departmentId, $gender, $designationIdArr, $departmentIdArr, $status, $employeeId, $employeeIdArr) {

                if (!empty($employeeId)) {
                    $query->where('id', $employeeId);
                }

                if (count($employeeIdArr) > 0) {
                    $query->whereIn('id', $employeeIdArr);
                }

                if (!empty($designationId)) {
                    $query->where('designation_id', $designationId);
                }

                if (count($designationIdArr) > 0) {
                    $query->whereIn('designation_id', $designationIdArr);
                }

                if (!empty($departmentId)) {
                    $query->where('department_id', $departmentId);
                }

                if (count($departmentIdArr) > 0) {
                    $query->whereIn('department_id', $departmentIdArr);
                }

                if (!empty($gender)) {
                    $query->where('gender', $gender);
                }

                if (!empty($status)) {
                    $query->where('status', $status);
                }
            })
            ->where(function ($query2) use ($isActive, $roleId, $userId) {
                if ($isActive != null) {
                    $query2->where('is_active', $isActive);
                }

                if ($roleId != null) {
                    $userIds = DB::table('gnl_sys_users')->where([['is_delete', 0], ['is_active', 1], ['sys_user_role_id', $roleId]])->pluck('emp_id')->toArray();

                    if (count($userIds) > 0) {
                        $query2->whereIn('id', $userIds);
                    }
                }

                if (!empty($userId)) {
                    $query2->where('user_id', $userId);
                }
            })
            ->where(function ($query3) use ($joinDate, $joinDateFrom, $joinDateTo) {
                if (!empty($joinDateFrom) && !empty($joinDateTo)) {
                    $query3->whereBetween('join_date', [$joinDateFrom, $joinDateTo]);
                }

                if (!empty($joinDateFrom) && empty($joinDateTo)) {
                    $query3->where('join_date', '>=', $joinDateFrom);
                }

                if (empty($joinDateFrom) && !empty($joinDateTo)) {
                    $query3->where('join_date', '<=', $joinDateTo);
                }

                if (!empty($joinDate)) {
                    $query3->where('join_date', $joinDate);
                }
            })
            ->where(function ($query3) use ($fromDate, $toDate) {
                if (!empty($fromDate) && !empty($toDate)) { ## During period a closing employee anbe ai condition diye
                    $query3->whereBetween('closing_date', [$fromDate, $toDate]);
                }

                ## active emoloyee ante hole only fromDate dite hobe, formDate & toDate both dile closing employee asbe

                if (!empty($fromDate)) { ## active employee anbe and
                    $query3->whereNull('closing_date');
                    $query3->orWhere('closing_date', '>=', $fromDate);
                }
            })
            ->where(function ($query) use ($ignoreIdArr, $ignoreDesignationArr, $ignoreDepartmentArr) {

                if (count($ignoreIdArr) > 0) {
                    $query->whereNotIn('id', $ignoreIdArr);
                }

                if (count($ignoreDesignationArr) > 0) {
                    $query->whereNotIn('designation_id', $ignoreDesignationArr);
                }

                if (count($ignoreDepartmentArr) > 0) {
                    $query->whereNotIn('department_id', $ignoreDepartmentArr);
                }
            })
            ->when(true, function ($query4) use ($orderByArr, $selectRaw) {
                if (count($orderByArr) > 0) {
                    foreach ($orderByArr as $orderBy) {
                        $query4->orderBy($orderBy[0], $orderBy[1]);
                    }
                } else {
                    $query4->orderBy('emp_code', 'ASC');
                }

                if (!empty($selectRaw)) {
                    $query4->selectRaw($selectRaw);
                } else {
                    $query4->selectRaw('*');
                }
            })
            ->get();
        */

        return $employeeQuery;
        // return $selectEmployee;
    }


    ##=======================> Start to  02-04-2023  <====================================================


    ##===============> Start Working For Year, Month Name, Dates  <=====================
    /*
        Get 'Month Name, Date And Days'
        Parameter StartDate & EndDate
    */
    /**
     * Calculate dates and corresponding days grouped by month between the given start and end dates.
     *
     * @param DateTime $startDate The start date for the calculation.
     * @param DateTime $endDate The end date for the calculation.
     *
     * @return array Returns a nested associative array where keys are month names, and values are arrays
     *               containing dates (Y-m-d) as keys and corresponding days (D) as values.
     */
    public static function getMonthNameDatesDays($startDate, $endDate)
    {

        ## Date Calculation Start
        $getMonthDate = array();
        $tempDate = clone $startDate;

        $getMonthDate[$tempDate->format('F')][$tempDate->format('Y-m-d')] = $tempDate->format('D');
        while ($tempDate <= $endDate) {
            $date = $tempDate->format('Y-m-d');
            $day = $tempDate->format('D');
            $getMonthDate[$tempDate->format('F')][$date] = $day;
            $tempDate = (($tempDate))->modify('+1 day');
            $tempDate++;
        }
        ## Date Calculation End

        return $getMonthDate;
    }


    /*
        Get 'Dates And Days'
        Parameter StartDate & EndDate
    */
    /**
     * Calculate dates and corresponding days between the given start and end dates.
     *
     * @param DateTime $startDate The start date for the calculation.
     * @param DateTime $endDate The end date for the calculation.
     *
     * @return array Returns an associative array where keys are dates (Y-m-d) and values are corresponding days (D).
     */
    public static function getDateAndDays($startDate, $endDate)
    {

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
        ## Date And Day Calculation End

        return $monthDates;
    }

    ##===============> End Working For Year, Month Name, Dates  <=====================


    ## ========  Start Common Query For Reports  ==========

    ## Attendance Query
    ## Parameter EmployeeIDArray, StartDate & EndDate
    /**
     * Retrieve attendance data based on employee IDs and a date range.
     *
     * @param array $employeeIdArr An array of employee IDs.
     * @param string $monthStartDate The start date of the month for filtering attendance data.
     * @param string $monthEndDate The end date of the month for filtering attendance data.
     *
     * @return \Illuminate\Support\Collection Returns a collection of attendance data including employee ID, date, and time.
     */
    public static function queryGetAttendanceData($employeeIdArr, $monthStartDate, $monthEndDate)
    {

        return DB::table('hr_attendance')
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
    }

    ## Movement Query
    ## Parameter BranchArray, EmployeeIDArray, StartDate & EndDate
    /**
     * Retrieve movement data based on selected branches, employee IDs, and date range.
     *
     * @param array $selBranchArr An array of selected branch IDs.
     * @param array $employeeIdArr An array of employee IDs.
     * @param string $monthStartDate The start date of the month for filtering movement data.
     * @param string $monthEndDate The end date of the month for filtering movement data.
     *
     * @return \Illuminate\Support\Collection Returns a collection of movement data including employee ID, reason, application type, movement date, start time, and end time.
     */
    public static function queryGetMovementData($selBranchArr =[], $employeeIdArr = [], $monthStartDate = '', $monthEndDate = '')
    {

        return DB::table('hr_app_movements')
            ->where([['is_delete', 0], ['is_active', 1]])
            // ->whereNotIn('reason', ['others', 'other', 'personal','Others', 'Other', 'Personal'])
            // ->whereIn('id', $reasonArr)
            ->whereIn('application_for', ['late', 'absent'])
            ->whereIn('branch_id', $selBranchArr)
            ->whereIn('emp_id', $employeeIdArr)->orWhere([['emp_id', 0], ['department_id', 0], ['is_delete', 0], ['is_active', 1]])
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['movement_date', '>=', $monthStartDate], ['movement_date', '<=', $monthEndDate]]);
                }
            })
            ->selectRaw('emp_id, reason, application_for, movement_date, start_time, end_time')
            ->get();
    }

    ## Leave Query
    ## Parameter BranchArray, EmployeeIDArray, StartDate & EndDate
    /**
     * Retrieve leave data based on selected branches, employee IDs, and date range.
     *
     * @param array $selBranchArr An array of selected branch IDs.
     * @param array $employeeIdArr An array of employee IDs.
     * @param string $monthStartDate The start date of the month for filtering leave data.
     * @param string $monthEndDate The end date of the month for filtering leave data.
     *
     * @return \Illuminate\Support\Collection Returns a collection of leave data including employee ID, start date, end date, leave category ID, reason, and short form of leave category.
     */
    public static function queryGetLeaveData($selBranchArr, $employeeIdArr, $monthStartDate, $monthEndDate)
    {

        // return DB::table('hr_app_leaves')
        //     ->where([['is_delete', 0], ['is_active', 1]])
        //     ->whereIn('branch_id', $selBranchArr)
        //     ->whereIn('emp_id', $employeeIdArr)->orWhere([['emp_id', 0], ['department_id', 0],['is_delete', 0], ['is_active', 1]])
        //     ->where(function ($query) use ($monthStartDate, $monthEndDate) {
        //         if (!empty($monthStartDate) && !empty($monthEndDate)) {
        //             $query->where([['date_from', '>=', $monthStartDate], ['date_from', '<=', $monthEndDate]]);
        //             $query->orWhere([['date_to', '>=', $monthStartDate], ['date_to', '<=', $monthEndDate]]);
        //         }
        //     })
        //     ->selectRaw('emp_id, date_from, date_to, leave_cat_id, reason')
        //     ->get();

        // dd($monthStartDate, $monthEndDate);

        return DB::table('hr_app_leaves as hal')
            ->where([['hal.is_delete', 0], ['hal.is_active', 1]])

            ->join('hr_leave_category as lcat', 'lcat.id', '=', 'hal.leave_cat_id')

            ->where(function ($query2) use ($employeeIdArr, $selBranchArr) {
                $query2->whereIn('hal.branch_id', $selBranchArr);
                $query2->whereIn('hal.emp_id', $employeeIdArr);
                $query2->orWhereIn('hal.emp_id', [0]);
            })
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['hal.date_from', '>=', $monthStartDate], ['hal.date_from', '<=', $monthEndDate]]);
                    $query->orWhere([['hal.date_to', '>=', $monthStartDate], ['hal.date_to', '<=', $monthEndDate]]);
                }
            })
            ->selectRaw('hal.emp_id, hal.date_from, hal.date_to, hal.leave_cat_id, hal.reason, lcat.short_form')
            ->get();
    }

    /**
     * Retrieve leave category details, including allocated leave, effective dates, short form, and consume policy.
     *
     * @return \Illuminate\Support\Collection Returns a collection of leave category details.
     */
    public static function queryGetLeaveCategoryDetails()
    {
        // return DB::table("hr_leave_category_details as lCatD")
        // ->join('hr_leave_category as lCat', 'lCatD.leave_cat_id', '=', 'lCat.id')
        // ->select('lCatD.leave_cat_id', 'lCatD.allocated_leave', 'lCatD.effective_date_from', 'lCatD.effective_date_to', 'lCat.short_form')
        // // ->groupBy("lCat.short_form")
        // ->groupBy("lCatD.leave_cat_id")
        // ->get();

        return DB::table("hr_leave_category_details as lCatD")
            ->join('hr_leave_category as lCat', function ($join) {
                $join->on('lCatD.leave_cat_id', '=', 'lCat.id')
                    ->where('lCat.is_delete', 0)
                    ->where('lCat.is_active', 1);
            })

            ->select('lCatD.leave_cat_id', 'lCatD.allocated_leave', 'lCatD.effective_date_from', 'lCatD.effective_date_to', 'lCat.short_form', 'lCatD.consume_policy')
            ->groupBy("lCatD.leave_cat_id")
            ->get();
    }

    ## Attendance Rules Query
    ## Parameter StartDate & EndDate
    /**
     * Retrieve attendance rules data based on the specified month start and end dates.
     *
     * @param string|null $monthStartDate The start date of the month.
     * @param string|null $monthEndDate The end date of the month.
     *
     * @return \Illuminate\Support\Collection Returns a collection of attendance rules data.
     */
    public static function queryGetAttendanceRulesData($monthStartDate, $monthEndDate)
    {

        return DB::table('hr_attendance_rules')
            ->where([['is_delete', 0], ['is_active', 1]])
            // ->whereIn('branch_id', $selBranchArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['eff_date_start', '<=', $monthEndDate]]);
                    $query->where(function ($query2) use ($monthStartDate) {
                        $query2->whereNull('eff_date_end');
                        $query2->orWhere([['eff_date_end', '>=', $monthStartDate]]);
                    });
                    // $query->where([['eff_date_start', '>=', $monthStartDate]]);
                    // $query->where(function ($query2) use ($monthEndDate) {
                    //     $query2->whereNull('eff_date_end');
                    //     $query2->orWhere([['eff_date_end', '<=', $monthEndDate]]);
                    // });
                }
            })->get();
    }


    ## Attendance Late Rules Query
    ## Parameter StartDate & EndDate
    /**
     * Retrieve attendance late rules data based on the specified month start and end dates.
     *
     * @param string|null $monthStartDate The start date of the month.
     * @param string|null $monthEndDate The end date of the month.
     *
     * @return \Illuminate\Support\Collection Returns a collection of attendance late rules data.
     */
    public static function queryGetAttendanceLateRulesData($monthStartDate, $monthEndDate)
    {

        return DB::table('hr_attendance_late_rules')
            ->where([['is_delete', 0], ['is_active', 1], ['eff_date_end', null]])
            // ->whereIn('branch_id', $selBranchArr)
            ->where(function ($query) use ($monthStartDate, $monthEndDate) {
                if (!empty($monthStartDate) && !empty($monthEndDate)) {
                    $query->where([['eff_date_start', '<=', $monthEndDate]]);
                    $query->where(function ($query2) use ($monthStartDate) {
                        $query2->whereNull('eff_date_end');
                        $query2->orWhere([['eff_date_end', '>=', $monthStartDate]]);
                    });
                }
            })->get();
    }
    ## ========  End Common Query For Reports  ============

    ## =========== HR Payroll Common Function Start ==========
    public static function query_get_hr_payroll_settings_loan_data()
    {
        return DB::table('hr_payroll_settings_loan')->where([['is_delete', 0], ['is_active', 1]])->get();
    }

    public static function query_get_hr_payroll_settings_pf_data($id)
    {
        return DB::table('hr_payroll_settings_pf')->where([['id', $id], ['is_delete', 0], ['is_active', 1]])->get();
        // return DB::table('hr_payroll_settings_pf')->where([['id', $id],['is_delete', 0], ['is_active', 1],['effective_date', '<=',  $startEffDate]])->get();
    }

    /**
     * Retrieve payroll settings workflow data for a specific ID, grade, and level.
     *
     * @param int $id The ID of the payroll settings workflow.
     * @param int $gradeId The grade ID for filtering the data.
     * @param int $levelId The level ID for filtering the data.
     *
     * @return \Illuminate\Support\Collection Returns a collection of payroll settings workflow data.
     */
    public static function query_get_hr_payroll_settings_wf_data($id, $gradeId, $levelId)
    {
        $getWfData = DB::table('hr_payroll_settings_wf as wf')
            ->where([['wf.id', $id], ['wf.is_active', 1], ['wf.is_delete', 0]])
            ->leftJoin('hr_payroll_settings_wf_details as wfd', 'wf.id', '=', 'wfd.wf_id')
            ->where(function ($query) use ($gradeId, $levelId) {

                // if (!empty($gradeId) && empty($levelId)) {
                //     $query->where('wfd.grade', $gradeId);
                //     $query->orWhere('wfd.grade', 0);
                //     $query->orWhere('wfd.grade', null);

                // }elseif (!empty($gradeId) && !empty($levelId)) {
                //     $query->where('wfd.grade', $gradeId);
                //     $query->orWhere('wfd.level', $levelId);
                //     $query->orWhere([['wfd.grade', 0], ['wfd.level', 0]]);
                //     $query->orWhere([['wfd.grade', null], ['wfd.level', null]]);
                // }
                $query->where('wfd.grade', $gradeId);
                $query->orWhere('wfd.level', $levelId);
                $query->orWhere([['wfd.grade', 0], ['wfd.level', 0]]);
                $query->orWhere('wfd.grade', null);
            })
            ->where([['wfd.type', '!=', 'wf_contri']])
            ->select('wf.id', 'wf.interest_rate', 'wf.effective_date', DB::raw('SUM(wfd.amount) as amount'), 'wfd.grade', 'wfd.level')
            ->groupBy('wfd.grade')
            ->havingRaw('COUNT(wfd.wf_id) >= 1')
            ->get();

        return $getWfData;
    }


    public static function query_get_hr_payroll_settings_pension_data($epsId)
    {
        return DB::table('hr_payroll_settings_pension_setting')
            ->where([['id', $epsId], ['is_delete', 0], ['is_active', 1]])->get();
    }

    public static function query_get_hr_payroll_settings_pension_details_data()
    {
        return DB::table('hr_payroll_settings_pension_details')->get();
    }

    public static function query_get_hr_payroll_settings_osf_data($startEffDate, $osfId)
    {
        return DB::table('hr_payroll_settings_osf')->where([['id', $osfId], ['is_delete', 0], ['is_active', 1]])->get();
    }

    public static function query_get_hr_payroll_settings_insurance_data($startEffDate, $incId)
    {
        return DB::table('hr_payroll_settings_insurance')->where([['id', $incId], ['is_delete', 0], ['is_active', 1]])->get();
    }

    /**
     * Retrieve the active and non-deleted payroll payscale data.
     *
     * @return \Illuminate\Support\Collection Returns a collection of payroll payscale data.
     */
    public static function query_get_hr_payroll_payscale_data()
    {
        return DB::table('hr_payroll_payscale')->where([['is_delete', 0], ['is_active', 1]])->get();
    }


    ## Get Pay Scale Year Start Date By Pay Scale Year ID
    /**
     * Get the effective start date of a payscale year based on payscale ID.
     *
     * @param int|null $payscale_id The ID of the payscale.
     *
     * @return string|null Returns the effective start date of the payscale year or null if no data is found.
     */
    public static function getPayScaleYearStartDateByPayScaleYearID($payscale_id = null)
    {
        $payScaleData = DB::table('hr_payroll_payscale')->where([['id', $payscale_id], ['is_delete', 0], ['is_active', 1]])->first();
        return !empty($payScaleData->eff_date_start) ? $payScaleData->eff_date_start : null;
    }

    ## =========== PF Pyroll Start ============
    /**
     * Get PF (Provident Fund) data based on company, project, and payscale ID.
     *
     * @param int $companyId The ID of the company.
     * @param int $projectId The ID of the project.
     * @param int $payscale_id The ID of the payscale.
     *
     * @return \stdClass|null Returns an object containing PF data or null if no data is found.
     */
    public static function getPfData($companyId, $projectId, $payscale_id)
    {
        $getPfData = DB::table('hr_payroll_settings_pf as pf')
            ->where([['pf.is_active', 1], ['pf.is_delete', 0]])
            ->where([['pf.company_id', $companyId], ['pf.project_id', $projectId]])
            ->where('pf.effective_date', '<=', self::getPayScaleYearStartDateByPayScaleYearID($payscale_id))
            ->orderBy('effective_date', 'desc')
            ->select('id', 'calculation_amount', 'effective_date', 'rec_type_ids')->first();

        return $getPfData;
    }
    ## =========== PF Pyroll End ============

    ## =========== WF Pyroll Start ============
    /**
     * Get WF (Welfare Fund) data based on company, project, grade, level, and payscale ID.
     *
     * @param int $companyId The ID of the company.
     * @param int $projectId The ID of the project.
     * @param int $gradeId The ID of the grade.
     * @param int $levelId The ID of the level.
     * @param int $payscale_id The ID of the payscale.
     *
     * @return \stdClass|null Returns an object containing WF data or null if no data is found.
     */
    public static function getWfData($companyId, $projectId, $gradeId, $levelId, $payscale_id)
    {

        $getWfData = DB::table('hr_payroll_settings_wf as wf')
            ->where([['wf.is_active', 1], ['wf.is_delete', 0]])
            ->where('wf.effective_date', '<=', self::getPayScaleYearStartDateByPayScaleYearID($payscale_id))
            ->leftJoin('hr_payroll_settings_wf_details as wfd', 'wf.id', '=', 'wfd.wf_id')
            ->where(function ($query) use ($companyId, $projectId, $gradeId, $levelId) {
                $query->where([['wf.company_id', $companyId], ['wf.project_id', $projectId]]);

                $query->where('wfd.grade', $gradeId);
                $query->orWhere('wfd.level', $levelId);
                $query->orWhere('wfd.grade', 0);
                $query->orWhere('wfd.level', 0);
            })
            ->where([['wfd.type', '!=', 'wf_contri']])
            ->select('wf.id', 'wf.interest_rate', 'wf.effective_date', DB::raw('SUM(wfd.amount) as amount'), 'wfd.grade', 'wfd.level', 'rec_type_ids')
            ->groupBy('wfd.wf_id')
            ->havingRaw('COUNT(wfd.grade) >= 1')
            ->orderBy('wf.effective_date', 'desc')->first();

        // ss($getWfData);

        return $getWfData;
    }
    ## =========== WF Pyroll End ============

    ## =========== EPS Pyroll Start ============
    /**
     * Get EPS (Employee Pension Scheme) data based on company, project, grade, and payscale ID.
     *
     * @param int $companyId The ID of the company.
     * @param int $projectId The ID of the project.
     * @param int $gradeId The ID of the grade.
     * @param int $payscale_id The ID of the payscale.
     *
     * @return \stdClass|null Returns an object containing EPS data or null if no data is found.
     */
    public static function getEpsData($companyId, $projectId, $gradeId, $payscale_id)
    {
        $getEpsData = DB::table('hr_payroll_settings_pension_setting as eps')
            ->where([['eps.is_active', 1], ['eps.is_delete', 0], ['grade', $gradeId]])
            ->where('eps.effective_date', '<=', self::getPayScaleYearStartDateByPayScaleYearID($payscale_id))
            ->where([['eps.company_id', $companyId], ['eps.project_id', $projectId]])->orderBy('effective_date', 'desc')
            ->select('id', 'amount', 'effective_date', 'rec_type_ids')->first();
        return $getEpsData;
    }
    ## =========== EPS Pyroll End ============

    ## =========== OSF Pyroll Start ============
    /**
     * Get OSF (Other Service Facility) data based on company, project, and payscale ID.
     *
     * @param int $companyId The ID of the company.
     * @param int $projectId The ID of the project.
     * @param int $payscale_id The ID of the payscale.
     *
     * @return \stdClass|null Returns an object containing OSF data or null if no data is found.
     */
    public static function getOsfData($companyId, $projectId, $payscale_id)
    {
        $getOsfData = DB::table('hr_payroll_settings_osf as osf')
            ->where([['osf.is_active', 1], ['osf.is_delete', 0]])
            ->where('osf.effective_date', '<=', self::getPayScaleYearStartDateByPayScaleYearID($payscale_id))
            ->where([['osf.company_id', $companyId], ['osf.project_id', $projectId]])->orderBy('effective_date', 'desc')
            ->select('osf.id', 'osf.calculation_amount', 'osf.effective_date', 'rec_type_ids')->first();
        return $getOsfData;
    }
    ## =========== OSF Pyroll End ============

    ## =========== Insurance Pyroll Start ============
    /**
     * Get insurance data based on company, project, and payscale ID.
     *
     * @param int $companyId The ID of the company.
     * @param int $projectId The ID of the project.
     * @param int $payscale_id The ID of the payscale.
     *
     * @return \stdClass|null Returns an object containing insurance data or null if no data is found.
     */
    public static function getInsuranceData($companyId, $projectId, $payscale_id)
    {
        $getIncData = DB::table('hr_payroll_settings_insurance as inc')
            ->where([['inc.is_active', 1], ['inc.is_delete', 0]])
            ->where('inc.effective_date', '<=', self::getPayScaleYearStartDateByPayScaleYearID($payscale_id))
            ->where([['inc.company_id', $companyId], ['inc.project_id', $projectId]])->orderBy('effective_date', 'desc')
            ->select('inc.id', 'inc.calculation_amount', 'inc.effective_date', 'rec_type_ids')->first();

        return $getIncData;
    }
    ## =========== Insurance Pyroll End ============


    ## =========== HR Payroll Common Function End ============


    ## =========== Apply_UPDATED_ATTENDANCE_LATE_RULES Start ============
    /**
     * Calculate the number of LWP (Leave Without Pay) based on updated attendance rules.
     *
     * @param string $getFromLpBreakdown JSON-encoded breakdown data
     * @param int $totalLp Total leave period
     * @return int The number of LWP calculated
     */
    public static function getDataFromUpdatedAttendanceRules($getFromLpBreakdown, $totalLp = 0)
    {

        if ($totalLp > 0) {

            $lpBreakdownArray = [];
            $lp_breakdown_data = json_decode($getFromLpBreakdown, true);
            foreach ($lp_breakdown_data as $item) {
                $key = key($item);
                $getFromLpBreakdown = current($item);
                $lpBreakdownArray[] = [$key => intval($getFromLpBreakdown)];
            }

            $loopCount = 0;
            $let_LP = $totalLp;
            $takeLWP = 0;
            foreach ($lpBreakdownArray as $key1 => $val1) {

                $array_keys = array_keys($lpBreakdownArray);
                $lastKey = end($array_keys);

                if ($loopCount == $key1 && $lastKey != $key1) {

                    foreach ($lpBreakdownArray[$key1] as $key2 => $val2) {

                        $let_LP -= $key2;
                        $takeLWP += $val2;
                        // echo "<br>key 1 = ".$key1." -- Key 2 =".$key2." -- Result = ".$takeLWP."Remain = ".$let_LP;
                        // echo "<br>";
                    }
                } elseif ($loopCount == $key1 && $lastKey == $key1) {

                    // $lll = $lpBreakdownArray[$lastKey];
                    $key3 = key($lpBreakdownArray[$lastKey]);
                    $val3 = $lpBreakdownArray[$lastKey][$key3];

                    while ($let_LP >=  $key3) {
                        $let_LP -= $key3;
                        $takeLWP += $val3;

                        // echo "<br>key 1 = ".$key1." -- Key 3 =".$key3." -- Result = ".$takeLWP."Remain = ".$let_LP;
                        // echo "<br>";

                    }
                }

                $loopCount++;
            }

            return $takeLWP;
        } else {
            return 0;
        }
    }
    
    public static function getDataFromUpdatedAttendanceRules_Backup($getFromLpBreakdown, $totalLp)
    {
        if ($totalLp > 0) {
            $lpBreakdownArray = json_decode($getFromLpBreakdown, true);

            $result = array_sum(array_reduce(
                array_map(
                    function ($item) use (&$totalLp) {
                        [$key] = array_keys($item);
                        $value = current($item);

                        return array_fill(0, $totalLp >= $key ? $totalLp / $key : 0, $value);
                    },
                    $lpBreakdownArray
                ),
                'array_merge',
                []
            ));

            return $result;
        } else {
            return 0;
        }
    }
    ## =========== Apply_UPDATED_ATTENDANCE_LATE_RULES End ============

    ## =========== Fiscal Year Data Start ============
    public static function getFiscalYearData($company_id = 1, $type = "FFY")
    {
        return DB::table('gnl_fiscal_year')
            ->where([['is_active', 1], ['is_delete', 0], ['company_id', $company_id]])
            ->whereIn('fy_for', ['BOTH', $type])
            ->orderBy('fy_name', 'asc')
            ->get();
    }
    ## =========== Pay Scale YearData End ============

    ## =========== Fiscal Year Data Start ============
    // public static function getFiscalYearData($company_id = 1, $type = "FFY"){
    //     return DB::table('gnl_fiscal_year')
    //     ->where([['is_active', 1], ['is_delete', 0],['company_id', $company_id],])
    //     ->whereIn('fy_for', ['BOTH', $type])
    //     ->orderBy('fy_name','asc')
    //     ->get();
    // }
    ## =========== Fiscal Year Data End ============

    ## =========== Attendance Date And Time Create Start ==========
    public static function AttendanceDateCreate($attendanceDateTime)
    {
        // $decimalNumber = 45045.381134259;
        $baseDate = new DateTime('1899-12-30'); // The base date in Excel's date system

        $days = floor($attendanceDateTime);
        $hours = floor(($attendanceDateTime - $days) * 24);
        $minutes = floor((($attendanceDateTime - $days) * 24 - $hours) * 60);
        $seconds = floor(((((($attendanceDateTime - $days) * 24) - $hours) * 60) - $minutes) * 60);

        $interval = new \DateInterval("P{$days}D"); // Create a date interval for the number of days
        $baseDate->add($interval); // Add the interval to the base date

        // Set the time components
        $baseDate->setTime($hours, $minutes, $seconds);

        // Format the datetime as desired
        $formattedDateTime = $baseDate->format('Y-m-d H:i:s');
        // $formattedDateTime = $baseDate->format('d-m-Y h:i:s A');

        return $formattedDateTime;
        // return 1;
    }
    ## =========== Attendance Date And Time Create End ============


    // hr_app_resigns
    ## =========== Get Employee Resign Date Start ==========
    public static function getEmployeeResignDate($empId)
    {
        return DB::table('hr_app_resigns')
            ->where([['is_active', 1], ['is_delete', 0], ['emp_id', $empId],])->first();
    }
    ## =========== Get Employee Resign Date End ============

    public static function getUserAccesableData()
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


    ## Check Leave Type Start
    public static function checkLeaveType($leaveID)
    {
        $leaveDetails = DB::table('hr_leave_category_details as hrlcd')->where('leave_cat_id', $leaveID)->first();

        if ($leaveDetails->remaining_leave_policy == 'flash') {
            return 1;
        } else {
            return 0;
        }
    }
    ## Check Leave Type End

    ## LWP Count For Leave Consume & Balance Report Start
    /**
     * Calculate Leave Without Pay (LWP) Count for Leave Report
     *
     * Calculates the count of Leave Without Pay (LWP) for a specified employee during a given period. The function considers various factors such as attendance, movement, leave, and holidays.
     *
     * @param array $parametersData - An associative array containing parameters for the calculation.
     * @return int - The count of Leave Without Pay (LWP) based on the specified parameters.
     *
     * @throws \Exception - If an invalid date format is encountered during the calculations.
     *
     * @example
     * 
     * $parameters = [
     *     'selBranchArr' => [1, 2], // Selected branch IDs
     *     'companyId' => 1, // Company ID
     *     'branchId' => 2, // Branch ID
     *     'employeeIdArr' => [123, 456], // Employee ID array
     *     'empId' => 123, // Specific Employee ID
     *     'empResignDate' => '2023-01-01', // Employee resignation date (Y-m-d)
     *     'empJoinDate' => '2022-01-01', // Employee joining date (Y-m-d)
     *     'type' => 'opening', // Calculation type ('opening' or 'during')
     *     'flt_start_date' => '2022-06-01', // Filter start date (Y-m-d)
     *     'flt_end_date' => '2022-06-30', // Filter end date (Y-m-d)
     * ];
     *
     * $lwpCount = lwpCountForLeaveReport($parameters);
     * 
     *
     * @notes
     * - The function calculates the Leave Without Pay (LWP) count based on attendance, movement, leave, and holiday data.
     * - The parameters array must include relevant details such as selected branches, company and branch IDs, employee details, dates, and calculation type.
     * - The calculation type can be either 'opening' or 'during', specifying whether to calculate LWP for the opening or during the specified period.
     * - Ensure that the date formats in the parameters array are valid (Y-m-d) to avoid exceptions.
     * - The function returns the count of LWP based on the specified parameters.
     *
     * @throws \Exception - If an invalid date format is encountered during the calculations.
     */
    public static function lwpCountForLeaveReport($perametersData = []) {

        $selBranchArr = isset($perametersData['selBranchArr']) ? $perametersData['selBranchArr'] : [];
        $companyId = isset($perametersData['companyId']) ? $perametersData['companyId'] : null;
        $branchId = isset($perametersData['branchId']) ? $perametersData['branchId'] : null;
        $employeeIdArr = isset($perametersData['employeeIdArr']) ? $perametersData['employeeIdArr'] : [];
        $empId = isset($perametersData['empId']) ? $perametersData['empId'] : null;
        $empResignDate = isset($perametersData['empResignDate']) ? $perametersData['empResignDate'] : '';
        $empJoinDate = isset($perametersData['empJoinDate']) ? $perametersData['empJoinDate'] : '';
        $type = isset($perametersData['type']) ? $perametersData['type'] : 'opening';

        $startDate = isset($perametersData['flt_start_date']) ? $perametersData['flt_start_date'] : '';
        $endDate = isset($perametersData['flt_end_date']) ? $perametersData['flt_end_date'] : '';
        $fyStartDate = isset($perametersData['flt_start_date']) ? $perametersData['flt_start_date'] : '';
        $fyEndDate = isset($perametersData['flt_end_date']) ? $perametersData['flt_end_date'] : '';


        $startDate = (new DateTime($startDate))->format("Y-m-d");
        $endDate = (new DateTime($endDate))->format("Y-m-d");
        $year = date("Y", strtotime($startDate));
        $firstDateOfYear = date("$year-01-01");
        $firstDateOfYear = (new DateTime($firstDateOfYear))->format("Y-m-d");

        $getMonthDate_begin = new DateTime($firstDateOfYear);
        $getMonthDate_start = new DateTime($startDate);
        $getMonthDate_end = new DateTime($endDate);

        $empResignDate = new DateTime($empResignDate);
        $empJoinDate = new DateTime($empJoinDate);

        ## Attendance data
        $attendanceData = self::queryGetAttendanceData($employeeIdArr, $firstDateOfYear, $endDate);
        $employeeWiseAttendanceData = $attendanceData->groupBy(['emp_id', 'date'])->toArray();
        $empAttendanceData = (isset($employeeWiseAttendanceData[$empId])) ? $employeeWiseAttendanceData[$empId] : array();
        ## Attendance data End

        ## Movement Query Start
        if ("get_movement_query") {
            $movementData = self::queryGetMovementData($selBranchArr, $employeeIdArr, $firstDateOfYear, $endDate);
            $movementArr = $movementData->groupBy(['emp_id', 'movement_date'])->toArray();

            $empMovementData = (isset($movementArr[$empId])) ? $movementArr[$empId] : array();
            $allEmpMovementData = (isset($movementArr[0])) ? $movementArr[0] : array();
            $empMovementData = array_merge($empMovementData, $allEmpMovementData);
        }
        ## Movement Query End

        ## Leave Query Start
        if ("get_leave_query") {
            $leaveData = self::queryGetLeaveData($selBranchArr, $employeeIdArr, $firstDateOfYear, $endDate);
            $leaveArr = $leaveData->groupBy('emp_id')->toArray();
            $leaveCatIdArr = array_values(array_unique($leaveData->pluck('leave_cat_id')->toArray()));

            $empLeaveData = (isset($leaveArr[$empId])) ? $leaveArr[$empId] : array();
            $allLeaveData = (isset($leaveArr[0])) ? $leaveArr[0] : array();
            $empLeaveData = array_merge($empLeaveData, $allLeaveData);

            $leaveData = [];
            foreach ($empLeaveData as $rowLeave) {
                $leaveStartDate = $rowLeave->date_from;
                $leaveEndDate = $rowLeave->date_to;
                $leaveCatId = $rowLeave->leave_cat_id;

                $tempDate = $leaveStartDate;

                if ($leaveStartDate == $leaveEndDate) {
                    array_push($leaveData, $leaveStartDate);
                } else {
                    while (($tempDate <= $leaveEndDate)) {
                        array_push($leaveData, $tempDate);
                        $tempDate = date("Y-m-d", strtotime("+1 day", strtotime($tempDate)));
                    }
                }
            }

            $keys = array_fill_keys($leaveData, " ");
            $leaveData = array_intersect_key($keys, array_flip($leaveData));
        }
        ## Leave Query End

        ## Holiday Query
        $holidays = self::systemHolidays($companyId, $branchId, null, $firstDateOfYear, $endDate);
        $keys = array_fill_keys($holidays, " ");
        $holidaysData = array_intersect_key($keys, array_flip($holidays));

        if ("Marge Array") {
            // $mergedAllDataArray = array_merge($empAttendanceData, $empMovementData, $leaveData, $holidaysData);
            $mergedAllDataArray = array_merge($empAttendanceData, $leaveData, $holidaysData);
            ksort($mergedAllDataArray);
        }

        if ($type == 'during') {
            $lwpForDuring = 0;

            $duringGetMonthDate = self::getDateAndDays($getMonthDate_start, $getMonthDate_end);
            foreach ($duringGetMonthDate as $date => $value) {
                $runningDate = (new DateTime($date));

                // dd($mergedAllDataArray);

                if ($runningDate >= $getMonthDate_start && $runningDate <= $getMonthDate_end) {
                    if ($runningDate >= $empJoinDate && $runningDate <= $empResignDate) {
                        if (!isset($mergedAllDataArray[$date]) && !isset($empMovementData[$date])) {
                            $lwpForDuring++;
                        }
                    }
                }
            }
            return $lwpForDuring;
        } elseif ($type == 'opening') {
            $lwpForOpening = 0;
            $openingGetMonthDate = self::getDateAndDays($getMonthDate_begin, $getMonthDate_end);
            foreach ($openingGetMonthDate as $date => $value) {
                // dd($openingGetMonthDate);
                $runningDate = (new DateTime($date));
                if ($runningDate >= $getMonthDate_begin && $runningDate < $getMonthDate_start) {

                    if ($runningDate > $empJoinDate && $runningDate < $empResignDate) {
                        if (!isset($mergedAllDataArray[$date])  && !isset($empMovementData[$date])) {
                            $lwpForOpening += 1;
                        }
                    }
                }
            }

            // dd('1',$lwpForOpening);
            return $lwpForOpening;
        }
    }

    /**
     * Calculate LWP (Leave Without Pay) for a given employee during a specified period.
     *
     * @param array $parametersData
     * @return int
     */
    public static function lwpCountForLeaveReport_Backup_2($parametersData = []) {
        $startDate = new DateTime($parametersData['flt_start_date']);
        $endDate = new DateTime($parametersData['flt_end_date']);
        $empJoinDate = new DateTime($parametersData['empJoinDate']);
        $empResignDate = new DateTime($parametersData['empResignDate']);

        $selBranchArr = isset($parametersData['selBranchArr']) ? $parametersData['selBranchArr']: [];

        $employeeId = $parametersData['empId'];

        // Query Attendance, Movement, Leave, and Holidays data
        $attendanceData = self::queryGetAttendanceData($employeeId, $startDate, $endDate);
        $movementData = self::queryGetMovementData($selBranchArr, $employeeId, $startDate, $endDate);
        $leaveData = self::queryGetLeaveData($employeeId, $startDate, $endDate);
        $holidaysData = self::systemHolidays($parametersData['companyId'], $parametersData['branchId'], null, $startDate, $endDate);

        // Merge all data
        $mergedAllDataArray = array_merge($attendanceData, $movementData, $leaveData, $holidaysData);
        ksort($mergedAllDataArray);

        $lwpCount = 0;

        // Check each date against employee's join and resign dates
        foreach (self::getDateAndDays($startDate, $endDate) as $date => $value) {
            $runningDate = new DateTime($date);

            if ($runningDate >= $empJoinDate && $runningDate <= $empResignDate) {
                if (!isset($mergedAllDataArray[$date]) && !isset($movementData[$date])) {
                    $lwpCount++;
                }
            }
        }

        return $lwpCount;
    }
    public static function lwpCountForLeaveReport_Backup($parametersData = []) {
        $startDate = new DateTime($parametersData['flt_start_date']);
        $endDate = new DateTime($parametersData['flt_end_date']);
        $empJoinDate = new DateTime($parametersData['empJoinDate']);
        $empResignDate = new DateTime($parametersData['empResignDate']);

        $selBranchArr = isset($parametersData['selBranchArr']) ? $parametersData['selBranchArr']: [];
    
        $employeeId = $parametersData['empId'];
    
        // dd($employeeId, $startDate, $endDate, $selBranchArr);
        // Query Attendance, Movement, Leave, and Holidays data
        $attendanceData = self::queryGetAttendanceData($employeeId, $startDate, $endDate);
        $movementData = self::queryGetMovementData($selBranchArr, $employeeId, $startDate, $endDate);
        $leaveData = self::queryGetLeaveData($selBranchArr, $employeeId, $startDate, $endDate);
        $holidaysData = self::systemHolidays($parametersData['companyId'], $parametersData['branchId'], null, $startDate, $endDate);
    
        // Merge all data
        $mergedAllDataArray = array_merge($attendanceData, $movementData, $leaveData, $holidaysData);
        ksort($mergedAllDataArray);
    
        $dateRange = array_flip(array_keys(self::getDateAndDays($startDate, $endDate)));
    
        // Check each date against employee's join and resign dates
        $lwpCount = count(array_filter($dateRange, function ($date) use ($empJoinDate, $empResignDate, $mergedAllDataArray, $movementData) {
            $runningDate = new DateTime($date);
    
            return $runningDate >= $empJoinDate &&
                   $runningDate <= $empResignDate &&
                   !isset($mergedAllDataArray[$date]) &&
                   !isset($movementData[$date]);
        }));
    
        return $lwpCount;
    }
    ## LWP Count For Leave Consume & Balance Report End


    ## =========== Get Rectuitment Type Start ============
    public static function getRectuitmentTypeData()
    {
        $rectuitmentData = DB::table('gnl_dynamic_form_value as gdfv')
            ->where([['gdfv.is_active', 1], ['gdfv.is_delete', 0], ['gdfv.type_id', 3], ['gdfv.form_id', 'HR.1']])
            ->select('gdfv.*')
            ->get();
        return $rectuitmentData;
    }
    ## =========== Get Rectuitment Type End   ============

    ## =========== Get Permanent and Non-Permanent  Start ============
    /**
     * Get Permanent and Non-Permanent Recruitment Data
     *
     * Retrieves data related to permanent and non-permanent recruitment from the dynamic form values.
     *
     * @return \Illuminate\Support\Collection - A collection containing dynamic form values for permanent and non-permanent recruitment.
     *
     * @example
     * 
     * $recruitmentData = getPermanentNonPermanentData();
     * 
     *
     * @notes
     * - The function queries the dynamic form values to fetch data related to permanent and non-permanent recruitment.
     * - The returned data is provided as an Illuminate\Support\Collection.
     * - Ensure that the dynamic form values exist and are correctly configured to get accurate recruitment data.
     */
    public static function getPermanentNonPermanentData()
    {
        $rectuitmentData = DB::table('gnl_dynamic_form_value as gdfv')
            ->where([['gdfv.is_active', 1], ['gdfv.is_delete', 0], ['gdfv.type_id', 3], ['gdfv.form_id', 'HR.3']])
            ->select('gdfv.*')
            ->get();
        return $rectuitmentData;
    }
    ## =========== Get Permanent and Non-Permanent End   ============



    /**
     * Get HR Payroll Deduction Data
     *
     * Retrieves the list of payroll deductions based on the provided recruitment type.
     *
     * @param int|null $recruitment_type - The ID of the recruitment type (default is null).
     *
     * @return array - An associative array containing the payroll deduction data for the specified recruitment type.
     *
     * @example
     * 
     * $recruitmentType = 1;
     * $deductionData = query_get_hr_payroll_deduction_data($recruitmentType);
     * 
     *
     * @notes
     * - The function queries the HR recruitment types and configuration menu to determine the payroll deductions based on the recruitment type.
     * - If the recruitment type is not provided, it defaults to null.
     * - The returned array is associative, with deduction names in uppercase as keys and corresponding values as array indices.
     * - Ensure that the recruitment type exists and is valid to get accurate payroll deduction data.
     */
    public static function query_get_hr_payroll_deduction_data($rectuitment_type = null)
    {

        $rec_type = DB::table('hr_recruitment_types')->where('id', intval($rectuitment_type))->first();
        $empType = !empty($rec_type) ? $rec_type->employee_type : 'permanent';
        $deductData = DB::table('hr_payroll_configuration_menu')->where([['is_delete', 0], ['is_active', 1]])
            ->select($empType)->first();


        if ($empType == 'permanent') {
            $deductionArr = !empty($deductData->permanent) ? explode(',', $deductData->permanent) : [];
        } else {
            $deductionArr = !empty($deductData->nonpermanent) ? explode(',', $deductData->nonpermanent) : [];
        }

        $outputArray = array_combine(
            array_map('strtoupper', $deductionArr),
            array_keys($deductionArr)
        );

        // ss($rec_type, $deductData, $deductionArr, $outputArray);

        return $outputArray;
    }

    /**
     * Generate Salary Structure (Main Code Backup)
     *
     * This function generates the salary structure based on the provided parameters.
     *
     * @param int $grade - The grade of the employee.
     * @param int $level - The level of the employee.
     * @param int $payscaleId - The ID of the pay scale associated with the salary structure.
     * @param int $recruitmentId - The ID of the recruitment type.
     * @param int $step - The step or iteration to calculate the salary structure.
     *
     * @return array|null - An array containing the generated salary structure for the specified step, or null if parameters are missing or invalid.
     *
     * @example
     * 
     * $grade = 2;
     * $level = 3;
     * $payscaleId = 5;
     * $recruitmentId = 1;
     * $step = 2;
     * $salaryStructure = genarateSalaryStructure_Main_Code_Backup($grade, $level, $payscaleId, $recruitmentId, $step);
     * 
     *
     * @notes
     * - Ensure that all required parameters are provided for accurate salary structure generation.
     * - The generated salary structure array includes details such as basic salary, allowances, deductions, and more for a specific step.
     * - The step parameter determines the iteration or stage of salary structure generation.
     * - If any required parameter is missing or invalid, the function returns null.
     * - This function is a backup, and it is advisable to use the latest version for accurate calculations.
     */
    ## Salary Structure Start
    public static function genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step)
    {
        // dd($grade, $level, $payscaleId, $recruitmentId, $step);
        if (empty($grade) || empty($level) || empty($payscaleId) || empty($recruitmentId) || empty($step) ) {
            return;
        }

        $payscaleYearsData = DB::table('hr_payroll_payscale')->where([['is_delete', 0], ['is_active', 1]])->first();
        $payscaleStartEffDate = $payscaleYearsData->eff_date_start;

        $salary_struct = DB::table('hr_payroll_salary_structure as ss')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0]])
            ->where([['ss.grade', $grade], ['ss.level', $level], ['ss.pay_scale_id', $payscaleId], ['ss.recruitment_type_id', $recruitmentId]])
            ->join('hr_payroll_salary_structure_details as ssd', function ($join) {
                $join->on('ss.id', '=', 'ssd.salary_structure_id');
            })->get();

        $allowance_id = $salary_struct->where('data_type', 'allowance')->pluck('allowance_type_id');
        $allowance = DB::table('hr_payroll_allowance')->where([['is_active', 1], ['is_delete', 0]])->whereIn('id', $allowance_id)->get();

        $headerData = [];
        $total_year = 0;
        $basic = 0;
        $incrementData = [];
        $allowance_data = [];
        $data = [];

        $salary_struct_data = DB::table('hr_payroll_salary_structure as ss')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0]])
            ->where([['ss.grade', $grade], ['ss.level', $level], ['ss.pay_scale_id', $payscaleId], ['ss.recruitment_type_id', $recruitmentId]])->first();


        $salary_structure_id = optional($salary_struct_data)->id;


        #####################> 18-06-2023 <##########################
        $pfId  = count($salary_struct) > 0 ? $salary_struct[0]->pf_id  : 0;
        $wfId  = count($salary_struct) > 0 ? $salary_struct[0]->wf_id  : 0;
        $epsId = count($salary_struct) > 0 ? $salary_struct[0]->ps_id  : 0;
        $osfId = count($salary_struct) > 0 ? $salary_struct[0]->osf_id : 0;
        $incId = count($salary_struct) > 0 ? $salary_struct[0]->inc_id : 0;

        $pfSettingData = self::query_get_hr_payroll_settings_pf_data($id = $pfId);
        $wfSettingData = self::query_get_hr_payroll_settings_wf_data($wfId, $grade, $level);
        $EpsSettingData = self::query_get_hr_payroll_settings_pension_data($epsId);
        $EpsSettingDetailsData = self::query_get_hr_payroll_settings_pension_details_data();
        $OsfSettingData = self::query_get_hr_payroll_settings_osf_data($payscaleStartEffDate, $osfId);
        $IncSettingData = self::query_get_hr_payroll_settings_insurance_data($payscaleStartEffDate, $incId);
        // ss($pfSettingData, $wfSettingData,$EpsSettingData);
        ####################> 18-06-2023 <###########################

        $recruitment_type = $recruitmentId;
        $deductionDataArr = self::query_get_hr_payroll_deduction_data($recruitment_type);
        // ss($deductionDataArr, $recruitment_type);

        if (count($salary_struct) > 0) {

            $ss = SalaryStructure::where([['grade', $grade], ['level', $level], ['pay_scale_id', $payscaleId], ['recruitment_type_id', $recruitmentId]])->first();
            if ($ss == null) {
                return;
            }

            $headerData['company']        = $ss->company()->comp_name;
            $headerData['designations']   = $ss->designations();
            // $headerData['recruitment_types']   = $recruitment_type;
            $headerData['recruitment_types']   = $ss->recruitmentType()->title;
            $headerData['project']        = $ss->project()->project_name;
            $headerData['grade'] = $salary_struct[0]->grade;
            $headerData['level'] = $salary_struct[0]->level;
            $headerData['basic'] = $salary_struct[0]->basic;
            $headerData['acting_benefit_amount'] = $salary_struct[0]->acting_benefit_amount;

            $no_of_inc_arr = [];
            $increment = [];
            $basic = $salary_struct[0]->basic;
            $salary_struct = $salary_struct->groupBy('data_type');
            $incrementData = $salary_struct['increment']->sortBy('no_of_inc');

            $i = 2;
            $increment[1] = 0;
            foreach ($incrementData as $n) {
                for (; $i <= $n->no_of_inc + 1; $i++) {
                    $increment[$i] = $n->amount;
                }
            }

            $total_year = $incrementData->last()->no_of_inc;

            foreach ($allowance as $al) {
                $allowance_data[$al->benifit_type_uid][$al->id] = $salary_struct['allowance']->where('allowance_type_id', $al->id)->first();
            }

            $data[1]['basic'] = $basic;

            for ($y = 1; $y <= ($total_year + 1); $y++) {
                $data[$y]['year'] = $y;
                $data[$y + 1]['basic'] = $data[$y]['basic'] + $increment[$y];
                $data[$y]['increment'] = $increment[$y];
                $data[$y]['total_basic'] = $data[$y]['basic'] + $increment[$y];

                $data[$y]['salary_structure_id'] = $salary_structure_id;
                $data[$y]['incrementPer'] = $pfSettingData[0]->calculation_amount;

                $allowanceTot = [];
                foreach ($allowance_data as $keyBen => $benType) {
                    $allowanceTot[$keyBen] = 0;
                    foreach ($benType as $key => $al) {

                        ## Get Allowance Name
                        $allowId = !empty($al->allowance_type_id) ? $al->allowance_type_id : null;
                        $getAllowance = DB::table('hr_payroll_allowance')->where([['is_active', 1], ['is_delete', 0]])->where('id', $allowId)->first();

                        $data[$y]['allowance'][$keyBen][$getAllowance->short_name] = ($al->calculation_type == 2) ? $al->amount : (($al->amount * $data[$y]['total_basic']) / 100);
                        $allowanceTot[$keyBen] += $data[$y]['allowance'][$keyBen][$getAllowance->short_name];
                    }
                }

                $data[$y]['total_gross_a'] = $data[$y]['total_basic']   + (isset($allowanceTot[1]) ? $allowanceTot[1] : 0);
                $data[$y]['total_gross_b'] = $data[$y]['total_gross_a'] + (isset($allowanceTot[2]) ? $allowanceTot[2] : 0);
                $data[$y]['total_gross_c'] = $data[$y]['total_gross_b'] + (isset($allowanceTot[3]) ? $allowanceTot[3] : 0);


                $deductionTot = [];
                $data[$y]['deduction'] = array();
                $totalDeduction = 0;
                foreach ($deductionDataArr as $dKey => $dValue) {
                    // ss($deductionDataArr);
                    $data[$y]['deduction'][$dKey] = array();

                    //================ Provident Fund (PF) Start ====================
                    if ($dKey == 'PF') {
                        // dd($pfSettingData);
                        if (count($pfSettingData) > 0 && !empty($pfSettingData[0])) {

                            if ($pfSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($pfSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? $pfSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ Provident Fund (PF) End ====================

                    //================ Welfare Fund  Start ====================
                    if ($dKey == 'WF') {
                        // dd('wf' ,$wfSettingData);
                        /*
                        if (count($wfSettingData) > 0 && !empty($wfSettingData[0])) {

                            if ($wfSettingData[0]->interest_rate != null ) {

                                $data[$y]['deduction'][$dKey] = !empty($wfSettingData[0]->interest_rate) ? round((($data[$y]['total_basic']) * ($wfSettingData[0]->interest_rate)) / 100) : 0;

                            }else{
                                $data[$y]['deduction'][$dKey] = !empty($wfSettingData[0]->amount) ? $wfSettingData[0]->amount : 0;
                            }
                        }else{
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        */

                        // dd($wfSettingData, count($wfSettingData));
                        if (count($wfSettingData) > 0 && !empty($wfSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = $wfSettingData[0]->amount;
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }

                        $totalDeduction += $data[$y]['deduction'][$dKey];

                        // ss($totalDeduction);
                    }
                    //================ Welfare Fund  End ====================


                    //================ Pension Scheme Setting Start ====================
                    if ($dKey == 'EPS') {
                        // dd($EpsSettingData);

                        if (count($EpsSettingData) > 0 && !empty($EpsSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = !empty($EpsSettingData[0]->amount) ? $EpsSettingData[0]->amount : 0;
                            $totalDeduction += $data[$y]['deduction'][$dKey];
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }

                        /*
                        $epsDetails = $EpsSettingDetailsData->where('pension_id', $EpsSettingData[0]->id)->pluck('rate')->toArray();
                        foreach($epsDetails as $epsKey => $epsValue){

                            if ( ($y-1) == $epsKey) {
                                $data[$y]['deduction'][$dKey] = !empty($epsValue) ? round((($data[$y]['total_basic']) * ($epsValue)) / 100) : 0;
                                $totalDeduction += $data[$y]['deduction'][$dKey];
                            }
                        }
                        */
                    }
                    //================ Pension Scheme Setting End ====================


                    //================ OSF Settings Start ====================
                    if ($dKey == 'OSF') {
                        // dd($OsfSettingData);
                        if (count($OsfSettingData) > 0 && !empty($OsfSettingData[0])) {
                            if ($OsfSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($OsfSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? $OsfSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ OSF Settings End ====================

                    //================ Insurance Start ====================
                    if ($dKey == 'INC') {
                        // dd($IncSettingData, count($IncSettingData));

                        if (count($IncSettingData) > 0 && !empty($IncSettingData[0])) {
                            if ($IncSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($IncSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? $IncSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];
                    }
                    //================ Insurance End ====================

                }
                $data[$y]['totalDeduction'] = $totalDeduction;

                ## Remove Element Which Not Define
                $tmpDeductioArr = $data[$y]['deduction'];
                foreach ($tmpDeductioArr as $tmpKey => $tmpVal) {
                    if ($tmpDeductioArr[$tmpKey] < 1 || empty($tmpDeductioArr[$tmpKey])) {
                        unset($data[$y]['deduction'][$tmpKey]);
                    }
                }
                $deductionDataArr = $data[$y]['deduction'];

            }

            unset($data[$total_year + 2]);

            return isset($data[$step]) ? $data[$step] : null;
            // dd($data[$step]);
        }


        //dd($salary_struct['increment']);
        //dd($data);
        //dd($total_year);
        //dd($allowance_data);
        //dd($incrementData);


    }
    ## Salary Structure End

    /**
     * Generate Salary Structure
     *
     * This function generates the salary structure based on the provided parameters such as grade, level, payscale ID, recruitment ID, and step.
     *
     * @param int $grade - The grade of the employee.
     * @param int $level - The level of the employee.
     * @param int $payscaleId - The ID of the payscale.
     * @param int $recruitmentId - The ID of the recruitment type.
     * @param int $step - The step in the salary structure.
     * @param bool $dynamicId (optional) - If true, dynamic IDs are used; otherwise, defaults to false.
     *
     * @return array|null - The generated salary structure data for the specified step.
     *
     * @example
     * 
     * $grade = 2;
     * $level = 3;
     * $payscaleId = 1;
     * $recruitmentId = 5;
     * $step = 3;
     * $dynamicId = true;
     *
     * $salaryStructure = genarateSalaryStructure($grade, $level, $payscaleId, $recruitmentId, $step, $dynamicId);
     * 
     *
     * @notes
     * - Ensure all parameters are provided and not empty for accurate salary structure generation.
     * - Dynamic IDs are optional and default to false.
     * - Returns null if the provided parameters are incomplete or invalid.
     * - The returned array includes details such as basic salary, allowances, deductions, and other related information.
     */
    ## Salary Structure Start
    public static function genarateSalaryStructure_Backup($grade, $level, $payscaleId, $recruitmentId, $step, $dynamicId = false)
    {
        // dd($grade, $level, $payscaleId, $recruitmentId, $step);
        if (empty($grade) || empty($level) || empty($payscaleId) || empty($recruitmentId) || empty($step) ) {
            return;
        }

        $payscaleYearsData = DB::table('hr_payroll_payscale')->where([['is_delete', 0], ['is_active', 1]])->first();
        $payscaleStartEffDate = $payscaleYearsData->eff_date_start;

        $salary_struct = DB::table('hr_payroll_salary_structure as ss')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0]])
            ->where([['ss.grade', $grade], ['ss.level', $level], ['ss.pay_scale_id', $payscaleId], ['ss.recruitment_type_id', $recruitmentId]])
            ->join('hr_payroll_salary_structure_details as ssd', function ($join) {
                $join->on('ss.id', '=', 'ssd.salary_structure_id');
            })->get();

        $allowance_id = $salary_struct->where('data_type', 'allowance')->pluck('allowance_type_id');
        $allowance = DB::table('hr_payroll_allowance')->where([['is_active', 1], ['is_delete', 0]])->whereIn('id', $allowance_id)->get();

        $headerData = [];
        $total_year = 0;
        $basic = 0;
        $incrementData = [];
        $allowance_data = [];
        $data = [];

        $salary_struct_data = DB::table('hr_payroll_salary_structure as ss')
            ->where([['ss.is_active', 1], ['ss.is_delete', 0]])
            ->where([['ss.grade', $grade], ['ss.level', $level], ['ss.pay_scale_id', $payscaleId], ['ss.recruitment_type_id', $recruitmentId]])->first();


        $salary_structure_id = optional($salary_struct_data)->id;


        #####################> 18-06-2023 <##########################
        $pfId  = count($salary_struct) > 0 ? $salary_struct[0]->pf_id  : 0;
        $wfId  = count($salary_struct) > 0 ? $salary_struct[0]->wf_id  : 0;
        $epsId = count($salary_struct) > 0 ? $salary_struct[0]->ps_id  : 0;
        $osfId = count($salary_struct) > 0 ? $salary_struct[0]->osf_id : 0;
        $incId = count($salary_struct) > 0 ? $salary_struct[0]->inc_id : 0;

        $pfSettingData = self::query_get_hr_payroll_settings_pf_data($id = $pfId);
        $wfSettingData = self::query_get_hr_payroll_settings_wf_data($wfId, $grade, $level);
        $EpsSettingData = self::query_get_hr_payroll_settings_pension_data($epsId);
        $EpsSettingDetailsData = self::query_get_hr_payroll_settings_pension_details_data();
        $OsfSettingData = self::query_get_hr_payroll_settings_osf_data($payscaleStartEffDate, $osfId);
        $IncSettingData = self::query_get_hr_payroll_settings_insurance_data($payscaleStartEffDate, $incId);
        // ss($pfSettingData, $wfSettingData,$EpsSettingData);
        ####################> 18-06-2023 <###########################

        $recruitment_type = $recruitmentId;
        $deductionDataArr = self::query_get_hr_payroll_deduction_data($recruitment_type);
        // ss($deductionDataArr, $recruitment_type);

        if (count($salary_struct) > 0) {

            $ss = SalaryStructure::where([['grade', $grade], ['level', $level], ['pay_scale_id', $payscaleId], ['recruitment_type_id', $recruitmentId]])->first();
            if ($ss == null) {
                return;
            }

            $headerData['company']        = $ss->company()->comp_name;
            $headerData['designations']   = $ss->designations();
            // $headerData['recruitment_types']   = $recruitment_type;
            $headerData['recruitment_types']   = $ss->recruitmentType()->title;
            $headerData['project']        = $ss->project()->project_name;
            $headerData['grade'] = $salary_struct[0]->grade;
            $headerData['level'] = $salary_struct[0]->level;
            $headerData['basic'] = $salary_struct[0]->basic;
            $headerData['acting_benefit_amount'] = $salary_struct[0]->acting_benefit_amount;

            $no_of_inc_arr = [];
            $increment = [];
            $basic = $salary_struct[0]->basic;
            $salary_struct = $salary_struct->groupBy('data_type');
            $incrementData = $salary_struct['increment']->sortBy('no_of_inc');

            $i = 2;
            $increment[1] = 0;
            foreach ($incrementData as $n) {
                for (; $i <= $n->no_of_inc + 1; $i++) {
                    $increment[$i] = $n->amount;
                }
            }

            $total_year = $incrementData->last()->no_of_inc;

            foreach ($allowance as $al) {
                $allowance_data[$al->benifit_type_uid][$al->id] = $salary_struct['allowance']->where('allowance_type_id', $al->id)->first();

            }
            // ss($allowance, $allowance_data);

            $data[1]['basic'] = $basic;

            for ($y = 1; $y <= ($total_year + 1); $y++) {
                $data[$y]['year'] = $y;
                $data[$y + 1]['basic'] = $data[$y]['basic'] + $increment[$y];
                $data[$y]['increment'] = $increment[$y];
                $data[$y]['total_basic'] = $data[$y]['basic'] + $increment[$y];

                $data[$y]['salary_structure_id'] = $salary_structure_id;
                $data[$y]['incrementPer'] = $pfSettingData[0]->calculation_amount;

                $data[$y]['acting_benefit'] = !empty($salary_struct_data->acting_benefit_amount) ? $salary_struct_data->acting_benefit_amount : 0;

                // ss($salary_struct_data->acting_benefit_amount);

                $allowanceTot = [];
                foreach ($allowance_data as $keyBen => $benType) {
                    $allowanceTot[$keyBen] = 0;
                    foreach ($benType as $key => $al) {
                        ## Get Allowance Name
                        $allowId = !empty($al->allowance_type_id) ? $al->allowance_type_id : null;
                        $getAllowance = DB::table('hr_payroll_allowance')->where([['is_active', 1], ['is_delete', 0]])->where('id', $allowId)->first();

                        if($dynamicId == true){
                            $data[$y]['allowance'][$keyBen][$getAllowance->id] = ($al->calculation_type == 2) ? $al->amount : (($al->amount * $data[$y]['total_basic']) / 100);
                            $allowanceTot[$keyBen] += $data[$y]['allowance'][$keyBen][$getAllowance->id];
                        }else{
                            $data[$y]['allowance'][$keyBen][$getAllowance->short_name] = ($al->calculation_type == 2) ? $al->amount : (($al->amount * $data[$y]['total_basic']) / 100);
                            $allowanceTot[$keyBen] += $data[$y]['allowance'][$keyBen][$getAllowance->short_name];
                        }
                    }
                }

                $data[$y]['total_gross_a'] = $data[$y]['total_basic']   + (isset($allowanceTot[1]) ? $allowanceTot[1] : 0);
                $data[$y]['total_gross_b'] = $data[$y]['total_gross_a'] + (isset($allowanceTot[2]) ? $allowanceTot[2] : 0);
                $data[$y]['total_gross_c'] = $data[$y]['total_gross_b'] + (isset($allowanceTot[3]) ? $allowanceTot[3] : 0);


                $deductionTot = [];
                $data[$y]['deduction'] = array();
                $totalDeduction = 0;
                foreach ($deductionDataArr as $dKey => $dValue) {
                    // ss($deductionDataArr);
                    $data[$y]['deduction'][$dKey] = array();

                    //================ Provident Fund (PF) Start ====================
                    if ($dKey == 'PF') {
                        // ss($pfSettingData);
                        if (count($pfSettingData) > 0 && !empty($pfSettingData[0])) {

                            if ($pfSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($pfSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($pfSettingData[0]->calculation_amount) ? $pfSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];

                        if($dynamicId){
                            $data[$y]['pf_org'] = intval($data[$y]['deduction'][$dKey]);
                            $data[$y]['pf_self'] = intval($data[$y]['deduction'][$dKey]);
                        }
                    }
                    //================ Provident Fund (PF) End ====================

                    //================ Welfare Fund  Start ====================
                    if ($dKey == 'WF') {
                        // ss($wfSettingData);
                        if (count($wfSettingData) > 0 && !empty($wfSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = intval($wfSettingData[0]->amount);
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];

                        if($dynamicId){
                            $wfDetails = DB::table('hr_payroll_settings_wf_details')->where('wf_id', $wfSettingData[0]->id)->get();
                            foreach ($wfDetails as $key => $value) {

                                // ss($wfDetails, $value);
                                if ($wfDetails[$key]->type == 'wf_contri') {
                                    $data[$y]['wf_org'] = $wfDetails[$key]->amount;
                                }
                                if ($wfDetails[$key]->type == 'wf_nrf') {
                                    $data[$y]['wf_self_non_refundable'] = $wfDetails[$key]->amount;
                                }
                                if ($wfDetails[$key]->type == 'wf_rf') {
                                    $data[$y]['wf_self_refundable'] = $wfDetails[$key]->amount;
                                }
                            }
                        }
                    }
                    //================ Welfare Fund  End ====================


                    //================ Pension Scheme Setting Start ====================
                    if ($dKey == 'EPS') {
                        if (count($EpsSettingData) > 0 && !empty($EpsSettingData[0])) {
                            $data[$y]['deduction'][$dKey] = !empty($EpsSettingData[0]->amount) ? $EpsSettingData[0]->amount : 0;
                            $totalDeduction += $data[$y]['deduction'][$dKey];
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }

                        if($dynamicId){
                            $data[$y]['eps'] = intval($data[$y]['deduction'][$dKey]);
                        }
                    }
                    //================ Pension Scheme Setting End ====================


                    //================ OSF Settings Start ====================
                    if ($dKey == 'OSF') {
                        // dd($OsfSettingData);
                        if (count($OsfSettingData) > 0 && !empty($OsfSettingData[0])) {
                            if ($OsfSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($OsfSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($OsfSettingData[0]->calculation_amount) ? $OsfSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];

                        if($dynamicId){
                            $data[$y]['osf_org'] = intval($data[$y]['deduction'][$dKey]);
                            $data[$y]['osf_self'] = intval($data[$y]['deduction'][$dKey]);
                        }
                    }
                    //================ OSF Settings End ====================

                    //================ Insurance Start ====================
                    if ($dKey == 'INC') {
                        // dd($IncSettingData, count($IncSettingData));

                        if (count($IncSettingData) > 0 && !empty($IncSettingData[0])) {
                            if ($IncSettingData[0]->calculation_type == 'percentage') {
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? round((($data[$y]['total_basic']) * ($IncSettingData[0]->calculation_amount)) / 100) : 0;
                            } else {
                                $data[$y]['deduction'][$dKey] = !empty($IncSettingData[0]->calculation_amount) ? $IncSettingData[0]->calculation_amount : 0;
                            }
                        } else {
                            $data[$y]['deduction'][$dKey] = 0;
                        }
                        $totalDeduction += $data[$y]['deduction'][$dKey];

                        if($dynamicId){
                            // ss($IncSettingData);
                            $data[$y]['insurance_org'] = intval($data[$y]['deduction'][$dKey]);
                            $data[$y]['insurance_self'] = intval($data[$y]['deduction'][$dKey]);
                        }
                    }
                    //================ Insurance End ====================

                }
                $data[$y]['totalDeduction'] = intval($totalDeduction);

                ## Remove Element Which Not Define
                $tmpDeductioArr = $data[$y]['deduction'];
                foreach ($tmpDeductioArr as $tmpKey => $tmpVal) {
                    if ($tmpDeductioArr[$tmpKey] < 1 || empty($tmpDeductioArr[$tmpKey])) {
                        unset($data[$y]['deduction'][$tmpKey]);
                    }
                }
                $deductionDataArr = $data[$y]['deduction'];

                if($dynamicId){
                    ## Security Money
                    $securityMoney = DB::table('hr_payroll_settings_security_money')
                        ->where([['is_active', 1], ['is_delete', 0]])
                        ->where([['grade_id', $grade], ['level_id', $level]])
                        ->first();
                    $data[$y]['security_money'] = !empty($securityMoney->amount) ? $securityMoney->amount : 0;

                    ## Self Loan
                    $loanData = DB::table('hr_payroll_settings_loan')
                        ->where([['is_active', 1], ['is_delete', 0]])->orderBy('id','desc')->first();
                    if(!empty($loanData)){
                        $data[$y]['vehicle_loan'][$loanData->vehicle_type] =  intval(round((($data[$y]['total_basic']) * ($loanData->intrest_rate)) / 100));
                    }

                    // ss($loanData);

                }

            }

            unset($data[$total_year + 2]);
            return isset($data[$step]) ? $data[$step] : null;
            // dd($data[$step]);
        }


    }
    ## Salary Structure End


    ## Get Auth User Department ID Strat
    public static function getUserDepartmentId($userId = null){
        if($userId == null){
            $userInfo = Auth::user();
            $userId = $userInfo['id'];
        }
        $empData = DB::table('hr_employees')->where([['id', $userId], ['is_delete', 0]])->first();
        return optional($empData)->department_id;
    }
    ## Get Auth User Department ID End


    /**
     * Get Leave Adjustment Query
     *
     * This function retrieves leave adjustment data based on the specified start and end dates.
     *
     * @param \Carbon\Carbon $startDate - The start date for leave adjustment data retrieval.
     * @param \Carbon\Carbon $endDate - The end date for leave adjustment data retrieval.
     *
     * @return \Illuminate\Database\Query\Builder - The query builder instance containing leave adjustment data.
     *
     * @example
     * 
     * $startDate = Carbon::parse('2023-01-01');
     * $endDate = Carbon::parse('2023-12-31');
     * $leaveAdjustmentQuery = getLeaveAdjustmentData($startDate, $endDate);
     * $leaveAdjustmentData = $leaveAdjustmentQuery->get();
     * 
     *
     * @notes
     * - Ensure that the start and end dates are provided in the proper Carbon format for accurate data retrieval.
     * - The returned query builder instance can be further customized or executed as needed.
     * - This function is designed to retrieve leave adjustment data for a specific fiscal year based on the provided date range.
     */
    ## Get Leave Adjustment Query Start
    public static function getLeaveAdjustmentData($startDate, $endDate){

        $startDate = ($startDate)->format('Y-m-d');
        $endDate = ($endDate)->format('Y-m-d');

        // ss($startDate, $endDate);

        $fiscaleYearData =  DB::table('gnl_fiscal_year')
                    ->where([['is_active', 1], ['is_delete', 0],['fy_for','LFY']])
                    ->where('fy_start_date',  $startDate)
                    ->first();
        $fiscaleYearId = optional($fiscaleYearData)->id;

        return DB::table('hr_app_leaves_adjustment')
                    ->where([['is_active', 1], ['is_delete', 0],['fiscal_year_id',$fiscaleYearId]])
                    ->join('hr_months', 'hr_months.id', 'hr_app_leaves_adjustment.adjustment_month')
                    ->select('emp_id', 'fiscal_year_id', 'adjustment_for', 'adjustment_month', 'adjustment_value','application_date','name as month_name');

    }
    ## Get Leave Adjustment Query End

    ## Get All Branch Data Query Start
    // public static function getAllBranchData(){
    //     $allBranch = Common::getBranchIdsForAllSection(['branchId'=> -3]);

    //     dd($allBranch);
    // }
    ## Get All Branch Data Query End


    public static function getMonthNameDatesDaysData($val1){
        $startDate = new DateTime($val1->fy_start_date);
        $endDate = new DateTime($val1->fy_end_date);

        return self::getMonthNameDatesDays($startDate, $endDate);
    }

    ## get Data From hr_emp_organization_details
    public static function getEmpOrganizationDetails($empId){

        return DB::table('hr_emp_organization_details')->where('emp_id', $empId)->first();
    }

    

    /**
     * Modify Query Based on User Permissions
     *
     * This function dynamically adjusts the given query based on the user's permissions and specified conditions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $perQuery - The original query to be modified.
     * @param array $userInfo - User information containing details such as employee ID (`emp_id`).
     * @param array $statusArray - An array defining the user's permissions and access levels.
     * @param int $loginUserDeptId - The department ID of the logged-in user.
     * @param array $selectBranchArr - An array containing branch IDs for which the user has permission.
     * @param string $alies (optional) - An alias that can be used for additional conditions when filtering data. Defaults to an empty string.
     *
     * @return \Illuminate\Database\Eloquent\Builder - The modified query.
     *
     * @example
     * 
     * $query = MyModel::query();
     * $userInfo = ['emp_id' => 123];
     * $statusArray = [100, 104.6];
     * $loginUserDeptId = 5;
     * $selectBranchArr = [1, 2, 3];
     * $alies = 'e';
     *
     * $modifiedQuery = permissionQuery($query, $userInfo, $statusArray, $loginUserDeptId, $selectBranchArr, $alies);
     * 
     *
     * @notes
     * - Ensure to integrate this function within the context of your application's data retrieval logic.
     * - Customize the function based on your specific use case and database structure.
     */
    public static function permissionQuery($perQuery, $userInfo, $statusArray, $loginUserDeptId, $selectBranchArr, $alies = ''){

        if(Common::isSuperUser() == true || Common::isDeveloperUser() == true ){
            ## nothing to do
            // dd(Common::isSuperUser() == true);
        }
        else {

            // dd($statusArray);
            if(in_array(100, $statusArray)){
                ## All Data for Permitted Branches
                $perQuery->whereIn('branch_id', $selectBranchArr);

            }elseif(in_array(100.1, $statusArray)){
                ## 	Own Data
                if(!empty($alies)){
                    $perQuery->where($alies.'.emp_id', $userInfo['emp_id']);
                    $perQuery->orWhere($alies.'.created_by', $userInfo['emp_id']);
                }else{
                    $perQuery->where('id', $userInfo['emp_id']);
                    $perQuery->orWhere('created_by', $userInfo['emp_id']);
                }
            
            }elseif(in_array(104.1, $statusArray)){
                ## Own department for All branch with HO
                $perQuery->where('department_id', $loginUserDeptId);
            
            }elseif(in_array(104.2, $statusArray)){
                ## 	All department for All branch without HO
                $perQuery->where('branch_id', '<>' , 1);
            
            }elseif(in_array(104.3, $statusArray)){
                ## 	Own department for All branch without HO
                $perQuery->where('branch_id', '<>' , 1);
                $perQuery->where('department_id', $loginUserDeptId);
            
            }elseif(in_array(104.4, $statusArray)){
                ## 	All department Only HO
                $perQuery->where('branch_id', 1);
            
            }elseif(in_array(104.5, $statusArray)){
                ## 	Own department Only HO
                $perQuery->where('branch_id', 1);
                $perQuery->where('department_id', $loginUserDeptId);
            
            }elseif(in_array(104.6, $statusArray)){
                ## 	All department for permitted branch
                $perQuery->whereIn('branch_id', $selectBranchArr);
            
            }elseif(in_array(104.7, $statusArray)){
                ## 	Own department for permitted branch
                $perQuery->whereIn('branch_id', $selectBranchArr);
                $perQuery->where('department_id', $loginUserDeptId);
            
            }
        }

        return $perQuery;
    }

    public static function getCompanyAddress(){
        return DB::table('gnl_companies')->select('comp_name','comp_addr')->first();
    }



    public static function getValidReasonIds($eventId){
        $validIds = DB::table('hr_app_reasons')
            ->select('id')
            ->where([['is_delete', 0],['event_id', $eventId]])
            // ->where(function($query) use ($eventId){
            //     if($eventId == 14){
            //         $query->whereNotIn('reason', ['others', 'other', 'personal','Others', 'Other', 'Personal']);
            //     }
            // })
            ->pluck('id')->toArray();
        return $validIds;
    }

  
}
