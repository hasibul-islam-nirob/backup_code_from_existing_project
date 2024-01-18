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
                        <th>Amount</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th style="width:10%;" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- Datatable --}}


<script>
    $(document).ready(function() {
        $('.httpRequest').hide(); //Hide new entry button
        $('.ajaxRequest').show();
        
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
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'level',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'amount',
                    className: 'text-center',
                    orderable: false,
                },
                {
                    data: 'effective_date',
                    className: 'text-center',
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