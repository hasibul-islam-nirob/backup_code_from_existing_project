@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\RoleService as Role;
?>
<!-- Page -->
  <div class="row">
      <div class="col-lg-12">
          <table class="table w-full table-hover table-bordered table-striped clsDataTable">
              <thead>
                  <tr>
                      <th style="width:5%;">SL</th>
                      <th>Date</th>
                      <th>Title</th>
                      <th>Description</th>
                      <th style="width:15%;" class="text-center">Action</th>
                  </tr>
              </thead>

          </table>
      </div>
  </div>
<!-- End Page -->

<script>
    function fnDelete(RowID) {
        fnAjaxDeleteReloadTable("{{ url()->current() }}/delete/", RowID, "clsDataTable");
    }

    $(document).ready(function () {
        ajaxDataLoad();
    });

    function ajaxDataLoad() {

        $('.clsDataTable').DataTable({
            destroy: true,
            // retrieve: true,
            processing: true,
            serverSide: true,
            order: [
                [1, "DESC"]
            ],
            stateSave: true,
            stateDuration: 1800,
            ordering: false,
            // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            "ajax": {
                "url": "{{route('gnlHrgovHolDatatable')}}",
                "dataType": "json",
                "type": "post",
            },
            columns: [
                {
                    data: 'id',
                    className: 'text-center'
                },
                {
                    data: 'gh_date',
                },
                {
                    data: 'gh_title',
                },
                {
                    data: 'gh_description',
                },
                {
                    data: 'action',
                    className: 'text-center'
                },

            ],
            'fnRowCallback': function (nRow, aData, Index) {
                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name,
                    aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
            }

        });

    }
</script>

@endsection
