@extends('Layouts.erp_master_full_width')
@section('content')

    <form enctype="multipart/form-data" method="post" id="employee_search_form">
        @csrf
        <!-- Search Option Start -->
        @include('elements.report.report_filter_options', [
            'branch' => true,
            'zone' => true,
            'area' => true,
            'dateFields' => [
            [
                'field_text' => 'Start Date',
                'field_id' => 'start_date',
                'field_name' => 'start_date',
                'field_value' => null
            ],
            [
                'field_text' => 'End Date',
                'field_id' => 'end_date',
                'field_name' => 'end_date',
                'field_value' => null
            ]
            ],
            'employee' => true,
            'department' => true,
            'designation' => true,
            'textField' => [
            'field_text' => 'Resign Code',
            'field_id' => 'se_resign_code',
            'field_name' => 'resign_code',
            'field_value' => null
        ],
        'applicationStatus' => true,
        ])
        <!-- Search Option End -->
    </form>


    <div class="w-full show" style="display: none;">
        <div class="panel">
            <div class="panel-body panel-search pt-2">
                
                @include('elements.report.company_header', [
                    'reportTitle' => 'Active Responsibility Report',
                    'title_excel' => 'Act_Response_Report',
                    'printIcon' => true,
                    ])
                

                @include('elements.report.all_report_filter_view', ['elements' => [
                    'designation_id' => 'Designation',
                    'department_id' => 'Department',
                    'appl_status' => 'Status',
                      
                ]])

                <div class="row ExportDiv">
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12" id="report_body">
                        {{-- <div></div> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- End Page -->
    <script>
        $('.httpRequest').hide(); //Hide new entry button
        $(document).ready(function(event){
    
            $('#searchButton').click(function(event){

                $('.show').show('slow');
                $("#report_body").load('{{URL::to("hr/reports/emp_resign/loadData")}}'+'?'+$("#employee_search_form").serialize());

            });
            
        });
        
    </script>
@endsection

