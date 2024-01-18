<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Jobs\SendMailJob;
use App\Services\HrService;
use App\Services\AccService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\CommonService as Common;

class MfnService
{
    // this variables are used to generate loan schedule
    public static $regularLoanConfig   = null;
    public static $holidays            = [];
    public static $samity              = null;
    public static $samityDayChanges    = null;
    public static $requirement         = 'installments';
    public static $loanStatusFromDate  = null;
    public static $loanStatusToDate    = null;
    public static $loanReschedules     = null;
    public static $rescheduledLoanIds  = [];
    public static $exceptRescheduleId  = null;
    public static $massLoanReschedules = null;
    public static $scheduleMethodForHoliday = null;

    public static function resetProperties()
    {
        self::$regularLoanConfig   = null;
        self::$holidays            = [];
        self::$samity              = null;
        self::$samityDayChanges    = null;
        self::$requirement         = 'installments';
        self::$loanStatusFromDate  = null;
        self::$loanStatusToDate    = null;
        self::$loanReschedules     = null;
        self::$rescheduledLoanIds  = [];
        self::$exceptRescheduleId  = null;
        self::$massLoanReschedules = null;
        self::$scheduleMethodForHoliday = null;
    }

    public static function systemCurrentDate($branchId)
    {
        $sysDate = DB::table('mfn_day_end')
            ->where([
                ['branchId', $branchId],
                ['isActive', 1],
            ])
            ->where('is_delete', 0)
            ->max('date');

        if ($sysDate == null) {
            $sysDate = DB::table('gnl_branchs')
                ->where('id', $branchId)
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                ->first()
                ->mfn_start_date;
        }

        return $sysDate;
    }

    public static function getActiveFieldOfficers($branchId, $showDesignation = '')
    {
        // $notification = array(
        //     'message' => "Field officer set",
        //     'alert-type' => 'error',
        //     // 'Status Code' => 500,
        //     'display_type' => 'swal'
        // );

        // return response()->json($notification);

        // if($request->ajax()){
        //     return response()->json($notification);
        // }
        // else {
        //     return redirect()->back()->with($notification);
        // }

        // return $notification;

        // $fieldOfficersDesignationIds = json_decode(DB::table('mfn_config')->where('title', 'fieldOfficerHrDesignationIds')->first()->content);

        ## UID 1 = Credit Officer, 2 = Accountant
        $positions = DB::table('gnl_dynamic_form_value')
            ->where([['form_id', 3], ['type_id', 3], ['is_delete', 0], ['is_active', 1]])
            ->whereIn('uid', [1, 2])
            ->pluck('uid')
            ->toarray();

        $selectedDesignationQry = DB::table('hr_designation_role_mapping')
            ->whereIn('position_id', $positions)
            ->pluck('designation_ids')
            ->toArray();

        $designationIds = array();
        foreach ($selectedDesignationQry as $desig) {
            $selDesigArr = explode(',', $desig);
            foreach ($selDesigArr as $selDesig) {
                array_push($designationIds, $selDesig);
            }
        }

        if ($showDesignation) {
            $filedOfficers = DB::table('hr_employees as he')
                ->leftjoin('hr_designations as hd', function ($queryData) {
                    $queryData->on('hd.id', '=', 'he.designation_id');
                })
                ->where([
                    ['he.is_delete', 0],
                    ['he.is_active', 1],
                    ['he.status', 1],
                    ['he.branch_id', $branchId],
                ])
                ->whereIn('he.designation_id', $designationIds)
                ->select(DB::raw("CONCAT(he.emp_name, ' [', he.emp_code, ']', ' - ', hd.name) AS name, he.id"))
                ->get();
        } else {
            $filedOfficers = DB::table('hr_employees')
                ->where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['branch_id', $branchId],
                ])
                // ->whereIn('designation_id', $fieldOfficersDesignationIds)
                ->whereIn('designation_id', $designationIds)
                ->select(DB::raw("CONCAT(emp_name, ' [', emp_code, ']') AS name, id"))
                ->get();
        }

        return $filedOfficers;
    }
    public static function getActiveFieldOfficersBackup($branchId, $showDesignation = '')
    {
        // $notification = array(
        //     'message' => "Field officer set",
        //     'alert-type' => 'error',
        //     // 'Status Code' => 500,
        //     'display_type' => 'swal'
        // );

        // return response()->json($notification);

        // if($request->ajax()){
        //     return response()->json($notification);
        // }
        // else {
        //     return redirect()->back()->with($notification);
        // }

        // return $notification;

        // $fieldOfficersDesignationIds = json_decode(DB::table('mfn_config')->where('title', 'fieldOfficerHrDesignationIds')->first()->content);

        ## UID 1 = Credit Officer, 2 = Accountant
        $positions = DB::table('gnl_dynamic_form_value')
            ->where([['form_id', 3], ['type_id', 3], ['is_delete', 0], ['is_active', 1]])
            ->whereIn('uid', [1, 2])
            ->pluck('uid')
            ->toarray();

        $selectedDesignationQry = DB::table('hr_designation_role_mapping')
            ->whereIn('position_id', $positions)
            ->pluck('designation_ids')
            ->toArray();

        $designationIds = array();
        foreach ($selectedDesignationQry as $desig) {
            $selDesigArr = explode(',', $desig);
            foreach ($selDesigArr as $selDesig) {
                array_push($designationIds, $selDesig);
            }
        }

        if ($showDesignation) {
            $filedOfficers = DB::table('hr_employees as he')
                ->leftjoin('hr_designations as hd', function ($queryData) {
                    $queryData->on('hd.id', '=', 'he.designation_id');
                })
                ->where([
                    ['he.is_delete', 0],
                    ['he.is_active', 1],
                    ['he.status', 1],
                    ['he.branch_id', $branchId],
                ])
                ->whereIn('he.designation_id', $designationIds)
                ->select(DB::raw("CONCAT(he.emp_name, ' [', he.emp_code, ']', ' - ', hd.name) AS name, he.id"))
                ->get();
        } else {
            $filedOfficers = DB::table('hr_employees')
                ->where([
                    ['is_delete', 0],
                    ['is_active', 1],
                    ['branch_id', $branchId],
                ])
                // ->whereIn('designation_id', $fieldOfficersDesignationIds)
                ->whereIn('designation_id', $designationIds)
                ->select(DB::raw("CONCAT(emp_name, ' [', emp_code, ']') AS name, id"))
                ->get();
        }

        return $filedOfficers;
    }
    public static function getFieldOfficers($branchId, $showDesignation = '')
    {
        ## UID 1 = Credit Officer, 2 = Accountant
        $positions = DB::table('gnl_dynamic_form_value')
            ->where([['form_id', 3], ['type_id', 3], ['is_delete', 0], ['is_active', 1]])
            ->whereIn('uid', [1, 2])
            ->pluck('uid')
            ->toarray();

        $selectedDesignationQry = DB::table('hr_designation_role_mapping')
            ->whereIn('position_id', $positions)
            ->pluck('designation_ids')
            ->toArray();

        $designationIds = array();
        foreach ($selectedDesignationQry as $desig) {
            $selDesigArr = explode(',', $desig);
            foreach ($selDesigArr as $selDesig) {
                array_push($designationIds, $selDesig);
            }
        }

        if ($showDesignation) {
            $filedOfficers = DB::table('hr_employees as he')
                ->leftjoin('hr_designations as hd', function ($queryData) {
                    $queryData->on('hd.id', '=', 'he.designation_id');
                })
                ->where([
                    ['he.is_delete', 0],
                    // ['he.is_active', 1],
                    // ['he.status', 1],
                    ['he.branch_id', $branchId],
                ])
                ->whereIn('he.designation_id', $designationIds)
                ->selectRaw("CONCAT(he.emp_name, ' [', he.emp_code, ']', ' - ', hd.name) AS name, he.id")
                ->get();
        } else {
            $filedOfficers = DB::table('hr_employees')
                ->where([
                    ['is_delete', 0],
                    // ['is_active', 1],
                    ['branch_id', $branchId],
                ])
                // ->whereIn('designation_id', $fieldOfficersDesignationIds)
                ->whereIn('designation_id', $designationIds)
                ->selectRaw("CONCAT(
                        emp_name,
                        ' [', emp_code, ']',
                        (case
                            when status = 2 then '(Resigned)'
                            when status = 3 then '(Dismissed)'
                            when status = 4 then '(Terminated)'
                            when status = 5 then '(Retired)'
                            else ''
                        end)
                    ) AS name, id")
                ->get();
        }

        return $filedOfficers;
    }

    public static function getSamityAssignedDesignations($branchId, $showDesignation = '')
    {
        $filedOfficers = array();
        $designationIds = json_decode(DB::table('mfn_config')
            ->where('title', 'samity')
            ->select('content')
            ->pluck('content')
            ->first());

        $designationIds = isset($designationIds->samityAssignedDesignationIds) ? $designationIds->samityAssignedDesignationIds : '';
        if (empty($designationIds)) {
            return $filedOfficers;
        }
        if ($showDesignation) {
            $filedOfficers = DB::table('hr_employees as he')
                ->leftjoin('hr_designations as hd', function ($queryData) {
                    $queryData->on('hd.id', '=', 'he.designation_id');
                })
                ->where([
                    ['he.is_delete', 0],
                    ['he.is_active', 1],
                    ['he.status', 1],
                    ['he.branch_id', $branchId],
                ])
                ->whereIn('he.designation_id', $designationIds)
                ->selectRaw("CONCAT(he.emp_name, ' [', he.emp_code, ']', ' - ', hd.name) AS name, he.id")
                ->get();
        } else {
            $filedOfficers = DB::table('hr_employees')
                ->where([
                    ['is_delete', 0],
                    ['he.is_active', 1],
                    ['he.status', 1],
                    ['branch_id', $branchId],
                ])
                ->whereIn('designation_id', $designationIds)
                ->selectRaw("CONCAT(he.emp_name, ' [', he.emp_code, ']', ' - ') AS name, he.id")
                ->get();
        }

        return $filedOfficers;
    }

    public static function getCreditOfficersOfBranch($branchId)
    {
        $fieldOfficers = json_decode(DB::table('mfn_config')
                        ->where('title', 'samity')
                        ->select('content')
                        ->pluck('content')
                        ->first());
        $fieldOfficers = isset($fieldOfficers->samityAssignedDesignationIds) ? $fieldOfficers->samityAssignedDesignationIds : '';

        $creditOfficers = DB::table('hr_employees')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
                ['status', 1],
                ['branch_id', $branchId],
            ])
            ->whereIn('designation_id', $fieldOfficers)
            ->select(DB::raw("CONCAT(emp_name, ' [', emp_code, ']') AS name, id"))
            ->get();

        return $creditOfficers;
    }

    public static function getSamities($branchIdOrIds)
    {
        if (is_numeric($branchIdOrIds)) {
            $sysDate = self::systemCurrentDate($branchIdOrIds);

            $samities = DB::table('mfn_samity')
                ->where([
                    ['is_delete', 0],
                    ['branchId', $branchIdOrIds],
                    ['openingDate', '<=', $sysDate],
                    ['closingDate', null],
                ])
                ->orderBy('samityCode')
                ->selectRaw("CONCAT(name, ' [', samityCode, ']') AS name, id")
                ->get();
        } else {
            $samities = DB::table('mfn_samity')
                ->where([
                    ['is_delete', 0],
                    ['closingDate', null],
                ])
                ->orderBy('samityCode')
                ->whereIn('branchId', $branchIdOrIds)
                ->selectRaw("CONCAT(name, ' [', samityCode, ']') AS name, id")
                ->get();
        }

        return $samities;
    }

    public static function getWorkingWeekDaysBackup()
    {
        $weekDays = array(
            'Saturday'  => 'Saturday',
            'Sunday'    => 'Sunday',
            'Monday'    => 'Monday',
            'Tuesday'   => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday'  => 'Thursday',
            'Friday'    => 'Friday',
        );
        // get weekly holidays
        $weekEnds = DB::table('hr_holidays_comp')
            ->where([
                ['ch_title', 'Weekend'],
                ['is_delete', 0],
                ['is_active', 1],
            ])
            ->value('ch_day');

        $weekEnds = explode(',', $weekEnds);
        $weekDays = array_diff($weekDays, $weekEnds);

        return $weekDays;
    }

    public static function getWorkingWeekDays()
    {
        $sysDate = self::systemCurrentDate(Auth::user()->branch_id);

        $weekDays = array(
            'Saturday'  => 'Saturday',
            'Sunday'    => 'Sunday',
            'Monday'    => 'Monday',
            'Tuesday'   => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday'  => 'Thursday',
            'Friday'    => 'Friday',
        );
        // get weekly holidays
        $weekEnds = DB::table('hr_holidays_comp')
            ->where([
                // ['ch_title', 'Weekend'],
                ['is_delete', 0],
                ['is_active', 1],
            ])
            ->where('ch_eff_date', '<=', $sysDate)
            ->orderBy('ch_eff_date', 'DESC')
            ->first();

        $weekEnds = $weekEnds->ch_day;

        $weekEnds = explode(',', $weekEnds);
        $weekDays = array_diff($weekDays, $weekEnds);

        return $weekDays;
    }

    public static function getSavingsRegularProductInterestRate($productId, $date)
    {
        $interestRate = DB::table('mfn_savings_product_interest_rates')
            ->where([
                ['is_delete', 0],
                ['productId', $productId],
                ['effectiveDate', '<=', $date],
            ])
            ->where(function ($query) use ($date) {
                $query->where('validTill', '>=', $date)
                    ->orWhere('validTill', '0000-00-00');
            })
            ->value('interestRate');

        return $interestRate;
    }

    /**
     * get the interest rates of savings one time product
     * depending on the durations
     *
     * @return  array              [index = month, value = interest rate]
     */
    public static function getSavingsOnetimeProductInterestRates($productId, $date)
    {
        $interestRates = DB::table('mfn_savings_product_interest_rates')
            ->where([
                ['is_delete', 0],
                ['productId', $productId],
                ['parentId', 0],
                ['effectiveDate', '<=', $date],
            ])
            ->where(function ($query) use ($date) {
                $query->where('validTill', '>=', $date)
                    ->orWhere('validTill', '0000-00-00');
            })
            ->orderBy('durationMonth')
            ->pluck('interestRate', 'durationMonth')
            ->toArray();

        return $interestRates;
    }

    public static function getSavingsAccounts($filters = [])
    {
        $savAccs = DB::table('mfn_savings_accounts')->where('is_delete', 0);
        if (isset($filters['branchIds'])) {
            $savAccs->whereIn('branchId', $filters['branchIds']);
        }
        if (isset($filters['branchId'])) {
            $savAccs->where('branchId', $filters['branchId']);
        }
        if (isset($filters['samityId'])) {
            $savAccs->where('samityId', $filters['samityId']);
        }
        if (isset($filters['memberId'])) {
            $savAccs->where('memberId', $filters['memberId']);
        }
        if (isset($filters['openingDateFrom'])) {
            $savAccs->where('openingDate', '>=', $filters['openingDateFrom']);
        }
        if (isset($filters['openingDateTo'])) {
            $savAccs->where('openingDate', '<=', $filters['openingDateTo']);
        }
        if (!isset($filters['openingDateTo']) && !isset($filters['openingDateFrom'])) {
            if (isset($filters['memberId'])) { // must have filtered with member. so has correct branchId
                $temp    = clone ($savAccs);
                if (!empty($temp->first())) {
                    $sysDate = self::systemCurrentDate($temp->first()->branchId);
                    $savAccs->where('openingDate', '<=', $sysDate);
                }
            }
        }
        if (isset($filters['onlyActiveAccounts'])) {
            if ($filters['onlyActiveAccounts'] == 'yes') {
                $savAccs->where('closingDate', '0000-00-00');
            }
        }
        if (isset($filters['loanAdjustment'])) {
            if (!empty($filters['loanAdjustment'])) {
                $savAccs->whereIn('savingsProductId', $filters['loanAdjustment']);
            }
        }
        // if (isset($filters['accountType'])) {
        //     if ($filters['accountType'] == 'regular') {
        //         $productIds = DB::table('mfn_savings_product')->where('is_delete', 0)->where('productTypeId', 1)->pluck('id')->all();
        //     } elseif ($filters['accountType'] == 'onetime') {
        //         $productIds = DB::table('mfn_savings_product')->where('is_delete', 0)->where('productTypeId', 2)->pluck('id')->all();
        //     }
        //     $savAccs->whereIn('savingsProductId', $productIds);
        // }

        $savAccs = $savAccs->get();
        // dd($savAccs);
        return $savAccs;
    }

    public static function getSavingsBalance($filers = [])
    {
        $deposit  = DB::table('mfn_savings_deposit')->where([['is_delete', 0]])->whereNotIn('transactionTypeId', [8, 9]);
        $withdraw = DB::table('mfn_savings_withdraw')->where([['is_delete', 0]])->whereNotIn('transactionTypeId', [8, 9]);

        if (isset($filers['branchId'])) {
            $deposit->where('branchId', $filers['branchId']);
            $withdraw->where('branchId', $filers['branchId']);
        }
        if (isset($filers['samityId'])) {
            $deposit->where('samityId', $filers['samityId']);
            $withdraw->where('samityId', $filers['samityId']);
        }
        if (isset($filers['memberId'])) {
            $deposit->where('memberId', $filers['memberId']);
            $withdraw->where('memberId', $filers['memberId']);
        }
        if (isset($filers['memberIds'])) {
            $deposit->whereIn('memberId', $filers['memberIds']);
            $withdraw->whereIn('memberId', $filers['memberIds']);
        }
        if (isset($filers['accountId'])) {
            $deposit->where('accountId', $filers['accountId']);
            $withdraw->where('accountId', $filers['accountId']);
        }
        if (isset($filers['accountIds'])) {
            $deposit->whereIn('accountId', $filers['accountIds']);
            $withdraw->whereIn('accountId', $filers['accountIds']);
        }
        if (isset($filers['dateTo'])) {
            $deposit->where('date', '<=', $filers['dateTo']);
            $withdraw->where('date', '<=', $filers['dateTo']);
        }

        if (isset($filers['createAtTo'])) {
            $deposit->where('created_at', '<=', $filers['createAtTo']);
            $withdraw->where('created_at', '<=', $filers['createAtTo']);
        }

        if (isset($filers['primaryProductId'])) {
            $deposit->where('primaryProductId', $filers['primaryProductId']);
            $withdraw->where('primaryProductId', $filers['primaryProductId']);
        }

        if (isset($filers['individual'])) {
            if ($filers['individual'] === true) {
                $deposits = $deposit->groupBy('accountId')->select(DB::raw('accountId, SUM(amount) AS amount'))
                    ->get();
                $withdraws = $withdraw->groupBy('accountId')->select(DB::raw('accountId, SUM(amount) AS amount'))
                    ->get();

                $accountBalances = array();

                foreach ($deposits as $deposit) {
                    $accountBalance['accountId'] = $deposit->accountId;
                    $accountBalance['balance']   = $deposits->where('accountId', $deposit->accountId)->sum('amount') - $withdraws->where('accountId', $deposit->accountId)->sum('amount');
                    array_push($accountBalances, $accountBalance);
                }

                return $accountBalances;
            }
        }
        if (isset($filers['isAuthorized'])) {
            $deposit->where('isAuthorized', $filers['isAuthorized']);
            $withdraw->where('isAuthorized', $filers['isAuthorized']);
        }

        $balance = $deposit->sum('amount') - $withdraw->sum('amount');

        // if (isset($filers['neglectAmount'])) {
        //     $balance -= $filers['neglectAmount'];
        // }

        return $balance;
    }

    public static function getSavingsWithdraw($filers = [])
    {
        // $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0)->whereIn('transactionTypeId', [1, 2, 4, 6, 7, 10]);
        $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0)->whereNotIn('transactionTypeId', [8, 9]);

        if (isset($filers['branchId'])) {
            $withdraw->where('branchId', $filers['branchId']);
        }
        if (isset($filers['samityId'])) {
            $withdraw->where('samityId', $filers['samityId']);
        }
        if (isset($filers['memberId'])) {
            $withdraw->where('memberId', $filers['memberId']);
        }
        if (isset($filers['accountId'])) {
            $withdraw->where('accountId', $filers['accountId']);
        }
        if (isset($filers['dateTo'])) {
            $withdraw->where('date', '<=', $filers['dateTo']);
        }

        $balance = $withdraw->sum('amount');

        if (isset($filers['neglectAmount'])) {
            $balance -= $filers['neglectAmount'];
        }
        return $balance;
    }

    public static function getSavingsDeposit($filers = [])
    {
        // $deposit = DB::table('mfn_savings_deposit')->where('is_delete', 0)->whereIn('transactionTypeId', [1, 2, 4, 6, 7]);
        $deposit = DB::table('mfn_savings_deposit')->where('is_delete', 0)->whereNotIn('transactionTypeId', [8, 9]);
        // $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0);

        if (isset($filers['branchId'])) {
            $deposit->where('branchId', $filers['branchId']);
        }
        if (isset($filers['samityId'])) {
            $deposit->where('samityId', $filers['samityId']);
        }
        if (isset($filers['memberId'])) {
            $deposit->where('memberId', $filers['memberId']);
        }
        if (isset($filers['accountId'])) {
            $deposit->where('accountId', $filers['accountId']);
        }
        if (isset($filers['dateTo'])) {
            $deposit->where('date', '<=', $filers['dateTo']);
        }

        $balance = $deposit->sum('amount');

        if (isset($filers['neglectAmount'])) {
            $balance -= $filers['neglectAmount'];
        }
        return $balance;
    }

    public static function getSavingsInterest($filers = [])
    {
        $deposit = DB::table('mfn_savings_deposit')->where('is_delete', 0)->whereIn('transactionTypeId', [3, 5]);
        // $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0);

        if (isset($filers['branchId'])) {
            $deposit->where('branchId', $filers['branchId']);
        }
        if (isset($filers['samityId'])) {
            $deposit->where('samityId', $filers['samityId']);
        }
        if (isset($filers['memberId'])) {
            $deposit->where('memberId', $filers['memberId']);
        }
        if (isset($filers['accountId'])) {
            $deposit->where('accountId', $filers['accountId']);
        }
        if (isset($filers['dateTo'])) {
            $deposit->where('date', '<=', $filers['dateTo']);
        }

        $balance = $deposit->sum('amount');

        if (isset($filers['neglectAmount'])) {
            $balance -= $filers['neglectAmount'];
        }
        return $balance;
    }

    public static function getLoanCollection($filers = [])
    {
        $collection = DB::table('mfn_loan_collections')->where('is_delete', 0);
        // $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0);

        if (isset($filers['branchId'])) {
            $collection->where('branchId', $filers['branchId']);
            // $withdraw->where('branchId', $filers['branchId']);
        }
        if (isset($filers['samityId'])) {
            $collection->where('samityId', $filers['samityId']);
            // $withdraw->where('samityId', $filers['samityId']);
        }
        if (isset($filers['memberId'])) {
            $collection->where('memberId', $filers['memberId']);
            // $withdraw->where('memberId', $filers['memberId']);
        }
        if (isset($filers['loanId'])) {
            $collection->where('loanId', $filers['loanId']);
            // $withdraw->where('loanId', $filers['loanId']);
        }
        if (isset($filers['dateTo'])) {
            $collection->where('collectionDate', '<=', $filers['dateTo']);
            // $withdraw->where('date', '<=', $filers['dateTo']);
        }
        // print_r($collection."---");

        $balance = $collection->sum('amount');

        return $balance;
    }

    public static function isSamityDay($samityId, $date)
    {
        if ($date != null) {
            $date = date('Y-m-d', strtotime($date));
        }

        $isSamityDay     = false;
        $samityDayChange = DB::table('mfn_samity_day_changes')
            ->where([
                ['is_delete', 0],
                ['samityId', $samityId],
                ['effectiveDate', '>=', $date],
            ])
            ->orderBy('effectiveDate')
            ->limit(1)
            ->first();

        if ($samityDayChange != null) {
            $samityDay = $samityDayChange->newSamityDay;
        } else {
            $samityDay = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $samityId)->first()->samityDay;
        }

        if ($samityDay == date('l', strtotime($date))) {
            $isSamityDay = true;
        }

        return $isSamityDay;
    }

    /**
     *  determine is it from opening or not
     *  if the system date is equal to software start date
     *  and branch opening date is less than this date, than it is from opening
     *
     * @param   [int]  $branchId
     *
     * @return  [boolean]
     */
    public static function isOpening($branchId)
    {
        $sysDate = self::systemCurrentDate($branchId);
        $branch  = DB::table('gnl_branchs')
            ->where('is_delete', 0)
            ->where('is_active', 1)
            ->where('is_approve', 1)
            ->where('id', $branchId)
            ->first();
        if ($branch->branch_opening_date <= $branch->mfn_start_date && $sysDate <= $branch->mfn_start_date) {
            $isOpening = true;
        } else {
            $isOpening = false;
        }

        return $isOpening;
    }

    public function getActiveMembers($branchId, $samityId = null)
    {
        // $activeMembers = DB::table('mfn_members')
    }

    public static function getSelectizeMembers($filters = [])
    {
        $members = DB::table('mfn_members AS m')
            ->leftJoin('gnl_branchs as b', 'b.id', 'm.branchId')
            ->leftJoin('mfn_samity as s', 's.id', 'm.samityId')
            ->where([
                ['m.is_delete', 0],
                ['b.is_delete', 0],
                ['b.is_active', 1],
                ['b.is_approve', 1],
                ['s.is_delete', 0],
                ['m.closingDate', '0000-00-00'],
            ])
            ->orderBy('m.memberCode')
            ->select(DB::raw("m.id, m.name, m.memberCode, CONCAT(m.name, ' [', m.memberCode, ']') as member, CONCAT(b.branch_name, ' [', b.branch_code, ']') as branch, CONCAT(s.name, ' [', s.samityCode, ']') as samity, s.workingAreaId"));

        if (isset($filters['branchId'])) {
            $members->where('m.branchId', $filters['branchId']);
        }

        if (isset($filters['samityId'])) {
            $members->where('m.samityId', $filters['samityId']);
        }

        if (isset($filters['dateTo'])) {
            $members->where('m.admissionDate', '<=', $filters['dateTo']);
        }

        $members = $members->get();

        foreach ($members as $key => $member) {
            $worKingArea = DB::table('mfn_working_areas')
                ->where('is_delete', 0)
                ->where('id', $member->workingAreaId)
                ->value('name');

            $members[$key]->workingArea = $worKingArea;
        }

        return $members;
    }

    public static function getFirstRepayDate_backup_14092022($samityId, $loanProductId, $disbursementDate, $repaymentFrequencyId = null, $periodMonth = null)
    {
        $samity            = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $samityId)->first();
        $product           = DB::table('mfn_loan_products')->where('is_delete', 0)->where('id', $loanProductId)->first();
        $gracePeriodInDays = 0;

        // $disbursementDate = "01-03-2022";

        if ($product->productTypeId == 1) { // if it is regular

            $repaymentInfo = json_decode($product->repaymentInfo);
            $repaymentInfo = collect($repaymentInfo);

            $repayment = $repaymentInfo->where('repaymentFrequencyId', $repaymentFrequencyId)->first();

            if ($repayment == null) {
                return null;
            }
            $gracePeriodInDays = $repayment->gracePeriod;

            $targetDate = Carbon::parse($disbursementDate)->addDays($gracePeriodInDays);
        }

        if ($product->productTypeId == 2) { // if it is one time loan

            if ($periodMonth == null) {
                return null;
            }

            $targetDate = Carbon::parse($disbursementDate)->addMonthsNoOverflow($periodMonth);
        }

        // dd($disbursementDate, $targetDate);

        ## back up old code
        // if ($repaymentFrequencyId == 1) {
        //     $firstRepayDate = $targetDate;
        // } else {
        //     $firstRepayDate = self::getSamityDateOfWeek($samityId, $targetDate->format('Y-m-d'));

        //     while (Carbon::parse($firstRepayDate)->lt($targetDate)) {
        //         $firstRepayDate = self::getSamityDateOfWeek($samityId, Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d'));
        //     }
        // }

        if ($repaymentFrequencyId == 1) { ## Daily
            $firstRepayDate = $targetDate;
        } else {
            //// here
            $tempDisburseDate = date('m', strtotime($disbursementDate)) . '-' . date('d', strtotime($disbursementDate));
            if ($tempDisburseDate == '01-30' || $tempDisburseDate == '01-31') {
                $disburseMonth = date("m", strtotime($disbursementDate));
                $targetMonth   = date("m", strtotime($targetDate));

                if (intval($targetMonth) - intval($disburseMonth) == 2) {
                    $targetDate = Carbon::parse($targetDate->format("Y-02-28"));
                }
            }
            // dd($targetDate);

            $firstRepayDate = self::getSamityDateOfWeek($samityId, $targetDate->format('Y-m-d'));

            /*
            nicher part tuku only for monthly loans (proti month a atleast 1 ta collection aste e hobe)
            test case:
            disburseement date : 19-08-2021
            repaymentFrequencyId : 2 (weekly)
            product: 7 (covid 19)
            grace period : 14days

            First repay date => 23-08-2021 (not evena a week)

            tai nicher check boshano to we will only consider monthly loans
             */

            if (($targetDate->format("Y-m") != (new DateTime($firstRepayDate))->format('Y-m')) && $repaymentFrequencyId == 4) {

                $disbursementDateT = new DateTime($disbursementDate);
                $firstRepayDateT   = new DateTime($firstRepayDate);

                if ($disbursementDateT->format('Y-m') == $firstRepayDateT->format('Y-m')) {
                    $firstRepayDate = self::getSamityDateOfWeek($samityId, Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d'));
                } else {
                    $firstRepayDate = self::getSamityDateOfWeek($samityId, Carbon::parse($firstRepayDate)->subDays(7)->format('Y-m-d'));
                }
            } else {
                $firstRepayDateActual = new DateTime($firstRepayDate);

                while (Carbon::parse($firstRepayDate)->lt($targetDate)) {

                    $firstRepayDate = self::getSamityDateOfWeek($samityId, Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d'));
                    ## check if holiday exists on repay date
                    $isHolidayArr = HrService::systemHolidays(null, $samity->branchId, $samity->id, $firstRepayDate, $firstRepayDate);

                    if (count($isHolidayArr) > 0) {
                        $firstRepayDate = Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d');
                        $firstRepayDate = self::getSamityDateOfWeek($samityId, $firstRepayDate);

                        ## While holiday exists on repay date && firstRepayDate and taget date on same month
                        while (
                            Carbon::parse($firstRepayDate)->format('Y-m') == Carbon::parse($targetDate)->format('Y-m') &&
                            count(HrService::systemHolidays(null, $samity->branchId, $samity->id, $firstRepayDate, $firstRepayDate)) > 0
                        ) {
                            $firstRepayDate = Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d');
                            $firstRepayDate = self::getSamityDateOfWeek($samityId, $firstRepayDate);
                        }
                    }
                }
                ## jodi firstRepayDateActual & firstRepayDate er month different hoy tahole firstRepayDateActual newa ucit.
                ## noyto monthly loan gulor khetre 30 diner grace period meet kore na
                ## abar weekly loan hole month different hole jhamela nai
                if ($repaymentFrequencyId != 2) { ## except daily & weekly loan
                    if (
                        $firstRepayDateActual->format('Y-m') != (new DateTime($firstRepayDate))->format('Y-m')
                        && $firstRepayDateActual->format('Y-m') != (new DateTime($disbursementDate))->format('Y-m')
                    ) {
                        $firstRepayDate = $firstRepayDateActual->format('Y-m-d');
                    }
                }
            }
        }
        return $firstRepayDate;
    }

    public static function getFirstRepayDate($samityId, $loanProductId, $disbursementDate, $repaymentFrequencyId = null, $periodMonth = null)
    {
        $samity            = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $samityId)->first();
        $product           = DB::table('mfn_loan_products')->where('is_delete', 0)->where('id', $loanProductId)->first();
        $gracePeriodInDays = 0;

        if (self::$scheduleMethodForHoliday == null) {
            $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
            if ($companyConfig) {
                self::$scheduleMethodForHoliday = $companyConfig->form_value;
            }
        }

        // $disbursementDate = "22-01-2022";
        if ($product->productTypeId == 1) { // if it is regular

            $repaymentInfo = json_decode($product->repaymentInfo);
            $repaymentInfo = collect($repaymentInfo);

            $repayment = $repaymentInfo->where('repaymentFrequencyId', $repaymentFrequencyId)->first();

            if ($repayment == null) {
                return null;
            }
            $gracePeriodInDays = $repayment->gracePeriod;

            $targetDate = Carbon::parse($disbursementDate)->addDays($gracePeriodInDays);
        } elseif ($product->productTypeId == 2) { // if it is one time loan

            if ($periodMonth == null) {
                return null;
            }

            $targetDate = Carbon::parse($disbursementDate)->addMonthsNoOverflow($periodMonth);
        } else {
            return null;
        }

        #################
        /**
         * MONTHLY loan hole disbursment er porer month a must first repay date porbe.
         * Quarterly loan hole disbursment er porer 3 month er majhe must repay date porbe
         * Half Yearly hole disbursment er porer 6 month er majhe must repay date porbe
         * Yearly hole disbursment er porer 12 month er majhe must repay date porbe
         * ai karone nicher condition dewa
         */
        ##

        // dd($targetDate);

        if ($repaymentFrequencyId == 4) { ## monthly
            $disbursementDateObj = new DateTime($disbursementDate);
            $lastDateodDisbursementMonth = clone $disbursementDateObj;
            $lastDateodDisbursementMonth = ($lastDateodDisbursementMonth->modify('last day of this month'))->format('d');

            if ($disbursementDateObj->format('d-m') == '30-01' || $disbursementDateObj->format('d-m') == '31-01') {
                $targetDate = clone $disbursementDateObj;
                $targetDate = ($targetDate->modify('last day of next month'))->format('Y-m-d');
            } elseif ($disbursementDateObj->format('d') == '01') {
                $targetDate = clone $disbursementDateObj;
                $targetDate = ($targetDate->modify('first day of next month'))->format('Y-m-d');
            } else {
                if ($lastDateodDisbursementMonth == '30') {
                    $targetDate = Carbon::parse($targetDate)->subDays(1);
                } elseif ($lastDateodDisbursementMonth == '29') {
                    $targetDate = Carbon::parse($targetDate)->subDays(2);
                } elseif ($lastDateodDisbursementMonth == '28') {
                    $targetDate = Carbon::parse($targetDate)->subDays(3);
                }
            }
        }

        // dd($targetDate);

        ## only Weekly loan hole samity day er din first repay date porbe other day na
        if ($repaymentFrequencyId == 2) { ## Weekly
            $firstRepayDate = self::getSamityDateOfWeek($samityId, $targetDate->format('Y-m-d'));

            while (Carbon::parse($firstRepayDate)->lt($targetDate)) {

                $firstRepayDate = self::getSamityDateOfWeek($samityId, Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d'));
                ## check if holiday exists on repay date
                $isHolidayArr = HrService::systemHolidays(null, $samity->branchId, $samity->id, $firstRepayDate, $firstRepayDate);

                if (count($isHolidayArr) > 0) {
                    $firstRepayDate = Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d');
                    $firstRepayDate = self::getSamityDateOfWeek($samityId, $firstRepayDate);

                    ## While holiday exists on repay date && firstRepayDate and taget date on same month
                    while (
                        Carbon::parse($firstRepayDate)->format('Y-m') == Carbon::parse($targetDate)->format('Y-m') &&
                        count(HrService::systemHolidays(null, $samity->branchId, $samity->id, $firstRepayDate, $firstRepayDate)) > 0
                    ) {
                        $firstRepayDate = Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d');
                        $firstRepayDate = self::getSamityDateOfWeek($samityId, $firstRepayDate);
                    }
                }
            }
        } else {
            $firstRepayDate = $targetDate;
            $firstRepayDate = date('Y-m-d', strtotime($firstRepayDate));

            $isHolidayArr = HrService::systemHolidays(null, $samity->branchId, $samity->id, $firstRepayDate, $firstRepayDate);

            if (count($isHolidayArr) > 0) {

                if (self::$scheduleMethodForHoliday == "next") {
                    $firstRepayDate = HrService::systemNextWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);
                } else {
                    $firstRepayDate = HrService::systemPreviousWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);
                }
            }
        }

        ## daily(1)(1 day), weekly(2)(30 days) and Fortnightly(3)(15 days) chara sokol loan jei month a disburse hoyeche oi mase porbe na
        ## same month a 2 ta schedule porbe na
        if ($repaymentFrequencyId > 3) {

            $disbursementDateT = date("Y-m", strtotime($disbursementDate));
            $firstRepayDateT   = date("Y-m", strtotime($firstRepayDate));

            while ($disbursementDateT == $firstRepayDateT) {
                $firstRepayDate = HrService::systemNextWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);
                $firstRepayDateT   = date("Y-m", strtotime($firstRepayDate));
            }
        }

        ## monthly loan hole must 1 maser majhe ante hobe, jodi besi hoy tahole kombe date
        ## 2 ta schedule er majhe 1 gap 1 maser besi hobe na
        if ($repaymentFrequencyId == 4) {

            $disbursementDateTM = new DateTime($disbursementDate);
            $firstRepayDateTM = new DateTime($firstRepayDate);

            $disbursementDateTM->modify('first day of this month');
            $firstRepayDateTM->modify('first day of this month');

            $monthDiff = $disbursementDateTM->diff($firstRepayDateTM);
            $months = (int) ($monthDiff->y * 12 + $monthDiff->m + $monthDiff->d / 30);

            while ($months > 1) {
                $firstRepayDate = HrService::systemPreviousWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);

                $firstRepayDateTM = new DateTime($firstRepayDate);
                $firstRepayDateTM->modify('first day of this month');
                $monthDiff = $disbursementDateTM->diff($firstRepayDateTM);
                $months = (int) ($monthDiff->y * 12 + $monthDiff->m + $monthDiff->d / 30);
            }
        }

        return $firstRepayDate;
    }

    public static function getSamityDateOfWeek($samity, $date)
    {
        // week start date is SATURDAY
        if (date('D', strtotime($date)) == 'Sat') {
            $startOfWeek = strtotime($date);
        } else {
            $startOfWeek = strtotime("last Saturday", strtotime($date));
        }

        if (is_numeric($samity)) {
            $samity = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $samity)->first();
        }

        if (self::$samityDayChanges !== null) {
            $samityDayChange = self::$samityDayChanges
                ->where('effectiveDate', '>', $date)
                ->sortBy('effectiveDate')
                ->first();
        } else {
            $samityDayChange = DB::table('mfn_samity_day_changes')
                ->where('is_delete', 0)
                ->where('samityId', $samity->id)
                ->where('effectiveDate', '>', $date)
                ->orderBy('effectiveDate')
                ->limit(1)
                ->select('oldSamityDay')
                ->first();
        }

        if ($samityDayChange != null) {
            $samityDay = $samityDayChange->oldSamityDay;
        } else {
            $samityDay = $samity->samityDay;
        }

        if (date('l', strtotime($date)) == $samityDay) {
            $samityDate = date('Y-m-d', strtotime($date));
        } elseif ($samityDay == "Daily") {

            $samityDate = date('Y-m-d', strtotime($date));
            // $samityDate = "2022-02-21";

            $isHolidayArr = HrService::systemHolidays(null, $samity->branchId, $samity->id, $samityDate, $samityDate);

            if (count($isHolidayArr) > 0) {
                $samityDate = HrService::systemNextWorkingDay($samityDate, $samity->branchId, null, $samity->id);
                $samityDate = date('Y-m-d', strtotime($samityDate));
            }
        } else {
            $samityDate = date('Y-m-d', strtotime('next ' . $samityDay, $startOfWeek));
        }

        return $samityDate;
    }

    public static function getSamityFieldOfficerEmpId($samityIdOrIds, $date)
    {
        $date = date('Y-m-d', strtotime($date));

        if (is_array($samityIdOrIds)) {
            $samityIds = $samityIdOrIds;
        } else {
            $samityIds = [$samityIdOrIds];
        }

        $samities = DB::table('mfn_samity')
            ->whereIn('id', $samityIds)
            ->where('is_delete', 0)
            ->select('id', 'fieldOfficerEmpId')
            ->get();

        $fieldOfficerChanges = DB::table('mfn_samity_field_officer_change')
            ->where([
                ['is_delete', 0],
                ['effectiveDate', '<=', $date],
            ])
            ->whereIn('samityId', $samityIds)
            ->select('samityId', 'newFieldOfficerEmpId', 'effectiveDate')
            ->get();

        foreach ($samities as $key => $samity) {
            if (count($fieldOfficerChanges->where('samityId', $samity->id)) > 0) {
                $maxDate                           = $fieldOfficerChanges->where('samityId', $samity->id)->max('effectiveDate');
                $samities[$key]->fieldOfficerEmpId = $fieldOfficerChanges->where('samityId', $samity->id)->where('effectiveDate', $maxDate)->first()->newFieldOfficerEmpId;
            }
        }

        if (is_array($samityIdOrIds)) {
            return $samities->pluck('fieldOfficerEmpId', 'id')->toArray();
        }

        return $samities->first()->fieldOfficerEmpId;
    }

    /**
     * You can pass loan id as int or loanids as array
     * if you pass a single date, you will receive loan status on that date
     * if you pass two dates, then you will get loan status on second date and other statuses from first date to second date
     * ## dueAmount ... = Always Cumulative ()
     * ## onPeriodDueAmount ... = Within period
     * ## openingDueAmount ... = Before Starting Period
     *
     * @param [int/array] $loanIdOrIds
     * @param [date] ...$dates
     * @return array
     */
    public static function getLoanStatus($loanIdOrIds, ...$dates)
    {
        self::$requirement = 'status';

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->whereIn('id', $loanIdOrIds)
            ->where('is_delete', 0)
            ->select('id', 'loanAmount', 'repayAmount', 'samityId', 'memberId', 'productId', 'installmentAmount', 'lastInstallmentAmount')
            ->get()->keyBy('id');

        $loanCollections = DB::table('mfn_loan_collections')
            ->whereIn('loanId', $loanIdOrIds)
            ->where('is_delete', 0)
            ->groupby('loanId')
            ->groupby('paymentType')
            ->select(DB::raw("loanId, paymentType, SUM(amount) AS amount, SUM(principalAmount) AS principalAmount, SUM(interestAmount) AS interestAmount"));

        if (self::$loanStatusToDate != null) {
            $loanCollections->where('collectionDate', '<=', self::$loanStatusToDate);
        }

        if (self::$loanStatusFromDate != null) {
            $loanCollectionOnPeriod = clone $loanCollections;
            $loanCollectionOnPeriod->where('collectionDate', '>=', self::$loanStatusFromDate);
            $loanCollectionOnPeriod = $loanCollectionOnPeriod->get();
        }

        $loanCollections = $loanCollections->get();

        $loanStatuses = self::generateLoanSchedule($loanIdOrIds, ...$dates);

        // if self::$loanStatusFromDate != null then you are trying to get loan status
        // between two dates. Here we will get payable amount till $loanStatusToDate date
        // and payable amount between two dates, also we will get paid amount till $loanStatusToDate
        // date and between two dates

        foreach ($loanStatuses as $key => $loanStatus) {
            $loanCollection      = $loanCollections->where('loanId', $loanStatus['loanId']);
            $paidAmount          = $loanCollection->sum('amount');
            $paidAmountPrincipal = $loanCollection->sum('principalAmount');
            $paidAmountInterest  = $loanCollection->sum('interestAmount');

            $dueAmount              = $loanStatus['payableAmount'] - $paidAmount;
            $dueAmountPrincipal     = round($loanStatus['payableAmountPrincipal'] - $paidAmountPrincipal, 5);
            $advanceAmount          = $dueAmount == 0 ? 0 : -$dueAmount;
            $advanceAmountPrincipal = $dueAmountPrincipal == 0 ? 0 : -$dueAmountPrincipal;

            $dueAmount              = $dueAmount < 0 ? 0 : $dueAmount;
            $dueAmountPrincipal     = $dueAmountPrincipal <= 0 ? 0 : $dueAmountPrincipal;
            $advanceAmount          = $advanceAmount < 0 ? 0 : $advanceAmount;
            $advanceAmountPrincipal = $advanceAmountPrincipal < 0 ? 0 : $advanceAmountPrincipal;

            // Assign values to main object
            $loanStatuses[$key]['memberId']  = $loans[$loanStatus['loanId']]->memberId;
            $loanStatuses[$key]['samityId']  = $loans[$loanStatus['loanId']]->samityId;
            $loanStatuses[$key]['productId'] = $loans[$loanStatus['loanId']]->productId;

            $loanStatuses[$key]['paidAmount']             = $paidAmount;
            $loanStatuses[$key]['paidAmountPrincipal']    = $paidAmountPrincipal;
            $loanStatuses[$key]['paidAmountInterest']     = $paidAmountInterest;
            $loanStatuses[$key]['rebateAmount']           = $loanCollection->where('paymentType', 'Rebate')->sum('amount');
            $loanStatuses[$key]['dueAmount']              = $dueAmount;
            $loanStatuses[$key]['dueAmountPrincipal']     = $dueAmountPrincipal;
            $loanStatuses[$key]['advanceAmount']          = $advanceAmount;
            $loanStatuses[$key]['advanceAmountPrincipal'] = $advanceAmountPrincipal;

            // $loanStatuses[$key]['outstanding']            = $loans->where('id', $loanStatus['loanId'])->sum('repayAmount') - $paidAmount;
            // $loanStatuses[$key]['outstandingPrincipal']   = $loans->where('id', $loanStatus['loanId'])->sum('loanAmount') - $paidAmountPrincipal;

            $loanStatuses[$key]['outstanding']          = $loans[$loanStatus['loanId']]->repayAmount - $paidAmount;
            $loanStatuses[$key]['outstandingPrincipal'] = $loans[$loanStatus['loanId']]->loanAmount - $paidAmountPrincipal;

            // now calculate on period data i.e. between two dates
            if (self::$loanStatusFromDate != null) {
                // to know about on period status, we need to know status before start date
                $beginningPayable          = $loanStatus['payableAmount'] - $loanStatus['periodPayableAmount'];
                $beginningPayablePrincipal = $loanStatus['payableAmountPrincipal'] - $loanStatus['periodPayableAmountPrincipal'];

                $onPeriodPaidAmount = $loanCollectionOnPeriod->where('loanId', $loanStatus['loanId'])->sum('amount');

                $onPeriodPaidAmountPrincipal  = $loanCollectionOnPeriod->where('loanId', $loanStatus['loanId'])->sum('principalAmount');
                $beginningPaidAmount          = $paidAmount - $onPeriodPaidAmount;
                $beginningPaidAmountPrincipal = $paidAmountPrincipal - $onPeriodPaidAmountPrincipal;

                $beginigAdvanceAmount          = $beginningPaidAmount - $beginningPayable;
                $beginigAdvanceAmountPrincipal = $beginningPaidAmountPrincipal - $beginningPayablePrincipal;

                $beginigDueAmount          = -$beginigAdvanceAmount;
                $beginigDueAmountPrincipal = -$beginigAdvanceAmountPrincipal;

                $beginigAdvanceAmount          = $beginigAdvanceAmount < 0 ? 0 : $beginigAdvanceAmount;
                $beginigAdvanceAmountPrincipal = $beginigAdvanceAmountPrincipal < 0 ? 0 : $beginigAdvanceAmountPrincipal;

                $beginigDueAmount          = $beginigDueAmount < 0 ? 0 : $beginigDueAmount;
                $beginigDueAmountPrincipal = $beginigDueAmountPrincipal < 0 ? 0 : $beginigDueAmountPrincipal;

                // if advanced paid before period than it will be deducted
                $onPeriodPayable          = $loanStatus['periodPayableAmount'] - $beginigAdvanceAmount;
                $onPeriodPayable          = $onPeriodPayable < 0 ? 0 : $onPeriodPayable;
                $onPeriodPayablePrincipal = $loanStatus['periodPayableAmountPrincipal'] - $beginigAdvanceAmountPrincipal;
                $onPeriodPayablePrincipal = $onPeriodPayablePrincipal < 0 ? 0 : $onPeriodPayablePrincipal;

                $onPeriodDueAmount              = $onPeriodPayable - $onPeriodPaidAmount;
                $onPeriodAdvanceAmount          = -$onPeriodDueAmount;
                $onPeriodDueAmountPrincipal     = $onPeriodPayablePrincipal - $onPeriodPaidAmountPrincipal;
                $onPeriodAdvanceAmountPrincipal = -$onPeriodDueAmountPrincipal;

                $onPeriodDueAmount              = $onPeriodDueAmount <= 0 ? 0 : $onPeriodDueAmount;
                $onPeriodAdvanceAmount          = $onPeriodAdvanceAmount <= 0 ? 0 : $onPeriodAdvanceAmount;
                $onPeriodDueAmountPrincipal     = $onPeriodDueAmountPrincipal <= 0 ? 0 : $onPeriodDueAmountPrincipal;
                $onPeriodAdvanceAmountPrincipal = $onPeriodAdvanceAmountPrincipal <= 0 ? 0 : $onPeriodAdvanceAmountPrincipal;

                // Assign values to main object
                $loanStatuses[$key]['onPeriodPayable']                = $onPeriodPayable;
                $loanStatuses[$key]['onPeriodPayablePrincipal']       = $onPeriodPayablePrincipal;
                $loanStatuses[$key]['onPeriodDueAmount']              = $onPeriodDueAmount;
                $loanStatuses[$key]['onPeriodDueAmountPrincipal']     = $onPeriodDueAmountPrincipal;
                $loanStatuses[$key]['onPeriodAdvanceAmount']          = $onPeriodAdvanceAmount;
                $loanStatuses[$key]['onPeriodAdvanceAmountPrincipal'] = $onPeriodAdvanceAmountPrincipal;
                $loanStatuses[$key]['onPeriodCollection']             = $onPeriodPaidAmount;
                $loanStatuses[$key]['onPeriodCollectionPrincipal']    = $onPeriodPaidAmountPrincipal;
                $loanStatuses[$key]['onPeriodCollectionInterest']     = $onPeriodPaidAmount - $onPeriodPaidAmountPrincipal;

                // classify regular, due, advance collection
                $remainingCollectionAmount                      = $onPeriodPaidAmount;
                $loanStatuses[$key]['onPeriodReularCollection'] = min($onPeriodPayable, $remainingCollectionAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollection'];

                $loanStatuses[$key]['onPeriodDueCollection'] = min($remainingCollectionAmount, $beginigDueAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollection'];

                $loanStatuses[$key]['onPeriodAdvanceCollection'] = $remainingCollectionAmount;

                // Principal
                $remainingCollectionAmount                               = $onPeriodPaidAmountPrincipal;
                $loanStatuses[$key]['onPeriodReularCollectionPrincipal'] = min($onPeriodPayablePrincipal, $remainingCollectionAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollectionPrincipal'];

                $loanStatuses[$key]['onPeriodDueCollectionPrincipal'] = min($remainingCollectionAmount, $beginigDueAmountPrincipal);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollectionPrincipal'];

                $loanStatuses[$key]['onPeriodAdvanceCollectionPrincipal'] = $remainingCollectionAmount;

                $loanStatuses[$key]['onPeriodRebateAmount'] = $loanCollectionOnPeriod->where('paymentType', 'Rebate')->sum('amount');

                // calculate number of regular full collection
                // $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($loanStatuses[$key]['onPeriodReularCollection'] / $loans[$loanStatus['loanId']]->installmentAmount);

                // if ($loanStatuses[$key]['isLastInstallmentPresent'] && ($loanStatuses[$key]['onPeriodReularCollection'] % $loans[$loanStatus['loanId']]->installmentAmount >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)) {
                //     $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                // }

                $onPeriodRegAdvCollection = $beginigAdvanceAmount + $loanStatuses[$key]['onPeriodReularCollection'];
                $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($onPeriodRegAdvCollection / $loans[$loanStatus['loanId']]->installmentAmount);

                // if($loanStatus['loanId'] == 19733){
                //     dd($onPeriodRegAdvCollection, $loans[$loanStatus['loanId']]->installmentAmount, $loanStatuses[$key]['numberOfFullyPaidRegularCollection']);
                // }

                if (
                    $loanStatuses[$key]['isLastInstallmentPresent']
                    && (($onPeriodRegAdvCollection % $loans[$loanStatus['loanId']]->installmentAmount) >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)
                ) {
                    $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                }
            }
        }

        return $loanStatuses;
    }

    ## This Function is called for scheduling in all reports except col sheet
    public static function generateLoanSchedule($loanIdOrIds, ...$dates)
    {
        if (self::$scheduleMethodForHoliday == null) {
            $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
            if ($companyConfig) {
                self::$scheduleMethodForHoliday = $companyConfig->form_value;
            }
        }

        self::$regularLoanConfig = json_decode(DB::table('mfn_config')->where('title', 'regularLoan')->value('content'));
        rsort(self::$regularLoanConfig->preferedAmounts);

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId')
            ->orderBy('samityId')
            ->get();

        // get reschedules
        // here self::$loanReschedules and self::$rescheduledLoanIds it defind into a condition
        // because generateLoanSchedule() functionn may call from dummy reschedule data
        // when it is call from dummy values then merge it with original
        $loanReschedules = DB::table('mfn_loan_reschedules')
            ->where('is_delete', 0)
            ->whereIn('loanId', $loanIdOrIds);
        if (self::$exceptRescheduleId !== null) {
            $loanReschedules->where('id', '!=', self::$exceptRescheduleId);
        }
        $loanReschedules = $loanReschedules->get();

        if (self::$loanReschedules !== null) {
            self::$loanReschedules = self::$loanReschedules->merge($loanReschedules);
        } else {
            self::$loanReschedules = $loanReschedules;
        }

        self::$rescheduledLoanIds = self::$loanReschedules->pluck('loanId')->toArray();

        self::$massLoanReschedules = DB::table('mfn_loan_mass_reschedules')->where('is_delete', 0)->get();

        $currentSamityId = 0;
        $schedules       = [];
        $loanStatuses    = [];

        // get the rage of dates between which we will get the holidays
        $holidayFrom = date('Y-m-d', strtotime($loans->min('disbursementDate')));
        $holidayTo   = date('Y-m-d', strtotime("+10 years", strtotime($loans->max('disbursementDate')))); // here we assume that no loan period is longer than 10 years

        ## Newly added
        // if(self::$loanStatusFromDate){
        //     $holidayFrom = self::$loanStatusFromDate;
        // }
        // if(self::$loanStatusToDate){
        //     $holidayTo = self::$loanStatusToDate;
        // }
        ###

        $samityIdsHavingSamityHoliday = DB::table('hr_holidays_special')
            ->where([
                ['is_delete', 0],
                ['sh_date_from', '>=', $holidayFrom],
                ['sh_date_to', '<=', $holidayTo],
                ['samity_id', '>', 0],
            ])
            ->groupBy('samity_id')
            ->pluck('samity_id')
            ->toArray();



        foreach ($loans as $key => $loan) {

            if ($currentSamityId != $loan->samityId) {
                $currentSamityId        = $loan->samityId;
                self::$samity           = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $loan->samityId)->first();
                self::$samityDayChanges = DB::table('mfn_samity_day_changes')
                    ->where([
                        ['is_delete', 0],
                        ['samityId', $loan->samityId],
                    ])
                    ->get();
            }


            if (count(self::$holidays) == 0 || in_array($loan->samityId, $samityIdsHavingSamityHoliday)) {
                $tempHolidays = HrService::systemHolidays(null, $loan->branchId, $loan->samityId, $holidayFrom, $holidayTo);
                self::$holidays = $tempHolidays;
            }

            ## April month er por data thik extra installment amount change kora hoyechilo tai db er data e final tader jonno
            if ($loan->disbursementDate < "2022-04-01") {
                $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);
            } else {
                ## old data store in table no need to generate again. new generate korle old data er sathe match kore na
                $repayAmount = round($loan->loanAmount * $loan->interestRateIndex);
                $interestAmount        = $repayAmount - $loan->loanAmount;

                $installmentAmountPrincipal = round($loan->installmentAmount / $loan->interestRateIndex, 5);
                $installmentAmountInterest  = $loan->installmentAmount - $installmentAmountPrincipal;

                $lastInstallmentPrincipal   = round($loan->loanAmount - ($installmentAmountPrincipal * ($loan->numberOfInstallment - 1)), 5);
                $lastInstallmentInterest    = round($interestAmount - ($installmentAmountInterest * ($loan->numberOfInstallment - 1)), 5);


                $installments['installmentAmount'] = $loan->installmentAmount;
                $installments['actualInastallmentAmount'] = $loan->actualInstallmentAmount;
                $installments['extraInstallmentAmount'] = $loan->extraInstallmentAmount;
                $installments['lastInstallmentAmount'] = $loan->lastInstallmentAmount;

                $installments['installmentAmountPrincipal'] = $installmentAmountPrincipal;
                $installments['installmentAmountInterest'] = $installmentAmountInterest;
                $installments['lastInstallmentPrincipal'] = $lastInstallmentPrincipal;
                $installments['lastInstallmentInterest'] = $lastInstallmentInterest;
            }


            $insallmentdates = self::generateInstallmentDates($loan);

            $installmentNo            = 1;
            $installmentCounted       = 0;
            $periodInstallmentCounted = 0;
            $payableAmount            = $payableAmountPrincipal            = $periodPayableAmount            = $periodPayableAmountPrincipal            = 0;

            foreach ($insallmentdates as $insallmentdate) {

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $insallmentdate;
                $schedule['weekDay']         = date('l', strtotime($insallmentdate));

                if ($installmentNo == $loan->numberOfInstallment) {
                    // if it the last installment
                    $schedule['installmentAmount']          = $installments['lastInstallmentAmount'];
                    $schedule['actualInastallmentAmount']   = 0;
                    $schedule['extraInstallmentAmount']     = 0;
                    $schedule['installmentAmountPrincipal'] = $installments['lastInstallmentPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['lastInstallmentInterest'];
                } else {
                    $schedule['installmentAmount']          = $installments['installmentAmount'];
                    $schedule['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                    $schedule['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                    $schedule['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['installmentAmountInterest'];
                }

                if (self::$requirement == 'status') {
                    $payableAmount += $schedule['installmentAmount'];
                    $payableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                    $installmentCounted++;

                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate) {
                        $periodPayableAmount += $schedule['installmentAmount'];
                        $periodPayableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                        $periodInstallmentCounted++;
                    }
                } else {
                    // dd($insallmentdate, self::$loanStatusFromDate, $schedule);
                    if ($insallmentdate >= self::$loanStatusFromDate) {
                        array_push($schedules, $schedule);
                    }
                }

                $installmentNo++;
            }
            if (self::$requirement == 'status') {
                $loanStatus['loanId']                 = $loan->id;
                $loanStatus['payableAmount']          = $payableAmount;
                $loanStatus['payableAmountPrincipal'] = $payableAmountPrincipal;

                $loanStatus['isLastInstallmentPresent'] = false;
                if (isset($insallmentdate)) {
                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    } elseif (self::$loanStatusFromDate == null && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    }
                }

                // $loanStatus['isLastInstallmentPresent'] = --$installmentNo == $loan->numberOfInstallment ? true : false;
                $loanStatus['installmentCounted'] = $installmentCounted;
                if (self::$loanStatusFromDate != null) {
                    $loanStatus['periodPayableAmount']          = $periodPayableAmount;
                    $loanStatus['periodPayableAmountPrincipal'] = $periodPayableAmountPrincipal;
                    $loanStatus['periodInstallmentCounted']     = $periodInstallmentCounted;
                }
                array_push($loanStatuses, $loanStatus);
            }
        } /* loan loop end */

        if (self::$requirement == 'status') {
            return $loanStatuses;
        }

        return $schedules;
    }
    /**
     ** এই ফাংশনটি বানানো হয়েছে কারন Date Range এর মধ্যে যেইসব লোন এর Schedule আছে শুধুমাত্র সেইসব লোন এর ID গুলো
     * পাওয়ার জন্য।
     ** আমরা getLoanStatusTTL ফাংশনটি একই রেখে এই ফাংশন এর মাধ্যমে লোন ID Customize করে শুধুমাত্র সেইসব লোন ID গুলো
     * getLoanStatusTTL ফাংশনটিতে পাঠাব। এতে করে আমাদের লোড এর পরিমান অনেকাংশে কমে যাবে।
     ** এই ফাংশনটির প্যারামিটার গুলো হল branchId, samityId, loanIdArr, startDate, endDate, branchArr
     ** এই প্যারামিটার গুলার যেকোনো একটি বা দুইটি প্যারামিটার না দিলে কোন ইররর দেখাবেনা কিন্তু কোন প্যারামিটারই যদি না দেয়া হয় তবে
     * অধিক ডাটার কারনে ইররর দেখাতে পারে।
     */
    public static function getLoanIdsScheduleWise($parameter = [])
    {
        $branchId   = (isset($parameter['branchId'])) ? $parameter['branchId'] : null;
        $samityId   = (isset($parameter['samityId'])) ? $parameter['samityId'] : null;
        $loanIdArr  = (isset($parameter['loanIds'])) ? $parameter['loanIds'] : array();
        $startDate  = (isset($parameter['startDate'])) ? $parameter['startDate'] : null;
        $endDate    = (isset($parameter['endDate'])) ? $parameter['endDate'] : null;
        $branchArr  = (isset($parameter['branchArr'])) ? $parameter['branchArr'] : HrService::getUserAccesableBranchIds();
        $loanIds = DB::table('mfn_loan_schedule')
                ->whereIn('branchId', $branchArr)
                ->where(function ($query) use ($branchId, $samityId, $loanIdArr, $startDate, $endDate) {
                    if(!empty($branchId))
                    {
                        $query->where('branchId', $branchId);
                    }
                    if(!empty($samityId))
                    {
                        $query->where('samityId', $samityId);
                    }
                    if(!empty($loanIdArr))
                    {
                        $query->whereIn('loanId', $loanIdArr);
                    }
                    if(!empty($endDate) && !empty($startDate))
                    {
                        $query->whereBetween('date', [$startDate, $endDate]);
                    }
                    if(empty($endDate) && !empty($startDate))
                    {
                        $query->where('date', '>=', $startDate);
                    }
                    if(!empty($endDate) && empty($startDate))
                    {
                        $query->where('date', '<=', $endDate);
                    }
                })
                ->groupBy('loanId')
                ->pluck('loanId')
                ->toArray();

        return $loanIds;
    }

    /**
     * getLoanStatusTTL() function is updated function for getting loan status.
     * In this function we provide all kind of loan information.
     * You can pass loan id as int or loanids as array
     * if you pass a single date, you will receive loan status on that date
     * if you pass two dates, then you will get loan status on second date and other statuses from first date to second date
     * ## dueAmount ... = Always Cumulative ()
     * ## onPeriodDueAmount ... = Within period
     * ## openingDueAmount ... = Before Starting Period
     *
     * @param [int/array] $loanIdOrIds
     * @param [date] ...$dates
     * @param $byCurrentFieldOfficer = values("yes", "no")
     * We know samity's feild officer changable and we need to samity data view feild officer wise.
     * But sometime we don't need to divided data between feild officer. All data view in current feild officer's name.
     * Thats why we use $byCurrentFieldOfficer variable for flag.
     * $byCurrentFieldOfficer = Show report by current field officer
     * // ## $samityFosForOp = samity feild officer for on period
     * @return array
     */

    public static function getLoanStatusTTL($loanIdOrIds, $byCurrentFieldOfficer, $withDueMember = false, ...$dates)
    {
        $loanStatuses = array();

        $dueMembers = array();

        self::resetProperties();
        // self::$requirement = 'status';

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        // $loans = DB::table('mfn_loans')
        //     ->whereIn('id', $loanIdOrIds)
        //     ->select('id', 'loanAmount', 'repayAmount', 'samityId', 'productId', 'installmentAmount', 'lastInstallmentAmount')
        //     ->get()
        //     ->keyBy('id');

        $loanStatuses = self::generateLoanScheduleTTL($loanIdOrIds, ...$dates);

        $samityIdsFound = array();
        $samityIdsFound = array_map(function ($row) {
            return $row['loan_data']['samityId'];
        }, array_values($loanStatuses));

        if (count($samityIdsFound) > 0) {
            $samityIdsFound = array_values(array_unique($samityIdsFound));
        }

        $samityData = self::getSamityInformationDataset($samityIdsFound, self::$loanStatusFromDate, self::$loanStatusToDate, $byCurrentFieldOfficer);

        // dd($samityData);

        if ("loan_collection_query") {

            $loanCollectionOnPeriod = array();
            $loanCollectionOnPeriodIdWise = array();
            $loanCollectionOnPeriodIdPaymentWise = array();

            $loanCollectionBeforePeriod = array();
            $loanCollectionBeforePeriodIdWise = array();
            $loanCollectionBeforePeriodIdPaymentWise = array();

            $loanCollectionCum = array();
            $loanCollectionCumLoanIdWise = array();
            $loanCollectionCumLoanIdWisePay = array();

            $loanCollections = DB::table('mfn_loan_collections')
                ->whereIn('loanId', $loanIdOrIds)
                ->where('is_delete', 0)
                ->where('isAuthorized', 1)
                ->selectRaw('loanId, productId, collectionDate, paymentType, amount, principalAmount, interestAmount, extraInterest');

            if (self::$loanStatusToDate != null) {
                ## Cumulative Data until dateTo
                $loanCollectionCum = clone $loanCollections;
                $loanCollectionCum->where('collectionDate', '<=', self::$loanStatusToDate)
                    ->orWhere("paymentType","LIKE", "OB"); ## Newly Added 30-09-2023 

                $loanCollectionCum = $loanCollectionCum->get();
                $loanCollectionCumLoanIdWise = $loanCollectionCum->groupBy('loanId');
                $loanCollectionCumLoanIdWisePay = $loanCollectionCum->groupBy(['loanId', 'paymentType']);

                ## this is do for dateTo selected only
                if (self::$loanStatusFromDate == null) {
                    $loanCollectionOnPeriod = $loanCollectionCum;
                    $loanCollectionOnPeriodIdWise = $loanCollectionCumLoanIdWise;
                    $loanCollectionOnPeriodIdPaymentWise = $loanCollectionCumLoanIdWisePay;
                }
            }

            if (self::$loanStatusFromDate != null) {

                ## on period
                $loanCollectionOnPeriod = clone $loanCollections;
                $loanCollectionOnPeriod->where('collectionDate', '>=', self::$loanStatusFromDate)->where("paymentType","NOT LIKE", "OB");
                if (self::$loanStatusToDate != null) {
                    ## dateTo null hole from date theke system er current date porjonto calculation asbe.
                    ## aikhane bug dhora porte pare valo kore test dite hobe
                    $loanCollectionOnPeriod->where('collectionDate', '<=', self::$loanStatusToDate);
                }

                $loanCollectionOnPeriod = $loanCollectionOnPeriod->get();
                $loanCollectionOnPeriodIdWise = $loanCollectionOnPeriod->groupBy('loanId');
                $loanCollectionOnPeriodIdPaymentWise = $loanCollectionOnPeriod->groupBy(['loanId', 'paymentType']);


                ## Before Period
                $loanCollectionBeforePeriod = clone $loanCollections;
                $loanCollectionBeforePeriod->where('collectionDate', '<', self::$loanStatusFromDate)->orWhere("paymentType","LIKE", "OB");

                $loanCollectionBeforePeriod = $loanCollectionBeforePeriod->get();
                $loanCollectionBeforePeriodIdWise = $loanCollectionBeforePeriod->groupBy('loanId');
                $loanCollectionBeforePeriodIdPaymentWise = $loanCollectionBeforePeriod->groupBy(['loanId', 'paymentType']);
            }

            $loanCollections = $loanCollections->get();
            $loanCollectionLoanIdWise = $loanCollections->groupBy('loanId');
            $loanCollectionLoanIdWisePay = $loanCollections->groupBy(['loanId', 'paymentType']);

            // 'Cash','Bank','Rebate','Waiver','WriteOff','OB','Adjustment'
        }

        // if self::$loanStatusFromDate != null then you are trying to get loan status
        // between two dates. Here we will get payable amount till $loanStatusToDate date
        // and period payable amount between two dates, also we will get paid amount till $loanStatusToDate
        // date and between two dates

        // field officer wise data only on period a asbe

        /**
         * Loan_data or loan_data_cal is represent loan all over information(no date range applicable)
         * cumulative_data or cumulative_calculation is represent loan status until DateTo selected
         * before_period_data or before_period_calculation is represent loan status until (dateFrom -1) day
         * on_period_data or on_period_calculation is represent loan status during date range
         */

        // dd($loanStatuses);
        foreach ($loanStatuses as $loanId => $loanStatus) {

            $loanData = $loanStatus['loan_data'];
            $samityId = $loanData['samityId'];

            ## nicher sokol array er jonno pre value ache ja loan schedule theke astase tai assign kore nite hocche
            ## but fo_wise_data variable aikhanei generate hocche tai faka array declare kora hoyeche
            $on_periodSchedule = $loanStatus['on_period']['schedule'];
            $cumulativeCalculation = $loanStatus['cumulative']['calculation'];
            $on_periodCalculation = $loanStatus['on_period']['calculation'];
            $before_periodCalculation = $loanStatus['before_period']['calculation'];
            $fo_wise_data = array();


            $openingDue = $openingAdvance = 0;
            $openingDuePri = $openingAdvancePri = 0;
            // $openingDueSC = $openingAdvanceSC = 0;

            if ("loan_data_cal") {
                $loanStatus['loan_data'] += [
                    'collectionAmount' => 0,
                    'collectionPrinciple' => 0,
                    'collectionServiceCharge' => 0,
                    'collectionExtraInterest' => 0,
                    'outstandingAmount' => 0,
                    'outstandingPrinciple' => 0,
                    'outstandingServiceCharge' => 0
                ];

                if (isset($loanCollectionLoanIdWise[$loanId])) {
                    $loanCollection      = $loanCollectionLoanIdWise[$loanId];

                    $loanStatus['loan_data']['collectionAmount'] += $loanCollection->sum('amount');
                    $loanStatus['loan_data']['collectionPrinciple'] += $loanCollection->sum('principalAmount');
                    $loanStatus['loan_data']['collectionServiceCharge'] += $loanCollection->sum('interestAmount');
                    $loanStatus['loan_data']['collectionExtraInterest'] += $loanCollection->sum('extraInterest');

                    $loanStatus['loan_data']['outstandingAmount'] += ($loanData['repayAmount'] - $loanStatus['loan_data']['collectionAmount']);
                    $loanStatus['loan_data']['outstandingPrinciple'] += ($loanData['loanAmount'] - $loanStatus['loan_data']['collectionPrinciple']);
                    $loanStatus['loan_data']['outstandingServiceCharge'] += ($loanData['ineterestAmount'] - $loanStatus['loan_data']['collectionServiceCharge']);
                }
            }

            if ("cumulative_calculation") {
                $cumulativeCalculation += [
                    'collectionAmount' => 0,
                    'collectionPrinciple' => 0,
                    'collectionServiceCharge' => 0,
                    'collectionExtraInterest' => 0,

                    'dueAmount' => 0,
                    'duePrinciple' => 0,
                    'dueServiceCharge' => 0,

                    'advanceAmount' => 0,
                    'advancePrinciple' => 0,
                    'advanceServiceCharge' => 0,

                    'outstandingAmount' => 0,
                    'outstandingPrinciple' => 0,
                    'outstandingServiceCharge' => 0
                ];

                if (isset($loanCollectionCumLoanIdWise[$loanId])) {
                    $loanCollectionCumulative      = $loanCollectionCumLoanIdWise[$loanId];

                    $cumulativeCalculation['collectionAmount'] += $loanCollectionCumulative->sum('amount');
                    $cumulativeCalculation['collectionPrinciple'] += $loanCollectionCumulative->sum('principalAmount');
                    $cumulativeCalculation['collectionServiceCharge'] += $loanCollectionCumulative->sum('interestAmount');
                    $cumulativeCalculation['collectionExtraInterest'] += $loanCollectionCumulative->sum('extraInterest');

                    $tempAmount = ($cumulativeCalculation['payableAmount'] - $cumulativeCalculation['collectionAmount']);
                    $tempPri = ($cumulativeCalculation['payablePrincipal'] - $cumulativeCalculation['collectionPrinciple']);
                    $tempSC = ($cumulativeCalculation['payableServiceCharge'] - $cumulativeCalculation['collectionServiceCharge']);

                    if ($tempAmount > 0) { # due
                        $cumulativeCalculation['dueAmount'] += $tempAmount;
                        array_push($dueMembers, $loanData['memberId']);
                    } elseif ($tempAmount < 0) { # advance
                        $cumulativeCalculation['advanceAmount'] += abs($tempAmount);
                    }

                    if ($tempPri > 0) { # due
                        $cumulativeCalculation['duePrinciple'] += $tempPri;
                    } elseif ($tempPri < 0) { # advance
                        $cumulativeCalculation['advancePrinciple'] += abs($tempPri);
                    }

                    if ($tempSC > 0) { # due
                        $cumulativeCalculation['dueServiceCharge'] += $tempSC;
                    } elseif ($tempSC < 0) { # advance
                        $cumulativeCalculation['advanceServiceCharge'] += abs($tempSC);
                    }

                    $cumulativeCalculation['outstandingAmount'] += ($loanData['repayAmount'] - $cumulativeCalculation['collectionAmount']);
                    $cumulativeCalculation['outstandingPrinciple'] += ($loanData['loanAmount'] - $cumulativeCalculation['collectionPrinciple']);
                    $cumulativeCalculation['outstandingServiceCharge'] += ($loanData['ineterestAmount'] - $cumulativeCalculation['collectionServiceCharge']);
                }
                else ## Modify 30-09-23 for if any loanId don't have collection then show outstanding 0, outstandingPrinciple 0, outstandingServiceCharge = 0;
                {
                    $cumulativeCalculation['outstandingAmount'] = $loanData['repayAmount'];
                    $cumulativeCalculation['outstandingPrinciple'] = $loanData['loanAmount'];
                    $cumulativeCalculation['outstandingServiceCharge'] = $loanData['ineterestAmount'];
                }

                ## payment Wise data set
                if (isset($loanCollectionCumLoanIdWisePay[$loanId])) {
                    // 'Cash','Bank','Rebate','Waiver','WriteOff','OB','Adjustment'
                    $lcCumPayType      = $loanCollectionCumLoanIdWisePay[$loanId];

                    if (isset($lcCumPayType['Cash'])) {
                        $cumulativeCalculation['cashCollection'] = $lcCumPayType['Cash']->sum('amount');
                        $cumulativeCalculation['cashPrinciple'] = $lcCumPayType['Cash']->sum('principalAmount');
                        $cumulativeCalculation['cashServiceCharge'] = $lcCumPayType['Cash']->sum('interestAmount');
                        $cumulativeCalculation['cashExtraInterest'] = $lcCumPayType['Cash']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['Bank'])) {
                        $cumulativeCalculation['bankCollection'] = $lcCumPayType['Bank']->sum('amount');
                        $cumulativeCalculation['bankPrinciple'] = $lcCumPayType['Bank']->sum('principalAmount');
                        $cumulativeCalculation['bankServiceCharge'] = $lcCumPayType['Bank']->sum('interestAmount');
                        $cumulativeCalculation['bankExtraInterest'] = $lcCumPayType['Bank']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['Rebate'])) {
                        $cumulativeCalculation['rebateCollection'] = $lcCumPayType['Rebate']->sum('amount');
                        $cumulativeCalculation['rebatePrinciple'] = $lcCumPayType['Rebate']->sum('principalAmount');
                        $cumulativeCalculation['rebateServiceCharge'] = $lcCumPayType['Rebate']->sum('interestAmount');
                        $cumulativeCalculation['rebateExtraInterest'] = $lcCumPayType['Rebate']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['Waiver'])) {
                        $cumulativeCalculation['waiverCollection'] = $lcCumPayType['Waiver']->sum('amount');
                        $cumulativeCalculation['waiverPrinciple'] = $lcCumPayType['Waiver']->sum('principalAmount');
                        $cumulativeCalculation['waiverServiceCharge'] = $lcCumPayType['Waiver']->sum('interestAmount');
                        $cumulativeCalculation['waiverExtraInterest'] = $lcCumPayType['Waiver']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['WriteOff'])) {
                        $cumulativeCalculation['writeOffCollection'] = $lcCumPayType['WriteOff']->sum('amount');
                        $cumulativeCalculation['writeOffPrinciple'] = $lcCumPayType['WriteOff']->sum('principalAmount');
                        $cumulativeCalculation['writeOffServiceCharge'] = $lcCumPayType['WriteOff']->sum('interestAmount');
                        $cumulativeCalculation['writeOffExtraInterest'] = $lcCumPayType['WriteOff']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['OB'])) {
                        $cumulativeCalculation['obCollection'] = $lcCumPayType['OB']->sum('amount');
                        $cumulativeCalculation['obPrinciple'] = $lcCumPayType['OB']->sum('principalAmount');
                        $cumulativeCalculation['obServiceCharge'] = $lcCumPayType['OB']->sum('interestAmount');
                        $cumulativeCalculation['obExtraInterest'] = $lcCumPayType['OB']->sum('extraInterest');
                    }

                    if (isset($lcCumPayType['Adjustment'])) {
                        $cumulativeCalculation['adjustmentCollection'] = $lcCumPayType['Adjustment']->sum('amount');
                        $cumulativeCalculation['adjustmentPrinciple'] = $lcCumPayType['Adjustment']->sum('principalAmount');
                        $cumulativeCalculation['adjustmentServiceCharge'] = $lcCumPayType['Adjustment']->sum('interestAmount');
                        $cumulativeCalculation['adjustmentExtraInterest'] = $lcCumPayType['Adjustment']->sum('extraInterest');
                    }
                }
            }

            if ("before_period_calculation") {
                $before_periodCalculation += [
                    'collectionAmount' => 0,
                    'collectionPrinciple' => 0,
                    'collectionServiceCharge' => 0,
                    'collectionExtraInterest' => 0,

                    'dueAmount' => 0,
                    'duePrinciple' => 0,
                    'dueServiceCharge' => 0,

                    'advanceAmount' => 0,
                    'advancePrinciple' => 0,
                    'advanceServiceCharge' => 0,

                    'outstandingAmount' => 0,
                    'outstandingPrinciple' => 0,
                    'outstandingServiceCharge' => 0
                ];

                if (isset($loanCollectionBeforePeriodIdWise[$loanId])) {
                    $loanCollectionBefore      = $loanCollectionBeforePeriodIdWise[$loanId];

                    $before_periodCalculation['collectionAmount'] += $loanCollectionBefore->sum('amount');
                    $before_periodCalculation['collectionPrinciple'] += $loanCollectionBefore->sum('principalAmount');
                    $before_periodCalculation['collectionServiceCharge'] += $loanCollectionBefore->sum('interestAmount');
                    $before_periodCalculation['collectionExtraInterest'] += $loanCollectionBefore->sum('extraInterest');

                    $tempAmount = ($before_periodCalculation['payableAmount'] - $before_periodCalculation['collectionAmount']);
                    $tempPri = ($before_periodCalculation['payablePrincipal'] - $before_periodCalculation['collectionPrinciple']);
                    $tempSC = ($before_periodCalculation['payableServiceCharge'] - $before_periodCalculation['collectionServiceCharge']);

                    if ($tempAmount > 0) { # due
                        $before_periodCalculation['dueAmount'] += $tempAmount;
                    } elseif ($tempAmount < 0) { # advance
                        $before_periodCalculation['advanceAmount'] += abs($tempAmount);
                    }

                    if ($tempPri > 0) { # due
                        $before_periodCalculation['duePrinciple'] += $tempPri;
                    } elseif ($tempPri < 0) { # advance
                        $before_periodCalculation['advancePrinciple'] += abs($tempPri);
                    }

                    if ($tempSC > 0) { # due
                        $before_periodCalculation['dueServiceCharge'] += $tempSC;
                    } elseif ($tempSC < 0) { # advance
                        $before_periodCalculation['advanceServiceCharge'] += abs($tempSC);
                    }

                    $before_periodCalculation['outstandingAmount'] += ($loanData['repayAmount'] - $before_periodCalculation['collectionAmount']);
                    $before_periodCalculation['outstandingPrinciple'] += ($loanData['loanAmount'] - $before_periodCalculation['collectionPrinciple']);
                    $before_periodCalculation['outstandingServiceCharge'] += ($loanData['ineterestAmount'] - $before_periodCalculation['collectionServiceCharge']);

                    $openingDue = $before_periodCalculation['dueAmount'];
                    $openingDuePri = $before_periodCalculation['duePrinciple'];
                    // $openingDueSC = $before_periodCalculation['dueServiceCharge'];
                    $openingAdvance = $before_periodCalculation['advanceAmount'];
                    $openingAdvancePri = $before_periodCalculation['advancePrinciple'];
                    // $openingAdvanceSC = $before_periodCalculation['advanceServiceCharge'];
                }
                else ## Modify 30-09-23 for if any loanId don't have collection then show outstanding 0, outstandingPrinciple 0, outstandingServiceCharge = 0;
                {
                    $before_periodCalculation['outstandingAmount'] = $loanData['repayAmount'];
                    $before_periodCalculation['outstandingPrinciple'] = $loanData['loanAmount'];
                    $before_periodCalculation['outstandingServiceCharge'] = $loanData['ineterestAmount'];
                }

                ## payment Wise data set
                if (isset($loanCollectionBeforePeriodIdPaymentWise[$loanId])) {
                    // 'Cash','Bank','Rebate','Waiver','WriteOff','OB','Adjustment'
                    $lcBPPayType      = $loanCollectionBeforePeriodIdPaymentWise[$loanId];

                    if (isset($lcBPPayType['Cash'])) {
                        $before_periodCalculation['cashCollection'] = $lcBPPayType['Cash']->sum('amount');
                        $before_periodCalculation['cashPrinciple'] = $lcBPPayType['Cash']->sum('principalAmount');
                        $before_periodCalculation['cashServiceCharge'] = $lcBPPayType['Cash']->sum('interestAmount');
                        $before_periodCalculation['cashExtraInterest'] = $lcBPPayType['Cash']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['Bank'])) {
                        $before_periodCalculation['bankCollection'] = $lcBPPayType['Bank']->sum('amount');
                        $before_periodCalculation['bankPrinciple'] = $lcBPPayType['Bank']->sum('principalAmount');
                        $before_periodCalculation['bankServiceCharge'] = $lcBPPayType['Bank']->sum('interestAmount');
                        $before_periodCalculation['bankExtraInterest'] = $lcBPPayType['Bank']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['Rebate'])) {
                        $before_periodCalculation['rebateCollection'] = $lcBPPayType['Rebate']->sum('amount');
                        $before_periodCalculation['rebatePrinciple'] = $lcBPPayType['Rebate']->sum('principalAmount');
                        $before_periodCalculation['rebateServiceCharge'] = $lcBPPayType['Rebate']->sum('interestAmount');
                        $before_periodCalculation['rebateExtraInterest'] = $lcBPPayType['Rebate']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['Waiver'])) {
                        $before_periodCalculation['waiverCollection'] = $lcBPPayType['Waiver']->sum('amount');
                        $before_periodCalculation['waiverPrinciple'] = $lcBPPayType['Waiver']->sum('principalAmount');
                        $before_periodCalculation['waiverServiceCharge'] = $lcBPPayType['Waiver']->sum('interestAmount');
                        $before_periodCalculation['waiverExtraInterest'] = $lcBPPayType['Waiver']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['WriteOff'])) {
                        $before_periodCalculation['writeOffCollection'] = $lcBPPayType['WriteOff']->sum('amount');
                        $before_periodCalculation['writeOffPrinciple'] = $lcBPPayType['WriteOff']->sum('principalAmount');
                        $before_periodCalculation['writeOffServiceCharge'] = $lcBPPayType['WriteOff']->sum('interestAmount');
                        $before_periodCalculation['writeOffExtraInterest'] = $lcBPPayType['WriteOff']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['OB'])) {
                        $before_periodCalculation['obCollection'] = $lcBPPayType['OB']->sum('amount');
                        $before_periodCalculation['obPrinciple'] = $lcBPPayType['OB']->sum('principalAmount');
                        $before_periodCalculation['obServiceCharge'] = $lcBPPayType['OB']->sum('interestAmount');
                        $before_periodCalculation['obExtraInterest'] = $lcBPPayType['OB']->sum('extraInterest');
                    }

                    if (isset($lcBPPayType['Adjustment'])) {
                        $before_periodCalculation['adjustmentCollection'] = $lcBPPayType['Adjustment']->sum('amount');
                        $before_periodCalculation['adjustmentPrinciple'] = $lcBPPayType['Adjustment']->sum('principalAmount');
                        $before_periodCalculation['adjustmentServiceCharge'] = $lcBPPayType['Adjustment']->sum('interestAmount');
                        $before_periodCalculation['adjustmentExtraInterest'] = $lcBPPayType['Adjustment']->sum('extraInterest');
                    }
                }
            }

            /**
             * On period collection a must due thakle seta age deduct hobe then regular installment then advance amount
             */
            if ("on_period_calculation") {
                $on_periodCalculation += [
                    'collectionAmount' => 0,
                    'collectionPrinciple' => 0,
                    'collectionServiceCharge' => 0,
                    'collectionExtraInterest' => 0,

                    'dueAmount' => 0,
                    'duePrinciple' => 0,
                    'dueServiceCharge' => 0,

                    'advanceAmount' => 0,
                    'advancePrinciple' => 0,
                    'advanceServiceCharge' => 0,

                    'regularCollection' => 0,
                    'regularCollectionPrinciple' => 0,
                    'regularCollectionServiceCharge' => 0,

                    'dueCollection' => 0,
                    'dueCollectionPrinciple' => 0,
                    'dueCollectionServiceCharge' => 0,

                    'advanceCollection' => 0,
                    'advanceCollectionPrinciple' => 0,
                    'advanceCollectionServiceCharge' => 0,
                ];

                if (isset($loanCollectionOnPeriodIdWise[$loanId])) {
                    $loanCollectionPeriod      = $loanCollectionOnPeriodIdWise[$loanId];

                    $on_periodCalculation['collectionAmount'] += $loanCollectionPeriod->sum('amount');
                    $on_periodCalculation['collectionPrinciple'] += $loanCollectionPeriod->sum('principalAmount');
                    $on_periodCalculation['collectionServiceCharge'] += $loanCollectionPeriod->sum('interestAmount');
                    $on_periodCalculation['collectionExtraInterest'] += $loanCollectionPeriod->sum('extraInterest');

                    $tempAmount = (($on_periodCalculation['payableAmount'] + $before_periodCalculation['dueAmount']) - ($on_periodCalculation['collectionAmount'] + $before_periodCalculation['advanceAmount']));
                    $tempPri = (($on_periodCalculation['payablePrincipal'] + $before_periodCalculation['duePrinciple']) - ($on_periodCalculation['collectionPrinciple'] + $before_periodCalculation['advancePrinciple']));
                    $tempSC = ($tempAmount - $tempPri);

                    if ($tempAmount > 0) { # due
                        $on_periodCalculation['dueAmount'] += $tempAmount;
                    } elseif ($tempAmount < 0) { # advance
                        $on_periodCalculation['advanceAmount'] += abs($tempAmount);
                    }

                    if ($tempPri > 0) { # due
                        $on_periodCalculation['duePrinciple'] += $tempPri;
                    } elseif ($tempPri < 0) { # advance
                        $on_periodCalculation['advancePrinciple'] += abs($tempPri);
                    }

                    if ($tempSC > 0) { # due
                        $on_periodCalculation['dueServiceCharge'] += $tempSC;
                    } elseif ($tempSC < 0) { # advance
                        $on_periodCalculation['advanceServiceCharge'] += abs($tempSC);
                    }
                }

                ## payment Wise data set
                if (isset($loanCollectionOnPeriodIdPaymentWise[$loanId])) {
                    // 'Cash','Bank','Rebate','Waiver','WriteOff','OB','Adjustment'
                    $lcOPPayType      = $loanCollectionOnPeriodIdPaymentWise[$loanId];

                    if (isset($lcOPPayType['Cash'])) {
                        $on_periodCalculation['cashCollection'] = $lcOPPayType['Cash']->sum('amount');
                        $on_periodCalculation['cashPrinciple'] = $lcOPPayType['Cash']->sum('principalAmount');
                        $on_periodCalculation['cashServiceCharge'] = $lcOPPayType['Cash']->sum('interestAmount');
                        $on_periodCalculation['cashExtraInterest'] = $lcOPPayType['Cash']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['Bank'])) {
                        $on_periodCalculation['bankCollection'] = $lcOPPayType['Bank']->sum('amount');
                        $on_periodCalculation['bankPrinciple'] = $lcOPPayType['Bank']->sum('principalAmount');
                        $on_periodCalculation['bankServiceCharge'] = $lcOPPayType['Bank']->sum('interestAmount');
                        $on_periodCalculation['bankExtraInterest'] = $lcOPPayType['Bank']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['Rebate'])) {
                        $on_periodCalculation['rebateCollection'] = $lcOPPayType['Rebate']->sum('amount');
                        $on_periodCalculation['rebatePrinciple'] = $lcOPPayType['Rebate']->sum('principalAmount');
                        $on_periodCalculation['rebateServiceCharge'] = $lcOPPayType['Rebate']->sum('interestAmount');
                        $on_periodCalculation['rebateExtraInterest'] = $lcOPPayType['Rebate']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['Waiver'])) {
                        $on_periodCalculation['waiverCollection'] = $lcOPPayType['Waiver']->sum('amount');
                        $on_periodCalculation['waiverPrinciple'] = $lcOPPayType['Waiver']->sum('principalAmount');
                        $on_periodCalculation['waiverServiceCharge'] = $lcOPPayType['Waiver']->sum('interestAmount');
                        $on_periodCalculation['waiverExtraInterest'] = $lcOPPayType['Waiver']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['WriteOff'])) {
                        $on_periodCalculation['writeOffCollection'] = $lcOPPayType['WriteOff']->sum('amount');
                        $on_periodCalculation['writeOffPrinciple'] = $lcOPPayType['WriteOff']->sum('principalAmount');
                        $on_periodCalculation['writeOffServiceCharge'] = $lcOPPayType['WriteOff']->sum('interestAmount');
                        $on_periodCalculation['writeOffExtraInterest'] = $lcOPPayType['WriteOff']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['OB'])) {
                        $on_periodCalculation['obCollection'] = $lcOPPayType['OB']->sum('amount');
                        $on_periodCalculation['obPrinciple'] = $lcOPPayType['OB']->sum('principalAmount');
                        $on_periodCalculation['obServiceCharge'] = $lcOPPayType['OB']->sum('interestAmount');
                        $on_periodCalculation['obExtraInterest'] = $lcOPPayType['OB']->sum('extraInterest');
                    }

                    if (isset($lcOPPayType['Adjustment'])) {
                        $on_periodCalculation['adjustmentCollection'] = $lcOPPayType['Adjustment']->sum('amount');
                        $on_periodCalculation['adjustmentPrinciple'] = $lcOPPayType['Adjustment']->sum('principalAmount');
                        $on_periodCalculation['adjustmentServiceCharge'] = $lcOPPayType['Adjustment']->sum('interestAmount');
                        $on_periodCalculation['adjustmentExtraInterest'] = $lcOPPayType['Adjustment']->sum('extraInterest');
                    }
                }

                if ("all_collection_period") {
                    // $all_collection = $openingAdvance + $on_periodCalculation['collectionAmount'];
                    $all_collection = $on_periodCalculation['collectionAmount'];
                    if ($openingDue > 0) {
                        if (($openingDue - $all_collection) <= 0) {
                            $on_periodCalculation['dueCollection'] = $openingDue;
                        } else {
                            $on_periodCalculation['dueCollection'] = $all_collection;
                        }
                    }

                    $remain_collection = ($all_collection - $on_periodCalculation['dueCollection']);

                    if ($remain_collection > 0) {
                        if (($on_periodCalculation['payableAmount'] - $remain_collection) <= 0) {

                            $on_periodCalculation['regularCollection'] = $on_periodCalculation['payableAmount'];
                        } else {
                            $on_periodCalculation['regularCollection'] = $remain_collection;
                        }
                    }

                    $for_advance_collection = ($remain_collection - $on_periodCalculation['regularCollection']);

                    if ($for_advance_collection > 0) {
                        $on_periodCalculation['advanceCollection'] = $for_advance_collection;
                    }
                }

                if ("principle_collection_period") {
                    // $all_collectionPri = $openingAdvancePri + $on_periodCalculation['collectionPrinciple'];
                    $all_collectionPri = $on_periodCalculation['collectionPrinciple'];
                    if ($openingDuePri > 0) {
                        if (($openingDuePri - $all_collectionPri) <= 0) {
                            $on_periodCalculation['dueCollectionPrinciple'] = $openingDuePri;
                        } else {
                            $on_periodCalculation['dueCollectionPrinciple'] = $all_collectionPri;
                        }
                    }

                    $remain_collectionPri = ($all_collectionPri - $on_periodCalculation['dueCollectionPrinciple']);

                    if ($remain_collectionPri > 0) {
                        if (($on_periodCalculation['payablePrincipal'] - $remain_collectionPri) <= 0) {

                            $on_periodCalculation['regularCollectionPrinciple'] = $on_periodCalculation['payablePrincipal'];
                        } else {
                            $on_periodCalculation['regularCollectionPrinciple'] = $remain_collectionPri;
                        }
                    }

                    $for_advance_collectionPri = ($remain_collectionPri - $on_periodCalculation['regularCollectionPrinciple']);

                    if ($for_advance_collectionPri > 0) {
                        $on_periodCalculation['advanceCollectionPrinciple'] = $for_advance_collectionPri;
                    }
                }

                $on_periodCalculation['dueCollectionServiceCharge'] = ($on_periodCalculation['dueCollection'] - $on_periodCalculation['dueCollectionPrinciple']);
                $on_periodCalculation['regularCollectionServiceCharge'] = ($on_periodCalculation['regularCollection'] - $on_periodCalculation['regularCollectionPrinciple']);
                $on_periodCalculation['advanceCollectionServiceCharge'] = ($on_periodCalculation['advanceCollection'] - $on_periodCalculation['advanceCollectionPrinciple']);

                // // classify regular, due, advance collection
                // $remainingCollectionAmount                      = $onPeriodPaidAmount;
                // $loanStatuses[$key]['onPeriodReularCollection'] = min($onPeriodPayable, $remainingCollectionAmount);
                // $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollection'];
                // $loanStatuses[$key]['onPeriodDueCollection'] = min($remainingCollectionAmount, $beginigDueAmount);
                // $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollection'];
                // $loanStatuses[$key]['onPeriodAdvanceCollection'] = $remainingCollectionAmount;


                $adjustableRecoverable = ($before_periodCalculation['dueAmount'] - $before_periodCalculation['advanceAmount']) + $on_periodCalculation['payableAmount'];

                $on_periodCalculation['numberOfFullyPaidRegularCollection'] = 0;
                if ($adjustableRecoverable <= $on_periodCalculation['collectionAmount']) {
                    $on_periodCalculation['numberOfFullyPaidRegularCollection'] = 1;
                }

                if (
                    $on_periodCalculation['isLastInstallmentPresent']
                    && (($adjustableRecoverable <= $on_periodCalculation['collectionAmount']) >= $loanData['lastInstallmentAmount'])
                ) {
                    $on_periodCalculation['numberOfFullyPaidRegularCollection']++;
                }

                // $on_periodCalculation['numberOfFullyPaidRegularCollection'] = (int) ($on_periodCalculation['collectionAmount'] / $adjustableRecoverable);

                // if (
                //     $on_periodCalculation['isLastInstallmentPresent']
                //     && (($on_periodCalculation['collectionAmount'] % $adjustableRecoverable) >= $loanData['lastInstallmentAmount'])
                // ) {
                //     $on_periodCalculation['numberOfFullyPaidRegularCollection']++;
                // }
            }

            ## feild officer wise data make
            if ("fo_wise_data_make") {
                // ## $samityFosForOp = samity feild officer for on period
                $samityFosForOp = (isset($samityData[$samityId]->fieldOfficers)) ? $samityData[$samityId]->fieldOfficers : array();

                foreach ($samityFosForOp as $key => $foData) {

                    $foid = $foData['fieldOfficerId'];

                    $openingDueFo = $openingAdvanceFo = 0;
                    $openingDuePriFo = $openingAdvancePriFo = 0;

                    $foBeforArr = [
                        'installment_counted' => 0,

                        'payableAmount' => 0,
                        'payablePrincipal' => 0,
                        'payableServiceCharge' => 0,

                        'collectionAmount' => 0,
                        'collectionPrinciple' => 0,
                        'collectionServiceCharge' => 0,
                        'collectionExtraInterest' => 0,

                        'dueAmount' => 0,
                        'duePrinciple' => 0,
                        'dueServiceCharge' => 0,

                        'advanceAmount' => 0,
                        'advancePrinciple' => 0,
                        'advanceServiceCharge' => 0,

                        'outstandingAmount' => 0,
                        'outstandingPrinciple' => 0,
                        'outstandingServiceCharge' => 0
                    ];

                    $foTepArr = [
                        'installment_counted' => 0,
                        // 'isLastInstallmentPresent' => false,

                        'payableAmount' => 0,
                        'payablePrincipal' => 0,
                        'payableServiceCharge' => 0,

                        'collectionAmount' => 0,
                        'collectionPrinciple' => 0,
                        'collectionServiceCharge' => 0,
                        'collectionExtraInterest' => 0,

                        'dueAmount' => 0,
                        'duePrinciple' => 0,
                        'dueServiceCharge' => 0,

                        'advanceAmount' => 0,
                        'advancePrinciple' => 0,
                        'advanceServiceCharge' => 0,

                        'regularCollection' => 0,
                        'regularCollectionPrinciple' => 0,
                        'regularCollectionServiceCharge' => 0,

                        'dueCollection' => 0,
                        'dueCollectionPrinciple' => 0,
                        'dueCollectionServiceCharge' => 0,

                        'advanceCollection' => 0,
                        'advanceCollectionPrinciple' => 0,
                        'advanceCollectionServiceCharge' => 0,

                        'numberOfFullyPaidRegularCollection' => 0,
                    ];

                    /**
                     * kono installment date jodi kono fo er majhe na pore tahole array er last fo er data te bosano hocche.
                     * jhamela face korbo jokhon collecction korte jabo. karon
                     * ekta loan shuru hoise array er 1st fo er datefrom er age sei sob date gulo porbe array er last er fo er majhe but
                     * collection dekhanor somoy o last fo er majhe obossoi oi date gulor collection last fo te dekhate hobe, array er onno
                     * fo der majhe dekhano jabe na. tokhono collection query te bosate hobe not between fo's datefrom and dateto
                     */

                    ## before period
                    if ($key == 0) {
                        $openingDueFo = $openingDue;
                        $openingDuePriFo = $openingDuePri;

                        $openingAdvanceFo = $openingAdvance;
                        $openingAdvancePriFo = $openingAdvancePri;

                        $foBeforArr = $before_periodCalculation;
                    } else {
                        ## from date theke 1st fo er end date porjonto due ber kore ane aikhane jog korte hobe
                        ## code remaining

                        ## previous fo er on period data current fo er before data, and previous fo er before data aikhane show hobe kina pore dekhbo
                        $pre_fo_op_data = $fo_wise_data[$samityFosForOp[$key - 1]['fieldOfficerId']]['on_period']['calculation'];

                        $openingDueFo = $pre_fo_op_data['dueAmount'];
                        $openingDuePriFo = $pre_fo_op_data['duePrinciple'];

                        $openingAdvanceFo = $pre_fo_op_data['advanceAmount'];
                        $openingAdvancePriFo = $pre_fo_op_data['advancePrinciple'];

                        // if ($loanId == 18672) {
                        //     dd($fo_wise_data[$samityFosForOp[$key - 1]['fieldOfficerId']]['on_period']['calculation']);
                        // }

                        $foBeforArr = [
                            'installment_counted' => $pre_fo_op_data['installment_counted'],

                            'payableAmount' => $pre_fo_op_data['payableAmount'],
                            'payablePrincipal' => $pre_fo_op_data['payablePrincipal'],
                            'payableServiceCharge' => $pre_fo_op_data['payableServiceCharge'],

                            'collectionAmount' => $pre_fo_op_data['collectionAmount'],
                            'collectionPrinciple' => $pre_fo_op_data['collectionPrinciple'],
                            'collectionServiceCharge' => $pre_fo_op_data['collectionServiceCharge'],
                            'collectionExtraInterest' => $pre_fo_op_data['collectionExtraInterest'],

                            'dueAmount' => $pre_fo_op_data['dueAmount'],
                            'duePrinciple' => $pre_fo_op_data['duePrinciple'],
                            'dueServiceCharge' => $pre_fo_op_data['dueServiceCharge'],

                            'advanceAmount' => $pre_fo_op_data['advanceAmount'],
                            'advancePrinciple' => $pre_fo_op_data['advancePrinciple'],
                            'advanceServiceCharge' => $pre_fo_op_data['advanceServiceCharge'],

                            'outstandingAmount' => ($before_periodCalculation['outstandingAmount'] + $pre_fo_op_data['dueAmount'] - $pre_fo_op_data['advanceAmount']),
                            'outstandingPrinciple' => ($before_periodCalculation['outstandingPrinciple'] + $pre_fo_op_data['duePrinciple'] - $pre_fo_op_data['advancePrinciple']),
                            'outstandingServiceCharge' => ($before_periodCalculation['outstandingServiceCharge'] + $pre_fo_op_data['dueServiceCharge'] - $pre_fo_op_data['advanceServiceCharge'])
                        ];
                    }

                    ## on period recoverable or payeable amount field officer wise
                    foreach ($on_periodSchedule as $opSchedule) {

                        if (($opSchedule['installmentDate'] >= $foData['dateFrom'] && $opSchedule['installmentDate'] <= $foData['dateTo'])) {

                            $foTepArr['installment_counted'] += 1;

                            $foTepArr['payableAmount'] += $opSchedule['installmentAmount'];
                            $foTepArr['payablePrincipal'] += $opSchedule['installmentAmountPrincipal'];
                            $foTepArr['payableServiceCharge'] += $opSchedule['installmentAmountInterest'];
                        }
                    }

                    ## on period collection and others amount field officer wise
                    if (isset($loanCollectionOnPeriodIdWise[$loanId])) {
                        $lColOpFo      = $loanCollectionOnPeriodIdWise[$loanId];

                        $colAT = $lColOpFo->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                        $colPriAT = $lColOpFo->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                        $colSCT = $lColOpFo->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                        $colExI = $lColOpFo->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');

                        $foTepArr['collectionAmount'] += $colAT;
                        $foTepArr['collectionPrinciple'] += $colPriAT;
                        $foTepArr['collectionServiceCharge'] += $colSCT;
                        $foTepArr['collectionExtraInterest'] += $colExI;

                        $tempAmount = (($foTepArr['payableAmount'] + $foBeforArr['dueAmount']) - ($colAT + $foBeforArr['advanceAmount']));
                        $tempPri = (($foTepArr['payablePrincipal'] + $foBeforArr['duePrinciple']) - ($colPriAT + $foBeforArr['advancePrinciple']));
                        $tempSC = ($tempAmount - $tempPri);

                        if ($tempAmount > 0) { # due
                            $foTepArr['dueAmount'] += $tempAmount;
                        } elseif ($tempAmount < 0) { # advance
                            $foTepArr['advanceAmount'] += abs($tempAmount);
                        }

                        if ($tempPri > 0) { # due
                            $foTepArr['duePrinciple'] += $tempPri;
                        } elseif ($tempPri < 0) { # advance
                            $foTepArr['advancePrinciple'] += abs($tempPri);
                        }

                        if ($tempSC > 0) { # due
                            $foTepArr['dueServiceCharge'] += $tempSC;
                        } elseif ($tempSC < 0) { # advance
                            $foTepArr['advanceServiceCharge'] += abs($tempSC);
                        }
                    }

                    ## payment Wise data set
                    if (isset($loanCollectionOnPeriodIdPaymentWise[$loanId])) {
                        // 'Cash','Bank','Rebate','Waiver','WriteOff','OB','Adjustment'
                        $lColOpPayFo      = $loanCollectionOnPeriodIdPaymentWise[$loanId];

                        if (isset($lColOpPayFo['Cash'])) {
                            $foTepArr['cashCollection'] = $lColOpPayFo['Cash']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['cashPrinciple'] = $lColOpPayFo['Cash']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['cashServiceCharge'] = $lColOpPayFo['Cash']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['cashExtraInterest'] = $lColOpPayFo['Cash']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['Bank'])) {
                            $foTepArr['bankCollection'] = $lColOpPayFo['Bank']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['bankPrinciple'] = $lColOpPayFo['Bank']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['bankServiceCharge'] = $lColOpPayFo['Bank']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['bankExtraInterest'] = $lColOpPayFo['Bank']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['Rebate'])) {
                            $foTepArr['rebateCollection'] = $lColOpPayFo['Rebate']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['rebatePrinciple'] = $lColOpPayFo['Rebate']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['rebateServiceCharge'] = $lColOpPayFo['Rebate']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['rebateExtraInterest'] = $lColOpPayFo['Rebate']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['Waiver'])) {
                            $foTepArr['waiverCollection'] = $lColOpPayFo['Waiver']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['waiverPrinciple'] = $lColOpPayFo['Waiver']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['waiverServiceCharge'] = $lColOpPayFo['Waiver']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['waiverExtraInterest'] = $lColOpPayFo['Waiver']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['WriteOff'])) {
                            $foTepArr['writeOffCollection'] = $lColOpPayFo['WriteOff']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['writeOffPrinciple'] = $lColOpPayFo['WriteOff']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['writeOffServiceCharge'] = $lColOpPayFo['WriteOff']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['writeOffExtraInterest'] = $lColOpPayFo['WriteOff']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['OB'])) {
                            $foTepArr['obCollection'] = $lColOpPayFo['OB']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['obPrinciple'] = $lColOpPayFo['OB']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['obServiceCharge'] = $lColOpPayFo['OB']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['obExtraInterest'] = $lColOpPayFo['OB']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }

                        if (isset($lColOpPayFo['Adjustment'])) {
                            $foTepArr['adjustmentCollection'] = $lColOpPayFo['Adjustment']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('amount');
                            $foTepArr['adjustmentPrinciple'] = $lColOpPayFo['Adjustment']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('principalAmount');
                            $foTepArr['adjustmentServiceCharge'] = $lColOpPayFo['Adjustment']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('interestAmount');
                            $foTepArr['adjustmentExtraInterest'] = $lColOpPayFo['Adjustment']->whereBetween('collectionDate', [$foData['dateFrom'], $foData['dateTo']])->sum('extraInterest');
                        }
                    }

                    if ("all_collection_period") {
                        // $all_collection = $openingAdvanceFo + $foTepArr['collectionAmount'];
                        $all_collection = $foTepArr['collectionAmount'];
                        if ($openingDueFo > 0) {
                            if (($openingDueFo - $all_collection) <= 0) {
                                $foTepArr['dueCollection'] = $openingDueFo;
                            } else {
                                $foTepArr['dueCollection'] = $all_collection;
                            }
                        }

                        $remain_collection = ($all_collection - $foTepArr['dueCollection']);

                        if ($remain_collection > 0) {
                            if (($foTepArr['payableAmount'] - $remain_collection) <= 0) {

                                $foTepArr['regularCollection'] = $foTepArr['payableAmount'];
                            } else {
                                $foTepArr['regularCollection'] = $remain_collection;
                            }
                        }

                        $for_advance_collection = ($remain_collection - $foTepArr['regularCollection']);

                        if ($for_advance_collection > 0) {
                            $foTepArr['advanceCollection'] = $for_advance_collection;
                        }
                    }

                    if ("principle_collection_period") {
                        // $all_collectionPri = $openingAdvancePriFo + $foTepArr['collectionPrinciple'];
                        $all_collectionPri = $foTepArr['collectionPrinciple'];
                        if ($openingDuePriFo > 0) {
                            if (($openingDuePriFo - $all_collectionPri) <= 0) {
                                $foTepArr['dueCollectionPrinciple'] = $openingDuePriFo;
                            } else {
                                $foTepArr['dueCollectionPrinciple'] = $all_collectionPri;
                            }
                        }

                        $remain_collectionPri = ($all_collectionPri - $foTepArr['dueCollectionPrinciple']);

                        if ($remain_collectionPri > 0) {
                            if (($foTepArr['payablePrincipal'] - $remain_collectionPri) <= 0) {

                                $foTepArr['regularCollectionPrinciple'] = $foTepArr['payablePrincipal'];
                            } else {
                                $foTepArr['regularCollectionPrinciple'] = $remain_collectionPri;
                            }
                        }

                        $for_advance_collectionPri = ($remain_collectionPri - $foTepArr['regularCollectionPrinciple']);

                        if ($for_advance_collectionPri > 0) {
                            $foTepArr['advanceCollectionPrinciple'] = $for_advance_collectionPri;
                        }
                    }

                    $foTepArr['dueCollectionServiceCharge'] = ($foTepArr['dueCollection'] - $foTepArr['dueCollectionPrinciple']);
                    $foTepArr['regularCollectionServiceCharge'] = ($foTepArr['regularCollection'] - $foTepArr['regularCollectionPrinciple']);
                    $foTepArr['advanceCollectionServiceCharge'] = ($foTepArr['advanceCollection'] - $foTepArr['advanceCollectionPrinciple']);

                    // // // classify regular, due, advance collection
                    // // $remainingCollectionAmount                      = $onPeriodPaidAmount;
                    // // $loanStatuses[$key]['onPeriodReularCollection'] = min($onPeriodPayable, $remainingCollectionAmount);
                    // // $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollection'];
                    // // $loanStatuses[$key]['onPeriodDueCollection'] = min($remainingCollectionAmount, $beginigDueAmount);
                    // // $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollection'];
                    // // $loanStatuses[$key]['onPeriodAdvanceCollection'] = $remainingCollectionAmount;

                    $adjustableRecoverableFo = ($foBeforArr['dueAmount'] - $foBeforArr['advanceAmount']) + $foTepArr['payableAmount'];

                    $foTepArr['numberOfFullyPaidRegularCollection'] = 0;
                    if ($adjustableRecoverableFo <= $foTepArr['collectionAmount']) {
                        $foTepArr['numberOfFullyPaidRegularCollection'] = 1;
                    }

                    if (
                        $on_periodCalculation['isLastInstallmentPresent']
                        && (($adjustableRecoverableFo <= $foTepArr['collectionAmount']) >= $loanData['lastInstallmentAmount'])
                    ) {
                        $foTepArr['numberOfFullyPaidRegularCollection']++;
                    }

                    // $foTepArr['numberOfFullyPaidRegularCollection'] = (int) ($foTepArr['collectionAmount'] / $adjustableRecoverableFo);

                    // if (
                    //     $on_periodCalculation['isLastInstallmentPresent']
                    //     && (($foTepArr['collectionAmount'] % $adjustableRecoverableFo) >= $loanData['lastInstallmentAmount'])
                    // ) {
                    //     $foTepArr['numberOfFullyPaidRegularCollection']++;
                    // }

                    $fo_wise_data[$foid]['fo_data'] = $foData;
                    $fo_wise_data[$foid]['on_period']['calculation'] = $foTepArr;
                    $fo_wise_data[$foid]['before_period']['calculation'] = $foBeforArr;
                }
            }

            // if ($loanId == 18672) {
            //     dd($loanStatus, $cumulativeCalculation, $before_periodCalculation, $on_periodCalculation, $samityFosForOp);
            // }

            $loanStatuses[$loanId]['loan_data'] = $loanStatus['loan_data'];
            $loanStatuses[$loanId]['cumulative']['calculation'] = $cumulativeCalculation;
            $loanStatuses[$loanId]['on_period']['calculation'] = $on_periodCalculation;
            $loanStatuses[$loanId]['before_period']['calculation'] = $before_periodCalculation;
            $loanStatuses[$loanId]['fo_wise_data'] = $fo_wise_data;
        }


        ///////////////////////////////////////// Here, remain implement into report
        // dd($loanStatuses["18672"]['cumulative']['calculation'], $loanStatuses["18672"]['before_period']['calculation'], $loanStatuses["18672"]['on_period']['calculation'], $loanStatuses["18672"]["fo_wise_data"]);

        if ($withDueMember)
            return $dueMembers;

        return $loanStatuses;
    }

    /**
     * @param $byCurrentFieldOfficer = values("yes", "no")
     * We know samity's feild officer changable and we need to samity data view feild officer wise.
     * But sometime we don't need to divided data between feild officer. All data view in current feild officer's name.
     * Thats why we use $byCurrentFieldOfficer variable for flag.
     * $byCurrentFieldOfficer = Show report by current field officer
     *
     * payable means recoverable and cumulative recoverable until dateto
     * period payable means regular recoverable
     */
    ## This Function is called for scheduling in all reports except col sheet
    public static function generateLoanScheduleTTL($loanIdOrIds, ...$dates)
    {
        self::resetProperties();

        // if (self::$scheduleMethodForHoliday == null) {
        //     $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
        //     if ($companyConfig) {
        //         self::$scheduleMethodForHoliday = $companyConfig->form_value;
        //     }
        // }

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $schedules       = array();

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId', 'ASC')
            ->orderBy('samityId', 'ASC')
            ->orderBy('memberId', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        /**
         * Jesob loan over due hoye geche mane loan last installment date from date er age
         * tader data o generateInstallmentDatesTTL() function diye astase ja lagbe na jodi na reschedule pore.
         * amonki tader generateLoanSchedule() function thekei alada kore dewa ucit.
         * tahole load balance howar chance ache. ja pore kora hobe.
         *
         * code analysis kora bujhlam jodi loan reschedule hoy tokhon generateInstallmentDatesTTL() ai function ke
         * tana lagbe new date generate korar jonno tai apatoto over due loan gulo alada na korai valo.
         */

        /**
         * generateLoanScheduleTTL() ai function ti loan er schedule anar jonno use hocche.
         * ekta date dile Dateto hisebe count kore oidin porjonto schedule niye asche,
         * and 2 ta date dile date range er majhe schedulekore dicche
         * schedule anar khetre jesob loan er last installment date from date er age(over due) tader schedule porche na,
         * but loan status dorkar tader jonno, tai tader jonno generateInstallmentDatesTTL() ai function call kora lagbe na.
         * jesob loan er kono schedule porar kotha na mane over due list er loan guloke bad dewa jacche na karon tader loan reschedule o thakte pare,
         * tai ai function a query kore ana hocche, ai query er optimzation pore kora hobe.
         * aikhane over due bad dile obossoi loanstatus function a query kore sei data gulo ante hobe, query akhon bondho kora ache
         */

        $insallmentDatesAllLoan = self::generateInstallmentDatesTTL($loanIdOrIds);

        // dd($insallmentDatesAllLoan);

        foreach ($loans as $key => $loan) {

            $loanId = $loan->id;

            $installments = array();
            if ("generate_installment_details") {

                ## April month er por data thik extra installment amount change kora hoyechilo tai db er data e final tader jonno
                if ($loan->disbursementDate < "2022-04-01") {
                    $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);
                } else {
                    ## old data store in table no need to generate again. new generate korle old data er sathe match kore na
                    $repayAmount = round($loan->loanAmount * $loan->interestRateIndex);
                    $interestAmount        = $repayAmount - $loan->loanAmount;

                    $installmentAmountPrincipal = round($loan->installmentAmount / $loan->interestRateIndex, 5);
                    $installmentAmountInterest  = $loan->installmentAmount - $installmentAmountPrincipal;

                    $lastInstallmentPrincipal   = round($loan->loanAmount - ($installmentAmountPrincipal * ($loan->numberOfInstallment - 1)), 5);
                    $lastInstallmentInterest    = round($interestAmount - ($installmentAmountInterest * ($loan->numberOfInstallment - 1)), 5);


                    $installments['installmentAmount'] = $loan->installmentAmount;
                    $installments['actualInastallmentAmount'] = $loan->actualInstallmentAmount;
                    $installments['extraInstallmentAmount'] = $loan->extraInstallmentAmount;
                    $installments['lastInstallmentAmount'] = $loan->lastInstallmentAmount;

                    $installments['installmentAmountPrincipal'] = $installmentAmountPrincipal;
                    $installments['installmentAmountInterest'] = $installmentAmountInterest;
                    $installments['lastInstallmentPrincipal'] = $lastInstallmentPrincipal;
                    $installments['lastInstallmentInterest'] = $lastInstallmentInterest;
                }
            }

            $insallmentdates = $insallmentDatesAllLoan[$loan->id];

            $schedules[$loanId]['loanId'] = $loanId;
            $schedules[$loanId]['loan_data'] = (array) $loan;
            $schedules[$loanId]['installment_details'] = $installments;

            $schedules[$loanId]['cumulative']['schedule'] = [];
            $schedules[$loanId]['on_period']['schedule'] = [];
            $schedules[$loanId]['before_period']['schedule'] = [];

            $schedules[$loanId]['cumulative']['calculation'] = [
                'installment_counted' => 0,
                'payableAmount' => 0,
                'payablePrincipal' => 0,
                'payableServiceCharge' => 0,
            ];

            $schedules[$loanId]['on_period']['calculation'] = [
                'installment_counted' => 0,
                'payableAmount' => 0,
                'payablePrincipal' => 0,
                'payableServiceCharge' => 0,
            ];

            $schedules[$loanId]['before_period']['calculation'] = [
                'installment_counted' => 0,
                'payableAmount' => 0,
                'payablePrincipal' => 0,
                'payableServiceCharge' => 0,
            ];

            // $schedules[$loanId]['field_officer_wise_data'] = [];

            $installmentNo            = 1;
            $max_inst_date = 0;

            foreach ($insallmentdates as $insallmentdate) {

                $max_inst_date = max($max_inst_date, $insallmentdate);

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $insallmentdate;
                $schedule['weekDay']         = date('l', strtotime($insallmentdate));

                if ($installmentNo == $loan->numberOfInstallment) {
                    // if it the last installment
                    $schedule['installmentAmount']          = $installments['lastInstallmentAmount'];
                    $schedule['actualInastallmentAmount']   = 0;
                    $schedule['extraInstallmentAmount']     = 0;
                    $schedule['installmentAmountPrincipal'] = $installments['lastInstallmentPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['lastInstallmentInterest'];
                } else {
                    $schedule['installmentAmount']          = $installments['installmentAmount'];
                    $schedule['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                    $schedule['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                    $schedule['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['installmentAmountInterest'];
                }

                // if(self::$loanStatusFromDate != null){
                if ($insallmentdate >= self::$loanStatusFromDate) {
                    $schedules[$loanId]['on_period']['schedule'][] = $schedule;
                    $schedules[$loanId]['on_period']['calculation']['installment_counted'] += 1;
                    $schedules[$loanId]['on_period']['calculation']['payableAmount'] += $schedule['installmentAmount'];
                    $schedules[$loanId]['on_period']['calculation']['payablePrincipal'] += $schedule['installmentAmountPrincipal'];
                    $schedules[$loanId]['on_period']['calculation']['payableServiceCharge'] += $schedule['installmentAmountInterest'];
                } else {
                    $schedules[$loanId]['before_period']['schedule'][] = $schedule;
                    $schedules[$loanId]['before_period']['calculation']['installment_counted'] += 1;
                    $schedules[$loanId]['before_period']['calculation']['payableAmount'] += $schedule['installmentAmount'];
                    $schedules[$loanId]['before_period']['calculation']['payablePrincipal'] += $schedule['installmentAmountPrincipal'];
                    $schedules[$loanId]['before_period']['calculation']['payableServiceCharge'] += $schedule['installmentAmountInterest'];
                }
                // }

                if ("cumulative") {
                    $schedules[$loanId]['cumulative']['schedule'][] = $schedule;
                    $schedules[$loanId]['cumulative']['calculation']['installment_counted'] += 1;
                    $schedules[$loanId]['cumulative']['calculation']['payableAmount'] += $schedule['installmentAmount'];
                    $schedules[$loanId]['cumulative']['calculation']['payablePrincipal'] += $schedule['installmentAmountPrincipal'];
                    $schedules[$loanId]['cumulative']['calculation']['payableServiceCharge'] += $schedule['installmentAmountInterest'];
                }

                ## old code
                // if ($insallmentdate >= self::$loanStatusFromDate) {
                //         array_push($schedules, $schedule);
                //     }

                $installmentNo++;
            }

            $schedules[$loanId]['max_inst'] = $max_inst_date;

            $schedules[$loanId]['on_period']['calculation']['isLastInstallmentPresent'] = false;

            if (isset($insallmentdate)) {
                if (
                    self::$loanStatusFromDate != null
                    && ($insallmentdate >= self::$loanStatusFromDate)
                    && ($insallmentdate <= self::$loanStatusToDate)
                    && (--$installmentNo == $loan->numberOfInstallment)
                ) {
                    $schedules[$loanId]['on_period']['calculation']['isLastInstallmentPresent'] = true;
                } elseif (
                    self::$loanStatusFromDate == null
                    && ($insallmentdate <= self::$loanStatusToDate)
                    && (--$installmentNo == $loan->numberOfInstallment)
                ) {
                    $schedules[$loanId]['on_period']['calculation']['isLastInstallmentPresent'] = true;
                }
            }
        }

        // if (self::$requirement == 'status') {
        //     return $loanStatuses;
        // }

        return $schedules;
    }

    public static function generateLoanScheduleTTL_temp_back($loanIdOrIds, $byCurrentFieldOfficer, ...$dates)
    {
        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        /**
         * generateLoanScheduleTTL() ai function ti 2 ta kaje use kora hocche,
         * loan er status & loan er schedule anar jonno use hocche.
         * schedule anar khetre jesob loan er last installment date from date er age(over due) tader schedule porche na,
         * but loan status dorkar tader jonno, tai tader jonno generateInstallmentDatesTTL() ai function call kora lagbe na.
         */

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId')
            ->orderBy('samityId')
            ->get();

        $samityIdsFound = array();
        if (count($loans->toArray()) > 0) {
            $samityIdsFound = $loans->pluck('samityId')->toArray();
        }

        $samityData = self::getSamityInformationDataset($samityIdsFound, self::$loanStatusFromDate, self::$loanStatusToDate, $byCurrentFieldOfficer);

        /**
         * Jesob loan over due hoye geche mane loan last installment date from date er age
         * tader data o generateInstallmentDatesTTL() function diye astase ja lagbe na jodi na reschedule pore.
         * amonki tader generateLoanSchedule() function thekei alada kore dewa ucit.
         * tahole load balance howar chance ache. ja pore kora hobe.
         *
         * code analysis kora bujhlam jodi loan reschedule hoy tokhon generateInstallmentDatesTTL() ai function ke
         * tana lagbe new date generate korar jonno tai apatoto over due loan gulo alada na korai valo.
         */

        $schedules       = [];
        $loanStatuses    = [];

        $insallmentDatesAllLoan = self::generateInstallmentDatesTTL($loans);

        // dd($insallmentDatesAllLoan);

        /**
         * ---------------------
         * samity field officer dhore data anar kaj ai function a korte hobe.
         */

        // dd($insallmentDatesAllLoan, $samityData);

        foreach ($loans as $key => $loan) {


            $samityId = $loan->samityId;
            // $loanStatuses[$loan->id]['opening_data'] = [];
            // $loanStatuses[$loan->id]['period_data'] = [];
            // $loanStatuses[$loan->id]['cumulative_data'] = [];
            // $loanStatuses[$loan->id]['field_officer_wise'] = [];
            // $loanStatuses[$loan->id]['field_officer_wise'] = [];
            // $loanStatuses[$loan->id]['field_officer_wise'] = [];

            $installments = array();
            if ("generate_installment_details") {
                $repayAmount = round($loan->loanAmount * $loan->interestRateIndex);
                $interestAmount        = $repayAmount - $loan->loanAmount;

                $installmentAmountPrincipal = round($loan->installmentAmount / $loan->interestRateIndex, 5);
                $installmentAmountInterest  = $loan->installmentAmount - $installmentAmountPrincipal;

                $lastInstallmentPrincipal   = round($loan->loanAmount - ($installmentAmountPrincipal * ($loan->numberOfInstallment - 1)), 5);
                $lastInstallmentInterest    = round($interestAmount - ($installmentAmountInterest * ($loan->numberOfInstallment - 1)), 5);


                $installments['installmentAmount'] = $loan->installmentAmount;
                $installments['actualInastallmentAmount'] = $loan->actualInstallmentAmount;
                $installments['extraInstallmentAmount'] = $loan->extraInstallmentAmount;
                $installments['lastInstallmentAmount'] = $loan->lastInstallmentAmount;

                $installments['installmentAmountPrincipal'] = $installmentAmountPrincipal;
                $installments['installmentAmountInterest'] = $installmentAmountInterest;
                $installments['lastInstallmentPrincipal'] = $lastInstallmentPrincipal;
                $installments['lastInstallmentInterest'] = $lastInstallmentInterest;
            }

            $insallmentdates = $insallmentDatesAllLoan[$loan->id];
            $samityFosForOp = (isset($samityData[$samityId]->fieldOfficers)) ? $samityData[$samityId]->fieldOfficers : array();

            $installmentNo            = 1;
            $installmentCounted = $payableAmount = $payableAmountPrincipal = $payableServiceCharge = 0;
            $periodInstallmentCounted = $periodPayableAmount = $periodPayableAmountPrincipal = $periodPayableServiceCharge = 0;

            $foWisePayableData = array();
            $max_inst_date = 0;

            foreach ($insallmentdates as $insallmentdate) {

                $max_inst_date = max($max_inst_date, $insallmentdate);

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $insallmentdate;
                $schedule['weekDay']         = date('l', strtotime($insallmentdate));

                if ($installmentNo == $loan->numberOfInstallment) {
                    // if it the last installment
                    $schedule['installmentAmount']          = $installments['lastInstallmentAmount'];
                    $schedule['actualInastallmentAmount']   = 0;
                    $schedule['extraInstallmentAmount']     = 0;
                    $schedule['installmentAmountPrincipal'] = $installments['lastInstallmentPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['lastInstallmentInterest'];
                } else {
                    $schedule['installmentAmount']          = $installments['installmentAmount'];
                    $schedule['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                    $schedule['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                    $schedule['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['installmentAmountInterest'];
                }

                if (self::$requirement == 'status') {
                    ## total installment payable until todate for this loan
                    $payableAmount += $schedule['installmentAmount'];
                    $payableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                    $payableServiceCharge += $schedule['installmentAmountInterest'];
                    $installmentCounted++;

                    ## on period (Regular Recoverable) installment payable until todate for this loan
                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate) {
                        $periodPayableAmount += $schedule['installmentAmount'];
                        $periodPayableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                        $periodPayableServiceCharge += $schedule['installmentAmountInterest'];
                        $periodInstallmentCounted++;
                    }

                    ## feild officer wise data make
                    foreach ($samityFosForOp as $key => $foData) {

                        $foid = $foData['fieldOfficerId'];

                        /**
                         * kono installment date jodi kono fo er majhe na pore tahole array er last fo er data te bosano hocche.
                         * jhamela face korbo jokhon collecction korte jabo. karon
                         * ekta loan shuru hoise array er 1st fo er datefrom er age sei sob date gulo porbe array er last er fo er majhe but
                         * collection dekhanor somoy o last fo er majhe obossoi oi date gulor collection last fo te dekhate hobe, array er onno
                         * fo der majhe dekhano jabe na. tokhono collection query te bosate hobe not between fo's datefrom and dateto
                         */

                        if (($insallmentdate >= $foData['dateFrom'] && $insallmentdate <= $foData['dateTo']) || ($key == (count($samityFosForOp) - 1))) {

                            ## total installment payable until todate for this loan
                            if (isset($foWisePayableData[$foid]['payableAmount'])) {
                                $foWisePayableData[$foid]['payableAmount'] += $schedule['installmentAmount'];
                            } else {
                                $foWisePayableData[$foid]['payableAmount'] = $schedule['installmentAmount'];
                            }

                            if (isset($foWisePayableData[$foid]['payableAmountPrincipal'])) {
                                $foWisePayableData[$foid]['payableAmountPrincipal'] += $schedule['installmentAmountPrincipal'];
                            } else {
                                $foWisePayableData[$foid]['payableAmountPrincipal'] = $schedule['installmentAmountPrincipal'];
                            }

                            if (isset($foWisePayableData[$foid]['payableServiceCharge'])) {
                                $foWisePayableData[$foid]['payableServiceCharge'] += $schedule['installmentAmountInterest'];
                            } else {
                                $foWisePayableData[$foid]['payableServiceCharge'] = $schedule['installmentAmountInterest'];
                            }

                            if (isset($foWisePayableData[$foid]['installmentCounted'])) {
                                $foWisePayableData[$foid]['installmentCounted'] += 1;
                            } else {
                                $foWisePayableData[$foid]['installmentCounted'] = 1;
                            }

                            ## on period (Regular Recoverable) installment payable until todate for this loan
                            if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate) {

                                if (isset($foWisePayableData[$foid]['periodPayableAmount'])) {
                                    $foWisePayableData[$foid]['periodPayableAmount'] += $schedule['installmentAmount'];
                                } else {
                                    $foWisePayableData[$foid]['periodPayableAmount'] = $schedule['installmentAmount'];
                                }

                                if (isset($foWisePayableData[$foid]['periodPayableAmountPrincipal'])) {
                                    $foWisePayableData[$foid]['periodPayableAmountPrincipal'] += $schedule['installmentAmountPrincipal'];
                                } else {
                                    $foWisePayableData[$foid]['periodPayableAmountPrincipal'] = $schedule['installmentAmountPrincipal'];
                                }

                                if (isset($foWisePayableData[$foid]['periodPayableServiceCharge'])) {
                                    $foWisePayableData[$foid]['periodPayableServiceCharge'] += $schedule['installmentAmountInterest'];
                                } else {
                                    $foWisePayableData[$foid]['periodPayableServiceCharge'] = $schedule['installmentAmountInterest'];
                                }

                                if (isset($foWisePayableData[$foid]['periodInstallmentCounted'])) {
                                    $foWisePayableData[$foid]['periodInstallmentCounted'] += 1;
                                } else {
                                    $foWisePayableData[$foid]['periodInstallmentCounted'] = 1;
                                }
                            }
                        }
                    }
                } else {
                    if ($insallmentdate >= self::$loanStatusFromDate) {
                        array_push($schedules, $schedule);
                    }
                }

                $installmentNo++;
            }

            $loanStatuses[$loan->id]['loanId']    = $loan->id;
            $loanStatuses[$loan->id]['loan_data'] = (array) $loan;
            $loanStatuses[$loan->id]['installment_data'] = $installments;
            $loanStatuses[$loan->id]['installment_dates'] = $insallmentdates;
            $loanStatuses[$loan->id]['field_officer_wise_data'] = $foWisePayableData;

            /**
             * payableAmount = cumulative amount untill date to
             * periodPayableAmount = on period payable amount or regular recovery amount
             * openingData or BeforePeriodData = payable - period payable
             */

            dd($loanStatuses, $insallmentdate);


            if (self::$requirement == 'status') {
                $loanStatus['loanId']                 = $loan->id;
                $loanStatus['payableAmount']          = $payableAmount;
                $loanStatus['payableAmountPrincipal'] = $payableAmountPrincipal;
                $loanStatus['payableServiceCharge'] = $payableServiceCharge;
                $loanStatus['max_inst'] = $max_inst_date;

                $loanStatus['isLastInstallmentPresent'] = false;

                if (isset($insallmentdate)) {
                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    } elseif (self::$loanStatusFromDate == null && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    }
                }

                // $loanStatus['isLastInstallmentPresent'] = --$installmentNo == $loan->numberOfInstallment ? true : false;
                $loanStatus['installmentCounted'] = $installmentCounted;
                if (self::$loanStatusFromDate != null) {
                    $loanStatus['periodPayableAmount']          = $periodPayableAmount;
                    $loanStatus['periodPayableAmountPrincipal'] = $periodPayableAmountPrincipal;
                    $loanStatus['periodInstallmentCounted']     = $periodInstallmentCounted;
                }
                array_push($loanStatuses, $loanStatus);
            }
        } /* loan loop end */

        // dd($loanStatuses);

        if (self::$requirement == 'status') {
            return $loanStatuses;
        }

        return $schedules;
    }

    ## parameter accept object
    public static function generateInstallmentDatesTTL($loanIdOrIds)
    {
        if (self::$scheduleMethodForHoliday == null) {
            $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
            if ($companyConfig) {
                self::$scheduleMethodForHoliday = $companyConfig->form_value;
            }
        }
        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->where(function ($query) use ($loanIdOrIds) {
                if(!empty($loanIdOrIds) )
                {
                    $chunkedValues = array_chunk($loanIdOrIds, 2000);
                    array_map(function ($chankValue) use ($query) {
                        $query->orWhereIn('id', $chankValue);
                    }, $chunkedValues);
                }
                else
                {
                    $query->whereIn('id', $loanIdOrIds);
                }
            })
            // ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId', 'ASC')
            ->orderBy('samityId', 'ASC')
            ->orderBy('memberId', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $tempArray = array();

        if (count($loans->toArray()) < 1) {
            return $tempArray;
        }

        self::$regularLoanConfig = json_decode(DB::table('mfn_config')->where('title', 'regularLoan')->value('content'));
        rsort(self::$regularLoanConfig->preferedAmounts);

        // get reschedules
        // here self::$loanReschedules and self::$rescheduledLoanIds it defind into a condition
        // because generateLoanSchedule() functionn may call from dummy reschedule data
        // when it is call from dummy values then merge it with original
        if ("loan_reschedule_query") {
            $loanReschedules = DB::table('mfn_loan_reschedules')
                ->where('is_delete', 0)
                ->where(function ($query) use ($loanIdOrIds) {
                    if(!empty($loanIdOrIds) )
                    {
                        $chunkedValues = array_chunk($loanIdOrIds, 2000);
                        array_map(function ($chankValue) use ($query) {
                            $query->orWhereIn('loanId', $chankValue);
                        }, $chunkedValues);
                    }
                    else
                    {
                        $query->whereIn('loanId', $loanIdOrIds);
                    }
                });
                // ->whereIn('loanId', $loanIdOrIds);
            if (self::$exceptRescheduleId !== null) {
                $loanReschedules->where('id', '!=', self::$exceptRescheduleId);
            }
            $loanReschedules = $loanReschedules->get();

            if (self::$loanReschedules !== null) {
                self::$loanReschedules = self::$loanReschedules->merge($loanReschedules);
            } else {
                self::$loanReschedules = $loanReschedules;
            }

            self::$rescheduledLoanIds = self::$loanReschedules->pluck('loanId')->toArray();

            self::$massLoanReschedules = DB::table('mfn_loan_mass_reschedules')->where('is_delete', 0)->get();
        }

        $holidayFrom = date('Y-m-d', strtotime($loans->min('disbursementDate')));
        $holidayTo   = date('Y-m-d', strtotime("+10 years", strtotime($loans->max('disbursementDate')))); // here we assume that no loan period is longer than 10 years

        $samityIdsHavingSamityHoliday = DB::table('hr_holidays_special')
            ->where([
                ['is_delete', 0],
                ['sh_date_from', '>=', $holidayFrom],
                ['sh_date_to', '<=', $holidayTo],
                ['samity_id', '>', 0],
            ])
            ->groupBy('samity_id')
            ->pluck('samity_id')
            ->toArray();


        $currentSamityId = 0;
        foreach ($loans as $key => $loan) {
            $dates = [];


            if ($currentSamityId != $loan->samityId) {
                $currentSamityId        = $loan->samityId;
                self::$samity           = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $loan->samityId)->first();
                self::$samityDayChanges = DB::table('mfn_samity_day_changes')
                    ->where([
                        ['is_delete', 0],
                        ['samityId', $loan->samityId],
                    ])
                    ->get();
            }

            if (count(self::$holidays) == 0 || in_array($loan->samityId, $samityIdsHavingSamityHoliday)) {
                self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            }

            // if it is one time loan
            if ($loan->loanType === 'Onetime') {
                $dates[0] = $loan->firstRepayDate;
                while (in_array($dates[0], self::$holidays)) {
                    $dates[0] = date('Y-m-d', strtotime("+7 day", strtotime($dates[0])));
                }
            } elseif ($loan->loanType === 'Regular') {
                // if it is Daily Loan
                if ($loan->repaymentFrequencyId === 1) {
                    $dates = self::generateDailyInstallmentDates($loan);
                }
                // if it is Weekly Loan
                elseif ($loan->repaymentFrequencyId === 2) {
                    $dates = self::generateWeeklyInstallmentDates($loan);
                }
                // if it is Monthly Loan
                elseif ($loan->repaymentFrequencyId === 4) {
                    $dates = self::generateMonthlyInstallmentDates($loan);
                }
            }

            // array_push($tempArray, [ $loan->id  => $dates ]);

            $tempArray[$loan->id] = $dates;
        }

        return $tempArray;
    }

    public static function generateInstallmentDatesTTLTesting($loanIdOrIds, $startDate, $endDate = null, $flag = false)
    {
        if (self::$scheduleMethodForHoliday == null) {
            $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
            if ($companyConfig) {
                self::$scheduleMethodForHoliday = $companyConfig->form_value;
            }
        }
        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId', 'ASC')
            ->orderBy('samityId', 'ASC')
            ->orderBy('memberId', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $tempArray = array();

        if (count($loans->toArray()) < 1) {
            return $tempArray;
        }

        self::$regularLoanConfig = json_decode(DB::table('mfn_config')->where('title', 'regularLoan')->value('content'));
        rsort(self::$regularLoanConfig->preferedAmounts);

        // get reschedules
        // here self::$loanReschedules and self::$rescheduledLoanIds it defind into a condition
        // because generateLoanSchedule() functionn may call from dummy reschedule data
        // when it is call from dummy values then merge it with original
        if ("loan_reschedule_query") {
            $loanReschedules = DB::table('mfn_loan_reschedules')
                ->where('is_delete', 0)
                ->whereIn('loanId', $loanIdOrIds);
            if (self::$exceptRescheduleId !== null) {
                $loanReschedules->where('id', '!=', self::$exceptRescheduleId);
            }
            $loanReschedules = $loanReschedules->get();

            if (self::$loanReschedules !== null) {
                self::$loanReschedules = self::$loanReschedules->merge($loanReschedules);
            } else {
                self::$loanReschedules = $loanReschedules;
            }

            self::$rescheduledLoanIds = self::$loanReschedules->pluck('loanId')->toArray();

            self::$massLoanReschedules = DB::table('mfn_loan_mass_reschedules')->where('is_delete', 0)->get();
        }

        $holidayFrom = date('Y-m-d', strtotime($loans->min('disbursementDate')));
        $holidayTo   = date('Y-m-d', strtotime("+10 years", strtotime($loans->max('disbursementDate')))); // here we assume that no loan period is longer than 10 years

        $samityIdsHavingSamityHoliday = DB::table('hr_holidays_special')
            ->where([
                ['is_delete', 0],
                ['sh_date_from', '>=', $holidayFrom],
                ['sh_date_to', '<=', $holidayTo],
                ['samity_id', '>', 0],
            ])
            ->groupBy('samity_id')
            ->pluck('samity_id')
            ->toArray();


        $currentSamityId = 0;
        foreach ($loans as $key => $loan) {
            $dates = [];


            if ($currentSamityId != $loan->samityId) {
                $currentSamityId        = $loan->samityId;
                self::$samity           = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $loan->samityId)->first();
                self::$samityDayChanges = DB::table('mfn_samity_day_changes')
                    ->where([
                        ['is_delete', 0],
                        ['samityId', $loan->samityId],
                    ])
                    ->get();
            }

            if (count(self::$holidays) == 0 || in_array($loan->samityId, $samityIdsHavingSamityHoliday)) {
                self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            }

            // if it is one time loan
            if ($loan->loanType === 'Onetime') {
                $dates[0] = $loan->firstRepayDate;
                while (in_array($dates[0], self::$holidays)) {
                    $dates[0] = date('Y-m-d', strtotime("+7 day", strtotime($dates[0])));
                }
            } elseif ($loan->loanType === 'Regular') {
                // if it is Daily Loan
                if ($loan->repaymentFrequencyId === 1) {
                    $dates = self::generateDailyInstallmentDates($loan);
                }
                // if it is Weekly Loan
                elseif ($loan->repaymentFrequencyId === 2) {
                    $dates = self::generateWeeklyInstallmentDates($loan);
                }
                // if it is Monthly Loan
                elseif ($loan->repaymentFrequencyId === 4) {
                    $dates = self::generateMonthlyInstallmentDates($loan);
                }
            }

            // array_push($tempArray, [ $loan->id  => $dates ]);
            //  $tempArray[$loan->id] = $dates;

            #Testing purpose
            if(isset($startDate) && in_array($startDate, $dates) && $flag == false)
            {
                if(!empty($startDate) && !empty($endDate))
                {
                    $dates = array_filter($dates, function($date) use ($startDate, $endDate) {
                        return ($date >= $startDate && $date <= $endDate);
                    });
                    $tempArray[$loan->id] = $dates;
                }
                // array_push($tempArray, $loan->id);
            }
            if($flag == true)
            {
                if(!empty($startDate) && empty($endDate))
                {
                    $dates = array_filter($dates, function($date) use ($startDate) {
                        return ($date >= $startDate);
                    });
                    // if($dates != [])
                    // {
                        $tempArray[$loan->id] = $dates;
                    // }
                }
                elseif(!empty($startDate) && !empty($endDate))
                {
                    $dates = array_filter($dates, function($date) use ($startDate, $endDate) {
                            return ($date >= $startDate  && $date <= $endDate);
                        });
                    $tempArray[$loan->id] = $dates;
                }
            }

        }

        return $tempArray;
    }

    public static function getLoanStatusAllModified($loanIdOrIds, ...$dates)
    {
        self::$requirement = 'status';

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->whereIn('id', $loanIdOrIds)
            ->select('id', 'loanAmount', 'repayAmount', 'samityId', 'productId', 'installmentAmount', 'lastInstallmentAmount')
            ->get()->keyBy('id');

        $loanCollections = DB::table('mfn_loan_collections')
            ->whereIn('loanId', $loanIdOrIds)
            ->where('is_delete', 0)
            ->selectRaw('loanId, paymentType ,amount, principalAmount, interestAmount');

        if (self::$loanStatusToDate != null) {
            $loanCollections->where('collectionDate', '<=', self::$loanStatusToDate);
        }

        if (self::$loanStatusFromDate != null) {
            $loanCollectionOnPeriod = clone $loanCollections;
            $loanCollectionOnPeriod->where('collectionDate', '>=', self::$loanStatusFromDate);
            $loanCollectionOnPeriod = $loanCollectionOnPeriod->get();

            $loanCollectionOnPeriodIdWise = $loanCollectionOnPeriod->groupBy('loanId');
            $loanCollectionOnPeriodIdPaymentWise = $loanCollectionOnPeriod->groupBy(['loanId', 'paymentType']);

            #### Before Period
            $loanCollectionBeforePeriod = clone $loanCollections;
            $loanCollectionBeforePeriod->where('collectionDate', '<', self::$loanStatusFromDate);
            $loanCollectionBeforePeriod = $loanCollectionBeforePeriod->get();

            // dd(19);

            $loanCollectionBeforePeriodIdWise = $loanCollectionBeforePeriod->groupBy('loanId');
            $loanCollectionBeforePeriodIdPaymentWise = $loanCollectionBeforePeriod->groupBy(['loanId', 'paymentType']);
        }

        $loanCollections = $loanCollections->get();

        // dd(20);

        $loanCollectionLoanIdWise = $loanCollections->groupBy('loanId');
        $loanCollectionLoanIdWisePay = $loanCollections->groupBy(['loanId', 'paymentType']);
        $loanStatuses = self::generateLoanScheduleModified($loanIdOrIds, ...$dates);
        // dd($loanCollectionLoanIdWise);
        // $loanStatuses = self::generateLoanSchedule($loanIdOrIds, ...$dates); #@


        // if self::$loanStatusFromDate != null then you are trying to get loan status
        // between two dates. Here we will get payable amount till $loanStatusToDate date
        // and payable amount between two dates, also we will get paid amount till $loanStatusToDate
        // date and between two dates

        foreach ($loanStatuses as $key => $loanStatus) {
            // dd($loanStatus);

            $loanStatuses[$key]['max_inst'] = $loanStatus['max_inst'];
            $paidAmount          =  $paidAmountPrincipal = $paidAmountInterest  = 0;

            if (isset($loanCollectionLoanIdWise[$loanStatus['loanId']])) {
                $loanCollection      = $loanCollectionLoanIdWise[$loanStatus['loanId']];
                $paidAmount          = $loanCollection->sum('amount');
                $paidAmountPrincipal = $loanCollection->sum('principalAmount');
                $paidAmountInterest  = $loanCollection->sum('interestAmount');
            }

            // dd($loanCollectionLoanIdWisePay[$loanStatus['loanId']]['OB']->sum('amount'), $loanStatus['loanId'], $paidAmount, $paidAmountPrincipal, $paidAmountInterest);

            $dueAmount              = $loanStatus['payableAmount'] - $paidAmount;
            $dueAmountPrincipal     = round($loanStatus['payableAmountPrincipal'] - $paidAmountPrincipal, 5);
            $advanceAmount          = $dueAmount == 0 ? 0 : -$dueAmount;
            $advanceAmountPrincipal = $dueAmountPrincipal == 0 ? 0 : -$dueAmountPrincipal;

            $dueAmount              = $dueAmount < 0 ? 0 : $dueAmount;
            $dueAmountPrincipal     = $dueAmountPrincipal <= 0 ? 0 : $dueAmountPrincipal;
            $advanceAmount          = $advanceAmount < 0 ? 0 : $advanceAmount;
            $advanceAmountPrincipal = $advanceAmountPrincipal < 0 ? 0 : $advanceAmountPrincipal;

            // Assign values to main object
            $loanStatuses[$key]['samityId']  = $loans[$loanStatus['loanId']]->samityId;
            $loanStatuses[$key]['productId'] = $loans[$loanStatus['loanId']]->productId;

            $loanStatuses[$key]['paidAmount']             = $paidAmount;
            $loanStatuses[$key]['paidAmountPrincipal']    = $paidAmountPrincipal;
            $loanStatuses[$key]['paidAmountInterest']     = $paidAmountInterest;
            // $loanStatuses[$key]['rebateAmount']           = $loanCollection->where('paymentType', 'Rebate')->sum('amount');
            $loanStatuses[$key]['rebateAmount']           = (isset($loanCollectionLoanIdWisePay[$loanStatus['loanId']]['Rebate']))
                ? $loanCollectionLoanIdWisePay[$loanStatus['loanId']]['Rebate']->sum('amount')
                : 0;

            $loanStatuses[$key]['dueAmount']              = $dueAmount;
            $loanStatuses[$key]['dueAmountPrincipal']     = $dueAmountPrincipal;
            $loanStatuses[$key]['advanceAmount']          = $advanceAmount;
            $loanStatuses[$key]['advanceAmountPrincipal'] = $advanceAmountPrincipal;

            $loanStatuses[$key]['outstanding']          = $loans[$loanStatus['loanId']]->repayAmount - $paidAmount;
            $loanStatuses[$key]['outstandingPrincipal'] = $loans[$loanStatus['loanId']]->loanAmount - $paidAmountPrincipal;

            // now calculate on period data i.e. between two dates
            if (self::$loanStatusFromDate != null) {
                // to know about on period status, we need to know status before start date
                $beginningPayable          = $loanStatus['payableAmount'] - $loanStatus['periodPayableAmount'];
                $beginningPayablePrincipal = $loanStatus['payableAmountPrincipal'] - $loanStatus['periodPayableAmountPrincipal'];

                $onPeriodPaidAmount          = $onPeriodPaidAmountPrincipal = 0;

                $beforePeriodPaidAmount          = $beforePeriodPaidAmountPrincipal = 0;

                if (isset($loanCollectionOnPeriodIdWise[$loanStatus['loanId']])) {
                    $onPeriodPaidAmount          = $loanCollectionOnPeriodIdWise[$loanStatus['loanId']]->sum('amount');
                    $onPeriodPaidAmountPrincipal = $loanCollectionOnPeriodIdWise[$loanStatus['loanId']]->sum('principalAmount');
                }

                ## Opening Col Data
                if (isset($loanCollectionBeforePeriodIdWise[$loanStatus['loanId']])) {
                    $beforePeriodPaidAmount          = $loanCollectionBeforePeriodIdWise[$loanStatus['loanId']]->sum('amount');
                    $beforePeriodPaidAmountPrincipal = $loanCollectionBeforePeriodIdWise[$loanStatus['loanId']]->sum('principalAmount');
                }
                $loanStatuses[$key]['openingCollection']     =  $beforePeriodPaidAmount;
                $loanStatuses[$key]['openingoutstanding']     = $loans[$loanStatus['loanId']]->repayAmount - $beforePeriodPaidAmount;
                $loanStatuses[$key]['openingoutstandingPrincipal']     = $loans[$loanStatus['loanId']]->loanAmount - $beforePeriodPaidAmountPrincipal;

                $beginningPaidAmount          = $paidAmount - $onPeriodPaidAmount;
                $beginningPaidAmountPrincipal = $paidAmountPrincipal - $onPeriodPaidAmountPrincipal;

                $beginigAdvanceAmount          = $beginningPaidAmount - $beginningPayable;
                $beginigAdvanceAmountPrincipal = $beginningPaidAmountPrincipal - $beginningPayablePrincipal;

                $beginigDueAmount          = -$beginigAdvanceAmount;
                $beginigDueAmountPrincipal = -$beginigAdvanceAmountPrincipal;

                $beginigAdvanceAmount          = $beginigAdvanceAmount < 0 ? 0 : $beginigAdvanceAmount;
                $beginigAdvanceAmountPrincipal = $beginigAdvanceAmountPrincipal < 0 ? 0 : $beginigAdvanceAmountPrincipal;

                $beginigDueAmount          = $beginigDueAmount < 0 ? 0 : $beginigDueAmount;
                $beginigDueAmountPrincipal = $beginigDueAmountPrincipal < 0 ? 0 : $beginigDueAmountPrincipal;

                // if advanced paid before period than it will be deducted
                $onPeriodPayable          = $loanStatus['periodPayableAmount'];
                $onPeriodPayable          = $onPeriodPayable < 0 ? 0 : $onPeriodPayable;
                $onPeriodPayablePrincipal = $loanStatus['periodPayableAmountPrincipal'];
                $onPeriodPayablePrincipal = $onPeriodPayablePrincipal < 0 ? 0 : $onPeriodPayablePrincipal;

                $onPeriodDueAmount              = $onPeriodPayable - $onPeriodPaidAmount;
                $onPeriodAdvanceAmount          = -$onPeriodDueAmount;
                $onPeriodDueAmountPrincipal     = $onPeriodPayablePrincipal - $onPeriodPaidAmountPrincipal;
                $onPeriodAdvanceAmountPrincipal = -$onPeriodDueAmountPrincipal;

                $onPeriodDueAmount              = $onPeriodDueAmount <= 0 ? 0 : $onPeriodDueAmount;
                $onPeriodAdvanceAmount          = $onPeriodAdvanceAmount <= 0 ? 0 : $onPeriodAdvanceAmount;
                $onPeriodDueAmountPrincipal     = $onPeriodDueAmountPrincipal <= 0 ? 0 : $onPeriodDueAmountPrincipal;
                $onPeriodAdvanceAmountPrincipal = $onPeriodAdvanceAmountPrincipal <= 0 ? 0 : $onPeriodAdvanceAmountPrincipal;

                ## hasib added opening variables
                $loanStatuses[$key]['openingPayable']                = $beginningPayable;
                $loanStatuses[$key]['openingPayablePrincipal']       = $beginningPayablePrincipal;
                $loanStatuses[$key]['openingDueAmount']              = $beginigDueAmount;
                $loanStatuses[$key]['openingDueAmountPrincipal']     = $beginigDueAmountPrincipal;
                $loanStatuses[$key]['openingAdvanceAmount']          = $beginigAdvanceAmount;
                $loanStatuses[$key]['openingAdvanceAmountPrincipal'] = $beginigAdvanceAmountPrincipal;
                $loanStatuses[$key]['openingCollection']             = $beginningPaidAmount;
                $loanStatuses[$key]['openingCollectionPrincipal']    = $beginningPaidAmountPrincipal;
                $loanStatuses[$key]['openingCollectionInterest']     = $beginningPaidAmount - $beginningPaidAmountPrincipal;
                // Assign values to main object
                $loanStatuses[$key]['onPeriodPayable']                = $onPeriodPayable;
                $loanStatuses[$key]['onPeriodPayablePrincipal']       = $onPeriodPayablePrincipal;
                $loanStatuses[$key]['onPeriodDueAmount']              = $onPeriodDueAmount;
                $loanStatuses[$key]['onPeriodDueAmountPrincipal']     = $onPeriodDueAmountPrincipal;
                $loanStatuses[$key]['onPeriodAdvanceAmount']          = $onPeriodAdvanceAmount;
                $loanStatuses[$key]['onPeriodAdvanceAmountPrincipal'] = $onPeriodAdvanceAmountPrincipal;
                $loanStatuses[$key]['onPeriodCollection']             = $onPeriodPaidAmount;
                $loanStatuses[$key]['onPeriodCollectionPrincipal']    = $onPeriodPaidAmountPrincipal;
                $loanStatuses[$key]['onPeriodCollectionInterest']     = $onPeriodPaidAmount - $onPeriodPaidAmountPrincipal;


                ## Add Regular Due Calculation on 07-12-22 by tuli
                $regularDue = $onPeriodPayable - ($onPeriodPaidAmount + $beginigAdvanceAmount);
                $loanStatuses[$key]['regularDue'] = $regularDue <= 0 ? 0 : $regularDue;

                $regularDuePrincipal = $onPeriodPayablePrincipal - ($onPeriodPaidAmountPrincipal + $beginigAdvanceAmountPrincipal);
                $loanStatuses[$key]['regularDuePrincipal'] = $regularDuePrincipal <= 0 ? 0 : $regularDuePrincipal;
                ### END

                // classify regular, due, advance collection
                $remainingCollectionAmount                      = $onPeriodPaidAmount;
                $loanStatuses[$key]['onPeriodReularCollection'] = min($onPeriodPayable, $remainingCollectionAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollection'];

                $loanStatuses[$key]['onPeriodDueCollection'] = min($remainingCollectionAmount, $beginigDueAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollection'];

                $loanStatuses[$key]['onPeriodAdvanceCollection'] = $remainingCollectionAmount;

                // Principal
                $remainingCollectionAmount                               = $onPeriodPaidAmountPrincipal;
                $loanStatuses[$key]['onPeriodReularCollectionPrincipal'] = min($onPeriodPayablePrincipal, $remainingCollectionAmount);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodReularCollectionPrincipal'];

                $loanStatuses[$key]['onPeriodDueCollectionPrincipal'] = min($remainingCollectionAmount, $beginigDueAmountPrincipal);

                $remainingCollectionAmount -= $loanStatuses[$key]['onPeriodDueCollectionPrincipal'];

                $loanStatuses[$key]['onPeriodAdvanceCollectionPrincipal'] = $remainingCollectionAmount;

                // $loanStatuses[$key]['onPeriodRebateAmount'] = $loanCollectionOnPeriod->where('paymentType', 'Rebate')->sum('amount');

                $loanStatuses[$key]['onPeriodRebateAmount']           = (isset($loanCollectionOnPeriodIdPaymentWise[$loanStatus['loanId']]['Rebate']))
                    ? $loanCollectionOnPeriodIdPaymentWise[$loanStatus['loanId']]['Rebate']->sum('amount')
                    : 0;

                // $loanStatuses[$key]['onPeriodRebateAmount'] = $loanCollectionOnPeriod->where('paymentType', 'Rebate')->sum('amount');

                // calculate number of regular full collection
                // $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($loanStatuses[$key]['onPeriodReularCollection'] / $loans[$loanStatus['loanId']]->installmentAmount);

                // if ($loanStatuses[$key]['isLastInstallmentPresent'] && ($loanStatuses[$key]['onPeriodReularCollection'] % $loans[$loanStatus['loanId']]->installmentAmount >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)) {
                //     $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                // }

                $onPeriodRegAdvCollection = $beginigAdvanceAmount + $loanStatuses[$key]['onPeriodReularCollection'];
                $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($onPeriodRegAdvCollection / $loans[$loanStatus['loanId']]->installmentAmount);

                // if($loanStatus['loanId'] == 19733){
                //     dd($onPeriodRegAdvCollection, $loans[$loanStatus['loanId']]->installmentAmount, $loanStatuses[$key]['numberOfFullyPaidRegularCollection']);
                // }

                if (
                    $loanStatuses[$key]['isLastInstallmentPresent']
                    && (($onPeriodRegAdvCollection % $loans[$loanStatus['loanId']]->installmentAmount) >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)
                ) {
                    $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                }
            }
        }
        // dd(2);
        return $loanStatuses;
    }

    ## This Function is called for scheduling in all reports except col sheet
    public static function generateLoanScheduleModified($loanIdOrIds, ...$dates)
    {
        if (self::$scheduleMethodForHoliday == null) {
            $companyConfig = DB::table("gnl_company_config")->where([["form_id", 28], ["module_id", 5], ["company_id", 1]])->first();
            if ($companyConfig) {
                self::$scheduleMethodForHoliday = $companyConfig->form_value;
            }
        }

        self::$regularLoanConfig = json_decode(DB::table('mfn_config')->where('title', 'regularLoan')->value('content'));
        rsort(self::$regularLoanConfig->preferedAmounts);

        if (isset($dates[1])) {
            self::$loanStatusToDate   = date('Y-m-d', strtotime($dates[1]));
            self::$loanStatusFromDate = date('Y-m-d', strtotime($dates[0]));
        } elseif (isset($dates[0])) {
            self::$loanStatusToDate = date('Y-m-d', strtotime($dates[0]));
        }

        if (self::$loanStatusFromDate > self::$loanStatusToDate) {
            return 'From date should be appear first';
        }

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId')
            ->orderBy('samityId')
            ->get();

        // get reschedules
        // here self::$loanReschedules and self::$rescheduledLoanIds it defind into a condition
        // because generateLoanSchedule() functionn may call from dummy reschedule data
        // when it is call from dummy values then merge it with original
        $loanReschedules = DB::table('mfn_loan_reschedules')
            ->where('is_delete', 0)
            ->whereIn('loanId', $loanIdOrIds);
        if (self::$exceptRescheduleId !== null) {
            $loanReschedules->where('id', '!=', self::$exceptRescheduleId);
        }
        $loanReschedules = $loanReschedules->get();

        if (self::$loanReschedules !== null) {
            self::$loanReschedules = self::$loanReschedules->merge($loanReschedules);
        } else {
            self::$loanReschedules = $loanReschedules;
        }

        self::$rescheduledLoanIds = self::$loanReschedules->pluck('loanId')->toArray();

        self::$massLoanReschedules = DB::table('mfn_loan_mass_reschedules')->where('is_delete', 0)->get();

        $currentSamityId = 0;
        $schedules       = [];
        $loanStatuses    = [];

        // get the rage of dates between which we will get the holidays
        // $holidayFrom = date('Y-m-d', strtotime($loans->min('disbursementDate')));
        // $holidayTo   = date('Y-m-d', strtotime("+10 years", strtotime($loans->max('disbursementDate')))); // here we assume that no loan period is longer than 10 years

        // $samityIdsHavingSamityHoliday = DB::table('hr_holidays_special')
        //     ->where([
        //         ['is_delete', 0],
        //         ['sh_date_from', '>=', $holidayFrom],
        //         ['sh_date_to', '<=', $holidayTo],
        //         ['samity_id', '>', 0],
        //     ])
        //     ->groupBy('samity_id')
        //     ->pluck('samity_id')
        //     ->toArray();


        $Datainsallmentdates = self::generateInstallmentDatesMultiple($loanIdOrIds);


        foreach ($loans as $key => $loan) {
            // if ($currentSamityId != $loan->samityId) {
            //     $currentSamityId        = $loan->samityId;
            //     self::$samity           = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $loan->samityId)->first();
            //     self::$samityDayChanges = DB::table('mfn_samity_day_changes')
            //         ->where([
            //             ['is_delete', 0],
            //             ['samityId', $loan->samityId],
            //         ])
            //         ->get();
            // }

            // if (count(self::$holidays) == 0 || in_array($loan->samityId, $samityIdsHavingSamityHoliday)) {
            //     self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            // }

            // if(isset($Datainsallmentdates[$loan->id])){
            //     $insallmentdates = $Datainsallmentdates[$loan->id];
            // }else{
            //     $insallmentdates = self::generateInstallmentDates($loan);
            // }

            ## April month er por data thik extra installment amount change kora hoyechilo tai db er data e final tader jonno
            if ($loan->disbursementDate < "2022-04-01") {
                $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);
            } else {
                ## old data store in table no need to generate again. new generate korle old data er sathe match kore na
                $repayAmount = round($loan->loanAmount * $loan->interestRateIndex);
                $interestAmount        = $repayAmount - $loan->loanAmount;

                $installmentAmountPrincipal = round($loan->installmentAmount / $loan->interestRateIndex, 5);
                $installmentAmountInterest  = $loan->installmentAmount - $installmentAmountPrincipal;

                $lastInstallmentPrincipal   = round($loan->loanAmount - ($installmentAmountPrincipal * ($loan->numberOfInstallment - 1)), 5);
                $lastInstallmentInterest    = round($interestAmount - ($installmentAmountInterest * ($loan->numberOfInstallment - 1)), 5);


                $installments['installmentAmount'] = $loan->installmentAmount;
                $installments['actualInastallmentAmount'] = $loan->actualInstallmentAmount;
                $installments['extraInstallmentAmount'] = $loan->extraInstallmentAmount;
                $installments['lastInstallmentAmount'] = $loan->lastInstallmentAmount;

                $installments['installmentAmountPrincipal'] = $installmentAmountPrincipal;
                $installments['installmentAmountInterest'] = $installmentAmountInterest;
                $installments['lastInstallmentPrincipal'] = $lastInstallmentPrincipal;
                $installments['lastInstallmentInterest'] = $lastInstallmentInterest;
            }

            $insallmentdates = $Datainsallmentdates[$loan->id];

            $installmentNo            = 1;
            $installmentCounted       = 0;
            $periodInstallmentCounted = 0;
            $payableAmount            = $payableAmountPrincipal            = $periodPayableAmount            = $periodPayableAmountPrincipal            = 0;

            $max_inst_date = 0;
            foreach ($insallmentdates as $insallmentdate) {

                $max_inst_date = max($max_inst_date, $insallmentdate);

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $insallmentdate;
                $schedule['weekDay']         = date('l', strtotime($insallmentdate));

                if ($installmentNo == $loan->numberOfInstallment) {
                    // if it the last installment
                    $schedule['installmentAmount']          = $installments['lastInstallmentAmount'];
                    $schedule['actualInastallmentAmount']   = 0;
                    $schedule['extraInstallmentAmount']     = 0;
                    $schedule['installmentAmountPrincipal'] = $installments['lastInstallmentPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['lastInstallmentInterest'];
                } else {
                    $schedule['installmentAmount']          = $installments['installmentAmount'];
                    $schedule['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                    $schedule['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                    $schedule['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                    $schedule['installmentAmountInterest']  = $installments['installmentAmountInterest'];
                }

                if (self::$requirement == 'status') {
                    $payableAmount += $schedule['installmentAmount'];
                    $payableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                    $installmentCounted++;

                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate) {
                        $periodPayableAmount += $schedule['installmentAmount'];
                        $periodPayableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                        $periodInstallmentCounted++;
                    }
                } else {

                    if ($insallmentdate >= self::$loanStatusFromDate) {
                        array_push($schedules, $schedule);
                    }
                }

                $installmentNo++;
            }
            if (self::$requirement == 'status') {
                $loanStatus['loanId']                 = $loan->id;
                $loanStatus['payableAmount']          = $payableAmount;
                $loanStatus['payableAmountPrincipal'] = $payableAmountPrincipal;
                $loanStatus['max_inst'] = $max_inst_date;

                $loanStatus['isLastInstallmentPresent'] = false;
                if (isset($insallmentdate)) {
                    if (self::$loanStatusFromDate != null && $insallmentdate >= self::$loanStatusFromDate && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    } elseif (self::$loanStatusFromDate == null && $insallmentdate <= self::$loanStatusToDate && --$installmentNo == $loan->numberOfInstallment) {
                        $loanStatus['isLastInstallmentPresent'] = true;
                    }
                }

                // $loanStatus['isLastInstallmentPresent'] = --$installmentNo == $loan->numberOfInstallment ? true : false;
                $loanStatus['installmentCounted'] = $installmentCounted;
                if (self::$loanStatusFromDate != null) {
                    $loanStatus['periodPayableAmount']          = $periodPayableAmount;
                    $loanStatus['periodPayableAmountPrincipal'] = $periodPayableAmountPrincipal;
                    $loanStatus['periodInstallmentCounted']     = $periodInstallmentCounted;
                }
                array_push($loanStatuses, $loanStatus);
            }
        } /* loan loop end */

        // dd(1);

        if (self::$requirement == 'status') {
            return $loanStatuses;
        }

        return $schedules;
    }

    public static function generateInstallmentDetails($loanAmount, $numberOfInstallment, $interestRateIndex, $loanType)
    {
        if ($loanType == 'Onetime') {
            $data = array(
                'installmentAmount'          => $loanAmount,
                'actualInastallmentAmount'   => $loanAmount,
                'extraInstallmentAmount'     => 0,
                'installmentAmountPrincipal' => $loanAmount,
                'installmentAmountInterest'  => 0,
                'lastInstallmentAmount'      => $loanAmount,
                'lastInstallmentPrincipal'   => $loanAmount,
                'lastInstallmentInterest'    => 0,
                'adoptedPolicy'              => 'Onetime',
            );

            return $data;
        }

        $repayAmount = round($loanAmount * $interestRateIndex);

        $interestAmount        = $repayAmount - $loanAmount;
        $installmentAmount     = null;
        $installmentAmountFlag = false;
        $adoptedPolicy         = null;

        $actualInastallmentAmount = round($repayAmount / $numberOfInstallment, 5);
        // it is the last two digit with the fractional part
        $actualInstallmentLastDigits = (float) substr(number_format($actualInastallmentAmount, 5, '.', ''), -8);

        if (self::$regularLoanConfig == null) {
            self::$regularLoanConfig = json_decode(DB::table('mfn_config')->where('title', 'regularLoan')->value('content'));
            rsort(self::$regularLoanConfig->preferedAmounts);
        }

        if ($actualInastallmentAmount <= 1000) {
            self::$regularLoanConfig->installmentAmountGeneratePolicies = [
                "roundToDecade",
                "nearestPreferedAmount",
                "higestPreferedAmount",
                "2.5Percent",
                "roundToOne"
            ];
        } else {
            self::$regularLoanConfig->installmentAmountGeneratePolicies = [
                "nearestPreferedAmount",
                "higestPreferedAmount",
                "roundToDecade",
                "2.5Percent",
                "roundToOne"
            ];
        }

        // dd(self::$regularLoanConfig->installmentAmountGeneratePolicies);

        // in this loop we will find $installmentAmount, $extraInstallmentAmount and $lastInstallmentAmount
        foreach (self::$regularLoanConfig->installmentAmountGeneratePolicies as $key => $policy) {

            if ($policy == '2.5Percent') {
                $installmentAmount      = $loanAmount * 0.025;
                $extraInstallmentAmount = $installmentAmount - $actualInastallmentAmount;
                $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);

                if ($installmentAmount != round($installmentAmount) || $lastInstallmentAmount <= 0 || $extraInstallmentAmount < 0) {
                    continue;
                }

                $installmentAmountFlag = true;
                $adoptedPolicy         = $policy;
            } elseif ($policy == 'higestPreferedAmount') {
                if ($actualInastallmentAmount == round($actualInastallmentAmount) && in_array($actualInstallmentLastDigits, self::$regularLoanConfig->preferedAmounts)) {
                    $installmentAmount      = $actualInastallmentAmount;
                    $extraInstallmentAmount = $installmentAmount - $actualInastallmentAmount;
                    $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);
                    if ($lastInstallmentAmount <= 0) {
                        continue;
                    }
                    $installmentAmountFlag = true;
                    $adoptedPolicy         = $policy;
                } else {
                    foreach (self::$regularLoanConfig->preferedAmounts as $key => $preferedAmount) {
                        $extraInstallmentAmount = $preferedAmount - $actualInstallmentLastDigits;
                        $installmentAmount      = $actualInastallmentAmount + $extraInstallmentAmount;
                        $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);
                        if ($extraInstallmentAmount < 0 || $lastInstallmentAmount <= 0) {
                            continue;
                        }
                        $installmentAmountFlag = true;
                        $adoptedPolicy         = $policy;
                        break;
                    }
                }
            } elseif ($policy == 'nearestPreferedAmount') {
                $preferedAmounts = self::$regularLoanConfig->preferedAmounts;
                $preferedAmounts = array_filter($preferedAmounts, function ($value) use ($actualInstallmentLastDigits) {
                    return $value >= $actualInstallmentLastDigits;
                });
                sort($preferedAmounts);
                foreach ($preferedAmounts as $key => $preferedAmount) {
                    $extraInstallmentAmount = $preferedAmount - $actualInstallmentLastDigits;
                    $installmentAmount      = $actualInastallmentAmount + $extraInstallmentAmount;
                    $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);
                    if ($extraInstallmentAmount < 0 || $lastInstallmentAmount <= 0) {
                        continue;
                    }
                    $installmentAmountFlag = true;
                    $adoptedPolicy         = $policy;
                    break;
                }
            } elseif ($policy == 'roundToDecade') {
                $extraInstallmentAmount = (ceil($actualInstallmentLastDigits / 10) * 10) - $actualInstallmentLastDigits;
                $installmentAmount      = $actualInastallmentAmount + $extraInstallmentAmount;
                $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);
                if ($lastInstallmentAmount <= 0) {
                    continue;
                }
                $installmentAmountFlag = true;
                $adoptedPolicy         = $policy;
            } elseif ($policy == 'roundToOne') {
                $extraInstallmentAmount = ceil($actualInstallmentLastDigits) - $actualInstallmentLastDigits;
                $installmentAmount      = $actualInastallmentAmount + $extraInstallmentAmount;
                $lastInstallmentAmount  = $repayAmount - $installmentAmount * ($numberOfInstallment - 1);
                if ($lastInstallmentAmount <= 0) {
                    continue;
                }
                $installmentAmountFlag = true;
                $adoptedPolicy         = $policy;
            }

            if ($installmentAmountFlag == true) {
                break;
            }
        }

        if ($installmentAmountFlag == false) {
            return null;
        }

        $installmentAmountPrincipal = round($installmentAmount / $interestRateIndex, 5);
        $installmentAmountInterest  = $installmentAmount - $installmentAmountPrincipal;
        $lastInstallmentPrincipal   = round($loanAmount - ($installmentAmountPrincipal * ($numberOfInstallment - 1)), 5);
        $lastInstallmentInterest    = round($interestAmount - ($installmentAmountInterest * ($numberOfInstallment - 1)), 5);

        $data = array(
            'installmentAmount'          => $installmentAmount,
            'actualInastallmentAmount'   => $actualInastallmentAmount,
            'extraInstallmentAmount'     => $extraInstallmentAmount,
            'installmentAmountPrincipal' => $installmentAmountPrincipal,
            'installmentAmountInterest'  => $installmentAmountInterest,
            'lastInstallmentAmount'      => $lastInstallmentAmount,
            'lastInstallmentPrincipal'   => $lastInstallmentPrincipal,
            'lastInstallmentInterest'    => $lastInstallmentInterest,
            'adoptedPolicy'              => $adoptedPolicy,
        );

        // dd($data);

        return $data;
    }

    public static function generateInstallmentDates($loan)
    {
        $dates = [];
        // if it is one time loan
        if ($loan->loanType === 'Onetime') {
            $dates[0] = $loan->firstRepayDate;
            while (in_array($dates[0], self::$holidays)) {
                $dates[0] = date('Y-m-d', strtotime("+7 day", strtotime($dates[0])));
            }
        } elseif ($loan->loanType === 'Regular') {
            // if it is Daily Loan
            if ($loan->repaymentFrequencyId === 1) {
                $dates = self::generateDailyInstallmentDates($loan);
            }
            // if it is Weekly Loan
            elseif ($loan->repaymentFrequencyId === 2) {
                $dates = self::generateWeeklyInstallmentDates($loan);
            }
            // if it is Monthly Loan
            elseif ($loan->repaymentFrequencyId === 4) {
                $dates = self::generateMonthlyInstallmentDates($loan);
            }
        }

        return $dates;
    }

    public static function generateInstallmentDatesMultiple($loanIdOrIds) #@
    {

        if (is_numeric($loanIdOrIds)) {
            $loanIdOrIds = [$loanIdOrIds];
        }

        $loans = DB::table('mfn_loans')
            ->where('is_delete', 0)
            ->whereIn('id', $loanIdOrIds)
            ->orderBy('branchId')
            ->orderBy('samityId')
            ->get();

        $holidayFrom = date('Y-m-d', strtotime($loans->min('disbursementDate')));
        $holidayTo   = date('Y-m-d', strtotime("+10 years", strtotime($loans->max('disbursementDate')))); // here we assume that no loan period is longer than 10 years

        $samityIdsHavingSamityHoliday = DB::table('hr_holidays_special')
            ->where([
                ['is_delete', 0],
                ['sh_date_from', '>=', $holidayFrom],
                ['sh_date_to', '<=', $holidayTo],
                ['samity_id', '>', 0],
            ])
            ->groupBy('samity_id')
            ->pluck('samity_id')
            ->toArray();

        $tempArray = array();
        $currentSamityId = 0;
        foreach ($loans as $key => $loan) {
            $dates = [];


            if ($currentSamityId != $loan->samityId) {
                $currentSamityId        = $loan->samityId;
                self::$samity           = DB::table('mfn_samity')->where('is_delete', 0)->where('id', $loan->samityId)->first();
                self::$samityDayChanges = DB::table('mfn_samity_day_changes')
                    ->where([
                        ['is_delete', 0],
                        ['samityId', $loan->samityId],
                    ])
                    ->get();
            }

            if (count(self::$holidays) == 0 || in_array($loan->samityId, $samityIdsHavingSamityHoliday)) {
                self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            }

            // if it is one time loan
            if ($loan->loanType === 'Onetime') {
                $dates[0] = $loan->firstRepayDate;
                while (in_array($dates[0], self::$holidays)) {
                    $dates[0] = date('Y-m-d', strtotime("+7 day", strtotime($dates[0])));
                }
            } elseif ($loan->loanType === 'Regular') {
                // if it is Daily Loan
                if ($loan->repaymentFrequencyId === 1) {
                    $dates = self::generateDailyInstallmentDates($loan);
                }
                // if it is Weekly Loan
                elseif ($loan->repaymentFrequencyId === 2) {
                    $dates = self::generateWeeklyInstallmentDates($loan);
                }
                // if it is Monthly Loan
                elseif ($loan->repaymentFrequencyId === 4) {
                    $dates = self::generateMonthlyInstallmentDates($loan);
                }
            }

            // array_push($tempArray, [ $loan->id  => $dates ]);

            $tempArray[$loan->id] = $dates;
        }



        return $tempArray;
    }

    public static function generateDailyInstallmentDates($loan)
    {
        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
            return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        });

        $massLoanRescheduleDates = [];

        foreach ($massLoanReschedules as $massLoanReschedule) {
            $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
            while ($rescheduleFrom < $massLoanReschedule->rescheduleTo) {
                array_push($massLoanRescheduleDates, $rescheduleFrom);
                $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
            }
        }

        $installmentDate = $loan->firstRepayDate;
        $dates           = [];
        for ($i = 0; $i < $loan->numberOfInstallment && (self::$loanStatusToDate == null || $installmentDate <= self::$loanStatusToDate); $i++) {
            // reschedule installment
            if (in_array($loan->id, self::$rescheduledLoanIds)) {
                $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
                if ($numberOfTerm > 0) {
                    $installmentDate = date('Y-m-d', strtotime("+" . $numberOfTerm . " day", strtotime($installmentDate)));
                }
            }

            while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
            }
            if (self::$loanStatusToDate != null && $installmentDate > self::$loanStatusToDate) {
                continue;
            }
            $dates[$i]       = $installmentDate;
            $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
        }

        return $dates;
    }

    public static function generateWeeklyInstallmentDates($loan)
    {
        // it should be ensured that first repay date is on samiy day
        $installmentDate = $loan->firstRepayDate;
        $dates           = [];
        $willContinue    = true;

        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
            return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        });

        $massLoanRescheduleDates = [];

        foreach ($massLoanReschedules as $massLoanReschedule) {
            $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
            while ($rescheduleFrom < $massLoanReschedule->rescheduleTo) {
                array_push($massLoanRescheduleDates, $rescheduleFrom);
                $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
            }
        }

        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {

            // reschedule installment
            if (in_array($loan->id, self::$rescheduledLoanIds)) {
                $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
                if ($numberOfTerm > 0) {
                    $installmentDate = date('Y-m-d', strtotime("+" . (7 * $numberOfTerm) . " day", strtotime($installmentDate)));
                }
            }

            $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

            while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates) || $installmentDate < $loan->firstRepayDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
                $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
            }
            if (self::$loanStatusToDate != null && $installmentDate > self::$loanStatusToDate) {
                $willContinue = false;
                continue;
            }
            $dates[$i]       = $installmentDate;
            $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
        }

        return $dates;
    }

    ## generateMonthlyInstallmentDates_samity_day_wise currently active
    public static function generateMonthlyInstallmentDates($loan)
    {
        $installmentDate = $loan->firstRepayDate;
        $monthStartDate  = date("Y-m-01", strtotime($installmentDate));
        $monthEndDate    = date("Y-m-t", strtotime($installmentDate));

        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
            return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        });

        $massLoanRescheduleDates = [];

        foreach ($massLoanReschedules as $massLoanReschedule) {
            $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
            while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
                array_push($massLoanRescheduleDates, $rescheduleFrom);
                $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
            }
        }

        $dates        = [];
        $willContinue = true;
        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {
            // reschedule installment
            if (in_array($loan->id, self::$rescheduledLoanIds)) {
                $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
                if ($numberOfTerm > 0) {
                    $monthStartDate = date('Y-m-d', strtotime("+" . ($numberOfTerm) . " months", strtotime($monthStartDate)));
                    $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

                    $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
                }
            }

            $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

            while ($installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
            }
            while ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
            }

            $initialDate = $installmentDate;

            while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates) || $installmentDate < $loan->firstRepayDate || $installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
                $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
            }

            $progessiveInstallmentDate = $installmentDate;

            if ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($initialDate)));
                while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates) || $installmentDate > $loan->firstRepayDate || $installmentDate > $monthStartDate) {
                    $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
                    $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
                }
            }

            if ($installmentDate < $monthStartDate || $installmentDate < $loan->firstRepayDate) {

                if (self::$regularLoanConfig->monthlyLoanMonthOverflow == 'no') {
                    // set the installment date to the next any working day
                    $installmentDate = $initialDate;
                    while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                        $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
                    }
                    if ($installmentDate > $monthEndDate) {
                        $installmentDate = $initialDate;
                        while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                            $installmentDate = date('Y-m-d', strtotime("-1 day", strtotime($installmentDate)));
                        }
                    }
                    if ($installmentDate < $monthStartDate) {
                        // it means that whole month is holiday,
                        // so $progessiveInstallmentDate will be the $installmentDate
                        $installmentDate = $progessiveInstallmentDate;
                    }
                } elseif (self::$regularLoanConfig->monthlyLoanMOnthOverflow == 'yes') {
                    $installmentDate = $progessiveInstallmentDate;
                }
            }

            ##@Hasib@ need to generate all installment date till 18 but this code gennerating 12 fot todate check

            if (self::$loanStatusToDate != null && $installmentDate > self::$loanStatusToDate) {
                $willContinue = false;
                continue;
            }

            $dates[$i] = $installmentDate;

            $monthStartDate = date('Y-m-d', strtotime("+1 day", strtotime(date("Y-m-t", strtotime($installmentDate)))));
            $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

            $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
        }

        return $dates;
    }

    public static function generateMonthlyInstallmentDates_backup_23102022_samity_day_wise($loan)
    {
        $installmentDate = $loan->firstRepayDate;
        $monthStartDate  = date("Y-m-01", strtotime($installmentDate));
        $monthEndDate    = date("Y-m-t", strtotime($installmentDate));

        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
            return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        });

        $massLoanRescheduleDates = [];

        foreach ($massLoanReschedules as $massLoanReschedule) {
            $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
            while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
                array_push($massLoanRescheduleDates, $rescheduleFrom);
                $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
            }
        }

        $dates        = [];
        $willContinue = true;
        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {
            // reschedule installment
            if (in_array($loan->id, self::$rescheduledLoanIds)) {
                $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
                if ($numberOfTerm > 0) {
                    $monthStartDate = date('Y-m-d', strtotime("+" . ($numberOfTerm) . " months", strtotime($monthStartDate)));
                    $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

                    $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
                }
            }

            $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

            while ($installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
            }
            while ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
            }

            $initialDate = $installmentDate;

            while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates) || $installmentDate < $loan->firstRepayDate || $installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
                $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
            }

            $progessiveInstallmentDate = $installmentDate;

            if ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($initialDate)));
                while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates) || $installmentDate > $loan->firstRepayDate || $installmentDate > $monthStartDate) {
                    $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
                    $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
                }
            }

            if ($installmentDate < $monthStartDate || $installmentDate < $loan->firstRepayDate) {

                if (self::$regularLoanConfig->monthlyLoanMonthOverflow == 'no') {
                    // set the installment date to the next any working day
                    $installmentDate = $initialDate;
                    while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                        $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
                    }
                    if ($installmentDate > $monthEndDate) {
                        $installmentDate = $initialDate;
                        while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                            $installmentDate = date('Y-m-d', strtotime("-1 day", strtotime($installmentDate)));
                        }
                    }
                    if ($installmentDate < $monthStartDate) {
                        // it means that whole month is holiday,
                        // so $progessiveInstallmentDate will be the $installmentDate
                        $installmentDate = $progessiveInstallmentDate;
                    }
                } elseif (self::$regularLoanConfig->monthlyLoanMOnthOverflow == 'yes') {
                    $installmentDate = $progessiveInstallmentDate;
                }
            }

            ##@Hasib@ need to generate all installment date till 18 but this code gennerating 12 fot todate check

            if (self::$loanStatusToDate != null && $installmentDate > self::$loanStatusToDate) {
                $willContinue = false;
                continue;
            }

            $dates[$i] = $installmentDate;

            $monthStartDate = date('Y-m-d', strtotime("+1 day", strtotime(date("Y-m-t", strtotime($installmentDate)))));
            $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

            $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
        }

        return $dates;
    }

    public static function generateMonthlyInstallmentDates_backup_23102023_date_wise($loan)
    {
        $installmentDate = $loan->firstRepayDate;
        $monthStartDate  = date("Y-m-01", strtotime($installmentDate));
        $monthEndDate    = date("Y-m-t", strtotime($installmentDate));

        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // dd(self::$scheduleMethodForHoliday);

        // if (self::$scheduleMethodForHoliday == "next") {
        //     $firstRepayDate = HrService::systemNextWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);
        // } else {
        //     $firstRepayDate = HrService::systemPreviousWorkingDay($firstRepayDate, $samity->branchId, null, $samity->id);
        // }

        // mass reshudule
        $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
            return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        });

        $massLoanRescheduleDates = [];

        foreach ($massLoanReschedules as $massLoanReschedule) {
            $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
            while ($rescheduleFrom < $massLoanReschedule->rescheduleTo) {
                array_push($massLoanRescheduleDates, $rescheduleFrom);
                $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
            }
        }

        $dates        = [];
        $willContinue = true;
        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {
            // reschedule installment
            if (in_array($loan->id, self::$rescheduledLoanIds)) {
                $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
                if ($numberOfTerm > 0) {
                    $monthStartDate = date('Y-m-d', strtotime("+" . ($numberOfTerm) . " months", strtotime($monthStartDate)));
                    $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

                    $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
                }
            }

            // $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
            // while ($installmentDate < $monthStartDate) {
            //     $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
            // }
            // while ($installmentDate > $monthEndDate) {
            //     $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
            // }

            while ($installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
            }
            while ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-1 day", strtotime($installmentDate)));
            }

            $initialDate = $installmentDate;
            while (
                in_array($installmentDate, self::$holidays)
                || in_array($installmentDate, $massLoanRescheduleDates)
                || $installmentDate < $loan->firstRepayDate
                || $installmentDate < $monthStartDate
            ) {
                // $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
                // $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

                ## only holiday hole company configuration check dibe noyto onnno condition er jonno next day hobe.
                ## karon reschedule, firstRepayDate, monthstartdate agulor khetre always next working date hobe logically.
                if (in_array($installmentDate, self::$holidays)) {

                    if (self::$scheduleMethodForHoliday == "next") {
                        $installmentDate = HrService::systemNextWorkingDay($installmentDate, self::$samity->branchId, null, self::$samity->id);
                    } else {
                        $installmentDate = HrService::systemPreviousWorkingDay($installmentDate, self::$samity->branchId, null, self::$samity->id);
                    }
                } else {
                    $installmentDate = HrService::systemNextWorkingDay($installmentDate, self::$samity->branchId, null, self::$samity->id);
                    if (in_array($installmentDate, $massLoanRescheduleDates)) {
                        $monthEndDate   = date("Y-m-t", strtotime($installmentDate));
                    }
                }
            }

            // dd(self::$samity);
            // dd($installmentDate, $monthStartDate, $monthEndDate, $loan->firstRepayDate);

            $progessiveInstallmentDate = $installmentDate;
            ## jehetu installmentDate ti monthEndDate er besi tai always previous working date hobe
            if ($installmentDate > $monthEndDate) {
                // $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($initialDate)));
                $installmentDate = HrService::systemPreviousWorkingDay($initialDate, self::$samity->branchId, null, self::$samity->id);

                while (
                    in_array($installmentDate, self::$holidays)
                    || in_array($installmentDate, $massLoanRescheduleDates)
                    || $installmentDate > $loan->firstRepayDate
                    || $installmentDate > $monthStartDate
                ) {
                    // $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
                    // $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

                    $installmentDate = HrService::systemPreviousWorkingDay($installmentDate, self::$samity->branchId, null, self::$samity->id);
                }
            }

            if ($installmentDate < $monthStartDate || $installmentDate < $loan->firstRepayDate) {

                if (self::$regularLoanConfig->monthlyLoanMonthOverflow == 'no') {
                    // set the installment date to the next any working day
                    $installmentDate = $initialDate;
                    while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                        $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
                    }
                    if ($installmentDate > $monthEndDate) {
                        $installmentDate = $initialDate;
                        while (in_array($installmentDate, self::$holidays) || in_array($installmentDate, $massLoanRescheduleDates)) {
                            $installmentDate = date('Y-m-d', strtotime("-1 day", strtotime($installmentDate)));
                        }
                    }
                    if ($installmentDate < $monthStartDate) {
                        // it means that whole month is holiday,
                        // so $progessiveInstallmentDate will be the $installmentDate
                        $installmentDate = $progessiveInstallmentDate;
                    }
                } elseif (self::$regularLoanConfig->monthlyLoanMOnthOverflow == 'yes') {
                    $installmentDate = $progessiveInstallmentDate;
                }
            }

            ##@Hasib@ need to generate all installment date till 18 but this code gennerating 12 fot todate check

            if (self::$loanStatusToDate != null && $installmentDate > self::$loanStatusToDate) {
                $willContinue = false;
                continue;
            }

            $dates[$i] = $installmentDate;

            $monthStartDate = date('Y-m-d', strtotime("+1 day", strtotime(date("Y-m-t", strtotime($installmentDate)))));
            $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

            $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
        }

        return $dates;
    }

    public static function getLoanReschedulableDate($loanId, $installmentNo, $numberOfTerm, $exceptRescheduleId = null)
    {
        self::$exceptRescheduleId = $exceptRescheduleId;

        $loanId                    = (int) $loanId;
        $reschedule                = new \stdClass();
        $reschedule->loanId        = $loanId;
        $reschedule->installmentNo = $installmentNo;
        $reschedule->numberOfTerm  = $numberOfTerm;

        self::$loanReschedules = collect([
            0 => $reschedule,
        ]);

        $schedules = self::generateLoanSchedule($loanId);

        return $schedules[$installmentNo - 1]['installmentDate'];
    }

    public static function getInterestRateForRegularLoan($productId, $repaymentFrequencyId, $numberOfInstallment, $date)
    {
        $date         = date('Y-m-d', strtotime($date));
        $interestRate = DB::table('mfn_loan_product_interest_rates')
            ->where([
                ['is_delete', 0],
                ['productId', $productId],
                ['repaymentFrequencyId', $repaymentFrequencyId],
                ['numberOfInstallment', $numberOfInstallment],
                ['effectiveDate', '<=', $date],
            ])
            ->where(function ($query) use ($date) {
                $query->where('validTill', '0000-00-00')
                    ->orWhere('validTill', '>=', $date);
            })
            ->first();

        return $interestRate;
    }

    public static function getInterestRateForOnetimeLoan($productId, $date)
    {
        $date         = date('Y-m-d', strtotime($date));
        $interestRate = DB::table('mfn_loan_product_interest_rates')
            ->where([
                ['is_delete', 0],
                ['productId', $productId],
                ['effectiveDate', '<=', $date],
            ])
            ->where(function ($query) use ($date) {
                $query->where('validTill', '0000-00-00')
                    ->orWhere('validTill', '>=', $date);
            })
            ->first();

        return $interestRate;
    }

    /**
     * Get Branch MIS Software Opening date
     */
    public static function getBranchMisSoftwareStartDate($branchID = null)
    {

        $branchModel = 'App\\Model\\GNL\\Branch';
        if ($branchID == null) {
            $branchID = Session::get('LoginBy.user_config.branch_id');
        }

        $BranchData = $branchModel::where('id', $branchID)->first();
        //    $date = new DateTime ();
        //    $date->format('Y-m-d');

        return $BranchData->mfn_start_date;
    }

    public static function getLoanAccounts($filters = [])
    {
        $loans = DB::table('mfn_loans')->where('is_delete', 0);

        if (isset($filters['memberId'])) {
            $loans->where('memberId', $filters['memberId']);
        }
        if (isset($filters['isAuthorized'])) {
            $loans->where('isAuthorized', $filters['isAuthorized']);
        }
        if (isset($filters['status'])) {
            if ($filters['status'] == 'Living') {
                $loans->where('loanStatusId', 4); // 4 = Living
            }
        }
        if (isset($filters['onlyActiveLoan'])) {
            if ($filters['onlyActiveLoan'] == 'yes') {
                if (isset($filters['date'])) {
                    $date = $filters['date'];
                    $loans->where(function ($query) use ($date) {
                        $query->where('loanCompleteDate', '0000-00-00')
                            ->orWhere('loanCompleteDate', '>=', $date);
                    });
                } else {
                    $loans->where('loanCompleteDate', '0000-00-00');
                }
            }
        }
        if (isset($filters['date'])) {
            $loans->where('disbursementDate', '<=', $filters['date']);
        }
        if (isset($filters['overDueLoan'])) {
            if ($filters['overDueLoan'] == 'no') {
                if (isset($filters['date'])) {
                    $date = $filters['date'];
                    $loans->where(function ($query) use ($date) {
                        $query->where('lastInastallmentDate', '>=', $date);
                    });
                }
            }
        }

        return $loans->get();
    }

    public static function getCloseWeekDate($date1 = null, $date2 = null, $freequency = null)
    {

        $weekdate    = new DateTime($date1);
        $openingDate = new DateTime($date2);

        if ($freequency == 3) {
            $weekNum           = round($weekdate->format('d') / 7);
            $openingDayWeekNum = round($openingDate->format('d') / 7);

            if ($openingDayWeekNum >= 5) {
                $openingDayWeekNum = 4;
            }

            if ($weekNum == $openingDayWeekNum) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public static function getCashLedgerId($branchId = null, $projectID = null, $projectTypeID = null, $groupHead = 0, $level = null)
    {
        if (is_null($branchId)) {
            $branchId = Auth::user()->branch_id;
        }

        $accTypeID = 4; // acctype id 4 for cash

        $ledgerData = AccService::getLedgerAccount($branchId, $projectID, $projectTypeID, $accTypeID, $groupHead, $level, "voucherEntry");

        if ($ledgerData->count() > 0) {
            $ledgerId = collect($ledgerData)->first()->id;

            return $ledgerId;
        } else {
            return null;
        }
    }

    public static function sendMail($table = null, $memberId = null, $time = null, $amount = 0, $isUpdate = false)
    {
        if (DB::table('mfn_config')->where('title', 'mail')->first()->content == 'yes') {

            $memberMail = DB::table('mfn_mail_verification')->where('memberId', $memberId);

            if ($memberMail->exists()) {

                if ($memberMail->first()->isVerified == 'yes') {

                    $dataAttachInMessage = array(
                        'amount' => $amount,
                        'date'   => date('d-m-Y', strtotime($time)),
                    );

                    $name  = DB::table('mfn_members')->where('is_delete', 0)->where('id', $memberId)->first()->name;
                    $email = DB::table('mfn_member_details')->where('memberId', $memberId)->first()->email;

                    if ($isUpdate == false) {
                        $subject = __('mail.' . $table . '.insert.subject');
                        $body    = __('mail.' . $table . '.insert.body', $dataAttachInMessage);
                    } else {

                        $subject = __('mail.' . $table . '.update.subject');
                        $body    = __('mail.' . $table . '.update.body', $dataAttachInMessage);
                    }

                    SendMailJob::dispatch($email, $subject, $name, $body)->delay(now()->addMinutes(2));
                }
            }
        }
    }

    public static function getSavingsAccountsBalance($branchId)
    {
        $accounts = DB::table('mfn_savings_accounts as msa')
            ->where([
                ['msa.is_delete', 0],
                ['msa.branchId', $branchId],
            ])
            ->leftjoin('mfn_members as mm', 'msa.memberId', 'mm.id')
            ->select('msa.id as accountId', 'msa.branchId', 'msa.samityId', 'msa.memberId', 'mm.name as memberName')
            ->get();

        // $accountIds = $accounts->pluck('accountId')->all();

        $savingAccountInfo = array();

        foreach ($accounts as $key => $account) {

            $deposit = DB::table('mfn_savings_deposit')
                ->where([
                    ['is_delete', 0],
                    ['accountId', $account->accountId],
                    ['branchId', $account->branchId],
                    ['samityId', $account->samityId],
                    ['memberId', $account->memberId],
                ])
                ->sum('amount');

            $withdraw = DB::table('mfn_savings_withdraw')
                ->where([
                    ['is_delete', 0],
                    ['accountId', $account->accountId],
                    ['branchId', $account->branchId],
                    ['samityId', $account->samityId],
                    ['memberId', $account->memberId],
                ])
                ->sum('amount');

            $savingAccountInfo[$key]['accountId']  = $account->accountId;
            $savingAccountInfo[$key]['branchId']   = $account->branchId;
            $savingAccountInfo[$key]['samityId']   = $account->samityId;
            $savingAccountInfo[$key]['memberId']   = $account->memberId;
            $savingAccountInfo[$key]['memberName'] = $account->memberName;
            $savingAccountInfo[$key]['balance']    = (is_null($deposit) ? 0 : floatval($deposit)) - (is_null($withdraw) ? 0 : floatval($withdraw));
        }

        return $savingAccountInfo;
    }

    /**
     * It returns the primary product of a member
     * if date is null then it returns the current primary product
     * if date is given, then it returns primary product id of the member on particular date
     *
     * @param [int or array if int] $memberIdOrIds
     * @param [null or date] $date
     * @return [if $memberIdOrIds is int then it will return int, if $memberIdOrIds is an array then it will retuen an array where index is the member id and vale is the primaryProductId, ['memberId' => 'primaryProductId'] ]
     */
    public static function getMemberPrimaryProductId($memberIdOrIds, $date = null)
    {
        $memberIds = $memberIdOrIds;
        if (!is_array($memberIdOrIds)) {
            $memberIds = [$memberIdOrIds];
        }

        $memberProductIds = DB::table('mfn_members')
            ->whereIn('id', $memberIds)
            ->where('is_delete', 0)
            ->pluck('primaryProductId', 'id')
            ->toArray();

        if ($date == null) {
            if (is_int($memberIdOrIds)) {
                return $memberProductIds[$memberIdOrIds];
            }
            return $memberProductIds;
        }

        $productTransfers = DB::table('mfn_member_primary_product_transfers')
            ->where([
                ['is_delete', 0],
                ['transferDate', '>', $date],
            ])
            ->whereIn('memberId', $memberIds)
            // ->orderBy('transferDate')
            ->select('memberId', 'oldProductId', 'transferDate')
            ->get();

        foreach ($memberProductIds as $memberId => $productId) {
            if ($productTransfers->where('memberId', $memberId)->first() != null) {
                $closestTransferDate = $productTransfers->where('memberId', $memberId)->min('transferDate');

                $memberProductIds[$memberId] = $productTransfers->where('memberId', $memberId)->where('transferDate', $closestTransferDate)->first()->oldProductId;
            }
        }

        if (is_int($memberIdOrIds)) {
            return $memberProductIds[$memberIdOrIds];
        }
        return $memberProductIds;
    }

    public static function getBranchAssignedLoanProductIds($branchIdOrIds)
    {
        if (!is_array($branchIdOrIds)) {
            $branchIds = [$branchIdOrIds];
        } else {
            $branchIds = $branchIdOrIds;
        }

        $branchProducts = DB::table('mfn_branch_products')
            ->whereIn('branchId', $branchIds)
            ->pluck('loanProductIds')
            ->toArray();

        $productIds = [];

        foreach ($branchProducts as $key => $branchProduct) {
            $productIds = array_merge($productIds, array_map('intval', json_decode($branchProduct)));
        }

        return $productIds;
    }

    public static function getBranchAssignedSavProductIds($branchIdOrIds)
    {
        if (!is_array($branchIdOrIds)) {
            $branchIds = [$branchIdOrIds];
        } else {
            $branchIds = $branchIdOrIds;
        }

        $branchProducts = DB::table('mfn_branch_products')
            ->whereIn('branchId', $branchIds)
            ->pluck('savingProductIds')
            ->toArray();

        $productIds = [];

        foreach ($branchProducts as $key => $branchProduct) {
            $productIds = array_merge($productIds, array_map('intval', json_decode($branchProduct)));
        }

        $productIds = array_unique($productIds);

        return $productIds;
    }

    /**
     * [setOpeningBalanceForOneTimeSavings description]
     *
     * @param   int  $branchId
     *
     * @return  [void]
     */
    public static function setOpeningBalanceForOneTimeSavings($branchId, $accountId = null)
    {
        $branch = DB::table('gnl_branchs')
            ->where('is_delete', 0)
            ->where('is_active', 1)
            ->where('is_approve', 1)
            ->where('id', $branchId)
            ->first();

        if ($branch == null) {
            return null;
        }

        $oneTimeProductIds = DB::table('mfn_savings_product')
            ->where('productTypeId', 2)
            ->where('is_delete', 0)
            ->pluck('id')
            ->toArray();

        $savAccs = DB::table('mfn_savings_accounts')
            ->where([
                ['is_delete', 0],
                ['branchId', $branchId],
                ['openingDate', '<=', $branch->mfn_start_date],
                ['isOpening', 1],
            ])
            ->whereIn('savingsProductId', $oneTimeProductIds);

        if ($accountId != null) {
            $savAccs->where('id', $accountId);
        }

        $savAccs = $savAccs->get();

        // store or update opening and deposit for one time savings
        foreach ($savAccs as $key => $savAcc) {
            $memberPrimaryProductId = self::getMemberPrimaryProductId($savAcc->memberId, $savAcc->openingDate);

            // insert opening balance information
            DB::table('mfn_savings_opening_balance')
                ->updateOrInsert(
                    ['accountId' => $savAcc->id],
                    [
                        'memberId'       => $savAcc->memberId,
                        'samityId'       => $savAcc->samityId,
                        'branchId'       => $savAcc->branchId,
                        'depositAmount'  => $savAcc->autoProcessAmount,
                        'interestAmount' => 0,
                        'withdrawAmount' => 0,
                        'openingBalance' => $savAcc->autoProcessAmount,
                        'created_at'     => date('Y-m-d H:m:s'),
                        'created_by'     => 0,
                    ]
                );

            DB::table('mfn_savings_deposit')
                ->updateOrInsert(
                    ['accountId' => $savAcc->id, 'is_delete' => 0, 'transactionTypeId' => 4], // 4 is for opening balance
                    [
                        'memberId'         => $savAcc->memberId,
                        'samityId'         => $savAcc->samityId,
                        'branchId'         => $savAcc->branchId,
                        'primaryProductId' => $memberPrimaryProductId,
                        'savingsProductId' => $savAcc->savingsProductId,
                        'amount'           => $savAcc->autoProcessAmount,
                        'date'             => $savAcc->openingDate,
                        'ledgerId'         => 0,
                        'isAuthorized'     => 1,
                        'created_at'       => date('Y-m-d H:m:s'),
                        'created_by'       => 0,
                    ]
                );
        }
    }

    public static function getFieldOfficersByDate($samityIdOrIds, $startDate, $endDate)
    {
        $startDate = (new DateTime($startDate))->format('Y-m-d');
        $endDate = (new DateTime($endDate))->format('Y-m-d');

        if (is_array($samityIdOrIds)) {
            $samityIds = $samityIdOrIds;
        } else {
            $samityIds = [$samityIdOrIds];
        }

        $samities = DB::table('mfn_samity')
            ->whereIn('id', $samityIds)
            ->where('is_delete', 0)
            ->select('id', 'fieldOfficerEmpId')
            ->get();

        $fieldOfficerChanges = DB::table('mfn_samity_field_officer_change')
            ->where([
                ['is_delete', 0],
                ['effectiveDate', '>=', $startDate],
                ['effectiveDate', '<=', $endDate],
            ])
            ->orderBy('samityId', 'asc')
            ->orderBy('effectiveDate', 'asc')
            ->whereIn('samityId', $samityIds)
            ->get();

        $samities = $samities->whereNotIn('id', $fieldOfficerChanges->pluck('samityId')->toArray());

        $data = array();

        foreach ($samities as $key => $samity) {
            $info['fieldOfficerId'] = $samity->fieldOfficerEmpId;
            $info['samityId']       = $samity->id;
            $info['dateFrom']       = $startDate;
            $info['dateTo']         = $endDate;
            array_push($data, $info);
        }

        foreach ($fieldOfficerChanges as $key => $fieldOfficerChange) {
            // for old field officer
            $info['fieldOfficerId'] = $fieldOfficerChange->oldFieldOfficerEmpId;
            $info['samityId']       = $fieldOfficerChange->samityId;
            $wouldPush              = true;

            $dateFrom = $startDate;
            if (isset($fieldOfficerChanges[$key - 1])) {
                if ($fieldOfficerChanges[$key - 1]->samityId == $fieldOfficerChange->samityId) {
                    $dateFrom  = $fieldOfficerChanges[$key - 1]->effectiveDate;
                    $wouldPush = false;
                }
            }
            $info['dateFrom'] = $dateFrom;

            $dateTo         = date('Y-m-d', strtotime('-1 days', strtotime($fieldOfficerChange->effectiveDate)));
            $info['dateTo'] = $dateTo;

            if ($wouldPush) {
                // if($dateTo <= $endDate)
                if ($dateTo >= $startDate && $dateTo <= $endDate) {
                    array_push($data, $info);
                }
            }

            // for new field officer
            $info['fieldOfficerId'] = $fieldOfficerChange->newFieldOfficerEmpId;
            $info['samityId']       = $fieldOfficerChange->samityId;

            $dateFrom         = $fieldOfficerChange->effectiveDate;
            $info['dateFrom'] = $dateFrom;

            $dateTo = $endDate;
            if (isset($fieldOfficerChanges[$key + 1])) {
                if ($fieldOfficerChanges[$key + 1]->samityId == $fieldOfficerChange->samityId) {
                    $dateTo = date('Y-m-d', strtotime('-1 days', strtotime($fieldOfficerChanges[$key + 1]->effectiveDate)));
                }
            }
            $info['dateTo'] = $dateTo;

            array_push($data, $info);
        }

        return $data;
    }

    /**
     * This function returns an array with 'status' and 'message' index.
     *
     * Parameters are-
     * $parameters['branchId']
     * $parameters['dateFrom']
     * $parameters['dateTo']
     */
    public function anyDayEndExist($parameters)
    {
        $status  = false;
        $messgae = '';

        $dayEnds = DB::table('mfn_day_end')
            ->where([
                ['is_delete', 0],
                ['date', '>=', $parameters['dateFrom']],
            ]);

        if (isset($parameters['branchId'])) {
            $dayEnds->where('branchId', $parameters['branchId']);
        }

        if (isset($parameters['dateTo'])) {
            $dayEnds->where('date', '<=', $parameters['dateTo']);
        }

        $dayEnds = $dayEnds->get();

        if (count($dayEnds)) {
            $branches = DB::table('gnl_branchs')
                ->where('is_delete', 0)
                ->where('is_active', 1)
                ->where('is_approve', 1)
                ->whereIn('id', $dayEnds->pluck('branchId'))
                ->select(DB::raw("CONCAT(branch_name, ' [', branch_code,']') AS name"))
                ->get();

            $branchNames = implode(', ', $branches->pluck('name')->toArray());

            $status  = true;
            $messgae = "Day End Exists for $branchNames";
        }

        $data = array(
            'status'  => $status,
            'messgae' => $messgae,
        );

        return $data;
    }

    /**
     * This function returns an array with 'status' and 'message' index.
     *
     * Parameters are-
     * $parameters['branchId']
     * $parameters['samityId']
     * $parameters['dateFrom']
     * $parameters['dateTo']
     */
    public function anyTransactionExist($parameters)
    {
        $status  = false;
        $messgae = '';

        $tables = array(
            'mfn_samity'                    => 'openingDate',
            'mfn_samity_closing'            => 'closingDate',
            'mfn_samity_day_changes'        => 'effectiveDate',
            'mfn_members'                   => 'admissionDate',
            'mfn_member_closings'           => 'closingDate',
            'mfn_loans'                     => 'disbursementDate',
            'mfn_loan_collections'          => 'collectionDate',
            'mfn_loan_writeoff_collections' => 'date',
            'mfn_savings_accounts'          => 'openingDate',
            'mfn_savings_closings'          => 'closingDate',
            'mfn_savings_deposit'           => 'date',
            'mfn_savings_withdraw'          => 'date',
            'mfn_savings_provision'         => 'provisionDate',
        );

        $aaditionalConditions = array(
            'mfn_loan_collections' => [['amount', '!=', 0]],
            'mfn_savings_deposit'  => [['amount', '!=', 0]],
            'mfn_savings_withdraw' => [['amount', '!=', 0]],
        );

        foreach ($tables as $table => $dateField) {
            $transactions = DB::table($table)
                ->where([
                    ['is_delete', 0],
                    ['branchId', 0],
                ])
                ->where($dateField, '>=', $parameters['dateFrom']);

            if (isset($aaditionalConditions[$table])) {
                $transactions->where($aaditionalConditions[$table]);
            }
        }
    }

    //MIP provision check
    public static function MIPcheck($branchId, $date)
    {
        return [
            'alert-type' => 'success',
        ];

        //get MIP accounts. if exist then go through check
        $savAccs = DB::table('mfn_savings_accounts')
            ->where([
                ['is_delete', 0],
                ['branchId', $branchId],
                ['openingDate', '<', $date],
                ['savingsProductId', 7],
            ])
            ->where(function ($query) use ($date) {
                $query->where('closingDate', '0000-00-00')
                    ->orWhere('closingDate', '>=', $date);
            })
            ->get();

        if ($savAccs->count() == 0) {
            //do not need to do provision
            return [
                'alert-type' => 'success',
            ];
        }

        $config = DB::table('mfn_config')->where('title', 'OneTimeSavingsProductProvisionFrequency')->first();

        if ($config->content == 'Daily' || $config->content == 'Monthly') {
            $MIPprovisionToday = DB::table('mfn_savings_provision')
                ->where([
                    ['branchId', $branchId],
                    ['provisionDate', $date],
                    ['productId', 7],
                    ['is_delete', 0],
                ])
                ->get();

            if (count($MIPprovisionToday) == 0) {
                $notification = array(
                    'alert-type' => 'error',
                    'message'    => 'MIP provision is not completed',
                );
                return $notification;
            } else {
                return [
                    'alert-type' => 'success',
                ];
            }
        }
    }

    ## This function is used to check if tx exists under an employee
    ## before transfer/termination
    public static function checkTransactionForEmployee($employeeId, $action = "terminating")
    {
        $moduleFlag = false;
        $errMessage = '';

        if (Common::checkActivatedModule('mfn')) {
            $moduleFlag = true;
        }

        ## write code for checking transaction
        if ($moduleFlag == true) {

            $isAssingedToSamity = DB::table('mfn_samity')
                ->where([
                    ['is_delete', 0],
                    ['fieldOfficerEmpId', $employeeId],
                    ['closingDate', '0000-00-00'],
                ])
                ->exists();
            if ($isAssingedToSamity) {
                $errMessage = 'This employee is assigned to samity as Credit Officer.';
            }
        }

        if ($errMessage != '') {
            $errMessageTxt = "This Employee has transaction in MFN Module. Please Transfer/Remove Transaction before " . $action . " this employee." . $errMessage;
        } else {
            $errMessageTxt = $errMessage;
        }
        return $errMessageTxt;
    }

    public static function fnForBranchZoneAreaWise($branchId = null, $zoneId = null, $areaId = null, $companyID = null)
    {
        $selBranchArr = Common::fnForBranchZoneAreaWise($branchId, $zoneId, $areaId, $companyID);

        return $selBranchArr;
    }

    public static function fnForMemberData($arrayIDs, $withCode = true)
    {
        $queryData = array();
        $queryData = DB::table('mfn_members')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->when(true, function($query) use ($withCode){
                if($withCode)
                {
                    $query->selectRaw('CONCAT(name, " [", memberCode, "]") AS memberName, id');
                }
                else
                {
                    $query->selectRaw('name as memberName, memberCode, id');
                }
            })
            ->pluck('memberName', 'id')
            ->toArray();
        return $queryData;
    }

    public static function getMemberInformationDataset($arrayIDs)
    { ## updated function for fnForMemberData()
        $queryData = array();
        $queryData = DB::table('mfn_members')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('id, name, memberCode, gender, admissionDate, closingDate, status,
                CONCAT(name, " [", memberCode, "]") AS nameCode')
            ->get([
                'id', 'name', 'memberCode', 'memberName', 'gender',
                'admissionDate', 'closingDate', 'status', 'nameCode'
            ])
            ->keyBy('id')
            ->toArray();

        return $queryData;
    }

    public static function fnForLoanStatusData($arrayIDs)
    { ## Depricated
        $queryData = array();
        $queryData = DB::table('mfn_loan_status')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('name, id')
            ->pluck('name', 'id')
            ->toArray();
        return $queryData;
    }

    public static function getLoanStatusDataset($arrayIDs)
    { ## updated function for getLoanStatusDataset()
        $queryData = array();
        $queryData = DB::table('mfn_loan_status')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('name, id, shortName')
            ->get(['id', 'name', 'shortName'])
            ->keyBy('id')
            ->toArray();

        return $queryData;
    }

    public static function fnForSavingsProductData($arrayIDs)
    { ## Depricated
        $queryData = array();
        $queryData = DB::table('mfn_savings_product')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('CONCAT(name, " [", productCode, "]") AS name, id')
            ->pluck('name', 'id')
            ->toArray();
        return $queryData;
    }

    public static function getSavingsProductDataset($arrayIDs)
    { ## updated function for fnForSavingsProductData()
        $queryData = array();
        $queryData = DB::table('mfn_savings_product')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('id, name, shortName, productCode, CONCAT(name, " [", productCode, "]") AS nameCode, productTypeId')
            ->get(['id', 'name', 'shortName', 'productCode', 'nameCode', 'productTypeId'])
            ->keyBy('id')
            ->toArray();
        return $queryData;
    }

    public static function fnForSamityData($arrayIDs)
    { ## Depricated
        $queryData = array();
        $queryData = DB::table('mfn_samity')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('CONCAT(name, " [", samityCode, "]") AS samityName, id')
            ->pluck('samityName', 'id')
            ->toArray();
        return $queryData;
    }

    /**
     * @param $byCurrentFieldOfficer = values("yes", "no")
     * We know samity's feild officer changable and we need to samity data view feild officer wise.
     * But sometime we don't need to divided data between feild officer. All data view in current feild officer's name.
     * Thats why we use $byCurrentFieldOfficer variable for flag.
     * $byCurrentFieldOfficer = Show report by current field officer
     */
    public static function getSamityInformationDataset($arrayIDs, $fromDate = null, $toDate = null, $byCurrentFieldOfficer = null)
    { ## updated function for fnForSamityData()
        $queryData = array();
        $queryData = DB::table('mfn_samity')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('id, name, samityCode, openingDate, samityType, samityDay, fieldOfficerEmpId as currentfieldOfficerId, CONCAT(name, " [", samityCode, "]") AS nameCode')
            ->orderBy('id', 'ASC')
            ->get(['id', 'name', 'samityCode', 'openingDate', 'samityType', 'samityDay', 'currentfieldOfficerId', 'nameCode'])
            ->keyBy('id')
            ->toArray();

        if (count($queryData) < 1) {
            return $queryData;
        }

        if ($byCurrentFieldOfficer == 'yes') {

            $queryData = array_map(function ($row) use ($fromDate, $toDate) {

                if (empty($fromDate) && empty($toDate)) {
                    $fromDate = $row->openingDate;
                    $toDate = (new DateTime('now'))->format('Y-m-d');
                }

                $fieldOfficers = array();
                $fieldOfficers[] = [
                    'fieldOfficerId' => $row->currentfieldOfficerId,
                    'samityId' => $row->id,
                    'dateFrom' => $fromDate,
                    'dateTo' => $toDate,
                ];

                $row->fieldOfficers = $fieldOfficers;
                return $row;
            }, $queryData);
        } elseif ($byCurrentFieldOfficer == 'no' && !empty($fromDate) && !empty($toDate)) {

            ## Samity Wise FeildOfficer
            $fieldOfficersSamity_DateWise =  self::getFieldOfficersByDate(array_keys($queryData), $fromDate, $toDate);

            if (count($fieldOfficersSamity_DateWise) > 0) {
                $queryData = array_map(function ($row) use ($fieldOfficersSamity_DateWise) {

                    $fieldOfficers = array();
                    $samityId = $row->id;

                    $fieldOfficers = array_filter($fieldOfficersSamity_DateWise, function ($x) use ($samityId) {
                        return $x['samityId'] == $samityId;
                    });

                    $row->fieldOfficers = array_values($fieldOfficers);

                    return $row;
                }, $queryData);
            }
        }

        return $queryData;
    }

    public static function fnFoLoanProductData($arrayIDs)
    { ## Depricated
        $queryData = array();
        $queryData = DB::table('mfn_loan_products')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('CONCAT(name, " [", productCode, "]") AS name, id')
            ->pluck('name', 'id')
            ->toArray();
        return $queryData;
    }

    public static function getLoanProductDataset($arrayIDs)
    { ## updated function for fnFoLoanProductData()
        $queryData = array();
        $queryData = DB::table('mfn_loan_products')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('id, name, shortName, productCode, CONCAT(name, " [", productCode, "]") AS nameCode')
            ->get([
                'id', 'name', 'shortName', 'productCode', 'nameCode'
            ])
            ->keyBy('id')
            ->toArray();

        return $queryData;
    }

    public static function getRepaymentFrequencyDataset($arrayIDs)
    {
        $queryData = array();
        $queryData = DB::table('mfn_loan_repayment_frequency')
            ->where([['is_delete', 0], ['status', 1]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('id, name, durationInDays')
            ->get([
                'id', 'name', 'durationInDays'
            ])
            ->keyBy('id')
            ->toArray();

        return $queryData;
    }

    public static function fnForTransactionTypeData($arrayIDs)
    {
        $queryData = array();
        $queryData = DB::table('mfn_savings_transaction_types')
            ->whereIn('id', $arrayIDs)
            ->selectRaw('name, id')
            ->pluck('name', 'id')
            ->toArray();
        return $queryData;
    }

    public static function fnForSavingsAccCodeData($arrayIDs)
    {
        $queryData = array();
        $queryData = DB::table('mfn_savings_accounts')
            ->where([['is_delete', 0]])
            ->whereIn('id', $arrayIDs)
            ->selectRaw('accountCode, id')
            ->pluck('accountCode', 'id')
            ->toArray();
        return $queryData;
    }

    public static function getInformationForSummaryData($parameter = [], $searchBy = 3, $fromData, $toDate, $fiscalYearId = null)
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
            $selBranchArr = Common::getBranchIdsForAllSection([
                'companyId'     => $companyId,
                'projectId'     => $projectId,
                'projectTypeId' => $projectTypeId,
                'branchId'      => $branchId
            ]);
        }

        ## ##### change kora hoyeche ignore branch kaj korchilo na tai
        // $activeBranchArr = $selBranchArr;

        if ($branchId == -1 || $branchId == -2 || empty($branchId)) {
            $branchId = Common::getBranchId();
        }

        $brOpeningDate   = new DateTime(Common::getBranchSoftwareStartDate($branchId, 'mfn'));
        $loginSystemDate = new DateTime(Common::systemCurrentDate($branchId, 'mfn'));

        $current_fiscal_year = Common::systemFiscalYear('', $companyId, $branchId, 'mfn');
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

        if ($searchBy == 3 && $fromData != false && $toDate != false) {
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
            // $searching_fiscal_id = Common::systemFiscalYear($startDateDR->format('Y-m-d'), $companyId, $branchId, 'mfn')['id'];

        }

        $onPeriodDataFetchFromArr = array();

        // dd($selBranchArr, $activeBranchArr);

        if ($onPeriodDataFetch == true) {

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
                $onPeriodWorkingArr = HrService::systemWorkingDay("branch", [
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
                $beforePeriodWorkingArr = HrService::systemWorkingDay("branch", [
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

    public static function getSamityByFieldOfficer($feildOfficer, $fromDate = null, $toDate = null)
    {

        $queryForFOTransfer = array();
        if (!empty($feildOfficer) && !empty($fromData) && !empty($toDate)) {
            $queryForFOTransfer = DB::table("mfn_samity_field_officer_change")
                ->where([['is_delete', 0], ['oldFieldOfficerEmpId', $feildOfficer]])
                ->whereBetween('effectiveDate', [$fromDate, $toDate])
                ->orderBy('samityId', 'ASC')
                ->pluck("samityId")
                ->toArray();
        }

        $query = DB::table("mfn_samity")
            ->where([["is_delete", 0]])
            ->where(function ($query) use ($feildOfficer, $queryForFOTransfer) {
                $query->where("fieldOfficerEmpId", $feildOfficer);
                if (count($queryForFOTransfer) > 0) {
                    $query->orWhereIn("id", $queryForFOTransfer);
                }
            })
            ->where(function ($query) use ($toDate) {
                if (!empty($toDate)) {
                    $query->where("closingDate", "0000-00-00");
                    $query->orWhere("closingDate", "<=", $toDate);
                }
            })
            ->orderBy('id', 'ASC')
            ->selectRaw("CONCAT(name, ' [', samityCode, ']') AS name, id")
            ->get();
        // ->pluck('id')
        // ->toArray();

        // $samityIds = array_unique(array_merge($query, $queryForFOTransfer));
        // $samityIds = $query;

        return $query;
    }

    public static function getLoanProducts($branchIds = [], $fundingOrgId = null, $productCategory = null, $productId = null)
    {
        /*
            branch wise loan product fetch kora hoyehe ja only transaction er somoy use hobe,
            report new function use hobe ja brnach er product er opor depend korbe na.
        */

        $query = DB::table('mfn_loan_products')->where([['is_delete', 0], ['status', 1]])
            ->where(function ($query) use ($branchIds, $fundingOrgId, $productCategory, $productId) {
                if (count($branchIds) > 0) {
                    $branchWiseProductIds =  self::getBranchAssignedLoanProductIds($branchIds);
                    $query->whereIn('id', $branchWiseProductIds);
                }

                if (!empty($fundingOrgId)) {
                    $query->where('fundingOrgId', $fundingOrgId);
                }

                if (!empty($productCategory)) {
                    $query->where('productCategoryId', $productCategory);
                }

                if (!empty($productId)) {
                    $query->where('id', $productId);
                }
            })
            ->selectRaw("id, CONCAT(name, ' [', productCode, ']') as name")
            ->get();

        return $query;
    }

    public static function getLoanProductsAll($productCategory = null, $productId = null)
    {
        ## Branch wise product load hocche na
        $query = DB::table('mfn_loan_products')->where([['is_delete', 0], ['status', 1]])
            ->where(function ($query) use ($productCategory, $productId) {

                if (!empty($productCategory)) {
                    $query->where('productCategoryId', $productCategory);
                }

                if (!empty($productId)) {
                    $query->where('id', $productId);
                }
            })
            ->selectRaw("id, CONCAT(name, ' [', productCode, ']') as name")
            ->get();
        return $query;
    }

    public static function getUserAccesableSamityIds()
    {
        $samityIds = [];

        if (Auth::user()->branch_id == 1) {
            $samityIds = DB::table('mfn_samity')
                ->where('is_delete', 0)
                ->pluck('id')
                ->toArray();
        } else {

            $empId = Auth::user()->emp_id;

            if (!empty($empId)) {
                $employeeData = DB::table("hr_employees")->where([['is_active', 1], ['is_delete', 0], ['id', $empId]])->selectRaw('id, branch_id, designation_id')->first();

                if (!empty($employeeData)) {

                    $designationId = $employeeData->designation_id;
                    $branchIds = HrService::getUserAccesableBranchIds();

                    $samityIds = DB::table('mfn_samity')
                        ->where('is_delete', 0)
                        ->whereIn('branchId', $branchIds)
                        ->pluck('id')
                        ->toArray();

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

                        if ($positionId == 1) { # Credit Officer
                            $samityIds = DB::table('mfn_samity')
                                ->where('is_delete', 0)
                                ->whereIn('branchId', $branchIds)
                                ->whereIn('fieldOfficerEmpId', $empId)
                                ->pluck('id')
                                ->toArray();
                        }
                    }
                }
            }
        }

        return $samityIds;
    }
}
