@extends('Layouts.erp_master')
@section('content')


<!-- Page -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Name</th>
                        <th>Short Name</th>
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
});

$('#searchFieldBtn').click(function(){
    ajaxDataLoad();
});


function ajaxDataLoad( ){
    $('.clsDataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        stateDuration: 1800,
        "ajax":{
                    "url": "{{url()->current()}}",
                    "dataType": "json",
                    "type": "post",
                },
        columns: [
            { data: 'id', orderable: false, className: 'text-center'},
            { data: "dept_name" },
            { data: "short_name" },
            {data: 'action',orderable: false,className: 'text-center d-print-none',width: '15%'},
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
