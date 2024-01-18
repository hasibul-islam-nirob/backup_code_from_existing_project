@extends('Layouts.erp_master')
@section('content')
    @php
        $moduleList = DB::table('gnl_sys_modules')
            ->where('is_delete', 0)
            ->select('id', 'module_name', 'module_short_name')
            ->orderBy('module_name', 'ASC')
            ->get();
    @endphp

    <!-- Search Option Start -->
    <div class="row align-items-center d-flex justify-content-center pb-5">

        <div class="col-lg-3">
            <label class="input-title">Module Name</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="moduleId" id="moduleId"
                    onchange="fnAjaxSelectBox('menu_id', this.value,
                '{{ base64_encode('gnl_sys_menus') }}',
                '{{ base64_encode('module_id') }}',
                '{{ base64_encode('id,menu_name,route_link') }}',
                '{{ url('/ajaxSelectBox') }}');">
                    <option value="">Select All</option>
                    @foreach ($moduleList as $row)
                        <option value="{{ $row->id }}">{{ $row->module_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-4">
            <label class="input-title">Parent Menu</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="menu_id" id="menu_id">
                    <option value="">Select One</option>
                </select>
            </div>
        </div>

        <div class="col-lg-3">
            <label class="input-title">Active Data</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="isActive" id="isActive">
                    <option value="1">Active</option>
                    <option value="0">All</option>
                    <option value="2">In-Active</option>
                </select>
            </div>
        </div>

        @include('elements.button.common_button', [
            'search' => [
                'action' => true,
                'title' => 'search',
                'id' => 'searchFieldBtn',
                'name' => 'searchFieldBtn',
                'exClass' => 'float-right',
            ],
        ])

    </div>
    <!-- Search Option End -->

    <div class="row">
        <div class="col-lg-12">
            <!-- data-plugin="dataTable" -->
            <div class="table-responsive">
                <table class="table w-full table-hover table-bordered table-striped clsDataTable">
                    <thead>
                        <tr>
                            <th width="5%">SL</th>
                            <th width="20%">Menu Name</th>
                            <th width="15%">Parent Menu</th>
                            <th width="15%">Route Link</th>
                            <!--<th width="15%">Controller</th>
                            <th width="8%">Method</th>-->
                            <th width="5%">Icon</th>
                            <th width="10%">Order By</th>
                            <th width="15%">Module</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>

    <script>
        function fnDelete(RowID) {
            fnAjaxDeleteReloadTable("{{ url()->current() }}/delete", RowID, "clsDataTable");
        }

        $(document).ready(function() {
            ajaxDataLoad();
            $('#searchFieldBtn').click(function() {
                ajaxDataLoad();
            });
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
                    "url": "{{ route('sysUserMenusDatatable') }}",
                    "dataType": "json",
                    "type": "post",
                    "data": {
                        _token: "{{ csrf_token() }}",
                        module_id: $('#module_id').val(),
                        menu_id: $('#menu_id').val(),
                        isActive: $('#isActive').val(),
                    }
                },
                columns: [{
                        data: 'id',
                        className: 'text-center'
                    },
                    {
                        data: 'menu_name',
                    },
                    {
                        data: 'parent_menu',
                    },
                    {
                        data: 'route_link',
                    },
                    {
                        data: 'menu_icon',
                    },
                    {
                        data: 'order_by',
                    },
                    {
                        data: 'module_name',
                    },
                    {
                        data: 'action',
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
    </script>
@endsection
