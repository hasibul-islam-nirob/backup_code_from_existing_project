@extends('Layouts.erp_master')
@section('content')

    <!-- Search Option Start -->
    {{-- @include('elements.common_filter_options', [
        'branch' => true,
        'zone' => true,
        'area' => true,
        'dateFields' => [
        [
            'field_text' => 'Start Date',
            'field_id' => 'acr_start_date',
            'field_name' => 'acr_start_date',
            'field_value' => null
        ],
        [
            'field_text' => 'End Date',
            'field_id' => 'acr_end_date',
            'field_name' => 'acr_end_date',
            'field_value' => null
        ]
        ],
        'employee' =>true,
        'employeeFieldLabel' => 'Employee',
        'department' => true,
        'designation' => true,
        'textField' => [
            'field_text' => 'Active Responsibility Code',
            'field_id' => 'se_active_responsibility_code',
            'field_name' => 'se_active_responsibility_code',
            'field_value' => null
        ],
        'applicationStatus' => true,
    ]) --}}
    @php
        $elementArray = array();

        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'area_id', 'name' => 'area_id'];
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'branch_id', 'name' => 'branch_id','onload' => '1', 'required' => false, 'withHeadOffice'=>true];
        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'designation_id', 'name' => 'designation_id', 'onload' => '1'];
        $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'employee_id', 'name' => 'employee_id', 'onload' => '1'];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate', 'required' => false];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate', 'required' => false];

        $elementArray['select_box2'] = [
            'label'=>"Active Responsibility Code",
            'type'=>'text',
            'id'=> 'se_active_responsibility_code',
            'name' => 'se_active_responsibility_code',
        ];

        $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];

        $ignoreElements = ['company', 'zone', 'region', 'area', 'branch', 'startDate', 'endDate' ];
    @endphp
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])
    <!-- Search Option End -->

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Active Responsibility Code</th>
                        <th>Employee Name [Code]</th>
                        <th>Branch [Code]</th>
                        <th>Current Designation</th>
                        <th>Designation To Promote</th>
                        <th>Active Responsibility Date</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th style="width:15%;" class="text-center">Action</th>
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

            $('.ajaxRequest').show();
            $('.httpRequest').hide(); //Hide new entry button
            ajaxDataLoad();
        });

        $('#searchButton').click(function(){
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
                        "start_date": $('#startDate').val(),
                        "end_date": $('#endDate').val(),
                        "zone_id": $('#zone_id').val(),
                        "area_id": $('#area_id').val(),
                        "branch_id": $('#branch_id').val(),
                        "employee_id": $('#employee_id').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                        "appl_code": $('#se_active_responsibility_code').val(),
                        "appl_status": $('#status').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'active_responsibility_code',
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
                        data: 'current_designation',
                        orderable: true,
                    },
                    {
                        data: 'designation_to_promote',
                        orderable: true,
                    },
                    {
                        data: 'active_responsibility_date',
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
