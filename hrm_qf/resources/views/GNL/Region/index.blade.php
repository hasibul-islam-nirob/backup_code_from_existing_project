@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\RoleService as Role;
use App\Services\CommonService as Common;
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                    <thead>
                        <tr>
                            <th style="width:3%;">SL</th>
                            <th style="width:20%;">Name</th>
                            <th style="width:7%;" class="text-center">Code</th>
                            <th style="width:60%; word-wrap: break-word;">Area</th>
                            <th style="width:10%;" class="text-center">Action</th>
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
            "{{url('gnl/region/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID,
            "{{base64_encode('region_id')}}",
            "{{base64_encode('is_delete,0')}}",
            // "{{base64_encode('gnl_companies')}}"
        );
    }

    $(document).ready(function () {
        ajaxDataLoad();
    });

    function ajaxDataLoad() {

        $('.clsDataTable').DataTable({
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
                "url": "{{route('regionDatatable')}}",
                "dataType": "json",
                "type": "post",
            },
            columns: [
                {
                    data: 'id',
                    className: 'text-center'
                },
                {
                    data: 'region_name',
                },
                {
                    data: 'region_code',
                    className: 'text-center'
                },
                {
                    data: 'area_name',
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
