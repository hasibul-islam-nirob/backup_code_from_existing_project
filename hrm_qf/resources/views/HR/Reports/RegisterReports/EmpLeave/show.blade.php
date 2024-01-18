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

        $elementArray['select_box1'] = [
            'label'=>"Gender",
            'type'=>'select',
            'id'=> 'emp_gender',
            'name' => 'emp_gender',
            'selected_value' => ' ',
            'options'=>[''=>'Both', 'Male'=>'Male', 'Female'=>'Female']
        ];

        $elementArray['leaveCategory'] = ['label' => 'Leave Category', 'type' => 'select', 'id' => 'leave_cat_id', 'name' => 'leave_cat_id','required' => false, 'onload' => '1'];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => true];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate', 'required' => true];

        $elementArray['select_box2'] = [
            'label'=>"Leave Code",
            'type'=>'text',
            'id'=> 'leave_code',
            'name' => 'leave_code',
        ];

        $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];

        // $elementArray['select_box3'] = [
        //     'label'=>"Status",
        //     'type'=>'select',
        //     'id'=> 'status',
        //     'name' => 'status',
        //     'selected_value' => 'all',
        //     'options'=>[
        //         'all'=>'All', 
        //         'p'=>'Present', 
        //         'a'=>'Absent', 
        //         'lp'=>'Late Present', 
        //         'mp'=>'Movement Present',
        //         'al'=>'Annual Leave',
        //         'sl'=>'Sick Leave',
        //         'cl'=>'Casual Leave',
        //         ]
        // ];
        


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
                    'reportTitle' => 'Employee Leave Report',
                    'title_excel' => 'Employee_Leave_Report',
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
        $('.httpRequest').hide(); //Hide new entry button
        $(document).ready(function(event){

            $('#searchButton').click(function () {
                $('#filterFormId').submit(function (event) {
                    event.preventDefault();
                    // $("#reportingDiv").empty();
                    var flag = true;

                    if(flag){
                        $('.show').show('slow');
                        $("#reportingDiv").load('{{URL::to("hr/reports/emp_leave/loadData")}}'+'?'+$("#filterFormId").serialize());
                    }
                });
            })

            
            
        });
        
    </script>
@endsection

