@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Vehicle Type</th>
                        <th>Maximum Installment</th>
                        <th>Maximum Amount</th>
                        <th>Settlement Fee (%)</th>

                        <th>Intrest Rate (%)</th>
                        <th>Interest Method</th>

                        <th>Status</th>
                        <th style="width:15%;" class="text-center">Action</th>
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
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'vehicle_type',
                        orderable: false,
                    },
                    {
                        data: 'max_installment',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'max_amount',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'settlement_fee',
                        className: 'text-center',
                        orderable: false,
                    },


                    {
                        data: 'intrest_rate',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'intrest_method',
                        orderable: false,
                    },


                    {
                        data: 'status',
                        className: 'text-center',
                        orderable: false,
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
