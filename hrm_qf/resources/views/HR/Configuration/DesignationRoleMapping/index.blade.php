@extends('Layouts.erp_master')
@section('content')

@php
    use App\Services\CommonService as Common;
    use App\Services\RoleService as Role;

    $positions = DB::table('gnl_dynamic_form_value')->where([['form_id', 3],['type_id', 3], ['is_delete', 0], ['is_active', 1]])->get();
    $designations = DB::table('hr_designations')->where([['is_active', 1],['is_delete', 0]])->get();

    $editData = DB::table('hr_designation_role_mapping')->get();

    $role_ids = array();
    $ids[] = Common::getRoleId();
    $role_ids = array_merge($ids, Role::childRolesIds(Common::getRoleId()));

    $roles = DB::table("gnl_sys_user_roles")
        ->where([['is_delete', 0], ['is_active', 1]])
        ->whereIn('id', $role_ids)
        ->select('id', 'role_name', 'parent_id', 'order_by')
        ->get();

        // dd($positions,$designations,$editData,$roles);

@endphp

    <form id="designation_role_mapping_form" enctype="multipart/form-data" method="post" class="form-horizontal d-print-none" >
        @csrf

        <div id="mapper_main_div">

            @foreach ($positions as $key => $p)
            @php
                $selectedDesignationQry = $editData->where('position_id', $p->uid)->pluck('designation_ids')->toArray();
                $selDesig = array();
                if(count($selectedDesignationQry) > 0){
                    $selDesig = explode(',',$selectedDesignationQry[0]);
                }

                $selectedRoleQry = $editData->where('position_id', $p->uid)->pluck('role_id')->toArray();
                $selRole = array();
                if(count($selectedRoleQry) > 0){
                    $selRole = explode(',',$selectedRoleQry[0]);
                }
            @endphp
            <div class="row border">

                <div class="col-sm-2 form-group">
                    <label class="input-title">&nbsp;</label>
                    <div class="input-group">
                        <label class="input-title row-sl">{{ $key + 1 }}. &nbsp; &nbsp;{{ $p->name }}</label>
                    </div>
                    <input hidden value="{{ $p->uid }}" name="position_id[]">
                </div>

                <div class="col-sm-6">
                    
                    <label class="input-title">@if($key == 0) Designations @endif </label>
                    <div class="input-group">
                        <select  multiple name="designation_ids[{{ $p->uid }}][]" class="form-control clsSelect2 designations" style="width: 100%">
                            @foreach ($designations as $d)
                            <option {{ (in_array($d->id, $selDesig)) ? 'selected' : '' }} value="{{ $d->id }}">
                                {{ $d->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-4">
                    
                    <label class="input-title">@if($key == 0) Role @endif</label>
                    
                    <div class="input-group">
                        <select multiple name="role_id[{{ $p->uid }}][]" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Role</option>
                            @foreach ($roles as $r)

                            <option {{ (in_array($r->id, $selRole)) ? 'selected' : '' }} value="{{ $r->id }}">
                                {{ $r->role_name }}
                            </option>

                            @endforeach
                        </select>
                    </div>
                </div>
    
            </div>
            @endforeach

        </div>

        <div class="form-row align-items-center">

            <div class="col-sm-2"></div>

            <div class="col-sm-5">
                <div class="form-group d-flex justify-content-center">

                    @include('elements.button.common_button', [
                        'back' => true,
                        'submitBtn' => [
                            'id' => 'edit_updateBtn'
                        ],
                        'print' => [
                            'action' => 'print',
                            'title' => 'Print',
                            'exClass' => 'float-right',
                            'jsEvent' => 'onclick= window.print()'
                        ]
                    ])

                </div>
            </div>

        </div>

    </form>

    <div id="print_div" class="d-none d-print-block">

        @foreach ($positions as $key => $p)
        @php
            $selectedDesignationQry = $editData->where('position_id', $p->uid)->pluck('designation_ids')->toArray();
            $selDesig = array();
            if(count($selectedDesignationQry) > 0){
                $selDesig = explode(',',$selectedDesignationQry[0]);
            }

            $selectedRoleQry = $editData->where('position_id', $p->uid)->pluck('role_id')->toArray();
            $selRole = array();
            if(count($selectedRoleQry) > 0){
                $selRole = explode(',',$selectedRoleQry[0]);
            }
        @endphp

        <div class="row border">

            <div class="col-sm-1 form-group">
                <label class="input-title">&nbsp;</label>
                <div class="input-group">
                    <label class="input-title">{{ $key + 1 }}</label>
                </div>
            </div>

            <div class="col-sm-2 form-group">
                <label class="input-title">&nbsp;</label>
                <div class="input-group">
                    <label class="input-title">{{ $p->name }}</label>
                </div>
                <input hidden value="{{ $p->uid }}" name="position_id[]">
            </div>
            
            <div class="col-sm-6">
                <label class="input-title">&nbsp;</label>
                <div class="input-group">
                    @php $flag = 0; @endphp
                    @foreach ($designations as $d)
                        @if (count($editData) > 0)
                        @php
                            if (in_array($d->id, $selDesig)) {
                                echo '<label class="input-title">'. (($flag != 0) ? ', ' : '') . $d->name .'</label>';
                                $flag ++;
                            }
                        @endphp
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="col-sm-3">
                <label class="input-title">&nbsp;</label>
                <div class="input-group">
                    @foreach ($roles as $r)
                        @if (count($editData) > 0)
                            @php
                                if (in_array($r->id, $selRole)) {
                                    echo '<label class="input-title">'. (($flag != 0) ? ', ' : '') . $r->role_name .'</label>';
                                    $flag ++;
                                }
                            @endphp
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
        @endforeach

    </div>

    <script>

        $('#printBtn').click(function(){
            
            var divToPrint=document.getElementById('print_div');

            var newWin=window.open('','Print-Window');

            newWin.document.open();

            // newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            //newWin.document.write($('head').clone());

            newWin.document.close();

            //setTimeout(function(){newWin.close();},10);
        })

        $('#edit_updateBtn').click(function(event) {
            event.preventDefault();
            callApi("{{ url()->current() }}/update/api", 'post', new FormData($('#designation_role_mapping_form')[
                    0]),
                function(response, textStatus, xhr) {
                    showApiResponse(xhr.status, '');
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            )
        });

    </script>
@endsection
