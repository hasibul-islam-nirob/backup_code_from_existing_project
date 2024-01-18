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

        $elementArray['district'] = ['label' => 'District', 'type' => 'select', 'id' => 'district_id_index', 'name' => 'district_id', 'exClass'=> 'c'];
        $elementArray['upazila'] = ['label' => 'Upazila', 'type' => 'select', 'id' => 'upazila_id_index', 'name' => 'upazila_id', 'exClass'=> 'c'];

        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
        $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];

        $elementArray['select_box1'] = [
            'label'=>"Gender",
            'type'=>'select',
            'id'=> 'emp_gender',
            'name' => 'emp_gender',
            'selected_value' => ' ',
            'options'=>[''=>'Both', 'Male'=>'Male', 'Female'=>'Female']
        ];

        $elementArray['select_box2'] = [
            'label'=>"Religion",
            'type'=>'select',
            'id'=> 'emp_religion',
            'name' => 'emp_religion',
            'selected_value' => ' ',
            'options'=>[
                ''=>'Select Religion', 
                'Islam'=>'Islam', 
                'Hinduism'=>'Hinduism',
                'Buddhists'=>'Buddhists',
                'Christians'=>'Christians',
            ]
        ];

        $elementArray['select_box3'] = [
            'label'=>"Marital Status",
            'type'=>'select',
            'id'=> 'emp_marital_status',
            'name' => 'emp_marital_status',
            'selected_value' => ' ',
            'options'=>[
                ''=>'Select Marital', 
                'Married'=>'Married', 
                'Unmarried'=>'Unmarried',
                'Divorced'=>'Divorced',
                'Widow'=>'Widow',
            ]
        ];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => false];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate', 'required' => false];


        $elementArray['dateOfBirth'] = ['label' => 'Date of Birth', 'type'=>'dateNotRange', 'id' => 'd_o_b', 'name'=> 'd_o_b', 'required' => false];
        $elementArray['joiningDate'] = ['label' => 'Joining Date', 'type'=>'dateNotRange', 'id' => 'joiningDate', 'name'=> 'joiningDate', 'required' => false];


        // $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];

        $elementArray['select_box4'] = [
            'label'=>"Status",
            'type'=>'select',
            'id'=> 'emp_status',
            'name' => 'emp_status',
            'selected_value' => ' ',
            'options'=>[
                ''=>'All', 
                '1'=>'Present', 
                '2'=>'Resigned',
                '3'=>'Dismissed',
                '4'=>'Terminated',
                '5'=>'Retired',
            ]
        ];


        $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];

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
                    'reportTitle' => 'Employee  Report',
                    'title_excel' => 'Employee_Report',
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
            $('#searchButton').click(function(){

                $('#filterFormId').submit(function (event) {
                    event.preventDefault();
                    // $("#reportingDiv").empty();
                    var flag = true;

                    if(flag){
                        $('.show').show('slow');
                        $("#reportingDiv").load('{{URL::to("hr/reports/employee_report/loadData")}}'+'?'+$("#filterFormId").serialize());
                    }
                });
            })
        });


        /*  USE JQURY Autocomplete Widget */       
        $('#district_id_index').select2({
            ajax: {
                url: "{{ url()->current() }}/../../employee/getData",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        context: 'searchDistrict'
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a district',
            minimumInputLength: 2
        });

        $('#upazila_id_index').select2({
            ajax: {
                url: "{{ url()->current() }}/../../employee/getData",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        context: 'searchUpazila'
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: 'Search for a upazila',
            minimumInputLength: 2
        });
        /* END */

    </script>
@endsection

