<?php

namespace App\Services;

use App\Model\GNL\Branch;
use App\Model\POS\Product;
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;
use DateTime;
use Illuminate\Support\Facades\DB;

class backup_PosService
{
    public function __construct()
    {
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
    }

/** Start function for report join query & search */
    public static function fnForBranchZoneAreaWise($branchId = null, $zoneId = null, $areaId = null, $companyID = null)
    {
        // $selBranchArr = array();
        // if (empty($branchId)) {
        //     if (!empty($zoneId) && !empty($areaId)) {
        //         $zoneQuery = DB::table('gnl_zones')
        //             ->where([['is_active', 1], ['is_delete', 0], ['id', $zoneId]])
        //             ->where(function ($zoneQuery) use ($companyID) {
        //                 if (!empty($companyID)) {
        //                     $zoneQuery->where('company_id', $companyID);
        //                 }
        //             })
        //             ->select('branch_arr')
        //             ->first();

        //         if ($zoneQuery) {
        //             $selBranchArrZ = explode(',', $zoneQuery->branch_arr);
        //         } else {
        //             $selBranchArrZ = HRS::getUserAccesableBranchIds();
        //         }

        //         $areaQuery = DB::table('gnl_areas')
        //             ->where([['is_active', 1], ['is_delete', 0], ['id', $areaId]])
        //             ->where(function ($areaQuery) use ($companyID) {
        //                 if (!empty($companyID)) {
        //                     $areaQuery->where('company_id', $companyID);
        //                 }
        //             })
        //             ->select('branch_arr')
        //             ->first();

        //         if ($areaQuery) {
        //             $selBranchArr = explode(',', $areaQuery->branch_arr);
        //         } else {
        //             $selBranchArr = HRS::getUserAccesableBranchIds();
        //         }


        //         // $selBranchArr = array_unique(array_merge($selBranchArrZ, $selBranchArrA));

        //         // dd(count($selBranchArr));
        //     }
        //     elseif (!empty($zoneId) && empty($areaId)) {
        //         $zoneQuery = DB::table('gnl_zones')
        //             ->where([['is_active', 1], ['is_delete', 0], ['id', $zoneId]])
        //             ->where(function ($zoneQuery) use ($companyID) {
        //                 if (!empty($companyID)) {
        //                     $zoneQuery->where('company_id', $companyID);
        //                 }
        //             })
        //             ->select('branch_arr')
        //             ->first();

        //         if ($zoneQuery) {
        //             $selBranchArr = explode(',', $zoneQuery->branch_arr);
        //         } else {
        //             $selBranchArr = HRS::getUserAccesableBranchIds();
        //         }

        //     } elseif (!empty($areaId) && empty($zoneId)) {
        //         $areaQuery = DB::table('gnl_areas')
        //             ->where([['is_active', 1], ['is_delete', 0], ['id', $areaId]])
        //             ->where(function ($areaQuery) use ($companyID) {
        //                 if (!empty($companyID)) {
        //                     $areaQuery->where('company_id', $companyID);
        //                 }
        //             })
        //             ->select('branch_arr')
        //             ->first();

        //         if ($areaQuery) {
        //             $selBranchArr = explode(',', $areaQuery->branch_arr);
        //         } else {
        //             $selBranchArr = HRS::getUserAccesableBranchIds();
        //         }

        //     } else {
        //         $selBranchArr = HRS::getUserAccesableBranchIds();
        //     }
        // } else {
        //     $selBranchArr = [$branchId];
        // }

        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId, $companyID);

        return $selBranchArr;
    }

    public static function fnForBranchData($branchArr = [])
    {
        // $branchData = array();
        // if (count($branchArr) > 0) {

        //     if (Common::getDBConnection() == "sqlite") {
        //         $branchData = DB::table('gnl_branchs')
        //             ->where([['is_delete', 0], ['is_active', 1]])
        //             ->whereIn('id', $branchArr)
        //             ->selectRaw('(branch_name || " (" || branch_code || ")" ) AS branch_name, id')
        //             ->pluck('branch_name', 'id')
        //             ->toArray();
        //     } else {
        //         $branchData = DB::table('gnl_branchs')
        //             ->where([['is_delete', 0], ['is_active', 1]])
        //             ->whereIn('id', $branchArr)
        //             ->selectRaw('CONCAT(branch_name, " (", branch_code, ")") AS branch_name, id')
        //             ->pluck('branch_name', 'id')
        //             ->toArray();
        //     }
        // }

        $branchData = Common::fnForBranchData($branchArr);

        return $branchData;
    }

    public static function fnForEmployeeData($employeeArr = [])
    {
        // $employeeData = array();
        // if (count($employeeArr) > 0) {

        //     if (Common::getDBConnection() == "sqlite") {
        //         $employeeData = DB::table('hr_employees')
        //             ->where([['is_delete', 0], ['is_active', 1]])
        //             ->whereIn('employee_no', $employeeArr)
        //             ->selectRaw('(emp_name || " (" || emp_code || ")") AS emp_name, employee_no')
        //             ->pluck('emp_name', 'employee_no')
        //             ->toArray();
        //     } else {
        //         $employeeData = DB::table('hr_employees')
        //             ->where([['is_delete', 0], ['is_active', 1]])
        //             ->whereIn('employee_no', $employeeArr)
        //             ->selectRaw('CONCAT(emp_name, " (", emp_code, ")") AS emp_name, employee_no')
        //             ->pluck('emp_name', 'employee_no')
        //             ->toArray();
        //     }

        // }

        $employeeData = Common::fnForEmployeeData($employeeArr);

        return $employeeData;
    }


/** End report function */

/** start Schedule function  */

    public static function pre30112020_installmentSchedule($companyID = null, $branchID = null, $somityID = null,
        $salesDate = null, $instType = null, $instMonth = null) {

        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchID = (!empty($branchID)) ? $branchID : Common::getBranchId();
        $somityID = (!empty($somityID)) ? $somityID : 1;
        $companyID = (!empty($companyID)) ? $companyID : 1;

        $fromDate = null;
        $actualToDate = null;
        $toDate = null;
        $instCount = 0;
        $instMonth = (int) $instMonth;
        $instWeek = 0;

        $scheduleDays = array();
        $tempScheduleDays = array();

        if (!empty($salesDate) && !empty($instMonth)) {

            $fromDate = new DateTime($salesDate);
            $tempDate = clone $fromDate;
            $actualTempDate = clone $fromDate;

            /*
             * Extra 2 month add kora hocche karon jodi
            kono date, week, month holiday te pore
            tahole add or remove kora jay
             */
            $actualToDate = $actualTempDate->modify('+' . ($instMonth) . ' month');
            $toDate = $tempDate->modify('+' . (($instMonth - 1) + 2) . ' month');

            ## Week Count from Two Dates
            /*
             * 1 Week = 60*60*24*7 = 604800
             */
            $dateDiff = strtotime($actualToDate->format('d-m-Y'), 0) - strtotime($fromDate->format('d-m-Y'), 0);
            $instWeek = (int) floor($dateDiff / 604800);

            // dd($fromDate, $actualToDate, $instWeek);

        }

        // ///// This is for query
        if (!empty($fromDate) && !empty($toDate)) {

            // Fixed Govt Holiday Query
            $govtHolidays = DB::table('hr_holidays_govt')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            $fixedGovtHoliday = (count($govtHolidays->toarray()) > 0) ? $govtHolidays->toarray() : array();

            // Company Holiday Query
            $companyArr = (!empty($companyID)) ? ['company_id', '=', $companyID] : ['company_id', '<>', ''];
            $companyHolidayQuery = DB::table('hr_holidays_comp')->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
                ->where([$companyArr])
                ->get();
            $companyHolidays = (count($companyHolidayQuery->toarray()) > 0) ? $companyHolidayQuery->toarray() : array();

            // Special Holiday for Organization Query
            $specialHolidayORGQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysORG = (count($specialHolidayORGQuery->toarray()) > 0) ? $specialHolidayORGQuery->toarray() : array();

            // Special Holiday for Branch Query
            $specialHolidayBrQuery = DB::table('hr_holidays_special')->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where('branch_id', '=', $branchID)
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            $sHolidaysBr = (count($specialHolidayBrQuery->toarray()) > 0) ? $specialHolidayBrQuery->toarray() : array();
        }

        if (!empty($fromDate) && !empty($toDate) && !empty($instType)) {

            ///////////////////////////////////// test ////////////////////////////
            // $instType = 2;
            ///////////////////////////////////// test ////////////////////////////
            // $week = $installmentDate->format("W");

            $firstInstallmentDay = $fromDate->format('d');
            $firstInstallmentMonth = $fromDate->format('m');
            $firstInstallmentYear = $fromDate->format('Y');

            if ($instType == 1) {
                // Month Type
                $installmentDate = clone $fromDate;
                array_push($tempScheduleDays, clone $installmentDate);

                while ($installmentDate <= $toDate) {

                    if ($firstInstallmentDay == '31' || $firstInstallmentDay == 31) {
                        $installmentDate = $installmentDate->modify('last day of next month');
                    } else if ($firstInstallmentDay == '30' || $firstInstallmentDay == 30
                        || $firstInstallmentDay == '29' || $firstInstallmentDay == 29) {

                        $tempNextMonth = clone $installmentDate;
                        $tempNextMonth = $tempNextMonth->modify('last day of next month');

                        if ($tempNextMonth->format('m') == '2' || $tempNextMonth->format('m') == '02'
                            || $tempNextMonth->format('m') == 2 || $tempNextMonth->format('m') == 02) {

                            $installmentDate = $installmentDate->modify('last day of next month');
                        } else {
                            $installmentDate = $installmentDate->modify('+1 month');
                        }
                    } else {
                        $installmentDate = $installmentDate->modify('+1 month');
                    }

                    array_push($tempScheduleDays, clone $installmentDate);
                }

            } elseif ($instType == 2) {
                // Week Type
                $installmentDate = clone $fromDate;
                while ($installmentDate <= $toDate) {
                    // array_push($tempScheduleDays, $installmentDate->format('Y-m-d'));
                    array_push($tempScheduleDays, clone $installmentDate);
                    $installmentDate = $installmentDate->modify('+1 week');
                }
            }

            foreach ($tempScheduleDays as $tempRow) {

                $holidayFlag = true;
                $tempLoopDate = clone $tempRow;

                while ($holidayFlag == true) {

                    $holidayFlag = false;

                    // Fixed Govt Holiday Check
                    foreach ($fixedGovtHoliday as $RowFG) {
                        $RowFG = (array) $RowFG;
                        if ($RowFG['gh_date'] == $tempLoopDate->format('d-m')) {
                            $holidayFlag = true;
                        }
                    }

                    // Company Holiday Check
                    if ($holidayFlag == false) {
                        foreach ($companyHolidays as $RowC) {
                            $RowC = (array) $RowC;

                            $ch_day = $RowC['ch_day'];

                            $ch_day_arr = explode(',', $RowC['ch_day']);
                            $ch_eff_date = new DateTime($RowC['ch_eff_date']);

                            // This is Full day name
                            $dayName = $tempLoopDate->format('l');

                            if (in_array($dayName, $ch_day_arr) && ($ch_eff_date <= $tempLoopDate)) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    // Special Holiday Org check
                    if ($holidayFlag == false) {
                        foreach ($sHolidaysORG as $RowO) {
                            $RowO = (array) $RowO;

                            $sh_date_from = new DateTime($RowO['sh_date_from']);
                            $sh_date_to = new DateTime($RowO['sh_date_to']);

                            if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    // Special Holiday Branch check
                    if ($holidayFlag == false) {
                        foreach ($sHolidaysBr as $RowB) {

                            $RowB = (array) $RowB;

                            $sh_date_from_b = new DateTime($RowB['sh_date_from']);
                            $sh_date_to_b = new DateTime($RowB['sh_date_to']);

                            if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                                $holidayFlag = true;
                            }
                        }
                    }

                    if ($holidayFlag == false) {
                        array_push($scheduleDays, $tempLoopDate->format('Y-m-d'));
                        // array_push($scheduleDays, clone $tempLoopDate);

                    } else {
                        // $tempCurMonth = $tempRow->format('m');
                        $tempLoopDate = $tempLoopDate->modify('+1 day');
                    }
                }
            }

            // dd($scheduleDays);

        }

        ///////////////////////////////////////////////////////////////
        // Incomplete function, check remain if full week holiday skip this week but schedule date count must be equal installment month or week
        // When month and week end and go to next week that case date modify minus day

        if ($instType == 1) {
            if (count($scheduleDays) > $instMonth) {
                $countDiff = count($scheduleDays) - $instMonth;
                for ($r = 0; $r < $countDiff; $r++) {
                    array_pop($scheduleDays);
                }
            }
        } else if ($instType == 2) {
            if (count($scheduleDays) > $instWeek) {
                $countDiff = count($scheduleDays) - $instWeek;
                for ($r = 0; $r < $countDiff; $r++) {
                    array_pop($scheduleDays);
                }
            }
        }

        // dd($scheduleDays);

        return $scheduleDays;
    }

    public static function pre30112020_installmentSchedule_multiple($companyID = null, $branchArr = [], $branchDateTypeMonthArr = [], $somityArr = [])
    {
        // ## integer, ## string, ## array
        // if (gettype($branchDateTypeMonthArr) == "string") {
        //     $branchDateTypeMonthArr = unserialize($branchDateTypeMonthArr);
        // }

        // if (gettype($branchArr) == "string") {
        //     $branchArr = unserialize($branchArr);
        // }

        $allScheduleData = array();

        // // ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $scheduleFlag = (count($branchDateTypeMonthArr) > 0) ? true : false;

        // ## if sales date, type, month empty then return initial with empty array
        if ($scheduleFlag == false) {
            // return serialize($allScheduleData);
            return $allScheduleData;
        }

        $weekDayArr = [
            1 => 'Saturday',
            2 => 'Sunday',
            3 => 'Monday',
            4 => 'Tuesday',
            5 => 'Wednesday',
            6 => 'Thursday',
            7 => 'Friday',
        ];

        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchArr = (count($branchArr) > 0) ? $branchArr : [Common::getBranchId()];
        $somityArr = (count($somityArr) > 0) ? $somityArr : [1];

        // ## ----------------------------------- Holiday Query Start
        if ($scheduleFlag) {
            /**
             * Collection theke Array Faster,
             * @current due report - 24-09-2020 porjonto sales ache 1714 ta,
             * @collection diye korle page load hote time ney 15.72s file size 9.2 kB
             * but query er data array te convert kore check korle seta time ney 3.21s only file size 9.2 kB
             * @single data pass korle file size 9.2kB load hote time ney 4.80s but multiple function seikhane 3.21s a load hoy
             * @test korte hole ai same function er ekta copy rakha ache old a seta test kore dekha jabe
             *
             */

            // // ## Fixed Govt Holiday Query
            $fixedGovtHoliday = DB::table('hr_holidays_govt')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date')
                ->get();
            // $fixedGovtHoliday = (count($fixedGovtHoliday->toarray()) > 0) ? $fixedGovtHoliday->toarray() : array();

            // // ## Company Holiday Query
            $companyHolidays = DB::table('hr_holidays_comp')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date')
                ->where(function ($companyHolidays) use ($companyID) {
                    if (!empty($companyID)) {
                        $companyHolidays->where('company_id', $companyID);
                    }
                })
                ->get();
            // $companyHolidays = (count($companyHolidays->toarray()) > 0) ? $companyHolidays->toarray() : array();

            // // ## Special Holiday for Organization Query
            $sHolidaysORG = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'org']])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysORG = (count($sHolidaysORG->toarray()) > 0) ? $sHolidaysORG->toarray() : array();

            // // ## Special Holiday for Branch Query
            $sHolidaysBr = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1], ['sh_app_for', 'branch']])
                ->where(function ($sHolidaysBr) use ($branchArr) {
                    if (!empty($branchArr)) {
                        $sHolidaysBr->whereIn('branch_id', $branchArr);
                    }
                })
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to')
                ->get();

            // $sHolidaysBr = (count($sHolidaysBr->toarray()) > 0) ? $sHolidaysBr->toarray() : array();

        }
        // ## ----------------------------------- End Holiday Query

        // // ## Schedule Make Start
        foreach ($branchDateTypeMonthArr as $passingValue) {

            // ## explode concat value
            $passingArr = explode('@', $passingValue);
            $branchID = (isset($passingArr[0]) && !empty($passingArr[0])) ? $passingArr[0] : null;
            $salesDate = (isset($passingArr[1]) && !empty($passingArr[1])) ? $passingArr[1] : null;
            $instType = (isset($passingArr[2]) && !empty($passingArr[2])) ? $passingArr[2] : null;
            $instMonth = (isset($passingArr[3]) && !empty($passingArr[3])) ? $passingArr[3] : null;
            // ## end explode

            // dd($passingValue);

            // // ## Start Process Make Schedule
            $fromDate = null;
            $actualToDate = null;
            $toDate = null;
            $instCount = 0;
            $instMonth = (int) $instMonth;
            $instWeek = 0;

            $scheduleDays = array();
            $tempScheduleDays = array();

            if (!empty($salesDate) && !empty($instMonth)) {

                $fromDate = new DateTime($salesDate);
                $tempDate = clone $fromDate;
                $actualTempDate = clone $fromDate;

                /*
                 * Extra 2 month add kora hocche karon jodi
                kono date, week, month holiday te pore
                tahole add or remove kora jay
                 */
                $actualToDate = $actualTempDate->modify('+' . ($instMonth) . ' month');
                $toDate = $tempDate->modify('+' . (($instMonth - 1) + 2) . ' month');

                ## Week Count from Two Dates
                /*
                 * 1 Week = 60*60*24*7 = 604800
                 */
                $dateDiff = strtotime($actualToDate->format('d-m-Y'), 0) - strtotime($fromDate->format('d-m-Y'), 0);
                $instWeek = (int) floor($dateDiff / 604800);

                // dd($fromDate, $actualToDate, $instWeek);
            }

            if (!empty($fromDate) && !empty($toDate) && !empty($instType)) {

                ///////////////////////////////////// test ////////////////////////////
                // $instType = 2;
                ///////////////////////////////////// test ////////////////////////////
                // $week = $installmentDate->format("W");

                $firstInstallmentDay = $fromDate->format('d');
                $firstInstallmentMonth = $fromDate->format('m');
                $firstInstallmentYear = $fromDate->format('Y');

                if ($instType == 1) {
                    // Month Type
                    $installmentDate = clone $fromDate;
                    array_push($tempScheduleDays, clone $installmentDate);

                    while ($installmentDate <= $toDate) {

                        if ($firstInstallmentDay == '31' || $firstInstallmentDay == 31) {
                            $installmentDate = $installmentDate->modify('last day of next month');
                        } else if ($firstInstallmentDay == '30' || $firstInstallmentDay == 30
                            || $firstInstallmentDay == '29' || $firstInstallmentDay == 29) {

                            $tempNextMonth = clone $installmentDate;
                            $tempNextMonth = $tempNextMonth->modify('last day of next month');

                            if ($tempNextMonth->format('m') == '2' || $tempNextMonth->format('m') == '02'
                                || $tempNextMonth->format('m') == 2 || $tempNextMonth->format('m') == 02) {

                                $installmentDate = $installmentDate->modify('last day of next month');
                            } else {
                                $installmentDate = $installmentDate->modify('+1 month');
                            }
                        } else {
                            $installmentDate = $installmentDate->modify('+1 month');
                        }

                        array_push($tempScheduleDays, clone $installmentDate);
                    }

                } elseif ($instType == 2) {
                    // Week Type
                    $installmentDate = clone $fromDate;
                    while ($installmentDate <= $toDate) {
                        // array_push($tempScheduleDays, $installmentDate->format('Y-m-d'));
                        array_push($tempScheduleDays, clone $installmentDate);
                        $installmentDate = $installmentDate->modify('+1 week');
                    }
                }

                foreach ($tempScheduleDays as $tempRow) {

                    $holidayFlag = true;
                    $tempLoopDate = clone $tempRow;

                    while ($holidayFlag == true) {

                        $holidayFlag = false;

                        // Fixed Govt Holiday Check
                        foreach ($fixedGovtHoliday as $RowFG) {

                            $RowFG = (array) $RowFG;

                            if (($RowFG->gh_date == $tempLoopDate->format('d-m'))
                                && (empty($RowFG->efft_start_date) || ($RowFG->efft_start_date <= $tempLoopDate->format('Y-m-d')))
                                && (empty($RowFG->efft_end_date) || ($RowFG->efft_end_date >= $tempLoopDate->format('Y-m-d')))) {

                                $holidayFlag = true;
                            }
                        }

                        // Company Holiday Check
                        if ($holidayFlag == false) {
                            foreach ($companyHolidays as $RowC) {

                            $RowC = (array) $RowC;

                                $ch_day = $RowC->ch_day;
                                $ch_day_arr = explode(',', $RowC->ch_day);
                                $ch_eff_date = (!empty($RowC->ch_eff_date)) ? new DateTime($RowC->ch_eff_date) : null;

                                // ## This is Full day name
                                $dayName = $tempLoopDate->format('l');
                                if (in_array($dayName, $ch_day_arr) && (empty($ch_eff_date) || ($ch_eff_date <= $tempLoopDate))) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        // Special Holiday Org check
                        if ($holidayFlag == false) {
                            foreach ($sHolidaysORG as $RowO) {

                            $RowO = (array) $RowO;


                                $sh_date_from = new DateTime($RowO->sh_date_from);
                                $sh_date_to = new DateTime($RowO->sh_date_to);

                                if (($sh_date_from <= $tempLoopDate) && ($sh_date_to >= $tempLoopDate)) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        // Special Holiday Branch check
                        if ($holidayFlag == false) {
                            foreach ($sHolidaysBr as $RowB) {

                            $RowB = (array) $RowB;


                                $sh_date_from_b = new DateTime($RowB->sh_date_from);
                                $sh_date_to_b = new DateTime($RowB->sh_date_to);

                                if (($sh_date_from_b <= $tempLoopDate) && ($sh_date_to_b >= $tempLoopDate)) {
                                    $holidayFlag = true;
                                }
                            }
                        }

                        if ($holidayFlag == false) {
                            array_push($scheduleDays, $tempLoopDate->format('Y-m-d'));
                            // array_push($scheduleDays, clone $tempLoopDate);

                        } else {
                            // $tempCurMonth = $tempRow->format('m');
                            $tempLoopDate = $tempLoopDate->modify('+1 day');
                        }
                    }

                }
            }

            ///////////////////////////////////////////////////////////////
            // Incomplete function, check remain if full week holiday skip this week but schedule date count must be equal installment month or week
            // When month and week end and go to next week that case date modify minus day

            if ($instType == 1) {
                if (count($scheduleDays) > $instMonth) {
                    $countDiff = count($scheduleDays) - $instMonth;
                    for ($r = 0; $r < $countDiff; $r++) {
                        array_pop($scheduleDays);
                    }
                }
            } else if ($instType == 2) {
                if (count($scheduleDays) > $instWeek) {
                    $countDiff = count($scheduleDays) - $instWeek;
                    for ($r = 0; $r < $countDiff; $r++) {
                        array_pop($scheduleDays);
                    }
                }
            }

            // // ## ## Data merge with (branch@salesdate@installmentType@installmentMonth) key
            $allScheduleData[$passingValue] = $scheduleDays;

            // dd($allScheduleData);
        }
        // // ## Schedule Make End

        // return serialize($allScheduleData);
        return $allScheduleData;
    }

    // ## please dont delete this function
    public static function old_installmentSchedule_multiple($companyID = null, $branchArr = [], $branchDateTypeMonthArr = [], $somityArr = [])
    {
        // ## integer, ## string, ## array
        if (gettype($branchDateTypeMonthArr) == "string") {
            $branchDateTypeMonthArr = unserialize($branchDateTypeMonthArr);
        }

        if (gettype($branchArr) == "string") {
            $branchArr = unserialize($branchArr);
        }

        $allScheduleData = array();

        // // ## Concat data by @ (branch@salesdate@installmentType@installmentMonth)
        $scheduleFlag = (count($branchDateTypeMonthArr) > 0) ? true : false;

        // ## if sales date, type, month empty then return initial with empty array
        if ($scheduleFlag == false) {
            return serialize($allScheduleData);
        }

        $weekDayArr = [
            1 => 'Saturday',
            2 => 'Sunday',
            3 => 'Monday',
            4 => 'Tuesday',
            5 => 'Wednesday',
            6 => 'Thursday',
            7 => 'Friday',
        ];

        $companyID = (!empty($companyID)) ? $companyID : Common::getCompanyId();
        $branchArr = (count($branchArr) > 0) ? $branchArr : [Common::getBranchId()];
        $somityArr = (count($somityArr) > 0) ? $somityArr : [1];

        // ## ----------------------------------- Holiday Query Start
        if ($scheduleFlag) {
            // ## Fixed Govt Holiday Query
            $fixedGovtHoliday = DB::table('hr_holidays_govt')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'gh_title', 'gh_date', 'efft_start_date', 'efft_end_date');

            // ## Company Holiday Query
            $companyHolidays = DB::table('hr_holidays_comp')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where(function ($companyHolidays) use ($companyID) {
                    if (!empty($companyID)) {
                        $companyHolidays->where('company_id', $companyID);
                    }
                })
                ->select('id', 'company_id', 'ch_title', 'ch_day', 'ch_eff_date');

            // ## Special Holiday Query
            $specialHolidays = DB::table('hr_holidays_special')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->select('id', 'company_id', 'branch_id', 'sh_title', 'sh_app_for', 'sh_date_from', 'sh_date_to');

        }
        // ## ----------------------------------- End Holiday Query

        // // ## Schedule Make Start
        foreach ($branchDateTypeMonthArr as $passingValue) {

            // ## explode concat value
            $passingArr = explode('@', $passingValue);
            $branchID = (isset($passingArr[0]) && !empty($passingArr[0])) ? $passingArr[0] : null;
            $salesDate = (isset($passingArr[1]) && !empty($passingArr[1])) ? $passingArr[1] : null;
            $instType = (isset($passingArr[2]) && !empty($passingArr[2])) ? $passingArr[2] : null;
            $instMonth = (isset($passingArr[3]) && !empty($passingArr[3])) ? $passingArr[3] : null;
            // ## end explode

            // // ## Start Process Make Schedule
            $fromDate = null;
            $actualToDate = null;
            $toDate = null;
            $instCount = 0;
            $instMonth = (int) $instMonth;
            $instWeek = 0;

            $scheduleDays = array();
            $tempScheduleDays = array();

            if (!empty($salesDate) && !empty($instMonth)) {

                $fromDate = new DateTime($salesDate);
                $tempDate = clone $fromDate;
                $actualTempDate = clone $fromDate;

                /*
                 * Extra 2 month add kora hocche karon jodi
                kono date, week, month holiday te pore
                tahole add or remove kora jay
                 */
                $actualToDate = $actualTempDate->modify('+' . ($instMonth) . ' month');
                $toDate = $tempDate->modify('+' . (($instMonth - 1) + 2) . ' month');

                ## Week Count from Two Dates
                /*
                 * 1 Week = 60*60*24*7 = 604800
                 */
                $dateDiff = strtotime($actualToDate->format('d-m-Y'), 0) - strtotime($fromDate->format('d-m-Y'), 0);
                $instWeek = (int) floor($dateDiff / 604800);

                // dd($fromDate, $actualToDate, $instWeek);
            }

            if (!empty($fromDate) && !empty($toDate) && !empty($instType)) {

                ///////////////////////////////////// test ////////////////////////////
                // $instType = 2;
                ///////////////////////////////////// test ////////////////////////////
                // $week = $installmentDate->format("W");

                $firstInstallmentDay = $fromDate->format('d');
                $firstInstallmentMonth = $fromDate->format('m');
                $firstInstallmentYear = $fromDate->format('Y');

                if ($instType == 1) {
                    // Month Type
                    $installmentDate = clone $fromDate;
                    array_push($tempScheduleDays, clone $installmentDate);

                    while ($installmentDate <= $toDate) {

                        if ($firstInstallmentDay == '31' || $firstInstallmentDay == 31) {
                            $installmentDate = $installmentDate->modify('last day of next month');
                        } else if ($firstInstallmentDay == '30' || $firstInstallmentDay == 30
                            || $firstInstallmentDay == '29' || $firstInstallmentDay == 29) {

                            $tempNextMonth = clone $installmentDate;
                            $tempNextMonth = $tempNextMonth->modify('last day of next month');

                            if ($tempNextMonth->format('m') == '2' || $tempNextMonth->format('m') == '02'
                                || $tempNextMonth->format('m') == 2 || $tempNextMonth->format('m') == 02) {

                                $installmentDate = $installmentDate->modify('last day of next month');
                            } else {
                                $installmentDate = $installmentDate->modify('+1 month');
                            }
                        } else {
                            $installmentDate = $installmentDate->modify('+1 month');
                        }

                        array_push($tempScheduleDays, clone $installmentDate);
                    }

                } elseif ($instType == 2) {
                    // Week Type
                    $installmentDate = clone $fromDate;
                    while ($installmentDate <= $toDate) {
                        // array_push($tempScheduleDays, $installmentDate->format('Y-m-d'));
                        array_push($tempScheduleDays, clone $installmentDate);
                        $installmentDate = $installmentDate->modify('+1 week');
                    }
                }

                foreach ($tempScheduleDays as $tempRow) {

                    $holidayFlag = true;
                    $tempLoopDate = clone $tempRow;

                    while ($holidayFlag == true) {

                        $holidayFlag = false;

                        // ## ---------------- Fixed Govt Holiday Check Start
                        $countFixedHolyday = clone $fixedGovtHoliday;

                        $countFixedHolyday = $countFixedHolyday->where('gh_date', $tempLoopDate->format('d-m'))
                            ->where(function ($countFixedHolyday) use ($tempLoopDate) {
                                if (!empty($tempLoopDate)) {
                                    $countFixedHolyday->whereNull('efft_start_date');
                                    $countFixedHolyday->orWhere('efft_start_date', '<=', $tempLoopDate->format('Y-m-d'));
                                }
                            })
                            ->where(function ($countFixedHolyday) use ($tempLoopDate) {
                                if (!empty($tempLoopDate)) {
                                    $countFixedHolyday->whereNull('efft_end_date');
                                    $countFixedHolyday->orWhere('efft_end_date', '>=', $tempLoopDate->format('Y-m-d'));
                                }
                            })
                            ->count();

                        if ($countFixedHolyday > 0) {
                            $holidayFlag = true;
                        }
                        // ## ---------------- End Fixed Govt Holiday Check

                        // ## ---------------- Company Holiday Check Start
                        if ($holidayFlag == false) {
                            // ## This is Full day name
                            $dayName = $tempLoopDate->format('l');

                            $countComHolyday = clone $companyHolidays;

                            $countComHolyday = $countComHolyday->where(function ($countComHolyday) use ($dayName) {
                                if (!empty($dayName)) {
                                    $countComHolyday->where('ch_day', 'LIKE', "%,{$dayName},%")
                                        ->orWhere('ch_day', 'LIKE', "{$dayName},%")
                                        ->orWhere('ch_day', 'LIKE', "%,{$dayName}")
                                        ->orWhere('ch_day', 'LIKE', "{$dayName}");
                                }
                            })
                                ->where(function ($countComHolyday) use ($tempLoopDate) {
                                    if (!empty($tempLoopDate)) {
                                        $countComHolyday->whereNull('ch_eff_date');
                                        $countComHolyday->orWhere('ch_eff_date', '<=', $tempLoopDate->format('Y-m-d'));
                                    }
                                })
                                ->count();

                            if ($countComHolyday > 0) {
                                $holidayFlag = true;
                            }
                        }
                        // ## ---------------- End Company Holiday Check

                        // ## ---------------- Special Holiday Org check Start
                        if ($holidayFlag == false) {

                            $countOrgHolyday = clone $specialHolidays;
                            $countOrgHolyday = $countOrgHolyday->where('sh_app_for', 'org')
                                ->where(function ($countOrgHolyday) use ($tempLoopDate) {
                                    if (!empty($tempLoopDate)) {
                                        $countOrgHolyday->where('sh_date_from', '<=', $tempLoopDate->format('Y-m-d'));
                                    }
                                })
                                ->where(function ($countOrgHolyday) use ($tempLoopDate) {
                                    if (!empty($tempLoopDate)) {
                                        $countOrgHolyday->where('sh_date_to', '>=', $tempLoopDate->format('Y-m-d'));
                                    }
                                })
                                ->count();

                            if ($countOrgHolyday > 0) {
                                $holidayFlag = true;
                            }
                        }
                        // ## ---------------- End Special Holiday Org check

                        // ## ---------------- Special Holiday Branch check Start
                        if ($holidayFlag == false) {

                            $countBranchHolyday = clone $specialHolidays;

                            $countBranchHolyday = $countBranchHolyday->where('sh_app_for', 'branch')
                                ->where(function ($countBranchHolyday) use ($branchID) {
                                    if (!empty($branchID)) {
                                        $countBranchHolyday->where('branch_id', $branchID);
                                    }
                                })
                                ->where(function ($countBranchHolyday) use ($tempLoopDate) {
                                    if (!empty($tempLoopDate)) {
                                        $countBranchHolyday->where('sh_date_from', '<=', $tempLoopDate->format('Y-m-d'));
                                    }
                                })
                                ->where(function ($countBranchHolyday) use ($tempLoopDate) {
                                    if (!empty($tempLoopDate)) {
                                        $countBranchHolyday->where('sh_date_to', '>=', $tempLoopDate->format('Y-m-d'));
                                    }
                                })
                                ->count();

                            if ($countBranchHolyday > 0) {
                                $holidayFlag = true;
                            }
                        }
                        // ## ---------------- End Special Branch Org check

                        if ($holidayFlag == false) {
                            array_push($scheduleDays, $tempLoopDate->format('Y-m-d'));
                            // array_push($scheduleDays, clone $tempLoopDate);

                        } else {
                            // $tempCurMonth = $tempRow->format('m');
                            $tempLoopDate = $tempLoopDate->modify('+1 day');
                        }
                    }
                }
            }

            ///////////////////////////////////////////////////////////////
            // Incomplete function, check remain if full week holiday skip this week but schedule date count must be equal installment month or week
            // When month and week end and go to next week that case date modify minus day

            if ($instType == 1) {
                if (count($scheduleDays) > $instMonth) {
                    $countDiff = count($scheduleDays) - $instMonth;
                    for ($r = 0; $r < $countDiff; $r++) {
                        array_pop($scheduleDays);
                    }
                }
            } else if ($instType == 2) {
                if (count($scheduleDays) > $instWeek) {
                    $countDiff = count($scheduleDays) - $instWeek;
                    for ($r = 0; $r < $countDiff; $r++) {
                        array_pop($scheduleDays);
                    }
                }
            }

            // // ## ## Data merge with (branch@salesdate@installmentType@installmentMonth) key
            $allScheduleData[$passingValue] = $scheduleDays;

            // dd($allScheduleData);
        }
        // // ## Schedule Make End

        return serialize($allScheduleData);
    }
/** End Schedule function */


}
