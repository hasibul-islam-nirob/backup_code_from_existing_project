@php
    use App\Services\CommonService as Common;

    $startDate = Common::systemCurrentDate();
    $endDate = Common::systemCurrentDate();
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

<div class="filterOptionView ExportHeading">
    <table>
        @foreach ($elements as $filterId => $label)
            <tr class="">
                <td>
                <span>{{ $label }}:</span>
                <span>
                    <span id="{{ $filterId }}_rptxt">&nbsp;</span>
                </span>
                </td>
            </tr>
        @endforeach
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