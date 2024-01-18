
@php
use App\Services\CommonService as Common;
use App\Services\HrService as HRS;

$startDate = Common::systemCurrentDate();
$endDate = Common::systemCurrentDate();

## This is for Collection Sheet Period
if(!empty(Request::input('month_year'))){
    $monthYear = Request::input('month_year');
    $startDate = new DateTime($monthYear);
    $startDate = ($startDate->modify('first day of this month'))->format('d-m-Y');

    $endDate = new DateTime($monthYear);
    $endDate = ($endDate->modify('last day of this month'))->format('d-m-Y');
}

$ledgerId      = (!empty(Request::input('ledger_id'))) ?  Request::input('ledger_id') : ((!empty(Request::input('ledger_cash'))) ?  Request::input('ledger_cash') : ( (!empty(Request::input('ledger_bank'))) ?  Request::input('ledger_bank') : null ));
$ledgerIdCashBank = (!empty(Request::input('ledger_cash_bank'))) ? Request::input('ledger_cash_bank') : null;
$flag = false;

if(!empty($ledgerId)){
    $ledgerData = DB::table('acc_account_ledger')->where([['is_delete',0], ['is_active',1],['id',$ledgerId]])->first();
    $flag = true;
}
else if(!empty($ledgerIdCashBank)){
    $ledgerData = DB::table('acc_account_ledger')->where([['is_delete',0], ['is_active',1],['id',$ledgerIdCashBank]])->first();
    $flag = true;
}
else {
    $route = Route::current()->uri();

    if($route == "acc/report/cash_book"){
        $ledgerTitle = "All Cash";
        $flag = true;
    }
    else if($route == "acc/report/bank_book"){
        $ledgerTitle = "All Bank";
        $flag = true;
    }
}
@endphp

<style type="text/css">
    .filterOptionView > div > p {
        margin:0;
        padding:0;
        color: #000;
    }
    .filterOptionView {
        border:1px solid #948a8a;
        font-size: 10px;
        margin: 1px;
    }

    .filterOptionView > div {
        border-right:1px solid #948a8a;
        margin: 4px;
        padding: 5px;
        /* width: 16%; */
    }

    @media print{
        .filterOptionView {
            margin: 0!important;
        }

        .filterOptionView > div {
            margin: 3px;
            padding: 5px;
            width: 16%;
        }
    }
</style>

<div class="row filterOptionView ExportHeading">

    @if(isset($ledgerHead) && $ledgerHead && $flag)
        <div class="">
            <p><b>Account Head:</b></p>
            <p>
                @if(isset($ledgerTitle) && $ledgerTitle)
                <span id="ledgerHead">{{ $ledgerTitle }}</span>
                @else
                <span id="ledgerHead">{{ isset($ledgerData) ? $ledgerData->name . ' (' . $ledgerData->code. ')' : '' }}</span>
                @endif
            </p>
        </div>
    @endif

    @if(isset($projectName) && $projectName)
        <div class="">
            <p><b>Project:</b></p>
            <p>
                <span id="projectName"></span>
            </p>
        </div>
    @endif

    @if(isset($projectTypeName) && $projectTypeName)
        <div class="">
            <p><b>Project Type:</b></p>
            <p>
                <span id="projectTypeName"></span>
            </p>
        </div>
    @endif

    @if(isset($totalCustomer) && $totalCustomer)
        <div class="">
            <p><b>Total Customer</b></p>
            <p>
                <span id="totalRowDiv">0</span>
            </p>
        </div>
    @endif

    @if(isset($zone) && $zone)
        <div class="">
            <p><b>Zone:</b></p>
            <p>
                <span id="zoneName"></span>
            </p>
        </div>
    @endif

    <div class="">
        <p><b>Reporting Date:</b></p>
        <p>
            <span id="start_date_txt">{{ $startDate }}</span>
            <span id="text_to">to</span>
            <span id="end_date_txt">{{ $endDate }}</span>
        </p>
    </div>

    <div class="">
        <p><b>Printed Date:</b></p>
        <p>
            {{ (new Datetime())->format('d-m-Y') }}
        </p>
    </div>

</div>
<br>
