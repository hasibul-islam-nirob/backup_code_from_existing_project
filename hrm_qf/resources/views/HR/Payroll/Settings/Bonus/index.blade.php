@extends('Layouts.erp_master')
@section('content')

    {{-- Datatable --}}
    <div class="row">
        <div class="col-lg-12">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width:5%;">SL</th>
                        <th>Name</th>
                        <th>Recruitment Types</th>
                        <th>Religion</th>
                        <th>Calculation Percentage</th>
                        <th>Based On</th>
                        <th>Bonus Payable min job month</th>
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
                        data: 'name',
                        orderable: false,
                    },
                    {
                        data: 'rec_type',
                        orderable: false,
                    },
                    {
                        data: 'religion',
                        orderable: false,
                    },
                    {
                        data: 'calculation_percentage',
                        orderable: false,
                    },
                    {
                        data: 'based_on',
                        orderable: false,
                    },
                    {
                        data: 'min_job_m',
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
