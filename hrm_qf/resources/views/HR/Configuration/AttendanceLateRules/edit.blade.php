<?php
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;

$leaveData = HRS::queryGetLeaveCategoryDetails();
// dd($leaveData);
$loginUserInfo = Auth::user();
?>

<style>
    #ui-datepicker-div {
        z-index: 100000 !important;
    }
</style>

<form id="attendance_late_rules_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden id="attendance_late_rule_id" name="attendance_late_rule_id" value="">
    <div class="row">
        <div class="col-sm-10 offset-sm-2">

            <div class="row d-none">
                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Duty Start Time</label>
                    <div class="input-group flex-nowrap">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" disabled id="start_time" name="start_time" class="form-control"
                            placeholder="H:M" style="width: 100%;" autocomplete="off">
                    </div>
                </div>
            </div>




            {{-- <div class="row form-group d-none">
                <label class="input-title col-sm-7 offset-sm-1" style=""> Late Bypass (<small>Designation</small>)</label>
                <div class="col-sm-7 offset-sm-1 input-group flex-nowrap">
                    {!! HTML::forDesignationFieldHr('designation_id', 'attendance_bypass[]') !!}
                </div>
            </div> --}}



            <div class="row form-group">
                <label class="input-title col-sm-7 offset-sm-1" style=""> Late Present Breakdown
                </label>
                <div class="col-sm-7 offset-sm-1 ">
                    <div class="input-group flex-nowrap">

                        <table class="table table-bordered">
                            <thead>

                                <tr>
                                    <th width="5%">SL</th>
                                    <th width="60%">Total Late Present</th>
                                    <th width="25%">Leave Deduct</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody id="LpBreakdownTable">

                                <tr>
                                    <td style="text-align: center;">  </td>
                                    <td>
                                        <span id="lpbTitle">For remaining next </span>  <input id="dayInput1" type="text" value="" style="border:0px; float: right; width: 5rem; margin-right: 1rem;">
                                    </td>

                                    <td>
                                        <input id="ldInput1" type="text" value="" style="border:0px; float: right; width: 5rem; margin-right: 1rem;">
                                    </td>
                                    <td class="" style="text-align: center;">
                                        <a class="createLpRowIcon"><i class="fa fa-plus-circle"style="color: rgb(139, 137, 137);"></i></a>
                                    </td>
                                </tr>

                                <textarea name="lp_breakdown" id="setJsonStringLPB" class="d-none" cols="30" rows="10"></textarea>

                            </tbody>
                        </table>



                    </div>
                </div>
            </div>



            <div class="row form-group">
                <label class="input-title col-sm-7 offset-sm-1 " style=""> Late Deduction </label>
                <div class="col-sm-7 offset-sm-1 flex-nowrap">

                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <th width="10%">SL</th>
                                <th width="50%">Deduct From</th>
                                <th width="30%">Number</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="ActionForLpTable">

                            <tr>
                                <td style="text-align: center;">  </td>
                                <td>
                                    <select name="" id="lpInput1" style="border:0px; float: right; width: 8rem; margin-right: 3rem; height: 20px; ">
                                        <option value="LWP"> LWP </option>
                                        @foreach ($leaveData as $item)
                                        <option value="{{$item->short_form}}"> {{$item->short_form}} </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input id="lpNumInput1" type="text" value="" style="border:0px; float: right; width: 5rem; margin-right: 1rem;  text-align: center;">
                                </td>
                                <td class="" style="text-align: center;">
                                    <a class="AddActionForLpBtn"><i class="fa fa-plus-circle"style="color: rgb(139, 137, 137);"></i></a>
                                </td>
                            </tr>

                            <textarea name="lp_deduction" id="setJsonActionForLpString" class="d-none" cols="30" rows="10"></textarea>

                        </tbody>
                    </table>



                </div>
            </div>


            <div class="row form-group">
                <label class="input-title col-sm-7 offset-sm-1 RequiredStar" style="">Effective Date</label>
                <div class="col-sm-7 offset-sm-1 ">
                    <div class="input-group-prepend ">
                        <span class="input-group-text ">
                            <i class="icon wb-calendar" aria-hidden="true"></i>
                        </span>
                        <input type="text" id="eff_date_start" name="eff_date_start"
                            class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css-js/timepicker-master/timepicker.css') }}">
<script src="{{ asset('assets/css-js/timepicker-master/timepicker.js') }}"></script>

<script>

//===================================================
    var dataLPBArray = [];
    $(".createLpRowIcon").on('click', function(event){
        event.preventDefault();
        // var dataLPBArray = [];

        let dayInput = $("#dayInput1").val();
        let ldInput = $("#ldInput1").val();

        let trCount = $('#LpBreakdownTable tr').length;
        if(trCount < 1){
            $("#lpbTitle").html("From 0 to  ");
        }
        let text1 = "From 0 to  ";
        if (trCount > 1) {
            text1 = "For remaining next ";
        }
        
        if (dayInput == '') {
            dayInput = 0;
        }
        if (ldInput == '') {
            ldInput = 0;
        }
        
        $("#LpBreakdownTable").append(
            '<tr>'+
                '<td style="text-align: center;"> '+trCount+' </td>'+
                '<td> '+text1+' <input id="lpDay'+trCount+'" type="text" name="lateBreakdownName[]" value="'+dayInput+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem; text-align: center;" ></td>'+
                '<td><input id="ld'+trCount+'" type="text" name="lateBreakdownNum[]" value="'+ldInput+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem; text-align: center;"></td>'+
                '<td class="" style="text-align: center;">'+
                    '<a onclick="removeLpBreakdownTr(this)" class="createLpRowIcon'+trCount+'"><i class="fa fa-minus-circle" style="color: red;"></i></a>'+
                '</td>'+
            '</tr>'
            )

        if (1) {
            let item = {};
            item[dayInput] = ldInput;
            dataLPBArray.push(item);
            let lpbJsonData = JSON.stringify(dataLPBArray);

            let LpbData = $("#setJsonStringLPB").html();

            const mergedArray = lpbJsonData.concat(LpbData);

            // console.log("Marege"+mergedArray);
            $("#setJsonStringLPB").html(mergedArray);


        }
        // alert(trCount)
        $("#dayInput1").val('');
        $("#ldInput1").val('');
    })
    

    function removeLpBreakdownTr(node){
        $(node).parent().parent().remove();

        let trCount = $('#LpBreakdownTable tr').length;
        if(trCount < 1){
            $("#lpbTitle").html("From 0 to ");
        }else{
            $("#lpbTitle").html("For remaining next ");
        }
    }

    var dataActionForLpArray = [];
    $(".AddActionForLpBtn").on('click', function(event) {
        event.preventDefault();

        let dayInput = $("#lpInput1").val();
        let ldInput = $("#lpNumInput1").val();

        if (ldInput == '') {
            ldInput = 0;
        }

        let trCount = $('#ActionForLpTable tr').length;
        let text1 = "";
        if (trCount > 1) {
            text1 = "";
        }

        $("#ActionForLpTable").append(
            '<tr>'+
                '<td style="text-align: center;"> '+trCount+' </td>'+
                '<td> '+text1+' <input id="lpInput'+trCount+'" type="text" name="lateDeductionName[]" value="'+dayInput+'" readonly style="border:0px; float: right; width: 8rem; margin-right: 3rem; height: 20px; "></td>'+
                '<td><input id="lpNum'+trCount+'" type="text" name="lateDeductionNum[]" value="'+ldInput+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem;  text-align: center;"></td>'+
                '<td class="" style="text-align: center;">'+
                    '<a onclick="removeLpBreakdownTr(this)" class="createLpRowIcon'+trCount+'"><i class="fa fa-minus-circle" style="color: red;"></i></a>'+
                '</td>'+
            '</tr>'
            )

            if (1) {
                
                let item = {};
                item[dayInput] = ldInput;
                dataActionForLpArray.push(item);
                let aflpJsonData = JSON.stringify(dataActionForLpArray);

                let afLpData = $("#setJsonActionForLpString").html();
                const mergeAflArray = afLpData.concat(aflpJsonData);
                $("#setJsonActionForLpString").html(mergeAflArray);

                // console.log(mergeAflArray);
            }

        $("#lpNumInput1").val('');

    });
    //===================================================

    $(document).ready(function(){
        $("#designation_id").attr('multiple', true)
        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#attendance_late_rules_edit_form')[0]),
            function(response, textStatus, xhr) {
                // console.log(response);
                
                $('#attendance_late_rule_id').val("{{ $id }}");
                $('#eff_date_start').val(response.eff_date_start);

                // Latebypass Start
                if (response.late_bypass != null) {
                    let lateBypassArr = response.late_bypass.split(',')
                    $("#designation_id").val(lateBypassArr);
                    $("#designation_id").trigger('change');
                }else{
                    let lateBypassArr = [0];
                }
                // Latebypass End


                // LP Breakdown start
                // console.log(response.lp_breakdown);
                if (1) {
                    const breakdownArray = JSON.parse(response.lp_breakdown);
                    $.each(breakdownArray, function(index, obj) {
                        const breakdownKey = Object.keys(obj)[0];
                        const breakdownValue = obj[breakdownKey];

                        let trCount = index + 1;
                        let text1 = "From 0 to ";
                        if (trCount > 1) {
                            text1 = "For remaining next";
                        }

                        if(trCount < 1){
                            $("#lpbTitle").html("From 0 to ");
                        }else{
                            $("#lpbTitle").html("For remaining next ");
                        }
                        
                        $("#LpBreakdownTable").append(
                        '<tr>'+
                            '<td style="text-align: center;"> '+trCount+' </td>'+
                            '<td> '+text1+' <input id="lpDay'+trCount+'" type="text"  name="lateBreakdownName[]" value="'+breakdownKey+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem; text-align: center;" ></td>'+
                            '<td><input id="ld'+trCount+'" type="text" name="lateBreakdownNum[]" value="'+breakdownValue+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem; text-align: center;"></td>'+
                            '<td class="" style="text-align: center;">'+
                                '<a onclick="removeLpBreakdownTr(this)" class="createLpRowIcon'+trCount+'"><i class="fa fa-minus-circle" style="color: red;"></i></a>'+
                            '</td>'+
                        '</tr>'
                        )
                       
                    });
                }
                // LP breakdown end

                // Leave Deduction start
                // console.log(response.lp_deduction);
                if (1) {
                    const deductionArray = JSON.parse(response.lp_deduction);
                    $.each(deductionArray, function(index, obj) {
                        const deductionKey = Object.keys(obj)[0];
                        const deductionValue = obj[deductionKey];

                        let trCount = index + 1;
                        let text1 = "";
                        if (trCount > 1) {
                            text1 = "";
                        }
                        
                        $("#ActionForLpTable").append(
                            '<tr>'+
                                '<td style="text-align: center;"> '+trCount+' </td>'+
                                '<td> '+text1+' <input id="lpInput'+trCount+'" type="text" name="lateDeductionName[]" value="'+deductionKey+'" readonly style="border:0px; float: right; width: 8rem; margin-right: 3rem; height: 20px; "></td>'+
                                '<td><input id="lpNum'+trCount+'" type="text" name="lateDeductionNum[]" value="'+deductionValue+'" readonly style="border:0px; float: right; width: 5rem; margin-right: 1rem;  text-align: center;"></td>'+
                                '<td class="" style="text-align: center;">'+
                                    '<a onclick="removeLpBreakdownTr(this)" class="createLpRowIcon'+trCount+'"><i class="fa fa-minus-circle" style="color: red;"></i></a>'+
                                '</td>'+
                            '</tr>'
                        )
                        
                    });
                }
                // Leave Deduction End
                
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

    

    showModal({
        titleContent: "Update Attendance Late Rules",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save'
            },
            'btnName': {
                0: 'Save'
            },
            'btnId': {
                0: 'update'
            }
        }),
    });

    $('#update').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#attendance_late_rules_edit_form')[0]);

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


    $("#addLatePresentFieldId").on('click', function(event) {
        event.preventDefault();
        $("#inc_div_row").append('<span class="addBtnClass row col-12 ">' +
            '<div class="col-sm-5 ml-0 mr-1 text-center" style="border: 1px solid #fff; padding: 0;">' +
            '<div class="input-group">' +
            '<input type="number" name="late[]" style="width: 100%;" class="form-control">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-5 ml-2 mr-2 text-center" style="border: 1px solid #fff; padding: 0;">' +
            '<div class="input-group">' +
            '<input type="number" name="leave[]" style="width: 100%;" class="form-control">' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-1 ml-2 text-center" style="border: 1px solid #fff; padding: 0;">' +

            '<a onclick="removeRow(this)" class="">' +
            '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>' +
            '</div>' +
            '</span>')
    })

    function removeRow(node) {
        $(node).parent().parent().remove();
    }

    function removeLpDiv(node) {
        $(node).parent().remove();
    }

</script>
