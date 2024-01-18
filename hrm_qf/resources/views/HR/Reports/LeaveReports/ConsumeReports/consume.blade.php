@extends('Layouts.erp_master_full_width')
@section('content')

    @php
        use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();

        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];

        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id','onload' => '1', 'required' => false, 'withHeadOffice'=>true];

        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
        $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];

        $elementArray['leaveType'] = ['label' => 'Leave Type', 'type' => 'select', 'id' => 'leave_type', 'name' => 'leave_type','required' => false, 'onload' => '1'];
        // $elementArray['leaveCategory'] = ['label' => 'Leave Category', 'type' => 'select', 'id' => 'leave_cat_id', 'name' => 'leave_cat_id','required' => false, 'onload' => '1'];

        $elementArray['searchBy'] = ['label' => 'Search By', 'type' => 'searchBy', 'id' => 'search_by',
            'name' => 'search_by', 'required' => true, 'onload' => '1', 'loadOption' => ['1', '2', '3', '5'], 'fiscalYearLoad'=>"LFY"];


        $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];

    @endphp


    <form enctype="multipart/form-data" method="post" id="filterFormId">
        @csrf
        @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])
    </form>

    <div class="w-full show" style="display: block;">
        <div class="panel">
            <div class="panel-body panel-search pt-2">

                @include('elements.report.company_header')

                @include('elements.report.reporting_header', [
                    'reportTitle' => 'Leave Consume Report',
                    'title_excel' => 'Leave_Consume_Report',
                    'incompleteBranch' => false,
                    'elements' => $elementArray,
                    'ignoreElements' => $ignoreElements
                ])


                <div class="row ExportDiv" id="reportingDiv">&nbsp;</div>
            </div>
        </div>
    </div>


    <script>

        $(document).ready(function () {
            $('.show').hide();
            $('#searchButton').click(function () {

                let selected = $('#search_by').val();
                let flagMessage = false;

                if (selected == '') {

                    flagMessage = "Please select an item from Search By.";

                } else if (selected == 1) { // fiscal year

                    let fiscal_year = $('#fiscal_year').val();
                    let fiscal_year_txt = $('#fiscal_year :selected').text();

                    if ($('#fiscal_year').val() == '') {
                        flagMessage = "Please select Fiscal Year.";
                    }

                }  else if (selected == 3) { // date range
                    if ($('#start_date_dr').val() == '' || $('#end_date_dr').val() == '') {
                        flagMessage = "Please select Date.";
                    }
                }

                if(flagMessage == false){
                    // $('.show').show('slow');

                    if ($("#filterFormId").length) {
                        fnLoading(true);
                    }

                    // $("#filterFormId").submit();
                }
                else{
                    swal({
                        icon: 'warning',
                        title: 'Warning',
                        text: flagMessage,
                        timer: 3000
                    }).then(function() {
                        $(".wb-plus").trigger('click');
                    });

                    return false;
                }
            });


            // // // Loader In-Active
            fnLoading(false);
        });



        var flag = false;
        $('#searchButton').click(function(){

            $('#filterFormId').submit(function (event) {
                event.preventDefault();
                // $("#reportingDiv").empty();
                var flag = true;

                if(flag){
                    $('.show').show('slow');
                    $("#reportingDiv").load('{{URL::to("hr/reports/consume/consume_report_body")}}'+'?'+$("#filterFormId").serialize());
                }
            });
            var flag = false;
        })


        // let search_by = $("#search_by").val();
        // if (search_by == 1 && start_date_txt != "") {
        //     let start = $('#start_date_fy').val();
        //     let end = $('#end_date_fy').val();
        //     $('#start_date_txt').html(viewDateFormat(start));
        //     $('#end_date_txt').html(viewDateFormat(end));

        // }else if (search_by == 2 && start_date_txt != "") {
        //     let start = $('#start_date_fy').val();
        //     let end = $('#end_date_dr').val();
        //     $('#start_date_txt').html(viewDateFormat(start));
        //     $('#end_date_txt').html(viewDateFormat(end));

        // }else if (search_by == 3 && start_date_txt != "") {
        //     let start = $('#start_date_dr').val();
        //     let end = $('#end_date_dr').val();
        //     $('#start_date_txt').html(viewDateFormat(start));
        //     $('#end_date_txt').html(viewDateFormat(end));
        // }




    </script>
@endsection

