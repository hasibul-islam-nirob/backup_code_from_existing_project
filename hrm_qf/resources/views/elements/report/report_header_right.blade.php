@php
    use App\Services\CommonService as Common;

    $startDate = Common::systemCurrentDate();
    $endDate = Common::systemCurrentDate();

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

    .filterOptionView > table {
        /* border:1px solid #948a8a;
        margin: 2px;
        padding: 2px; */
        /* width: 16%; */
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
            /* width: 16%; */
            color: #000;
            font-size: 10px;
        }
    }
</style>

<div class="filterOptionView ExportHeading text-right">
    <table>
        @if(isset($printIcon) && $printIcon)
            <tr class="d-print-none">
                @if(isset($incompleteBranch) && $incompleteBranch)
                <td>
                    <a href="javascript:void(0)" title="Branch List for Incomplete Data" onClick="incompleteBranchList();"
                    class="btnIncompleList">
                        <i class="fa fa-file-text-o fa-lg" style="font-size:20px; margin-right: 5px;"></i>
                    </a>
                </td>
                @endif
                <td>
                    <a href="javascript:void(0)" title="Print Document" onClick="window.print();"
                    class="btnPrint" >
                        <i class="fa fa-print fa-lg" style="font-size:20px;"></i>
                    </a>

                    <a href="javascript:void(0)" title="Download PDF" onclick="getDownloadPDF();">
                        <i class="fa fa-file-pdf-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>

                    <a href="javascript:void(0)" title="Download Excel" 
                        onclick="fnDownloadExcel('ExportHeading,ExportDiv', '{{ $title_excel }}_{{ (new Datetime())->format('d-m-Y') }}');">
                        <i class="fa fa-file-excel-o fa-lg" style="font-size:20px; margin-left: 5px;"></i>
                    </a>
                </td>
            </tr>
        @endif

        <tr>
            <td>
                <span>Reporting Date:</span>
                <span>
                    <span id="start_date_txt">{{ $startDate }}</span>
                    <span id="text_to">to </span>
                    <span id="end_date_txt">{{ $endDate }}</span>
                </span>
            </td>
        </tr>

        <tr>
            <td>
                <span>Printed Date:</span>
                <span>
                    {{ (new Datetime())->format('d-m-Y') }}
                </span>
            </td>
        </tr>
    </table>
</div>

<script>
    $('#searchButton').click(function (event) {

        @foreach ($elements as $filterId => $label)
            var spanIdN = "{{$filterId}}_rptxt";
            var contentId = "{{ $filterId }}";
            $('#' + spanIdN).html('');

            if($('#'+ contentId + ' option:selected').text()!=""){
                $('#' + spanIdN).html($('#'+ contentId + ' option:selected').text());
            }else if($('#'+ contentId).val() !=""){
                $('#' + spanIdN).html($('#'+ contentId).val());
            }

            
        @endforeach

        $('#end_date_txt').html('');
        $('#start_date_txt').html('');
        $('#text_to').html('to ');
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

            $('#start_date_txt').hide();
            $('#text_to').html('Up to ');
        }
        

        if ($('#end_date').val() != '' && typeof ($('#end_date').val()) != 'undefined') {
            $('#end_date_txt').html($('#end_date').val());

        }
        else if ($('#endDate').val() != '' && typeof ($('#endDate').val()) != 'undefined') {
            $('#end_date_txt').html($('#endDate').val());

        }
        else if (typeof ($('#end_date').val()) == 'undefined' || $('#end_date').val() == '' 
            && typeof ($('#endDate').val()) == 'undefined' || $('#endDate').val() == '') {

            $('#end_date_txt').hide();
            $('#text_to').hide();
        }
    });

</script>