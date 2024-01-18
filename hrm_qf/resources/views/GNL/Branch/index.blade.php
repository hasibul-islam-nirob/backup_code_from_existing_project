@extends('Layouts.erp_master')
@section('content')
    @php
        use App\Services\CommonService as Common;

        ## Convension mismatch thats why variable name change
        $elementArray = [];

        $elementArray['zone'] = ['label' => 'Zone', 'type' => 'select', 'id' => 'zoneId', 'name' => 'zoneId', 'default_option' => 'All'];
        $elementArray['region'] = ['label' => 'Region', 'type' => 'select', 'id' => 'regionId', 'name' => 'regionId', 'default_option' => 'All'];
        $elementArray['area'] = ['label' => 'Area', 'type' => 'select', 'id' => 'areaId', 'name' => 'areaId', 'default_option' => 'All'];

        $elementArray['independent_box'] = [
            'label' => 'Independent Branch',
            'type' => 'select',
            'id' => 'isIndependent',
            'name' => 'isIndependent',
            'selected_value' => 'All',
            'options' => ['0' => 'All', '1' => 'Yes', '2' => 'No'],
        ];

        $elementArray['approve_box'] = [
            'label' => 'Status',
            'type' => 'select',
            'id' => 'isApproved',
            'name' => 'isApproved',
            'selected_value' => 'All',
            'options' => ['0' => 'All', '1' => 'Approved', '2' => 'Pending'],
        ];

        $elementArray['active_box'] = [
            'label' => 'Active Branch',
            'type' => 'select',
            'id' => 'isActive',
            'name' => 'isActive',
            'selected_value' => 'All',
            'options' => ['0' => 'All', '1' => 'Active', '2' => 'In-Active'],
        ];

    @endphp

    @include('elements.report.common_filter.filter_options', ['elements' => $elementArray])

    <!-- Page -->
    <div class="row">
        <div class="col-lg-12 table-responsive">
            <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                <thead>
                    <tr>
                        <th style="width: 3%;">SL</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Area</th>
                        <th>Region</th>
                        <th>Zone</th>
                        <th>Contact Info</th>
                        <th>Opening Date</th>
                        <th>Company</th>
                        <th>Approve</th>
                        <th width = "8%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- End Page -->

    <script>
        function ajaxDataLoad() {
            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                order: [
                    [2, "ASC"]
                ],
                // stateSave: true,
                // stateDuration: 1800,
                // ordering: false,
                // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
                "ajax": {
                    "url": "{{ route('branchDatatable') }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        _token: "{{ csrf_token() }}",
                        zoneId: $('#zoneId').val(),
                        regionId: $('#regionId').val(),
                        areaId: $('#areaId').val(),
                        isIndependent: $('#isIndependent').val(),
                        isApproved: $('#isApproved').val(),
                        isActive: $('#isActive').val()
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'branch_code',
                        name: 'branch_code',
                        className: 'text-center'
                    },
                    {
                        data: 'area_name',
                        name: 'area_name',
                        orderable: false
                    },
                    {
                        data: 'region_name',
                        name: 'region_name',
                        orderable: false
                    },
                    {
                        data: 'zone_name',
                        name: 'zone_name',
                        orderable: false
                    },
                    {
                        data: 'Contact Info',
                        name: 'Contact Info',
                        orderable: false
                    },
                    {
                        data: 'opening Date',
                        name: 'opening Date',
                        orderable: false
                    },
                    {
                        data: 'comp_name',
                        name: 'comp_name',
                        orderable: false,
                    },
                    {
                        data: 'approved',
                        name: 'approved',
                        className: 'text-center',
                        orderable: false,
                    },
                    {
                        data: 'action',
                        orderable: false,
                        className: 'text-center d-print-none'
                    },
                ],
                'fnRowCallback': function(nRow, aData, Index) {
                    var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name,
                        aData.action.action_link);
                    $('td:last', nRow).html(actionHTML);
                }
            });
        }

        $(document).ready(function() {
            ajaxDataLoad();

            $('#searchButton').click(function() {
                ajaxDataLoad();
            });
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
                "{{ url('gnl/branch/delete/') }}",
                "{{ url('/ajaxDeleteCheck') }}",
                RowID,
                "{{ base64_encode('branch_id') }}",
                "",
                ""
            );
        }
    </script>
    <!-- End Page -->
@endsection
