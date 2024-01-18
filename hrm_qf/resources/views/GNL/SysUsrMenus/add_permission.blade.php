@extends('Layouts.erp_master')
@section('content')

<form method="post" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="ParentName">Permission Type</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        @php
                        $actionList = DB::table('gnl_dynamic_form_value')
                                        ->where([['is_active', 1], ['is_delete', 0],
                                            ['type_id', 2], ['form_id', "GCONF.5"]])
                                        ->orderBy('order_by', 'ASC')
                                        ->pluck('name', 'value_field')
                                        ->toArray();
                        @endphp
                        <select class="form-control clsSelect2" name="set_status" id="select_name">
                            <option value="">Select One</option>
                            @foreach ($actionList as $setStatus => $actionName)
                                <option value="{{ $setStatus }}">{{ "[" . $setStatus. "] " . $actionName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Permission Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Menu Name" name="name" id="RoleName" required data-error="Please enter menu name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Route Link</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Route Link" name="route_link" id="route_link" required data-error="Please enter Route Link." >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Page Title</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <input type="text" class="form-control round" id="page_title" name="page_title">
                    </div>
                </div>
            </div>

            <!-- <div class="form-row align-items-center">
                <label class="col-lg-3 input-title" for="roleName">Method Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Method Name" name="method_name" id="RoleName" data-error="Please enter Method name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                    @error('method_name')
                    <div class="help-block with-errors is-invalid">{{ $message }}</div>
                    @enderror
                </div>
            </div> -->

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title" for="groupName">Order By</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <input type="number" class="form-control round" id="OrderBy" name="order_by" placeholder="Enter Order">
                    </div>
                </div>
            </div>
            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'validateButton2',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>

<script type="text/javascript">
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });
</script>
@endsection
