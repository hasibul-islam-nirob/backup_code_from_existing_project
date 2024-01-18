<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<style>
    #ui-datepicker-div{
        z-index: 100000 !important;
    }
</style>

<form id="attendance_rules_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden id="attendance_rule_id" name="attendance_rule_id">
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Duty Start Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text"  id="start_time" name="start_time" class="start_time form-control" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off" disabled>

                        <input type="text"  id="" name="start_time" class="start_time form-control d-none" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Duty End Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" id="end_time" name="end_time" class="end_time form-control" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off" disabled>

                        <input type="text" id="" name="end_time" class="end_time form-control d-none" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group d-none">
                    <label class="input-title">Extendent Entry Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text"  id="ext_start_time"name="ext_start_time"  class="form-control" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Late Accepted (<small>Minutes</small>) </label>
                    <div class="input-group flex-nowrap">
                        <input type="text"  id="late_accept_minute"name="late_accept_minute"  class="form-control" 
                            placeholder="Example 15 Minutes" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Early Accepted (<small>Minutes</small>)</label>
                    <div class="input-group flex-nowrap">
                        <input type="text"  id="early_accept_minute"name="early_accept_minute"  class="form-control" 
                            placeholder="Example 15 Minutes" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Over Time Cycle (<small>Minutes</small>)</label>
                    <div class="input-group flex-nowrap">
                        <input type="text"  id="ot_cycle_minute"name="ot_cycle_minute"  class="form-control" 
                            placeholder="Example 60 Minutes" style="width: 100%;" autocomplete="off" readonly>
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Late Present Accepted </label>
                    <div class="input-group flex-nowrap">

                        <div class="col-sm-12" id="inc_div">

                            <div class="row">
        
                                <div class="col-sm-5 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                                    <label style="color:#fff; font-size: 12px;">Late</label>
                                </div>
        
                                <div class="col-sm-5 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                                    <label style="color:#fff; font-size: 12px;">Leave</label>
                                </div>
                            </div>
        
                            <div class="row" id="inc_div_row">
        
                                <div class="col-sm-5 text-center" style="border: 1px solid #fff; padding: 0;">
                                    <div class="input-group">
                                        <input type="number" name="late[]" style="width: 100%;" class="form-control">
                                    </div>
                                </div>
        
                                <div class="col-sm-5 text-center" style="border: 1px solid #fff; padding: 0;">
                                    <div class="input-group">
                                        <input type="number" name="leave[]" style="width: 100%;" class="form-control">
                                    </div>
                                </div>
        
                                <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                                    <span class="addBtnClass">
                                        <a onclick="addIncRow(this)" class="">
                                            <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                        </a>
                                    </span>
                                </div>
                                
                            </div>
        
                        </div>
                        
                    </div>
                </div>
            </div> --}}

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Attendance Bypass (<small>Designation</small>)</label>
                    <div class="input-group flex-nowrap">
                        {!! HTML::forDesignationFieldHr('designation_id', 'attendance_bypass[]') !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Late Bypass(<small>Designation</small>)</label>
                    <div class="input-group flex-nowrap">
                        {!! HTML::forDesignationFieldHr('latebypass_designation_id', 'late_bypass[]') !!}
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Late Present Accepted </label>
                    <div class="input-group flex-nowrap">
                        <input type="text"  id="lp_accept"name="lp_accept"  class="form-control" 
                            placeholder="3 Days" style="width: 100%;" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Acction For Late Present </label>
                    <div class="input-group flex-nowrap">
                        <select name="acction_for_lp" id="acction_for_lp" class="form-control">
                            <option value="AL">AL</option>
                            <option value="LWP">LWP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Leave Accepted Each Month </label>
                    <div class="input-group flex-nowrap">
                        <input type="text"  id="leave_allow"name="leave_allow"  class="form-control" 
                            placeholder="2" style="width: 100%;" autocomplete="off">
                    </div>
                </div>
            </div> --}}

            <div class="row">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" id="eff_date_start" name="eff_date_start" class="form-control datepicker-custom common_effective_date"
                            placeholder="DD-MM-YYYY" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" type="text/css" href="{{asset('assets/css-js/timepicker-master/timepicker.css')}}">
<script src="{{asset('assets/css-js/timepicker-master/timepicker.js')}}"></script>
{{-- <script src="http://192.168.68.113/erp_modules/hrm/dev/hrm_d2/public/assets/js/custom-js.js" defer></script> --}}

<script>

    // $("#late_accept_minute").on('focus', function(){
    //     let startTime = $("#start_time").val();
    //     let endTime = $("#end_time").val();

    //     if ( (Date("h:i a", startTime)) > (Date("h:i a", endTime))) {
    //         $("#end_time").val('');
    //         $('#save').addClass("disabled")

    //         swal({
    //             icon: 'warning',
    //             title: 'Oops...',
    //             text: 'End date is less then start date..'
    //         });
            
    //     }else{
    //         $('#save').removeClass("disabled")
    //     }

    //     console.log('ddd');
    //     console.log( (Date("h:i a", startTime)) );
    //     console.log((Date("h:i a", endTime)));
        
    // })

    $(document).ready(function(){
        $("#designation_id").attr('multiple', true)
        $("#latebypass_designation_id").attr('multiple', true);
        window.attData = [];
        window.flag = 0;

        $('#start_time, #end_time, #ext_start_time').timepicker();

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#attendance_rules_form')[0]),
            function(response, textStatus, xhr) {
                console.log(response);
                
                $('#attendance_rule_id').val("{{ $id }}");
                $('.start_time').val(response.start_time);
                $('.end_time').val(response.end_time);
                // $('#ext_start_time').val(response.ext_start_time);

                $('#late_accept_minute').val(response.late_accept_minute);
                $('#early_accept_minute').val(response.early_accept_minute);
                $('#ot_cycle_minute').val(response.ot_cycle_minute);

                // $('#leave_allow').val(response.leave_allow);
                // $('#lp_accept').val(response.lp_accept);
                // $('#acction_for_lp').val(response.acction_for_lp);

                $('#eff_date_start').val(response.eff_date_start);

                if (response.attendance_bypass != null) {
                    let attBypassArr = response.attendance_bypass.split(',')
                    $("#designation_id").val(attBypassArr);
                    $("#designation_id").trigger('change');
                }else{
                    let attBypassArr = [];
                }

                if (response.late_bypass != null) {
                    let lateBypassArr = response.late_bypass.split(',')
                    $("#latebypass_designation_id").val(lateBypassArr);
                    $("#latebypass_designation_id").trigger('change');
                }else{
                    let lateBypassArr = [];
                }
                
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

    

    showModal({
        titleContent: "Update Attendance Rules",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save'
            },
            'btnName': {
                0: 'Save'
            },
            'btnId': {
                0: 'save'
            }
        }),
    });

    $('#save').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#attendance_rules_form')[0]);

        callApi("{{ url()->current() }}/../../update/send/api", 'post', formData,
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                // hideModal();
                ajaxDataLoad();

                if(response.if_exist_start_date) {

                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: 'Same Date Rule Already Exist!',
                    });
                } else {
                    hideModal();
                }
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });


    function addIncRow(){
        $('#inc_div').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#inc_div_row').clone().find('.addBtnClass')
        .html(
            '<a onclick="addIncRow(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#inc_div');
    }

    function removeRow(node){
        $(node).parent().parent().parent().remove();
    }

</script>
