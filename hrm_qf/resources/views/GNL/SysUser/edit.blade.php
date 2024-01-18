@extends('Layouts.erp_master')
@section('content')

@php
use App\Services\HtmlService as HTML;
use App\Services\CommonService as Common;

@endphp

<form method="post" enctype="multipart/form-data" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">
        <div class="col-lg-8 offset-lg-3">
            <!-- Html View Load  -->
            {!! HTML::forCompanyFeild($suser->company_id, '', true) !!}
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 offset-lg-3">

            @if(Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                {!! HTML::forBranchFeild(true,'branch_id','branch_id',$suser->branch_id) !!}
            @else
                {!! HTML::forBranchFeild(false,'branch_id','branch_id',$suser->branch_id) !!}
            @endif

        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="ParentName">User Role</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="sys_user_role_id" id="userRole" required
                            data-error="Select Role">
                            <!-- <option value="<?php //$roleID ?>">Select One</option> -->
                            @foreach($user_roles as $role)
                            <option value="{{ $role->id }}" <?php if ($role->id == $suser->sys_user_role_id) { echo "selected";}?> >
                                {{ $role->role_name }}
                            </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Employee</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="emp_id" id="employee_id">

                            {{-- onchange="fnCheckDuplicate(
                            '{{base64_encode('gnl_sys_users')}}',
                            this.name+'&&is_delete',
                            this.value+'&&0',
                            '{{url('/ajaxCheckDuplicate')}}',
                            this.id,
                            'txtCodeErrorE',
                            'Employee');" --}}

                            <option value="" selected="selected">Select One</option>

                            @foreach ($EmployeeData as $Row)
                                <option value="{{$Row->id}}" {{ $Row->id == $suser->emp_id ? 'selected' : '' }}>
                                    {{$Row->emp_name." (".$Row->emp_code.")"}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="help-block with-errors is-invalid" id="txtCodeErrorE"></div> --}}
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Full Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Name" name="full_name"
                            id="fullName" required data-error="Please enter name." value="{{ $suser->full_name }}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Username</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter username" name="username"
                            id="username" value="{{ $suser->username }}" readonly>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>


            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Email</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control round" id="email" name="email" placeholder="Enter Email"
                            data-error="Please enter email." value="{{ $suser->email }}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Contact No</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <input type="number" class="form-control round" id="contactNo" name="contact_no"
                            placeholder="Enter Phone Number" value="{{ $suser->contact_no }}">

                    </div>
                </div>
            </div>

            <!--                            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Designation</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" id="designation" name="designation" placeholder="Enter Designation" value="{{ $suser->designation }}">
                    </div>
                </div>
            </div>-->

            <!--                            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Department</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" id="department" name="department" placeholder="Enter Department" value="{{ $suser->department }}">
                    </div>
                </div>
            </div>-->

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">User Image</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control round" readonly="">
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="userImage" name="user_image"
                                    onchange="validate_fileupload(this.id, 1, 'image');">
                            </span>
                        </div>
                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>

                <div class="col-lg-2">
                    @if(!empty($suser->user_image))

                    @if(file_exists($suser->user_image))
                    <img src="{{ asset($suser->user_image) }}" style="width: 70px;">
                    @endif
                    @endif
                </div>
            </div>

            {{-- <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">User Signature</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control round" readonly="">
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="userSignature" name="signature_image"
                                    onchange="validate_fileupload(this.id, 0.1, 'image');">
                            </span>
                        </div>
                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>
                <div class="col-lg-2">
                    @if(!empty($suser->signature_image))

                    @if(file_exists($suser->signature_image))
                    <img src="{{ asset($suser->signature_image) }}" style="width: 70px;">
                    @endif
                    @endif
                </div>
            </div> --}}

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
$('form').submit(function(event) {
    $(this).find(':submit').attr('disabled', 'disabled');
});

$('#branch_id').change(function(){

    let sqlite = "{{ (Common::getDBConnection() == 'sqlite') ? 1 : 0 }}";

    if(sqlite == 1){
        fnAjaxSelectBox('employee_id',
            $('#branch_id').val(),
            '{{base64_encode("hr_employees")}}',
            '{{base64_encode("branch_id")}}',
            '{{base64_encode("id,employee_no,emp_name,emp_code")}}',
            '{{url("/ajaxSelectBox")}}', null, 'isActiveOff'
        );
    }
    else{
        fnAjaxSelectBox('employee_id',
            $('#branch_id').val(),
            '{{base64_encode("hr_employees")}}',
            '{{base64_encode("branch_id")}}',
            '{{base64_encode("id,emp_name,emp_code")}}',
            '{{url("/ajaxSelectBox")}}', null, 'isActiveOff'
        );
    }
});

</script>

@endsection
