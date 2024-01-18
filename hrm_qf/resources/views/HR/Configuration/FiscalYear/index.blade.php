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
                        <th>Name</th>
                        <th>Fiscal For</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Company</th>
                        <th class="text-center" style="width:15%;">Action</th>
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
                "data": {
                    // "fy_name": $('#fy_name').val(),
                    // "fy_for": $('.fType').val() == 'FFY' ? 'Financial Fiscale Year' : 'HR Fiscale Year',
                    // "fy_start_date": $('#fy_start_date').val(),
                }
            },
            order: [[2, "ASC"]],
            columns: [{
                    data: 'id',
                    className: 'text-center',
                    orderable: false,
                    width: '5%'
                },
                {
                    data: 'fy_name',
                    orderable: true,
                },
                {
                    data: 'fy_for',
                    orderable: true,
                },
                {
                    data: 'fy_start_date',
                    orderable: true,
                    className: 'text-center',
                },
                {
                    data: 'fy_end_date',
                    orderable: true,
                    className: 'text-center',
                },
                {
                    data: 'company_id',
                    orderable: true,
                    className: 'text-center',
                },
                {
                    data: 'action',
                    orderable: false,
                    className: 'text-center d-print-none'
                },
            ],
            'fnRowCallback': function(nRow, aData, Index) {

                var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
                $('td:last', nRow).html(actionHTML);
            },
        });
    }

</script>

@endsection
