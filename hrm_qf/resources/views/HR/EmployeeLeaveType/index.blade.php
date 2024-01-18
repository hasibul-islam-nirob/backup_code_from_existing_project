@extends('Layouts.erp_master')
@section('content')
    <!-- Page -->
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                <tr>
                    <th style="width:5%;">SL</th>
                    <th>Leave Name</th>
                    <th>Short Name</th>
                    <th>Leave Type</th>
                    <th style="width:15%;" class="text-center">Action</th>
                </tr>
                </thead>
            </table>
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
                // ordering: false,
                // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                },
                columns: [
                    {
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'leave_name',
                        orderable: false,
                    },
                    {
                        data: 'short_name',
                        orderable: false,
                    },
                    {
                        data: 'leave_type',
                        orderable: false,
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
            ajaxDataLoad();
        });

        function fnDelete(RowID) {
            fnAjaxDeleteReloadTable("{{ url()->current() }}/delete", RowID, "clsDataTable");
        }

    </script>
@endsection
