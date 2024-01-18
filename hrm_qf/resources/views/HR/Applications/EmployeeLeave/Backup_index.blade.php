@extends('Layouts.erp_master')
@section('content')

    <!-- Search Option Start -->
    @include('elements.common_filter_options', [
        'branch' => true,
        'zone' => true,
        'area' => true,
        'dateFields' => [
        [
            'field_text' => 'Date From',
            'field_id' => 'lev_start_date',
            'field_name' => 'lev_start_date',
            'field_value' => null
        ],
        [
            'field_text' => 'Date To',
            'field_id' => 'lev_end_date',
            'field_name' => 'lev_end_date',
            'field_value' => null
        ]
        ],
        'employee' =>true,
        'employeeFieldLabel' => 'Employee',
        'department' => true,
        'designation' => true,
        'textField' => [
            'field_text' => 'Leave Code',
            'field_id' => 'se_leave_code',
            'field_name' => 'se_leave_code',
            'field_value' => null
        ],
        'applicationStatus' => true,
    ])
    <!-- Search Option End -->

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Leave Code</th>
                        <th>Employee Name [Code]</th>
                        <th>Branch [Code]</th>
                        <th>Application Date</th>
                        <th>Responsible Person</th>
                        <th>Leave Category</th>
                        <th>Date From</th>
                        <th>Date To</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}

    <!-- End Page -->
    <script>
        $(document).ready(function() {
            $('.ajaxRequest').show();
            $('.httpRequest').hide(); //Hide new entry button
            ajaxDataLoad();
        });

        $('#searchFieldBtn').click(function(){
            ajaxDataLoad();
        });

        function ajaxDataLoad() {

            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                stateDuration: 1800,
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        "start_date": $('#lev_start_date').val(),
                        "end_date": $('#lev_end_date').val(),
                        "zone_id": $('#zone_id').val(),
                        "area_id": $('#area_id').val(),
                        "branch_id": $('#branch_id').val(),
                        "employee_id": $('#employee_id').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                        "appl_code": $('#se_leave_code').val(),
                        "appl_status": $('#appl_status').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'leave_code',
                        orderable: true,
                    },
                    {
                        data: 'employee_name',
                        orderable: true,
                    },
                    {
                        data: 'branch',
                        orderable: true,
                    },
                    {
                        data: 'leave_date',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'resp_employee_name',
                        orderable: false,
                        // className: 'text-center',
                    },
                    {
                        data: 'leave_cat',
                        orderable: true,
                    },
                    {
                        data: 'date_from',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'date_to',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'reason',
                        orderable: true,
                        // className: 'text-center',
                    },
                    {
                        data: 'status',
                        orderable: true,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action
                        .action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }
    </script>
@endsection
