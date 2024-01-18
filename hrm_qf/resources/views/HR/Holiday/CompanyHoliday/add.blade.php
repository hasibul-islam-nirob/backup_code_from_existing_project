

<?php 
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;
?>

<style>
    .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
    .checkbox_branch:hover {
        background: #3e8ef7;
        transition: 0.3s;
        color: white !important;
    }
    .text_dark {
        color: #526069;
    }
    .text_dark:hover {
        color: white !important;
        transition: 0.3s;
    }

    #checkboxes {

    }
    #checkboxes label {
        display: block;
    }

</style>

<!-- Page -->
    <form id="company_holiday_add_form" enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" >
        <div class="row">
            <div class="col-sm-9 offset-lg-3">

                <!-- Html View Load  -->
                {!! HTML::forCompanyFeild() !!}

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Holiday Title</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control " name="ch_title" id="ch_title"
                            placeholder="Enter comp Holiday Title"  
                            required data-error="Please enter comp Holiday Title.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Day</label>
                    <div class="col-sm-5 form-group">
                        <div class="row" id="holodayDays">&nbsp;</div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Effective Date</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group ghdatepicker">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="icon wb-calendar " aria-hidden="true"></i>
                                </span>
                            </div>
                            <input type="text"class="form-control datepicker-custom common_effective_date" onchange="fnCheckDayEnd();" id="ch_eff_date" name="ch_eff_date" placeholder="DD-MM-YYYY"
                            required data-error="Please Select Date" autocomplete="off">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title" >Description</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <textarea class="form-control " id="ch_description" name="ch_description" 
                                rows="2" placeholder="Enter Description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Branch</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">

                            <select class="form-control clsSelect2" style="width: 100%" id="branch_id" name="branch_id" onchange="showCheckboxes();">
                                <option value="0">All</option>
                                <option value="1">Head Office</option>
                                <option value="-1">Branches</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 form-group">
            <div class="form-row align-items-center" id="checkboxes" style="display: none;">
                <div class="row" id="BranchData">&nbsp;</div>
            </div>
        </div>

        <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
    </form>

<script type="text/javascript">
    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../getData", 'get', {context:"weekDayBranchData"}, 
            function(response, textStatus, xhr) {
                showDaysData(response.days)
                showBranchData(response.branchData)
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );

    });

    function showBranchData(response){

        let checkbox = "";
        $.each(response, function(index, item) {
            checkbox += "<div class='col-sm-2'>"+
                    "<div class='input-group checkbox-custom checkbox-primary'>"+
                        "<input type='checkbox' name='branch_array[]' id='branch_array_" + index + "' value='" + index + "'>"+
                        "<label for='branch_array_" + index + "'>" + item + "</label>"+
                    "</div>"+
                "</div>";
        });

        $('#BranchData').html(checkbox);
    }

    function showDaysData(response){

        let checkbox = "";
        $.each(response, function(index, item) {

            checkbox += "<div class='col-sm-4'>"+
                " <div class='input-group checkbox-custom checkbox-primary'>"+
                    "<input type='checkbox' name='ch_day[]' id='ch_day_" + index + "' value='" + index + "'>"+
                    "<label for='ch_day_" + index + "'>" + item + "</label>"+
                "</div>"+
            "</div>";
        });

        $('#holodayDays').html(checkbox);
    }


    showModal({
        titleContent: "Add Company Holiday",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'add_saveBtn',
            }
        }),
    });


    function showCheckboxes() {

        $('#checkboxes').hide();

        if($("#branch_id").val() == "-1"){

            $('#checkboxes').show();
        } 
    }


    
    $('#add_saveBtn').click(function(event) {
        $('#add_saveBtn_submit').click();
    });


    $('#company_holiday_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#company_holiday_add_form')[
                0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });

//=============================================
 
//check Day End value on database in real time

function fnCheckDayEnd() {
            var startDateFrom = $('#ch_eff_date').val();
            // console.log(startDateFrom);

            if (startDateFrom != '') {
                $.ajax({
                    type: "get",
                    url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                    data: {
                        startDateFrom: startDateFrom,
                    },
                    dataType: "json",
                    success: function (data) {

                        if (data.Table == "DayEnd") {
                            swal({
                                title: "Day end exist, please select another day !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }else if(data.Table == "emptydata"){
                            swal({
                                title: "Date Empty, please select a date !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }
                    }
                });

            }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
       var startDateFrom = $('#ch_eff_date').val();
       // console.log(startDateFrom);

       if (startDateFrom != '') {
           $.ajax({
               type: "get",
               url: "{{url('gnl/compholiday/CheckDayEnd')}}",
               data: {
                   startDateFrom: startDateFrom,
               },
               dataType: "json",
               success: function (data) {

                   if (data.Table == "DayEnd") {
                       swal({
                           title: "Day end exist, please select another day !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }else if(data.Table == "emptydata"){
                       swal({
                           title: "Date Empty, please select a date !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }
               }
           });

       }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('hr/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    }


/*
$('.modal-dialog').addClass('w-75');

    function fnAllBranch() {

        if ($('#branch_array_0').is(':checked')) {
            $('#branch_modal_0 input:checkbox').each(function() {
                $(this).prop("checked", true);
            });
        } else {
            $('#branch_modal_0 input:checkbox').each(function() {
                $(this).prop("checked", false);
            });
        }
    }

    function fnBranch() {

        var flag = true;
        $('.branch_cls_0').each(function() {
            if ($(this).is(':checked') == false) {
                flag = false;
            }
        });

        if (flag) {
            $('#branch_array_0').prop("checked", true);
        } else {
            $('#branch_array_0').prop("checked", false);
        }
    }
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });

     //check Day End value on database in real time

    function fnCheckDayEnd() {
            var startDateFrom = $('#ch_eff_date').val();
            // console.log(startDateFrom);

            if (startDateFrom != '') {
                $.ajax({
                    type: "get",
                    url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                    data: {
                        startDateFrom: startDateFrom,
                    },
                    dataType: "json",
                    success: function (data) {

                        if (data.Table == "DayEnd") {
                            swal({
                                title: "Day end exist, please select another day !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }else if(data.Table == "emptydata"){
                            swal({
                                title: "Date Empty, please select a date !!",
                                icon: "error",
                            });

                            $('#ch_eff_date').val('');
                        }
                    }
                });

            }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
       var startDateFrom = $('#ch_eff_date').val();
       // console.log(startDateFrom);

       if (startDateFrom != '') {
           $.ajax({
               type: "get",
               url: "{{url('gnl/compholiday/CheckDayEnd')}}",
               data: {
                   startDateFrom: startDateFrom,
               },
               dataType: "json",
               success: function (data) {

                   if (data.Table == "DayEnd") {
                       swal({
                           title: "Day end exist, please select another day !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }else if(data.Table == "emptydata"){
                       swal({
                           title: "Date Empty, please select a date !!",
                           icon: "error",
                       });

                       $('#ch_eff_date').val('');
                   }
               }
           });

       }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('gnl/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    } //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#ch_eff_date').val();
        // console.log(startDateFrom);

        if (startDateFrom != '') {
            $.ajax({
                type: "get",
                url: "{{url('hr/compholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }else if(data.Table == "emptydata"){
                        swal({
                            title: "Date Empty, please select a date !!",
                            icon: "error",
                        });

                        $('#ch_eff_date').val('');
                    }
                }
            });

        }
    }

*/
</script>


