
<?php 
use App\Services\RoleService as Role;
?>


@extends('Layouts.erp_master')
@section('content')

<div class="row smallDivWidth">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th width="3%">SL</th>
                        <th width="15%">Payment System</th>
                        <th width="15%">Short Name</th>
                        <th width="15%">For</th>
                        <th width="15%">Order</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- End Page -->

<script>
function ajaxDataLoad() {
    // console.log(supplierID)

    $('.clsDataTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        stateSave: true,
        stateDuration: 1800,
        // order: [[3, "DESC"]],
        // ordering: false,
        // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
        "ajax": {
            "url": "{{ url()->current() }}",
            "dataType": "json",
            "type": "post",
            "data": {
                _token: "{{csrf_token()}}",
            }
        },
        columns: [{
                data: 'id',
                className: 'text-center'
            },
            {
                data: 'payment_system_name',
            },
            {
                data: 'short_name',
            },
            {
                data: 'status',
            },
            {
                data: 'order',
                className: 'text-center'
            },
            {
                data: 'action',
                orderable: false,
                className: 'text-center'
            },

        ],
        'fnRowCallback': function(nRow, aData, Index) {
            var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData.action.action_link);
            $('td:last', nRow).html(actionHTML);
        }
        // drawCallback: function (oResult) {
        //     $('#TQuantity').html(oResult.json.totalQuantity);
        //     $('#TUnitPrice').html(oResult.json.totalUnitPrice);
        //     $('#TAmount').html(oResult.json.totalAmount);
        // },
    });
}

$(document).ready(function() {

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
        "{{url('gnl/payment_sys/delete/')}}",
        "{{url('/ajaxDeleteCheck')}}",
        RowID

    );
}




</script>

@endsection


