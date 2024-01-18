@php

if(count(Request::all())>0){
    $requestData = Request::all();
}else{
    $requestData = null;
}

@endphp

{{-- On ready jquery code  --}}
<script>
    $(document).ready(function() {
        // fnLoading(false);

        @if(!empty($requestData))
            setTimeout(function () {
                fnForFilterFieldValueSet().then(() => {
                    showReportHeadingForAll('close');
                });
            }, 1000);
        @endif
    });
</script>

{{-- On click and submit jquery code  --}}
<script>
    $('#searchButton').click(function (event) {

        // // comment by Saurav
        // showReportHeadingForAll('close');

        // $('#end_date_txt').html('');
        // $('#start_date_txt').html('');
        // $('#text_to').html('to ');
        // $('#end_date_txt').show();
        // $('#text_to').show();
        // $('#start_date_txt').show();

        // if ($('#start_date').val() != '' && typeof ($('#start_date').val()) != 'undefined') {
        //     $('#start_date_txt').html(viewDateFormat($('#start_date').val()));
        // }
        // else if ($('#startDate').val() != '' && typeof ($('#startDate').val()) != 'undefined') {
        //     $('#start_date_txt').html(viewDateFormat($('#startDate').val()));
        // }
        // else if (typeof ($('#start_date').val()) == 'undefined' || $('#start_date').val() == ''
        //     && typeof ($('#startDate').val()) == 'undefined' || $('#startDate').val() == '') {

        //     $('#start_date_txt').hide();
        //     $('#text_to').html('Up to ');
        // }


        // if ($('#end_date').val() != '' && typeof ($('#end_date').val()) != 'undefined') {
        //     $('#end_date_txt').html(viewDateFormat($('#end_date').val()));

        // }
        // else if ($('#endDate').val() != '' && typeof ($('#endDate').val()) != 'undefined') {
        //     // console.log(viewDateFormat($('#endDate').val()),$('#endDate').val());
        //     $('#end_date_txt').html();

        // }
        // else if (typeof ($('#end_date').val()) == 'undefined' || $('#end_date').val() == ''
        //     && typeof ($('#endDate').val()) == 'undefined' || $('#endDate').val() == '') {

        //     $('#end_date_txt').hide();
        //     $('#text_to').hide();
        // }
    });

    $("#filterFormId").submit(function (event) {

        if ($("#filterFormId").length) {
            // fnLoading(true);
        }

        showReportHeadingForAll('close');
        // $("#filterFormId").submit();
    });
</script>

<script>
    async function fnForFilterFieldValueSet(){

        var requestData = {!! json_encode($requestData) !!};

        console.log("fn report set selected filter data");

        @if(isset($elements))
            @foreach ($elements as $key => $element)

                var contentId = "{{ $element['id'] }}";
                var contentName = "{{ $element['name'] }}";
                var contentType = "{{ $element['type'] }}";
                var contentOnLoad = "{{ (isset($element['onload']) && $element['onload'] = 1) ? $element['onload'] : 0 }}";

                if(requestData != null) {
                    if (requestData.hasOwnProperty(contentName)) {

                        if(contentType == 'select'){

                            if(requestData[contentName] != null){
                                // // value set
                                $("#" + contentId).val(requestData[contentName]);

                                if(contentType == 'select' && requestData[contentName] != null && contentOnLoad == 0){
                                    $("#" + contentId).trigger("change");
                                }
                            }
                        }
                        else{
                            // // value set
                            $("#" + contentId).val(requestData[contentName]);
                        }
                    }
                }

            @endforeach
        @endif
    }

    async function showReportHeadingForAll(filter_div = null) {

        console.log('fn Report Heading');

        if(filter_div === 'close'){
            setTimeout(function () {
                $(".wb-minus").trigger('click');
            }, 10);
        }
        // $(".wb-minus").trigger('click');
        $('.show').show('slow');

        var requestData = {!! json_encode($requestData) !!};

        @if(isset($elements))
        @foreach ($elements as $key => $element)

            var spanIdN = "{{ $element['id'] }}_rptxt";
            var contentId = "{{ $element['id'] }}";
            var contentName = "{{ $element['name'] }}";
            var contentType = "{{ $element['type'] }}";
            var contentValue = "";

            $('#' + spanIdN).html('');

            if(requestData == null){
                if($('#'+ contentId + ' option:selected').text()!=""){
                    contentValue = $('#'+ contentId + ' option:selected').text();
                }else if($('#'+ contentId).val() !=""){
                    contentValue = $('#'+ contentId).val();
                }
            }
            else {
                if (requestData.hasOwnProperty(contentName)) {

                    if(contentType == 'select'){

                        if(requestData[contentName] != null)
                        {
                            // // Header set
                            contentValue = $('#' + contentId + ' option[value="' + requestData[contentName] + '"]').text();
                        }
                        else {
                            contentValue = $('#' + contentId + ' option[value=""]').text();
                        }
                    }
                    else{
                        // // Header set
                        contentValue = requestData[contentName];
                    }
                }
            }

            $('#' + spanIdN).html(contentValue);

        @endforeach
        @endif

        var reportBranchTxt = false;
        var reportForTxt = false;

        @if(isset($elements['zone']))
            var contentIdZ = "{{ $elements['zone']['id'] }}";

            if ($('#' + contentIdZ).val() != '' && typeof ($('#' + contentIdZ).val()) != 'undefined') {
                reportBranchTxt = $('#' + contentIdZ).find("option:selected").text();
                reportForTxt = "Zone:";
            }
        @endif

        @if(isset($elements['region']))
            var contentIdZ = "{{ $elements['region']['id'] }}";

            if ($('#' + contentIdZ).val() != '' && typeof ($('#' + contentIdZ).val()) != 'undefined') {
                reportBranchTxt = $('#' + contentIdZ).find("option:selected").text();
                reportForTxt = "Region:";
            }
        @endif

        @if(isset($elements['area']))
            var contentIdA = "{{ $elements['area']['id'] }}";

            if ($('#' + contentIdA).val() != '' && typeof ($('#' + contentIdA).val()) != 'undefined') {
                reportBranchTxt = $('#' + contentIdA).find("option:selected").text();
                reportForTxt = "Area:";
            }
        @endif

        @if(isset($elements['branch']))
            var contentIdB = "{{ $elements['branch']['id'] }}";

            if ($('#' + contentIdB).val() != '' && typeof ($('#' + contentIdB).val()) != 'undefined') {

                reportBranchTxt = $('#' + contentIdB).find("option:selected").text();
                reportForTxt = "Branch:";

                $('#branchName').html($('#' + contentIdB + ' option:selected').text());

            }
        @endif

        @if(isset($elements['branch_to']))
            var contentIdBt = "{{ $elements['branch']['id'] }}";

            if ($('#' + contentIdBt).val() != '' && typeof ($('#' + contentIdBt).val()) != 'undefined') {
                reportBranchTxt = $('#' + contentIdBt).find("option:selected").text();
                reportForTxt = "Branch:";

                $('#branchName').html($('#' + contentIdBt + ' option:selected').text());

            }
        @endif

        @if(isset($elements['branch_from']))
            var contentIdBf = "{{ $elements['branch']['id'] }}";

            if ($('#' + contentIdBf).val() != '' && typeof ($('#' + contentIdBf).val()) != 'undefined') {
                reportBranchTxt = $('#' + contentIdBf).find("option:selected").text();
                reportForTxt = "Branch:";

                $('#branchName').html($('#' + contentIdBf + ' option:selected').text());

            }
        @endif

        // if ($('#branch_id option').length < 2 || $('#branch_to option').length < 2 || $('#branch_from option').length < 2) {
        //     reportBranchTxt = false;
        // }else{
        //     reportBranchTxt = "Head Office";
        //     reportForTxt = "Branch:";
        // }


        if (reportBranchTxt === false) {
            reportBranchTxt = "All Branch";
        }
        else if (reportBranchTxt == '') {
            reportBranchTxt = false;
            reportForTxt = false;
        }

        if(reportForTxt !== false){
            $('#reportFor').html(reportForTxt);
        }

        if(reportBranchTxt !== false) {
            $('#reportBranch').html(reportBranchTxt);
        }

        $('#end_date_txt').html('');
        $('#start_date_txt').html('');
        $('#text_to').html('to ');
        $('#end_date_txt').show();
        $('#text_to').show();
        $('#start_date_txt').show();

        @if(isset($elements['startDate']))
            let contentIdSd = "{{ $elements['startDate']['id'] }}";

            if ($('#' + contentIdSd).val() != '' && typeof ($('#' + contentIdSd).val()) != 'undefined') {
                $('#start_date_txt').html(viewDateFormat($('#' + contentIdSd).val()));
            }
            else if (typeof ($('#' + contentIdSd).val()) == 'undefined' || $('#' + contentIdSd).val() == '') {
                $('#start_date_txt').hide();
                // $('#text_to').hide();
                $('#text_to').html('Up to ');
            }
        @endif

        @if(isset($elements['endDate']))
            let contentIdEd = "{{ $elements['endDate']['id'] }}";

            if ($('#' + contentIdEd).val() != '' && typeof ($('#' + contentIdEd).val()) != 'undefined') {
                $('#end_date_txt').html(viewDateFormat($('#' + contentIdEd).val()));
            }
            else if (typeof ($('#' + contentIdEd).val()) == 'undefined' || $('#' + contentIdEd).val() == '') {
                $('#end_date_txt').hide();
                $('#text_to').hide();
            }

        @endif

        @if(isset($elements['monthYear']))
            let contentIdEd = "{{ $elements['monthYear']['id'] }}";
            if ($('#' + contentIdEd).val() != '' && typeof ($('#' + contentIdEd).val()) != 'undefined') {
                // $('#end_date_txt').html(viewDateFormat($('#' + contentIdEd).val()));
                let selectedMonth = new Date($('#' + contentIdEd).val());

                let firstDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth(), 1);
                let lastDayOfMonth = new Date(selectedMonth.getFullYear(), selectedMonth.getMonth() + 1, 0);

                firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
                lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);
                let selectedMonthT = $.datepicker.formatDate('MM-yy', selectedMonth);

                $('#start_date_txt').show();
                $('#end_date_txt').show();
                $('#text_to').show();

                $('#start_date_txt').html(viewDateFormat(firstDayOfMonth));
                $('#text_to').html(' to ');
                $('#end_date_txt').html(viewDateFormat(lastDayOfMonth));

                $('#afterTitle').html(" of " + selectedMonthT);
            }
            else if (typeof ($('#' + contentIdEd).val()) == 'undefined' || $('#' + contentIdEd).val() == '') {
                $('#start_date_txt').hide();
                $('#text_to').hide();
                $('#end_date_txt').hide();
            }
        @endif

        @if(isset($elements['month']) && isset($elements['year']))
            let contentIdEd = "{{ $elements['month']['id'] }}";
            let contentIdEd2 = "{{ $elements['year']['id'] }}";
            if ($('#' + contentIdEd).val() != '' && typeof ($('#' + contentIdEd).val()) != 'undefined' && $('#' + contentIdEd2).val() != '' && typeof ($('#' + contentIdEd2).val()) != 'undefined') {
                // $('#end_date_txt').html(viewDateFormat($('#' + contentIdEd).val()));
                let selectedMonth = new Date($('#' + contentIdEd).val());
                let selectedYear = new Date($('#' + contentIdEd2).val());

                let firstDayOfMonth = new Date(selectedYear.getFullYear(), selectedMonth.getMonth(), 1);
                let lastDayOfMonth = new Date(selectedYear.getFullYear(), selectedMonth.getMonth() + 1, 0);

                firstDayOfMonth = $.datepicker.formatDate('dd-mm-yy', firstDayOfMonth);
                lastDayOfMonth = $.datepicker.formatDate('dd-mm-yy', lastDayOfMonth);
                // let selectedMonthT = $.datepicker.formatDate('MM-yy', selectedMonth);

                $('#start_date_txt').show();
                $('#end_date_txt').show();
                $('#text_to').show();

                $('#start_date_txt').html(viewDateFormat(firstDayOfMonth));
                $('#text_to').html(' to ');
                $('#end_date_txt').html(viewDateFormat(lastDayOfMonth));

                // $('#afterTitle').html(" of " + selectedMonthT);
            }
            else if (typeof ($('#' + contentIdEd).val()) == 'undefined' || $('#' + contentIdEd).val() == '' && typeof ($('#' + contentIdEd2).val()) == 'undefined' || $('#' + contentIdEd2).val() == '') {
                $('#start_date_txt').hide();
                $('#text_to').hide();
                $('#end_date_txt').hide();
            }
        @endif

        @if(!isset($elements['month']) && isset($elements['year']))
            let contentIdEd = "{{ $elements['year']['id'] }}";
            if ($('#' + contentIdEd).val() != '' && typeof ($('#' + contentIdEd).val()) != 'undefined') {
                // $('#end_date_txt').html(viewDateFormat($('#' + contentIdEd).val()));
                // let selectedMonth = new Date($('#' + contentIdEd).val());
                let selectedYear = new Date($('#' + contentIdEd).val());

                let firstDayOfYear = new Date(selectedYear.getFullYear(), 0, 1);
                let lastDayOfYear = new Date(selectedYear.getFullYear(), 12, 0);

                firstDayOfYear = $.datepicker.formatDate('dd-mm-yy', firstDayOfYear);
                lastDayOfYear = $.datepicker.formatDate('dd-mm-yy', lastDayOfYear);
                // let selectedMonthT = $.datepicker.formatDate('MM-yy', selectedMonth);

                $('#start_date_txt').show();
                $('#end_date_txt').show();
                $('#text_to').show();

                $('#start_date_txt').html(viewDateFormat(firstDayOfYear));
                $('#text_to').html(' to ');
                $('#end_date_txt').html(viewDateFormat(lastDayOfYear));

                // $('#afterTitle').html(" of " + selectedMonthT);
            }
            else if (typeof ($('#' + contentIdEd).val()) == 'undefined' || $('#' + contentIdEd).val() == '') {
                $('#start_date_txt').hide();
                $('#text_to').hide();
                $('#end_date_txt').hide();
            }
        @endif


        @if(isset($elements['searchBy']))
            let searchById = "{{ $elements['searchBy']['id'] }}";

            if ($('#' + searchById).val() != '' && typeof ($('#' + searchById).val()) != 'undefined') {

                let searchByValue = $('#' + searchById).val();

                if (searchByValue == '1') {
                    let start = $('#start_date_fy').val();
                    let end = $('#end_date_fy').val();
                    $('#start_date_txt').html(viewDateFormat(start));
                    $('#end_date_txt').html(viewDateFormat(end));

                }else if (searchByValue == '2') {
                    let start = $('#start_date_cy').val();
                    let end = $('#end_date_cy').val();
                    $('#start_date_txt').html(viewDateFormat(start));
                    $('#end_date_txt').html(viewDateFormat(end));

                }else if (searchByValue == '3') {
                    let start = $('#start_date_dr').val();
                    let end = $('#end_date_dr').val();
                    $('#start_date_txt').html(viewDateFormat(start));
                    $('#end_date_txt').html(viewDateFormat(end));
                }
                else if (searchByValue == '5') {
                    let start = $('#start_date_fy').val();
                    let end = $('#end_date_cy').val();
                    $('#start_date_txt').html(viewDateFormat(start));
                    $('#end_date_txt').html(viewDateFormat(end));
                }

            }else if (typeof ($('#' + searchById).val()) == 'undefined' || $('#' + searchById).val() == '') {
                $('#start_date_txt').val(' ');
                $('#end_date_txt').val(' ');

                $('#start_date_txt').hide();
                $('#text_to').hide();
                $('#end_date_txt').hide();
            }

        @endif

    }
</script>

@include('elements/report/common_filter/fo_script_for_select_options_common')
@include('elements/report/common_filter/fo_script_for_select_options_pos')
@include('elements/report/common_filter/fo_script_for_select_options_mfn')
@include('elements/report/common_filter/fo_script_for_select_options_hr')
@include('elements/report/common_filter/fo_script_for_select_options_acc')
@include('elements/report/common_filter/fo_script_for_select_options_hms')
