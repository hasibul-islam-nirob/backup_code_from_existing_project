@extends('Layouts.erp_master')
@section('content')

@php
    // $menuName = DB::table("gnl_sys_menus")->where('id', $mid)->pluck('menu_name')->first();
    // $menuName = DB::table("gnl_sys_menus")->where('id', $mid)->pluck('menu_name')->first();
    $moduleList = DB::table('gnl_sys_modules')
            ->where('is_delete', 0)
            ->select('id', 'module_name', 'module_short_name')
            ->orderBy('module_name', 'ASC')
            ->get();

    $operationsForMenu = DB::table('gnl_dynamic_form_value')
                        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 2], ['form_id', 'GCONF.5']])
                        ->selectRaw('value_field, name')
                        ->pluck('name', 'value_field')
                        ->toArray();
@endphp

    <!-- Search Option Start -->
    <div class="row align-items-center d-flex justify-content-center pb-5">

        <div class="col-lg-2">
            <label class="input-title">Module Name</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="module_id" id="module_id"
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

        <div class="col-lg-2">
            <label class="input-title"> Menu</label>
            <div class="input-group">
                <select class="form-control clsSelect2" name="menu_id" id="menu_id">
                    <option value="">Select One</option>
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <label class="input-title">Action Type</label>
            <div class="input-group">
                @php
                $actionList = DB::table('gnl_dynamic_form_value')
                    ->where([['is_active', 1], ['is_delete', 0],
                        ['type_id', 2], ['form_id', "GCONF.5"]])
                    ->orderBy('order_by', 'ASC')
                    ->pluck('name', 'value_field')
                    ->toArray();
                @endphp
                <select class="form-control clsSelect2" name="permission_id" id="permission_id">
                    <option value="">Select One</option>
                    @foreach ($actionList as $setStatus => $actionName)
                        <option value="{{ $setStatus }}">{{ "[" . $setStatus. "] " . $actionName }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
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
    <br>

<div class="row">
    <div class="col-md-12">
        <table class="table w-full table-hover table-bordered table-striped clsDataTable">
            <thead>
                <tr>
                    <th width="2%">SL</th>
                    
                    <th>Action Name</th>
                    <th>Action Type</th>
                    <th>Route Link</th>

                    <th>Module Name</th>
                    <th style="width:10%;">Menu Name</th>

                    <th>Method Name</th>
                    <th>Order By</th>
                    <th style="width:10%;">Page Title</th>
                    <th style="width:10%;">Action</th>
                </tr>
            </thead>

        </table>
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
                "url": "{{ route('sysUserActionMenusDatatable') }}",
                "dataType": "json",
                "type": "post",
                "data": {
                    _token: "{{ csrf_token() }}",
                    module_id: $('#module_id').val(),
                    menu_id: $('#menu_id').val(),
                    permission_id: $('#permission_id').val(),
                    isActive: $('#isActive').val(),
                }
            },
            columns: [{
                    data: 'id',
                    className: 'text-center'
                },
                
                {
                    data: 'name',
                },
                {
                    data: 'action_type',
                },
                {
                    data: 'route_link',
                },
                {
                    data: 'module',
                },
                {
                    data: 'menu_name',
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
