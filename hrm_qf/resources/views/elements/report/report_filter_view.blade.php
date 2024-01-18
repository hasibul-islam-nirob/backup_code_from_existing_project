
@php
use App\Services\CommonService as CS;
use App\Services\HrService as HRS;

$startDate = CS::systemCurrentDate();
$endDate = CS::systemCurrentDate();

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
    .filterOptionView > table {
        color: #000;
        font-size: 10px;
    }

    @media print{
        .filterOptionView {
            margin: 0!important;
        }

        .filterOptionView > div {
            margin: 2px;
            padding: 2px;
            color: #000;
            font-size: 10px;
        }
    }
</style>


<div class="row reportHeading ExportHeading">
    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12 d-flex justify-content-center">

        <table style="width:100%; color:#000; margin:0 padding: 0; font-size: 12px;">

            @if(isset($ledgerHead) && $ledgerHead && $flag)
                <tr class="p-0 m-0">
                    <td class="text-left p-0 m-0"  width="50%">
                        <span>Account Head:</span>

                        @if(isset($ledgerTitle) && $ledgerTitle)
                            <span id="ledgerHead">{{ $ledgerTitle }}</span>
                        @else
                            <span id="ledgerHead">{{ isset($ledgerData) ? $ledgerData->name . ' (' . $ledgerData->code. ')' : '' }}</span>
                        @endif
                    </td>
                </tr>
            @endif

            @if(isset($projectName) || isset($projectTypeName))
                <tr class="p-0 m-0">

                    @if(isset($projectTypeName) && $projectTypeName)
                        <td class="text-left p-0 m-0"  width="50%">
                            <span>Project Type:</span>
                            <span>
                                <span id="projectTypeName"></span>
                            </span>
                        </td>
                    @endif

                    @if(isset($projectName) && $projectName)
                        <td class="text-right p-0 m-0"  width="50%">
                            <span>Project:</span>
                            <span>
                                <span id="projectName"></span>
                            </span>
                        </td>
                    @endif
                </tr>
            @endif

            <tr class="p-0 m-0">
                <td class="text-left p-0 m-0"  width="50%">
                    <p class="p-0 m-0">
                        Reporting Date:
                        <span id="start_date_txt">{{ CS::viewDateFormat($startDate) }}</span>
                        <span id="text_to">to </span>
                        <span id="end_date_txt">{{ CS::viewDateFormat($endDate) }}</span>
                    </p>
                </td>
                <td class="text-right p-0 m-0" width="50%">
                    <p class="p-0 m-0">
                        Printed Date: {{ CS::viewDateFormat(now()) }}
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>
