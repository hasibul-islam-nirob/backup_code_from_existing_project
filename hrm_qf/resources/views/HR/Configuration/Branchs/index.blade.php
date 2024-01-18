@extends('Layouts.erp_master')
@section('content')

<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped clsDataTable">
        <thead>
            <tr>
                <th style="width: 3%;">SL</th>
                <th>Bank</th>
                <th>Branch Name</th>
                <th>Branch Address</th>
                <th>Branch Email Address</th>
                <th>Branch Phone No.</th>
                <th>Contact Person</th>
                <th>Contact Person's Designation</th>
                <th>Contact Person's Phone</th>
                <th>Contact Person's Email</th>
                <th style="width: 10%;" class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>

$(document).ready(function() {
    $('.ajaxRequest').show();
    $('.httpRequest').hide(); //Hide new entry button
    ajaxDataLoad();
});

$('#searchFieldBtn').click(function(){
    ajaxDataLoad();
});


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
                { data: 'id', orderable: false, className: 'text-center'},
                { data: "bank_name"},
                { data: "name" },
                { data: "address" },
                { data: "email" },
                { data: "phone"},
                { data: "contact_person" },
                { data: "contact_person_designation" },
                { data: "contact_person_phone" },
                { data: "contact_person_email" },
                {data: 'action',orderable: false,className: 'text-center d-print-none',width: '10%'},
            ],
            'fnRowCallback': function(nRow, aData, Index) {
                var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action
                    .action_name, aData.action.action_link, aData.id);
                $('td:last', nRow).html(actionHTML);
                },

        });
    }


/*
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
                { data: "bank_name"},
                { data: "name" },
                { data: "address" },
                { data: "email" },
                { data: "phone"},
                { data: "contact_person" },
                { data: "contact_person_designation" },
                { data: "contact_person_phone" },
                { data: "contact_person_email" },
                { "data": "id", name: 'action', orderable: false, "width": "10%" },
            ],

            "columnDefs": [ {
            "targets": 10,
            "createdCell": function (td, cellData, rowData, row, col) {
                // console.log(rowData);
                        $(td).addClass("text-center d-print-none");
                        $(td).closest('tr').attr("cellData", cellData);
                        $(td).html('<a href=' +
                            "{{ url()->current() }}" + '/view/' + cellData +
                            ' title="View" class="btnView"><i class="icon wb-eye mr-2 blue-grey-600"></i></a>'+
                            '<a href=' +
                            "{{ url()->current() }}" + '/edit/' + cellData +
                            ' title="Edit" class="btnEdit"><i class="icon wb-edit mr-2 blue-grey-600"></i></a> <a href="javascript:void(0)" onclick="fnDelete(\'' +
                            cellData + '\'' +
                            ');" title="Delete" class=""><i class="icon wb-trash mr-2 blue-grey-600"></i></a>'
                            );
                    }
            } ]
        });
    }

$(document).ready( function () {

    ajaxDataLoad();

});


function fnDelete(rowID) {
    swal({
        title: "Are you sure to delete data?",
        text: "Once Delete, this will be permanently deleted!",
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
            data: {id : rowID},
        })
        .done(function(response) {

            var row = $('table tbody tr[cellData=' + '\'' + rowID + '\'' + ']');

            if (response['alert-type']=='error') {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: response['message'],
                });
            }
            else{
                swal({
                    icon: 'success',
                    title: 'Success...',
                    text: response['message'],
                });
                row.remove();
            }

        })
        .fail(function() {
            alert("error");
        });
    });



}

*/
</script>
@endsection