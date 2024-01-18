@extends('Layouts.erp_master')
@section('content')

@php
    $menuName = DB::table("gnl_sys_menus")->where('id', $mid)->pluck('menu_name')->first();

    $operationsForMenu = DB::table('gnl_dynamic_form_value')
                        ->where([['is_delete', 0], ['is_active', 1], ['type_id', 2], ['form_id', 'GCONF.5']])
                        ->selectRaw('value_field, name')
                        ->pluck('name', 'value_field')
                        ->toArray();
@endphp

<div class="row">
    <div class="col-lg-12 text-right">
        <a class="btn btn-sm btn-primary btn-outline btn-round" href="{{ url('gnl/sys_permission/'.$mid.'/add') }}">
            <i class="icon wb-link" aria-hidden="true"></i>
            <span class="hidden-sm-down">New Entry</span>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <p class="text-dark">
            <b>Parent Menu:</b> {{ $menuName }}
        </p>
    </div>

    <div class="col-md-12">
        <table class="table w-full table-hover table-bordered table-striped clsDataTable">
            <thead>
                <tr>
                    <th width="2%">SL</th>
                    <th>Action Name</th>
                    <th>Action Code</th>
                    <th>Route Link</th>
                    <th>Method Name</th>
                    <th>Page Title</th>

                    <th>Order By</th>
                    <th style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 0; @endphp
                @foreach($menuActions as $row)
                <tr>
                    <td class="text-center">{{ ++$i }}</td>
                    <td>{{ $row->name }}</td>

                    <td class="text-center">
                        @if(isset($operationsForMenu[$row->set_status]))
                            {{ $operationsForMenu[$row->set_status] }} [{{ $row->set_status }}]
                        @else
                            {{ $row->set_status }}
                        @endif
                    </td>

                    <td>{{ $row->route_link }}</td>
                    <td>{{ $row->method_name }}</td>
                    <td>{{ $row->page_title }}</td>


                    <td class="text-center">{{ $row->order_by }}</td>

                    <td class="text-center">
                        <a href="{{ url('gnl/sys_permission/'.$mid.'/edit/'.$row->id) }}" title="Edit">
                            <i class="icon wb-edit mr-2 blue-grey-600"></i>
                        </a>

                        @if($row->is_active == 1)
                        <a href="{{ url('gnl/sys_permission/'.$mid.'/publish/'.$row->id) }}" title="Unpublish"
                            class="btnUnpublish">
                            <i class="icon fa-check-square-o mr-2 blue-grey-600"></i>
                        </a>
                        @else
                        <a href="{{ url('gnl/sys_permission/'.$mid.'/publish/'.$row->id) }}" title="Publish"
                            class="btnPublish">
                            <i class="icon fa-square-o mr-2 blue-grey-600"></i>
                        </a>
                        @endif

                        <a href="{{ url('gnl/sys_permission/'.$mid.'/delete/'.$row->id) }}" title="Delete"
                            class="btnDelete">
                            <i class="icon wb-trash mr-2 blue-grey-600"></i>
                        </a>

                        <a href="{{ url('gnl/sys_permission/'.$mid.'/destroy/'.$row->id)}}"
                            title="Parmanent Delete" class="btnDelete">
                            <i class="icon wb-scissor mr-2 blue-grey-600"></i>
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
