<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>
<style>
    .testt {
        z-index: 99999!important;
    }
</style>
<form id="attendance_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row justify-content-center">

                {!! HTML::forCompanyFeild() !!}

                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'isRequired'=> true,
                    'elementId' => 'edit_branch_id',
                    'isDisabled' =>false,
                    'divClass'=> "col-sm-5 form-group",
                    'formStyle'=> "vertical"
                ]) !!}

                {{-- <div class="col-sm-4 form-group">
                    {!! HTML::forBranchFeildNew(true, 'branch_id', 'edit_branch_id') !!}

                    {!! HTML::forCompanyFeild() !!}

                </div> --}}

                <div class="col-sm-4 form-group  d-none">
                    <label class="input-title RequiredStar">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('edit_department_id') !!}
                    </div>
                </div>

                <div class="col-sm-4 form-group  d-none">
                    <label class="input-title RequiredStar">Designation</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('edit_designation_id') !!}
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="edit_employee_id" name="emp_id" class="form-control" style="width: 100%">

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
                        class="form-control datepicker-custom" id="editHasDatepickerFixed" placeholder="DD-MM-YYYY" value ="" disabled>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">

                <div class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="time_and_date" name="time_and_date" type="text" style="z-index:99999 !important;"
                            class="form-control datepicker-custom" placeholder="DD-MM-YYYY" >

                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Time</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_start_time" name="ext_start_time" type="text" style="z-index:99999 !important;"
                            class="form-control" placeholder="H:M" autocomplete="off" required>

                    </div>
                </div>

            </div>

        </div>

    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css-js/timepicker-master/timepicker.css')}}">
<script src="{{asset('assets/css-js/timepicker-master/timepicker.js')}}"></script>


<!-- End Page -->
<script type="text/javascript">

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
        $("#editHasDatepickerFixed").val(formattedDateTime);
        // End Fixed


    $(document).ready(function(){

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        getEmployeeOptionsEdit();

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#attendance_edit_form')[0]),
            function(response, textStatus, xhr) {

                $('#edit_id').val("{{ $id }}");
                $("#edit_branch_id").val(response.result_data.branch_id).change();
                $("#edit_department_id").val(response.result_data.department_id).change();
                $("#edit_designation_id").val(response.result_data.designation_id).change();
                $("#edit_employee_id").val(response.result_data.emp_id).change();
                $("#time_and_date").val(response.result_data.time_and_date);

                // console.log(response.result_data.emp_id);


            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

        $('#time_and_date').datepicker();
        $('#time_and_date').datepicker('setDate', 'today');
        var dt = new Date();
        var time = dt.getHours() + ":" + dt.getMinutes();
        $("#edit_start_time").val(time);

    });



    // $("#branch_id").on('change', function(){
    //     getEmployeeOptions()
    //     $("#department_id").val('').change();
    // })
    // $("#department_id").on('change', function(){
    //     getEmployeeOptions()
    //     $("#designation_id").val('').change();
    // })

    function getEmployeeOptionsEdit(){

        callApi("{{ url()->current() }}/../../getData", 'get',
        {
            context:"employeeData",
            branchId: $("#edit_branch_id").val(),
            departmentId: $("#edit_department_id").val(),
            designationId: $("#edit_designation_id").val(),

        },
            function(response, textStatus, xhr) {

                $('#edit_employee_id').empty();

                $('#edit_employee_id').select2({
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

    // function getEmployeeOptions(){

    //     // callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#attendance_edit_form')[0]),
    //     //     function(response, textStatus, xhr) {

    //     //         $('#edit_id').val("{{ $id }}");

    //     //         if( $("#edit_branch_id").val() == '' || $("#edit_department_id").val() == '' || $("#edit_designation_id").val() == '' ){

    //     //             $("#edit_branch_id").val(response.result_data.branch_id).change();
    //     //             $("#edit_department_id").val(response.result_data.department_id).change();
    //     //             $("#edit_designation_id").val(response.result_data.designation_id).change();
    //     //             $("#edit_employee_id").val(response.result_data.emp_id).change();
    //     //         }

    //     //         console.log(response.result_data.emp_id);



    //     //     },
    //     //     function(response) {
    //     //         showApiResponse(response.status, JSON.parse(response.responseText).message);
    //     //     }
    //     // );


    //     callApi("{{ url()->current() }}/../getData", 'get',
    //     {
    //         context:"employeeData",
    //         branchId: $("#edit_branch_id").val(),
    //         departmentId: $("#edit_department_id").val(),
    //         designationId: $("#edit_designation_id").val(),
    //     },
    //         function(response, textStatus, xhr) {

    //             $('#edit_employee_id').empty();

    //             $('#edit_employee_id').select2({
    //                 data: response,
    //                 escapeMarkup: function(markup) {
    //                     return markup;
    //                 },
    //                 templateResult: function(data) {
    //                     return data.html;
    //                 },
    //                 templateSelection: function(data) {
    //                     return data.text;
    //                 }
    //             });

    //         },
    //         function(response) {
    //             showApiResponse(response.status, JSON.parse(response.responseText).message);
    //         }, false, true
    //     );

    // }


    showModal({
        titleContent: "Edit Attendance",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'update',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'edit_updateBtn',
            }
        }),
    });

    $('#start_time, #end_time, #edit_start_time').timepicker();


    $('#edit_updateBtn').click(function(event) {
        $('#edit_updateBtn_submit').click();
    });

    $('#attendance_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#attendance_edit_form')[0]),
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
