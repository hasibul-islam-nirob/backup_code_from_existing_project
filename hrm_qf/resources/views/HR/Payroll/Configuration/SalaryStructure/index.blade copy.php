@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Grade</th>
                        <th>Level</th>
                        <th>Basic Salary</th>
                        <th>Fiscal Year</th>
                        <th>Company</th>
                        <th>Designations</th>
                        <th>Project</th>
                        <th>Acting Benefit Amount</th>
                        <th>Is PF Applicible</th>
                        <th>Is Ps Applicible</th>
                        <th>WF Amount</th>
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
                        data: 'grade',
                        orderable: true,
                    },
                    {
                        data: 'level',
                        orderable: true,
                    },
                    {
                        data: 'basic',
                        orderable: true,
                    },
                    {
                        data: 'fiscal_year',
                        orderable: true,
                    },
                    {
                        data: 'company',
                        orderable: true,
                    },
                    {
                        data: 'designations',
                        orderable: true,
                    },
                    {
                        data: 'project',
                        orderable: true,
                    },
                    {
                        data: 'acting_benefit_amount',
                        orderable: true,
                    },
                    {
                        data: 'pf',
                        orderable: true,
                    },
                    {
                        data: 'ps',
                        orderable: true,
                    },
                    {
                        data: 'wf_amount',
                        orderable: true,
                    },
                    {
                        data: 'status',
                        orderable: true,
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
