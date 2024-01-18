@extends('Layouts.erp_master')
@section('content')
<!-- Page -->

<?php 
    use App\Services\RoleService as Role;
?>

<div class="row smallDivWidth">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th width="3%">SL</th>
                        <th width="5%">Payment Sys.</th>
                        <th width="15%">For</th>
                        <th width="15%">Provider Name</th>
                        <th width="15%">Holder Name</th>
                        <th width="10%">Account No.</th>
                        <th width="15%">Ledger</th>

                        <th width="5%">Mobile</th>
                        <th width="3%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- End Page -->

<script>
    function ajaxDataLoad( ){
        $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            "ajax":{
                "url": "{{ url()->current() }}",
                "dataType": "json",
                "type": "post",
                "data": {}
            },
            "columns": [
                {data: 'sl', name: 'sl', orderable: false, targets: 1, className: 'text-center'},
                { "data": "payment_system_id" },
                { "data": "status" },
                { "data": "provider_name" },
                { "data": "acc_holder_name" },
                { "data": "account_no" },
                { "data": "ledger" },

                { "data": "mobile" },
                { "data": "action", name: 'action', orderable: false, "width": "20%" },
            ],
            'fnRowCallback': function(nRow, aData, Index) {

                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
            }
        });
    }

    $(document).ready( function () {
        ajaxDataLoad();
    });

    // Delete Data
    function fnDelete(RowID) {
        /**
         * para1 = link to delete without id
         * para 2 = ajax check link same for all
         * para 3 = id of deleting item
         * para 4 = matching column
         * para 5 = table 1
         * para 6 = table 2
         * para 7 = table 3
         */

        fnDeleteCheck(
            "{{url('gnl/payment_acc/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID

        );
    }
    
</script>
@endsection