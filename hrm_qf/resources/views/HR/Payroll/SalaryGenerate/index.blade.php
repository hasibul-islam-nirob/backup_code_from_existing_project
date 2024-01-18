@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:3%;">SL</th>
                        <th>Fiscal Year</th>
                        <th>Salary Month</th>
                        <th>Branch</th>
                        <th>Approved By</th>
                        <th>Approved Date</th>
                        <th>Payment Date</th>
                        {{-- <th>Voucher Generate</th> --}}
                        <th>Create By</th>
                        <th>Create At</th>
                        <th>Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}

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

        function ajaxDataLoad() {

            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                stateDuration: 1800,
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                },
                columns: [
                    {data: 'id',className: 'text-center',orderable: false,width: '5%'},
                    {data: 'pay_scale',orderable: false, className: 'text-center'},
                    {data: 'month_name',orderable: false, className: 'text-center'},
                    {data: 'branch', orderable: false},
                    {data: 'approved_by',orderable: false, className: 'text-center'},
                    {data: 'approved_date',orderable: false,className: 'text-center'},
                    {data: 'payment_date',orderable: false,className: 'text-center'},
                    {data: 'create_by', orderable: false},
                    {data: 'create_at', orderable: false, className: 'text-center'},
                    {data: 'status',orderable: false},
                    {data: 'action',orderable: false,className: 'text-center d-print-none',width: '10%'},
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
