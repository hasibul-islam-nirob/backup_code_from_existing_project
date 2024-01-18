@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>
<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped clsDataTable">
        <thead>
            <tr>
                <th style="width: 3%;">SL</th>
                <th>Employee Name</th>
                <th>Generated At</th>
                <th style="width: 10%;" class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>
<script>
function ajaxDataLoad( ){
        $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            "ajax":{
                     "url": "{{url()->current()}}",
                     "dataType": "json",
                     "type": "post",
                   },
            "columns": [
                { data: 'sl', name: 'sl', orderable: false, targets: 1, className: 'text-center'},
                { data: "name" },
                { data: "printed_at" ,className: 'text-center'},
                { "data": "id", name: 'action', orderable: false, "width": "10%" },
            ],
            "columnDefs": [ {
            "targets": 3,
            "createdCell": function (td, cellData, rowData, row, col) {
                // console.log(rowData);
                        $(td).addClass("text-center d-print-none");
                        $(td).closest('tr').attr("cellData", cellData);
                        $(td).html('<a href=' +
                            "{{ url()->current() }}" + '/view/' + cellData +
                            ' title="View" class="btnView"><i class="icon wb-eye mr-2 blue-grey-600"></i></a>'
                            );
                    }
            } ]
        });
    }

$(document).ready( function () {

    ajaxDataLoad();

});
</script>
@endsection
