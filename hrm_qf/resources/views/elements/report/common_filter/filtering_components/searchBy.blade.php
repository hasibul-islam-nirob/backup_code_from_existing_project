@php
    use App\Services\CommonService as Common;
    use App\Services\HrService as HRS;
    $requestData = Request::all();
    $loadOptions = $element['loadOption'];
    $fiscalYearLoad = (isset($element['fiscalYearLoad'])) ? $element['fiscalYearLoad'] : "FFY";

    // dd($loadOptions);

    // dd($element);
@endphp
    <div class="col-lg-2">
        @if (isset($element['required']) && $element['required'])
            <label class="input-title RequiredStar">{{$element['label']}}</label>
        @else
            <label class="input-title">{{$element['label']}}</label>
        @endif
        <div class="input-group">
            <select class="form-control clsSelect2" id="search_by" name="search_by"
            @if (isset($element['required']) && $element['required'])
            required
            @endif>
                <option value="">Select</option>

                @if (in_array('1', $loadOptions))
                    <option value="1">Fiscal Year</option>
                @endif

                @if (in_array('2', $loadOptions))
                    <option value="2">Current Year</option>
                @endif

                @if (in_array('3', $loadOptions))
                    <option value="3">Date Range</option>
                @endif

                @if (in_array('4', $loadOptions))
                    <option value="4">Month Wise</option>
                @endif

                @if (in_array('5', $loadOptions))
                    <option value="5">Fiscal Year until Date</option>
                @endif

            </select>
        </div>
    </div>

    <!-- Select box for fiscal Year [Option 1]-->
    <div class="col-lg-2" style="display: none" id="fyDiv">

        <label class="input-title RequiredStar">Fiscal Year</label>
        <div class="input-group">

            {{-- {!! HTML::forLeavePayscaleFieldHr('fiscal_year','fiscal_year','HR') !!} --}}

            <select class="form-control clsSelect2" style="width: 100%"  name="fiscal_year" id="fiscal_year">
                <option value="">Select</option>
                @php
                // $fiscalYearData = Common::ViewTableOrder('gnl_fiscal_year',
                // [['is_delete', 0],['is_active', 1],['company_id', Common::getCompanyId()], ['fy_for', ['LFY']]],
                // ['id', 'fy_name','fy_start_date','fy_end_date'],
                // ['fy_name', 'ASC']);

                $fiscalYearData = HRS::getFiscalYearData(Common::getCompanyId(), $fiscalYearLoad);

                @endphp
                @foreach ($fiscalYearData as $Row)
                    @php
                        $start_date_fy = new DateTime($Row->fy_start_date);
                        $end_date_fy = new DateTime($Row->fy_end_date);

                        $loginSystemDate = new DateTime($EndDate);
                        $loginBranchOpenDate = new DateTime($branchOpenDate);

                        if($loginBranchOpenDate >= $start_date_fy && $loginBranchOpenDate <= $end_date_fy){
                            $start_date_fy=$loginBranchOpenDate;
                        }

                        if($loginSystemDate>= $start_date_fy && $loginSystemDate <= $end_date_fy){
                            $end_date_fy=$loginSystemDate;
                        }
                    @endphp

                    <option value="{{ $Row->id }}"
                        data-startdate="{{ $start_date_fy->format('d-m-Y') }}"
                        data-enddate="{{ $end_date_fy->format('d-m-Y') }}"
                        data-orginalStartdate="{{ $start_date_fy->format('d-m-Y') }}"
                        data-orginalEnddate="{{ $end_date_fy->format('d-m-Y') }}"
                        >
                        {{ $Row->fy_name }}
                    </option>
                @endforeach
            </select>

            <input type="hidden" name="start_date_fy" id="start_date_fy">
            <input type="hidden" name="end_date_fy" id="end_date_fy">
        </div>
    </div>

    <!-- End Date Datepicker for current year [Option 2]--->
    <div class="col-lg-2" style="display: none" id="endDateDivCY">
        <label class="input-title RequiredStar">Date To</label>
        <div class="input-group">
            <input type="text" class="form-control" id="end_date_cy" name="end_date_cy"
                placeholder="DD-MM-YYYY" autocomplete="off">
        </div>

        <input type="hidden" name="start_date_cy" id="start_date_cy">
        <input type="hidden" name="fy_name_cy" id="fy_name_cy">
    </div>

    <!-- Start Date Datepicker for Date Range [Option 3]--->
    <div class="col-lg-2" style="display: none" id="startDateDivDR">
        <label class="input-title RequiredStar">Start Date</label>
        <div class="input-group">
            <input type="text" class="form-control datepicker-custom" id="start_date_dr"
                name="start_date_dr" placeholder="DD-MM-YYYY" autocomplete="off" value="{{$StartDate}}">
        </div>
    </div>

    <!-- End Date Datepicker for Date Range [Option 3]--->
    <div class="col-lg-2" style="display: none" id="endDateDivDR">
        <label class="input-title RequiredStar">End Date</label>
        <div class="input-group">
            <input type="text" class="form-control datepicker-custom" id="end_date_dr" name="end_date_dr"
                placeholder="DD-MM-YYYY" autocomplete="off" value="{{$EndDate}}">
        </div>
    </div>

    <!-- month Datepicker for Date Range [Option 4]--->
    <div class="col-lg-2" style="display: none" id="monthDateDivDR">
        <label class="input-title RequiredStar">Month</label>
        <div class="input-group">
            <input type="text" class="form-control monthPicker" id="month_yr" name="month_yr"
                placeholder="MM-YYYY" autocomplete="off">
        </div>
    </div>

<script>

</script>

<!-- functions For Fiscal / Current / serchby  -->
<script type="text/javascript">

    function fnAjaxFiscalYear() {
        let moduleName = "{{ Common::getModuleByRoute() }}";
        let branchId = '';
        if(moduleName == 'mfn')
        {
            branchId = $('#branchId').val();
        }
        else
        {
            branchId = $('#branch_id').val();
        }

        console.log("- "+branchId);

        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: moduleName,
                currentFY: false,
                fiscalYearLoad: "{{ $fiscalYearLoad }}"
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let brOpeningDate = response['brOpeningDate'];
                    let loginSystemDate = response['loginSystemDate'];

                    console.log("branch open-->", response);


                    $("#fiscal_year option").each(function () {
                        let startDate = $(this).attr('data-orginalStartdate');
                        let endDate = $(this).attr('data-orginalEnddate');

                        // console.log("test1 --> ",startDate, endDate);

                        if (startDate === undefined && endDate === undefined) {
                            return true;
                        }

                        let startArr = startDate.split("-");
                        let endArr = endDate.split("-");

                        /////////////// Y-m-d
                        startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
                        endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

                        if (brOpeningDate >= startDate && brOpeningDate <= endDate) {
                            startDate = brOpeningDate;
                        }
                        else if(brOpeningDate >= endDate){
                            // branch open date end date theke boro hole select kora jabe na.
                            startDate = brOpeningDate;
                        }

                        if (loginSystemDate >= startDate || loginSystemDate <= endDate) {
                            endDate = loginSystemDate;
                        }

                        if (startDate >= endDate) {
                            // start date end date theke boro hole select kora jabe na.
                            $(this).attr('disabled', true);
                        }
                        else {
                            $(this).removeAttr("disabled");
                        }

                        startDate = $.datepicker.formatDate('dd-mm-yy', new Date(startDate));
                        endDate = $.datepicker.formatDate('dd-mm-yy', new Date(endDate));

                        // console.log("test2 --> ",startDate, endDate);

                        $(this).attr('data-startdate', startDate);
                        $(this).attr('data-enddate', endDate);

                        $(this).data('startdate', startDate);
                        $(this).data('enddate', endDate);



                    });

                    fnForSearchBy();

                    console.log('fn 10');

                } else if (response['status'] == 'error') {
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function () {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnAjaxCurrentFY() {
        let moduleName = "{{ Common::getModuleByRoute() }}";
        let branchId = '';
        if(moduleName == 'mfn')
        {
            branchId = $('#branchId').val();
        }
        else
        {
            branchId = $('#branch_id').val();
        }

        console.log("c "+branchId);

        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: moduleName,
                fiscalYearLoad: "{{ $fiscalYearLoad }}"
            },
            success: function (response) {
                if (response['status'] == 'success') {
                    var result_data = response['result_data'];

                    // let tempStartDate = $.datepicker.formatDate('dd-mm-yy', new Date(result_data['fy_start_date']));

                    let tempStartDate = new Date(result_data['fy_start_date']);
                    let tempEndDate = new Date(result_data['fy_end_date']);

                    let start_date = tempStartDate;
                    let start_date_ex = tempStartDate;

                    let loginBranchOpenDate = response['brOpeningDate'];

                    loginBranchOpenDate = new Date(loginBranchOpenDate);
                    if (loginBranchOpenDate >= tempStartDate && loginBranchOpenDate <= tempEndDate) {
                        start_date = loginBranchOpenDate;
                        start_date_ex = loginBranchOpenDate;
                    }

                    start_date = $.datepicker.formatDate('dd-mm-yy', new Date(start_date));

                    $("#start_date_cy").val(start_date);
                    $("#fy_name_cy").val(result_data['fy_name']);

                    // $("#end_date_cy").datepicker("option", "minDate", new Date(result_data['fy_start_date']));
                    $("#end_date_cy").datepicker("option", "minDate", start_date_ex);
                    $("#end_date_cy").datepicker("option", "maxDate", new Date(result_data['fy_end_date']));


                    fnForSearchBy();

                    console.log('fn 11');


                } else if (response['status'] == 'error') {
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: response['message'],
                        timer: 3000
                    }).then(function () {
                        // window.location = "{{url('/acc')}}";
                    });
                }
            }
        });
    }

    function fnForSearchBy() {
        let selected = $('#search_by').val();
        let start_date_txt = "";
        let end_date_txt = "";

        if (selected == 1 || selected == 5) { // fiscal year
            $('#endDateDivCY,#startDateDivDR,#endDateDivDR').hide('fast');
            $('#fyDiv').show('slow');

            if ($('#fiscal_year').val() != '') {
                let start_date_fy = $('#fiscal_year :selected').data('startdate');
                let end_date_fy = $('#fiscal_year :selected').data('enddate');

                $('#start_date_fy').val(start_date_fy);
                $('#end_date_fy').val(end_date_fy);

                let startArr = start_date_fy;
                let endArr = end_date_fy;

                startArr = startArr.split("-");
                endArr = endArr.split("-");

                /////////////// Y-m-d
                startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
                endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

                let pre_fiscal_start = (Number(startArr[2]) - 1);
                let pre_fiscal_end = startArr[2];

                let cur_fiscal_start = startArr[2];
                let cur_fiscal_end = endArr[2];

                if(cur_fiscal_start === cur_fiscal_end) {
                    cur_fiscal_end = Number(cur_fiscal_end) + 1;
                }

                $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
                $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);

                start_date_txt = start_date_fy;
                end_date_txt = end_date_fy;

                if(selected == 5){

                    let fyNameCy = $('#fiscal_year :selected').text();

                    $("#start_date_cy").val(start_date_fy);
                    $("#fy_name_cy").val(fyNameCy);

                    $("#end_date_cy").datepicker("option", "minDate", start_date_fy);
                    $("#end_date_cy").datepicker("option", "maxDate", end_date_fy);

                    $('#endDateDivCY').show('slow');

                    end_date_txt = $("#end_date_cy").val();
                }


                $('#start_date').val(start_date_txt);
                $('#start_date_txt').html(viewDateFormat(start_date_txt));
                $('#end_date').val(end_date_txt);
                $('#end_date_txt').html(viewDateFormat(end_date_txt));
                $('.title_date').html(end_date_txt);


            }

        } else if (selected == 2) { // current year
            $('#fyDiv,#startDateDivDR,#endDateDivDR').hide('fast');
            $('#endDateDivCY').show('slow');

            start_date_txt = $('#start_date_cy').val();
            end_date_txt = $('#end_date_cy').val();

            let startArr = start_date_txt;
            let endArr = end_date_txt;

            startArr = startArr.split("-");
            endArr = endArr.split("-");

            /////////////// Y-m-d
            startDate = startArr[2] + "-" + startArr[1] + "-" + startArr[0];
            endDate = endArr[2] + "-" + endArr[1] + "-" + endArr[0];

            let pre_fiscal_start = (Number(startArr[2]) - 1);
            let pre_fiscal_end = startArr[2];

            let cur_fiscal_start = startArr[2];
            let cur_fiscal_end = (Number(startArr[2]) + 1);

            if(cur_fiscal_start === cur_fiscal_end) {
                cur_fiscal_end = Number(cur_fiscal_end) + 1;
            }

            $('#prev_year').html(pre_fiscal_start + "-" + pre_fiscal_end);
            $('#current_year').html(cur_fiscal_start + "-" + cur_fiscal_end);

        }
        else if (selected == 3) { // date range
            $('#fyDiv,#endDateDivCY').hide('fast');
            $('#startDateDivDR,#endDateDivDR').show('slow');

            start_date_txt = $('#start_date_dr').val();
            end_date_txt = $('#end_date_dr').val();

        }
        else {
            $('#fyDiv,#endDateDivCY').hide('');
        }

        if (start_date_txt != "") {
            $('#start_date').val(start_date_txt);
            $('#start_date_txt').html(viewDateFormat(start_date_txt));
        }

        if (end_date_txt != "") {
            $('#end_date').val(end_date_txt);

            $('#end_date_txt').html(viewDateFormat(end_date_txt));
            $('.title_date').html(end_date_txt);
        }

        console.log('fn 12');
    }
</script>

<!-- ////////////////////// Function Declare End //////////////////////////// -->

<!-- on load -->
<script type="text/javascript">

    $(document).ready(function () {

        var searchByLoad = $('#search_by').val();

        if (searchByLoad == "1" || searchByLoad == "5") {
            fnAjaxFiscalYear();
        } else if (searchByLoad == "2") {
            fnAjaxCurrentFY();
        } else {
            fnForSearchBy();
        }

        /* Load Default */

        $('#fiscal_year').select2({
            'width': '100%'
        });
    });
</script>

<!-- on Change -->
<script type="text/javascript">
    $('#search_by').change(function () {

        let searchBy = $('#search_by').val();


    // $('#branch_id').change(function () {

    //     if (searchByG == 1) {
    //         let searchBy = $('#search_by').val();

    //         if (searchBy == "1" || searchBy == "5") {
    //             fnAjaxFiscalYear();
    //         }

    //         if (searchBy == "2") {
    //             fnAjaxCurrentFY();
    //         }

    //         if (searchBy == "3" || searchBy == "4") {
    //             fnForSearchBy();
    //         }
    //     }
    // });


    /* for Fiscal / Current / serchby */
    // if (searchByG == 1) {
    //     $('#search_by').change(function () {
    //         // 1, 2 er jonno tader ajax a load hocche fnForSearchBy function

    //         let searchBy = $('#search_by').val();

    //         if (searchBy == "1" || searchBy == "5") {
    //             fnAjaxFiscalYear();
    //         }

    //         if (searchBy == "2") {
    //             console.log("tesssssssssss");
    //             fnAjaxCurrentFY();
    //         }

    //         if (searchBy == "3" || searchBy == "4") {
    //             fnForSearchBy();
    //         }
    //     });

    //     $("#fiscal_year").change(function () {
    //         fnForSearchBy();
    //     });
    // }

    $('#search_by').change(function () {

        let searchBy = $('#search_by').val();

        if (searchBy == "1" ) {
            $('#fiscal_year').prop("required", true);
            $('#end_date_cy').prop("required", false);
        }
        else if (searchBy == "2" ) {
            $('#end_date_cy').prop("required", true);
            $('#fiscal_year').prop("required", false);
        }
        else if (searchBy == "5" ) {
            $('#fiscal_year').prop("required", true);
            $('#end_date_cy').prop("required", true);
        }
        else
        {
            $('#fiscal_year').prop("required", false);
            $('#end_date_cy').prop("required", false);
        }
    });
</script>

<!-- on Submit / click  -->
<script type="text/javascript">

    $('#refreshButton').click(function(event){
        window.location.href = window.location.href.split('#')[0];
    });

    $('#searchButton').click(function (event) {

        // $(".wb-minus").trigger('click');

        if ($("#filterFormId").length) {
            fnLoading(true);
        }

        showReportHeading('close');
        $("#filterFormId").submit();
    });
</script>
