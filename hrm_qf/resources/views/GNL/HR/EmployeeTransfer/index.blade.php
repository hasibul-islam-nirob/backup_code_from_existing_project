@extends('Layouts.erp_master')
@section('content')

    <!-- Search Option Start -->
    @include('elements.common_filter_options', [
        'branchFrom' => true,
        'branchTo' => true,
        'zone' => true,
        'area' => true,
        'dateFields' => [
        [
            'field_text' => 'Start Date',
            'field_id' => 'trn_start_date',
            'field_name' => 'trn_start_date',
            'field_value' => null
        ],
        [
            'field_text' => 'End Date',
            'field_id' => 'trn_end_date',
            'field_name' => 'trn_end_date',
            'field_value' => null
        ]
        ],
        'department' => true,
        'designation' => true,
        'textField' => [
            'field_text' => 'Transfer Code',
            'field_id' => 'se_transfer_code',
            'field_name' => 'se_transfer_code',
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
                        <th>Transfer Code</th>
                        <th>Employee Name (Code)</th>
                        <th>Transfer From (Code)</th>
                        <th>Transfer To (Code)</th>
                        <th>Application Date</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th style="width:15%;" class="text-center">Action</th>
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
                        "start_date": $('#trn_start_date').val(),
                        "end_date": $('#trn_end_date').val(),
                        "zone_id": $('#zone_id').val(),
                        "region_id": $('#region_id').val(),
                        "area_id": $('#area_id').val(),
                        "branch_from": $('#branch_from').val(),
                        "branch_to": $('#branch_to').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                        "appl_code": $('#se_transfer_code').val(),
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
                        data: 'transfer_code',
                        orderable: true,
                    },
                    {
                        data: 'employee_name',
                        orderable: true,
                    },
                    {
                        data: 'branch_from',
                        orderable: true,
                    },
                    {
                        data: 'branch_to',
                        orderable: true,
                    },
                    {
                        data: 'transfer_date',
                        orderable: true,
                        className: 'text-center',
                    },
                    {
                        data: 'effective_date',
                        orderable: true,
                        className: 'text-center',
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
