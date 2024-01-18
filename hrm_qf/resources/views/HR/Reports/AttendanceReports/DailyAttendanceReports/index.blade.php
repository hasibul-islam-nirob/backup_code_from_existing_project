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
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id', 'onload' => '1', 'required' => false, 'withHeadOffice'=>true];
        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
        $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];

        $elementArray['startDate'] = ['label' => 'Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => true];


        $elementArray['select_box3'] = [
            'label'=>"Status",
            'type'=>'select',
            'id'=> 'status',
            'name' => 'status',
            'selected_value' => 'all',
            'options'=>[
                'all'=>'All',
                'p'=>'Present (Regular)',
                'a'=>'Absent',
                'lp'=>'Present (Late)',
                'mp'=>'Present (Movement)',
                'pl'=>'Present (Leave)',

                ]
        ];

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
                    'reportTitle' => 'Daily Attendance Report',
                    'title_excel' => 'Leave_Consume_Report',
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

            $('#filterFormId').submit(function (event) {
                event.preventDefault();


                let dateOne = $( "#startDate" ).val();
                var d1 = new Date(dateOne);
                let dateTwo = $( "#endDate" ).val();
                var d2 = new Date(dateTwo);

                let y1 = d1.getFullYear();
                let y2 = d2.getFullYear();

                let startDate = new Date(d1.getFullYear(), d1.getMonth() , 1);
                let endDate = new Date(d2.getFullYear(), d2.getMonth() + 1, 0);

                const timeDiff = endDate.getTime() - startDate.getTime();
                const diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

                if (startDate > endDate) {
                    $( "#startDate" ).val('');
                    $( "#endDate" ).val('');

                    swal({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Start Date must be smallest..'
                    });

                }else if(diffDays > 365){

                    swal({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Maximum select 365 days ...'
                    });

                }



                $("#reportingDiv").empty();
                var flag = true;

                if(flag){
                    $('.show').show('slow');
                    $("#reportingDiv").load('{{ url()->current() }}/body'+'?'+$("#filterFormId").serialize());
                }

            });



            // ========= Date Range Start ================
            var startDate;
            var endDate;

            $('#startDate').change(function() {
                endDate = $(this).datepicker('getDate');
                endDate.setDate(endDate.getDate() + 365);
                $("#endDate").datepicker("option", "maxDate", endDate);
                $("#endDate").datepicker("option", "minDate", $(this).datepicker('getDate'));
            })


            $('#endDate').change(function() {
                startDate = $(this).datepicker('getDate');
                startDate.setDate(startDate.getDate() - 365);
                $("#startDate").datepicker("option", "minDate", startDate);
                $("#startDate").datepicker("option", "maxDate", $(this).datepicker('getDate')  );
            })
            // ========= Date Range End ================

        });

    </script>
@endsection

