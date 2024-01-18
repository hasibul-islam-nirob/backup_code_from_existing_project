@extends('Layouts.erp_master')

@section('content')

<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped clsDataTable">
        <thead>
            <tr class="text-center">
                <th width="3%">SL</th>
                <th width="30%">Title</th>
                <th width="7%">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    function ajaxDataLoad(){
        $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            order: [[0, "ASC"]],
            "ajax":{
                     "url": "{{route('dTypeDatatable')}}",
                     "dataType": "json",
                     "type": "post",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { data: 'id', className: 'text-center'},
                { data: "name"},
                { data: 'action', orderable: false, className: 'text-center d-print-none' },
            ],
            'fnRowCallback': function(nRow, aData, Index) {
                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
                // $('td:nth-child(8)', nRow).html(actionHTML);
            }
        });
    }

    $(document).ready( function () {
        ajaxDataLoad();
    });

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
            "{{url('gnl/dynamic_type/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID,
            "{{base64_encode('type_id')}}",
            "{{base64_encode('is_delete,0')}}",
            "{{base64_encode('gnl_dynamic_form')}}"
        );
    }

 </script>

@endsection
