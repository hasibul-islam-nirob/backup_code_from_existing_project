@extends('Layouts.erp_master_full_width')
@section('content')

    <div class="w-full show">
        <div class="panel">
            <div class="panel-body panel-search pt-2">

                <!-- Search Option Start -->
                @include('elements.common_filter_options', [
                'branch' => true,
                'withHeadOffice' => true,
                'zone' => true,
                'area' => true,
                'employee' => true,
                'employeeFieldLabel' => "Employee",
                'isActive' => true,
                'userRole' => true
                ])
                <!-- Search Option End -->

                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                            <thead>
                                <tr class="text-center">
                                    <th width="3%">SL</th>
                                    <th width="12%">Username</th>
                                    <th width="15%">User Role</th>
                                    <th width="15%">Employee(Code)</th>
                                    <th width="15%">Branch(Code)</th>
                                    <th width="15%">Infomation</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function ajaxDataLoad(zone_id = null, region_id = null, area_id = null, branch_id = null,
            employee_id = null, userStatus = null, user_role_id = null) {

            $('.clsDataTable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                order: [
                    [1, "DESC"]
                ],
                stateSave: true,
                stateDuration: 300,
                // ordering: false,
                // lengthMenu: [[10, 20, 30, 50], [10, 20, 30, 50]],
                "ajax": {
                    "url": "{{ url()->current() }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        _token: "{{ csrf_token() }}",
                        zoneId: zone_id,
                        regionId: region_id,
                        areaId: area_id,
                        branchId: branch_id,
                        employeeId: employee_id,
                        userStatus: userStatus,
                        user_role_id: user_role_id
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'user_role',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'employee_info',
                        orderable: false,
                    },
                    {
                        data: 'branch_info',
                        orderable: false
                    },
                    {
                        data: 'other_info',
                        orderable: false
                    },
                    {
                        data: 'user_status',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'text-center'
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

            $('#searchFieldBtn').click(function() {

                var zone_id = $('#zone_id').val();
                var region_id = $('#region_id').val();
                var area_id = $('#area_id').val();
                var branch_id = $('#branch_id').val();

                var employee_id = $('#employee_id').val();
                var userStatus = $('#userStatus').val();
                var user_role_id = $('#user_role_id').val();

                ajaxDataLoad(zone_id, region_id, area_id, branch_id,
                    employee_id, userStatus, user_role_id);

            });
        });

        // Delete Data
        function fnDelete(RowID) {

            // console.log('test');
            // return false;
            /**
             * para 1 = link to delete without id
             * para 2 = ajax check link same for all
             * para 3 = id of deleting item
             * para 4 = matching column
             * para 5 = condition2
             * para 6 = table 1
             * para 7 = table 2
             * para 8 = table 3
             */
            /*Common::ViewTableOrder('pos_p_subcategories', [['is_delete', 0], ['is_active', 1]], ['id', 'sub_cat_name'], ['sub_cat_name', 'ASC']);*/


            fnDeleteCheck(
                "{{ url()->current() }}/delete",
                "{{ url('/ajaxDeleteCheck') }}",
                RowID
            );
        }
    </script>

@endsection
