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
                        <th style="width:3%;">SL</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Company</th>
                        <th style="width:20%;">Action</th>
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
                     "data": {
                    }
                   },
            "columns": [
                {data: 'sl', name: 'sl', orderable: false, targets: 1, className: 'text-center'},
                { "data": "name" },
                { "data": "comp_name" },
                { "data": "id", name: 'action', orderable: false, "width": "10%" },
            ],
            "columnDefs": [{
                "targets": 3,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).addClass("text-center d-print-none");
                    $(td).closest('tr').attr("cellData", cellData);
                    $(td).html('<a href=' + "{{ url()->current() }}" + '/view/' + cellData +
                        ' title="View" class="btnView"><i class="icon wb-eye mr-2 blue-grey-600"></i></a> <a href=' +
                        "{{ url()->current() }}" + '/edit/' + cellData +
                        ' title="Edit" class="btnEdit"><i class="icon wb-edit mr-2 blue-grey-600"></i></a> <a href="javascript:void(0)" onclick="fnDelete(\'' +
                        cellData + '\'' +
                        ');" title="Delete" class=""><i class="icon wb-trash mr-2 blue-grey-600"></i></a>'
                        );
                }
            }]

        });
    }

    $(document).ready( function () {
        ajaxDataLoad();
    });
    function fnDelete(rowID) {

        swal({
                title: "Are you sure to delete data?",
                text: "Once Delete, this will be permanently delete!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((isConfirm) => {
                if (!isConfirm) {
                    return false;
                }
                var row = $('table tbody tr[cellData=' + '\'' + rowID + '\'' + ']');
                $.ajax({
                        url: "{{ url()->current() }}" + '/delete',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: rowID
                        },
                    })
                    .done(function (response) {

                        var row = $('table tbody tr[cellData=' + '\'' + rowID + '\'' + ']');
                        if (response['alert-type'] == 'error') {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                text: response['message'],
                            });
                        } else {
                            swal({
                                icon: 'success',
                                title: 'Success...',
                                text: response['message'],
                            });
                            row.remove();
                            $('.clsDataTable').DataTable().draw();
                        }

                    })
                    .fail(function () {
                        alert("error");
                    });
            });
    }
</script>
@endsection