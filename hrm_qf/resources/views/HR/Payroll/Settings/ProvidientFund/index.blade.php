@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        {{-- <th>Group</th> --}}
                        <th>Company</th>
                        <th>Project</th>
                        <th>Recruitment Types</th>
                        {{-- <th>Calculation Type</th> --}}
                        <th>Calculation Amount</th>
                        {{-- <th>Employee Contribution After (month)</th> --}}
                        {{-- <th>Org. Contribution After (month)</th> --}}
                        {{-- <th>Loan Withdraw Min Month</th> --}}
                        {{-- <th>Loan Withdraw (Percentage)</th> --}}
                        {{-- <th>Loan Early Settlement Percentage</th> --}}
                        {{-- <th>Org. PF Withdraw Min Job year</th> --}}
                        {{-- <th>Emp. PF Withdraw Min Job year</th> --}}
                        <th>Interest Rate</th>
                        {{-- <th>Method</th> --}}
                        <th>Effective Date</th>
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
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'company',
                        orderable: false,
                    },
                    {
                        data: 'project',
                        orderable: false,
                    },
                    {
                        data: 'rec_type',
                        orderable: false,
                    },
                    {
                        data: 'calculation_amount',
                        orderable: false,
                    },
                    {
                        data: 'interest_rate',
                        orderable: false,
                    },
                    {
                        data: 'effective_date',
                        orderable: false,
                    },
                    {
                        data: 'status',
                        orderable: false,
                    },
                    
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none',
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
