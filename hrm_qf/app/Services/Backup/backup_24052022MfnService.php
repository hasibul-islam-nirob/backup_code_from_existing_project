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

class backup_24052022MfnService
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
    }

    public static function systemCurrentDate($branchId)
    {
        $sysDate = null;
        if(DB::getSchemaBuilder()->hasTable('mfn_day_end')){
            $sysDate = DB::table('mfn_day_end')
                ->where([
                    ['branchId', $branchId],
                    ['isActive', 1],
                ])
                ->where('is_delete', 0)
                ->max('date');
        }
        
        if ($sysDate == null) {
            $sysDate = DB::table('gnl_branchs')
                ->where('id', $branchId)
                ->where([['is_delete', 0], ['is_active', 1], ['is_approve', 1]])
                ->first()
                ->mfn_start_date;
        }

        return $sysDate;
    }

    public static function getFieldOfficers($branchId)
    {
        $fieldOfficersDesignationIds = json_decode(DB::table('mfn_config')->where('title', 'fieldOfficerHrDesignationIds')->first()->content);

        $filedOfficers = DB::table('hr_employees')
            ->where([
                ['is_delete', 0],
                ['is_active', 1],
                ['branch_id', $branchId],
            ])
            ->whereIn('designation_id', $fieldOfficersDesignationIds)
            ->select(DB::raw("CONCAT(emp_name, ' [', emp_code, ']') AS name, id"))
            ->get();

        return $filedOfficers;
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

    public static function getWorkingWeekDays()
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
                $sysDate = self::systemCurrentDate($temp->first()->branchId);
                $savAccs->where('openingDate', '<=', $sysDate);
            }
        }
        if (isset($filters['onlyActiveAccounts'])) {
            if ($filters['onlyActiveAccounts'] == 'yes') {
                $savAccs->where('closingDate', '0000-00-00');
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
        $deposit  = DB::table('mfn_savings_deposit')->where('is_delete', 0)->whereNotIn('transactionTypeId', [8, 9]);
        $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0)->whereNotIn('transactionTypeId', [8, 9]);

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

        $balance = $deposit->sum('amount') - $withdraw->sum('amount');

        if (isset($filers['neglectAmount'])) {
            $balance -= $filers['neglectAmount'];
        }

        return $balance;
    }

    public static function getSavingsWithdraw($filers = [])
    {
        $withdraw = DB::table('mfn_savings_withdraw')->where('is_delete', 0)->whereIn('transactionTypeId', [1, 2, 4, 6, 7]);

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
        $deposit = DB::table('mfn_savings_deposit')->where('is_delete', 0)->whereIn('transactionTypeId', [1, 2, 4, 6, 7]);
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
            ->select(DB::raw("m.id, m.name, m.memberCode, CONCAT(m.name,' - ',m.memberCode) as member, CONCAT(b.branch_name,' - ',b.branch_code) as branch, CONCAT(s.name,' - ',s.samityCode) as samity, s.workingAreaId"));

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

    public static function getFirstRepayDate($samityId, $loanProductId, $disbursementDate, $repaymentFrequencyId = null, $periodMonth = null)
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

                    if(count($isHolidayArr) > 0){
                        $firstRepayDate = Carbon::parse($firstRepayDate)->addDays(7)->format('Y-m-d');
                        $firstRepayDate = self::getSamityDateOfWeek($samityId, $firstRepayDate);
                        
                        ## While holiday exists on repay date && firstRepayDate and taget date on same month
                        while ( Carbon::parse($firstRepayDate)->format('Y-m') == Carbon::parse($targetDate)->format('Y-m') && 
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
                    if ($firstRepayDateActual->format('Y-m') != (new DateTime($firstRepayDate))->format('Y-m')
                        && $firstRepayDateActual->format('Y-m') != (new DateTime($disbursementDate))->format('Y-m')) {
                        $firstRepayDate = $firstRepayDateActual->format('Y-m-d');
                    }
                }
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
            
        } elseif($samityDay == "Daily") {

            $samityDate = date('Y-m-d', strtotime($date));
            // $samityDate = "2022-02-21";

            $isHolidayArr = HrService::systemHolidays(null, $samity->branchId, $samity->id, $samityDate, $samityDate);

            if(count($isHolidayArr) > 0){
                $samityDate = HrService::systemNextWorkingDay($samityDate, $samity->branchId, null, $samity->id);
                $samityDate = date('Y-m-d', strtotime($samityDate));
            }
            
        }
        else {
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
            ->select('id', 'loanAmount', 'repayAmount', 'samityId', 'productId', 'installmentAmount', 'lastInstallmentAmount')
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
                $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($loanStatuses[$key]['onPeriodReularCollection'] / $loans[$loanStatus['loanId']]->installmentAmount);

                if ($loanStatuses[$key]['isLastInstallmentPresent'] && ($loanStatuses[$key]['onPeriodReularCollection'] % $loans[$loanStatus['loanId']]->installmentAmount >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)) {
                    $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                }
            }
        }

        return $loanStatuses;
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
    public static function getLoanStatusAll($loanIdOrIds, ...$dates)
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

        $loanStatuses = self::generateLoanSchedule($loanIdOrIds, ...$dates); #@
        // $loanStatuses = self::generateLoanScheduleModified($loanIdOrIds, ...$dates);

        

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

                $onPeriodPaidAmount          = $loanCollectionOnPeriod->where('loanId', $loanStatus['loanId'])->sum('amount');
                $onPeriodPaidAmountPrincipal = $loanCollectionOnPeriod->where('loanId', $loanStatus['loanId'])->sum('principalAmount');

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
                $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($loanStatuses[$key]['onPeriodReularCollection'] / $loans[$loanStatus['loanId']]->installmentAmount);

                if ($loanStatuses[$key]['isLastInstallmentPresent'] && ($loanStatuses[$key]['onPeriodReularCollection'] % $loans[$loanStatus['loanId']]->installmentAmount >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)) {
                    $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                }
            }
        }

        return $loanStatuses;
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
        }

        $loanCollections = $loanCollections->get();

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

            if(isset($loanCollectionLoanIdWise[$loanStatus['loanId']])) {
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

                if(isset($loanCollectionOnPeriodIdWise[$loanStatus['loanId']])) {
                    $onPeriodPaidAmount          = $loanCollectionOnPeriodIdWise[$loanStatus['loanId']]->sum('amount');
                    $onPeriodPaidAmountPrincipal = $loanCollectionOnPeriodIdWise[$loanStatus['loanId']]->sum('principalAmount');
                }


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
                $loanStatuses[$key]['numberOfFullyPaidRegularCollection'] = (int) ($loanStatuses[$key]['onPeriodReularCollection'] / $loans[$loanStatus['loanId']]->installmentAmount);

                if ($loanStatuses[$key]['isLastInstallmentPresent'] && ($loanStatuses[$key]['onPeriodReularCollection'] % $loans[$loanStatus['loanId']]->installmentAmount >= $loans[$loanStatus['loanId']]->lastInstallmentAmount)) {
                    $loanStatuses[$key]['numberOfFullyPaidRegularCollection']++;
                }
            }
        }
        // dd(1);
        return $loanStatuses;
    }

    ## This Function is called for scheduling in all reports except col sheet
    public static function generateLoanSchedule($loanIdOrIds, ...$dates)
    {
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
                self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            }

            $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);

            $installmentdates = self::generateInstallmentDates($loan);

            $insallmentdatesPrevious = self::generateInstallmentDates($loan);

            $installmentDatesNew = array_diff($insallmentdatesPrevious, $installmentdates);

            $installmentNo            = 1;
            $installmentCounted       = 0;
            $periodInstallmentCounted = 0;
            $payableAmount            = $payableAmountPrincipal            = $periodPayableAmount            = $periodPayableAmountPrincipal            = 0;

            foreach ($installmentdates as $installmentdate) {

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $installmentdate;
                $schedule['weekDay']         = date('l', strtotime($installmentdate));

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

                    if (self::$loanStatusFromDate != null && $installmentdate >= self::$loanStatusFromDate) {
                        $periodPayableAmount += $schedule['installmentAmount'];
                        $periodPayableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                        $periodInstallmentCounted++;
                    }
                } else {

                    if ($installmentdate >= self::$loanStatusFromDate) {
                        array_push($schedules, $schedule);
                    }
                }

                $installmentNo++;
            }

            foreach ($installmentDatesNew as $installmentdateN) {

                $scheduleN['loanId']          = $loan->id;
                $scheduleN['installmentNo']   = $installmentNo;
                $scheduleN['installmentDate'] = $installmentdateN;
                $scheduleN['weekDay']         = date('l', strtotime($installmentdateN));

                $scheduleN['installmentAmount']          = $installments['installmentAmount'];
                $scheduleN['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                $scheduleN['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                $scheduleN['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                $scheduleN['installmentAmountInterest']  = $installments['installmentAmountInterest'];

                $scheduleN['reschedule'] = true;

                if ($installmentdateN >= self::$loanStatusFromDate) {
                    array_push($schedules, $scheduleN);
                }

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

    ## This Function is called for scheduling in all reports except col sheet
    public static function generateLoanScheduleModified($loanIdOrIds, ...$dates)
    {
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
           

            $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);

            // if(isset($Datainsallmentdates[$loan->id])){
            //     $insallmentdates = $Datainsallmentdates[$loan->id];
            // }else{
            //     $insallmentdates = self::generateInstallmentDates($loan); 
            // }

            $insallmentdates = $Datainsallmentdates[$loan->id];

            $insallmentdatesPrevious = self::generateInstallmentDates($loan);

            $installmentDatesNew = array_diff($insallmentdatesPrevious, $insallmentdates);

            $installmentNo            = 1;
            $installmentCounted       = 0;
            $periodInstallmentCounted = 0;
            $payableAmount            = $payableAmountPrincipal            = $periodPayableAmount            = $periodPayableAmountPrincipal            = 0;
            
            $max_inst_date =0;
            foreach ($insallmentdates as $insallmentdate) {

                $max_inst_date = max($max_inst_date,$insallmentdate);

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

            foreach ($installmentDatesNew as $installmentdateN) {

                $scheduleN['loanId']          = $loan->id;
                $scheduleN['installmentNo']   = $installmentNo;
                $scheduleN['installmentDate'] = $installmentdateN;
                $scheduleN['weekDay']         = date('l', strtotime($installmentdateN));

                $scheduleN['installmentAmount']          = $installments['installmentAmount'];
                $scheduleN['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                $scheduleN['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                $scheduleN['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                $scheduleN['installmentAmountInterest']  = $installments['installmentAmountInterest'];

                $scheduleN['reschedule'] = true;

                if ($installmentdateN >= self::$loanStatusFromDate) {
                    array_push($schedules, $scheduleN);
                }

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

    ## This Function is called from Collection Sheet for showing Previous scheduled loan Data
    public static function generateLoanScheduleforColSheet($loanIdOrIds, ...$dates)
    {
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
            ->whereIn('id', $loanIdOrIds)
            ->where('is_delete', 0)
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
                self::$holidays = HrService::systemHolidays($companyId = null, $branchId = $loan->branchId, $samityId = $loan->samityId, $holidayFrom, $holidayTo);
            }

            $installments = self::generateInstallmentDetails($loan->loanAmount, $loan->numberOfInstallment, $loan->interestRateIndex, $loan->loanType);

            $installmentdates = self::generateInstallmentDates($loan);

            $insallmentdatesPrevious = self::generateInstallmentDatesColSheet($loan);

            $installmentDatesNew = array_diff($insallmentdatesPrevious, $installmentdates);

            $installmentNo            = 1;
            $installmentCounted       = 0;
            $periodInstallmentCounted = 0;
            $payableAmount            = $payableAmountPrincipal            = $periodPayableAmount            = $periodPayableAmountPrincipal            = 0;

            foreach ($installmentdates as $installmentdate) {

                $schedule['loanId']          = $loan->id;
                $schedule['installmentNo']   = $installmentNo;
                $schedule['installmentDate'] = $installmentdate;
                $schedule['weekDay']         = date('l', strtotime($installmentdate));

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

                    if (self::$loanStatusFromDate != null && $installmentdate >= self::$loanStatusFromDate) {
                        $periodPayableAmount += $schedule['installmentAmount'];
                        $periodPayableAmountPrincipal += $schedule['installmentAmountPrincipal'];
                        $periodInstallmentCounted++;
                    }
                } else {

                    if ($installmentdate >= self::$loanStatusFromDate) {
                        array_push($schedules, $schedule);
                    }
                }

                $installmentNo++;
            }

            foreach ($installmentDatesNew as $installmentdateN) {

                $scheduleN['loanId']          = $loan->id;
                $scheduleN['installmentNo']   = $installmentNo;
                $scheduleN['installmentDate'] = $installmentdateN;
                $scheduleN['weekDay']         = date('l', strtotime($installmentdateN));

                $scheduleN['installmentAmount']          = $installments['installmentAmount'];
                $scheduleN['actualInastallmentAmount']   = $installments['actualInastallmentAmount'];
                $scheduleN['extraInstallmentAmount']     = $installments['extraInstallmentAmount'];
                $scheduleN['installmentAmountPrincipal'] = $installments['installmentAmountPrincipal'];
                $scheduleN['installmentAmountInterest']  = $installments['installmentAmountInterest'];

                $scheduleN['reschedule'] = true;

                if ($installmentdateN >= self::$loanStatusFromDate) {
                    array_push($schedules, $scheduleN);
                }

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
        }
        else {
            self::$regularLoanConfig->installmentAmountGeneratePolicies = [
                "nearestPreferedAmount",
                "higestPreferedAmount",
                "roundToDecade",
                "2.5Percent",
                "roundToOne"
            ];
        }

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

    public static function generateInstallmentDatesColSheet($loan)
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
                $dates = self::generateDailyInstallmentDatesColSheet($loan);
            }
            // if it is Weekly Loan
            elseif ($loan->repaymentFrequencyId === 2) {
                $dates = self::generateWeeklyInstallmentDatesColSheet($loan);
            }
            // if it is Monthly Loan
            elseif ($loan->repaymentFrequencyId === 4) {
                $dates = self::generateMonthlyInstallmentDatesColSheet($loan);
            }
        }

        return $dates;
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
            while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
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
            while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
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

    public static function generateDailyInstallmentDatesColSheet($loan)
    {
        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        // $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
        //     return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        // });

        // $massLoanRescheduleDates = [];

        // foreach ($massLoanReschedules as $massLoanReschedule) {
        //     $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
        //     while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
        //         array_push($massLoanRescheduleDates, $rescheduleFrom);
        //         $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
        //     }
        // }

        $installmentDate = $loan->firstRepayDate;
        $dates           = [];
        for ($i = 0; $i < $loan->numberOfInstallment && (self::$loanStatusToDate == null || $installmentDate <= self::$loanStatusToDate); $i++) {
            // reschedule installment
            // if (in_array($loan->id, self::$rescheduledLoanIds)) {
            //     $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
            //     if ($numberOfTerm > 0) {
            //         $installmentDate = date('Y-m-d', strtotime("+" . $numberOfTerm . " day", strtotime($installmentDate)));
            //     }
            // }

            while (in_array($installmentDate, self::$holidays)) {
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

    public static function generateWeeklyInstallmentDatesColSheet($loan)
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
        // $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
        //     return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        // });

        // $massLoanRescheduleDates = [];

        // foreach ($massLoanReschedules as $massLoanReschedule) {
        //     $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
        //     while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
        //         array_push($massLoanRescheduleDates, $rescheduleFrom);
        //         $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
        //     }
        // }

        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {

            // reschedule installment
            // if (in_array($loan->id, self::$rescheduledLoanIds)) {
            //     $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
            //     if ($numberOfTerm > 0) {
            //         $installmentDate = date('Y-m-d', strtotime("+" . (7 * $numberOfTerm) . " day", strtotime($installmentDate)));
            //     }
            // }

            $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

            while (in_array($installmentDate, self::$holidays) || $installmentDate < $loan->firstRepayDate) {
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

    public static function generateMonthlyInstallmentDatesColSheet($loan)
    {
        $installmentDate = $loan->firstRepayDate;
        $monthStartDate  = date("Y-m-01", strtotime($installmentDate));
        $monthEndDate    = date("Y-m-t", strtotime($installmentDate));

        $loanProductId            = $loan->productId;
        $loanRepaymentFrequencyId = $loan->repaymentFrequencyId;
        $loanSamityId             = $loan->samityId;
        $loanBranchId             = $loan->branchId;

        // mass reshudule
        // $massLoanReschedules = self::$massLoanReschedules->filter(function ($obj, $key) use ($loanProductId, $loanRepaymentFrequencyId, $loanSamityId, $loanBranchId) {
        //     return ($obj->loanProductId == 0 || $obj->loanProductId == $loanProductId) && ($obj->loanRepayFrequencyId == 0 || $obj->loanRepayFrequencyId == $loanRepaymentFrequencyId) && ($obj->samityId == 0 || $obj->samityId == $loanSamityId) && ($obj->branchId == 0 || $obj->branchId == $loanBranchId);
        // });

        // $massLoanRescheduleDates = [];

        // foreach ($massLoanReschedules as $massLoanReschedule) {
        //     $rescheduleFrom = $massLoanReschedule->rescheduleFrom;
        //     while ($rescheduleFrom <= $massLoanReschedule->rescheduleTo) {
        //         array_push($massLoanRescheduleDates, $rescheduleFrom);
        //         $rescheduleFrom = date('Y-m-d', strtotime('+1 day', strtotime($rescheduleFrom)));
        //     }
        // }

        $dates        = [];
        $willContinue = true;
        for ($i = 0; $i < $loan->numberOfInstallment && $willContinue; $i++) {
            // reschedule installment
            // if (in_array($loan->id, self::$rescheduledLoanIds)) {
            //     $numberOfTerm = self::$loanReschedules->where('loanId', $loan->id)->where('installmentNo', $i + 1)->sum('numberOfTerm');
            //     if ($numberOfTerm > 0) {
            //         $monthStartDate = date('Y-m-d', strtotime("+" . ($numberOfTerm) . " months", strtotime($monthStartDate)));
            //         $monthEndDate   = date("Y-m-t", strtotime($monthStartDate));

            //         $installmentDate = date('Y-m-' . date('d', strtotime($loan->firstRepayDate)), strtotime($monthStartDate));
            //     }
            // }

            $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);

            while ($installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
            }
            while ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
            }

            $initialDate = $installmentDate;

            while (in_array($installmentDate, self::$holidays) || $installmentDate < $loan->firstRepayDate || $installmentDate < $monthStartDate) {
                $installmentDate = date('Y-m-d', strtotime("+7 day", strtotime($installmentDate)));
                $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
            }

            $progessiveInstallmentDate = $installmentDate;

            if ($installmentDate > $monthEndDate) {
                $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($initialDate)));
                while (in_array($installmentDate, self::$holidays) || $installmentDate > $loan->firstRepayDate || $installmentDate > $monthStartDate) {
                    $installmentDate = date('Y-m-d', strtotime("-7 day", strtotime($installmentDate)));
                    $installmentDate = self::getSamityDateOfWeek(self::$samity, $installmentDate);
                }
            }

            if ($installmentDate < $monthStartDate || $installmentDate < $loan->firstRepayDate) {

                if (self::$regularLoanConfig->monthlyLoanMonthOverflow == 'no') {
                    // set the installment date to the next any working day
                    $installmentDate = $initialDate;
                    while (in_array($installmentDate, self::$holidays)) {
                        $installmentDate = date('Y-m-d', strtotime("+1 day", strtotime($installmentDate)));
                    }
                    if ($installmentDate > $monthEndDate) {
                        $installmentDate = $initialDate;
                        while (in_array($installmentDate, self::$holidays)) {
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

        $ledgerData = AccService::getLedgerAccount($branchId, $projectID, $projectTypeID, $accTypeID, $groupHead, $level);

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
            ->orderBy('samityId')
            ->orderBy('effectiveDate')
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

    public static function fnForMemberData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_members')
                        ->where([['is_delete', 0]])
                        ->whereIn('id', $arrayIDs)
                        ->selectRaw('CONCAT(name, " [", memberCode, "]") AS memberName, id')
                        ->pluck('memberName', 'id')
                        ->toArray();
        return $QueryData;
        
    }

    public static function fnForLoanStatusData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_loan_status')
                        ->where([['is_delete', 0]])
                        ->whereIn('id', $arrayIDs)
                        ->selectRaw('name, id')
                        ->pluck('name', 'id')
                        ->toArray();
        return $QueryData;
    }


    public static function fnForSavingsProductData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_savings_product')
                        ->where([['is_delete', 0]])
                        ->whereIn('id', $arrayIDs)
                        ->selectRaw('CONCAT(name, " [", productCode, "]") AS name, id')
                        ->pluck('name', 'id')
                        ->toArray();
        return $QueryData;
    }
    public static function fnForSamityData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_samity')
                        ->where([['is_delete', 0]])
                        ->whereIn('id', $arrayIDs)
                        ->selectRaw('CONCAT(name, " [", samityCode, "]") AS samityName, id')
                        ->pluck('samityName', 'id')
                        ->toArray();
        return $QueryData;
    }

    public static function fnFoLoanProductData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_loan_products')
                    ->where([['is_delete', 0]])
                    ->whereIn('id', $arrayIDs)
                    ->selectRaw('CONCAT(name, " [", productCode, "]") AS name, id')
                    ->pluck('name', 'id')
                    ->toArray();
        return $QueryData;
    }

    public static function fnForTransactionTypeData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_savings_transaction_types')
                    ->whereIn('id', $arrayIDs)
                    ->selectRaw('name, id')
                    ->pluck('name', 'id')
                    ->toArray();
        return $QueryData;
    }
    public static function fnForSavingsAccCodeData($arrayIDs)
    {
        $QueryData = array();
        $QueryData = DB::table('mfn_savings_accounts')
                    ->where([['is_delete', 0]])
                    ->whereIn('id', $arrayIDs)
                    ->selectRaw('accountCode, id')
                    ->pluck('accountCode', 'id')
                    ->toArray();
        return $QueryData;
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
            $selBranchArr = Common::fnForBranchZoneAreaWise($branchId);
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

}
