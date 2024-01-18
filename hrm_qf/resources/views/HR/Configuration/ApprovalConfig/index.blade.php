@extends('Layouts.erp_master')
@section('content')

<style>
.select2-container {
    z-index: 100000;
}
</style>
<!-- Page -->
<div class="row">
    <div class="col-sm-12 mx-auto">
        <h5 class="text-dark text-center" style="font-size: 14px;">{{$moduleName}} Module / {{$eventName}} Application</h5>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped clsDataTable">
            <thead>
                <tr>
                    <th style="width:10%;">SL</th>
                    <th style="width:30%;">Designation</th>
                    <th style="width:30%;">Department</th>
                    <th style="width:20%;">Last Update</th>
                    <th style="width:15%;" class="text-center">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- End Page -->
<script>

$(document).ready(function() {
    $('.ajaxRequest').show();
    $('.httpRequest').hide(); //Hide new entry button
    ajaxDataLoad();

    let old_link = $('.addAction').data('link');
    $('.addAction').data('link', old_link + '/{{ last(request()->segments()) }}');

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
        },
        columns: [{
                data: 'sl',
                className: 'text-center',
                orderable: false,
                width: '5%'
            },
            {
                data: 'designation_for',
                orderable: false,
            },
            {
                data: 'department_for',
                orderable: false,
            },
            {
                data: 'created_at',
                orderable: false,
                class: 'text-center',
            },
            {
                data: 'action',
                orderable: false,
                className: 'text-center d-print-none'
            },
        ],
        'fnRowCallback': function(nRow, aData, Index) {

            var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
            $('td:last', nRow).html(actionHTML);
        },
    });
}
</script>
@endsection