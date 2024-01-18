@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\RoleService as Role;
?>
<!-- Page -->
  <div class="row">
      <div class="col-sm-12">
          {{-- <table class="table w-full table-hover table-bordered table-striped dataTable" data-plugin="dataTable"> --}}
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
              <thead>
                <tr>
                    <th style="width:5%;">SL</th>
                    <th>Title</th>
                    <th>Day</th>
                    <th>Effective Day</th>
                    <th>Company</th>
                    <th>Branch</th>
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
        order: [[2, "ASC"]],
        columns: [
            { data: 'id', orderable: false, className: 'text-center'},
            {data: 'ch_title'},
            {data: 'ch_day'},
            {data: 'ch_eff_date', className: 'text-center'},
            {data: 'comp_name'},
            {data: 'application_for'},
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
