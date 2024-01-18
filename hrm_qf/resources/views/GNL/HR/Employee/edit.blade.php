@extends('Layouts.erp_master')
@section('content')

@php  
    use App\Services\CommonService as Common;
    use App\Services\HtmlService as HTML;

    $designationData = Common::ViewTableOrder('hr_designations',
                            [['is_delete', 0]],
                            ['id', 'name'],
                            ['name', 'ASC']);

    $departmentData = Common::ViewTableOrder('hr_departments',
                            [['is_delete', 0]],
                            ['id', 'dept_name'],
                            ['dept_name', 'ASC']);
@endphp

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true" autocomplete="off">
    @csrf
    <div class="panel">
        <div class="panel-body">

            <div class="panel panel-default" style="box-shadow:0 0px 0px rgb(0 0 0 / 0%);">
                <div class="panel-heading p-2 mb-4">Organizational Information</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <input type="hidden" name="company_id" id="company_id" value="1">
                            {!! HTML::forBranchFeild(true,'branch_id','branch_id',$EmployeeData->branch_id,'disabled') !!}
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-lg-4 form-group">
                            <label class="input-title RequiredStar">Employee Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="emp_name" name="emp_name"
                                value="{{ $EmployeeData->emp_name}}"
                                    placeholder="Enter Employee Name" required
                                    data-error="Please enter Employee name.">
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title RequiredStar">Employee Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="emp_code" name="emp_code"
                                    value="{{ $EmployeeData->emp_code }}"
                                    placeholder="Enter Employee Code" required
                                    data-error="Please enter Employee Code."
                                    onblur="fnCheckDuplicate(
                                        '{{base64_encode('hr_employees')}}', 
                                        this.name+'&&is_delete', 
                                        this.value+'&&0',
                                        '{{url('/ajaxCheckDuplicate')}}',
                                        this.id,
                                        'txtCodeError', 
                                        'employee code');">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title RequiredStar">Designation</label>
                            <div class="input-group">
                                <select class="form-control clsSelect2" required data-error="Please select Designation"
                                name="designation_id" id="designation_id">
                                    <option value="">Select Designation</option>
                                    @foreach ($designationData as $Row)
                                    <option value="{{$Row->id}}" {{ $Row->id == $EmployeeData->designation_id ? 'selected' : '' }}>{{$Row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title RequiredStar">Department</label>
                            <div class="input-group">
                                <select class="form-control clsSelect2" required 
                                data-error="Please select department"
                                name="department_id" id="department_id">
                                    <option value="">Select Department</option>
                                    @foreach ($departmentData as $Row)
                                    <option value="{{$Row->id}}" {{ $Row->id == $EmployeeData->department_id ? 'selected' : '' }}>{{$Row->dept_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                        

                        <div class="col-lg-4 form-group">
                            <label class="input-title">organizational Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control round" id="org_email" name="org_email"
                                    value="{{$EmployeeData->org_email}}"
                                    placeholder="Enter organizational Email">
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">organizational Mobile</label>
                            <div class="input-group">
                                <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="org_mobile"
                                    value="{{$EmployeeData->org_mobile}}"
                                    id="org_mobile" placeholder="Mobile Number (01*********)"
                                    data-error="Please Enter Mobile Number (01*********)"
                                    minlength="11" maxlength="11"
                                    onblur="fnCheckDuplicate(
                                        '{{base64_encode('hr_employees')}}', 
                                        this.name+'&&is_delete', 
                                        this.value+'&&0',
                                        '{{url('/ajaxCheckDuplicate')}}',
                                        this.id,
                                        'txtCodeErrorM', 
                                        'mobile number');">
                                </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeErrorM"></div>
                        </div>

                        
                    </div>
                </div>
            </div>

            <div class="panel panel-default" style="box-shadow:0 0px 0px rgb(0 0 0 / 0%);">
                <div class="panel-heading p-2 mb-4">Personal Information</div>
                <div class="panel-body">

                    <div class="row">

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Father's Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="father_name_en"
                                    value="{{ $empPersonalDetails->father_name_en }}"
                                    name="father_name_en" placeholder="Enter Father's Name">
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Mother's Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="mother_name_en"
                                    value="{{ $empPersonalDetails->mother_name_en }}"
                                    name="mother_name_en" placeholder="Enter Mother's Name">
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Date of Birth</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="icon wb-calendar round" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control round datepicker" id="dob"
                                name="dob" value="<?= (!empty($empPersonalDetails->dob)) ? date('d-m-Y', strtotime($empPersonalDetails->dob)) : '' ?>"
                                autocomplete="off" placeholder="DD-MM-YYYY">
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Gender</label>
                            <div class="input-group">
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="g1" name="gender" value="male" 
                                    {{ $EmployeeData->gender == 'Male' || $EmployeeData->gender == 'male'? 'checked' : ''}}>
                                    <label for="g1">Male &nbsp &nbsp </label>
                                </div>
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="g2" name="gender" value="female"
                                    {{ $EmployeeData->gender == 'Female' || $EmployeeData->gender == 'female'? 'checked' : ''}}>
                                    <label for="g2">Female &nbsp &nbsp </label>
                                </div>
                                <div class="radio-custom radio-primary">
                                    <input type="radio" id="g3" name="gender" value="others"
                                    {{ $EmployeeData->gender == 'Others' || $EmployeeData->gender == 'others' ? 'checked' : ''}}>
                                    <label for="g3">Others &nbsp &nbsp</label>
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Personal Email</label>
                            <div class="input-group">
                                <input type="email" class="form-control round" id="email" name="email"
                                    value="{{$empPersonalDetails->email}}" placeholder="Enter Personal Email">
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title RequiredStar">Personal Mobile</label>
                            <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="mobile_no"
                                    id="mobile_no" placeholder="Mobile Number (01*********)" required
                                    value="{{$empPersonalDetails->mobile_no}}"
                                    data-error="Please enter mobile number (01*********)" 
                                    minlength="11" maxlength="11"
                                    onblur="fnCheckDuplicate(
                                        '{{base64_encode('hr_emp_personal_details')}}', 
                                        this.name, 
                                        this.value,
                                        '{{url('/ajaxCheckDuplicate')}}',
                                        this.id,
                                        'txtMobileErrorM', 
                                        'mobile number');">
                            <div class="help-block with-errors is-invalid" id="txtMobileErrorM"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">NID/Smart Card</label>
                            <div class="input-group">

                                <input type="text" class="form-control round textNumber" name="nid_no"
                                    value="{{$empPersonalDetails->nid_no}}"
                                    id="nid_no" maxlength="17" placeholder="Enter NID/Smart Card No">
                            </div>
                            <div class="help-block with-errors is-invalid" id="errMsgNID"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Passport</label>
                            <div class="input-group">

                                <input type="text" class="form-control round textNumber" name="passport_no"
                                    id="passport_no" maxlength="9" value="{{$empPersonalDetails->passport_no}}"
                                    placeholder="Enter Passport No">
                            </div>
                            <div class="help-block with-errors is-invalid" id="errMsgPP"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Driving License</label>
                            <div class="input-group">

                                <input type="text" class="form-control round textNumber" 
                                    name="driving_license_no" id="driving_license_no" maxlength="15" 
                                    value="{{$empPersonalDetails->driving_license_no}}"
                                    placeholder="Enter Driving License No">
                            </div>
                            <div class="help-block with-errors is-invalid" id="errMsgDL"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Birth Certificate</label>
                            <div class="input-group">

                                <input type="text" class="form-control round textNumber" 
                                    name="birth_certificate_no" id="birth_certificate_no" maxlength="17" 
                                    value="{{$empPersonalDetails->birth_certificate_no}}"
                                    placeholder="Enter Birth Certificate No">
                            </div>
                            <div class="help-block with-errors is-invalid" id="errMsgBR"></div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Present Address</label>
                            <div class="input-group">
                                <textarea class="form-control round" id="pre_addr_street"
                                    name="pre_addr_street" rows="2" 
                                    placeholder="Enter Address">{{$empPersonalDetails->pre_addr_street}}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-4 form-group">
                            <label class="input-title">Permanent Address</label>
                            <div class="input-group">
                                <textarea class="form-control round" id="par_addr_street"
                                    name="par_addr_street" rows="2"
                                    placeholder="Enter Permanent Address">{{$empPersonalDetails->par_addr_street}}</textarea>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'btnSubmit',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>

<script>

    $( document ).ready(function() {
        $('form').submit(function (event) {
            $(this).find(':submit').attr('disabled', 'disabled');
        });

        // NID Verification
        $("#nid_no").blur(function() {
            let nidLength = $("#nid_no").val().length;
            if (nidLength > 0) {
                if (nidLength != 10) {
                    if (nidLength != 13) {
                        if (nidLength != 17) {
                            $("#errMsgNID").html("Invalid NID! NID must be of 10, 13 or 17 Digits").show();
                            $('#nid_no').css("border-color","red");
                            $('#btnSubmit').attr("disabled","disabled");
                        }else if ( nidLength == 17 ){
                                $("#errMsgNID").html('');
                                $('#nid_no').css('border-color','#e4eaec');

                                // // // Duplicate Check
                                var query = $(this).val();
                                var forWhich = $(this).attr("name");
                                var tableName = btoa('hr_emp_personal_details');

                                var columnName = $(this).attr("name");
                                var columnValue = $(this).val();
                                var url_text = "{{url('/ajaxCheckDuplicate')}}";
                                var fieldID = $(this).attr("id");

                                fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                                'errMsgNID', 'NID');

                                if($('#'+ fieldID).val() !== ''){
                                    $('#btnSubmit').removeAttr("disabled");
                                    $(this).css('border-color','#e4eaec');
                                }
                            }
                    } else if ( nidLength == 13 ){
                            $("#errMsgNID").html('');
                            $('#nid_no').css('border-color','#e4eaec');

                            // // // Duplicate Check
                            var query = $(this).val();
                            var forWhich = $(this).attr("name");
                            var tableName = btoa('hr_emp_personal_details');

                            var columnName = $(this).attr("name");
                            var columnValue = $(this).val();
                            var url_text = "{{url('/ajaxCheckDuplicate')}}";
                            var fieldID = $(this).attr("id");

                            fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                            'errMsgNID', 'NID');

                            if($('#'+ fieldID).val() !== ''){
                                $('#btnSubmit').removeAttr("disabled");
                                $(this).css('border-color','#e4eaec');
                            }
                        }
                } else if ( nidLength == 10 ){
                            $("#errMsgNID").html('');
                            $('#nid_no').css('border-color','#e4eaec');

                            // // // Duplicate Check
                            var query = $(this).val();
                            var forWhich = $(this).attr("name");
                            var tableName = btoa('hr_emp_personal_details');

                            var columnName = $(this).attr("name");
                            var columnValue = $(this).val();
                            var url_text = "{{url('/ajaxCheckDuplicate')}}";
                            var fieldID = $(this).attr("id");

                            fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                            'errMsgNID', 'NID');

                            if($('#'+ fieldID).val() !== ''){
                                $('#btnSubmit').removeAttr("disabled");
                                $(this).css('border-color','#e4eaec');
                            }
                        }
            }
            else {
                $("#errMsgNID").html('');
                $('#nid_no').css("border-color","#e4eaec");
                $('#btnSubmit').removeAttr('disabled');
            }
        });

        $("#passport_no").blur(function() {
            let passportLength = $("#passport_no").val().length;
            if(passportLength  > 0) {
                if (passportLength  != 9) {
                    $("#errMsgPP").html("Not a valid 9-digit Passport Number").show();
                    $(this).css('border-color','red');
                    $('#btnSubmit').attr("disabled","disabled");
                } 
                else if ( passportLength  == 9 ){
                    $("#errMsgPP").html('');
                    $('#passport_no').css('border-color','#e4eaec');

                    // // // Duplicate Check
                    var query = $(this).val();
                    var forWhich = $(this).attr("name");
                    var tableName = btoa('hr_emp_personal_details');

                    var columnName = $(this).attr("name");
                    var columnValue = $(this).val();
                    var url_text = "{{url('/ajaxCheckDuplicate')}}";
                    var fieldID = $(this).attr("id");

                    fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                    'errMsgPP', 'passport no');

                    if($('#'+ fieldID).val() !== ''){
                        $('#btnSubmit').removeAttr("disabled");
                        $(this).css('border-color','#e4eaec');
                    }
                }   
            }
            else {
                $("#errMsgPP").html('');
                $('#passport_no').css("border-color","#e4eaec");
                $('#btnSubmit').removeAttr('disabled');
            }
        });

        $("#driving_license_no").blur(function() {
            let licenceNo = $("#driving_license_no").val().length;
            if(licenceNo > 0) {
                if (licenceNo != 15) {
                    $("#errMsgDL").html("Not a valid 15-digit Driving Licence Number").show();
                    $(this).css('border-color','red');
                    $('#btnSubmit').attr("disabled","disabled");
                }
                else if ( licenceNo == 15 ){
                    $("#errMsgDL").html('');
                    $('#nid_no').css('border-color','#e4eaec');

                    // // // Duplicate Check
                    var query = $(this).val();
                    var forWhich = $(this).attr("name");
                    var tableName = btoa('hr_emp_personal_details');

                    var columnName = $(this).attr("name");
                    var columnValue = $(this).val();
                    var url_text = "{{url('/ajaxCheckDuplicate')}}";
                    var fieldID = $(this).attr("id");

                    fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                    'errMsgDL', 'driving license no');

                    if($('#'+ fieldID).val() !== ''){
                        $('#btnSubmit').removeAttr("disabled");
                        $(this).css('border-color','#e4eaec');
                    }
                }   
            }
            else {
                $("#errMsgDL").html('');
                $('#driving_license_no').css("border-color","#e4eaec");
                $('#btnSubmit').removeAttr('disabled');
            }
        });


        $("#birth_certificate_no").blur(function() {
            let brNoLength = $("#birth_certificate_no").val().length;
            if(brNoLength > 0) {
                if (brNoLength != 17) {
                    $("#errMsgBR").html("Not a valid 17-digit Birth Registration Number").show();
                    $(this).css('border-color','red');
                    $('#btnSubmit').attr("disabled","disabled");
                } 
                else if ( brNoLength == 17 ){
                    $("#errMsgBR").html('');
                    $('#nid_no').css('border-color','#e4eaec');

                    // // // Duplicate Check
                    var query = $(this).val();
                    var forWhich = $(this).attr("name");
                    var tableName = btoa('hr_emp_personal_details');

                    var columnName = $(this).attr("name");
                    var columnValue = $(this).val();
                    var url_text = "{{url('/ajaxCheckDuplicate')}}";
                    var fieldID = $(this).attr("id");

                    fnCheckDuplicate(tableName, columnName, columnValue, url_text, fieldID,
                    'errMsgBR', 'birth registration no');

                    if($('#'+ fieldID).val() !== ''){
                        $('#btnSubmit').removeAttr("disabled");
                        $(this).css('border-color','#e4eaec');
                    }

                }   
            }
            else {
                $("#errMsgBR").html('');
                $('#birth_certificate_no').css("border-color","#e4eaec");
                $('#btnSubmit').removeAttr('disabled');
            }
        });

    });
</script>

@endsection