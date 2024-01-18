@extends('Layouts.erp_master')
@section('content')

    <style>
        .select2-container {
            z-index: 100000;
        }
        .filterDiv {
            z-index: 0;
        }
    </style>
    @php
    use App\Services\CommonService as Common;

    // ## Convension mismatch thats why variable name change
    $elementArray = array();

    // $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
    // $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
    // $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];
    // $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id'];
    // $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
    // $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
    $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];


    // $allApplModel = [
    //     0 => 'EmployeeResign',
    //     1 => 'EmployeePromotion',
    //     2 => 'EmployeeDemotion',
    //     3 => 'EmployeeDismiss',
    //     4 => 'EmployeeTerminate',
    //     5 => 'EmployeeTransfer',
    //     6 => 'EmployeeActiveResponsibility',
    //     7 => 'EmployeeContractConclude',
    //     8 => 'EmployeeRetirement',
    //     9 => 'EmployeeLeave',
    //     10 => 'EmployeeMovement',
    //     11 => 'AppAdvanceSalary',
    //     12 => 'AppSecurityMoney',
    //     13 => 'HrApplicationLoan',
    // ];
    $elementArray['select_box3'] = [
        'label'=>"Application Type",
        'type'=>'select',
        'id'=> 'application_type',
        'name' => 'application_type',
        'selected_value' => 'all',
        'options'=>[
            ''=>'All',
            '9'=>'Leave Application',
            '10'=>'Movement Application',
            ]
    ];



    // $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => false];
    // $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate', 'required' => false];

    // $elementArray['select_box2'] = [
    //     'label'=>"Code",
    //     'type'=>'text',
    //     'id'=> 'application_code',
    //     'name' => 'application_code',
    // ];



    $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];

@endphp


    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])


    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Application Code</th>
                        <th>Application Type</th>
                        <th>Applicant Name</th>
                        {{-- <th>Applying date</th> --}}
                        <th>Date</th>
                        <th>Expected Effective Date</th>
                        <th>Current Stage <small>( Branch - Dept - Desig)</small> </th>
                        <th>Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}

    <script>
        $(document).ready(function() {

            // start hide toggle btn and panel border
            $('.panel-heading').hide();
            $('.filterDiv').css('box-shadow', 'none');
            // end hide toggle btn and panel border

            $('.page-header-actions').hide(); //Hide new entry button
            ajaxDataLoad();

            // $('#searchButton').attr('disabled', true);
        });

        $('#searchButton').click(function(){
            ajaxDataLoad();
        });

        function ajaxDataLoad() {
            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                stateSave: true,
                stateDuration: 1800,
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        // "zone_id": $('#zone_id').val(),
                        // "area_id": $('#area_id').val(),
                        // "branch_id": $('#branch_id').val(),
                        // "designation_id": $('#designation_id').val(),
                        // "department_id": $('#department_id').val(),
                        "employee_id": $('#employee_id').val(),
                        "application_type": $('#application_type').val(),
                        // "start_date": $('#startDate').val(),
                        // "end_date": $('#endDate').val(),
                        // "application_code": $('#application_code').val(),
                    }
                },
                columns: [{
                        data: 'sl',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'application_code',
                        orderable: false,
                    },
                    {
                        data: 'application_type',
                        orderable: false,
                    },
                    {
                        data: 'applicant_name',
                        orderable: false,
                    },
                    // {
                    //     data: 'applying_date',
                    //     orderable: false,
                    //     className: 'text-center',
                    // },
                    {
                        data: 'start_end_date',
                        orderable: false,
                        className: 'text-center',
                    },
                    {
                        data: 'effective_date',
                        orderable: false,
                        className: 'text-center',
                    },
                    {
                        data: 'current_stage',
                        orderable: false,
                        // className: 'text-center'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },
                ],

                'fnRowCallback': function(nRow, aData, Index) {

                    let actionHTML = '';

                    if (aData.source == 'pa') {//Pending application
                        actionHTML += '<a data-link = ' + "{{ url()->current() }}/../view/3/" +
                            aData.application_cat + '/' + aData.id +
                            ' class="pendingApplicationView" href="#"><i class="icon wb-eye mr-2 blue-grey-600"></i></a>';
                    }
                    else if (aData.source == 'aa' || aData.source == 'ra') { //Approved application or Rejected application
                        actionHTML += '<a data-link = ' + "{{ url()->current() }}/../view/0/" +
                            aData.application_cat + '/' + aData.id +
                            ' class="pendingApplicationView" href="#"><i class="icon wb-eye mr-2 blue-grey-600"></i></a>';
                    }

                    $('td:last', nRow).html(actionHTML);
                },

            });
        }
    </script>

@endsection
