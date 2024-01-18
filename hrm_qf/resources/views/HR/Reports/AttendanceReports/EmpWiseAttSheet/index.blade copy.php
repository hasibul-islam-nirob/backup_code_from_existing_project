@extends('Layouts.erp_master_full_width')
@section('content')

@php
    use App\Services\CommonService as Common;

    // ## Convension mismatch thats why variable name change
    $elementArray = array();

    // $elementArray['company'] = ['label' => 'Company', 'type' => 'select', 'id' => 'company_id', 'name' => 'company_id'];
    $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
    $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
    $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];
    $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id'];
    $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
    $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
    $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];

    // $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'monthYear', 'id' => 'startDate', 'name'=> 'startDate', 'required' => true];
    // $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'monthYear', 'id' => 'endDate', 'name'=> 'endDate', 'required' => true];


    $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'fromMonthYear', 'id' => 'startDate', 'name'=> 'startDate', 'required' => true];
    $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'toMonthYear', 'id' => 'endDate', 'name'=> 'endDate', 'required' => true];

    // $elementArray['select_box3'] = [
    //     'label'=>"Show report in Time ?",
    //     'type'=>'select',
    //     'id'=> 'withTime',
    //     'name' => 'withTime',
    //     'selected_value' => 'no',
    //     'options'=>['yes'=>'Yes', 'no'=>'No']
    // ];

    $ignoreElements = [
    'company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate'
    ];

@endphp

<form enctype="multipart/form-data" method="post" id="filterFormId">
    @csrf
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])
</form>

<div class="w-full show" style="display: none;">
    <div class="panel">
        <div class="panel-body panel-search pt-2">

            @include('elements.report.company_header')

            @include('elements.report.reporting_header', [
                'reportTitle' => 'Employee Wise Attendance Sheet',
                'title_excel' => 'Employee_Wise_Attendance_Sheet',
                'incompleteBranch' => false,
                'elements' => $elementArray,
                'ignoreElements' => $ignoreElements
            ])


            <div class="row ExportDiv" id="reportingDiv">&nbsp;</div>
        </div>
    </div>
</div>


    <!-- End Page -->
    <script>


    $(document).ready(function(event){

        // $('#monthYear').prop('required', 'required');
        // $('#startDate, #endDate').prop('required', 'required');


        $("#searchButton").on('click', function(){
            let startDate = $( "#fromMonthYear" ).val();
            let endDate = $( "#toMonthYear" ).val();

            if (startDate > endDate) {
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Start Date must be smallest..'
                });
            }
        })



        $('#filterFormId').submit(function (event) {
            event.preventDefault();

            $("#reportingDiv").empty();

            var flag = true;

            // if($('#branch_id').val != ''){
            //     flag = true;
            // }
            // else if($('#monthYear').val != ''){
            //     flag = true;
            // }

            if(flag){
                $('.show').show('slow');
                $("#reportingDiv").load('{{ url()->current() }}/body'+'?'+$("#filterFormId").serialize());
            }

            // fnLoading(true);
        });


        // $("#toMonthYear").on('change', function(event){
        //     let startDate = $( "#fromMonthYear" ).val();
        //     let endDate = $( "#toMonthYear" ).val();

        //     console.log(endDate);
        //     if(startDate > endDate){
        //         $("#searchButton").addClass("disabled")
        //         swal({
        //             icon: 'warning',
        //             title: 'Oops...',
        //             text: 'Start Date must be smallest..'
        //         });
        //     }else{
        //         $("#searchButton").removeClass("disabled")
        //     }
        // })


        // ========= Date Range Start ================
        var startDate;
        var endDate;
        $('#fromMonthYear').change(function() {
            endDate = $(this).datepicker('getDate');
            endDate.setDate(endDate.getDate() + 365);
            $("#toMonthYear").datepicker("option", "maxDate", endDate,{ yearRange: '2000:+10',});
            $("#toMonthYear").datepicker("option", "minDate", $(this).datepicker('getDate')  );
        })


        $('#toMonthYear').change(function() {
            startDate = $(this).datepicker('getDate');
            startDate.setDate(startDate.getDate() - 365);
            $("#fromMonthYear").datepicker("option", "minDate", startDate,{ yearRange: '2000:+10',});
            $("#fromMonthYear").datepicker("option", "maxDate", $(this).datepicker('getDate')  );
        })
        // ========= Date Range End ================




    });


    // $(document).ready(function(){

    //     $('#monthYear').datepicker({
    //         // dateFormat: 'MM-yy',
    //         dateFormat: 'MM-yy',
    //         // todayHighlight: true,
    //         changeMonth: true,
    //         changeYear: true,
    //         showButtonPanel: true,
    //         todayButton: false,

    //         onClose: function(dateText, inst) {
    //             var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
    //             var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
    //             $(this).val($.datepicker.formatDate('MM-yy', new Date(year, month, 1)));
    //         },

    //         beforeShow: function() {
    //             if ((selDate = $(this).val()).length > 0){

    //                 year = selDate.substring(selDate.length - 4, selDate.length);
    //                 month = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
    //                 $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
    //                 $(this).datepicker('setDate', new Date(year, month, 1));

    //             }
    //         }
    //     });
    // })


    </script>
@endsection

