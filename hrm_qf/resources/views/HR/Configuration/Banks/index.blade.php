@extends('Layouts.erp_master')
@section('content')

<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped clsDataTable">
        <thead>
            <tr>
                <th style="width: 3%;">SL</th>
                <th>Bank Name</th>
                <th>Bank Address</th>
                <th>Bank Email Address</th>
                <th>Bank Phone No.</th>
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
            // stateDuration: 1800,
            "ajax":{
                    "url": "{{url()->current()}}",
                    "dataType": "json",
                    "type": "post",
            },
            order: [[2, "ASC"]],
            columns: [
                { data: 'id', orderable: false, className: 'text-center'},
                { data: "name" },
                { data: "address" },
                { data: "email" },
                { data: "phone"},
                { data: "contact_person" },
                { data: "contact_person_designation" },
                { data: "contact_person_phone" },
                { data: "contact_person_email" },
                {data: 'action',orderable: false,className: 'text-center d-print-none',width: '15%'},
            ],
            'fnRowCallback': function(nRow, aData, Index) {
                var actionHTML = jsRoleWisePermissionForPopUp(aData.action.set_status, aData.action.action_name, aData.action.action_link, aData.id);
                $('td:last', nRow).html(actionHTML);
            },

        });
    }

</script>
@endsection
