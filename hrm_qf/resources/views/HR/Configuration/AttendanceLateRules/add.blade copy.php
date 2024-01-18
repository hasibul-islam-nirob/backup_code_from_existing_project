<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<style>
    #ui-datepicker-div{
        z-index: 100000 !important;
    }
</style>

<form id="attendance_late_rules_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            
            <div class="row d-none">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Duty Start Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" disabled  id="start_time" name="start_time" class="form-control" 
                            placeholder="H:M" style="width: 100%;" autocomplete="off">
                    </div>
                </div>
            </div>

           
            

            <div class="row">
                <div class="col-sm-7 offset-sm-3 form-group">
                    <label class="input-title "> Late Bypass</label>
                    <div class="input-group flex-nowrap">
                        {!! HTML::forDesignationFieldHr('designation_id', 'attendance_bypass[]') !!}
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-7 offset-sm-3 form-group">
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

                                <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                                    <span class="addBtnClass">
                                        <a class="" id="addLatePresentFieldId">
                                            <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                        </a>
                                    </span>
                                </div>
    
                            </div>
        
                            <div class="row" id="inc_div_row">

                                
                            </div>
        
                        </div>
                        
                    </div>
                </div>
            </div>

            
            
            <div class="row">
                <div class="col-sm-7 offset-sm-3 form-group">
                    <label class="input-title RequiredStar"> Late Deduction </label>
                    <div class="input-group flex-nowrap">
                        <button class="btn btn-block btn-info" id="actionForLpBtn" >+</button>
                    </div>

                    <div class="" id="AddActionForLpDiv"></div>

                    {{-- <ul class="list-group" id="AddActionForLpDiv">
                        <li class="lpDivList list-group-item my-1" >Sortable-1</li>
                        <li class="lpDivList list-group-item my-1" >Sortable-2</li>
                        <li class="lpDivList list-group-item my-1" >Sortable-3</li>
                        <li class="lpDivList list-group-item my-1" >Sortable-4</li>
                        <li class="lpDivList list-group-item my-1" >Sortable-5</li>
                    </ul> --}}

                </div>
            </div>

            
            <div class="row">
                <div class="col-sm-7 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" id="eff_date_start" name="eff_date_start" class="form-control datepicker-custom"
                            placeholder="DD-MM-YYYY" autocomplete="off">
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

    $("#actionForLpBtn").on('click', function(event){
        event.preventDefault();

        $("#AddActionForLpDiv").append(" <span class='row my-1 ml-1'>"+
                "<input type='text' class='col-10 form-control sortable' name='late_deduction[]'> "+
                "<i onclick='removeLpDiv(this)' class='col-2 fa fa-minus-circle' style='color: #bb1111;'></i>"+
                "</span>");

    });

    // $(function() {
    //     $( "#AddActionForLpDiv" ).sortable();
    //     $( "#AddActionForLpDiv" ).disableSelection();
    // });

    $(document).ready(function(){
        $("#designation_id").attr('multiple', true)

        window.attData = [];
        window.flag = 0;

        $('#start_time, #end_time, #ext_start_time').timepicker();

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

    });

    showModal({
        titleContent: "Add Attendance Late Rules",
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

        let formData = new FormData($('#attendance_late_rules_form')[0]);

        callApi("{{ url()->current() }}/../insert/send/api", 'post', formData,
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


    // function addIncRow(){
    //     // $('#inc_div').find('.addBtnClass')
    //     // .html(
    //     //     '<a onclick="removeRow(this)" class="">' +
    //     //         '<i class="fa fa-minus-circle" style="color: red;"></i>' +
    //     //     '</a>'
    //     // )

    //     // $('#inc_div_row').clone().find('.addBtnClass')
    //     // .html(
    //     //     '<a onclick="addIncRow(this)" class="">' +
    //     //         '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
    //     //     '</a>'
    //     // ).end()
    //     // .find("input").val("").end()
    //     // .appendTo('#inc_div');


    //     let inputField = '<div class="col-sm-5 text-center" style="border: 1px solid #fff; padding: 0;">'+'
    //                                 <div class="input-group">'+'
    //                                     <input type="number" name="late[]" style="width: 100%;" class="form-control">'+'
    //                                 </div>'+'
    //                             </div>'+'
    //                             <div class="col-sm-5 text-center" style="border: 1px solid #fff; padding: 0;">'+'
    //                                 <div class="input-group">'+'
    //                                     <input type="number" name="leave[]" style="width: 100%;" class="form-control">'+'
    //                                 </div>'+'
    //                             </div>'+'
    //                             <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">'+'
    //                                 <span class="addBtnClass">'+'
    //                                     <a onclick="addIncRow(this)" class="">'+'
    //                                         <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>'+'
    //                                     </a>'+'
    //                                 </span>'+'
    //                             </div>';
        
    //     $('#inc_div_row').appendTo(inputField);


    // }

    $("#addLatePresentFieldId").on('click', function(event){
        event.preventDefault();
        $("#inc_div_row").append('<span class="addBtnClass row col-12 ">'+
                                    '<div class="col-sm-5 ml-0 mr-1 text-center" style="border: 1px solid #fff; padding: 0;">'+
                                        '<div class="input-group">'+
                                            '<input type="number" name="late[]" style="width: 100%;" class="form-control">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-sm-5 ml-2 mr-2 text-center" style="border: 1px solid #fff; padding: 0;">'+
                                        '<div class="input-group">'+
                                            '<input type="number" name="leave[]" style="width: 100%;" class="form-control">'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-sm-1 ml-2 text-center" style="border: 1px solid #fff; padding: 0;">'+
                                    
                                        '<a onclick="removeRow(this)" class="">'+
                                            '<i class="fa fa-minus-circle" style="color: red;"></i>'+
                                        '</a>'+
                                    '</div>'+
                                '</span>')
    })

    function removeRow(node){
        $(node).parent().parent().remove();
    }

    function removeLpDiv(node){
        $(node).parent().remove();
    }

</script>
