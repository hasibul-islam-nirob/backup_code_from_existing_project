@php
    use App\Services\CommonService as Common;
    use App\Services\HrService as HRS;
@endphp

    <div class="col-lg-2">
        <label class="input-title RequiredStar">Search By</label>
        <div class="input-group">
            <select class="form-control clsSelect2" id="search_by" name="search_by">

                <option value="">Select</option>
                <option value="1">Fiscal Year</option>
                {{-- <option value="5">Fiscal Year until Date</option> --}}
                <option value="2">Current Year</option>
                <option value="3">Date Range</option>
                {{-- <option value="4">Month Wise</option> --}}


            </select>
        </div>
    </div>

    <!-- Select box for fiscal Year [Option 1]-->
    <div class="col-lg-2" style="display: none" id="fyDiv">

        <label class="input-title">Fiscal Year</label>
        <div class="input-group">

            {{-- {!! HTML::forLeavePayscaleFieldHr('fiscal_year','fiscal_year','HR') !!} --}}

            <select class="form-control clsSelect2" style="width: 100%"  name="fiscal_year" id="fiscal_year">
                <option value="">Select</option>
                @php
                // $fiscalYearData = Common::ViewTableOrder('gnl_fiscal_year',
                // [['is_delete', 0],['is_active', 1],['company_id', Common::getCompanyId()], ['fy_for', ['LFY']]],
                // ['id', 'fy_name','fy_start_date','fy_end_date'],
                // ['fy_name', 'ASC']);

                $fiscalYearData = HRS::getPayscaleYearData(Common::getCompanyId(), "LFY");

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
        <label class="input-title">Date To</label>
        <div class="input-group">
            <input type="text" class="form-control" id="end_date_cy" name="end_date_cy"
                placeholder="DD-MM-YYYY" autocomplete="off">
        </div>

        <input type="hidden" name="start_date_cy" id="start_date_cy">
        <input type="hidden" name="fy_name_cy" id="fy_name_cy">
    </div>

    <!-- Start Date Datepicker for Date Range [Option 3]--->
    <div class="col-lg-2" style="display: none" id="startDateDivDR">
        <label class="input-title">Start Date</label>
        <div class="input-group">
            <input type="text" class="form-control datepicker-custom" id="start_date_dr"
                name="start_date_dr" placeholder="DD-MM-YYYY" autocomplete="off" value="{{$StartDate}}">
        </div>
    </div>

    <!-- End Date Datepicker for Date Range [Option 3]--->
    <div class="col-lg-2" style="display: none" id="endDateDivDR">
        <label class="input-title">End Date</label>
        <div class="input-group">
            <input type="text" class="form-control datepicker-custom" id="end_date_dr" name="end_date_dr"
                placeholder="DD-MM-YYYY" autocomplete="off" value="{{$EndDate}}">
        </div>
    </div>

    <!-- month Datepicker for Date Range [Option 4]--->
    <div class="col-lg-2" style="display: none" id="monthDateDivDR">
        <label class="input-title">Month</label>
        <div class="input-group">
            <input type="text" class="form-control monthPicker" id="month_yr" name="month_yr"
                placeholder="MM-YYYY" autocomplete="off">
        </div>
    </div>


<script>

    var searchByG = "{{ (isset($searchBy) && $searchBy) ? 1 : 0 }}";
    console.log(searchByG);



    /* for Fiscal / Current / serchby */
    if (searchByG == 0) {
        $('#search_by').change(function () {
            // 1, 2 er jonno tader ajax a load hocche fnForSearchBy function

            let searchBy = $('#search_by').val();

            if (searchBy == "1" || searchBy == "5") {
                fnAjaxFiscalYear();
            }

            if (searchBy == "2") {
                // fnAjaxCurrentFY();
                fnForSearchBy();
            }

            if (searchBy == "3" || searchBy == "4") {
                fnForSearchBy();
            }
        });

        $("#fiscal_year").change(function () {
            fnForSearchBy();
        });
    }




</script>


<!-- functions For Fiscal / Current / serchby  -->
<script type="text/javascript">

    var rootStart = '';

    function fnAjaxFiscalYear() {
        let branchId = $('#branch_id').val();


        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: "{{ Common::getModuleByRoute() }}",
                currentFY: false
            },
            success: function (response) {

                if (response['status'] == 'success') {

                    let brOpeningDate = response['brOpeningDate'];
                    let loginSystemDate = response['loginSystemDate'];

                    // console.log("branch open-->", brOpeningDate);


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

                        if (loginSystemDate >= startDate && loginSystemDate <= endDate) {
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
        let branchId = $('#branch_id').val();

        if (branchId === "-1" || branchId === "-2" || branchId === -1 || branchId === -2) {
            branchId = 1;
        }

        $.ajax({
            method: "GET",
            url: "{{ url('ajaxCurrentFY') }}",
            dataType: "json",
            data: {
                branchId: branchId,
                moduleName: "{{ Common::getModuleByRoute() }}",
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

                rootStart = startArr;

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

            $('#end_date_cy').datepicker({
                dateFormat: 'dd-mm-yy',
                orientation: 'bottom',
                autoclose: true,
                todayHighlight: true,
                changeMonth: true,
                changeYear: true,
                yearRange: '1900:+10',
                minDate: new Date('2020-01-01'),
                maxDate: new Date(),

            });

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


    // $('#search_by').on('change', function() {
    //     let vvvv = $('#search_by').val();
    //     let fy1 = $('#start_date_fy').val();
    //     let fy2 = $('#end_date_fy').val();
    //     let cy1 = $('#start_date_cy').val();
    //     let cy2 = $('#end_date_cy').val();
    //     let dr1 = $('#start_date_dr').val();
    //     let dr2 = $('#end_date_dr').val();

    //     if (vvvv == 1) {
    //         console.log("fy1 = "+fy1);
    //         console.log("fy2 = "+fy2);

    //     }else if (vvvv == 2) {
    //         console.log("cy1 = "+cy1);
    //         console.log("cy2 = "+cy2);

    //     }else if (vvvv == 3) {
    //         console.log("dr1 = "+dr1);
    //         console.log("dr2 = "+dr2);
    //     }
    // })
</script>
