@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;
?>
<!-- Page -->
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                    <thead>
                        <tr>
                            <th width="3%">SL</th>
                            <th>Name</th>
                            <th class="text-center">Code</th>
                            <th>Email</th>
                            <th width="8%" class="text-center">Company Logo</th>
                            <th width="8%" class="text-center">Bill Logo</th>
                            <th width="8%" class="text-center">Cover Image</th>
                            <th>Group</th>
                            <th>Comapny Type</th>
                            <th>Modules</th>
                            <th width="7%" class="text-center">Action</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
<!-- End Page -->
<script>
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
            "{{url('gnl/company/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID,
            "{{base64_encode('company_id')}}",
            "{{base64_encode('is_delete,0')}}",
            "{{base64_encode('gnl_projects')}}",
            "{{base64_encode('gnl_project_types')}}",
            "{{base64_encode('gnl_branchs')}}"
        );
    }

    $(document).ready(function () {
        ajaxDataLoad();
        var table = $('.clsDataTable').DataTable();
        var isSuperUser = "{{ Common::isSuperUser() }}";
        if(isSuperUser != 1){
            table.columns([6]).visible(false);
        }

    });

    function ajaxDataLoad(isSuperUser) {

        var table = $('.clsDataTable').DataTable({
            destroy: true,
            // retrieve: true,
            processing: true,
            serverSide: true,
            order: [
                [1, "DESC"]
            ],
            stateSave: true,
            stateDuration: 1800,
            ordering: false,
            // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
            "ajax": {
                "url": "{{route('comDatatable')}}",
                "dataType": "json",
                "type": "post",
            },
            columns: [
                {
                    data: 'id',
                    className: 'text-center'
                },
                {
                    data: 'comp_name',
                },
                {
                    data: 'comp_code',
                },
                {
                    data: 'comp_email',
                },
                {
                    data: 'comp_logo',
                    className: 'text-center'
                },
                {
                    data: 'bill_logo',
                    className: 'text-center'
                },
                {
                    data: 'cover_image_lp',
                    className: 'text-center'
                },
                {
                    data: 'group_name',
                },
                {
                    data: 'company_type',
                    className: 'text-center'
                },
                {
                    data: 'module_name',
                },
                {
                    data: 'action',
                    className: 'text-center'
                },

            ],
            'fnRowCallback': function (nRow, aData, Index) {
                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name,
                    aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
            }

        });
    }
</script>
@endsection
