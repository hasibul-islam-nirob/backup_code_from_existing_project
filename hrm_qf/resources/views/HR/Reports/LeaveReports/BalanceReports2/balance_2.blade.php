@extends('Layouts.erp_master_full_width')
@section('content')

    <form enctype="multipart/form-data" method="post" id="lv_balance_search_form">
        @csrf
        <!-- Search Option Start -->
        @include('elements.report.report_filter_options', [
            'branch' => true,
            'zone' => true,
            'area' => true,
            'department' => true,
            'designation' => true,
            'searchBy' => true,
            'currentYear' => true,
            // 'dateRange' => true,
            'textField' => [
                'field_text' => 'Employee Code',
                'field_id' => 'emp_code',
                'field_name' => 'emp_code',
                'field_value' => null
            ],
            // 'leaveType' => true,
            'leaveCat' => true,
            'zeroBalance' => true,
        ])
        <!-- Search Option End -->
    </form>


    <div class="w-full show" style="display: none;">
        <div class="panel">
            <div class="panel-body panel-search pt-2">

                @include('elements.report.company_header', [
                    'reportTitle' => 'Leave Balance Report',
                    'title_excel' => 'Leave_Balance_Report',
                    'printIcon' => true,
                    ])


                @include('elements.report.all_report_filter_view', ['elements' => [
                    'zone_id' => 'Zone',
                    'area_id' => 'Area',
                    'branch_id' => 'Branch',
                    'designation_id' => 'Designation',
                    'department_id' => 'Department',
                    'emp_code' => 'Employee Code'
                ]])

                <div class="row ExportDiv">
                    <div class="col-xl-12 col-lg-12 col-sm-12 col-md-12 col-12" id="report_body">
                        {{-- <div ></div> --}}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- End Page -->
    <script>
        $(document).ready(function(event){

            $('#searchButton').click(function(event){

                $('.show').show('slow');

                if($('#search_by').val() == ''){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select search by',

                    });
                }
                else if($('#leave_type_id').val() == 2 && $('#leave_cat_id').val() != ''){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Non pay leave have no leave category',

                    });
                }
                else{
                    $("#report_body").load('{{URL::to("hr/reports/balance_2/balance_report_body_2")}}'+'?'+$("#lv_balance_search_form").serialize());
                }

            });

        });

    </script>
@endsection

