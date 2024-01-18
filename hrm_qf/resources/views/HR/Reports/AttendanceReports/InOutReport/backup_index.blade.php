@extends('Layouts.erp_master_full_width')
@section('content')

    <form enctype="multipart/form-data" method="post" id="filterFormId">
        @csrf
        <!-- Search Option Start -->
        @include('elements.report.report_filter_options', [
            'branch' => true,
            'zone' => true,
            'area' => true,
            'monthYear'=> [true],
            'department' => true,
            'designation' => true,
            'submit'  => true,
            // 'gender' => true,
            // 'religion' => true,
            // 'maritalStatus' => true,
            // 'textField' => [
            //     'field_text' => 'Employee Code',
            //     'field_id' => 'emp_code',
            //     'field_name' => 'emp_code',
            //     'field_value' => null
            // ],
            // 'employeeStatus' => true,
        ])
        <!-- Search Option End -->
    </form>


    <div class="w-full show" style="display: none;">
        <div class="panel">
            <div class="panel-body panel-search pt-2">
                
                @include('elements.report.company_header', [
                    'reportTitle' => 'Employee Report',
                    'title_excel' => 'Leave_Consume_Report',
                    'printIcon' => true,
                    ])
                

                @include('elements.report.all_report_filter_view', ['elements' => [
                    'zone_id' => 'Zone',
                    'area_id' => 'Area',
                    'branch_id' => 'Branch',
                    'designation_id' => 'Designation',
                    'department_id' => 'Department',
                    // 'emp_status' => 'Status',
                    // 'join_start_date' => 'Joining Date From',
                    // 'join_end_date' => 'Joining Date To',
                    // 'emp_code' => 'Employee Code',
                    // 'emp_religion' => 'Employee Religion',
                    // 'emp_gender' => 'Employee Gender',
                    // 'emp_marital_status' => 'Employee Marital Status'
                ]])

                <div class="w-full show" style="display: none;">
                    <div class="panel">
                        <div class="panel-body panel-search pt-2" id="reportingDiv">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- End Page -->
    <script>
        $(document).ready(function(event){

            $('#month_year').prop('required', 'required');
    
            $('#filterFormId').submit(function (event) {
                event.preventDefault();

                $("#reportingDiv").empty();

                var flag = false;

                if($('#branch_id').val != ''){
                    flag = true
                }
                else if($('#month_year').val != ''){
                    flag = true
                }

                if(flag){
                    $('.show').show('slow');
                    $("#reportingDiv").load('{{URL::to("hr/reports/attendance_in_out/body")}}'+'?'+$("#filterFormId").serialize());
                }

            });
            
        });
        
    </script>
@endsection

