<?php

namespace App\Services;

use DateTime;
use Exception;
use App\Model\Acc\Voucher;
use Illuminate\Http\Request;
use App\Model\Acc\VoucherDetails;
use App\Services\HrService as HRS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;

class backup30082022_AccService
{
    public function __construct()
    {
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
    }
    public static $GlobalCount = 1;
    public static $PublicLedger;
    public static $accountSet;

    public static function getLedgerData($parameter = [])
    {

        $branchId      = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;
        $selBranchArr  = (isset($parameter['selBranchArr'])) ? $parameter['selBranchArr'] : array();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : null;
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : null;

        $ledgerIds = (isset($parameter['ledgerIds'])) ? $parameter['ledgerIds'] : null;
        $accType   = (isset($parameter['accType'])) ? $parameter['accType'] : null;
        $parentId  = (isset($parameter['parentId'])) ? $parameter['parentId'] : null;

        $groupHead = (isset($parameter['groupHead'])) ? $parameter['groupHead'] : false;
        $level     = (isset($parameter['level'])) ? $parameter['level'] : null;

        $isActive = (isset($parameter['isActive'])) ? $parameter['isActive'] : true;

        $accTypeNotIn = (isset($parameter['accTypeNotIn'])) ? $parameter['accTypeNotIn'] : null;

        $ledgerData = array();

        $ledgerHeads = DB::table('acc_account_ledger as acl')
            ->where('acl.is_delete', 0)

            ->where(function ($ledgerHeads) use ($isActive) {
                if ($isActive == true) {
                    $ledgerHeads->where('acl.is_active', 1);
                }
            })

            ->where(function ($ledgerHeads) use ($ledgerIds) {
                $ledger_var_type = gettype($ledgerIds);

                if ($ledger_var_type == 'array' && count($ledgerIds) > 0) {
                    $ledgerHeads->whereIn('acl.id', $ledgerIds);
                } elseif (!empty($ledgerIds)) {
                    $ledgerHeads->where('acl.id', $ledgerIds);
                }
            })

            ->where(function ($ledgerHeads) use ($accType) {
                $acc_var_type = gettype($accType);

                if ($acc_var_type == 'array' && count($accType) > 0) {
                    $ledgerHeads->whereIn('acl.acc_type_id', $accType);
                } elseif (!empty($accType)) {
                    $ledgerHeads->where('acl.acc_type_id', $accType);
                }
            })
            ->where(function ($ledgerHeads) use ($accTypeNotIn) { ## acc type not in
                $acc_var_type_not_in = gettype($accTypeNotIn);

                if ($acc_var_type_not_in == 'array' && count($accTypeNotIn) > 0) {
                    $ledgerHeads->whereNotIn('acl.acc_type_id', $accTypeNotIn);
                } elseif (!empty($accTypeNotIn)) {
                    $ledgerHeads->where('acl.acc_type_id', '<>', $accTypeNotIn);
                }
            })

            ->where(function ($ledgerHeads) use ($parentId) {
                $parent_var_type = gettype($parentId);

                if ($parent_var_type == 'array' && count($parentId) > 0) {
                    $ledgerHeads->whereIn('acl.parent_id', $parentId);
                } elseif ($parentId != null) {
                    $ledgerHeads->where('acl.parent_id', $parentId);
                }
            })

            ->where(function ($ledgerHeads) use ($level) {

                if ($level != null) {
                    $ledgerHeads->where('level', $level);
                }
            })

            ->where(function ($ledgerHeads) use ($groupHead) {

                if ($groupHead !== false) {
                    if (empty($groupHead)) {
                        $ledgerHeads->where('is_group_head', 0);
                    } elseif ($groupHead > 0) {
                        $ledgerHeads->where('is_group_head', $groupHead);
                    }
                }
            })

            ->where(function ($ledgerHeads) use ($projectId) {

                $ledgerHeads->where(function ($ledgerHeads) {
                    $ledgerHeads->where('acl.project_arr', 'LIKE', "0")
                        ->orWhere('acl.project_arr', 'LIKE', "0,%")
                        ->orWhere('acl.project_arr', 'LIKE', "%,0,%")
                        ->orWhere('acl.project_arr', 'LIKE', "%,0");
                });

                if (!empty($projectId)) {
                    $ledgerHeads->orWhere(function ($ledgerHeads) use ($projectId) {
                        $ledgerHeads->where('acl.project_arr', 'LIKE', "{$projectId}")
                            ->orWhere('acl.project_arr', 'LIKE', "{$projectId},%")
                            ->orWhere('acl.project_arr', 'LIKE', "%,{$projectId},%")
                            ->orWhere('acl.project_arr', 'LIKE', "%,{$projectId}");
                    });
                }
            })
            ->where(function ($ledgerHeads) use ($selBranchArr,$branchId, $projectId) {

                if ($branchId == -2 || $branchId > 0) {

                    ## for all branch selected project or all project
                    if (!empty($projectId)) {
                        $ledgerHeads->where(function ($ledgerHeads) use ($projectId) {

                            $ledgerHeads->where('acl.branch_arr', 'LIKE', "{$projectId}-0")
                                ->orWhere('acl.branch_arr', 'LIKE', "{$projectId}-0,%")
                                ->orWhere('acl.branch_arr', 'LIKE', "%,{$projectId}-0,%")
                                ->orWhere('acl.branch_arr', 'LIKE', "%,{$projectId}-0");
                        });
                    } else {
                        $ledgerHeads->where(function ($ledgerHeads) {
                            $ledgerHeads->where('acl.branch_arr', 'LIKE', "%-0%");
                        });
                    }

                    ## selected branch
                    $ledgerHeads->orWhere(function ($ledgerHeads) use ($branchId) {
                        if ($branchId == -2) { ## all without HO
                            $ledgerHeads->where('acl.branch_arr', 'NOT LIKE', "1");
                        } elseif ($branchId > 0) { ## selected branch

                            $ledgerHeads->where('acl.branch_arr', 'LIKE', "%,{$branchId},%")
                                ->orWhere('acl.branch_arr', 'LIKE', "{$branchId},%")
                                ->orWhere('acl.branch_arr', 'LIKE', "%,{$branchId}")
                                ->orWhere('acl.branch_arr', 'LIKE', "{$branchId}");
                        }
                    });
                }
                ## Get data for multiple branch
                if(count($selBranchArr) > 0 && $branchId != -1){
                    foreach ($selBranchArr as $branch) {

                        $ledgerHeads->where('acl.branch_arr', 'LIKE', "%,{$branch},%")
                            ->orWhere('acl.branch_arr', 'LIKE', "{$branch},%")
                            ->orWhere('acl.branch_arr', 'LIKE', "%,{$branch}")
                            ->orWhere('acl.branch_arr', 'LIKE', "{$branch}");
                    }
                }
            })
            // ->select('acl.id', 'acl.name', 'acl.code', 'acl.is_group_head', 'acl.parent_id', 'acl.acc_type_id', 'acl.level')
            // ->orderBy('acl.sys_code', 'ASC')
            // ->orderBy('acl.order_by', 'ASC')
            ->orderBy('acl.code', 'ASC')
            ->get();

        if ($ledgerHeads) {
            $ledgerData = $ledgerHeads;
        }

        return $ledgerData;
    }

    public static function getInformationForSummaryData($parameter = [], $searchBy, $fromData, $toDate, $fiscalYearId = null)
    {
        /** beforePeriod = (BP), OnPeriod = (OP)
         * beforePeriod hocche system er open date theke select date er aag porjonto date dhora hoyeche.
         * ja hocche opening balance er calculation.
         * Cumulative = OB table + beforePeriod Data + onPeriod Data
         */

        $return_data = array();

        ## check branch have year end or day end
        ## this calculation use for all accounting report

        $activeBranchArr  = array();
        $ignorBranchArr   = array();
        $incompleteReason = "";

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();
        $branchId      = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;
        $selBranchArr  = (isset($parameter['selBranchArr'])) ? $parameter['selBranchArr'] : array();

        $onPeriodDataFetch     = (isset($parameter['onPeriodDataFetch'])) ? $parameter['onPeriodDataFetch'] : true;
        $beforePeriodDataFetch = (isset($parameter['beforePeriodDataFetch'])) ? $parameter['beforePeriodDataFetch'] : true;

        ## Branch Id fetch
        if (count($selBranchArr) <= 0) {
            $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);
        }

        $branchWithAccStartDates = DB::table('gnl_branchs')
                            ->where([['is_delete',0], ['is_active', 1], ['is_approve', 1]])
                            ->pluck('acc_start_date', 'id')
                            ->toArray();

        ## ##### change kora hoyeche ignore branch kaj korchilo na tai
        // $activeBranchArr = $selBranchArr;

        if ($branchId == -1 || $branchId == -2 || empty($branchId)) {
            $branchId = Common::getBranchId();
        }

        $brOpeningDate   = new DateTime(Common::getBranchSoftwareStartDate($branchId, 'acc'));
        $loginSystemDate = new DateTime(Common::systemCurrentDate($branchId, 'acc'));

        $current_fiscal_year = Common::systemFiscalYear('', $companyId, $branchId, 'acc');
        $searching_fiscal_id = 0;

        ## beforePeriod Date Before Selected start date
        $startDateBP = $brOpeningDate;
        $endDateBP   = $loginSystemDate;

        ## this is for onPeriod this month
        $startDateThisMonth = $endDateThisMonth = null;
        $startDateOP        = $endDateOP        = null;

        ## this is for onPeriod retail month
        $sDateRetailAhead = $sDateFull = $sDateRetailLater = null;
        $eDateRetailAhead = $eDateFull = $eDateRetailLater = null;

        ## This calculation for searching parameter wise start Date & End date give
        ## this calculation use for all accounting report
        ## this calculation return startDate & endDate for this month & onPeriod time & beforePeriod time
        ## beforePeriod Date range dhora hoyecche selected start date er agg porjonto

        $obFetchFlag           = false;
        $currentYearFlag       = false;
        $onPeriodDataFetchFrom = "month&&voucher";

        if ($searchBy == 1) {
            ## Fiscal Year
            if ($fiscalYearId == null) {
                $notification = array(
                    'message' => 'Please select fiscal year.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $fiscal_year_data = DB::table('gnl_fiscal_year')
                ->where([['is_delete', 0], ['is_active', 1], ['id', $fiscalYearId]])
                ->first();

            if (empty($fiscal_year_data)) {
                $notification = array(
                    'message' => 'Fiscal Year not found.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $tempStartFY   = new DateTime($fiscal_year_data->fy_start_date);
            $tempEndDateFY = new DateTime($fiscal_year_data->fy_end_date);

            $startDateFY = clone $tempStartFY;
            $endDateFY   = clone $tempEndDateFY;

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateFY) && ($brOpeningDate <= $endDateFY)) {
                $startDateFY = $brOpeningDate;
                $obFetchFlag = true;
            }

            if (($loginSystemDate >= $startDateFY) && ($loginSystemDate <= $endDateFY)) {
                $endDateFY = $loginSystemDate;
            }

            ## date select for onPeriod data
            $startDateOP = $startDateFY;
            $endDateOP   = $endDateFY;

            ## date select for onPeriod this month data
            $startDateThisMonth = new DateTime($endDateFY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateFY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            if (($startDateOP >= $startDateThisMonth) && ($startDateOP <= $endDateThisMonth)) {
                $startDateThisMonth = $startDateOP;
            }

            ## date select for beforePeriod data
            $searching_fiscal_id = $fiscal_year_data->id;

            $startDateBP = $startDateBP;
            $tempStartY  = clone $startDateFY;
            $endDateBP   = $tempStartY->modify('-1 day');

            if ($endDateBP < $startDateBP) {
                $endDateBP = $startDateBP;
            }

            if ($searching_fiscal_id == $current_fiscal_year['id']) {
                $currentYearFlag == true;
            } else {
                $onPeriodDataFetchFrom = "year";
                $obFetchFlag           = false;
            }
        } elseif ($searchBy == 2) {
            ## Current Year
            if ($toDate == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $currentYearFlag == true;

            ## Get Current Fiscal Year Start Date
            $startDateCY = new DateTime($current_fiscal_year['fy_start_date']);
            $endDateCY   = new DateTime($toDate);

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateCY) && ($brOpeningDate <= $endDateCY)) {
                $startDateCY = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for onPeriod data
            $startDateOP = $startDateCY;
            $endDateOP   = $endDateCY;

            ## date select for onPeriod this month data
            $startDateThisMonth = new DateTime($endDateCY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateCY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            if (($startDateOP >= $startDateThisMonth) && ($startDateOP <= $endDateThisMonth)) {
                $startDateThisMonth = $startDateOP;
            }

            ## date select for beforePeriod data
            $searching_fiscal_id = $current_fiscal_year['id'];

            $startDateBP = $startDateBP;
            $tempStartY  = clone $startDateCY;
            $endDateBP   = $tempStartY->modify('-1 day');

            if ($endDateBP < $startDateBP) {
                $endDateBP = $startDateBP;
            }
        } elseif ($searchBy == 3 && $fromData != false && $toDate != false) {
            ## Date Range
            if ($fromData == null && $toDate == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $startDateDR = new DateTime($fromData);
            $endDateDR   = new DateTime($toDate);

            if (($brOpeningDate >= $startDateDR) && ($brOpeningDate <= $endDateDR)) {
                $startDateDR = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for onPeriod data
            $startDateOP = $startDateDR;
            $endDateOP   = $endDateDR;

            ## date select for onPeriod this month data
            $startDateThisMonth = new DateTime($endDateDR->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateDR;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            if (($startDateOP >= $startDateThisMonth) && ($startDateOP <= $endDateThisMonth)) {
                $startDateThisMonth = $startDateOP;
            }

            $startDateBP = $startDateBP;
            $tempStartY  = clone $startDateDR;
            $endDateBP   = $tempStartY->modify('-1 day');

            if ($endDateBP < $startDateBP) {
                $endDateBP = $startDateBP;
            }

            ## date select for beforePeriod data
            $searching_fiscal_id = Common::systemFiscalYear($startDateDR->format('Y-m-d'), $companyId, $branchId, 'acc')['id'];
        } elseif ($searchBy == 5) {
            ## Fiscal Year
            if ($fiscalYearId == null) {
                $notification = array(
                    'message' => 'Please select fiscal year.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            if ($toDate == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $fiscal_year_data = DB::table('gnl_fiscal_year')
                ->where([['is_delete', 0], ['is_active', 1], ['id', $fiscalYearId]])
                ->first();

            if (empty($fiscal_year_data)) {
                $notification = array(
                    'message' => 'Fiscal Year not found.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            // $tempStartFY   = new DateTime($fiscal_year_data->fy_start_date);
            // $tempEndDateFY = new DateTime($toDate);

            $startDateFY = new DateTime($fiscal_year_data->fy_start_date);
            $endDateFY   = new DateTime($toDate);

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateFY) && ($brOpeningDate <= $endDateFY)) {
                $startDateFY = $brOpeningDate;
                $obFetchFlag = true;
            }

            // if (($loginSystemDate >= $startDateFY) && ($loginSystemDate <= $endDateFY)) {
            //     $endDateFY = $loginSystemDate;
            // }

            ## date select for onPeriod data
            $startDateOP = $startDateFY;
            $endDateOP   = $endDateFY;

            ## date select for onPeriod this month data
            $startDateThisMonth = new DateTime($endDateFY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateFY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            if (($startDateOP >= $startDateThisMonth) && ($startDateOP <= $endDateThisMonth)) {
                $startDateThisMonth = $startDateOP;
            }

            ## date select for beforePeriod data
            $searching_fiscal_id = $fiscal_year_data->id;

            $startDateBP = $startDateBP;
            $tempStartY  = clone $startDateFY;
            $endDateBP   = $tempStartY->modify('-1 day');

            if ($endDateBP < $startDateBP) {
                $endDateBP = $startDateBP;
            }

            if ($searching_fiscal_id == $current_fiscal_year['id']) {
                $currentYearFlag == true;
            } else {
                // $onPeriodDataFetchFrom = "year";
                // $obFetchFlag         = false;
            }
        }
        
        $onPeriodDataFetchFromArr = array();

        // dd($selBranchArr, $activeBranchArr);

        if ($onPeriodDataFetch == true) {
            if ($onPeriodDataFetchFrom === "year") {
                ## check for only fiscal year
                ## check year end

                ## Check if Branch's acc_start_date is in between fiscal year 
                $fy_end_date = DB::table('gnl_fiscal_year')
                                        ->where('id', $searching_fiscal_id)
                                       ->first()->fy_end_date;

                $selBranchArr = DB::table('gnl_branchs')
                                ->whereIn('id',$selBranchArr)
                                ->where('acc_start_date', '<=', $fy_end_date)
                                ->pluck('id')
                                ->toArray();

                $activeBranchArr = DB::table('acc_year_end')
                    ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId], ['fiscal_year_id', $searching_fiscal_id]])
                    ->whereIn('branch_id', $selBranchArr)
                    ->groupBy('branch_id')
                    ->pluck('branch_id')
                    ->toArray();

                $ignorBranchArr = array_values(array_diff($selBranchArr, $activeBranchArr));

                if (count($ignorBranchArr) > 0) {
                    $incompleteReason = "year_not_found";
                }

                $sDateRetailAhead = $sDateFull = $sDateRetailLater = $startDateOP;
                $eDateRetailAhead = $eDateFull = $eDateRetailLater = $endDateOP;

                $onPeriodDataFetchFromArr['yearEnd'] = [
                    'fiscal_year_id' => $searching_fiscal_id,
                    'startDate'      => $startDateOP,
                    'endDate'        => $endDateOP,
                ];
            } else {

                if ($startDateOP == $endDateOP) {

                    $onPeriodDataFetchFrom = "voucher";
                    $sDateRetailAhead      = $sDateFull      = $sDateRetailLater      = $startDateOP;
                    $eDateRetailAhead      = $eDateFull      = $eDateRetailLater      = $endDateOP;

                    $activeBranchArr = $selBranchArr;

                    $onPeriodDataFetchFromArr['voucherLA'] = [
                        'startDate' => $sDateRetailLater,
                        'endDate'   => $eDateRetailLater,
                    ];
                } elseif ($startDateOP < $endDateOP) {
                    ## check for Current Year & Date Range
                    ## check month end
                    $onPeriodWorkingArr = HRS::systemWorkingDay("branch", [
                        'startDate' => $startDateOP->format('Y-m-d'),
                        'endDate'   => $endDateOP->format('Y-m-d'),
                        'companyId' => $companyId,
                        'branchId'  => $selBranchArr,
                    ]);

                    ## calculation for retail month
                    ## login branch dhore calculation kora hocche;
                    $durationMonthCount = (isset($onPeriodWorkingArr[$branchId])) ? count($onPeriodWorkingArr[$branchId]['working_month']) : 0;

                    if ($durationMonthCount > 2) {
                        ## ai calculation milbe jodi date difference 2 month er besi hoy
                        $tempRetailAhead = clone $startDateOP;
                        $tempRetailLater = clone $endDateOP;

                        $tempFullStart = clone $startDateOP;
                        $tempFullEnd   = clone $endDateOP;

                        ## onPeriod Date Ahead retail month
                        $sDateRetailAhead = $startDateOP;
                        $eDateRetailAhead = $tempRetailAhead->modify('last day of this month');

                        if ($eDateRetailAhead > $endDateOP) {
                            $eDateRetailAhead = $endDateOP;
                        }

                        $onPeriodDataFetchFromArr['voucherRA'] = [
                            'startDate' => $sDateRetailAhead,
                            'endDate'   => $eDateRetailAhead,
                        ];

                        ## onPeriod Date Full month
                        $sDateFull = $tempFullStart->modify('first day of next month');
                        $eDateFull = $tempFullEnd->modify('last day of previous month');

                        $onPeriodDataFetchFromArr['monthEnd'] = [
                            'startDate' => $sDateFull,
                            'endDate'   => $eDateFull,
                        ];

                        ## onPeriod Date Later retail month
                        $sDateRetailLater = $tempRetailLater->modify('first day of this month');
                        $eDateRetailLater = $endDateOP;

                        if ($sDateRetailLater < $startDateOP) {
                            $sDateRetailLater = $startDateOP;
                        }

                        $onPeriodDataFetchFromArr['voucherLA'] = [
                            'startDate' => $sDateRetailLater,
                            'endDate'   => $eDateRetailLater,
                        ];
                        ## End Retail month date calculation

                        ## check month end have or not
                        $checkMonthQuery = DB::table('acc_month_end')
                            ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId]])
                            ->whereBetween('month_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                            ->whereIn('branch_id', $selBranchArr)
                            ->groupBy('branch_id')
                            ->selectRaw('count(id) as count_id, branch_id')
                            ->pluck('count_id', 'branch_id')
                            ->toArray();

                        foreach ($selBranchArr as $singleB) {

                            ## if branch's acc start date greater than searching end date then ignor the branch
                            if(isset($branchWithAccStartDates[$singleB]) && $branchWithAccStartDates[$singleB] 
                                && $branchWithAccStartDates[$singleB] > $eDateFull->format('Y-m-d')){
                                continue;
                            }
                            
                            $workingMonthCount = (isset($onPeriodWorkingArr[$singleB]) && isset($onPeriodWorkingArr[$singleB]['working_month']))
                                ? count($onPeriodWorkingArr[$singleB]['working_month'])
                                : 0;

                            $monthEndCount = (isset($checkMonthQuery[$singleB])) ? ($checkMonthQuery[$singleB]) : 0;

                            ## 2 minus kora hocche karon retail month bad diye check dewa hocche
                            if (($workingMonthCount > 0) && (($workingMonthCount - 2) <= $monthEndCount)) {
                                array_push($activeBranchArr, $singleB);
                            } else {
                                array_push($ignorBranchArr, $singleB);
                            }
                        }

                        if (count($ignorBranchArr) > 0) {
                            $incompleteReason = "month_not_found";
                        }
                    } else {
                        $onPeriodDataFetchFrom = "voucher";
                        $sDateRetailAhead      = $sDateFull      = $sDateRetailLater      = $startDateOP;
                        $eDateRetailAhead      = $eDateFull      = $eDateRetailLater      = $endDateOP;

                        $activeBranchArr = $selBranchArr;

                        $onPeriodDataFetchFromArr['voucherLA'] = [
                            'startDate' => $startDateOP,
                            'endDate'   => $endDateOP,
                        ];
                    }
                }
            }
        }

        $onPeriodDataFetchFromArr['obData']            = $obFetchFlag;
        $onPeriodDataFetchFromArr['onPeriodDataFetch'] = $onPeriodDataFetch;

        $onPeriodDataFetchFromArr['dateRange'] = [
            'startDate' => $startDateOP,
            'endDate'   => $endDateOP,
        ];
        $onPeriodDataFetchFromArr['thisMonth'] = [
            'startDate' => $startDateThisMonth,
            'endDate'   => $endDateThisMonth,
        ];

        ## end for check month & year end data ache kina branch er.
        ########################### end of onPeriod data calculation #####################################

        ## start beforePeriod Date Calculation

        ## beforePeriod & onPeriod date gulo same hole beforePeriod er data tanbe na
        if ((($startDateOP == $startDateBP) && ($endDateOP == $endDateBP)) || ($startDateOP == $startDateBP)) {
            $beforePeriodDataFetch = false;
        }

        /**
         * beforePeriodOBFetchFlag by default true pathate hobe,
         * otherwise Trail Balance, Cash, Bank Book, Ledger, Branch Wise Ledge
         * ai report gulote date range diye search dile & date jodi opening date hoy tahole
         * ob table er data add korte pare na.
         * abar fiscal year theke data tanle obossoi subtract korte hobe ob data ke,
         * jaa trail balance e kora ache 649 no line a
         */
        $beforePeriodOBFetchFlag                  = true;
        $beforePeriodDataFetchFrom                = array();
        $beforePeriodDataFetchFrom['Theory']      = "beforePeriod Date = branch start date to branch current date.";
        $beforePeriodDataFetchFrom['Calculation'] = "Cumulative = onPeriod Data + beforePeriod Query Data";

        ## this variable for beforePeriod date
        $bpSDateRetailAhead = $bpSDateFull = $bpSDateRetailLater = null;
        $bpEDateRetailAhead = $bpEDateFull = $bpEDateRetailLater = null;

        if ($beforePeriodDataFetch == true) {

            ## normally balance sheet chara onno report gulo onPeriod period a opening balance table er data sum hoye dekhay na,
            ## seta opening balance hisebe heading thake, tai beforePeriodObFetchflag dorkar ache.

            $beforePeriodOBFetchFlag = true;

            $beforePeriodTempRA = clone $startDateBP;

            ## beforePeriod Date Ahead retail
            $bpSDateRetailAhead = $startDateBP;
            $bpEDateRetailAhead = $beforePeriodTempRA->modify('last day of this month');

            ## previous fiscal year found
            $pre_fiscal_year_data = DB::table('gnl_fiscal_year')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->where('id', '<', $searching_fiscal_id)
                ->select('id', 'company_id', 'fy_start_date', 'fy_end_date', 'fy_name')
                ->orderBy('id', 'DESC')
                ->limit(2)
                ->get();

            if (count($pre_fiscal_year_data->toArray()) > 0) {
                $beforePeriodOBFetchFlag = false;

                $pre_fiscal_year_arr = $pre_fiscal_year_data->toArray();
                $bpEDateRetailAhead  = new DateTime($pre_fiscal_year_arr[0]->fy_end_date);

                $beforePeriodDataFetchFrom["yearEnd"] = [
                    'pre_fiscal_id' => $pre_fiscal_year_arr[0]->id,
                    'startDate'     => new DateTime($pre_fiscal_year_arr[0]->fy_start_date),
                    'endDate'       => $bpEDateRetailAhead,
                ];

                if (isset($pre_fiscal_year_arr[1])) {
                    $beforePeriodDataFetchFrom["yearEnd"]['2nd_last_fiscal_year'] = [
                        'fiscal_id' => $pre_fiscal_year_arr[1]->id,
                        'startDate' => new DateTime($pre_fiscal_year_arr[1]->fy_start_date),
                        'endDate'   => new DateTime($pre_fiscal_year_arr[1]->fy_end_date),
                    ];
                }

                ## beforePeriod jehetu software opening date theke suru hocche tai tar RetailAhead bolte kicchu nei,
                ## amra year end er datak dhore nicche retailsAhed.
            } else {
                $beforePeriodDataFetchFrom["voucherRA"] = [
                    'startDate' => $bpSDateRetailAhead,
                    'endDate'   => $bpEDateRetailAhead,
                ];
            }
            ## end of beforePeriod retail ahed date calculation

            ## beforePeriod full month
            $beforePeriodTempFullStart = clone $bpEDateRetailAhead;
            $beforePeriodTempFullEnd   = clone $endDateBP;

            $bpSDateFull = $beforePeriodTempFullStart->modify('first day of next month');
            $bpEDateFull = $beforePeriodTempFullEnd;

            if ($bpSDateFull == $bpEDateFull) {
                $bpSDateRetailLater = $bpSDateFull;
                $bpEDateRetailLater = $bpEDateFull;

                $beforePeriodDataFetchFrom["voucherLA"] = [
                    'startDate' => $bpSDateRetailLater,
                    'endDate'   => $bpEDateRetailLater,
                ];
            } elseif ($bpSDateFull < $bpEDateFull) {

                ## check month end
                $beforePeriodWorkingArr = HRS::systemWorkingDay("branch", [
                    'startDate' => $bpSDateFull->format('Y-m-d'),
                    'endDate'   => $bpEDateFull->format('Y-m-d'),
                    'companyId' => $companyId,
                    'branchId'  => $branchId,
                ]);

                ## calculation date after year end
                ## login branch dhore calculation kora hocche;
                $beforePeriodMonthCount = (isset($beforePeriodWorkingArr[$branchId])) ? count($beforePeriodWorkingArr[$branchId]['working_month']) : 0;

                if ($beforePeriodMonthCount > 2) {
                    ## ai calculation milbe jodi date difference 2 month er besi hoy
                    ## beforePeriod er aikhane Ahead retail month lagbe na karo aikhane start e hobe month s1 taikh theke

                    $beforePeriodTempFE = clone $bpEDateFull;

                    $beforePeriodTempRLStart = clone $bpEDateFull;
                    $beforePeriodTempRLEnd   = clone $bpEDateFull;

                    ## beforePeriod month end data
                    $bpSDateFull = $bpSDateFull;
                    $bpEDateFull = $beforePeriodTempFE->modify('last day of previous month');

                    $beforePeriodDataFetchFrom["monthEnd"] = [
                        'startDate' => $bpSDateFull,
                        'endDate'   => $bpEDateFull,
                    ];

                    # beforePeriod voucher retail later data
                    $bpSDateRetailLater = $beforePeriodTempRLStart->modify('first day of this month');
                    $bpEDateRetailLater = $beforePeriodTempRLEnd;

                    $beforePeriodDataFetchFrom["voucherLA"] = [
                        'startDate' => $bpSDateRetailLater,
                        'endDate'   => $bpEDateRetailLater,
                    ];
                } else {

                    $bpSDateRetailLater = $bpSDateFull;
                    $bpEDateRetailLater = $bpEDateFull;

                    $beforePeriodDataFetchFrom["voucherLA"] = [
                        'startDate' => $bpSDateRetailLater,
                        'endDate'   => $bpEDateRetailLater,
                    ];
                }
            }
        }

        $beforePeriodDataFetchFrom['obData']                = $beforePeriodOBFetchFlag;
        $beforePeriodDataFetchFrom['beforePeriodDataFetch'] = $beforePeriodDataFetch;

        $beforePeriodDataFetchFrom['beforePeriodDateRange'] = [
            'startDate' => $startDateBP,
            'endDate'   => $endDateBP,
        ];

        // dd($onPeriodDataFetchFromArr, $beforePeriodDataFetchFrom);

        ## end beforePeriod Date Calculation

        $return_data = [
            'brOpeningDate'             => $brOpeningDate,
            'loginSystemDate'           => $loginSystemDate,
            'current_fiscal_year'       => $current_fiscal_year,
            'searching_fiscal_id'       => $searching_fiscal_id,

            'obFetchFlag'               => $obFetchFlag,
            'currentYearFlag'           => $currentYearFlag,

            'onPeriodDataFetchFrom'     => $onPeriodDataFetchFromArr,
            'beforePeriodDataFetchFrom' => $beforePeriodDataFetchFrom,

            'activeBranchArr'           => array_unique($activeBranchArr),

            'ignorBranchArr'            => array_unique($ignorBranchArr),

            'incompleteReason'          => $incompleteReason,
        ];

        return $return_data;
    }

    /**
     * in $req == ()
     * credit_arr credit ledger id arrays
     * debit_arr debit ledger id arrays
     * amount_arr amount array
     * narration_arr  for local narration array
     *
     * others all same as voucher and voucher details staructure
     * /// iff need module perameter would be added later as per need
     *
     * *******************to generate a voucher dont passs voucher code .... it will generate by it self
     * *******************for update a voucher pass voucher code
     *
     *
     */

    public static function insertVoucher(Request $req)
    {

        $RequestData = new Request;
        $RequestData = $req->all();
        // dd($RequestData);
        // $RequestData = array();
        $RequestData['company_id'] = Common::getCompanyId();
        $prep_by                   = (isset($RequestData['prep_by']) && !empty($RequestData['prep_by'])) ? $RequestData['prep_by'] : Auth::id();
        $module_id                 = (isset($RequestData['module_id']) && !empty($RequestData['module_id'])) ? $RequestData['module_id'] : Common::getModuleId();

        // $RequestData['prep_by'] = $prep_by;
        // $RequestData['created_by'] = $prep_by;

        $RequestData['prep_by']   = Auth::id();
        $RequestData['module_id'] = $module_id;
        // dd($RequestData);

        $CreditACC             = (isset($req->credit_arr) ? $req->credit_arr : array());
        $DebitACC              = (isset($req->debit_arr) ? $req->debit_arr : array());
        $amount_arr            = (isset($req->amount_arr) ? $req->amount_arr : array());
        $narration_arr         = (isset($req->narration_arr) ? $req->narration_arr : array());
        $target_branch_arr     = (isset($req->target_branch_arr) ? $req->target_branch_arr : array());
        $target_branch_acc_arr = (isset($req->target_branch_acc_arr) ? $req->target_branch_acc_arr : array());

        /*
        //  set module id for mmicro finance auto voucher code
        if((empty($RequestData['voucher_code']) || $RequestData['voucher_code'] == null) && $RequestData['v_generate_type'] == 1){
        // dd('juj');
        $previousVoucher =  Voucher::where(['voucher_date' =>  $RequestData['voucher_date'], 'v_generate_type' =>  $RequestData['v_generate_type'],'branch_id' => $RequestData['branch_id'], 'voucher_type_id' =>$RequestData['voucher_type_id'] ])->first();

        }

        if(!empty($previousVoucher)){
        $RequestData['voucher_code']=$previousVoucher->voucher_code;
        }
        // dd($CreditACC,$DebitACC);

         */

        if (!empty($RequestData['voucher_code'])) {

            $vouchercode = Voucher::where('voucher_code', $RequestData['voucher_code'])->first();

            if (!empty($vouchercode)) {

                $RequestData['voucher_code'] = self::generateBillVoucher($req->branch_id, $req->voucher_type_id, $req->project_id, $req->project_type_id);
            }
        } else {
            # code...
            $RequestData['voucher_code'] = self::generateBillVoucher($req->branch_id, $req->voucher_type_id, $req->project_id, $req->project_type_id);
        }

        //    dd($vouchercode);
        DB::beginTransaction();
        try {

            // $RequestData['branch_id'] = (int) $RequestData['branch_id'];

            // dd($RequestData['branch_id']);

            $isInsert = Voucher::create($RequestData);

            $voucherInserted = Voucher::where(['voucher_code' => $RequestData['voucher_code']])->first();

            if ($isInsert) {

                /* Child Table Insertion */
                // $RequestData2['branch_id'] = $RequestData['branch_id'];
                $RequestData2['voucher_id'] = $voucherInserted->id;
                $RequestData2['ft_from']    = (!empty($RequestData['ft_from'])) ? $RequestData['ft_from'] : 0;

                $total_amount = 0;
                foreach ($amount_arr as $key => $Row) {
                    if (!empty($Row)) {
                        $total_amount += $Row;

                        if ($DebitACC[$key] == 0 || $CreditACC[$key] == 0) {
                            $notification = array(
                                'message'    => 'Debit/Credit Ledger can not be zero !!',
                                'alert-type' => 'error',
                            );
                            return $notification;
                        }
                        $RequestData2['ft_to']           = (!empty($target_branch_arr[$key])) ? $target_branch_arr[$key] : 0;
                        $RequestData2['ft_target_acc']   = (!empty($target_branch_acc_arr[$key])) ? $target_branch_acc_arr[$key] : null;
                        $RequestData2['amount']          = $Row;
                        $RequestData2['debit_acc']       = $DebitACC[$key];
                        $RequestData2['credit_acc']      = $CreditACC[$key];
                        $RequestData2['local_narration'] = isset($narration_arr[$key]) ? $narration_arr[$key] : null;

                        $isInsertDetails = VoucherDetails::create($RequestData2);
                    }
                }

                $voucherInserted->total_amount = $total_amount;
                $voucherInserted->update();

                DB::commit();
                $notification = array(
                    'message'      => 'Successfully inserted Voucher and Details',
                    'alert-type'   => 'success',
                    'voucher_code' => $RequestData['voucher_code'],
                );
                return $notification;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $notification = array(
                'message'       => 'Unsuccessful to inserted Voucher Details',
                'alert-type'    => 'error',
                'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
            );

            return $notification;
        }
    }

    public static function updateVoucher(Request $req)
    {
        $ModelVoucher        = 'App\\Model\\Acc\\Voucher';
        $ModelVoucherDetails = 'App\\Model\\Acc\\VoucherDetails';

        $RequestData = new Request;
        $RequestData = $req->all();
        //dd($req->all());

        // $RequestData = array();

        $RequestData['branch_id']  = $req->branch_id;
        $RequestData['company_id'] = Common::getCompanyId();
        // $prep_by = (isset($RequestData['prep_by']) && !empty($RequestData['prep_by'])) ? $RequestData['prep_by'] : Auth::id();
        // $module_id = (isset($RequestData['module_id']) && !empty($RequestData['module_id'])) ? $RequestData['module_id'] : Common::getModuleId();

        // $RequestData['prep_by'] = $prep_by;
        // $RequestData['created_by'] = $prep_by;
        // $RequestData['module_id'] = $module_id;

        $CreditACC             = (isset($req->credit_arr) ? $req->credit_arr : array());
        $DebitACC              = (isset($req->debit_arr) ? $req->debit_arr : array());
        $amount_arr            = (isset($req->amount_arr) ? $req->amount_arr : array());
        $narration_arr         = (isset($req->narration_arr) ? $req->narration_arr : array());
        $target_branch_arr     = (isset($req->target_branch_arr) ? $req->target_branch_arr : array());
        $target_branch_acc_arr = (isset($req->target_branch_acc_arr) ? $req->target_branch_acc_arr : array());

        if (empty($RequestData['voucher_code']) || $RequestData['voucher_code'] == null) {

            $notification = array(
                'message'    => 'Voucher Code can not be empty for update !!',
                'alert-type' => 'error',
            );

            return $notification;
        }

        DB::beginTransaction();
        try {

            // dd($DebitACC,$CreditACC,$RequestData['voucher_code']);

            $isInsert        = Voucher::updateOrCreate(['voucher_code' => $RequestData['voucher_code']], $RequestData);
            $voucherInserted = Voucher::where(['voucher_code' => $RequestData['voucher_code']])->first();
            // dd($isInsert);
            if ($isInsert) {

                /* Child Table Insertion */
                $RequestData2['voucher_id'] = $voucherInserted->id;
                $RequestData2['ft_from']    = (!empty($RequestData['ft_from'])) ? $RequestData['ft_from'] : 0;

                $deleteDetailsObjs = VoucherDetails::where('voucher_id', $voucherInserted->id)->get();

                $updateIDArray = array();
                $total_amount  = 0;
                foreach ($amount_arr as $key => $Row) {
                    if (!empty($Row)) {
                        $total_amount += $Row;
                        if ($DebitACC[$key] == 0 || $CreditACC[$key] == 0) {
                            $notification = array(
                                'message'    => 'Debit/Credit Ledger can not be zero !!',
                                'alert-type' => 'error',
                            );
                            return $notification;
                        }

                        $RequestData2['ft_to']           = (!empty($target_branch_arr[$key])) ? $target_branch_arr[$key] : 0;
                        $RequestData2['ft_target_acc']   = (!empty($target_branch_acc_arr[$key])) ? $target_branch_acc_arr[$key] : null;
                        $RequestData2['amount']          = $Row;
                        $RequestData2['debit_acc']       = $DebitACC[$key];
                        $RequestData2['credit_acc']      = $CreditACC[$key];
                        $RequestData2['local_narration'] = isset($narration_arr[$key]) ? $narration_arr[$key] : null;

                        $updateid = $deleteDetailsObjs->where('debit_acc', $DebitACC[$key])
                            ->where('credit_acc', $CreditACC[$key])
                            ->whereNotIn('id', $updateIDArray)
                            ->first();

                        if (!empty($updateid)) {
                            array_push($updateIDArray, $updateid->id);
                            $isInsertDetails = VoucherDetails::updateOrCreate(['id' => $updateid->id], $RequestData2);
                        } else {
                            $isInsertDetails = VoucherDetails::create($RequestData2);
                        }
                    }
                }

                $tobeDeleteIDs = $deleteDetailsObjs->whereNotIn('id', $updateIDArray);
                VoucherDetails::whereIn('id', $tobeDeleteIDs->pluck('id')->toArray())->delete();

                $voucherInserted->total_amount = $total_amount;
                $voucherInserted->update();

                // dd($deleteDetailsObjs->all());
                DB::commit();

                $notification = array(
                    'message'      => 'Successfully Updated Voucher and Details',
                    'alert-type'   => 'success',
                    'voucher_code' => $RequestData['voucher_code'],
                );
                return $notification;
            }
        } catch (Exception $e) {
            DB::rollBack();
            $notification = array(
                'message'       => 'Unsuccessful to Updated Voucher and Details',
                'alert-type'    => 'error',
                'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
            );

            return $notification;
        }
    }

    public static function insertVouchermfn(Request $req)
    {

        $ModelVoucher        = 'App\\Model\\Acc\\Voucher';
        $ModelVoucherDetails = 'App\\Model\\Acc\\VoucherDetails';

        $RequestData                     = array();
        $RequestData['branch_id']        = $req->branch_id;
        $RequestData['module_id']        = $req->module_id;
        $RequestData['company_id']       = Common::getCompanyId();
        $RequestData['voucher_type_id']  = $req->voucher_type_id;
        $RequestData['voucher_status']   = $req->voucher_status;
        $RequestData['project_id']       = $req->project_id;
        $RequestData['project_type_id']  = $req->project_type_id;
        $RequestData['v_generate_type']  = $req->v_generate_type;
        $RequestData['voucher_date']     = $req->voucher_date;
        $RequestData['global_narration'] = $req->global_narration;
        $RequestData['voucher_code']     = $req->voucher_code;
        $RequestData['prep_by']          = Auth::id();
        // dd(empty($RequestData['voucher_code']));
        $RequestData['ft_from']       = (!empty($req->ft_from)) ? $req->ft_from : 0;
        $RequestData['ft_to']         = (!empty($req->ft_to)) ? $req->ft_to : 0;
        $RequestData['ft_target_acc'] = (!empty($req->ft_target_acc)) ? $req->ft_target_acc : null;

        $CreditACC     = (isset($req->credit_arr) ? $req->credit_arr : array());
        $DebitACC      = (isset($req->debit_arr) ? $req->debit_arr : array());
        $amount_arr    = (isset($req->amount_arr) ? $req->amount_arr : array());
        $narration_arr = (isset($req->narration_arr) ? $req->narration_arr : array());

        if (count($amount_arr) <= 0) {
            $previousVoucher = $ModelVoucher::where([
                ['voucher_date', $RequestData['voucher_date']],
                ['v_generate_type', $RequestData['v_generate_type']],
                ['branch_id', $RequestData['branch_id']],
                ['voucher_type_id', $RequestData['voucher_type_id']],
                ['module_id', $RequestData['module_id']],
            ])->delete();

            $notification = array(
                'message'    => 'Successfully inserted Voucher',
                'alert-type' => 'success',
            );
            return response()->json($notification);
        }

        // dd($CreditACC,  $DebitACC ,$amount_arr);

        if ((empty($RequestData['voucher_code']) || $RequestData['voucher_code'] == null) && $RequestData['v_generate_type'] == 1) {
            // dd('jujdfghfg');
            $previousVoucher = $ModelVoucher::where([
                ['voucher_date', $RequestData['voucher_date']],
                ['v_generate_type', $RequestData['v_generate_type']],
                ['branch_id', $RequestData['branch_id']],
                ['voucher_type_id', $RequestData['voucher_type_id']],
                ['module_id', $RequestData['module_id']],
            ])->first();
            // dd($previousVoucher);
        }
        // dd(1, $previousVoucher, 'sdfhfg');

        if (!empty($previousVoucher)) {
            $RequestData['voucher_code'] = $previousVoucher->voucher_code;
        }
        // dd($CreditACC,$DebitACC);

        if (empty($RequestData['voucher_code']) || $RequestData['voucher_code'] == null) {
            //   dd('if');

            $RequestData['voucher_code'] = self::generateBillVoucher($req->branch_id, $req->voucher_type_id, $req->project_id, $req->project_type_id);

            DB::beginTransaction();
            try {
                // dd('aaa');

                $isInsert        = $ModelVoucher::create($RequestData);
                $lastInsertQuery = $ModelVoucher::where('voucher_code', $RequestData['voucher_code'])->first();

                // dd('voucher inserted');
                if ($isInsert) {

                    /* Child Table Insertion */
                    $RequestData2['branch_id']  = $RequestData['branch_id'];
                    $RequestData2['voucher_id'] = $lastInsertQuery->id;

                    $RequestData2['ft_from']       = (!empty($RequestData['ft_from'])) ? $RequestData['ft_from'] : 0;
                    $RequestData2['ft_to']         = (!empty($RequestData['ft_to'])) ? $RequestData['ft_to'] : 0;
                    $RequestData2['ft_target_acc'] = (!empty($RequestData['ft_target_acc'])) ? $RequestData['ft_target_acc'] : null;

                    $total_amount = 0;
                    foreach ($amount_arr as $key => $Row) {
                        if (!empty($Row)) {
                            $RequestData2['amount'] = $Row;
                            $total_amount += $Row;

                            $RequestData2['debit_acc']       = $DebitACC[$key];
                            $RequestData2['credit_acc']      = $CreditACC[$key];
                            $RequestData2['local_narration'] = isset($narration_arr[$key]) ? $narration_arr[$key] : null;

                            $isInsertDetails = $ModelVoucherDetails::create($RequestData2);
                        }
                    }

                    $lastInsertQuery->total_amount = $total_amount;
                    $lastInsertQuery->update();

                    // if (count($amount_arr) == 0) {
                    //     $ModelVoucher::where('voucher_code', $RequestData['voucher_code'])->delete();
                    // }

                    DB::commit();
                    $notification = array(
                        'message'    => 'Successfully inserted Voucher',
                        'alert-type' => 'success',
                    );
                    return response()->json($notification);
                }
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message'       => 'Unsuccessful to inserted Voucher',
                    'alert-type'    => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );

                return response()->json($notification);
            }
        } else {
            // dd('else');
            DB::beginTransaction();
            try {

                // dd($DebitACC, $CreditACC, $RequestData['voucher_code']);

                $isInsert        = $ModelVoucher::updateOrCreate(['voucher_code' => $RequestData['voucher_code']], $RequestData);
                $lastInsertQuery = $ModelVoucher::where('voucher_code', $RequestData['voucher_code'])->first();

                // dd($isInsert);
                if ($isInsert) {

                    /* Child Table Insertion */
                    $RequestData2['branch_id']     = $RequestData['branch_id'];
                    $RequestData2['voucher_id']    = $lastInsertQuery->id;
                    $RequestData2['ft_from']       = (!empty($RequestData['ft_from'])) ? $RequestData['ft_from'] : 0;
                    $RequestData2['ft_to']         = (!empty($RequestData['ft_to'])) ? $RequestData['ft_to'] : 0;
                    $RequestData2['ft_target_acc'] = (!empty($RequestData['ft_target_acc'])) ? $RequestData['ft_target_acc'] : null;

                    $deleteDetailsObjs = $ModelVoucherDetails::where('voucher_id', $lastInsertQuery->id)->get();

                    $updateIDArray = array();

                    $total_amount = 0;
                    foreach ($amount_arr as $key => $Row) {
                        if (!empty($Row)) {
                            $RequestData2['amount'] = $Row;
                            $total_amount += $Row;

                            $RequestData2['debit_acc']       = $DebitACC[$key];
                            $RequestData2['credit_acc']      = $CreditACC[$key];
                            $RequestData2['local_narration'] = isset($narration_arr[$key]) ? $narration_arr[$key] : null;

                            $updateid = $deleteDetailsObjs->where('debit_acc', $DebitACC[$key])
                                ->where('credit_acc', $CreditACC[$key])
                                ->whereNotIn('id', $updateIDArray)
                                ->first();

                            // dd($updateid->id);

                            if (!empty($updateid)) {
                                array_push($updateIDArray, $updateid->id);
                                $isInsertDetails = $ModelVoucherDetails::updateOrCreate(['id' => $updateid->id], $RequestData2);
                            } else {
                                $isInsertDetails = $ModelVoucherDetails::create($RequestData2);
                            }
                        }
                    }
                    $lastInsertQuery->total_amount = $total_amount;
                    $lastInsertQuery->update();

                    $tobeDeleteIDs = $deleteDetailsObjs->whereNotIn('id', $updateIDArray);
                    $ModelVoucherDetails::whereIn('id', $tobeDeleteIDs->pluck('id')->toArray())->delete();

                    // if (count($amount_arr) == 0) {
                    //     $ModelVoucher::where('voucher_code', $RequestData['voucher_code'])->delete();
                    // }

                    DB::commit();

                    $notification = array(
                        'message'    => 'Successfully inserted Voucher',
                        'alert-type' => 'success',
                    );
                    return response()->json($notification);
                }
            } catch (Exception $e) {
                DB::rollBack();
                $notification = array(
                    'message'       => 'Unsuccessful to inserted Voucher',
                    'alert-type'    => 'error',
                    'console_error' => str_replace("\\", "(DS)", $e->getFile()) . "\\n" . $e->getLine() . "\\n" . $e->getMessage(),
                );

                return response()->json($notification);
            }
        }

        // dd($RequestData,'ll');
        // dd($DebitACC);

        # code...
    }

    //  --------------------------------------------------------- ACC Bill generate

    public static function generateBillAccOB($branchId = null)
    {
        // $BranchT = 'App\\Model\\GNL\\Branch';
        // $ModelT = "App\\Model\\Acc\\OpeningBalanceMaster";

        $BranchCodeQuery = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        // $ldate = date('Ym');

        $PreBillNo = "AOB" . $BranchCode;
        $record    = DB::table('acc_ob_m')
            ->select(['id', 'ob_no'])
            ->where('branch_id', $branchId)
            ->where('ob_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('ob_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->ob_no);

            $BillNo = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillMEB($branchId = null)
    {

        $BranchCodeQuery = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "MEB" . $BranchCode;
        $record    = DB::table('acc_month_end_balance_m')
            ->select(['id', 'eb_no'])
            ->where('branch_id', $branchId)
            ->where('eb_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('eb_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->eb_no);

            $BillNo = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillYEB($branchId = null)
    {
        $BranchCodeQuery = DB::table('gnl_branchs')
            ->where([['is_delete', 0], ['is_approve', 1], ['id', $branchId]])
            ->select('branch_code')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }

        $PreBillNo = "YEB" . $BranchCode;
        $record    = DB::table('acc_year_end_balance_m')
            ->select(['id', 'eb_no'])
            ->where('branch_id', $branchId)
            ->where('eb_no', 'LIKE', "{$PreBillNo}%")
            ->orderBy('eb_no', 'DESC')
            ->first();

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->eb_no);

            $BillNo = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
    }

    public static function generateBillVoucher($BranchID = null, $voucherType = null, $projectId = null, $project_typeID = null)
    {
        $BranchM      = 'App\\Model\\GNL\\Branch';
        $ProjectM     = 'App\\Model\\GNL\\Project';
        $ProjectTypeM = 'App\\Model\\GNL\\ProjectType';
        $VoucherM     = 'App\\Model\\Acc\\Voucher';
        $VoucherTypeM = 'App\\Model\\Acc\\VoucherType';

        $BranchCodeQuery = DB::table('gnl_branchs')->where([['is_delete', 0], ['is_approve', 1], ['id', $BranchID]])
            ->select('branch_code')
            ->first();

        // $ProjectCodeQuery = DB::table('gnl_projects')->where([['is_delete', 0], ['is_active', 1], ['id', $projectId]])
        //     ->select('project_code')
        //     ->first();
        $ProjectTypeCodeQuery = DB::table('gnl_project_types')->where([['is_delete', 0], ['is_active', 1], ['id', $project_typeID]])
            ->select('project_type_code')
            ->first();
        $VoucherTypeCodeQuery = DB::table('acc_voucher_type')->where([['is_delete', 0], ['is_active', 1], ['id', $voucherType]])
            ->select('short_name')
            ->first();

        if ($BranchCodeQuery) {
            $BranchCode = sprintf("%04d", $BranchCodeQuery->branch_code);
        } else {
            $BranchCode = sprintf("%04d", 0);
        }
        // dd($BranchCode);
        // $ProjectTypeCodeQuery
        $ProjectTypeCode = sprintf("%04d", $ProjectTypeCodeQuery->project_type_code);

        $PreBillNo = $VoucherTypeCodeQuery->short_name . $BranchCode . $ProjectTypeCode;

        $record = DB::table('acc_voucher')
            ->where('branch_id', $BranchID)
            ->select(['id', 'voucher_code'])
            ->where('voucher_code', 'LIKE', "{$PreBillNo}%")
            ->orderBy('voucher_code', 'DESC')
            ->first();

        // dd($record);

        if ($record) {
            $OldBillNoA = explode($PreBillNo, $record->voucher_code);
            $BillNo     = $PreBillNo . sprintf("%05d", ($OldBillNoA[1] + 1));
        } else {
            $BillNo = $PreBillNo . sprintf("%05d", 1);
        }

        return $BillNo;
        //return 'v';
    }

    public static function generateLedgerSysCode($parent_id = null, $accType = null, $gHead = null, $company_id = null)
    {
        $ModelAccType = 'App\\Model\\Acc\\AccountType';
        $ModelLedger  = 'App\\Model\\Acc\\Ledger';

        if ($company_id == null) {
            $company_id = Common::getCompanyId();
        }

        $genCode = "";

        if ($parent_id == 0) {
            $AccTypeData = $ModelAccType::where([['is_delete', 0], ['is_active', 1], ['id', $accType]])
                ->select('code')
                ->first();

            if ($AccTypeData) {
                $genCode = $AccTypeData->code;
            } else {
                $genCode = 0;
            }
        } else {
            $parentLedger = $ModelLedger::where([['is_delete', 0], ['is_active', 1], ['id', $parent_id], ['company_id', $company_id]])
                ->select('sys_code')
                ->first();

            $PreCode = "";

            if ($parentLedger) {
                $PreCode = $parentLedger->sys_code;

                $LedgerCount = $ModelLedger::where([['is_delete', 0], ['is_active', 1], ['company_id', $company_id]])
                    ->where('parent_id', $parent_id)
                    ->where('sys_code', 'LIKE', "{$PreCode}%")
                    ->count();

                if ($gHead == 0) {
                    $genCode = $PreCode . sprintf("%03d", (($LedgerCount == 0) ? 1 : $LedgerCount + 1));
                } else {
                    $genCode = $PreCode . sprintf("%02d", (($LedgerCount == 0) ? 1 : $LedgerCount + 1));
                }
            } else {
                $genCode = 0;
            }
        }

        return $genCode;
    }

    /* --------------------------------------------------------------------- generate bill End */

    public static function LedgerHTML($GlobalRole = null, $AccType = null, $branchId = null, $projectId = null, $projectTypeId = null)
    {
        if (!empty($AccType)) {
            $AccType = explode(',', $AccType);
        }

        $accTypeData = DB::table('acc_account_type')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->selectRaw('name AS acc_name, id')
            ->pluck('acc_name', 'id')
            ->toArray();

        $ledgerHeads = self::getLedgerData(['branchId' => $branchId, 'projectId' => $projectId, 'accType' => $AccType]);

        // dd($ledgerHeads);
        // if(count($ledgerHeads) < 1){
        //     return "Ledger Not found.";
        // }

        if (count($ledgerHeads) > 0) {
            $Data_query_group = $ledgerHeads->groupBy('parent_id');
            $DataSet          = array();

            $spaceCount = 0;
            $html       = '';

            if (isset($Data_query_group[0])) {
                $QueryData = $Data_query_group[0];
            } else {
                $ID        = $ledgerHeads->toarray()[0]->parent_id;
                $QueryData = $Data_query_group[$ID];
            }

            foreach ($QueryData as $RootData) {

                $accName = (isset($accTypeData[$RootData->acc_type_id])) ? $accTypeData[$RootData->acc_type_id] : "";

                $html .= '<tr>';
                // .$RootData->id.'-'
                $html .= "<td> " . self::$GlobalCount . " </td>";
                self::$GlobalCount++;
                $html .= '<td>';

                if ($RootData->is_group_head == 1) {
                    $html .= '<i class="fa fa-folder-open" aria-hidden="true"></i>';
                    $html .= '<span>&nbsp&nbsp' . $RootData->name . '</span>';
                    $html .= "</td>";

                    // /dd($RootData);
                    $html .= '<td class="text-center">' . $RootData->code . '</td>';
                    $html .= '<td class="text-center">' . $accName . '</td>';
                    // need to add two row
                    $action = '<a href="coa/add/';
                    $action .= $RootData->id;
                    $action .= '"><i class="icon wb-plus mr-2 blue-grey-600"></i>';
                    $action .= Role::roleWisePermission($GlobalRole, $RootData->id);
                    $html .= '<td class="text-center">' . $action . '</td>';
                    $html .= "</tr>";
                } else {
                    $html .= '<i class="fa fa-fighter-jet" aria-hidden="true"></i>';
                    $html .= '<span>&nbsp&nbsp' . $RootData->name . '</span>';
                    $html .= "</td>";
                    $html .= '<td class="text-center">' . $RootData->code . '</td>';
                    $html .= '<td class="text-center">' . $accName . '</td>';
                    // need to add two row
                    $action = Role::roleWisePermission($GlobalRole, $RootData->id);
                    $html .= '<td class="text-center">' . $action . '</td>';
                    $html .= "</tr>";
                }

                $html .= self::SubLedgerHTML($RootData->id, $Data_query_group, $spaceCount, $GlobalRole, $accTypeData);
            }
            return $html;
        }
    }

    public static function SubLedgerHTML($ParentID = null, $ParentArr = [], $count = null, $GlobalRole = null, $accTypeData = [])
    {
        $subHtml = "";
        $space   = "";

        $count++;

        if (isset($ParentArr[$ParentID])) {
            $SubArrData = $ParentArr[$ParentID];

            for ($i = 0; $i < $count; $i++) {
                $space .= "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
            }

            foreach ($SubArrData as $Subdata) {

                $accName = (isset($accTypeData[$Subdata->acc_type_id])) ? $accTypeData[$Subdata->acc_type_id] : "";

                $subHtml .= '<tr>';
                $subHtml .= "<td> " . self::$GlobalCount . "</td>";
                self::$GlobalCount++;

                $subHtml .= '<td>';

                $subHtml .= '<span>' . $space;

                if ($Subdata->is_group_head == 1) {
                    $subHtml .= '<i class="fa fa-folder-open" aria-hidden="true"></i>';
                    $subHtml .= '&nbsp&nbsp' . $Subdata->name . '</span>';

                    $subHtml .= "</td>";

                    $subHtml .= '<td class="text-center">' . $Subdata->code . '</td>';
                    $subHtml .= '<td class="text-center">' . $accName . '</td>';
                    // need to add two row

                    $action = '<a href="coa/add/';
                    $action .= $Subdata->id;
                    $action .= '"><i class="icon wb-plus mr-2 blue-grey-600"></i>';
                    $action .= Role::roleWisePermission($GlobalRole, $Subdata->id);
                    $subHtml .= '<td class="text-center">' . $action . '</td>';
                    $subHtml .= "</tr>";
                } else {
                    $subHtml .= '<i class="fa fa-fighter-jet" aria-hidden="true"></i>';
                    $subHtml .= '&nbsp&nbsp' . $Subdata->name . '</span>';

                    $subHtml .= "</td>";

                    $subHtml .= '<td class="text-center">' . $Subdata->code . '</td>';
                    $subHtml .= '<td class="text-center">' . $accName . '</td>';
                    // need to add two row
                    $action = Role::roleWisePermission($GlobalRole, $Subdata->id);

                    $subHtml .= '<td class="text-center">' . $action . '</td>';
                    $subHtml .= "</tr>";
                }

                $subHtml .= self::SubLedgerHTML($Subdata->id, $ParentArr, $count, $GlobalRole, $accTypeData);
            }
        } else {
            $count--;
        }

        // print_r($count);

        return $subHtml;
    }

    public static function childLedgerIds($parent = null)
    {

        $data = DB::table('acc_account_ledger')
            ->where([['is_delete', 0], ['is_active', 1], ['parent_id', $parent]])
            ->orderBy('order_by', 'ASC')
            ->get();

        $ids = [];
        foreach ($data as $roleData) {
            $ids[$roleData->id] = $roleData->id;
            $child              = Self::childLedgerIds($roleData->id);
            $ids                = array_merge($ids, $child);
        }

        return $ids;
    }

    /**
     * This function returns an object having ledger account information.
     * calling -- dd(ACC::getLedgerAccount(1, 3, null, 3));
     * @return object
     */
    public static function getTransactionalLedger($branchId = null, $id = null, $projectId = null, $projectTypeId = null)
    {

        $arrayResult = array();

        if ($branchId == null) {
            $branchId = Common::getBranchId();
        }

        if ($projectId == null) {
            $projectId = Common::getProjectId($branchId);
        }

        if ($projectTypeId == null) {
            $projectTypeId = Common::getProjectTypeId($branchId);
        }

        $ledgerHeads = self::getLedgerData(['branchId' => $branchId, 'projectId' => $projectId, 'projectTypeId' => $projectTypeId, 'parentId' => $id]);

        if (count($ledgerHeads) > 0) {
            if ($ledgerHeads->where('is_group_head', 0)->count() > 0) {
                $ids = $ledgerHeads->where('is_group_head', 0)->pluck('id')->toArray();
                array_merge($arrayResult, $ids);
            } else {
                $ledgerHeads = $ledgerHeads->where('is_group_head', 1);
            }
        }

        foreach ($ledgerHeads as $row) {

            if ($row->is_group_head == 0) {

                array_push($arrayResult, $row->id);
            } else {

                self::getTransactionalLedger($branchId, $row->id, $projectId, $projectTypeId);
            }
        }

        // return $arrayResult;
    }


    /**
     * get refined ledger ids depending on needs 
     * If we need Auto voucher COnfigaration wise ledger then we will call throw this function 
     * if we need a ledger auto v as well as menual voucher then we need to config that 
     * this function return ledgers without having auto v configs ledgers 
     */
    public static function getAutoVoucherRefinedLedger(
        $branchId = null,
        $projectId = null,
        $projectTypeId = null,
        $accType = null,
        $groupHead = 0,
        $level = null
    ) {

        $ledger = self::getLedgerAccount($branchId, $projectId, $projectTypeId, $accType, $groupHead, $level);

        $ledger = self::getWithoutAutoVoucher($ledger);

        return $ledger;
    }

    public static function getWithoutAutoVoucher($ledger)
    {

        $auto_voucher_ledgers = array();


        ## check pos module 
        if (!empty(DB::table('gnl_sys_modules')->where([['id', 2], ['is_active', 1]])->first())) {
            if (\Illuminate\Support\Facades\Schema::hasTable("pos_auto_voucher_config")) {
                $pos_a_v_ledgers = DB::table('pos_auto_voucher_config')->where([['is_delete', 0], ['is_active', 1]])->get();
                $array = $pos_a_v_ledgers->unique()->pluck('ledger_id')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array);
            }
        }


        ## check fam module 
        if (!empty(DB::table('gnl_sys_modules')->where([['id', 9], ['is_active', 1]])->first())) {
            if (\Illuminate\Support\Facades\Schema::hasTable("fam_auto_voucher_config")) {
                $pos_a_v_ledgers = DB::table('fam_auto_voucher_config')->where([['is_delete', 0], ['is_active', 1]])->get();
                $array = $pos_a_v_ledgers->unique()->pluck('ledger_id')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array);
            }
        }


        ## check inv module 
        if (!empty(DB::table('gnl_sys_modules')->where([['id', 10], ['is_active', 1]])->first())) {
            if (\Illuminate\Support\Facades\Schema::hasTable("inv_auto_voucher_config")) {
                $pos_a_v_ledgers = DB::table('inv_auto_voucher_config')->where([['is_delete', 0], ['is_active', 1]])->get();
                $array = $pos_a_v_ledgers->unique()->pluck('ledger_id')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array);
            }
        }

        ## check mfn module 
        if (!empty(DB::table('gnl_sys_modules')->where([['id', 5], ['is_active', 1]])->first())) {

            if (\Illuminate\Support\Facades\Schema::hasTable("mfn_auto_voucher_components") && \Illuminate\Support\Facades\Schema::hasTable("mfn_auto_voucher_config")) {

                $mfn_a_v_comp = DB::table('mfn_auto_voucher_components')->where([['is_delete', 0], ['status', 1]])->get();

                $mfn_a_v_set = DB::table('mfn_auto_voucher_config')->whereIn('componentId', $mfn_a_v_comp->unique()->pluck('id')->toArray())->get();

                $array = $mfn_a_v_set->whereNotNull('principalLedgerId')->unique()->pluck('principalLedgerId')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array);

                $array1 = $mfn_a_v_set->whereNotNull('interestLedgerId')->unique()->pluck('interestLedgerId')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array1);

                $array2 = $mfn_a_v_set->whereNotNull('interestProvisionLedgerId')->unique()->pluck('interestProvisionLedgerId')->toArray();
                $auto_voucher_ledgers = array_merge($auto_voucher_ledgers, $array2);
            }
        }

        $auto_voucher_ledgers = array_values(array_unique($auto_voucher_ledgers));

        ##filter ledgers having both voucher permission 
        ## acc_voucher_both_config table ledger which having both v permission
        $voucherPreventData = DB::table('acc_voucher_both_config')->where([['is_delete', 0], ['is_active', 1], ['auto_voucher', 0]])->get();
        $LedgerIDstoPrevent = array();

        $LedgerIDstoPrevent = $voucherPreventData->where('is_group_head', 0)->pluck('ledger_id')->toArray();
        $vpLooop            = $voucherPreventData->where('is_group_head', 1);

        foreach ($vpLooop as $key => $value) {
            # code...
            $array              = self::childLedgerIds($value->ledger_id);
            $LedgerIDstoPrevent = array_merge($LedgerIDstoPrevent, $array);
        }

        $LedgerIDstoPrevent = array_unique($LedgerIDstoPrevent);


        $auto_voucher_ledgers = array_filter($auto_voucher_ledgers, function ($value) use ($LedgerIDstoPrevent) {
            return !in_array($value, $LedgerIDstoPrevent);
        });

        ## filter end 

        foreach ($ledger as $key => $row) {

            if (in_array($row->id, $auto_voucher_ledgers)) {
                $ledger[$key]->disable_flag = 1;
            } else {
                $ledger[$key]->disable_flag = 0;
            }
        }

        return $ledger;
    }



    /**
     * This function returns an object having ledger account information.
     * calling -- dd(ACC::getLedgerAccount(1, 3, null, 3));
     * @return object
     */
    public static function getLedgerAccount(
        $branchId = null,
        $projectId = null,
        $projectTypeId = null,
        $accType = null,
        $groupHead = 0,
        $level = null
    ) {
        /**
         * $groupHead = 0 = Transectional Ledger
         * when $groupHead = null or $groupHead = '' or $groupHead pass kora na hole then fetch data grouphead = 0,
         * when $groupHead = 'all' then all data fetch
         * when $groupHead = :value then fetch data value wise
         */

        if ($branchId == null) {
            $branchId = Common::getBranchId();
        }

        if ($projectId == null) {
            $projectId = Common::getProjectId($branchId);
        }

        if ($projectTypeId == null) {
            $projectTypeId = Common::getProjectTypeId($branchId);
        }

        $ledgerHeads = self::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'accType'       => $accType,
            'level'         => $level,
            'groupHead'     => $groupHead,
        ]);

        return $ledgerHeads;
    }

    public static function getCashLedger($branchId = 1, $projectId = null, $groupHead = 0)
    {
        /**
         * $groupHead = 0 = Transectional Ledger
         * when $groupHead = null or $groupHead = '' or $groupHead pass kora na hole then fetch data grouphead = 0,
         * when $groupHead = 'all' then all data fetch
         * when $groupHead = :value then fetch data value wise
         */

        $projectTypeId = Common::getProjectTypeId($branchId);

        $ledgerHeads = self::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'accType'       => 4,
            'groupHead'     => $groupHead,
        ]);

        return $ledgerHeads;
    }

    public static function getBankLedger($branchId = 1, $projectId = null, $groupHead = 0)
    {
        /**
         * $groupHead = 0 = Transectional Ledger
         * when $groupHead = null or $groupHead = '' or $groupHead pass kora na hole then fetch data grouphead = 0,
         * when $groupHead = 'all' then all data fetch
         * when $groupHead = :value then fetch data value wise
         */

        $projectTypeId = Common::getProjectTypeId($branchId);

        $ledgerHeads = self::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'accType'       => 5,
            'groupHead'     => $groupHead,
        ]);

        return $ledgerHeads;
    }

    public static function getIncomeLedger($branchId = 1, $projectId = null, $groupHead = 0)
    {
        /**
         * $groupHead = 0 = Transectional Ledger
         * when $groupHead = null or $groupHead = '' or $groupHead pass kora na hole then fetch data grouphead = 0,
         * when $groupHead = 'all' then all data fetch
         * when $groupHead = :value then fetch data value wise
         */
        $projectTypeId = Common::getProjectTypeId($branchId);

        $ledgerHeads = self::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'accType'       => 12,
            'groupHead'     => $groupHead,
        ]);

        return $ledgerHeads;
    }

    public static function getExpenseLedger($branchId = 1, $projectId = null, $groupHead = 0)
    {
        /**
         * $groupHead = 0 = Transectional Ledger
         * when $groupHead = null or $groupHead = '' or $groupHead pass kora na hole then fetch data grouphead = 0,
         * when $groupHead = 'all' then all data fetch
         * when $groupHead = :value then fetch data value wise
         */

        $projectTypeId = Common::getProjectTypeId($branchId);

        $ledgerHeads = self::getLedgerData([
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
            'accType'       => 13,
            'groupHead'     => $groupHead,
        ]);

        return $ledgerHeads;
    }

    /**
     * Balance Calculation is for all calculation
     */
    public static function balanceCalculation($OB, $ledgerArray = [], $startDate, $endDate = null, $branchId = null, $voucherTypeId = null, $companyId = null, $projectId = null, $projectTypeId = null)
    {
        // dd($OB, $ledgerArray, $startDate, $branchId);
        /**
         * $OB = true or false
         */
        $companyId = (empty($companyId)) ? Common::getCompanyId() : $companyId;
        $branchId  = (empty($branchId)) ? Common::getBranchId() : $branchId;

        $startDate = new DateTime($startDate);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new DateTime($endDate);
        $endDate = $endDate->format('Y-m-d');

        $resultData = array();

        // dd($ledgerArray);

        /** -------------------- Opening Balance Calculation ------------------------- */

        // // // Data Fetch from OB Tables for date range
        $obDateRange = DB::table('acc_ob_m as obm')
            ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.is_year_end', 0]])
            ->where(function ($obDateRange) use ($startDate, $companyId, $branchId, $projectId, $projectTypeId) {

                if (!empty($startDate)) {
                    $obDateRange->where('obm.opening_date', '<=', $startDate);
                }

                // if (!empty($companyId)) {
                //     $obDateRange->where('obm.company_id', $companyId);
                // }

                if (!empty($branchId)) {

                    if ($branchId >= 0) {
                        $obDateRange->where('obm.branch_id', $branchId); // Individual Branch
                    } else if ($branchId == -2) {
                        $obDateRange->where('obm.branch_id', '!=', 1); // Branch without head office
                    }
                }

                if (!empty($projectId)) {
                    $obDateRange->where('obm.project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $obDateRange->where('obm.project_type_id', $projectTypeId);
                }
            })
            ->join('acc_ob_d as obd', function ($obDateRange) use ($ledgerArray) {
                $obDateRange->on('obd.ob_no', 'obm.ob_no')
                    ->whereIn('obd.ledger_id', $ledgerArray);
            })
            ->select(DB::raw('IFNULL(SUM(obd.debit_amount),0) as debit_amount,
                    IFNULL(SUM(obd.credit_amount),0) as credit_amount'))
            ->orderBy('obm.id', 'ASC')
            ->first();

        if ($obDateRange) {
            $resultData['ob_ttl_debit_amt']  = $obDateRange->debit_amount;
            $resultData['ob_ttl_credit_amt'] = $obDateRange->credit_amount;
        }
        // // // End OB Tables for date range

        if (count($ledgerArray) == 0) {

            $ledgerArray = [0];
        }

        // IF LEDGER EMPTY SET ARRAY ZERO FOR QUERRY ERROR

        // // // Data Fetch from Voucher Tables for date range & before start date
        $voucherBegOB = DB::table('acc_voucher as av')
            ->where([['av.is_delete', 0], ['av.is_active', 1]])
            ->whereIn('av.voucher_status', [1, 2])
            ->where(function ($voucherBegOB) use ($OB, $companyId, $startDate, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                // if (!empty($companyId)) {
                //     $voucherBegOB->where('av.company_id', $companyId);
                // }

                if (!empty($startDate)) {
                    if ($OB) {
                        $voucherBegOB->where('av.voucher_date', '<', $startDate);
                    } else {
                        $voucherBegOB->where('av.voucher_date', '<=', $startDate);
                    }
                }

                if (!empty($branchId)) {

                    if ($branchId > 0) {
                        $voucherBegOB->where('av.branch_id', $branchId); // Individual Branch
                    } else if ($branchId == -2) {
                        $voucherBegOB->where('av.branch_id', '<>', 1); // Branch without head office

                    }
                }

                if (!empty($projectId)) {
                    $voucherBegOB->where('av.project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $voucherBegOB->where('av.project_type_id', $projectTypeId);
                }

                if (!empty($voucherTypeId)) {
                    $voucherBegOB->where('av.voucher_type_id', $voucherTypeId);
                }
            })
            ->join('acc_voucher_details as avd', function ($voucherBegOB) use ($ledgerArray) {

                $voucherBegOB->on('avd.voucher_id', 'av.id')
                    ->where(function ($voucherBegOB) use ($ledgerArray) {
                        $voucherBegOB->whereIn('avd.debit_acc', $ledgerArray)
                            ->orWhereIn('avd.credit_acc', $ledgerArray);
                    });
            })
            ->select(
                DB::raw(
                    'IFNULL(SUM(CASE WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as debit_amount,
                        IFNULL(SUM(CASE WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as credit_amount'
                )
            )
            ->orderBy('av.voucher_date', 'ASC')
            ->first();

        if ($voucherBegOB) {
            $resultData['ob_ttl_debit_amt'] += $voucherBegOB->debit_amount;
            $resultData['ob_ttl_credit_amt'] += $voucherBegOB->credit_amount;
            // $ob_ttl_balance += $voucherBegOB->debit_amount  - $voucherBegOB->credit_amount;
        }
        /** -------------------- END Opening Balance Calculation ------------------------- */

        return $resultData;
    }

    public static function cash_bankBookReport($bankReport, $ledgerArray = [], $startDate, $endDate, $branchId = null, $voucherTypeId = null, $companyId = null, $projectId = null, $projectTypeId = null)
    {
        if ($bankReport && count($ledgerArray) > 1) {
            $bankAll = true;
        } else {
            $bankAll = false;
        }

        $companyId = (empty($companyId)) ? Common::getCompanyId() : $companyId;
        $branchId  = (empty($branchId)) ? Common::getBranchId() : $branchId;

        $startDate = new DateTime($startDate);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new DateTime($endDate);
        $endDate = $endDate->format('Y-m-d');

        $resultData = array();

        // Initialize  variable
        $ob_ttl_debit_amt  = 0;
        $ob_ttl_credit_amt = 0;
        $ob_ttl_balance    = 0;

        $sub_ttl_debit_amt  = 0;
        $sub_ttl_credit_amt = 0;
        $sub_ttl_balance    = 0;
        $sub_ttl_dr_or_cr   = 'Dr';

        $ttl_debit_amt  = 0;
        $ttl_credit_amt = 0;
        $ttl_balance    = 0;
        $ttl_dr_or_cr   = 'Dr';

        /** -------------------- Opening Balance Calculation ------------------------- */

        $OBData = self::balanceCalculation(true, $ledgerArray, $startDate, null, $branchId, $voucherTypeId, $companyId, $projectId, $projectTypeId);

        if ($OBData) {
            $ob_ttl_debit_amt  = $OBData['ob_ttl_debit_amt'];
            $ob_ttl_credit_amt = $OBData['ob_ttl_credit_amt'];
        }

        $ob_ttl_balance          = ($ob_ttl_debit_amt - $ob_ttl_credit_amt);
        $positive_ob_ttl_balance = $ob_ttl_balance;
        $positive_ob_ttl_balance = abs($positive_ob_ttl_balance);

        if ($ob_ttl_balance < 0) {
            $ob_ttl_credit_amt = $positive_ob_ttl_balance;
            $ob_ttl_debit_amt  = 0;
        } else {
            $ob_ttl_credit_amt = 0;
            $ob_ttl_debit_amt  = $positive_ob_ttl_balance;
        }
        /** -------------------- END Opening Balance Calculation ------------------------- */

        if (count($ledgerArray) == 0) {

            $ledgerArray = [0];
        }

        if ($bankAll == false) {

            $ledgerReport = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($ledgerReport) use ($companyId, $startDate, $endDate, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $ledgerReport->where('av.company_id', $companyId);
                    // }

                    if (!empty($startDate) && !empty($endDate)) {
                        $ledgerReport->whereBetween('av.voucher_date', [$startDate, $endDate]);
                    }

                    if (!empty($branchId)) {
                        if ($branchId >= 0) {
                            $ledgerReport->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $ledgerReport->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $ledgerReport->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $ledgerReport->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $ledgerReport->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->join('acc_voucher_details as avd', function ($ledgerReport) use ($ledgerArray) {
                    $ledgerReport->on('avd.voucher_id', 'av.id')
                        ->where(function ($ledgerReport) use ($ledgerArray) {
                            $ledgerReport->whereIn('avd.debit_acc', $ledgerArray)
                                ->orWhereIn('avd.credit_acc', $ledgerArray);
                        });
                })
                ->join('acc_account_ledger as acl', function ($ledgerReport) use ($ledgerArray) {
                    $ledgerReport->on(DB::raw('CASE
                                        WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.credit_acc
                                        WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.debit_acc
                                        END'), 'acl.id')
                        ->where([['acl.is_delete', 0], ['acl.is_active', 1], ['acl.is_group_head', 0]]);
                })
                ->select(
                    'acl.name',
                    'avd.local_narration',
                    'av.voucher_date',
                    'av.voucher_code',
                    DB::raw(
                        'IFNULL((CASE WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as debit_amount,
                        IFNULL((CASE WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as credit_amount'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->get();
        } else {

            $ledgerReport = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($ledgerReport) use ($companyId, $startDate, $endDate, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $ledgerReport->where('av.company_id', $companyId);
                    // }

                    if (!empty($startDate) && !empty($endDate)) {
                        $ledgerReport->whereBetween('av.voucher_date', [$startDate, $endDate]);
                    }

                    if (!empty($branchId)) {
                        if ($branchId >= 0) {
                            $ledgerReport->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $ledgerReport->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $ledgerReport->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $ledgerReport->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $ledgerReport->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->join('acc_voucher_details as avd', function ($ledgerReport) use ($ledgerArray) {
                    $ledgerReport->on('avd.voucher_id', 'av.id')
                        ->where(function ($ledgerReport) use ($ledgerArray) {
                            $ledgerReport->whereIn('avd.debit_acc', $ledgerArray)
                                ->orWhereIn('avd.credit_acc', $ledgerArray);
                        });
                })
                ->leftJoin('acc_account_ledger as acl', function ($ledgerReport) use ($ledgerArray) {
                    $ledgerReport->on(DB::raw('CASE
                                        WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.credit_acc
                                        -- WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.debit_acc
                                        END'), 'acl.id')
                        ->where([['acl.is_delete', 0], ['acl.is_active', 1], ['acl.is_group_head', 0]]);
                })
                ->leftJoin('acc_account_ledger as acl2', function ($ledgerReport) use ($ledgerArray) {
                    $ledgerReport->on(DB::raw('CASE
                                        -- WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.credit_acc
                                        WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.debit_acc
                                        END'), 'acl2.id')
                        ->where([['acl2.is_delete', 0], ['acl2.is_active', 1], ['acl2.is_group_head', 0]]);
                })
                ->select(
                    'acl.name as CreditName',
                    'acl2.name as DebitName',
                    'avd.local_narration',
                    'av.voucher_date',
                    'av.voucher_code',
                    DB::raw(
                        'IFNULL((CASE WHEN avd.debit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as debit_amount,
                        IFNULL((CASE WHEN avd.credit_acc IN (' . implode(',', $ledgerArray) . ') THEN avd.amount END), 0) as credit_amount'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->get();
        }

        if (count($ledgerReport->toarray()) > 0) {
            $sub_ttl_debit_amt  = $ledgerReport->sum('debit_amount');
            $sub_ttl_credit_amt = $ledgerReport->sum('credit_amount');
        }

        $ttl_debit_amt  = $sub_ttl_debit_amt + $ob_ttl_debit_amt;
        $ttl_credit_amt = $sub_ttl_credit_amt + $ob_ttl_credit_amt;

        ////////// ---End-- Data Fetch & calculation For During Date range from vouchers table-------////////////

        $tb          = $ob_ttl_balance;
        $positive_tb = $tb;

        $DataSet = array();
        $sl      = 1;

        if ($bankAll == false) {
            foreach ($ledgerReport as $key => $row) {
                $tempSet = array();

                $tb          = $tb + ($row->debit_amount - $row->credit_amount);
                $positive_tb = $tb;

                $tempSet = [
                    'sl'              => $sl++,
                    'voucher_date'    => $row->voucher_date,
                    'voucher_code'    => $row->voucher_code,
                    'account_head'    => $row->name,
                    'local_narration' => $row->local_narration,
                    'debit_amount'    => $row->debit_amount,
                    'credit_amount'   => $row->credit_amount,
                    'balance'         => number_format(abs($positive_tb), 2),
                    'debit_or_credit' => ($tb >= 0) ? 'Dr' : 'Cr',
                ];

                $DataSet[] = $tempSet;
            }
        } else {

            foreach ($ledgerReport as $key => $row) {
                $tempSet = array();

                $tb          = $tb + ($row->debit_amount - $row->credit_amount);
                $positive_tb = $tb;

                if (!empty($row->CreditName) && !empty($row->DebitName)) {

                    $tempSet = [
                        'sl'              => $sl++,
                        'voucher_date'    => $row->voucher_date,
                        'voucher_code'    => $row->voucher_code,
                        'account_head'    => $row->DebitName,
                        'local_narration' => $row->local_narration,
                        'debit_amount'    => $row->debit_amount,
                        'credit_amount'   => number_format(0, 2),
                        'balance'         => number_format(abs($tb + $row->debit_amount), 2),
                        'debit_or_credit' => (($tb + $row->debit_amount) >= 0) ? 'Dr' : 'Cr',
                    ];

                    $DataSet[] = $tempSet;

                    $tempSet = [
                        'sl'              => $sl++,
                        'voucher_date'    => $row->voucher_date,
                        'voucher_code'    => $row->voucher_code,
                        'account_head'    => $row->CreditName,
                        'local_narration' => $row->local_narration,
                        'debit_amount'    => number_format(0, 2),
                        'credit_amount'   => $row->credit_amount,
                        'balance'         => number_format(abs($positive_tb), 2),
                        'debit_or_credit' => ($tb >= 0) ? 'Dr' : 'Cr',
                    ];

                    $DataSet[] = $tempSet;
                } else {

                    $tempSet = [
                        'sl'              => $sl++,
                        'voucher_date'    => $row->voucher_date,
                        'voucher_code'    => $row->voucher_code,
                        'account_head'    => (!empty($row->CreditName)) ? $row->CreditName : $row->DebitName,
                        'local_narration' => $row->local_narration,
                        'debit_amount'    => $row->debit_amount,
                        'credit_amount'   => $row->credit_amount,
                        'balance'         => number_format(abs($positive_tb), 2),
                        'debit_or_credit' => ($tb >= 0) ? 'Dr' : 'Cr',
                    ];

                    $DataSet[] = $tempSet;
                }
            }
        }

        $resultData = [
            'ob_ttl_debit_amt'   => $ob_ttl_debit_amt,
            'ob_ttl_credit_amt'  => $ob_ttl_credit_amt,
            'ob_ttl_balance'     => $ob_ttl_balance,

            'sub_ttl_debit_amt'  => $sub_ttl_debit_amt,
            'sub_ttl_credit_amt' => $sub_ttl_credit_amt,
            'sub_ttl_balance'    => $sub_ttl_balance,

            'ttl_debit_amt'      => $ttl_debit_amt,
            'ttl_credit_amt'     => $ttl_credit_amt,
            'ttl_balance'        => ($ttl_debit_amt - $ttl_credit_amt),

            'DataSet'            => $DataSet,
        ];

        return $resultData;
    }

    /**
     * blCalculationIE is stand for Balance Calculation for Income Expense,
     * its return array data,
     */
    public static function blCalculationIE($branchWise, $startDate = null, $endDate = null, $branchId = null, $companyId = null, $projectId = null, $projectTypeId = null, $voucherTypeId = null)
    {
        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        if ($toDate == null) {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        $resultData = array();

        if (Common::getDBConnection() == "sqlite") {
            $queryData = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($queryData) use ($companyId, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $queryData->where('av.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {
                        if ($branchId > 0) {
                            $queryData->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $queryData->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $queryData->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $queryData->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $queryData->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->where(function ($queryData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $queryData->whereBetween('av.voucher_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $queryData->where('av.voucher_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $queryData->where('av.voucher_date', '<=', $toDate);
                    }
                })
                ->join('acc_voucher_details as avd', function ($queryData) {
                    $queryData->on('avd.voucher_id', 'av.id');
                })
                ->join('acc_account_ledger as acl', function ($queryData) {
                    $queryData->on(function ($queryData) {
                        $queryData->on('acl.id', 'avd.debit_acc')
                            ->orOn('acl.id', 'avd.credit_acc');
                    });
                    $queryData->whereIn('acl.acc_type_id', [12, 13]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('av.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0) as sum_debit_income,
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0) as sum_credit_income,
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0) as sum_debit_expense,
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0) as sum_credit_expense,
                    (
                        IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                    ) as income_amount,
                    (
                        IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                    ) as expense_amount,
                    (
                        (
                            IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                            -
                            IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                        )
                        -
                        (
                            IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                            -
                            IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                        )
                    ) as surplus_amount,
                    av.branch_id, (br.branch_name || " [" || br.branch_code "]" ||) as branch_name'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->groupBy('av.branch_id')
                ->get();
        } else {
            $queryData = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($queryData) use ($companyId, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $queryData->where('av.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {
                        if ($branchId > 0) {
                            $queryData->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $queryData->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $queryData->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $queryData->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $queryData->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->where(function ($queryData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $queryData->whereBetween('av.voucher_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $queryData->where('av.voucher_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $queryData->where('av.voucher_date', '<=', $toDate);
                    }
                })
                ->join('acc_voucher_details as avd', function ($queryData) {
                    $queryData->on('avd.voucher_id', 'av.id');
                })
                ->join('acc_account_ledger as acl', function ($queryData) {
                    $queryData->on(function ($queryData) {
                        $queryData->on('acl.id', 'avd.debit_acc')
                            ->orOn('acl.id', 'avd.credit_acc');
                    });
                    $queryData->whereIn('acl.acc_type_id', [12, 13]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('av.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0) as sum_debit_income,
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0) as sum_credit_income,
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0) as sum_debit_expense,
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0) as sum_credit_expense,
                    (
                        IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                    ) as income_amount,
                    (
                        IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                    ) as expense_amount,
                    (
                        (
                            IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                            -
                            IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 12 THEN avd.amount END), 0)
                        )
                        -
                        (
                            IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                            -
                            IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 13 THEN avd.amount END), 0)
                        )
                    ) as surplus_amount,
                    av.branch_id, CONCAT(br.branch_code, "-", br.branch_name) as branch_name'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->groupBy('av.branch_id')
                ->get();
        }

        // dd($queryData);

        if ($branchWise == false) {
            // $resultData['sum_debit_income'] = $queryData->sum('sum_debit_income');
            // $resultData['sum_credit_income'] = $queryData->sum('sum_credit_income');
            // $resultData['sum_debit_expense'] = $queryData->sum('sum_debit_expense');
            // $resultData['sum_credit_expense'] = $queryData->sum('sum_credit_expense');
            $resultData['income_amount']  = $queryData->sum('income_amount');
            $resultData['expense_amount'] = $queryData->sum('expense_amount');
            $resultData['surplus_amount'] = $queryData->sum('surplus_amount');
        } else {
            $resultData[0]['income_amount']  = $queryData->sum('income_amount');
            $resultData[0]['expense_amount'] = $queryData->sum('expense_amount');
            $resultData[0]['surplus_amount'] = $queryData->sum('surplus_amount');

            foreach ($queryData as $row) {
                $resultData[$row->branch_name]['income_amount']  = $row->income_amount;
                $resultData[$row->branch_name]['expense_amount'] = $row->expense_amount;
                $resultData[$row->branch_name]['surplus_amount'] = $row->surplus_amount;
            }
        }

        return $resultData;
    }

    /* Cakculate Cash or Bank Amount (Amount = Debit - Credit) depending on Ledger IDs
    Param ($startDate = optional, $endDate = optional,
    $ledgerIds = Either Cash type or Bank Type Acc Ids, $branchId)
     */
    public static function blCalculationCB($branchWise, $startDate = null, $endDate = null, $branchId = null, $companyId = null, $projectId = null, $projectTypeId = null, $voucherTypeId = null)
    {

        // $companyId = (empty($companyId)) ? Common::getCompanyId() : $companyId;
        // $branchId = (empty($branchId)) ? Common::getBranchId() : $branchId;

        $fromDate = null;
        $toDate   = null;

        if ($startDate == '') {
            $startDate = null;
        }

        if ($endDate == '') {
            $endDate = null;
        }

        if (!empty($startDate)) {
            $fromDate = (new DateTime($startDate))->format('Y-m-d');
        }

        if (!empty($endDate)) {
            $toDate = (new DateTime($endDate))->format('Y-m-d');
        } else {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        if ($toDate == null) {
            $toDate = (new DateTime(Common::systemCurrentDate()))->format('Y-m-d');
        }

        $resultData = array();

        ///////////////////////////

        if (Common::getDBConnection() == "sqlite") {
            $obData = DB::table('acc_ob_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.is_year_end', 0]])
                ->where(function ($obData) use ($companyId, $branchId, $projectId, $projectTypeId) {

                    // if (!empty($companyId)) {
                    //     $obData->where('obm.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {

                        if ($branchId >= 0) {
                            $obData->where('obm.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $obData->where('obm.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $obData->where('obm.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $obData->where('obm.project_type_id', $projectTypeId);
                    }
                })
                ->where(function ($obData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $obData->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $obData->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $obData->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('acc_ob_d as obd', function ($obData) {
                    $obData->on('obd.ob_no', 'obm.ob_no');
                })
                ->join('acc_account_ledger as acl', function ($obData) {
                    $obData->on('acl.id', 'obd.ledger_id');
                    $obData->whereIn('acl.acc_type_id', [4, 5]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('obm.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.debit_amount END), 0) as sum_debit_cash,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.credit_amount END), 0) as sum_credit_cash,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.debit_amount END), 0) as sum_debit_bank,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.credit_amount END), 0) as sum_credit_bank,
                    (
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.debit_amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.credit_amount END), 0)
                    ) as cash_amount,
                    (
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.debit_amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.credit_amount END), 0)
                    ) as bank_amount,
                    obm.branch_id, (br.branch_code || "-" || br.branch_name) as branch_name'
                    )
                )
                ->orderBy('obm.opening_date', 'ASC')
                ->groupBy('obm.branch_id')
                ->get();

            ////////////////////////////////////////////////
            $queryData = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($queryData) use ($companyId, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $queryData->where('av.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {
                        if ($branchId > 0) {
                            $queryData->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $queryData->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $queryData->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $queryData->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $queryData->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->where(function ($queryData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $queryData->whereBetween('av.voucher_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $queryData->where('av.voucher_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $queryData->where('av.voucher_date', '<=', $toDate);
                    }
                })
                ->join('acc_voucher_details as avd', function ($queryData) {
                    $queryData->on('avd.voucher_id', 'av.id');
                })
                ->join('acc_account_ledger as acl', function ($queryData) {
                    $queryData->on(function ($queryData) {
                        $queryData->on('acl.id', 'avd.debit_acc')
                            ->orOn('acl.id', 'avd.credit_acc');
                    });
                    $queryData->whereIn('acl.acc_type_id', [4, 5]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('av.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0) as sum_debit_cash,
                IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0) as sum_credit_cash,
                IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0) as sum_debit_bank,
                IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0) as sum_credit_bank,
                (
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0)
                    -
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0)
                ) as cash_amount,
                (
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0)
                    -
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0)
                ) as bank_amount,
                av.branch_id, (br.branch_code || "-" || br.branch_name) as branch_name'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->groupBy('av.branch_id')
                ->get();
        } else {
            $obData = DB::table('acc_ob_m as obm')
                ->where([['obm.is_delete', 0], ['obm.is_active', 1], ['obm.is_year_end', 0]])
                ->where(function ($obData) use ($companyId, $branchId, $projectId, $projectTypeId) {

                    // if (!empty($companyId)) {
                    //     $obData->where('obm.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {

                        if ($branchId >= 0) {
                            $obData->where('obm.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $obData->where('obm.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $obData->where('obm.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $obData->where('obm.project_type_id', $projectTypeId);
                    }
                })
                ->where(function ($obData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $obData->whereBetween('obm.opening_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $obData->where('obm.opening_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $obData->where('obm.opening_date', '<=', $toDate);
                    }
                })
                ->join('acc_ob_d as obd', function ($obData) {
                    $obData->on('obd.ob_no', 'obm.ob_no');
                })
                ->join('acc_account_ledger as acl', function ($obData) {
                    $obData->on('acl.id', 'obd.ledger_id');
                    $obData->whereIn('acl.acc_type_id', [4, 5]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('obm.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.debit_amount END), 0) as sum_debit_cash,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.credit_amount END), 0) as sum_credit_cash,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.debit_amount END), 0) as sum_debit_bank,
                    IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.credit_amount END), 0) as sum_credit_bank,
                    (
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.debit_amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 4 THEN obd.credit_amount END), 0)
                    ) as cash_amount,
                    (
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.debit_amount END), 0)
                        -
                        IFNULL(SUM(CASE WHEN acl.acc_type_id = 5 THEN obd.credit_amount END), 0)
                    ) as bank_amount,
                    obm.branch_id, CONCAT(br.branch_code, "-", br.branch_name) as branch_name'
                    )
                )
                ->orderBy('obm.opening_date', 'ASC')
                ->groupBy('obm.branch_id')
                ->get();

            ////////////////////////////////////////////////
            $queryData = DB::table('acc_voucher as av')
                ->where([['av.is_delete', 0], ['av.is_active', 1]])
                ->whereIn('av.voucher_status', [1, 2])
                ->where(function ($queryData) use ($companyId, $branchId, $projectId, $projectTypeId, $voucherTypeId) {
                    // if (!empty($companyId)) {
                    //     $queryData->where('av.company_id', $companyId);
                    // }

                    if (!empty($branchId)) {
                        if ($branchId > 0) {
                            $queryData->where('av.branch_id', $branchId); // Individual Branch
                        } else if ($branchId == -2) {
                            $queryData->where('av.branch_id', '!=', 1); // Branch without head office
                        }
                    }

                    if (!empty($projectId)) {
                        $queryData->where('av.project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $queryData->where('av.project_type_id', $projectTypeId);
                    }

                    if (!empty($voucherTypeId)) {
                        $queryData->where('av.voucher_type_id', $voucherTypeId);
                    }
                })
                ->where(function ($queryData) use ($fromDate, $toDate) {
                    if (!empty($fromDate) && !empty($toDate)) {
                        $queryData->whereBetween('av.voucher_date', [$fromDate, $toDate]);
                    }

                    if (!empty($fromDate) && empty($toDate)) {
                        $queryData->where('av.voucher_date', '>=', $fromDate);
                    }

                    if (empty($fromDate) && !empty($toDate)) {
                        $queryData->where('av.voucher_date', '<=', $toDate);
                    }
                })
                ->join('acc_voucher_details as avd', function ($queryData) {
                    $queryData->on('avd.voucher_id', 'av.id');
                })
                ->join('acc_account_ledger as acl', function ($queryData) {
                    $queryData->on(function ($queryData) {
                        $queryData->on('acl.id', 'avd.debit_acc')
                            ->orOn('acl.id', 'avd.credit_acc');
                    });
                    $queryData->whereIn('acl.acc_type_id', [4, 5]);
                })
                ->join('gnl_branchs as br', function ($queryData) {
                    $queryData->on('av.branch_id', 'br.id');
                    $queryData->where([['br.is_active', 1], ['br.is_delete', 0], ['br.is_approve', 1]]);
                    $queryData->whereIn('br.id', HRS::getUserAccesableBranchIds());
                })
                ->select(
                    DB::raw(
                        'IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0) as sum_debit_cash,
                IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0) as sum_credit_cash,
                IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0) as sum_debit_bank,
                IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0) as sum_credit_bank,
                (
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0)
                    -
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 4 THEN avd.amount END), 0)
                ) as cash_amount,
                (
                    IFNULL(SUM(CASE WHEN avd.debit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0)
                    -
                    IFNULL(SUM(CASE WHEN avd.credit_acc = acl.id and acl.acc_type_id = 5 THEN avd.amount END), 0)
                ) as bank_amount,
                av.branch_id, CONCAT(br.branch_code, "-", br.branch_name) as branch_name'
                    )
                )
                ->orderBy('av.voucher_date', 'ASC')
                ->groupBy('av.branch_id')
                ->get();
        }

        if ($branchWise == false) {

            $resultData['Cash']          = $obData->sum('cash_amount') + $queryData->sum('cash_amount');
            $resultData['Bank']          = $obData->sum('bank_amount') + $queryData->sum('bank_amount');
            $resultData['Total_Balance'] = $resultData['Cash'] + $resultData['Bank'];
        } else {
            $resultData[0]['Cash']          = $obData->sum('cash_amount') + $queryData->sum('cash_amount');
            $resultData[0]['Bank']          = $obData->sum('bank_amount') + $queryData->sum('bank_amount');
            $resultData[0]['Total_Balance'] = $resultData[0]['Cash'] + $resultData[0]['Bank'];

            // Opening Balance Table
            foreach ($obData as $row) {
                $resultData[$row->branch_name]['Cash']          = $row->cash_amount;
                $resultData[$row->branch_name]['Bank']          = $row->bank_amount;
                $resultData[$row->branch_name]['Total_Balance'] = $row->cash_amount + $row->bank_amount;
            }

            // Voucher Table
            foreach ($queryData as $rowQ) {
                if (isset($resultData[$rowQ->branch_name])) {
                    $resultData[$rowQ->branch_name]['Cash'] += $rowQ->cash_amount;
                    $resultData[$rowQ->branch_name]['Bank'] += $rowQ->bank_amount;
                    $resultData[$rowQ->branch_name]['Total_Balance'] += $rowQ->cash_amount + $rowQ->bank_amount;
                } else {
                    $resultData[$rowQ->branch_name]['Cash']          = $rowQ->cash_amount;
                    $resultData[$rowQ->branch_name]['Bank']          = $rowQ->bank_amount;
                    $resultData[$rowQ->branch_name]['Total_Balance'] = $rowQ->cash_amount + $rowQ->bank_amount;
                }
            }
        }

        // $resultData['Cash'] = $obData->cash_amount + $queryData->cash_amount;
        // $resultData['Bank'] = $obData->bank_amount + $queryData->bank_amount;
        // $resultData['Total_Balance'] = $resultData['Cash'] + $resultData['Bank'];

        return $resultData;
    }

    ///////////////////////////////////////////////////////
    // Profit/Loss from income statement

    public static function funcIncomeStatememnt(
        $startDate,
        $endDate,
        $ledgerChilds = [],
        $branchId = null,
        $projectId = null,
        $projectTypeId = null
    ) {

        $companyId = Common::getCompanyId();

        $startDateY = $startDate;
        $endDateY   = $endDate;

        $fiscal_year         = Common::systemFiscalYear($startDate, $companyId);
        $searching_fiscal_id = $fiscal_year['id'];

        $obDebitData  = array();
        $obCreditData = array();

        $duringYearDebitData  = array();
        $duringYearCreditData = array();

        ## Opening Balance
        /**
         * cumulative er jonno ob data ana lagbe na karon year end er data porche ob data calculation korei.
         * income statement a ob table theke data normaly ase na but searching date & ob
         * date er fiscal year same hoy tahole ob table theke data asbe
         */
        $openingBalanceQuery = DB::table('acc_ob_m')
            ->where([
                ['is_delete', 0], ['is_active', 1],
                ['is_year_end', 0],
                ['fiscal_year_id', $searching_fiscal_id],
            ])
            // ->whereIn('branch_id', $selBranchArr)
            ->where(function ($openingBalanceQuery) use ($branchId, $companyId, $projectId, $projectTypeId) {
                if (!empty($branchId)) {
                    if ($branchId == -2) {
                        $openingBalanceQuery->where('branch_id', '<>', 1);
                    } elseif ($branchId > 0) {
                        $openingBalanceQuery->where('branch_id', $branchId);
                    }
                }

                if (!empty($companyId)) {
                    $openingBalanceQuery->where('company_id', $companyId);
                }

                if (!empty($projectId)) {
                    $openingBalanceQuery->where('project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $openingBalanceQuery->where('project_type_id', $projectTypeId);
                }
            })
            ->selectRaw('ob_no as ob_no, branch_id')
            ->pluck('ob_no', 'branch_id')
            ->toArray();

        $obNoForOpenBL = (count($openingBalanceQuery) > 0) ? array_values($openingBalanceQuery) : array();

        if (count($obNoForOpenBL) > 0) {
            $obDebitData = DB::table('acc_ob_d')
                ->whereIn('ob_no', $obNoForOpenBL)
                ->groupBy('ledger_id')
                ->selectRaw('SUM(IFNULL(debit_amount, 0)) as cl_debit_amount, ledger_id as cl_ledger_id')
                ->pluck('cl_debit_amount', 'cl_ledger_id')
                ->toArray();

            $obCreditData = DB::table('acc_ob_d')
                ->whereIn('ob_no', $obNoForOpenBL)
                ->groupBy('ledger_id')
                ->selectRaw('SUM(IFNULL(credit_amount, 0)) as cl_credit_amount, ledger_id as cl_ledger_id')
                ->pluck('cl_credit_amount', 'cl_ledger_id')
                ->toArray();
        }
        ## End Opening Balance Query

        ## Query For during this period data
        $duringQuery = DB::table('acc_voucher')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->whereIn('voucher_status', [1, 2])
            // ->whereIn('branch_id', $selBranchArr)
            ->where(function ($duringQuery) use ($branchId, $companyId, $projectId, $projectTypeId) {
                if (!empty($branchId)) {
                    if ($branchId == -2) {
                        $duringQuery->where('branch_id', '<>', 1);
                    } elseif ($branchId > 0) {
                        $duringQuery->where('branch_id', $branchId);
                    }
                }

                if (!empty($companyId)) {
                    $duringQuery->where('company_id', $companyId);
                }

                if (!empty($projectId)) {
                    $duringQuery->where('project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $duringQuery->where('project_type_id', $projectTypeId);
                }
            })
            ->where(function ($duringQuery) use ($startDateY, $endDateY) {
                if (!empty($startDateY) && !empty($endDateY)) {
                    $duringQuery->whereBetween('voucher_date', [$startDateY, $endDateY]);
                }
            })
            ->orderBy('voucher_date', 'ASC')
            ->get();

        $duringYearVoucher = $duringQuery->pluck('id')->toarray();

        if (count($duringYearVoucher) > 0) { ## During Year
            //
            $duringYearDebitData = DB::table('acc_voucher_details')
                ->whereIn('voucher_id', $duringYearVoucher)
                ->groupBy('debit_acc')
                ->selectRaw('SUM(IFNULL(amount, 0)) as debit_amount, debit_acc as ledger_id')
                ->pluck('debit_amount', 'ledger_id')
                ->toArray();

            $duringYearCreditData = DB::table('acc_voucher_details')
                ->whereIn('voucher_id', $duringYearVoucher)
                ->groupBy('credit_acc')
                ->selectRaw('SUM(IFNULL(amount, 0)) as credit_amount, credit_acc as ledger_id')
                ->pluck('credit_amount', 'ledger_id')
                ->toArray();
        }
        ## End During period query

        $income  = 0;
        $expense = 0;

        foreach ($ledgerChilds as $row) {

            $debit_amount  = 0;
            $credit_amount = 0;

            if ($row->acc_type_id == 12 || $row->acc_type_id == 13) {
                $debit_amount += (isset($obDebitData[$row->id])) ? $obDebitData[$row->id] : 0;
                $credit_amount += (isset($obCreditData[$row->id])) ? $obCreditData[$row->id] : 0;

                $debit_amount += (isset($duringYearDebitData[$row->id])) ? $duringYearDebitData[$row->id] : 0;
                $credit_amount += (isset($duringYearCreditData[$row->id])) ? $duringYearCreditData[$row->id] : 0;
            }

            if ($row->acc_type_id == 12) {
                $income += $credit_amount - $debit_amount;
            } elseif ($row->acc_type_id == 13) {
                $expense += $debit_amount - $credit_amount;
            }
        }

        $income_statement = $income - $expense;
        return $income_statement;
    }

    public static function funcIncomeStatememnt_bac(
        $startDate,
        $endDate,
        $ledgerChilds = [],
        $branchId = null,
        $projectId = null,
        $projectTypeId = null
    ) {
        $debit   = 0;
        $credit  = 0;
        $income  = 0;
        $expense = 0;

        foreach ($ledgerChilds as $row) {

            if ($row->acc_type_id == 12 || $row->acc_type_id == 13) {

                $incomeStatement = DB::table('acc_voucher as av')
                    ->where([['av.is_delete', 0], ['av.is_active', 1]])
                    ->whereIn('av.voucher_status', [1, 2])
                    ->where(function ($incomeStatement) use ($startDate, $endDate) {
                        if (!empty($startDate) && !empty($endDate)) {
                            $incomeStatement->whereBetween('av.voucher_date', [$startDate, $endDate]);
                        }
                    })
                    ->where(function ($incomeStatement) use ($branchId) {
                        if (!empty($branchId)) {
                            if ($branchId >= 0) {
                                $incomeStatement->where('av.branch_id', $branchId); // Individual Branch
                            } else if ($branchId == -2) {
                                $incomeStatement->where('av.branch_id', '!=', 1); // Branch without head office
                            }
                        }
                    })
                    ->where(function ($incomeStatement) use ($projectId) {
                        if (!empty($projectId)) {
                            $incomeStatement->where('av.project_id', $projectId);
                        }
                    })
                    ->where(function ($incomeStatement) use ($projectTypeId) {
                        if (!empty($projectTypeId)) {
                            $incomeStatement->where('av.project_type_id', $projectTypeId);
                        }
                    })
                    ->leftjoin('acc_voucher_details as avd', function ($incomeStatement) {
                        $incomeStatement->on('avd.voucher_id', 'av.id');
                    })
                    ->select(
                        DB::raw(
                            '
                         IFNULL(SUM(
                             CASE
                                 WHEN av.voucher_date >= "' . $startDate . '" and av.voucher_date <= "' . $endDate . '"
                                 and avd.debit_acc = "' . $row->id . '" and "' . $row->acc_type_id = 12 . '"
                                 THEN avd.amount
                             END
                         ), 0) as sum_debit_income,
                         IFNULL(SUM(
                             CASE
                                 WHEN av.voucher_date >= "' . $startDate . '" and av.voucher_date <= "' . $endDate . '"
                                 and avd.credit_acc = "' . $row->id . '" and "' . $row->acc_type_id = 12 . '"
                                 THEN avd.amount
                             END
                         ), 0) as sum_credit_income,
                         IFNULL(SUM(
                             CASE
                                 WHEN av.voucher_date >= "' . $startDate . '" and av.voucher_date <= "' . $endDate . '"
                                 and avd.debit_acc = "' . $row->id . '" and "' . $row->acc_type_id = 13 . '"
                                 THEN avd.amount
                             END
                         ), 0) as sum_debit_expense,
                         IFNULL(SUM(
                             CASE
                                 WHEN av.voucher_date >= "' . $startDate . '" and av.voucher_date <= "' . $endDate . '"
                                 and avd.credit_acc = "' . $row->id . '" and "' . $row->acc_type_id = 13 . '"
                                 THEN avd.amount
                             END
                         ), 0) as sum_credit_expense'
                        )
                    )
                    ->orderBy('av.voucher_date', 'ASC')
                    ->get();

                if ($row->acc_type_id == 12) {
                    $debit_income  = $incomeStatement->sum('sum_debit_income');
                    $credit_income = $incomeStatement->sum('sum_credit_income');
                    $income += $credit_income - $debit_income;
                } else if ($row->acc_type_id == 13) {
                    $debit_expense  = $incomeStatement->sum('sum_debit_expense');
                    $credit_expense = $incomeStatement->sum('sum_credit_expense');
                    $expense += $debit_expense - $credit_expense;
                }
            }
        }
        $income_statement = $income - $expense;
        return $income_statement;
    }

    ## ## Calculate Opening Balance For Particular Ledger
    public static function funcOpeningBalance(
        $ledgerId,
        $accStartDate,
        $startDateCY,
        $endDateCY,
        $branchId = null,
        $projectId = null,
        $projectTypeId = null,
        $search_by = null
    ) {

        // if ($ledgerId == 412) {

        $opening_bl     = 0;
        $opening_debit  = 0;
        $opening_credit = 0;

        $current_fiscal_year = Common::systemFiscalYear($endDateCY);

        $startDate = $current_fiscal_year['fy_start_date'];
        if ($search_by == 1) {
            $endDate = $current_fiscal_year['fy_end_date'];
        } else {
            $endDate = $endDateCY;
        }

        ## Check If First Fiscal Year Comparing with Software Start Date
        if ($accStartDate >= $startDate && $accStartDate <= $endDate) {
            $flag_year_end      = 0;
            $pre_fiscal_year_id = 0;
        } else {
            $flag_year_end         = 1;
            $current_fy_start_date = new DateTime($startDate);
            $pre_fy_end_date       = $current_fy_start_date->modify('-1 day');
            $pre_fiscal_year       = Common::systemFiscalYear($pre_fy_end_date->format('Y-m-d'));
            $pre_fiscal_year_id    = $pre_fiscal_year['id'];
        }

        // dd($current_fiscal_year);

        ## Query For Debit Amount (Opening Balance)
        $openingMaster = DB::table('acc_ob_m')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where(function ($openingMaster) use ($flag_year_end, $pre_fiscal_year_id) {
                $openingMaster->where('is_year_end', $flag_year_end);

                if ($flag_year_end == 1) {
                    $openingMaster->where('fiscal_year_id', $pre_fiscal_year_id);
                }
            })
            ->where(function ($openingMaster) use ($branchId, $projectId, $projectTypeId) {
                if (!empty($branchId)) {
                    if ($branchId >= 0) {
                        $openingMaster->where('branch_id', $branchId); // Individual Branch
                    } else if ($branchId == -2) {
                        $openingMaster->where('branch_id', '!=', 1); // Branch without head office
                    }
                }
                if (!empty($projectId)) {
                    $openingMaster->where('project_id', $projectId);
                }
                if (!empty($projectTypeId)) {
                    $openingMaster->where('project_type_id', $projectTypeId);
                }
            })
            ->orderBy('id', 'asc')
            ->pluck('ob_no')
            ->first();

        $openingBLData = array();
        if ($openingMaster) {

            $openingBLData = DB::table('acc_ob_d')
                ->where('ob_no', $openingMaster)
                ->where('ledger_id', $ledgerId)
                ->first();

            $opening_bl     = isset($openingBLData->balance_amount) ? $openingBLData->balance_amount : 0;
            $opening_debit  = isset($openingBLData->debit_amount) ? $openingBLData->debit_amount : 0;
            $opening_credit = isset($openingBLData->credit_amount) ? $openingBLData->credit_amount : 0;
        }

        $data = [
            'opening_bl'     => $opening_bl,
            'opening_debit'  => $opening_debit,
            'opening_credit' => $opening_credit,
        ];

        return $data;

        // return $opening_bl;

        // dd($openingBLData);
        // }

    }

    // -------------------------------------------------------------

    ## This function is used to check if tx exists under an employee
    ## before transfer/termination
    public static function checkTransactionForEmployee($employeeId, $action = "terminating")
    {
        $moduleFlag = false;
        $errMessage = false;

        if (Common::checkActivatedModule('acc')) {
            $moduleFlag = true;
        }
        return false;
    }

    public static function prepareSubLedger($parentId = null, $ParentMenuArr = [], $MasterParent)
    {
        $SubMenuSet  = array();
        $SubMenuData = $ParentMenuArr[$parentId];
        foreach ($SubMenuData as $SubMenu) {
            $TempArray = array();

            if ($SubMenu->is_group_head == 0) {
                $TempArray                           = $SubMenu->id;
                self::$PublicLedger[$MasterParent][] = $SubMenu->id;
            }

            if (isset($ParentMenuArr[$SubMenu->id])) {
                self::prepareSubLedger($SubMenu->id, $ParentMenuArr, $MasterParent);
            }

            $SubMenuSet[] = $TempArray;
        }
        return $SubMenuSet;
    }

    public static function prepareLedgerWithParent($ledgerHeads)
    {
        ## Ledger Data Group BY parent Ledger Wise
        $ledgerHeadsInGR = $ledgerHeads->groupBy('parent_id');
        $ledgerHeadsInGR = $ledgerHeadsInGR->toarray();

        ## Array Data make for parent wise transection head load set in Public Array
        foreach ($ledgerHeadsInGR as $key => $ParentLedgerData) {
            foreach ($ParentLedgerData as $RootLedger) {

                if ($RootLedger->is_group_head == 0) {
                    ## Public Variable
                    self::$PublicLedger[$RootLedger->parent_id][] = $RootLedger->id;
                }

                if (isset($ledgerHeadsInGR[$RootLedger->id])) {
                    self::prepareSubLedger($RootLedger->id, $ledgerHeadsInGR, $RootLedger->parent_id);
                }
            }
        }
        return self::$PublicLedger;
    }

    public static function backup_searchWiseDateCalculation($parameter = [], $searchBy, $fiscalYear, $endDateCY, $startDateDR, $endDateDR, $cmlDataFetch = true)
    {
        $return_data = array();

        ## check branch have year end or day end
        ## this calculation use for all accounting report
        $activeBranchArr  = array();
        $ignorBranchArr   = array();
        $incompleteReason = "";

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : Common::getBranchId();

        ## Branch Id fetch
        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);

        if ($branchId == -1 || $branchId == -2) {
            $branchId = Common::getBranchId();
        }

        $brOpeningDate   = new DateTime(Common::getBranchSoftwareStartDate($branchId, 'acc'));
        $loginSystemDate = new DateTime(Common::systemCurrentDate($branchId, 'acc'));

        $current_fiscal_year = Common::systemFiscalYear('', $companyId, $branchId, 'acc');
        $searching_fiscal_id = 0;

        ## Cumulative Date Before Selected start date
        $startDateCML = $brOpeningDate;
        $endDateCML   = $loginSystemDate;

        ## this is for during this month
        $startDateThisMonth = $endDateThisMonth = null;
        $startDateDuring    = $endDateDuring    = null;

        ## this is for during retail month
        $sDateRetailAhead = $sDateFull = $sDateRetailLater = null;
        $eDateRetailAhead = $eDateFull = $eDateRetailLater = null;

        ## this variable for cumulative date
        $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = null;
        $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = null;

        ## This calculation for searching parameter wise start Date & End date give
        ## this calculation use for all accounting report
        ## this calculation return startDate & endDate for this month & during time & cumulative time
        ## Cumulative Date range dhora hoyecche selected start date er agg porjonto
        $obFetchFlag         = false;
        $currentYearFlag     = false;
        $duringDataFetchFrom = "month&&voucher";

        if ($searchBy == 1) {
            ## Fiscal Year
            if ($fiscalYear == null) {
                $notification = array(
                    'message' => 'Please select fiscal year.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $fiscal_year_data = DB::table('gnl_fiscal_year')
                ->where([['is_delete', 0], ['is_active', 1], ['id', $fiscalYear]])
                ->first();

            if (empty($fiscal_year_data)) {
                $notification = array(
                    'message' => 'Fiscal Year not found.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $tempStartFY   = new DateTime($fiscal_year_data->fy_start_date);
            $tempEndDateFY = new DateTime($fiscal_year_data->fy_end_date);

            $startDateFY = clone $tempStartFY;
            $endDateFY   = clone $tempEndDateFY;

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateFY) && ($brOpeningDate <= $endDateFY)) {
                $startDateFY = $brOpeningDate;
                $obFetchFlag = true;
            }

            if (($loginSystemDate >= $startDateFY) && ($loginSystemDate <= $endDateFY)) {
                $endDateFY = $loginSystemDate;
            }

            ## date select for during data
            $startDateDuring = $startDateFY;
            $endDateDuring   = $endDateFY;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateFY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateFY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = $fiscal_year_data->id;

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateFY;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }

            if ($searching_fiscal_id == $current_fiscal_year['id']) {
                $currentYearFlag == true;
            } else {
                $duringDataFetchFrom = "year";
                $obFetchFlag         = false;
            }
        } elseif ($searchBy == 2) {
            ## Current Year
            if ($endDateCY == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $currentYearFlag == true;

            ## Get Current Fiscal Year Start Date
            $startDateCY = new DateTime($current_fiscal_year['fy_start_date']);
            $endDateCY   = new DateTime($endDateCY);

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateCY) && ($brOpeningDate <= $endDateCY)) {
                $startDateCY = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for during data
            $startDateDuring = $startDateCY;
            $endDateDuring   = $endDateCY;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateCY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateCY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = $current_fiscal_year['id'];

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateCY;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }
        } elseif ($searchBy == 3 && $startDateDR != false && $endDateDR != false) {
            ## Date Range
            if ($startDateDR == null && $endDateDR == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $startDateDR = new DateTime($startDateDR);
            $endDateDR   = new DateTime($endDateDR);

            if (($brOpeningDate >= $startDateDR) && ($brOpeningDate <= $endDateDR)) {
                $startDateDR = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for during data
            $startDateDuring = $startDateDR;
            $endDateDuring   = $endDateDR;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateDR->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateDR;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = 0;

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateDR;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }
        }

        if ($duringDataFetchFrom === "year") {
            ## check for only fiscal year
            ## check year end
            $activeBranchArr = DB::table('acc_year_end')
                ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId], ['fiscal_year_id', $searching_fiscal_id]])
                ->whereIn('branch_id', $selBranchArr)
                ->groupBy('branch_id')
                ->pluck('branch_id')
                ->toArray();

            $ignorBranchArr = array_values(array_diff($selBranchArr, $activeBranchArr));

            if (count($ignorBranchArr) > 0) {
                $incompleteReason = "year_not_found";
            }

            $sDateRetailAhead = $sDateFull = $sDateRetailLater = $startDateDuring;
            $eDateRetailAhead = $eDateFull = $eDateRetailLater = $endDateDuring;
        } else {
            ## check for Current Year & Date Range
            ## check month end
            $duringWorkingArr = HRS::systemWorkingDay("branch", [
                'startDate' => $startDateDuring->format('Y-m-d'),
                'endDate'   => $endDateDuring->format('Y-m-d'),
                'companyId' => $companyId,
                'branchId'  => $selBranchArr,
            ]);

            ## calculation for retail month
            ## login branch dhore calculation kora hocche;
            $durationMonthCount = (isset($duringWorkingArr[$branchId])) ? count($duringWorkingArr[$branchId]['working_month']) : 0;

            if ($durationMonthCount >= 3) {
                ## ai calculation milbe jodi date difference 2 month er besi hoy
                $tempRetailAhead = clone $startDateDuring;
                $tempRetailLater = clone $endDateDuring;

                $tempFullStart = clone $startDateDuring;
                $tempFullEnd   = clone $endDateDuring;

                ## During Date Ahead retail month
                $sDateRetailAhead = $startDateDuring;
                $eDateRetailAhead = $tempRetailAhead->modify('last day of this month');

                if ($eDateRetailAhead > $endDateDuring) {
                    $eDateRetailAhead = $endDateDuring;
                }

                ## During Date Full month
                $sDateFull = $tempFullStart->modify('first day of next month');
                $eDateFull = $tempFullEnd->modify('last day of previous month');

                ## During Date Later retail month
                $sDateRetailLater = $tempRetailLater->modify('first day of this month');
                $eDateRetailLater = $endDateDuring;

                if ($sDateRetailLater < $startDateDuring) {
                    $sDateRetailLater = $startDateDuring;
                }
                ## End Retail month date calculation

                ## check month end have or not
                $checkMonthQuery = DB::table('acc_month_end')
                    ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId]])
                    ->whereBetween('month_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                    ->whereIn('branch_id', $selBranchArr)
                    ->groupBy('branch_id')
                    ->selectRaw('count(id) as count_id, branch_id')
                    ->pluck('count_id', 'branch_id')
                    ->toArray();

                foreach ($selBranchArr as $singleB) {
                    $workingMonthCount = (isset($duringWorkingArr[$singleB]) && isset($duringWorkingArr[$singleB]['working_month']))
                        ? count($duringWorkingArr[$singleB]['working_month'])
                        : 0;

                    $monthEndCount = (isset($checkMonthQuery[$singleB])) ? ($checkMonthQuery[$singleB]) : 0;

                    ## 2 minus kora hocche karon retail month bad diye check dewa hocche
                    if (($workingMonthCount > 0) && (($workingMonthCount - 2) <= $monthEndCount)) {
                        array_push($activeBranchArr, $singleB);
                    } else {
                        array_push($ignorBranchArr, $singleB);
                    }
                }

                if (count($ignorBranchArr) > 0) {
                    $incompleteReason = "month_not_found";
                }
            } else {
                $duringDataFetchFrom = "voucher";

                $sDateRetailAhead = $sDateFull = $sDateRetailLater = $startDateDuring;
                $eDateRetailAhead = $eDateFull = $eDateRetailLater = $endDateDuring;

                $activeBranchArr = $selBranchArr;
            }
        }
        ## end for check month & year end data ache kina branch er.

        ## start Cumulative Date Calculation
        $cmlOBFetchFlag   = false;
        $cmlDataFetchFrom = "month&&voucher";
        // $cmlDataFetch     = true;

        // $pre_fiscal_year_data = array();

        $pre_fiscal_year_data = DB::table('gnl_fiscal_year')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', '<', $searching_fiscal_id)
            ->orderBy('id', 'DESC')
            ->first();

        ## cumulative & during date gulo same hole cumulative er data tanbe na
        if ((($startDateDuring == $startDateCML) && ($endDateDuring == $endDateCML)) || ($startDateDuring == $startDateCML)) {
            $cmlDataFetch   = false;
            $cmlOBFetchFlag = true;
            /**
             * cmlOBFetchFlag true kora holo karon debit credit calculation er somoy ai flag dhore ob data tana hobe
             */
        }

        if ($cmlDataFetch == true) {

            // $pre_fiscal_year_data = DB::table('gnl_fiscal_year')
            //     ->where([['is_delete', 0], ['is_active', 1]])
            //     ->where('id', '<', $searching_fiscal_id)
            //     ->orderBy('id', 'DESC')
            //     ->first();

            if ($pre_fiscal_year_data) {
                $cmlOBFetchFlag   = false;
                $cmlDataFetchFrom = "year";

                $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = $startDateCML;
                $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = $endDateCML;
            } else {
                $cmlOBFetchFlag = true;

                $cmlWorkingArr = HRS::systemWorkingDay("branch", [
                    'startDate' => $startDateCML->format('Y-m-d'),
                    'endDate'   => $endDateCML->format('Y-m-d'),
                    'companyId' => $companyId,
                    'branchId'  => $activeBranchArr,
                ]);

                ## calculation for retail month
                ## login branch dhore calculation kora hocche;
                $durationCumMonthCount = (isset($cmlWorkingArr[$branchId])) ? count($cmlWorkingArr[$branchId]['working_month']) : 0;

                if ($durationCumMonthCount >= 3) {
                    ## ai calculation milbe jodi date difference 2 month er besi hoy

                    $cmlTempRetailLater = clone $endDateCML;
                    $cmlTempFullEnd     = clone $endDateCML;

                    ## Branch Open date theke jehetu tai month end hobei, retail month hobe na, holeo month end er data asbe
                    $cmlSDateRetailAhead = $cmlSDateFull = $startDateCML;
                    $cmlEDateRetailAhead = $cmlEDateFull = $cmlTempFullEnd->modify('last day of previous month');

                    ## During Date Later retail month
                    $cmlSDateRetailLater = $cmlTempRetailLater->modify('first day of this month');
                    $cmlEDateRetailLater = $endDateCML;

                    if ($cmlSDateRetailLater < $startDateCML) {
                        $cmlSDateRetailLater = $startDateCML;
                    }
                    ## End Retail month date calculation

                } else {
                    $cmlDataFetchFrom = "voucher";

                    $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = $startDateCML;
                    $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = $endDateCML;
                }
            }
        }
        ## end Cumulative Date Calculation

        $return_data = [
            'brOpeningDate'        => $brOpeningDate,
            'loginSystemDate'      => $loginSystemDate,
            'current_fiscal_year'  => $current_fiscal_year,
            'searching_fiscal_id'  => $searching_fiscal_id,

            'startDateCML'         => $startDateCML,
            'endDateCML'           => $endDateCML,

            'startDateThisMonth'   => $startDateThisMonth,
            'endDateThisMonth'     => $endDateThisMonth,

            'startDateDuring'      => $startDateDuring,
            'endDateDuring'        => $endDateDuring,

            'sDateRetailAhead'     => $sDateRetailAhead,
            'sDateFull'            => $sDateFull,
            'sDateRetailLater'     => $sDateRetailLater,

            'eDateRetailAhead'     => $eDateRetailAhead,
            'eDateFull'            => $eDateFull,
            'eDateRetailLater'     => $eDateRetailLater,

            'cmlSDateRetailAhead'  => $cmlSDateRetailAhead,
            'cmlSDateFull'         => $cmlSDateFull,
            'cmlSDateRetailLater'  => $cmlSDateRetailLater,

            'cmlEDateRetailAhead'  => $cmlEDateRetailAhead,
            'cmlEDateFull'         => $cmlEDateFull,
            'cmlEDateRetailLater'  => $cmlEDateRetailLater,

            'obFetchFlag'          => $obFetchFlag,
            'currentYearFlag'      => $currentYearFlag,
            'duringDataFetchFrom'  => $duringDataFetchFrom,

            'cmlOBFetchFlag'       => $cmlOBFetchFlag,
            'cmlDataFetchFrom'     => $cmlDataFetchFrom,
            'cmlDataFetch'         => $cmlDataFetch,

            'pre_fiscal_year_data' => $pre_fiscal_year_data,

            'activeBranchArr'      => $activeBranchArr,

            'ignorBranchArr'       => $ignorBranchArr,

            'incompleteReason'     => $incompleteReason,
        ];

        return $return_data;
    }

    public static function searchWiseDateCalculation($parameter = [], $searchBy, $fiscalYear, $endDateCY, $startDateDR, $endDateDR, $cmlDataFetch = true)
    {
        $return_data = array();

        ## check branch have year end or day end
        ## this calculation use for all accounting report
        $activeBranchArr  = array();
        $ignorBranchArr   = array();
        $incompleteReason = "";

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : Common::getBranchId();

        ## Branch Id fetch
        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);

        if ($branchId == -1 || $branchId == -2) {
            $branchId = Common::getBranchId();
        }

        $brOpeningDate   = new DateTime(Common::getBranchSoftwareStartDate($branchId, 'acc'));
        $loginSystemDate = new DateTime(Common::systemCurrentDate($branchId, 'acc'));

        $current_fiscal_year = Common::systemFiscalYear('', $companyId, $branchId, 'acc');
        $searching_fiscal_id = 0;

        ## Cumulative Date Before Selected start date
        $startDateCML = $brOpeningDate;
        $endDateCML   = $loginSystemDate;

        ## this is for during this month
        $startDateThisMonth = $endDateThisMonth = null;
        $startDateDuring    = $endDateDuring    = null;

        ## this is for during retail month
        $sDateRetailAhead = $sDateFull = $sDateRetailLater = null;
        $eDateRetailAhead = $eDateFull = $eDateRetailLater = null;

        ## this variable for cumulative date
        $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = null;
        $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = null;

        ## This calculation for searching parameter wise start Date & End date give
        ## this calculation use for all accounting report
        ## this calculation return startDate & endDate for this month & during time & cumulative time
        ## Cumulative Date range dhora hoyecche selected start date er agg porjonto
        $obFetchFlag         = false;
        $currentYearFlag     = false;
        $duringDataFetchFrom = "month&&voucher";

        if ($searchBy == 1) {
            ## Fiscal Year
            if ($fiscalYear == null) {
                $notification = array(
                    'message' => 'Please select fiscal year.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $fiscal_year_data = DB::table('gnl_fiscal_year')
                ->where([['is_delete', 0], ['is_active', 1], ['id', $fiscalYear]])
                ->first();

            if (empty($fiscal_year_data)) {
                $notification = array(
                    'message' => 'Fiscal Year not found.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $tempStartFY   = new DateTime($fiscal_year_data->fy_start_date);
            $tempEndDateFY = new DateTime($fiscal_year_data->fy_end_date);

            $startDateFY = clone $tempStartFY;
            $endDateFY   = clone $tempEndDateFY;

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateFY) && ($brOpeningDate <= $endDateFY)) {
                $startDateFY = $brOpeningDate;
                $obFetchFlag = true;
            }

            if (($loginSystemDate >= $startDateFY) && ($loginSystemDate <= $endDateFY)) {
                $endDateFY = $loginSystemDate;
            }

            ## date select for during data
            $startDateDuring = $startDateFY;
            $endDateDuring   = $endDateFY;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateFY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateFY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = $fiscal_year_data->id;

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateFY;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }

            if ($searching_fiscal_id == $current_fiscal_year['id']) {
                $currentYearFlag == true;
                $duringDataFetchFrom = "month&&voucher";
            } else {
                $duringDataFetchFrom = "year";
                $obFetchFlag         = false;
            }
        } elseif ($searchBy == 2) {
            ## Current Year
            if ($endDateCY == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $currentYearFlag == true;

            ## Get Current Fiscal Year Start Date
            $startDateCY = new DateTime($current_fiscal_year['fy_start_date']);
            $endDateCY   = new DateTime($endDateCY);

            ## Fiscal Year, branch open, system current date wise start & end date fixed
            if (($brOpeningDate >= $startDateCY) && ($brOpeningDate <= $endDateCY)) {
                $startDateCY = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for during data
            $startDateDuring = $startDateCY;
            $endDateDuring   = $endDateCY;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateCY->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateCY;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = $current_fiscal_year['id'];

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateCY;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }
        } elseif ($searchBy == 3 && $startDateDR != false && $endDateDR != false) {
            ## Date Range
            if ($startDateDR == null && $endDateDR == null) {
                $notification = array(
                    'message' => 'Please select date.',
                    'status'  => 'error',
                );
                // return redirect()->back()->with($notification);
                return $notification;
            }

            $startDateDR = new DateTime($startDateDR);
            $endDateDR   = new DateTime($endDateDR);

            if (($brOpeningDate >= $startDateDR) && ($brOpeningDate <= $endDateDR)) {
                $startDateDR = $brOpeningDate;
                $obFetchFlag = true;
            }

            ## date select for during data
            $startDateDuring = $startDateDR;
            $endDateDuring   = $endDateDR;

            ## date select for during this month data
            $startDateThisMonth = new DateTime($endDateDR->format('Y-m-') . "01");
            $endDateThisMonth   = $endDateDR;

            if (($brOpeningDate >= $startDateThisMonth) && ($brOpeningDate <= $endDateThisMonth)) {
                $startDateThisMonth = $brOpeningDate;
            }

            ## date select for cumulative data
            $searching_fiscal_id = 0;

            $startDateCML = $startDateCML;
            $tempStartY   = clone $startDateDR;
            $endDateCML   = $tempStartY->modify('-1 day');

            if ($endDateCML < $startDateCML) {
                $endDateCML = $startDateCML;
            }

            $fiscal_year = Common::systemFiscalYear($startDateDuring->format('Y-m-d'), $companyId, $branchId, 'acc');

            if ($startDateDR->format('Y-m-d') >= $current_fiscal_year['fy_start_date']) {
                $searching_fiscal_id = $current_fiscal_year['id'];
            }
            ###### Only For ledger prev = during
            else if (
                isset($fiscal_year) && $startDateDR->format('Y-m-d') == $brOpeningDate->format('Y-m-d') &&
                $endDateDR->format('Y-m-d') == $fiscal_year['fy_end_date']
            ) {
                $searching_fiscal_id = $fiscal_year['id'];
                $duringDataFetchFrom = "year";
            } else if (isset($fiscal_year) && ($startDateDR->format('Y-m-d') == $brOpeningDate->format('Y-m-d')
                && $endDateDuring->format('Y-m-d') > $fiscal_year['fy_end_date'])) {
                $searching_fiscal_id = $fiscal_year['id'];
            }
        }

        if ($duringDataFetchFrom === "year") {
            ## check for only fiscal year
            ## check year end
            $activeBranchArr = DB::table('acc_year_end')
                ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId], ['fiscal_year_id', $searching_fiscal_id]])
                ->whereIn('branch_id', $selBranchArr)
                ->groupBy('branch_id')
                ->pluck('branch_id')
                ->toArray();

            $ignorBranchArr = array_values(array_diff($selBranchArr, $activeBranchArr));

            if (count($ignorBranchArr) > 0) {
                $incompleteReason = "year_not_found";
            }

            $sDateRetailAhead = $sDateFull = $sDateRetailLater = $startDateDuring;
            $eDateRetailAhead = $eDateFull = $eDateRetailLater = $endDateDuring;
        } else {
            ## check for Current Year & Date Range
            ## check month end
            $tmpStartDate = $startDateDuring->format('Y-m-d');
            if ($searching_fiscal_id > 0) {

                $searching_fiscal_year = Common::systemFiscalYear($endDateDuring->format('Y-m-d'), $companyId, $branchId, 'acc');
                if (
                    isset($searching_fiscal_year['fy_start_date']) &&
                    $searching_fiscal_year['fy_start_date'] > $tmpStartDate
                ) {
                    $tmpStartDate = $searching_fiscal_year['fy_start_date'];
                }
            }
            $tmpEndDate = ($loginSystemDate->format('Y-m-d') < $endDateDuring->format('Y-m-d')) ? $loginSystemDate : $endDateDuring;

            $duringWorkingArr = HRS::systemWorkingDay("branch", [
                'startDate' => $tmpStartDate,
                'endDate'   => $tmpEndDate->format('Y-m-d'),
                'companyId' => $companyId,
                'branchId'  => $selBranchArr,
            ]);
            ## calculation for retail month
            ## login branch dhore calculation kora hocche;
            $durationMonthCount = (isset($duringWorkingArr[$branchId])) ? count($duringWorkingArr[$branchId]['working_month']) : 0;

            if ($durationMonthCount >= 3) {
                ## ai calculation milbe jodi date difference 2 month er besi hoy
                $tempRetailAhead = clone $startDateDuring;
                $tempRetailLater = clone $endDateDuring;

                $tempFullStart = clone $startDateDuring;
                $tempFullEnd   = clone $endDateDuring;

                ## During Date Ahead retail month
                $sDateRetailAhead = $startDateDuring;
                $eDateRetailAhead = $tempRetailAhead->modify('last day of this month');

                if ($eDateRetailAhead > $endDateDuring) {
                    $eDateRetailAhead = $endDateDuring;
                }

                ## During Date Full month
                $sDateFull = $tempFullStart->modify('first day of next month');
                $eDateFull = $tempFullEnd->modify('last day of previous month');

                ## During Date Later retail month
                $sDateRetailLater = $tempRetailLater->modify('first day of this month');
                $eDateRetailLater = $endDateDuring;

                if ($sDateRetailLater < $startDateDuring) {
                    $sDateRetailLater = $startDateDuring;
                }
                ## End Retail month date calculation

                if (isset($fiscal_year) && ($startDateDR->format('Y-m-d') == $brOpeningDate->format('Y-m-d')
                    && $endDateDuring->format('Y-m-d') > $fiscal_year['fy_end_date'])) {
                    $duringDataFetchFrom = "year&&month&&voucher";
                }

                ## check month end have or not
                $checkMonthQuery = DB::table('acc_month_end')
                    ->where([['is_active', 0], ['is_delete', 0], ['company_id', $companyId]])
                    ->whereBetween('month_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                    ->whereIn('branch_id', $selBranchArr)
                    ->groupBy('branch_id')
                    ->selectRaw('count(id) as count_id, branch_id')
                    ->pluck('count_id', 'branch_id')
                    ->toArray();

                foreach ($selBranchArr as $singleB) {
                    $workingMonthCount = (isset($duringWorkingArr[$singleB]) && isset($duringWorkingArr[$singleB]['working_month']))
                        ? count($duringWorkingArr[$singleB]['working_month'])
                        : 0;

                    $monthEndCount = (isset($checkMonthQuery[$singleB])) ? ($checkMonthQuery[$singleB]) : 0;

                    ## 2 minus kora hocche karon retail month bad diye check dewa hocche
                    if (($workingMonthCount > 0) && (($workingMonthCount - 2) <= $monthEndCount)) {
                        array_push($activeBranchArr, $singleB);
                    } else {
                        array_push($ignorBranchArr, $singleB);
                    }
                }

                if (count($ignorBranchArr) > 0) {
                    $incompleteReason = "month_not_found";
                }
            } else {
                $duringDataFetchFrom = "voucher";

                $sDateRetailAhead = $sDateFull = $sDateRetailLater = $startDateDuring;
                $eDateRetailAhead = $eDateFull = $eDateRetailLater = $endDateDuring;

                $activeBranchArr = $selBranchArr;

                if (isset($fiscal_year) && ($startDateDR->format('Y-m-d') == $brOpeningDate->format('Y-m-d')
                    && $endDateDuring->format('Y-m-d') > $fiscal_year['fy_end_date'])) {
                    $duringDataFetchFrom = "year&&voucher";
                    $sDateFull           = new DateTime($current_fiscal_year['fy_start_date']);
                }
            }
        }
        ## end for check month & year end data ache kina branch er.

        ## start Cumulative Date Calculation
        $cmlOBFetchFlag   = false;
        $cmlDataFetchFrom = "month&&voucher";
        // $cmlDataFetch     = true;

        // $pre_fiscal_year_data = array();

        $pre_fiscal_year_data = DB::table('gnl_fiscal_year')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', '<', $searching_fiscal_id)
            ->orderBy('id', 'DESC')
            ->first();

        ## cumulative & during date gulo same hole cumulative er data tanbe na
        if ((($startDateDuring == $startDateCML) && ($endDateDuring == $endDateCML)) || ($startDateDuring == $startDateCML)) {
            $cmlDataFetch   = false;
            $cmlOBFetchFlag = true;
            /**
             * cmlOBFetchFlag true kora holo karon debit credit calculation er somoy ai flag dhore ob data tana hobe
             */
        }
        if ($cmlDataFetch == true) {

            if ($pre_fiscal_year_data && ($startDateDuring->format('Y-m-d') ==
                $current_fiscal_year['fy_start_date'])) {
                $cmlOBFetchFlag   = false;
                $cmlDataFetchFrom = "year";

                $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = $startDateCML;
                $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = $endDateCML;
            } else {
                $cmlOBFetchFlag = true;

                if ($pre_fiscal_year_data && ($startDateDuring->format('Y-m-d') >
                    $current_fiscal_year['fy_start_date'])) {
                    $startDateCML   = new DateTime($current_fiscal_year['fy_start_date']);
                    $cmlOBFetchFlag = false;
                }

                $cmlWorkingArr = HRS::systemWorkingDay("branch", [
                    'startDate' => $startDateCML->format('Y-m-d'),
                    'endDate'   => $endDateCML->format('Y-m-d'),
                    'companyId' => $companyId,
                    'branchId'  => $activeBranchArr,
                ]);

                ## calculation for retail month
                ## login branch dhore calculation kora hocche;
                $durationCumMonthCount = (isset($cmlWorkingArr[$branchId])) ? count($cmlWorkingArr[$branchId]['working_month']) : 0;

                if ($durationCumMonthCount >= 3) {
                    ## ai calculation milbe jodi date difference 2 month er besi hoy

                    $cmlTempRetailLater = clone $endDateCML;
                    $cmlTempFullEnd     = clone $endDateCML;

                    ## Branch Open date theke jehetu tai month end hobei, retail month hobe na, holeo month end er data asbe
                    $cmlSDateRetailAhead = $cmlSDateFull = $startDateCML;
                    $cmlEDateRetailAhead = $cmlEDateFull = $cmlTempFullEnd->modify('last day of previous month');

                    ## During Date Later retail month
                    $cmlSDateRetailLater = $cmlTempRetailLater->modify('first day of this month');
                    $cmlEDateRetailLater = $endDateCML;

                    if ($cmlSDateRetailLater < $startDateCML) {
                        $cmlSDateRetailLater = $startDateCML;
                    }
                    ## End Retail month date calculation

                    if ($pre_fiscal_year_data && ($startDateDuring->format('Y-m-d') >
                        $current_fiscal_year['fy_start_date'])) {
                        $cmlDataFetchFrom = "year&&month&&voucher";
                    }
                } else {
                    $cmlDataFetchFrom = "voucher";

                    $cmlSDateRetailAhead = $cmlSDateFull = $cmlSDateRetailLater = $startDateCML;
                    $cmlEDateRetailAhead = $cmlEDateFull = $cmlEDateRetailLater = $endDateCML;

                    if ($pre_fiscal_year_data && ($startDateDuring->format('Y-m-d') >
                        $current_fiscal_year['fy_start_date'])) {
                        $cmlDataFetchFrom = "year&&voucher";
                    }
                }
            }
        }
        ## end Cumulative Date Calculation
        $return_data = [
            'brOpeningDate'        => $brOpeningDate,
            'loginSystemDate'      => $loginSystemDate,
            'current_fiscal_year'  => $current_fiscal_year,
            'searching_fiscal_id'  => $searching_fiscal_id,

            'startDateCML'         => $startDateCML,
            'endDateCML'           => $endDateCML,

            'startDateThisMonth'   => $startDateThisMonth,
            'endDateThisMonth'     => $endDateThisMonth,

            'startDateDuring'      => $startDateDuring,
            'endDateDuring'        => $endDateDuring,

            'sDateRetailAhead'     => $sDateRetailAhead,
            'sDateFull'            => $sDateFull,
            'sDateRetailLater'     => $sDateRetailLater,

            'eDateRetailAhead'     => $eDateRetailAhead,
            'eDateFull'            => $eDateFull,
            'eDateRetailLater'     => $eDateRetailLater,

            'cmlSDateRetailAhead'  => $cmlSDateRetailAhead,
            'cmlSDateFull'         => $cmlSDateFull,
            'cmlSDateRetailLater'  => $cmlSDateRetailLater,

            'cmlEDateRetailAhead'  => $cmlEDateRetailAhead,
            'cmlEDateFull'         => $cmlEDateFull,
            'cmlEDateRetailLater'  => $cmlEDateRetailLater,

            'obFetchFlag'          => $obFetchFlag,
            'currentYearFlag'      => $currentYearFlag,
            'duringDataFetchFrom'  => $duringDataFetchFrom,

            'cmlOBFetchFlag'       => $cmlOBFetchFlag,
            'cmlDataFetchFrom'     => $cmlDataFetchFrom,
            'cmlDataFetch'         => $cmlDataFetch,

            'pre_fiscal_year_data' => $pre_fiscal_year_data,

            'activeBranchArr'      => $activeBranchArr,

            'ignorBranchArr'       => $ignorBranchArr,

            'incompleteReason'     => $incompleteReason,
        ];

        return $return_data;
    }

    public static function obTableData($parameter = [])
    {

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $activeBranchArr      = (isset($parameter['activeBranchArr'])) ? $parameter['activeBranchArr'] : array();
        $transectionLedgerArr = (isset($parameter['transectionLedgerArr'])) ? $parameter['transectionLedgerArr'] : array();

        $obMaster = DB::table('acc_ob_m')
            ->where([['is_active', 1], ['is_delete', 0], ['is_year_end', 0]])
            ->where(function ($obMaster) use ($activeBranchArr) {
                if (count($activeBranchArr) > 0) {
                    $obMaster->whereIn('branch_id', $activeBranchArr);
                }
            })
            ->where(function ($obMaster) use ($companyId, $projectId, $projectTypeId) {
                if (!empty($companyId)) {
                    $obMaster->where('company_id', $companyId);
                }

                if (!empty($projectId)) {
                    $obMaster->where('project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $obMaster->where('project_type_id', $projectTypeId);
                }
            })
            ->orderBy('id', 'ASC')
            ->pluck('ob_no')
            ->toArray();

        $obData = array();

        if (count($obMaster) > 0) {
            $obData = DB::table('acc_ob_d')
                ->whereIn('ob_no', $obMaster)
                ->where(function ($obData) use ($transectionLedgerArr) {
                    if (count($transectionLedgerArr) > 0) {
                        $obData->whereIn('ledger_id', $transectionLedgerArr);
                    }
                })
                // ->whereIn('ledger_id', $transectionLedgerArr)
                ->groupBy('ledger_id')
                ->selectRaw('ledger_id,
                            SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount,
                            SUM(IFNULL(cash_debit, 0)) as s_cash_debit, SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                            SUM(IFNULL(bank_debit, 0)) as s_bank_debit, SUM(IFNULL(bank_credit, 0)) as s_bank_credit,
                            SUM(IFNULL(jv_debit, 0)) as s_jv_debit, SUM(IFNULL(jv_credit, 0)) as s_jv_credit,
                            SUM(IFNULL(ft_debit, 0)) as s_ft_debit, SUM(IFNULL(ft_credit, 0)) as s_ft_credit')

                ->get([
                    'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                    's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                ])
                ->keyBy('ledger_id')
                ->toArray();
        }

        return $obData;
    }

    public static function findParent($pid)
    {
        $pid = $pid;
        if ($pid != 0) {
            $temp = DB::table('acc_account_ledger as acl')
                ->where([['acl.is_delete', 0], ['acl.is_active', 1]])
                ->where('acl.id', $pid)
                ->select('acl.parent_id')
                ->first();

            self::$accountSet[] = $temp->parent_id;
            self::findParent($temp->parent_id);
        } else {
            return self::$accountSet;
        }
    }

    public static function openingBalanceDateRange(
        $startDate,
        $ledgerIds = [],
        $branchIds = [],
        $projectId = null,
        $projectTypeId = null
    ) {

        // dd($startDate->format('Y-m-d'));

        $debitDateRange = DB::table('acc_voucher as av')
            ->where([['av.is_delete', 0], ['av.is_active', 1]])
            ->whereIn('av.voucher_status', [1, 2])
            ->where('av.voucher_date', '<', $startDate->format('Y-m-d'))
            ->where(function ($debitDateRange) use ($branchIds) {
                if (count($branchIds) > 0) {
                    $debitDateRange->whereIn('av.branch_id', $branchIds);
                }
            })
            ->where(function ($debitDateRange) use ($projectId) {
                if (!empty($projectId)) {
                    $debitDateRange->where('av.project_id', $projectId);
                }
            })
            ->where(function ($debitDateRange) use ($projectTypeId) {
                if (!empty($projectTypeId)) {
                    $debitDateRange->where('av.project_type_id', $projectTypeId);
                }
            })
            ->join('acc_voucher_details as avd', function ($debitDateRange) use ($ledgerIds) {
                $debitDateRange->on('avd.voucher_id', 'av.id')
                    ->where(function ($debitDateRange) use ($ledgerIds) {
                        $debitDateRange->whereIn('avd.debit_acc', $ledgerIds);
                    });
            })
            ->groupBy('avd.debit_acc')
            ->select(DB::raw('IFNULL(SUM(avd.amount),0) as debit_amount'))
            ->get();

        $creditDateRange = DB::table('acc_voucher as av')
            ->where([['av.is_delete', 0], ['av.is_active', 1]])
            ->whereIn('av.voucher_status', [1, 2])
            ->where('av.voucher_date', '<', $startDate->format('Y-m-d'))
            ->where(function ($creditDateRange) use ($branchIds) {
                if (count($branchIds) > 0) {
                    $creditDateRange->whereIn('av.branch_id', $branchIds);
                }
            })
            ->where(function ($creditDateRange) use ($projectId) {
                if (!empty($projectId)) {
                    $creditDateRange->where('av.project_id', $projectId);
                }
            })
            ->where(function ($creditDateRange) use ($projectTypeId) {
                if (!empty($projectTypeId)) {
                    $creditDateRange->where('av.project_type_id', $projectTypeId);
                }
            })
            ->join('acc_voucher_details as avd', function ($creditDateRange) use ($ledgerIds) {
                $creditDateRange->on('avd.voucher_id', 'av.id')
                    ->where(function ($creditDateRange) use ($ledgerIds) {
                        $creditDateRange->whereIn('avd.credit_acc', $ledgerIds);
                    });
            })
            ->groupBy('avd.credit_acc')
            ->select(DB::raw('IFNULL(SUM(avd.amount),0) as credit_amount'))
            ->get();

        $ob_date_range = ($debitDateRange->sum('debit_amount') - $creditDateRange->sum('credit_amount'));

        return $ob_date_range;
    }

    public static function fnForCashOrBankReceiptPayment($startDate, $endDate, $transectionLedgerArr = [], $activeBranchArr = [], $projectId = null, $projectTypeId = null, $duringDataFetchFrom, $sDateRetailAhead, $eDateRetailAhead, $sDateRetailLater, $eDateRetailLater, $sDateFull, $eDateFull, $cashOrBank, $searching_fiscal_id, $companyId)
    {

        $receipt_amount = 0;

        $reciept_month = 0;
        $payment_month = 0;
        $receipt_dur   = 0;
        $payment_dur   = 0;

        $receipt_amount = 0;
        $payment_amount = 0;

        ## start During Period Query
        if ($duringDataFetchFrom == "year") {
            $duringYearEndMaster = DB::table('acc_year_end_balance_m')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('fiscal_year_id', $searching_fiscal_id)
                ->whereIn('branch_id', $activeBranchArr)
                ->where(function ($duringYearEndMaster) use ($companyId, $projectId, $projectTypeId) {
                    if (!empty($companyId)) {
                        $duringYearEndMaster->where('company_id', $companyId);
                    }

                    if (!empty($projectId)) {
                        $duringYearEndMaster->where('project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $duringYearEndMaster->where('project_type_id', $projectTypeId);
                    }
                })
                ->orderBy('balance_date', 'ASC')
                ->pluck('eb_no')
                ->toArray();

            $duringYearEndDetails = array();
            if (count($duringYearEndMaster) > 0) {

                $duringYearEndDetails = DB::table('acc_year_end_balance_d')
                    ->whereIn('eb_no', $duringYearEndMaster)
                    ->whereIn('ledger_id', $transectionLedgerArr)
                    ->groupBy('ledger_id')
                    ->selectRaw('ledger_id,
                        SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount,
                        SUM(IFNULL(cash_debit, 0)) as s_cash_debit, SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                        SUM(IFNULL(bank_debit, 0)) as s_bank_debit, SUM(IFNULL(bank_credit, 0)) as s_bank_credit,
                        SUM(IFNULL(jv_debit, 0)) as s_jv_debit, SUM(IFNULL(jv_credit, 0)) as s_jv_credit,
                        SUM(IFNULL(ft_debit, 0)) as s_ft_debit, SUM(IFNULL(ft_credit, 0)) as s_ft_credit')

                    ->get([
                        'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                        's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                    ])
                    ->keyBy('ledger_id')
                    ->toArray();
            }

            $duringPeriodData = $duringYearEndDetails;

            $receipt_amount = collect($duringPeriodData)->sum('s_debit_amount');

            $payment_amount = collect($duringPeriodData)->sum('s_credit_amount');
        } elseif ($duringDataFetchFrom == "voucher") {

            $duringVoucherMaster = DB::table('acc_voucher')
                ->where([['is_delete', 0], ['is_active', 1]])
                ->whereIn('voucher_status', [1, 2])
                ->whereIn('branch_id', $activeBranchArr)
                ->whereBetween('voucher_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                ->where(function ($duringVoucherMaster) use ($projectId, $projectTypeId) {

                    if (!empty($companyId)) {
                        $duringVoucherMaster->where('company_id', $companyId);
                    }

                    if (!empty($projectId)) {
                        $duringVoucherMaster->where('project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $duringVoucherMaster->where('project_type_id', $projectTypeId);
                    }
                })
                ->orderBy('voucher_date', 'ASC')
                ->get();

            $duringPeriodVoucher = $duringVoucherMaster->pluck('id')->toarray();

            if (count($duringPeriodVoucher) > 0) {

                ## During Period
                $duringPeriodReceipt = DB::table('acc_voucher_details')
                    ->whereIn('voucher_id', $duringPeriodVoucher)
                    ->whereIn('debit_acc', $transectionLedgerArr)
                    ->distinct('debit_acc')
                    ->select(DB::raw('IFNULL(SUM(amount),0) as debit_amount'))
                    ->get();

                $duringPeriodPayment = DB::table('acc_voucher_details')
                    ->whereIn('voucher_id', $duringPeriodVoucher)
                    ->whereIn('credit_acc', $transectionLedgerArr)
                    ->distinct('credit_acc')
                    ->select(DB::raw('IFNULL(SUM(amount),0) as credit_amount'))
                    ->get();

                $receipt_amount = $duringPeriodReceipt->sum('debit_amount');

                $payment_amount = $duringPeriodPayment->sum('credit_amount');
            }
        } else {
            ## month&&voucher
            ## query for retail month
            {
                $duringRetailMaster = DB::table('acc_voucher')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('voucher_status', [1, 2])
                    ->whereIn('branch_id', $activeBranchArr)
                    ->where(function ($duringRetailMaster) use ($projectId, $projectTypeId) {

                        // if (!empty($companyId)) {
                        //     $duringRetailMaster->where('company_id', $companyId);
                        // }

                        if (!empty($projectId)) {
                            $duringRetailMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $duringRetailMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->where(function ($duringRetailMaster) use ($sDateRetailAhead, $eDateRetailAhead, $sDateRetailLater, $eDateRetailLater) {
                        $duringRetailMaster->whereBetween('voucher_date', [$sDateRetailAhead->format('Y-m-d'), $eDateRetailAhead->format('Y-m-d')]);
                        $duringRetailMaster->orWhereBetween('voucher_date', [$sDateRetailLater->format('Y-m-d'), $eDateRetailLater->format('Y-m-d')]);
                    })
                    ->orderBy('voucher_date', 'ASC')
                    ->get();

                $duringPeriodRetailVoucher = $duringRetailMaster->pluck('id')->toarray();

                ## during Period Retail Data calculation
                if (count($duringPeriodRetailVoucher) > 0) {
                    ## During Period
                    $duringPeriodRetailDebit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $duringPeriodRetailVoucher)
                        ->whereIn('debit_acc', $transectionLedgerArr)
                        ->distinct('debit_acc')
                        ->select(DB::raw('IFNULL(SUM(amount),0) as debit_amount'))
                        ->get();

                    $duringPeriodRetailCredit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $duringPeriodRetailVoucher)
                        ->whereIn('credit_acc', $transectionLedgerArr)
                        ->distinct('credit_acc')
                        ->select(DB::raw('IFNULL(SUM(amount),0) as credit_amount'))
                        ->get();
                }
            }
            ## end for during period retail data calculation

            ## for month end data calculation
            {
                $duringMonthEndMaster = DB::table('acc_month_end_balance_m')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->whereIn('branch_id', $activeBranchArr)
                    ->whereBetween('balance_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                    ->where(function ($duringMonthEndMaster) use ($projectId, $projectTypeId) {
                        // if (!empty($companyId)) {
                        //     $duringMonthEndMaster->where('company_id', $companyId);
                        // }

                        if (!empty($projectId)) {
                            $duringMonthEndMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $duringMonthEndMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('balance_date', 'ASC')
                    ->pluck('eb_no')
                    ->toArray();

                // $duringMonthEndDetails = array();

                if (count($duringMonthEndMaster) > 0) {

                    $duringMonthEndDetails = DB::table('acc_month_end_balance_d')
                        ->whereIn('eb_no', $duringMonthEndMaster)
                        ->whereIn('ledger_id', $transectionLedgerArr)
                        ->groupBy('ledger_id')
                        ->selectRaw('SUM(IFNULL(cash_debit, 0)) as s_cash_debit,
                            SUM(IFNULL(bank_debit, 0)) as s_bank_debit,
                            SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                            SUM(IFNULL(bank_credit, 0)) as s_bank_credit,

                            SUM(IFNULL(debit_amount, 0)) as debit_amount,
                            SUM(IFNULL(credit_amount, 0)) as credit_amount')
                        ->get();
                }

                if (isset($duringMonthEndDetails)) {

                    $reciept_month = $duringMonthEndDetails->sum('debit_amount');

                    $payment_month = $duringMonthEndDetails->sum('credit_amount');

                    // $reciept_month = ($cashOrBank == 'cash') ?
                    //     $duringMonthEndDetails->sum('s_cash_debit') : $duringMonthEndDetails->sum('s_bank_debit');

                    // $payment_month = ($cashOrBank == 'cash') ?
                    // $duringMonthEndDetails->sum('s_cash_credit') : $duringMonthEndDetails->sum('s_bank_credit');
                }

                if (isset($duringPeriodRetailDebit)) {
                    $receipt_dur = isset($duringPeriodRetailDebit) ? $duringPeriodRetailDebit->sum('debit_amount') : 0;

                    $payment_dur = isset($duringPeriodRetailCredit) ? $duringPeriodRetailCredit->sum('credit_amount') : 0;
                }

                $receipt_amount = $reciept_month + $receipt_dur;

                $payment_amount = $payment_month + $payment_dur;
            }
        }
        ## End During Period Query

        $data = [
            'receipt_amount' => $receipt_amount,
            'payment_amount' => $payment_amount,
        ];

        return $data;
    }

    public static function funcIncomeStatememntByFiscalYear(
        $searching_fiscal_id,
        $branchId = null,
        $companyId = null,
        $projectId = null,
        $projectTypeId = null
    ) {

        $companyId = Common::getCompanyId();

        $return_data = self::searchWiseDateCalculation([
            'companyId'     => $companyId,
            'branchId'      => $branchId,
            'projectId'     => $projectId,
            'projectTypeId' => $projectTypeId,
        ], 1, $searching_fiscal_id, '', '', '', '');

        $brOpeningDate       = $return_data['brOpeningDate'];
        $loginSystemDate     = $return_data['loginSystemDate'];
        $current_fiscal_year = $return_data['current_fiscal_year'];
        $searching_fiscal_id = $return_data['searching_fiscal_id'];

        $startDateCML = $return_data['startDateCML'];
        $endDateCML   = $return_data['endDateCML'];

        $startDateThisMonth = $return_data['startDateThisMonth'];
        $endDateThisMonth   = $return_data['endDateThisMonth'];

        $startDateDuring = $return_data['startDateDuring'];
        $endDateDuring   = $return_data['endDateDuring'];

        $sDateRetailAhead = $return_data['sDateRetailAhead'];
        $sDateFull        = $return_data['sDateFull'];
        $sDateRetailLater = $return_data['sDateRetailLater'];

        $eDateRetailAhead = $return_data['eDateRetailAhead'];
        $eDateFull        = $return_data['eDateFull'];
        $eDateRetailLater = $return_data['eDateRetailLater'];

        $cmlSDateRetailAhead = $return_data['cmlSDateRetailAhead'];
        $cmlSDateFull        = $return_data['cmlSDateFull'];
        $cmlSDateRetailLater = $return_data['cmlSDateRetailLater'];

        $cmlEDateRetailAhead = $return_data['cmlEDateRetailAhead'];
        $cmlEDateFull        = $return_data['cmlEDateFull'];
        $cmlEDateRetailLater = $return_data['cmlEDateRetailLater'];

        $obFetchFlag         = $return_data['obFetchFlag'];
        $currentYearFlag     = $return_data['currentYearFlag'];
        $duringDataFetchFrom = $return_data['duringDataFetchFrom'];

        $cmlOBFetchFlag   = $return_data['cmlOBFetchFlag'];
        $cmlDataFetchFrom = $return_data['cmlDataFetchFrom'];
        $cmlDataFetch     = $return_data['cmlDataFetch'];

        $pre_fiscal_year_data = $return_data['pre_fiscal_year_data'];

        $activeBranchArr = $return_data['activeBranchArr'];

        $ignorBranchArr = $return_data['ignorBranchArr'];

        $incompleteReason = $return_data['incompleteReason'];

        // dd($return_data);

        ## Query For Ledger Head [Only account type Income and Expense]
        $ledgerHeads = self::getLedgerData(['branchId' => $branchId, 'companyId' => $companyId, 'projectId' => $projectId, 'projectTypeId' => $projectTypeId, 'accType' => [12, 13]]);

        ## Ledger Data Split For Transectional Head
        $ledgerChilds = $ledgerHeads->groupBy('is_group_head');
        $ledgerChilds = $ledgerChilds->toarray();
        $ledgerChilds = (isset($ledgerChilds[0])) ? $ledgerChilds[0] : array();

        $transectionLedgerArr = $ledgerHeads->where('is_group_head', 0)->pluck('id')->toArray();

        $obData = self::obTableData([
            'activeBranchArr'      => $activeBranchArr,
            'transectionLedgerArr' => $transectionLedgerArr,
            'companyId'            => $companyId,
            'projectId'            => $projectId,
            'projectTypeId'        => $projectTypeId,
        ]);

        ## for month end data calculation
        {
            $duringMonthEndMaster = DB::table('acc_month_end_balance_m')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->whereIn('branch_id', $activeBranchArr)
                ->whereBetween('balance_date', [$sDateFull->format('Y-m-d'), $eDateFull->format('Y-m-d')])
                ->where(function ($duringMonthEndMaster) use ($companyId, $projectId, $projectTypeId) {
                    if (!empty($companyId)) {
                        $duringMonthEndMaster->where('company_id', $companyId);
                    }

                    if (!empty($projectId)) {
                        $duringMonthEndMaster->where('project_id', $projectId);
                    }

                    // if (!empty($projectTypeId)) {
                    //     $duringMonthEndMaster->where('project_type_id', $projectTypeId);
                    // }
                })
                ->orderBy('balance_date', 'ASC')
                ->pluck('eb_no')
                ->toArray();

            $duringMonthEndDetails = array();

            if (count($duringMonthEndMaster) > 0) {

                $duringMonthEndDetails = DB::table('acc_month_end_balance_d')
                    ->whereIn('eb_no', $duringMonthEndMaster)
                    ->whereIn('ledger_id', $transectionLedgerArr)
                    ->groupBy('ledger_id')
                    ->selectRaw('ledger_id,
                    SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount,
                    SUM(IFNULL(cash_debit, 0)) as s_cash_debit, SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                    SUM(IFNULL(bank_debit, 0)) as s_bank_debit, SUM(IFNULL(bank_credit, 0)) as s_bank_credit,
                    SUM(IFNULL(jv_debit, 0)) as s_jv_debit, SUM(IFNULL(jv_credit, 0)) as s_jv_credit,
                    SUM(IFNULL(ft_debit, 0)) as s_ft_debit, SUM(IFNULL(ft_credit, 0)) as s_ft_credit')

                    ->get([
                        'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                        's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                    ])
                    ->keyBy('ledger_id')
                    ->toArray();
            }

            foreach ($duringMonthEndDetails as $ledger => $row) {

                if (!isset($duringPeriodData[$ledger])) {
                    $duringPeriodData[$ledger] = $row;
                } else {
                    $duringPeriodData[$ledger]->s_debit_amount += $row->s_debit_amount;
                    $duringPeriodData[$ledger]->s_credit_amount += $row->s_credit_amount;
                    $duringPeriodData[$ledger]->s_cash_debit += $row->s_cash_debit;
                    $duringPeriodData[$ledger]->s_cash_credit += $row->s_cash_credit;
                    $duringPeriodData[$ledger]->s_bank_debit += $row->s_bank_debit;
                    $duringPeriodData[$ledger]->s_bank_credit += $row->s_bank_credit;
                    $duringPeriodData[$ledger]->s_jv_debit += $row->s_jv_debit;
                    $duringPeriodData[$ledger]->s_jv_credit += $row->s_jv_credit;
                    $duringPeriodData[$ledger]->s_ft_debit += $row->s_ft_debit;
                    $duringPeriodData[$ledger]->s_ft_credit += $row->s_ft_credit;
                }
            }
        }

        $income  = 0;
        $expense = 0;

        $count = 0;

        foreach ($ledgerChilds as $row) {

            $ledgerId = $row->id;

            $debit_amount  = 0;
            $credit_amount = 0;

            if ($row->acc_type_id == '12' || $row->acc_type_id == '13') {

                $debit_amount += (isset($obData[$row->id])) ? $obData[$row->id]->s_debit_amount : 0;
                $credit_amount += (isset($obData[$row->id])) ? $obData[$row->id]->s_credit_amount : 0;

                $debit_amount += isset($duringPeriodData[$ledgerId]) ? $duringPeriodData[$ledgerId]->s_debit_amount : 0;
                $credit_amount += isset($duringPeriodData[$ledgerId]) ? $duringPeriodData[$ledgerId]->s_credit_amount : 0;
            }
            if ($row->acc_type_id == 12) {
                $income += $credit_amount - $debit_amount;
            } elseif ($row->acc_type_id == 13) {
                $expense += $debit_amount - $credit_amount;
            }
        }

        $income_statement = $income - $expense;
        return $income_statement;
    }

    public static function funcRetainedEarning($searching_fiscal_id, $branchId = null, $companyId = null, $projectId = null, $projectTypeId = null)
    {

        ## Initialization
        $debit_previous   = 0;
        $credit_previous  = 0;
        $balance_previous = 0;

        $ledgerHeads = self::getLedgerData(['branchId' => $branchId, 'companyId' => $companyId, 'projectId' => $projectId, 'projectTypeId' => $projectTypeId]);

        ## Ledger Data Split For Transectional Head
        $ledgerChilds = $ledgerHeads->groupBy('is_group_head');
        $ledgerChilds = $ledgerChilds->toarray();
        $ledgerChilds = (isset($ledgerChilds[0])) ? $ledgerChilds[0] : array();

        $retainedLedgers = $ledgerHeads->where('is_group_head', 0)->where('acc_type_id', 10)->pluck('id')->toArray();

        $previousFiscalYear = DB::table('gnl_fiscal_year')
            ->where([['is_delete', 0], ['is_active', 1]])
            ->where('id', '<', $searching_fiscal_id)
            ->orderBy('id', 'DESC')
            ->first();

        $retainedForPreYearData = array();

        if (!empty($previousFiscalYear)) {
            $preYearEndMaster = DB::table('acc_year_end_balance_m')
                ->where([['is_active', 1], ['is_delete', 0]])
                ->where('fiscal_year_id', $previousFiscalYear->id)
                ->where('branch_id', $branchId)
                ->where(function ($preYearEndMaster) use ($companyId, $projectId, $projectTypeId) {
                    if (!empty($companyId)) {
                        $preYearEndMaster->where('company_id', $companyId);
                    }

                    if (!empty($projectId)) {
                        $preYearEndMaster->where('project_id', $projectId);
                    }

                    if (!empty($projectTypeId)) {
                        $preYearEndMaster->where('project_type_id', $projectTypeId);
                    }
                })
                ->orderBy('balance_date', 'ASC')
                ->pluck('eb_no')
                ->toArray();

            if (count($preYearEndMaster) > 0) {

                $retainedForPreYearData = DB::table('acc_year_end_balance_d')
                    ->whereIn('eb_no', $preYearEndMaster)
                    ->whereIn('ledger_id', $retainedLedgers)
                    ->groupBy('ledger_id')
                    ->selectRaw('ledger_id,
                            SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount,
                            SUM(IFNULL(cash_debit, 0)) as s_cash_debit, SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                            SUM(IFNULL(bank_debit, 0)) as s_bank_debit, SUM(IFNULL(bank_credit, 0)) as s_bank_credit,
                            SUM(IFNULL(jv_debit, 0)) as s_jv_debit, SUM(IFNULL(jv_credit, 0)) as s_jv_credit,
                            SUM(IFNULL(ft_debit, 0)) as s_ft_debit, SUM(IFNULL(ft_credit, 0)) as s_ft_credit')

                    ->get([
                        'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                        's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                    ])
                    ->keyBy('ledger_id')
                    ->toArray();
            }
        }

        foreach ($ledgerChilds as $row) {

            $ledgerId = $row->id;

            ## acc_type_id = 10 is retained earning
            if ($row->acc_type_id == 10) {

                if (isset($retainedForPreYearData[$ledgerId])) {
                    $RpreviousYearD = $retainedForPreYearData[$ledgerId];

                    $debit_previous += (isset($RpreviousYearD->s_debit_amount)) ? $RpreviousYearD->s_debit_amount : 0;
                    $credit_previous += (isset($RpreviousYearD->s_credit_amount)) ? $RpreviousYearD->s_credit_amount : 0;

                    $balance_previous += $debit_previous - $credit_previous;
                }
            }
        }
        return $balance_previous;
    }

    public static function obTableDataBranchWise($parameter = [])
    {

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $activeBranchArr      = (isset($parameter['activeBranchArr'])) ? $parameter['activeBranchArr'] : array();
        $transectionLedgerArr = (isset($parameter['transectionLedgerArr'])) ? $parameter['transectionLedgerArr'] : array();

        $obMaster = DB::table('acc_ob_m')
            ->where([['is_active', 1], ['is_delete', 0], ['is_year_end', 0]])
            ->where(function ($obMaster) use ($activeBranchArr) {
                if (count($activeBranchArr) > 0) {
                    $obMaster->whereIn('branch_id', $activeBranchArr);
                }
            })
            ->where(function ($obMaster) use ($companyId, $projectId, $projectTypeId) {
                if (!empty($companyId)) {
                    $obMaster->where('company_id', $companyId);
                }

                if (!empty($projectId)) {
                    $obMaster->where('project_id', $projectId);
                }

                if (!empty($projectTypeId)) {
                    $obMaster->where('project_type_id', $projectTypeId);
                }
            })
            ->orderBy('id', 'ASC')
            ->pluck('ob_no')
            ->toArray();

        $obData = array();

        if (count($obMaster) > 0) {
            $obData = DB::table('acc_ob_d')
                ->whereIn('ob_no', $obMaster)
                ->where(function ($obData) use ($transectionLedgerArr) {
                    if (count($transectionLedgerArr) > 0) {
                        $obData->whereIn('ledger_id', $transectionLedgerArr);
                    }
                })
                // ->whereIn('ledger_id', $transectionLedgerArr)
                ->groupBy('branch_id')
                ->selectRaw('branch_id,
                            SUM(IFNULL(debit_amount, 0)) as debit_amount, SUM(IFNULL(credit_amount, 0)) as credit_amount')

                ->get(['branch_id', 'debit_amount', 'credit_amount'])
                ->keyBy('branch_id')
                ->toArray();
        }

        return $obData;
    }

    public static function fnForLedgerData($ledgerArr)
    {
        $ledgerData = array();
        if (count($ledgerArr) > 0) {

            if (Common::getDBConnection() == "sqlite") {
                $ledgerData = DB::table('acc_account_ledger')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $ledgerArr)
                    ->selectRaw('(name || " [" || code || "]" ) AS account_head, id')
                    ->orderBy('code', 'ASC')
                    ->pluck('account_head', 'id')
                    ->toArray();
            } else {
                $ledgerData = DB::table('acc_account_ledger')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('id', $ledgerArr)
                    ->selectRaw('CONCAT(name, " [", code, "]") AS account_head, id')
                    ->orderBy('code', 'ASC')
                    ->pluck('account_head', 'id')
                    ->toArray();
            }
        }
        return $ledgerData;
    }

    public static function fnCashBankBalanceFromDaySummary($balanceFor, $parameter = [])
    {
        /**
         * $balanceFor
         * @value1 "branchLastDayBalance"
         * @value2 "branchOpeningDayBalance"
         * @value3 "singleDayBalance"
         * @value4 "multipleDayBalance"
         */

        $resultData = array();

        if (
            $balanceFor == "branchLastDayBalance"
            || $balanceFor == "branchOpeningDayBalance"
            || $balanceFor == "singleDayBalance"
            || $balanceFor == "multipleDayBalance"
        ) {
            true;
        } else {
            // return "reason_not_found";
            return false;
        }

        // $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        // $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        // $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $zoneId   = (isset($parameter['zoneId'])) ? $parameter['zoneId'] : null;
        $areaId   = (isset($parameter['areaId'])) ? $parameter['areaId'] : null;
        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;

        $startDate = $endDate = null;

        $selBranchArr = (isset($parameter['branchArr'])) ? $parameter['branchArr'] : Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId);

        if ($balanceFor == "multipleDayBalance") {
            $startDate = (isset($parameter['startDate'])) ? $parameter['startDate'] : null;
            $endDate   = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;

            if (!empty($startDate) && !empty($endDate)) {

                $startDate = (new DateTime($startDate))->format('Y-m-d');
                $endDate   = (new DateTime($endDate))->format('Y-m-d');
            } else {
                // return "date_range_not_found";
                return false;
            }

            # opening data calculation
            $openingData = DB::table('acc_day_end')
                ->where([['is_delete', 0], ['is_active', 0]])
                ->whereIn('branch_id', $selBranchArr)
                ->where(function ($openingData) use ($startDate) {
                    $openingData->where('branch_date', '<', $startDate);
                })
                ->groupBy('branch_id')
                ->selectRaw('branch_id,
                    SUM(cash_debit) as cash_debit, SUM(cash_credit) as cash_credit,
                    SUM(bank_debit) as bank_debit, SUM(bank_credit) as bank_credit')
                ->orderBy('branch_id', 'ASC')
                ->get()
                ->keyBy('branch_id');

            $tempArrOb = [
                'cash_debit'  => $openingData->sum('cash_debit'),
                'cash_credit' => $openingData->sum('cash_credit'),
                'bank_debit'  => $openingData->sum('bank_debit'),
                'bank_credit' => $openingData->sum('bank_credit'),
            ];

            $resultData['openingBalance']        = $openingData->toArray();
            $resultData['openingBalance']['all'] = (object) $tempArrOb;

            # During period calculation
            $onPeriodData = DB::table('acc_day_end')
                ->where([['is_delete', 0], ['is_active', 0]])
                ->whereIn('branch_id', $selBranchArr)
                ->where(function ($onPeriodData) use ($startDate, $endDate) {
                    $onPeriodData->whereBetween('branch_date', [$startDate, $endDate]);
                })
                ->selectRaw('branch_id, branch_date,
                    cash_debit, cash_credit, bank_debit, bank_credit')
                ->orderBy('branch_id', 'ASC')
                ->orderBy('branch_date', 'ASC')
                ->get();

            $onPeriodDateWise       = $onPeriodData->groupBy('branch_date');
            $resultData['onPeriod'] = $onPeriodDateWise;
        }

        if (
            $balanceFor == "branchLastDayBalance" || $balanceFor == "branchOpeningDayBalance"
            || $balanceFor == "singleDayBalance"
        ) {

            $maxDateBranchWiseArr = array();

            if ($balanceFor == "branchLastDayBalance") {

                $maxDateBranchWiseArr = DB::table('acc_day_end')
                    ->where([['is_delete', 0], ['is_active', 0]])
                    ->whereIn('branch_id', $selBranchArr)
                    ->orderBy('branch_id', 'ASC')
                    ->orderBy('branch_date', 'DESC')
                    ->groupBy('branch_id')
                    ->selectRaw('branch_id, MAX(branch_date) as last_branch_date')
                    ->pluck('last_branch_date', 'branch_id')
                    ->all();

                if (count($maxDateBranchWiseArr) < 1) {
                    return false;
                }
            }

            if ($balanceFor == "singleDayBalance") {
                $endDate = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;

                if (empty($endDate)) {
                    return false;
                } else {
                    $endDate = (new DateTime($endDate))->format('Y-m-d');
                }
            }

            $queryData = DB::table('acc_day_end')
                ->where([['is_delete', 0], ['is_active', 0]])
                ->whereIn('branch_id', $selBranchArr)
                ->where(function ($queryData) use ($balanceFor, $maxDateBranchWiseArr, $endDate) {

                    if ($balanceFor == "branchLastDayBalance") {
                        $queryData->where('branch_id', 0);
                        foreach ($maxDateBranchWiseArr as $branch => $branchDate) {
                            $queryData->orWhere([['branch_id', $branch], ['branch_date', $branchDate]]);
                        }
                    }

                    if ($balanceFor == "singleDayBalance") {
                        $queryData->where('branch_date', '<=', $endDate);
                        // $queryData->where('branch_date', $queryData->max('branch_date'));
                        // dd($queryData->max('branch_date'));
                    }
                })
                ->groupBy('branch_id')
                ->selectRaw('branch_id, branch_date,
                    cash_debit, cash_credit, bank_debit, bank_credit,
                    cum_cash_debit as cumulative_cash_debit, cum_cash_credit as cumulative_cash_credit,
                    cum_bank_debit as cumulative_bank_debit, cum_bank_credit as cumulative_bank_credit')
                // ->orderBy('branch_date', 'DESC')
                // ->orderBy('branch_id', 'ASC')
                ->get()
                ->keyBy('branch_id');

            // dd($selBranchArr, $queryData);

            if (count($queryData->toArray()) < 1) {
                return false;
            }

            $tempArrAll = [
                'cash_debit'             => $queryData->sum('cash_debit'),
                'cash_credit'            => $queryData->sum('cash_credit'),
                'bank_debit'             => $queryData->sum('bank_debit'),
                'bank_credit'            => $queryData->sum('bank_credit'),
                'cumulative_cash_debit'  => $queryData->sum('cumulative_cash_debit'),
                'cumulative_cash_credit' => $queryData->sum('cumulative_cash_credit'),
                'cumulative_bank_debit'  => $queryData->sum('cumulative_bank_debit'),
                'cumulative_bank_credit' => $queryData->sum('cumulative_bank_credit'),
            ];

            $resultData        = $queryData->toArray();
            $resultData['all'] = (object) $tempArrAll;
        }

        return $resultData;
    }

    public static function fnCashBankBalance($balanceFor, $parameter = [])
    {
        /**
         * $balanceFor
         * @value1 "branchLastDayBalance"
         * @value2 "branchOpeningDayBalance"
         * @value3 "singleDayBalance"
         * @value4 "multipleDayBalance"
         */

        $resultData = array();

        if (
            $balanceFor == "branchLastDayBalance"
            || $balanceFor == "branchOpeningDayBalance"
            || $balanceFor == "singleDayBalance"
            || $balanceFor == "multipleDayBalance"
        ) {
            true;
        } else {
            // return "reason_not_found";
            return false;
        }

        $companyId     = (isset($parameter['companyId'])) ? $parameter['companyId'] : Common::getCompanyId();
        $projectId     = (isset($parameter['projectId'])) ? $parameter['projectId'] : Common::getProjectId();
        $projectTypeId = (isset($parameter['projectTypeId'])) ? $parameter['projectTypeId'] : Common::getProjectTypeId();

        $zoneId   = (isset($parameter['zoneId'])) ? $parameter['zoneId'] : null;
        $areaId   = (isset($parameter['areaId'])) ? $parameter['areaId'] : null;
        $branchId = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;

        $opFetchFrom = (isset($parameter['opFetchFrom'])) ? $parameter['opFetchFrom'] : null;
        $bpFetchFrom = (isset($parameter['bpFetchFrom'])) ? $parameter['bpFetchFrom'] : null;

        $searchBy = (isset($parameter['searchBy'])) ? $parameter['searchBy'] : null;
        $fiscalYear = (isset($parameter['fiscalYear'])) ? $parameter['fiscalYear'] : null;

        $startDate = $endDate = null;

        $selBranchArr = (isset($parameter['branchArr'])) ? $parameter['branchArr'] : Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId);
        $flag = false;
        $branchDate = '';

        if ($balanceFor == "branchLastDayBalance") {

            $branchData = DB::table('acc_day_end')
                ->where([['is_delete', 0], ['is_active', 0]])
                ->whereIn('branch_id', $selBranchArr)
                ->orderBy('branch_id', 'ASC')
                ->orderBy('branch_date', 'DESC')
                ->select('id', 'branch_date')
                ->first();

            if (empty($branchData)) {
                return false;
            }

            $endDate = $branchData->branch_date;
            $branchDate = $endDate;
            $flag = true;
        }

        if ($balanceFor == "branchOpeningDayBalance") {

            $openingDate = Common::getBranchSoftwareStartDate($branchId, 'acc');

            if (empty($openingDate)) {
                return false;
            }

            $endDate = $openingDate;
            $flag = true;
        }

        if ($balanceFor == "singleDayBalance") {
            $endDate = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;

            if (empty($endDate)) {
                return false;
            } else {
                $endDate = (new DateTime($endDate))->format('Y-m-d');
                $flag = true;
            }
        }

        if ($balanceFor == "multipleDayBalance") {
            $startDate = (isset($parameter['startDate'])) ? $parameter['startDate'] : null;
            $endDate   = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;

            if (!empty($startDate) && !empty($endDate)) {

                $startDate = (new DateTime($startDate))->format('Y-m-d');
                $endDate   = (new DateTime($endDate))->format('Y-m-d');
            } else {
                return false;
            }
            $flag = true;
        }

        if ($flag) {

            ## This calculation for searching parameter wise start Date & End date give
            if (!isset($opFetchFrom) && !isset($bpFetchFrom)) {
                $return_data = self::getInformationForSummaryData([
                    'companyId'     => $companyId,
                    'branchId'      => $branchId,
                    'projectId'     => $projectId,
                    'projectTypeId' => $projectTypeId,
                ], $searchBy, $startDate, $endDate, $fiscalYear);

                $selBranchArr  = $return_data['activeBranchArr'];

                $opFetchFrom = $return_data['onPeriodDataFetchFrom'];
                $bpFetchFrom = $return_data['beforePeriodDataFetchFrom'];
            }

            ## Get Transaction Ledger Array of Cash/Bank 
            $ledgerHeads = self::getLedgerData([
                'branchId'      => $branchId,
                'companyId'     => $companyId,
                'projectId'     => $projectId,
                'projectTypeId' => $projectTypeId,
                'groupHead'     => 0,
            ]);

            if (count($ledgerHeads) < 1) {
                $notification = array(
                    'message' => 'Ledger Not found.',
                    'status'  => 'error',
                );
                return redirect()->back()->with($notification);
            }

            $cashLedgerIds     = $ledgerHeads->where('acc_type_id', 4)->pluck('id')->toArray();
            $bankLedgerIds     = $ledgerHeads->where('acc_type_id', 5)->pluck('id')->toArray();
            $transactionLedgerArr = $ledgerHeads->whereIn('acc_type_id', [4, 5])->pluck('id')->toArray();

            #Variable
            $amount_this_month_for_cash = $amount_op_for_cash = $amount_cml_for_cash = $amount_bp_for_cash = 0;
            $amount_this_month_for_bank = $amount_op_for_bank = $amount_cml_for_bank = $amount_bp_for_bank = 0;


            if (count($cashLedgerIds) == 0) {
                $cashLedgerIds = [0];
            }

            if (count($bankLedgerIds) == 0) {
                $bankLedgerIds = [0];
            }

            if (count($transactionLedgerArr) == 0) {
                $transactionLedgerArr = [0];
            }


            ## Start Opening Balance Query
            if ($opFetchFrom['obData'] == true || $bpFetchFrom['obData'] == true) {
                $obData = self::obTableData([
                    'activeBranchArr'      => $selBranchArr,
                    'transectionLedgerArr' => $transactionLedgerArr,
                    'companyId'            => $companyId,
                    'projectId'            => $projectId,
                    'projectTypeId'        => $projectTypeId,
                ]);
            }
            ## End Opening Balance Query

            ## Balance Calculation On Period
            if (isset($opFetchFrom['yearEnd'])) {

                $fiscal_year_id = $opFetchFrom['yearEnd']['fiscal_year_id'];

                $opYEMaster = DB::table('acc_year_end_balance_m')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('fiscal_year_id', $fiscal_year_id)
                    ->whereIn('branch_id', $selBranchArr)
                    ->where(function ($opYEMaster) use ($companyId, $projectId, $projectTypeId) {
                        if (!empty($companyId)) {
                            $opYEMaster->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $opYEMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $opYEMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('balance_date', 'ASC')
                    ->pluck('eb_no')
                    ->toArray();

                if (count($opYEMaster) > 0) {

                    $onPeriodData = DB::table('acc_year_end_balance_d')
                        ->whereIn('eb_no', $opYEMaster)
                        ->whereIn('ledger_id', $transactionLedgerArr)
                        ->where('is_surplus', 0)
                        ->groupBy('ledger_id')
                        ->selectRaw('ledger_id,
                            SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount')

                        ->get(['ledger_id', 's_debit_amount', 's_credit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();
                }
            }

            if (isset($opFetchFrom['monthEnd'])) {

                $startDateMD = $opFetchFrom['monthEnd']['startDate'];
                $endDateMD = $opFetchFrom['monthEnd']['endDate'];

                $opMEMaster = DB::table('acc_month_end_balance_m')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->whereIn('branch_id', $selBranchArr)
                    ->whereBetween('balance_date', [$startDateMD->format('Y-m-d'), $endDateMD->format('Y-m-d')])
                    ->where(function ($opMEMaster) use ($companyId, $projectId, $projectTypeId) {
                        if (!empty($companyId)) {
                            $opMEMaster->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $opMEMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $opMEMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('balance_date', 'ASC')
                    ->pluck('eb_no')
                    ->toArray();

                if (count($opMEMaster) > 0) {

                    $opMEDetails = DB::table('acc_month_end_balance_d')
                        ->whereIn('eb_no', $opMEMaster)
                        ->whereIn('ledger_id', $transactionLedgerArr)
                        ->groupBy('ledger_id')
                        ->selectRaw('ledger_id,
                        SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount,
                        SUM(IFNULL(cash_debit, 0)) as s_cash_debit, SUM(IFNULL(cash_credit, 0)) as s_cash_credit,
                        SUM(IFNULL(bank_debit, 0)) as s_bank_debit, SUM(IFNULL(bank_credit, 0)) as s_bank_credit,
                        SUM(IFNULL(jv_debit, 0)) as s_jv_debit, SUM(IFNULL(jv_credit, 0)) as s_jv_credit,
                        SUM(IFNULL(ft_debit, 0)) as s_ft_debit, SUM(IFNULL(ft_credit, 0)) as s_ft_credit')

                        ->get([
                            'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                            's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                        ])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($opMEDetails as $ledger => $row) {

                        if (!isset($onPeriodData[$ledger])) {
                            $onPeriodData[$ledger] = $row;
                        } else {
                            $onPeriodData[$ledger]->s_debit_amount += $row->s_debit_amount;
                            $onPeriodData[$ledger]->s_credit_amount += $row->s_credit_amount;
                            $onPeriodData[$ledger]->s_cash_debit += $row->s_cash_debit;
                            $onPeriodData[$ledger]->s_cash_credit += $row->s_cash_credit;
                            $onPeriodData[$ledger]->s_bank_debit += $row->s_bank_debit;
                            $onPeriodData[$ledger]->s_bank_credit += $row->s_bank_credit;
                            $onPeriodData[$ledger]->s_jv_debit += $row->s_jv_debit;
                            $onPeriodData[$ledger]->s_jv_credit += $row->s_jv_credit;
                            $onPeriodData[$ledger]->s_ft_debit += $row->s_ft_debit;
                            $onPeriodData[$ledger]->s_ft_credit += $row->s_ft_credit;
                        }
                    }
                }
            }

            $startDateRD = $endDateRD = $startDateLD = $endDateLD = null;

            if (isset($opFetchFrom['voucherRA'])) {
                $startDateRD = $opFetchFrom['voucherRA']['startDate'];
                $endDateRD = $opFetchFrom['voucherRA']['endDate'];
            }

            if (isset($opFetchFrom['voucherLA'])) {
                $startDateLD = $opFetchFrom['voucherLA']['startDate'];
                $endDateLD = $opFetchFrom['voucherLA']['endDate'];
            }

            if (isset($opFetchFrom['voucherRA']) || isset($opFetchFrom['voucherLA'])) {

                $opVoucherMQuery = DB::table('acc_voucher')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('voucher_status', [1, 2])
                    ->whereIn('branch_id', $selBranchArr)
                    ->where(function ($opVoucherM) use ($startDateRD, $endDateRD, $startDateLD, $endDateLD) {
                        $opVoucherM->where('voucher_date', null);

                        if (!empty($startDateRD) && !empty($endDateRD)) {
                            $opVoucherM->orWhereBetween('voucher_date', [$startDateRD->format('Y-m-d'), $endDateRD->format('Y-m-d')]);
                        }

                        if (!empty($startDateLD) && !empty($endDateLD)) {
                            $opVoucherM->orWhereBetween('voucher_date', [$startDateLD->format('Y-m-d'), $endDateLD->format('Y-m-d')]);
                        }
                    })
                    ->where(function ($opVoucherM) use ($companyId, $projectId, $projectTypeId) {

                        if (!empty($companyId)) {
                            $opVoucherM->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $opVoucherM->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $opVoucherM->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('voucher_date', 'ASC')
                    ->get();

                $opVoucherM = $opVoucherMQuery->pluck('id')->toarray();

                ## this Period transaction   
                if (count($opVoucherM) > 0) {

                    $onPeriodDebit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $opVoucherM)
                        ->whereIn('debit_acc', $transactionLedgerArr)
                        ->groupBy('debit_acc')
                        ->selectRaw('debit_acc as ledger_id, SUM(IFNULL(amount, 0)) as debit_amount')
                        ->get(['ledger_id', 'debit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($onPeriodDebit as $ledger => $row) {

                        if (!isset($onPeriodData[$ledger])) {
                            $onPeriodData[$ledger]                = new \stdClass();
                            $onPeriodData[$ledger]->ledger_id     = $ledger;
                            $onPeriodData[$ledger]->s_debit_amount = $row->debit_amount;
                            $onPeriodData[$ledger]->s_credit_amount = 0;
                            $onPeriodData[$ledger]->s_cash_debit  = 0;
                            $onPeriodData[$ledger]->s_cash_credit = 0;
                            $onPeriodData[$ledger]->s_bank_debit  = 0;
                            $onPeriodData[$ledger]->s_bank_credit = 0;
                            $onPeriodData[$ledger]->s_jv_debit    = 0;
                            $onPeriodData[$ledger]->s_jv_credit   = 0;
                            $onPeriodData[$ledger]->s_ft_debit    = 0;
                            $onPeriodData[$ledger]->s_ft_credit   = 0;
                        } else {
                            $onPeriodData[$ledger]->s_debit_amount +=  $row->debit_amount;
                        }
                    }

                    $onPeriodCredit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $opVoucherM)
                        ->whereIn('credit_acc', $transactionLedgerArr)
                        ->groupBy('credit_acc')
                        ->selectRaw('credit_acc as ledger_id, SUM(IFNULL(amount, 0)) as credit_amount')
                        ->get(['ledger_id', 'credit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($onPeriodCredit as $ledger => $row) {

                        if (!isset($onPeriodData[$ledger])) {
                            $onPeriodData[$ledger]                = new \stdClass();
                            $onPeriodData[$ledger]->ledger_id     = $ledger;
                            $onPeriodData[$ledger]->s_debit_amount = 0;
                            $onPeriodData[$ledger]->s_credit_amount = $row->credit_amount;
                            $onPeriodData[$ledger]->s_cash_debit  = 0;
                            $onPeriodData[$ledger]->s_cash_credit = 0;
                            $onPeriodData[$ledger]->s_bank_debit  = 0;
                            $onPeriodData[$ledger]->s_bank_credit = 0;
                            $onPeriodData[$ledger]->s_jv_debit    = 0;
                            $onPeriodData[$ledger]->s_jv_credit   = 0;
                            $onPeriodData[$ledger]->s_ft_debit    = 0;
                            $onPeriodData[$ledger]->s_ft_credit   = 0;
                        } else {
                            $onPeriodData[$ledger]->s_credit_amount +=  $row->credit_amount;
                        }
                    }
                }
            }
            #### END Balance Calculation On Period

            ## Balance Calculation Before Period
            if (isset($bpFetchFrom['yearEnd'])) {

                $bp_fiscal_id = $bpFetchFrom['yearEnd']['pre_fiscal_id'];

                $bpYEMaster = DB::table('acc_year_end_balance_m')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->where('fiscal_year_id', $bp_fiscal_id)
                    ->whereIn('branch_id', $selBranchArr)
                    ->where(function ($bpYEMaster) use ($companyId, $projectId, $projectTypeId) {
                        if (!empty($companyId)) {
                            $bpYEMaster->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $bpYEMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $bpYEMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('balance_date', 'ASC')
                    ->pluck('eb_no')
                    ->toArray();

                if (count($bpYEMaster) > 0) {

                    $beforePeriodData = DB::table('acc_year_end_balance_d')
                        ->whereIn('eb_no', $bpYEMaster)
                        ->whereIn('ledger_id', $transactionLedgerArr)
                        ->where('is_surplus', 0)
                        ->groupBy('ledger_id')
                        ->selectRaw('ledger_id,
                            SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount')

                        ->get(['ledger_id', 's_debit_amount', 's_credit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();
                }
            }


            if (isset($bpFetchFrom['monthEnd'])) {

                $startDateMC = $bpFetchFrom['monthEnd']['startDate'];
                $endDateMC = $bpFetchFrom['monthEnd']['endDate'];

                $bpMEMaster = DB::table('acc_month_end_balance_m')
                    ->where([['is_active', 1], ['is_delete', 0]])
                    ->whereIn('branch_id', $selBranchArr)
                    ->whereBetween('balance_date', [$startDateMC->format('Y-m-d'), $endDateMC->format('Y-m-d')])
                    ->where(function ($bpMEMaster) use ($companyId, $projectId, $projectTypeId) {
                        if (!empty($companyId)) {
                            $bpMEMaster->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $bpMEMaster->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $bpMEMaster->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('balance_date', 'ASC')
                    ->pluck('eb_no')
                    ->toArray();

                if (count($bpMEMaster) > 0) {

                    $bpMEDetails = DB::table('acc_month_end_balance_d')
                        ->whereIn('eb_no', $bpMEMaster)
                        ->whereIn('ledger_id', $transactionLedgerArr)
                        ->groupBy('ledger_id')
                        ->selectRaw('ledger_id,
                        SUM(IFNULL(debit_amount, 0)) as s_debit_amount, SUM(IFNULL(credit_amount, 0)) as s_credit_amount')

                        ->get([
                            'ledger_id', 's_debit_amount', 's_credit_amount', 's_cash_debit', 's_cash_credit',
                            's_bank_debit', 's_bank_credit', 's_jv_debit', 's_jv_credit', 's_ft_debit', 's_ft_credit'
                        ])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($bpMEDetails as $ledger => $row) {

                        if (!isset($beforePeriodData[$ledger])) {
                            $beforePeriodData[$ledger] = $row;
                        } else {
                            $beforePeriodData[$ledger]->s_debit_amount += $row->s_debit_amount;
                            $beforePeriodData[$ledger]->s_credit_amount += $row->s_credit_amount;
                        }
                    }
                }
            }

            $startDateRC = $endDateRC = $startDateLC = $endDateLC = null;

            if (isset($bpFetchFrom['voucherRA'])) {
                $startDateRC = $bpFetchFrom['voucherRA']['startDate'];
                $endDateRC = $bpFetchFrom['voucherRA']['endDate'];
            }

            if (isset($bpFetchFrom['voucherLA'])) {
                $startDateLC = $bpFetchFrom['voucherLA']['startDate'];
                $endDateLC = $bpFetchFrom['voucherLA']['endDate'];
            }

            if (isset($bpFetchFrom['voucherRA']) || isset($bpFetchFrom['voucherLA'])) {

                $bpVoucherM = DB::table('acc_voucher')
                    ->where([['is_delete', 0], ['is_active', 1]])
                    ->whereIn('voucher_status', [1, 2])
                    ->whereIn('branch_id', $selBranchArr)
                    ->where(function ($bpVoucherM) use ($startDateRC, $endDateRC, $startDateLC, $endDateLC) {
                        $bpVoucherM->where('voucher_date', null);

                        if (!empty($startDateRC) && !empty($endDateRC)) {
                            $bpVoucherM->orWhereBetween('voucher_date', [$startDateRC->format('Y-m-d'), $endDateRC->format('Y-m-d')]);
                        }

                        if (!empty($startDateLC) && !empty($endDateLC)) {
                            $bpVoucherM->orWhereBetween('voucher_date', [$startDateLC->format('Y-m-d'), $endDateLC->format('Y-m-d')]);
                        }
                    })
                    ->where(function ($bpVoucherM) use ($companyId, $projectId, $projectTypeId) {

                        if (!empty($companyId)) {
                            $bpVoucherM->where('company_id', $companyId);
                        }

                        if (!empty($projectId)) {
                            $bpVoucherM->where('project_id', $projectId);
                        }

                        if (!empty($projectTypeId)) {
                            $bpVoucherM->where('project_type_id', $projectTypeId);
                        }
                    })
                    ->orderBy('voucher_date', 'ASC')
                    ->pluck('id')
                    ->toArray();


                if (count($bpVoucherM) > 0) {

                    ## Cumulative Period
                    $beforePeriodDebit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $bpVoucherM)
                        ->whereIn('debit_acc', $transactionLedgerArr)
                        ->groupBy('debit_acc')
                        ->selectRaw('debit_acc as ledger_id, SUM(IFNULL(amount, 0)) as debit_amount')
                        ->get(['ledger_id', 'debit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($beforePeriodDebit as $ledger => $row) {

                        if (!isset($beforePeriodData[$ledger])) {
                            $beforePeriodData[$ledger]                = new \stdClass();
                            $beforePeriodData[$ledger]->ledger_id     = $ledger;
                            $beforePeriodData[$ledger]->s_debit_amount = $row->debit_amount;
                            $beforePeriodData[$ledger]->s_credit_amount = 0;
                            $beforePeriodData[$ledger]->s_cash_debit  = 0;
                            $beforePeriodData[$ledger]->s_cash_credit = 0;
                            $beforePeriodData[$ledger]->s_bank_debit  = 0;
                            $beforePeriodData[$ledger]->s_bank_credit = 0;
                            $beforePeriodData[$ledger]->s_jv_debit    = 0;
                            $beforePeriodData[$ledger]->s_jv_credit   = 0;
                            $beforePeriodData[$ledger]->s_ft_debit    = 0;
                            $beforePeriodData[$ledger]->s_ft_credit   = 0;
                        } else {
                            $beforePeriodData[$ledger]->s_debit_amount +=  $row->debit_amount;
                        }
                    }

                    $beforePeriodCredit = DB::table('acc_voucher_details')
                        ->whereIn('voucher_id', $bpVoucherM)
                        ->whereIn('credit_acc', $transactionLedgerArr)
                        ->groupBy('credit_acc')
                        ->selectRaw('credit_acc as ledger_id, SUM(IFNULL(amount, 0)) as credit_amount')
                        ->get(['ledger_id', 'credit_amount'])
                        ->keyBy('ledger_id')
                        ->toArray();

                    foreach ($beforePeriodCredit as $ledger => $row) {

                        if (!isset($beforePeriodData[$ledger])) {
                            $beforePeriodData[$ledger]                = new \stdClass();
                            $beforePeriodData[$ledger]->ledger_id     = $ledger;
                            $beforePeriodData[$ledger]->s_debit_amount = 0;
                            $beforePeriodData[$ledger]->s_credit_amount = $row->credit_amount;
                            $beforePeriodData[$ledger]->s_cash_debit  = 0;
                            $beforePeriodData[$ledger]->s_cash_credit = 0;
                            $beforePeriodData[$ledger]->s_bank_debit  = 0;
                            $beforePeriodData[$ledger]->s_bank_credit = 0;
                            $beforePeriodData[$ledger]->s_jv_debit    = 0;
                            $beforePeriodData[$ledger]->s_jv_credit   = 0;
                            $beforePeriodData[$ledger]->s_ft_debit    = 0;
                            $beforePeriodData[$ledger]->s_ft_credit   = 0;
                        } else {
                            $beforePeriodData[$ledger]->s_credit_amount +=  $row->credit_amount;
                        }
                    }
                }
            }

            #### Cash 
            foreach ($cashLedgerIds as $ledgerId) {

                ## Opening Balance
                if (isset($obData[$ledgerId])) {
                    $thisOb = $obData[$ledgerId];
                    $amount_op_for_cash += $thisOb->s_debit_amount - $thisOb->s_credit_amount;
                }

                ## onPeriod this Month
                if (isset($opThisMonthData[$ledgerId])) {
                    $thisMonth = $opThisMonthData[$ledgerId];

                    $amount_this_month_for_cash += $thisMonth->s_debit_amount - $thisMonth->s_credit_amount;
                }

                ##  onPeriod Data
                if (isset($onPeriodData[$ledgerId])) {
                    $opData = $onPeriodData[$ledgerId];
                    $amount_op_for_cash += $opData->s_debit_amount - $opData->s_credit_amount;
                }

                ## Before Period Data
                if (isset($beforePeriodData[$ledgerId])) {
                    $bpData = $beforePeriodData[$ledgerId];
                    $amount_bp_for_cash += ($bpData->s_debit_amount - $bpData->s_credit_amount);
                }
            }
            #### Bank 
            foreach ($bankLedgerIds as $ledgerId) {

                ## Opening Balance
                if (isset($obData[$ledgerId])) {
                    $thisOb = $obData[$ledgerId];
                    $amount_op_for_bank += $thisOb->s_debit_amount - $thisOb->s_credit_amount;
                }

                ## onPeriod this Month
                if (isset($opThisMonthData[$ledgerId])) {
                    $thisMonth = $opThisMonthData[$ledgerId];
                    $amount_this_month_for_bank += $thisMonth->s_debit_amount - $thisMonth->s_credit_amount;
                }

                ##  onPeriod Data
                if (isset($onPeriodData[$ledgerId])) {
                    $opData = $onPeriodData[$ledgerId];
                    $amount_op_for_bank += $opData->s_debit_amount - $opData->s_credit_amount;
                }

                ## Before Period Data
                if (isset($beforePeriodData[$ledgerId])) {
                    $bpData = $beforePeriodData[$ledgerId];

                    $amount_bp_for_bank += ($bpData->s_debit_amount - $bpData->s_credit_amount);
                }
            }


            #On Period calculation
            $OnPeriodCashAndBank = [
                'cash_book'  => $amount_op_for_cash,
                'bank_book' => $amount_op_for_bank,
            ];

            #On Period calculation
            $beforePeriodCashAndBank = [
                'cash_book'  => $amount_bp_for_cash,
                'bank_book' => $amount_bp_for_bank,
            ];


            $cumulativeCashAndBank = [
                'cash_book'  => $amount_op_for_cash + $amount_bp_for_cash,
                'bank_book' => $amount_op_for_bank + $amount_bp_for_bank,
            ];

            // $resultData['OnPeriodCashAndBank']        = $openingData->toArray();
            // $resultData['OnPeriodCashAndBank'] = (object) $OnPeriodCashAndBank;

            $resultData['onPeriod'] = $OnPeriodCashAndBank;

            $resultData['beforePeriod'] = $beforePeriodCashAndBank;

            $resultData['cumulative'] = $cumulativeCashAndBank;

            if ($branchDate) {
                $resultData['branchDate'] = $branchDate;
            }

            // dd($resultData,$obData);
        }

        return $resultData;
    }

    public static function fnGetselectedBranchArr($branchId = '', $areaId = '', $zoneId = ''){

        $selBranchArr = array();

        if($branchId == -1){
            $selBranchArr = DB::table('gnl_branchs')
                                ->where([['is_delete',0],['is_active',1],['is_approve',1]])
                                ->pluck('id')
                                ->toarray();
        }

        else if($branchId == -2){
            $selBranchArr = DB::table('gnl_branchs')
                                ->where([['is_delete',0],['is_active',1],['is_approve',1]])
                                ->where('id','!=',1)
                                ->pluck('id')
                                ->toarray();
        }
        else if(!empty($branchId)){
            $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);
        }

        else{

            if(!empty($zoneId)){

                $selBranchArr = DB::table('gnl_zones')
                                    ->where([['is_delete',0],['is_active',1]])
                                    ->where('id', $zoneId)
                                    ->pluck('branch_arr')
                                    ->toArray();

                if(count($selBranchArr) > 0){
                    $selBranchArr = explode(',', $selBranchArr[0]);
                }
            }
            if(!empty($areaId)){

                $selBranchArr = DB::table('gnl_areas')
                                    ->where([['is_delete',0],['is_active',1]])
                                    ->where('id', $areaId)
                                    ->pluck('branch_arr')
                                    ->toArray();

                if(count($selBranchArr) > 0){
                    $selBranchArr = explode(',', $selBranchArr[0]);
                }
            }
        }

        return $selBranchArr;

    }
}
