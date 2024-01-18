@extends('Layouts.erp_master')

@section('content')

@php
use App\Services\CommonService as Common;
@endphp

<!-- Page -->
    <form id="comForm" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf

        <ul class="nav nav-tabs  nav-tabs-reverse nav-fill" id="TabID">
            <li class="nav-item">
                <a href="#Basic" onclick="changeFormSource('basic')" class="nav-link basicTab" data-toggle="tab">Basic</a>
            </li>

            <li class="nav-item">
                <a href="#Configaration" onclick="changeFormSource('config')" class="nav-link configTab" data-toggle="tab">Configaration</a>
            </li>
        </ul>

        

        <div class="tab-content" style="background:none;">
            <!-- Basic Basic  -->
            <div class="tab-pane active" id="Basic">
                <div class="row">
                    <div class="col-sm-9 offset-lg-3">


                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title RequiredStar">Group</label>
                            <div class="col-sm-5 form-group">

                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="group_id" id="selgroup_id"
                                        required data-error="Select Group">
                                        <option value="">Select One</option>
                                        @foreach ($GroupData as $Row)
                                        <option value="{{$Row->id}}"
                                            {{ ($CompanyData->group_id == $Row->id) ? 'selected="selected"' : '' }}>
                                            {{$Row->group_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title RequiredStar">Company Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="comp_name"
                                        id="txtCompanyName" value="{{$CompanyData->comp_name}}"
                                        placeholder="Enter Company Name" required
                                        data-error="Please enter Company name.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title RequiredStar">Company Code</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" name="comp_code" id="checkDuplicateCode"
                                        class="form-control round" placeholder="Enter Company Code" required
                                        data-error="Please enter company code." value="{{$CompanyData->comp_code}}"
                                        onblur="fnCheckDuplicate(
                                        '{{base64_encode('gnl_companies')}}',
                                        this.name+'&&is_delete',
                                        this.value+'&&0',
                                        '{{url('/ajaxCheckDuplicate')}}',
                                        this.id,
                                        'txtCodeError',
                                        'company code',
                                        '{{$CompanyData->id}}');">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Company Phone</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber"
                                        name="comp_phone" value="{{$CompanyData->comp_phone}}"
                                        id="comp_phone" placeholder="Mobile Number (01*********)"
                                        data-error="Please enter mobile number (01*********)"
                                        minlength="0" maxlength="11"
                                        onblur="fnCheckDuplicate(
                                        '{{base64_encode('gnl_companies')}}',
                                        this.name+'&&is_delete',
                                        this.value+'&&0',
                                        '{{url('/ajaxCheckDuplicate')}}',
                                        this.id,
                                        'errMsgPhone',
                                        'mobile number',
                                        '{{$CompanyData->id}}');">
                                </div>
                                <div class="help-block with-errors is-invalid" id="errMsgPhone"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Email</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="email" class="form-control round" name="comp_email"
                                        id="txtCompanyEmail" value="{{$CompanyData->comp_email}}"
                                        placeholder="Enter Company Email">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Address</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <textarea class="form-control round" name="comp_addr" id="txtCompanyAddress"
                                        rows="2" placeholder="Enter Address">{{$CompanyData->comp_addr}}</textarea>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Website</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="comp_web_add"
                                        id="txtCompanyWeb" placeholder="Example www.example.com"
                                        value="{{$CompanyData->comp_web_add}}">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>


                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Company logo</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group input-group-file" data-plugin="inputGroupFile">
                                    <input type="text" class="form-control round" readonly="">
                                    <div class="input-group-append">
                                        <span class="btn btn-success btn-file" style="height: 30px">
                                            <i class="icon wb-upload" aria-hidden="true"></i>
                                            <input type="file" id="CompanyImage" name="comp_logo"
                                                onchange="validate_fileupload(this.id, 1, 'image');">
                                        </span>
                                    </div>
                                </div>
                                <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                            </div>
                            <div class="col-sm-2">
                                @if(!empty($CompanyData->comp_logo))

                                @if(file_exists($CompanyData->comp_logo))
                                <img src="{{ asset($CompanyData->comp_logo) }}" style="width: 70px;">
                                @endif
                                @endif
                            </div>
                        </div>

                        @if(Common::isSuperUser() == true)
                        <div class="form-row align-items-center">
                            <label class="col-sm-12 input-title">
                                Module Selection
                            </label>
                            <div class="col-sm-12">
                                <div class="row">
                                <?php
                                    $sysModules = Common::ViewTableOrder('gnl_sys_modules',
                                    [['is_active', 1], ['is_delete', 0]],
                                    ['id', 'module_name', 'module_short_name'],
                                    ['id', 'ASC']
                                    );

                                    $selecetedModule = explode(',', $CompanyData->module_arr);
                                    $i = 0;
                                    foreach($sysModules as $module){

                                        if (in_array($module->id, $selecetedModule)) {
                                            $CheckText = 'checked';
                                        } else {
                                            $CheckText = '';
                                        }

                                        $i++;
                                        ?>
                                        <div class="col-sm-4">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" class="checkboxs" {{$CheckText}} name="module_arr[]" id="module_arr_{{$i}}" value="{{$module->id}}" />
                                                <label for="module_arr_{{$i}}" style="color:#000;">{{$module->module_name}}</label>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                        @endif


                        <br><br>

                        @if(Common::isSuperUser() == true)
                        

                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">DB Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="db_name"
                                        id="db_name" value="{{$CompanyData->db_name}}"
                                        placeholder="Enter DB Name"
                                        data-error="Please enter DB name.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Host Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="host"
                                        id="host" value="{{$CompanyData->host}}"
                                        placeholder="Enter Host Name"
                                        data-error="Please enter Host name.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">User Name</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="username"
                                        id="username" value="{{$CompanyData->username}}"
                                        placeholder="Enter User Name"
                                        data-error="Please enter User name.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Password</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="password"
                                        id="password" value="{{$CompanyData->password}}"
                                        placeholder="Enter Password"
                                        data-error="Please enter Password.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-sm-3 input-title">Port</label>
                            <div class="col-sm-5 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="port"
                                        id="port" value="{{$CompanyData->port}}"
                                        placeholder="Enter Port"
                                        data-error="Please enter Port.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        @endif
                    </div>
                </div>
            </div>
            <!-- End Basic Basic  -->

            <!-- Configaration Configaration  -->
            <div class="tab-pane fade" id="Configaration">
                <div class="row">
                    <div id="dynamic_div" class="col-sm-9 offset-lg-3">

                    </div>
                </div>
            </div>
            <!-- End Configaration Configaration  -->
        </div>

        <input type="hidden" value="basic" name="submitFrom" id="submitType">
         <input type="hidden" value="" name="comId" id="comId">
        @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'nextTab',
                            'exClass' => 'float-right'
                        ]])
    </form>
<!-- End Page -->

<script type="text/javascript">

    $(document).ready(function(){
        let formRows = {!! json_encode($dFormRows) !!};
        createDynamicform(formRows);
        activaTab('Basic');
    });

    function changeFormSource(val){
        $('#submitType').val(val);
    }

    $('form#comForm').submit(function (e){
        e.preventDefault();

        let grp = $('#selgroup_id').val();
        let comName = $('#comp_name').val();
        let comCode = $('#checkDuplicateCode').val();

        if (grp == ""){
            alert("Group is required!!");
        }
        else if (comName == ""){
            alert("Company name is required!!");

        }
        else if (comCode == ""){
            alert("Company code is required!!");

        }
        else {
            var formData = new FormData(this);
            $.ajax({
                method: "post",
                url: "{{ url()->current() }}",
                datatype: "json",
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
                success: function (response){
                    if (response.alert_type === 'success'){

                        swal({
                            icon: 'success',
                            title: 'Success...',
                            text: response['message'],
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            if (response.addedTo === 'basic'){
                                $('.configTab').removeClass('disabled');
                                $('.basicTab').addClass('disabled');
                                $('#nextTab').val('Update');
                                $('#submitType').val('config');
                                $('#comId').val(response.comId);
                                activaTab('Configaration');
                            }
                            else {
                                window.location.href = "../";
                            }
                        });

                    }
                    else{

                    }
                }
            });
        }

    });

    /*$('#nextTab').click(function (){


    });*/

    function activaTab(tab){
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    };

    function createDynamicform(row){
        $.each(row, function (index, value){
            if (value.input_type === 'select'){
                makeSelectBox(value);
            }
            else if (value.input_type === 'radio'){
                makeRadioButton(value);
            }
            else if (value.input_type === 'text'){
                makeTextBox(value);
            }
            else if (value.input_type === 'checkbox'){
                makeCheckBox(value);
            }
            else if (value.input_type === 'textarea'){
                makeTextArea(value);
                
            }
        });
    }

    function makeTextBox(row){
        let html = "";
        let val = row.pre_value ? row.pre_value : "";

        html += '<div class="form-row align-items-center">';
        html += '<label class="col-sm-3 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-5 form-group">';
        html += '<div class="input-group">';
        html += '<input type="text" class="form-control round" value="'+ val +'" name="dynamic_form_'+ row.id +'" placeholder="Enter '+ row.name +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        html += '</div>';
        //html += '<div class="help-block with-errors is-invalid"></div>';
        html += '</div>';
        html += '</div>';

        $('#dynamic_div').append(html);
    }

    function makeSelectBox(row){

        let html = "";
        let endFiscalHidden = "";

        html += '<div class="form-row align-items-center">';
        html += '<label class="col-sm-3 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-5 form-group">';
        html += '<div class="input-group">';

        if (row.name === "Fiscal year start"){
            html += '<select class="form-control clsSelect2" onchange="fiscalYearStartChanged(this.value)" name="dynamic_form_'+ row.id +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        }
        else if (row.name === "Fiscal year end") {
            html += '<select disabled class="form-control clsSelect2" id="endFiscalYear" data-error="Select '+ row.name +'" style="width: 100%;" >';
            endFiscalHidden += '<input id="hiddenEndFiscalYear" value="'+ row.pre_value +'" type="hidden" name="dynamic_form_'+ row.id +'">';
        }
        else {
            html += '<select class="form-control clsSelect2" name="dynamic_form_'+ row.id +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        }
        //html += '<select class="form-control clsSelect2" name="dynamic_form_'+ row.id +'" required data-error="Select '+ row.name +'" style="width: 100%;" >';


        html += '<option value="">Select One</option>';

        $.each(row.form_values, function (index, value){
            if (value.value_field === row.pre_value){
                html += '<option selected value="'+ value.value_field +'">'+ value.name +'</option>';
            }
            else{
                html += '<option value="'+ value.value_field +'">'+ value.name +'</option>';
            }
        });

        html += '</select>';

        html+= endFiscalHidden;

        html += '</div>';
        //html += '<div class="help-block with-errors is-invalid"></div>';
        html += '</div>';
        html += '</div>';

        $('#dynamic_div').append(html);
    }

    function makeRadioButton(row){
        let html = "";

        html += '<div class="form-row align-items-center">';
        html += '<label class="col-sm-3 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-5 form-group">';
        html += '<div class="input-group">';


        html += '<label class="switch">';

        if (row.pre_value == 1){
            html += '<input checked type="checkbox" name="dynamic_form_'+ row.id +'" style="width: 100%;" value="1">';
        }
        else {
            html += '<input type="checkbox" class="checkbox_class" name="dynamic_form_'+ row.id +'" style="width: 100%;">';
        }

        html += '<span class="slider round"></span>';
        html += '</label>';

        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#dynamic_div').append(html);
    }

    function makeCheckBox(row){
        let html = "";

        html += '<div class="form-row align-items-center">';
        html += '<label class="col-sm-3 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-5 form-group">';
        html += '<div class="input-group">';

        console.log();

        


        $.each(row.form_values, function (index, value){

            let check = "";

            if (typeof row.pre_values !== 'undefined'){
                if(row.pre_values.includes(value.value_field)){
                    check = "checked"; 
                }
                
            }

            html += '<div class="checkbox-custom checkbox-primary" style="margin-right: 10%">';
            html += '<input type="checkbox" id="'+ value.name +'" '+check+' value="'+ value.value_field +'" class="checkboxs" name="dynamic_form_'+ row.id +'[]">';
            html += '<label for="'+ value.name +'">'+ value.name +'</label>';
            html += '</div>';
        });

        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#dynamic_div').append(html);
    }

    function makeTextArea(row){

    }
    function fiscalYearStartChanged(val){
        var startVal = val;
        if (startVal === '01-01') {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("31-12");

            $('#hiddenEndFiscalYear').val("31-12");

        }
        else {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');

        }

        if (startVal === '01-07') {
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("30-06");

            $('#hiddenEndFiscalYear').val("30-06");

        }
        else{
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');

        }
        $('form').submit(function (event) {
            // event.preventDefault();
            $(this).find(':submit').attr('disabled', 'disabled');
            // $(this).submit();
        });
    }

 
   

    $(document).on('click', '.checkbox_class', function () {
        var value = 0;
        if(this.checked) // if changed state is "CHECKED"
        {
            value = 1;
        }else{
            value = 0;
        }
        $('.checkbox_class').val(value);
        console.log(value, 'test');

    
    });

    /*$('#selectFiscalYearStart').change(function() {

        var startVal = $(this).children("option:selected").val();

        if (startVal === '01-01') {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("31-12");
        } else {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');
        }

        if (startVal === '01-07') {
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("30-06");
        } else {
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');
        }
        $('form').submit(function(event) {
            // event.preventDefault();
            $(this).find(':submit').attr('disabled', 'disabled');
            // $(this).submit();
        });

    });*/

</script>
@endsection
