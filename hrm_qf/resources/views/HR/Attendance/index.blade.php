@extends('Layouts.erp_master')
@section('content')

    @php
        use App\Services\CommonService as Common;

        // ## Convension mismatch thats why variable name change
        $elementArray = array();

        // $elementArray['company'] = ['label' => 'Company', 'type' => 'select', 'id' => 'company_id', 'name' => 'company_id'];
        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'filter_zone_id', 'name' => 'zone_id'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'filter_region_id', 'name' => 'region_id'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'filter_area_id', 'name' => 'area_id'];
        $elementArray['branch'] = ['label' => 'Branch', 'type' => 'select', 'id' => 'filter_branch_id', 'name' => 'branch_id'];
        $elementArray['department'] = ['label' => 'Department', 'type' => 'select', 'id' => 'filter_department_id', 'name' => 'department_id', 'onload' => '1'];
        $elementArray['designation'] = ['label' => 'Designation', 'type' => 'select', 'id' => 'filter_designation_id', 'name' => 'designation_id', 'onload' => '1'];
        $elementArray['employee'] = ['label' => 'Employee', 'type' => 'select', 'id' => 'filter_employee_id', 'name' => 'employee_id', 'onload' => '1'];

        $elementArray['startDate'] = ['label' => 'From Date', 'type'=>'startDate', 'id' => 'startDate', 'name'=> 'startDate'];
        $elementArray['endDate'] = ['label' => 'To Date', 'type'=>'endDate', 'id' => 'endDate', 'name'=> 'endDate'];

    @endphp
    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:1%;">SL</th>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Designamtion</th>
                        <th>Attendance <br> Date & Time</th>

                        <th>Entry Time</th>
                        <th>Entry By</th>
                        {{-- <th>Time</th> --}}
                        <th style="width:7%;" class="text-center">Action</th>
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
            // end hide toggle btn and panel border

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

                        "zone_id": $('#filter_zone_id').val(),
                        "region_id": $('#filter_region_id').val(),
                        "area_id": $('#filter_area_id').val(),
                        "branch_id": $('#filter_branch_id').val(),
                        "employee_id": $('#filter_employee_id').val(),
                        "designation_id": $('#filter_designation_id').val(),
                        "department_id": $('#filter_department_id').val(),
                        // "emp_code": $('#se_emp_code').val(),
                        // "appl_status": $('#appl_status').val(),
                    }
                },
                order: [[2, "ASC"]],
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'employee',
                        orderable: false,
                    },
                    {
                        data: 'branch_id',
                        orderable: false,
                    },
                    {
                        data: 'department_id',
                        orderable: false,
                    },
                    {
                        data: 'designation_id',
                        orderable: false,
                    },
                    {
                        data: 'time_and_date',
                        orderable: false,
                        className: 'text-center',
                    },

                    {
                        data: 'created_at',
                        orderable: false,
                        className: 'text-center',
                    },
                    {
                        data: 'created_by',
                        orderable: false,
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
