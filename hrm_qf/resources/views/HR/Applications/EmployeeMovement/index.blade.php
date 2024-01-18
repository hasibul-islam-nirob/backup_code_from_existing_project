@extends('Layouts.erp_master')
@section('content')
<link href="{{ asset('assets/css-js/datetimepicker-master/jquery.datetimepicker.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/css-js/datetimepicker-master/build/jquery.datetimepicker.full.min.js') }}"></script>

    <!-- Search Option Start -->
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
            'label'=>"Movement Code",
            'type'=>'text',
            'id'=> 'se_movement_code',
            'name' => 'se_movement_code',
        ];
        $elementArray['branchTo'] = ['label' => 'Branch To', 'type' => 'select', 'id' => 'branch_to_id', 'name' => 'branch_to_id'];
        $elementArray['select_box2'] = [
            'label'=>"Movement Area",
            'type'=>'text',
            'id'=> 'se_movement_area',
            'name' => 'se_movement_area',
        ];
        $elementArray['statusBox'] = ['label' => 'Status', 'type' => 'status', 'id' => 'status', 'name' => 'status', 'module' => 'HR','onload' => '1'];
        // $elementArray['purpose'] = ['label' => 'Purpose', 'type' => 'status', 'id' => 'purpose', 'name' => 'purpose', 'module' => 'HR','onload' => '1'];

        $elementArray['select_box1'] = [
            'label'=>"Purpose",
            'type'=>'select',
            'id'=> 'purpose',
            'name' => 'purpose',
            'selected_value' => ' ',
            'options'=>[''=>'All', 'official'=>'Official', 'personal'=>'Personal']
        ];

        $elementArray['select_box3'] = [
            'label'=>"Application For",
            'type'=>'select',
            'id'=> 'application_for',
            'name' => 'application_for',
            'selected_value' => ' ',
            'options'=>[''=>'All', 'early'=>'Early', 'late'=>'Late', 'absent'=>'Absent']
        ];


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
                        <th>Movement Code</th>
                        <th>Movement Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Purpose</th>
                        <th>Application <br>For</th>
                        <th>Employee Name [Code]</th>
                        <th>Branch From</th>
                        <th>Movement To</th>
                        {{-- <th>Application Date</th> --}}
                        {{-- <th>Area</th> --}}
                        {{-- <th>Application For</th> --}}
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
                        "region_id" : $('#region_id').val(),
                        "start_date": $('#startDate').val(),
                        "end_date": $('#endDate').val(),
                        "zone_id": $('#zone_id').val(),
                        "area_id": $('#area_id').val(),
                        "branch_id": $('#branch_id').val(),
                        "employee_id": $('#employee_id').val(),
                        "designation_id": $('#designation_id').val(),
                        "department_id": $('#department_id').val(),
                        "appl_code": $('#se_movement_code').val(),
                        "appl_status": $('#status').val(),
                        "branch_to": $('#branch_to_id').val(),
                        "movement_area": $('#se_movement_area').val(),
                        "purpose": $('#purpose').val(),
                        "application_for": $('#application_for').val()
                    }
                },
                columns: [
                    {data: 'id', className: 'text-center', orderable: false, width: '5%'},
                    {data: 'movement_code',orderable: false},
                    {data: 'movement_date',orderable: false,className: 'text-center'},
                    {data: 'start_time',orderable: false, className: 'text-center'},
                    {data: 'end_time',orderable: false,className: 'text-center'},
                    // {data: 'location_to',orderable: false,className: 'text-center'},
                    {data: 'reason', orderable: false, /*className: 'text-center'*/},
                    {data: 'appl_for', orderable: false, /*className: 'text-center'*/},

                    {data: 'employee_name',orderable: false},
                    {data: 'branch', orderable: false},
                    {data: 'location_to_branch', orderable: false},
                    // {data: 'appl_date',orderable: false,className: 'text-center'},

                    {data: 'status', orderable: false,className: 'text-center'},
                    {data: 'action', orderable: false,className: 'text-center d-print-none'},
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
