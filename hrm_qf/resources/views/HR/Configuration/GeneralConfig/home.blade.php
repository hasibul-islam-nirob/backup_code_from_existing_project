@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>
<h4 class="example-title text-center">Employee Code Generator</h4>
<form action="#" id="employeeCodeGenerator">
<div class="form-row form-group align-items-center">
    <h4 class="col-lg-4 input-title text-right">Employee Code Generator Type</h4>
    <div class="col-lg-7">
        <div class="input-group">
            <div class="radio-custom radio-primary mr-2">
                <input type="radio" name="gen_type" id="manual" value="manual" onclick="showInputField();" checked>
                <label for="manual" >Manual </label>
            </div>
            <div class="radio-custom radio-primary mr-2">
                <input type="radio" name="gen_type" id="automatic" value="automatic" onclick="showTable();" >
                <label for="automatic">Automatic </label>
            </div>
        </div>
    </div>
</div>

<div id="employeeCodeGeneratorTable">
    <div class="tab-pane show">
        <div>
            <table class="table w-full table-hover table-bordered table-striped">
                <thead>
                <tr>
                    <th width="13%">Content</th>
                    <th width="12%">IsApplicable</th>
                    <th width="12%">Value</th>
                    <th width="12%">Length</th>
                    <th width="12%">Position</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="label text-center">Prefix</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="prefix" id="prefix" data-plugin="switchery" onchange="changePrefixHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                    <input type="text" class="form-control round " name="prefix_val" id = "prefix_val" placeholder="Enter Prefix">

                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="number" class="form-control round textNumber" name="pre_position" id="pre_position" placeholder="Enter Position" value="1" readonly>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="form-control-label text-center">Organization-Wise Employee Serial</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="org_emp_serial" id="org_emp_serial" data-plugin="switchery" onchange="changeOrgHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="org_emp_serial_length" id="org_emp_serial_length" placeholder="Enter Length" >
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="org_emp_serial_position" id="org_emp_serial_position" placeholder="Enter Position" >
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="form-control-label text-center">Project-Wise Employee Serial</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="pro_emp_serial" id="pro_emp_serial" data-plugin="switchery" onchange="changeProHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="pro_emp_serial_length" id="pro_emp_serial_length" placeholder="Enter Length" >
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="pro_emp_serial_position" id="pro_emp_serial_position" placeholder="Enter Position" >
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="label text-center">Project Code</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="project_code" id="project_code" data-plugin="switchery" onchange="changeProCodeHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="project_code_position" id="project_code_position" placeholder="Enter Position" >
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="label text-center">Year-Month</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="year_month" id="year_month" data-plugin="switchery" onchange="changeYMHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="text" class="form-control round textNumber" name="year_month_position" id="year_month_position" placeholder="Enter Position" >
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-row align-items-center">
                                <label class="label text-center">Separator</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                <input type="checkbox" name="separator" id="separator" data-plugin="switchery" onchange="changeSeparatorHiddenField();"/>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="form-row align-items-center">
                            <div class="input-group">
                                    <input type="text" class="form-control round " name="separator_val" id = "separator_val" placeholder="Enter Separator" >

                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 form-group text-right">
            <div class="input-group checkbox-custom checkbox-primary">
                <input type="checkbox" id="preview" onchange="getData();">
                <label>Preview</label>
            </div>
        </div>
    </div>
    <div class="row" > <label class="col-lg-4 label text-right">Generated Employee Code</label><div class="col-lg-6" id="emp_code"></div></div>
</div>
<div class="row">
    <div class="col-lg">
        <div class="form-group d-flex justify-content-center">
            <div class="example example-buttons">
                <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                <button type="submit" class="btn btn-primary btn-round" id="btnSubmitCompany">Save</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$('#employeeCodeGeneratorTable').hide();
$('#prefix_val').hide();
$('#pre_position').hide();
$('#org_emp_serial_length').hide();
$('#org_emp_serial_position').hide();
$('#pro_emp_serial_length').hide();
$('#pro_emp_serial_position').hide();
$('#project_code_position').hide();
$('#year_month_position').hide();
$('#separator_val').hide();
function showInputField() {
    $('#employeeCodeGeneratorTable').hide();
}
function showTable() {
    $('#employeeCodeGeneratorTable').show();
}
function changePrefixHiddenField() {
    if (($('#prefix_val').is(":hidden") == true) && ($('#pre_position').is(":hidden") == true) ) {
        $('#prefix_val').show();
        $('#pre_position').show();
    } else {
        $('#prefix_val').hide();
        $('#pre_position').hide();
    }
}
function changeOrgHiddenField() {
    if (($('#org_emp_serial_length').is(":hidden") == true) && ($('#org_emp_serial_position').is(":hidden") == true) ) {
        $('#org_emp_serial_length').show();
        $('#org_emp_serial_position').show();
    } else {
        $('#org_emp_serial_length').hide();
        $('#org_emp_serial_position').hide();
    }
}
function changeProHiddenField() {
    if (($('#pro_emp_serial_length').is(":hidden") == true) && ($('#pro_emp_serial_position').is(":hidden") == true) ) {
        $('#pro_emp_serial_length').show();
        $('#pro_emp_serial_position').show();
    } else {
        $('#pro_emp_serial_length').hide();
        $('#pro_emp_serial_position').hide();
    }
}
function changeProCodeHiddenField() {
    if (($('#project_code_position').is(":hidden") == true) ) {
        $('#project_code_position').show();
    } else {
        $('#project_code_position').hide();
    }
}
function changeYMHiddenField() {
    if (($('#year_month_position').is(":hidden") == true) ) {
        $('#year_month_position').show();
    } else {
        $('#year_month_position').hide();
    }
}
function changeSeparatorHiddenField() {
    if (($('#separator_val').is(":hidden") == true)) {
        $('#separator_val').show();
    } else {
        $('#separator_val').hide();
    }
}

function getData() {
    if ($('#preview').is(":checked") == false ) {
        $('#emp_code').html('');
    }
    else
    {
        let months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        let codeStruct = [
        {"code" : $('#prefix_val').val(), "pos" : 1},
        {"code" : "O", "pos" : $('#org_emp_serial_position').val(), "len" : $('#org_emp_serial_length').val()},
        {"code" : "P", "pos" : $('#pro_emp_serial_position').val(), "len" : $('#pro_emp_serial_length').val()},
        {"code" : "PR005", "pos" : $('#project_code_position').val()},
        {"code" : "" + new Date().getFullYear() + "" + months[new Date().getMonth()], "pos" : $('#year_month_position').val()},
        {"code" : $('#separator_val').val(), "pos" : 99}
    ];

    for (let i=0; i<codeStruct.length-1; i++){
        for (let j=0; j<codeStruct.length-1-i; j++){
            if (codeStruct[j].pos > codeStruct[j+1].pos){
                let temp = codeStruct[j];
                codeStruct[j] = codeStruct[j+1];
                codeStruct[j+1] = temp;
            }
        }
    }

    //console.log(codeStruct);

    let empCode = '';
    let strLen = codeStruct.length;
    for (let i=0; i<strLen-1; i++){
        if (codeStruct[i].code !== "" && codeStruct[i].pos !== ""){
            if (typeof codeStruct[i].len !== 'undefined'){
                codeStruct[i].code = "";
                for (let n = 0; n<codeStruct[i].len-1; n++){
                    codeStruct[i].code += "0";
                }
                codeStruct[i].code += Math.floor(Math.random() * 10);
            }
            empCode += codeStruct[i].code;
            if (i !== strLen-2){
                empCode += codeStruct[strLen-1].code;
            }
        }
    }
        $('#emp_code').html('');
        $('#emp_code').append(`<input type="text" class="form-control round " id = "emp_code" placeholder='${empCode}' value="${empCode}" readonly>`);
    //console.log(empCode);

    /*$.ajax({
            type: "POST",
            url: " {{route('getEmployeeCode')}}",
            data: $('form').serialize(),
            dataType: "json",
            success: function (response) {
                $('#emp_code').html('');
                $('#emp_code').append(`<input type="text" class="form-control round " id = "emp_code" placeholder='${response}' value="${response}" readonly>`);

            },
            error: function(){
                alert('error!');
            }
        });*/
    }
}
$('#pre_position,#prefix, #prefix_val, #org_emp_serial, #org_emp_serial_length, #org_emp_serial_position, #pro_emp_serial, #pro_emp_serial_length,#pro_emp_serial_position,#project_code,#project_code_position,#year_month,#year_month_position,#separator,#separator_val').change(function (e) {
    e.preventDefault();
    if ($('#preview').is(":checked") == true ) {
        $('#preview').prop("checked",false);
    }
});
$('form').submit(function (event) {
    event.preventDefault();
        // $(this).find(':submit').attr('disabled', 'disabled');
        $.ajax({
                url: "{{ url()->current() }}",
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
                success: function (response) {
                    swal({
                        icon: 'success',
                        title: 'Success...',
                        text: response['message'],
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = "{{ url()->current() }}";
                    });
                },
                error: function () {
                    alert('error!');
                }
            })
});
</script>
@endsection
