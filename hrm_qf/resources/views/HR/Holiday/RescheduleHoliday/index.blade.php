@extends('Layouts.erp_master')
@section('content')
<!-- Page -->
  <div class="row">
      <div class="col-lg-12">
          <table class="table w-full table-hover table-bordered table-striped clsDataTable">
              <thead>
                  <tr>
                      <th style="width:5%;">SL</th>
                      <th>Title</th>
                      <th>Applicable For</th>
                      <th>Off-Day Date</th>
                      <th>Working-Day Date</th>
                      <th>Company</th>
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

  function ajaxDataLoad(){
      $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            stateDuration: 1800,
            // ordering: false,
            // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            "ajax":{
                    "url": "{{url()->current()}}",
                    "dataType": "json",
                    "type": "post",
                    },
            order: [[2, "ASC"]],
            columns: [
                { data: 'id', orderable: false, className: 'text-center'},
                { data: 'title', name: 'title', orderable: false },
                { data: 'app_for', name: 'app_for', orderable: false},
                { data: 'working_date', name: 'working_date', className: 'text-center', orderable: false },
                { data: 'reschedule_date', name: 'reschedule_date', className: 'text-center', orderable: false },
                { data: 'comp_name', name: 'comp_name', orderable: false },
                { data: 'action', name: 'action', orderable: false,className: 'text-center'},
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
