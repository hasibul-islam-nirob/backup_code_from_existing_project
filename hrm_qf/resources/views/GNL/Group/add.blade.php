@extends('Layouts.erp_master')
@section('content')

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator"
    novalidate="true" autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <div class="form-row form-group align-items-center">
                <label for="textGroupName" class="col-lg-3 input-title RequiredStar">Group Name</label>
                <div class="col-lg-5">
                    <div class="input-group ">
                        <input type="text" class="form-control round" placeholder="Enter Group Name"
                            name="group_name" id="textGroupName" required
                            data-error="Please enter Group name."
                            onblur="fnCheckDuplicate(
                                '{{base64_encode('gnl_groups')}}', 
                                this.name+'&&is_delete', 
                                this.value+'&&0',
                                '{{url('/ajaxCheckDuplicate')}}',
                                this.id,
                                'txtCodeError', 
                                'group name');"
                            >
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>
            <div class="form-row form-group align-items-center">
                <label for="textGroupName" class="col-lg-3 input-title">Group Email</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="email" class="form-control round" name="group_email"
                            id="GroupEmail" placeholder="Enter Group Email">

                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row form-group align-items-center">
                <label for="textGroupName" class="col-lg-3 input-title RequiredStar">Mobile</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber"
                            name="group_phone" id="textGroupPhone" placeholder="Mobile Number (01*********)"
                            required data-error="Please enter mobile number (01*********)" minlength="11" maxlength="11"
                            onblur="fnCheckDuplicate(
                                '{{base64_encode('gnl_groups')}}',
                                this.name+'&&is_delete',
                                this.value+'&&0',
                                '{{url('/ajaxCheckDuplicate')}}',
                                this.id,
                                'errMsgPhone',
                                'mobile number');">
                    </div>
                    <div class="help-block with-errors is-invalid" id="errMsgPhone"></div>
                </div>
            </div>
            <div class="form-row form-group align-items-center">
                <label for="textGroupName" class="col-lg-3 input-title">Address</label>
                <div class="col-lg-5">
                    <textarea class="form-control round" name="group_addr" id="GroupAddress" rows="2"
                        placeholder="Enter Address"></textarea>
                </div>
            </div>
            <div class="form-row form-group align-items-center">
                <label for="textGroupName" class="col-lg-3 input-title">Website</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control round" id="group_web_add"
                        name="group_web_add" placeholder="Enter Website">
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Group logo</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control round" readonly="">
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file" style="height: 30px">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="groupimage" name="group_logo" 
                                    onchange="validate_fileupload(this.id, 1, 'image');">
                            </span>
                        </div>
                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>
            </div>
            
            <div class="form-row form-group align-items-center">
                <label for="short_form" class="col-lg-3 input-title">Short Name</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" class="form-control round" name="short_form"
                            id="short_form" placeholder="Enter Group Short Name For barcode">

                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            
            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'submitButtonforGroup',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>

<script type="text/javascript">
    $('form').submit(function(event) {
        $(this).find(':submit').attr('disabled', 'disabled');

    });
</script>
@endsection