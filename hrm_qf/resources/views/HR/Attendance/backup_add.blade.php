<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>
<style>
    .testt {
        z-index: 99999!important;
    }
</style>
<form id="attendance_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">
                <div class="col-sm-4 form-group">
                    {!! HTML::forBranchFeildNew(true) !!}

                    {!! HTML::forCompanyFeild() !!}

                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('department_id') !!}
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Designation</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('designation_id') !!}
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="employee_id" class="form-control" style="width: 100%">

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

            {{-- <div class="row d-flex justify-content-center">
                <div class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Time & Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_time_and_date" name="time_and_date" type="text"
                            style="z-index:99999 !important;" class="form-control round datepicker"
                            placeholder="DD-MM-YYYY">
                    </div>
                </div>

                <div class="col-sm-2 offset-sm-3 form-group">
                    <label class="input-title">Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text"  id="ext_start_time" name="ext_start_time"  class="form-control" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off">
                    </div>
                </div>
            </div> --}}

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
                            class="form-control datepicker-custom" placeholder="DD-MM-YYYY">

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
                            class="form-control" placeholder="H:M">

                    </div>
                </div>

            </div>

        </div>

    </div>

</form>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css-js/timepicker-master/timepicker.css')}}">
<script src="{{asset('assets/css-js/timepicker-master/timepicker.js')}}"></script>

<script>

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
                1: 'addBtn',
            }
        }),
    });

    $(document).ready(function() {
        $('#emp_branch_id').trigger('change');

        /* $('#time_picker').timepicker({
            timeFormat: 'h:mm p',
            interval: 1,
            minTime: '10',
            maxTime: '6:00pm',
            defaultTime: '11',
            startTime: '10:00',
            dynamic: true,
            dropdown: true,
            scrollbar: true
        }); */

        /* $('#time_picker').timepicker({
            className: 'testt'
        }); */

        // $("body").delegate("#time_picker", "focusin", function(){
        //     $(this).timepicker();
        // });

    });

    $("#branch_id").on('change', function(){
        console.log( $("#branch_id").val() );
    })

    $("#department_id").on('change', function(){
        console.log( $("#department_id").val() );
    })

    $("#designation_id").on('change', function(){
        console.log( $("#designation_id").val() );
    })

    
   

    $('#start_time, #end_time, #ext_start_time').timepicker();
    
    $('#emp_designation_id, #emp_department_id, #emp_branch_id').change(function(e) {

        let data = new FormData();
        data.append('branch_id', $('#emp_branch_id').val());
        data.append('department_id', $('#emp_department_id').val());
        data.append('designation_id', $('#emp_designation_id').val());

        callApi("{{ route('searchEmployeeAndGetOptions') }}", 'post', data,
            function(response, textStatus, xhr) {
                $('#add_employee_id').val(null).trigger('change');
                $('#add_employee_id').select2({
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
            }
        );
    });

    $('#addBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#attendance_add_form')[
                0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                //hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>
