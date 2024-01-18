@extends('Layouts.erp_master')
@section('content')

<?php
    use App\Services\TmsService as TMS;

    $moduleData = DB::table('gnl_sys_modules')->where([['is_delete', 0]])->get();
    $employeeData = DB::table('hr_employees')
        ->where([['is_delete', 0]])
        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
        ->orderBy('emp_code', 'ASC')
        ->get();

    $TaskTypeData = TMS::fnGetAllTaskType();

?>
    <!-- Search Option Start -->
    <div class="row d-print-none">
        <div class="col-lg-3">
            <label class="input-title">Module</label>
            <select class="form-control clsSelect2" id="module">
                <option value="">All</option>

                @foreach ($moduleData as $Row)
                    <option value="{{ $Row->id }}">{{ $Row->module_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3">
            <label class="input-title">Task Type</label>
            <select class="form-control clsSelect2" id="task_type">
                <option value="">All</option>

                @foreach ($TaskTypeData as $Row)
                    <option value="{{ $Row->id }}">{{ $Row->type_name . " [". $Row->task_type_code . "]"}}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3">
            <label class="input-title">Employee</label>
            <select class="form-control clsSelect2" id="filter_emp_id">
                <option value="">All</option>

                @foreach ($employeeData as $row)
                    <option value="{{ $row->id }}">{{ $row->emp_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3">
            <label class="input-title">Assigned By</label>
            <select class="form-control clsSelect2" id="filter_assigned_by">
                <option value="">All</option>

                @foreach ($employeeData as $row)
                    <option value="{{ $row->id }}">{{ $row->emp_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3">
            <label class="input-title">Start Date</label>
            <input type="text" class="form-control datepicker" id="task_start_date" name="task_date" placeholder="DD-MM-YYYY" value="" autocomplete="off">
        </div>

        <div class="col-lg-3">
            <label class="input-title">End Date</label>
            <input type="text" class="form-control datepicker" id="task_end_date" placeholder="DD-MM-YYYY" value="" autocomplete="off">
        </div>

        <div class="col-sm-2">
            <a href="javascript:void(0)" id="searchFieldBtn" class="btn btn-primary btn-round text-uppercase mt-4">
                Search
            </a>
        </div>
    </div>
    <br>
    <!-- Search Option End -->

    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Task Date</th>
                        <th>Employee Name</th>
                        <th>Task Title</th>
                        <th>Task Type</th>
                        <th>Module</th>
                        <th>Assigned By</th>
                        <th style="width:15%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

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
                order: [[ 1, "DESC" ]],
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        "module_id": $('#module').val(),
                        "task_type_id": $('#task_type').val(),
                        "emp_id": $('#filter_emp_id').val(),
                        "filter_assigned_by": $('#filter_assigned_by').val(),
                        "start_date": $('#task_start_date').val(),
                        "to_date": $('#task_end_date').val(),
                    }
                },
                columns: [{
                        data: 'sl',
                        className: 'text-center',
                        orderable: false,
                        width: '3%'
                    },
                    {
                        data: 'task_date',
                        orderable: false,
                        width: '8%',
                        className: 'text-center',
                    },
                    {
                        data: 'emp_info',
                        orderable: false,
                    },
                    {
                        data: 'task_title',
                        orderable: false,
                    },
                    {
                        data: 'task_type',
                        orderable: false,
                    },
                    {
                        data: 'module_id',
                        orderable: false,
                    },
                    {
                        data: 'assigned_by',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none',
                        width: '8%'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }

        $('#task_start_date').click(()=>{
            $('#task_start_date').val("");
        })

        $('#task_end_date').click(()=>{
            $('#task_end_date').val("");
        })

    </script>
@endsection
