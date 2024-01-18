@php
use App\Services\CommonService as Common;
// use App\Services\HrService as HRS;
// use App\Services\HtmlService as HTML;

$startDate = Common::systemCurrentDate();
$endDate = Common::systemCurrentDate();

////////////////////////////////////////////////
## This is for Collection Sheet Period
// if(!empty(Request::input('month_year'))){
//     $monthYear = Request::input('month_year');
//     $startDate = new DateTime($monthYear);
//     $startDate = ($startDate->modify('first day of this month'))->format('d-m-Y');

//     $endDate = new DateTime($monthYear);
//     $endDate = ($endDate->modify('last day of this month'))->format('d-m-Y');
// }

// $ledgerId      = (!empty(Request::input('ledger_id'))) ?  Request::input('ledger_id') : ((!empty(Request::input('ledger_cash'))) ?  Request::input('ledger_cash') : ( (!empty(Request::input('ledger_bank'))) ?  Request::input('ledger_bank') : null ));
// $ledgerIdCashBank = (!empty(Request::input('ledger_cash_bank'))) ? Request::input('ledger_cash_bank') : null;
// $flag = false;

// if(!empty($ledgerId)){
//     $ledgerData = DB::table('acc_account_ledger')->where([['is_delete',0], ['is_active',1],['id',$ledgerId]])->first();
//     $flag = true;
// }
// else if(!empty($ledgerIdCashBank)){
//     $ledgerData = DB::table('acc_account_ledger')->where([['is_delete',0], ['is_active',1],['id',$ledgerIdCashBank]])->first();
//     $flag = true;
// }
// else {
//     $route = Route::current()->uri();

//     if($route == "acc/report/cash_book"){
//         $ledgerTitle = "All Cash";
//         $flag = true;
//     }
//     else if($route == "acc/report/bank_book"){
//         $ledgerTitle = "All Bank";
//         $flag = true;
//     }
// }

@endphp

<style type="text/css">
    /* .filterOptionView > div > span {
        margin:0; 
        padding:0;
        color: #000;
    }
    .filterOptionView {
        border:1px solid #948a8a; 
        font-size: 12px;
        margin: 1px;
    } */

    .filterOptionView > div {
        border:1px solid #948a8a;
        margin: 2px;
        padding: 2px;
        /* width: 16%; */
        color: #000;
        font-size: 11px;
    }

    @media print{
        .filterOptionView {
            margin: 0!important;
            padding-left: 0px!important; 
        }

        .filterOptionView > div {
            margin: 2px;
            padding: 2px;
            /* width: 16%; */
            color: #000;
            font-size: 11px;
        }
    }
</style>

{{-- <br> --}}
<div class="row filterOptionView ExportHeading pl-15">

    @foreach ($elements as $filterId => $label)
        <div class="" id="{{ $filterId }}_div">
            <span>{{ $label }}:</span>
            <span>
                <span id="{{ $filterId }}_rptxt">&nbsp;</span>
            </span>
        </div>

        {{-- <span class="">
            <strong style="color: #000;">{{ $label }}</strong>: <span id="{{ $filterId }}_rptxt">&nbsp;</span> || &nbsp;
        </span> --}}
    @endforeach

    {{-- <span class="">
        <strong style="color: #000;">Reporting Date:</strong>: 
        <span id="start_date_txt">{{ $startDate }}</span>
        <span id="text_to">to </span>
        <span id="end_date_txt">{{ $endDate }}</span>
        || &nbsp;
    </span>

    <span class="">
        <strong style="color: #000;">Printed Date:</strong>: 
        {{ (new Datetime())->format('d-m-Y') }}
    </span> --}}

    <div class="">
        <span>Reporting Date:</span>
        <span>
            <span id="start_date_txt">{{ $startDate }}</span>
            <span id="text_to">to </span>
            <span id="end_date_txt">{{ $endDate }}</span>
        </span>
    </div>

    <div class="">
        <span>Printed Date:</span>
        <span>
            {{ (new Datetime())->format('d-m-Y') }}
        </span>
    </div>
</div>
{{-- <br> --}}

<script>
    $('#searchButton').click(function (event) {

        @foreach ($elements as $filterId => $label)
            var spanIdN = "{{$filterId}}_rptxt";
            var contentId = "{{ $filterId }}";
            $('#' + spanIdN).html('');

            // console.log($('#'+ contentId).val());

            if($('#'+ contentId).val() != ""){
                
                if($('#'+ contentId + ' option:selected').text()!=""){
                    $('#' + spanIdN).html($('#'+ contentId + ' option:selected').text());
                }else {
                    $('#' + spanIdN).html($('#'+ contentId).val());
                }

                $('#'+ contentId + "_div").show();
            }
            else {
                $('#'+ contentId + "_div").hide();
            }

        @endforeach

        // $('#end_date_txt').html('');
        // $('#start_date_txt').html('');
        // $('#text_to').html('to ');
        $('#end_date_txt').show();
        $('#text_to').show();
        $('#start_date_txt').show();

        if ($('#start_date').val() != '' && typeof ($('#start_date').val()) != 'undefined') {
            $('#start_date_txt').html($('#start_date').val());
        }
        else if ($('#startDate').val() != '' && typeof ($('#startDate').val()) != 'undefined') {
            $('#start_date_txt').html($('#startDate').val());
        }
        else if (typeof ($('#start_date').val()) == 'undefined' || $('#start_date').val() == '' 
            && typeof ($('#startDate').val()) == 'undefined' || $('#startDate').val() == '') {

            // $('#start_date_txt').hide();
            // $('#text_to').html('Up to ');
        }
        

        if ($('#end_date').val() != '' && typeof ($('#end_date').val()) != 'undefined') {
            $('#end_date_txt').html($('#end_date').val());

        }
        else if ($('#endDate').val() != '' && typeof ($('#endDate').val()) != 'undefined') {
            $('#end_date_txt').html($('#endDate').val());

        }
        else if (typeof ($('#end_date').val()) == 'undefined' || $('#end_date').val() == '' 
            && typeof ($('#endDate').val()) == 'undefined' || $('#endDate').val() == '') {

            // $('#end_date_txt').hide();
            // $('#text_to').hide();
        }
    });

</script>