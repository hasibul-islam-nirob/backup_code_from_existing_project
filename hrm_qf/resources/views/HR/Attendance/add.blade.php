<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>
{{-- <style>
    .testt {
        z-index: 99999!important;
    }
</style> --}}
<form id="attendance_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row justify-content-center">
                {!! HTML::forCompanyFeild() !!}
                
                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'isRequired'=> true,
                    'divClass'=> "col-sm-6 form-group",
                    'formStyle'=> "vertical"
                ]) !!}

                <div class="col-sm-4 form-group d-none">
                    <label class="input-title">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('department_id') !!}
                    </div>
                </div>

                <div class="col-sm-4 form-group  d-none">
                    <label class="input-title">Designation</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('designation_id') !!}
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="emp_id" class="form-control clsSelect2" style="width: 100%">

                        </select>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group" style="display: none;">
                    <label class="input-title RequiredStar">Device</label>
                    <div class="input-group">
                        <select name="device_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select device</option>
                            <option value="1">Device 1 </option>
                            <option value="2">Device 2 </option>
                            <option value="3">Device 3 </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Current Date Time</label>
                    <div class="input-group">
                        <input name="" type="text" style="z-index:99999 !important;"
                        class="form-control datepicker-custom" id="hasDatepickerFixed" placeholder="DD-MM-YYYY" value ="" disabled>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">

                <div class="col-sm-3 form-group">
                    <label class="input-title RequiredStar">Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input name="time_and_date" type="text" style="z-index:99999 !important;"
                            class="form-control datepicker-custom" id="hasDatepicker" placeholder="DD-MM-YYYY" value ="">

                    </div>
                </div>

                <div class="col-sm-3 form-group">
                    <label class="input-title RequiredStar">Time</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="ext_start_time" name="ext_start_time" type="text" style="z-index:99999 !important;"
                            class="form-control" placeholder="H:M" autocomplete="off">

                    </div>
                </div>

            </div>

        </div>

    </div>
    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css-js/timepicker-master/timepicker.css')}}">
<script src="{{asset('assets/css-js/timepicker-master/timepicker.js')}}"></script>

<script>

    $(document).ready(function() {


        // Start Fixed
        function formatAMPM(date) {
            let hours = date.getHours();
            let minutes = date.getMinutes();
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // The hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            let strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }
        function formatDateWithTime(date) {
            let year = date.getFullYear();
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let day = date.getDate().toString().padStart(2, '0');
            let time = formatAMPM(date);
            return day + '-' + month + '-' + year + ' ' + time;
        }

        let now = new Date();
        let formattedDateTime = formatDateWithTime(now);
        $("#hasDatepickerFixed").val(formattedDateTime);
        // End Fixed


        $('#hasDatepicker').datepicker();
        $('#hasDatepicker').datepicker('setDate', 'today');
        var dt = new Date();
        var time = dt.getHours() + ":" + dt.getMinutes();
        $("#ext_start_time").val(time);


        $('#start_time, #end_time, #ext_start_time').timepicker();

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        getEmployeeOptions();
        getDesignationOptions();
    });

    $('#branch_id').on('change', function(){
        getDesignationOptions();
    })

    $('#branch_id, #department_id, #designation_id').on('change', function(){
        $('#add_employee_id').val('').trigger('change')
        getEmployeeOptions();
    })

    function getDesignationOptions(){

        callApi("{{ url()->current() }}/../getDesigData", 'get',{
                context:"getDesignationData",
                branchId: $("#branch_id").val(),
            },
            function(response, textStatus, xhr) {

                $("#designation_id").empty().append($('<option>', {
                        value: '',
                        text: 'Select Designation'
                }));

                $.each(response, function(i, item) {
                    $('#designation_id').append($('<option>', {
                        value: item.id,
                        text: item.name
                    }));
                });

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );
    }

    function getEmployeeOptions(){

        var selEmp = $('#add_employee_id').val();
        callApi("{{ url()->current() }}/../getData", 'get',{
            context:"employeeData",
            branchId: $("#branch_id").val(),
            departmentId: $("#department_id").val(),
            designationId: $("#designation_id").val(),
        },
            function(response, textStatus, xhr) {

                $('#add_employee_id').select2({
                    dropdownParent: $("#commonModal"),
                    data: response,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: function(data) {
                        return data.html;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                });


            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );
    }

    $("#add_employee_id").on('change', function(){
        // autoSetValue()
    });

    function autoSetValue(){

        let id = $("#add_employee_id").val();
        if(id != null){
            callApi("{{ url()->current() }}/../../employee_attendance/employeeInfo/"+ id +"/api", 'post', '',

                function(response, textStatus, xhr) {
                    $("#branch_id").val(response.branch_id).change();
                    $("#department_id").val(response.department_id).change();
                    $("#designation_id").val(response.designation_id).change();
                },
                function(response) {
                    showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
                }
            );
        }
    }

    showModal({
        titleContent: "Attendance",
        footerContent: getModalFooterElement({
            'btnNature': {
                1: 'save',
            },
            'btnName': {
                1: 'Save',
            },
            'btnId': {
                1: 'add_saveBtn',
            }
        }),
    });


    $('#add_saveBtn').click(function(event) {
        $('#add_saveBtn_submit').click();
    });

    $('#attendance_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#attendance_add_form')[
                0]),
            function(response, textStatus, xhr) {

                if(response == 400){
                    swal({
                        icon: 'warning',
                        title: 'Warning...',
                        text: "Attendance rules not set. Please configure attendance rules. Go to Configuration > attendance rules.",
                    });
                }else{
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                }

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>
