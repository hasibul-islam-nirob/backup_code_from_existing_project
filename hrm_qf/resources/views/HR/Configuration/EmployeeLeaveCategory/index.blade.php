@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th style="width:25%;">Category Name</th>
                        <th style="width:20%;">Short Name</th>
                        <th style="width:20%;">Leave Type</th>
                        <th style="width:20%;">Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}

    <!-- End Page -->
    <script>

        $(document).on('focus',".lv-datepicker", function(){
            $(this).datepicker({ dateFormat: 'dd-mm-yy' });
        });

        $(document).ready(function() {
            $('.ajaxRequest').show();
            $('.httpRequest').hide(); //Hide new entry button
            ajaxDataLoad();
        });

        $('#searchFieldBtn').click(function() {
            ajaxDataLoad();
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
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'name',
                        orderable: true,
                        width: '25%'
                    },
                    {
                        data: 'short_form',
                        orderable: false,
                        width: '20%'
                    },
                    {
                        data: 'leave_type',
                        className: 'text-center',
                        orderable: true,
                        width: '20%'
                    },
                    {
                        data: 'status',
                        orderable: true,
                        className: 'text-center',
                        width: '20%'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none',
                        width: '10%'
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
