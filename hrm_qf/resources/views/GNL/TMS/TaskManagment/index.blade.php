@extends('Layouts.erp_master')
@section('content')

<?php
    use App\Services\TmsService as TMS;

    $moduleData = DB::table('gnl_sys_modules')->where([['is_delete', 0]])->get();
    // , ['is_active', 1]
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
            <label class="input-title">Status</label>
            <select class="form-control clsSelect2" id="status_id">
                <option value="">All</option>
                <option value="-1">Draft</option>
                <option value="1">Approve and Incomplete</option>
                <option value="2">Working</option>
                <option value="5">Complete</option>
                <option value="6">Reject</option>
            </select>
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
                        <th>Task Code</th>
                        <th>Task Title</th>
                        <th>Task Type</th>
                        <th>Module</th>
                        <th>Assigned By</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Stage</th>
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
                        "status_id": $('#status_id').val(),
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
                        data: 'task_code',
                        orderable: false,
                        className: 'text-center',
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
                        data: 'assigned_to',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
                        width: '8%'
                    },
                    {   data: null,
                        defaultContent: "",
                        className: 'text-center d-print-none',
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

                    let htmlaprove = '';
                    let htmlStatusTx = '';
                    // if (aData.status == 0){
                    //     htmlaprove += '<a href="{{ url()->current() }}/approve/'+ aData.task_code +'" title="Approve" class="btn btn-sm btn-primary">Approve</a>';
                    // } else if (aData.status == 1){
                    //     htmlaprove += '<a href="{{ url()->current() }}/approve/'+ aData.task_code +'" title="Working" class="btn btn-sm btn-danger">Working</a>';
                    // } else if (aData.status == 2){
                    //     htmlaprove += '<a href="{{ url()->current() }}/approve/'+ aData.task_code +'" title="Complete" class="btn btn-sm btn-success">Complete</a>';
                    // } else if (aData.status == 5){
                    //     htmlaprove += '<i class="fa fa-check-circle text-success h4" aria-hidden="true"></i>';
                    // }

                    // if((aData.status == 1 || aData.status == 2) && aData.is_active == 0){
                    //     htmlaprove += '<a href="{{ url()->current() }}/approve/'+ aData.task_code +'" title="Accept" class="btn btn-sm btn-info">Accept</a>';
                    // }

                    if (aData.status == 0){
                        htmlaprove += '<a href="javascript:void(0);" onclick="updateStatus('+ aData.id +')" title="Approve" class="btn btn-sm btn-primary">Approve</a>';
                    } else if (aData.status == 1){
                        htmlaprove += '<a href="javascript:void(0);" onclick="updateStatus('+ aData.id +')" title="Working" class="btn btn-sm btn-danger">Working</a>';
                    } else if (aData.status == 2){
                        htmlaprove += '<a href="javascript:void(0);" onclick="updateStatus('+ aData.id +')" title="Complete" class="btn btn-sm btn-success">Complete</a>';
                    } else if (aData.status == 5){
                        htmlaprove += '<i class="fa fa-check-circle text-success h4" aria-hidden="true"></i>';
                    }

                    if((aData.status == 1 || aData.status == 2) && aData.is_active == 0){
                        htmlaprove += '<a href="javascript:void(0);" onclick="updateStatus('+ aData.id +')" title="Accept" class="btn btn-sm btn-info">Accept</a>';
                    }

                    if (aData.status == 0){
                        htmlStatusTx += '<span class="text-info">Draft</span>';
                    } else if (aData.status == 1){
                        htmlStatusTx += '<span class="text-primary">Approve and Incomplete</span>';
                    } else if (aData.status == 2){
                        htmlStatusTx += '<span class="text-danger">Working</span>';
                    } else if (aData.status == 5){
                        htmlStatusTx += '<span class="text-success">Completed</span>';
                    }

                    if((aData.status == 1 || aData.status == 2) && aData.is_active == 0){
                        htmlStatusTx += '<br>(<b>modified</b>)'
                    }

                    $('td:nth-child(9)', nRow).html(htmlStatusTx);
                    $('td:nth-child(10)', nRow).html(htmlaprove);

                    var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
                    $('td:last', nRow).html(actionHTML);
                },
            });
        }

        // $('#check').click(function(){
        //     console.log($('#check').val()); 
        // });
        function updateStatus(id)
        {
        //    var task_code = aData.task_code;
            $.ajax({
                method: "GET",
                url: "{{ route('approve') }}",
                dataType: "json",
                data: {
                    id: id,
                },
                success: function(response) {
                    // console.log(response);
                    if (response['alert-type'] == 'error') {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            text: response['message'],
                        });
                    } else {
                        $('.clsDataTable').DataTable().ajax.reload();
                        swal({
                            icon: response['alert-type'],
                            title: response['alert-type'],
                            text: response['message'],
                        });
                    }
                }
            });
        }

    </script>
@endsection
