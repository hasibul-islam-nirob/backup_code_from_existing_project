@extends('Layouts.erp_master')
@section('content')

@php
    $typeList = DB::table('gnl_dynamic_form_type')->where('is_delete', 0)->select('id', 'name')->orderBy('name', 'ASC')->get();
    $formList = DB::table('gnl_dynamic_form')->where('is_delete', 0)->select('uid', 'name')->orderBy('name', 'ASC')->get();
@endphp

<!-- Search Option Start -->
<div class="row align-items-center d-flex justify-content-center pb-5">

    <div class="col-lg-3">
        <label class="input-title">Dynamic Form Type</label>
        <div class="input-group">
            <select class="form-control clsSelect2" name="typeId" id="typeId" onchange="fnAjaxSelectBox('formId', this.value,
                '{{base64_encode('gnl_dynamic_form')}}', '{{base64_encode('type_id')}}',
                '{{base64_encode('uid,name')}}','{{url('/ajaxSelectBox')}}' );"
            >
                <option value="">Select All</option>
                @foreach ($typeList as $row)
                <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-lg-3">
        <label class="input-title">Dynamic Form</label>
        <div class="input-group">
            <select class="form-control clsSelect2" name="formUid" id="formUid" >
                <option value="">Select All</option>
                @foreach ($formList as $row)
                <option value="{{ $row->uid }}">{{ $row->name }} [{{ $row->uid }}]</option>
                @endforeach
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
            'exClass' => 'float-right'
        ]
    ])

</div>
<!-- Search Option End -->

<div class="table-responsive">
    <table class="table w-full table-hover table-bordered table-striped clsDataTable">
        <thead>
            <tr class="text-center">
                <th width="3%">SL</th>
                <th width="5%">Uid</th>
                <th width="20%">Title</th>
                <th width="15%">Static Value</th>
                <th width="10%">Order</th>
                <th width="23%">Form Name</th>
                <th width="12%">Form Type</th>
                <th width="5%">Data</th>
                <th width="7%">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    $(document).ready(function () {
        ajaxDataLoad();
        $('#searchFieldBtn').click(function(){
            ajaxDataLoad();
        });
    });

    function ajaxDataLoad(){
        $('.clsDataTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            order: [[0, "ASC"]],
            "ajax": {
                "url": "{{route('dFormValueDatatable')}}",
                "dataType": "json",
                "type": "post",
                "data":{ _token: "{{csrf_token()}}",
                        typeId: $('#typeId').val(),
                        formUid: $('#formUid').val(),
                        isActive: $('#isActive').val(),
                    }
            },
            "columns": [
                { data: 'id', className: 'text-center'},
                { data: 'uid', className: 'text-center'},
                { data: "name"},
                { data: "value_field", className: 'text-center', orderable: false},
                { data: "order_by", className: 'text-center'},
                { data: "form_name", orderable: false},
                { data: "form_type", orderable: false},
                { data: "status", className: 'text-center', orderable: false},
                { data: 'action', orderable: false, className: 'text-center d-print-none' },
            ],
            'fnRowCallback': function(nRow, aData, Index) {
                var actionHTML = jsRoleWisePermission(aData.action.set_status, aData.action.action_name, aData.action.action_link);
                $('td:last', nRow).html(actionHTML);
            }
        });
    }


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
            "{{url('gnl/dynamic_value/delete/')}}",
            "{{url('/ajaxDeleteCheck')}}",
            RowID
            // "{{base64_encode('form_id')}}",
            // "{{base64_encode('is_delete,0')}}",
            // "{{base64_encode('gnl_dynamic_form')}}"
        );
    }

 </script>

@endsection
