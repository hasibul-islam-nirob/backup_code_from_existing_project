@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\CommonService as Common;
use App\Services\HtmlService as HTML;

$moduleCheck = Common::checkActivatedModule('pos');
?>

<!-- Search Option Start -->
@include('elements.common_filter_options', [
    'branch' => true,
    'zone' => true,
    'area' => true,
    // 'dateFields' => [
    // [
    //     'field_text' => 'Start Date',
    //     'field_id' => 'join_start_date',
    //     'field_name' => 'join_start_date',
    //     'field_value' => null
    // ],
    // [
    //     'field_text' => 'End Date',
    //     'field_id' => 'join_end_date',
    //     'field_name' => 'join_end_date',
    //     'field_value' => null
    // ]
    // ],
    'department' => true,
    'designation' => true,
    'gender' => true,
    'textField' => [
        'field_text' => 'Employee Code',
        'field_id' => 'se_emp_code',
        'field_name' => 'se_emp_code',
        'field_value' => null
    ],
    'employeeStatus' => true,
])
<!-- Search Option End -->

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:4%;">SL</th>
                        <th>Name</th>
                        <th>Emp. Code</th>
                        <th>Gender</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Branch</th>
                        <th>Org. Mobile</th>
                        <th>Personal Mobile</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- End Page -->
<script>
function ajaxDataLoad() {

    $('.clsDataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        stateDuration: 1800,
        order: [
                [2, "ASC"]
            ],
        // ordering: false,
        // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
        "ajax": {
            "url": "{{route('employeeDatatableGnl')}}",
            "dataType": "json",
            "type": "post",
            "data": {
                _token: "{{csrf_token()}}",
                // "start_date": $('#join_start_date').val(),
                // "end_date": $('#join_end_date').val(),
                "zone_id": $('#zone_id').val(),
                "region_id": $('#region_id').val(),
                "area_id": $('#area_id').val(),
                "branch_id": $('#branch_id').val(),
                "designation_id": $('#designation_id').val(),
                "department_id": $('#department_id').val(),
                "emp_gender": $('#emp_gender').val(),
                "emp_code": $('#se_emp_code').val(),
                "emp_status": $('#emp_status').val(),
            }
        },
        columns: [{
                data: 'id',
                className: 'text-center',
                orderable: false,
                width: '5%'
            },
            {
                data: 'emp_name',
                orderable: true,
            },
            {
                data: 'emp_code',
                orderable: true,
            },
            {
                data: 'emp_gender',
                className: 'text-center',
                orderable: false,
            },
            {
                data: 'designation',
                orderable: true,
            },
            {
                data: 'department',
                orderable: true,
            },
            {
                data: 'branch',
                orderable: true,
            },
            {
                data: 'org_phone_number',
                orderable: false,
            },
            {
                data: 'personal_mobile_no',
                orderable: false,
            },
            {
                data: 'username',
                orderable: false,
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
            var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData
                .action.action_link);
            $('td:last', nRow).html(actionHTML);
        }

    });
}

$(document).ready(function() {
    $('#emp_status').val(1);
    ajaxDataLoad();

    $('#searchFieldBtn').click(function() {
        ajaxDataLoad();
    });


});
// $(document).ready(function() {
// $("#branch_id").on('change', function(){
// var Branch = $('#branch_id').val();
//     $("#employee_name").change();
// });
// });
// Delete Data
function fnDelete(RowID) {
    /**
     * para 1 = link to delete without id
     * para 2 = ajax check link same for all
     * para 3 = id of deleting item
     * para 4 = matching column
     * para 5 = table 1
     * para 6 = table 2
     * para 7 = table 3
     */

     var modulecheck = '{{$moduleCheck}}';
     var childTableCheck = '';

     if(modulecheck == true){
        childTableCheck = "{{base64_encode('pos_sales_m')}}";
     }

    fnDeleteCheck(
        "{{url('gnl/employee/delete/')}}",
        "{{url('/ajaxDeleteCheck')}}",
        RowID,
        "{{base64_encode('employee_id')}}",
        "{{base64_encode('is_delete,0')}}",
        childTableCheck,
    );
}
</script>
@endsection
