@extends('Layouts.erp_master')
@section('content')

<form  method="post" enctype="multipart/form-data" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Module Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Role Name" name="module_name" id="txt_module_name" required data-error="Please enter module name." value="{{ $module->module_name }}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Module Short Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Role Name" name="module_short_name" id="txt_module_name" required data-error="Please enter module name." value="{{ $module->module_short_name }}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Route Link</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="route_link" id="checkDuplicateCode"
                               class="form-control round" 
                               placeholder="Enter Route Link" required 
                               data-error="Please enter route link." 
                               value="{{ $module->route_link }}"
                               onblur="fnCheckDuplicate(
                                '{{base64_encode('gnl_sys_modules')}}', 
                                this.name+'&&is_delete', 
                                this.value+'&&0',
                                '{{url('/ajaxCheckDuplicate')}}',
                                this.id,
                                'txtCodeError', 
                                'route link',
                                '{{$module->id}}');"
                            >
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>

            <!-- <div class="form-row align-items-center">
                <label class="col-lg-3 input-title" for="groupName">Module Icon</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control round" readonly >
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="userImage" name="module_icon">
                                {{-- <input type="hidden" value="{{ $module->module_icon }}" name="old_module_icon"> --}}
                            </span>
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div> -->

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Module Icon</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="module_icon" class="form-control round" 
                        placeholder="Ex: fa-address-book-o" value="{{ $module->module_icon }}" >
                    </div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
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