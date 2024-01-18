
<?php
use App\Services\CommonService as Common;
?>

<style>
    .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important;
     }
</style>
<!-- Page -->
<form id="special_holiday_add_form" enctype="multipart/form-data" method="post" class="form-horizontal">

    <div class="row">
        <div class="col-sm-9 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Applicable For</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="radio-custom radio-primary">
                            <input type="radio" class="ApplicationFor" id="sh_app_for_org" checked="checked" name="sh_app_for" value="org">
                            <label for="sh_app_for_org">Organization &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary">
                            <input type="radio" class="ApplicationFor" id="sh_app_for_br" name="sh_app_for" value="branch">
                            <label for="sh_app_for_br">Branch &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary" style="display: none">
                            <input type="radio" class="ApplicationFor" id="sh_app_for_sm" name="sh_app_for" value="somity">
                            <label for="sh_app_for_sm">Somity &nbsp &nbsp </label>
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center desc" id="org" style="display: none;">
                <label class="col-sm-3 input-title RequiredStar">Organization</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="company_id" id="company_id" style="width: 100%">

                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center desc" id="branch" style="display: none;">
                <label for="branch_id" class="col-sm-3 input-title RequiredStar">Branch</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="branch_id" id="branch_id" style="width: 100%">
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center desc" id="somity" style="display: none;">
                <label class="col-sm-3 input-title RequiredStar">Somity</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="somity_id" id="somity_id">
                            <option value="" selected="selected">Select Somity</option>

                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control " placeholder="Enter Holiday Title" name="sh_title"
                            id="sh_title" required data-error="Please enter Holiday Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title RequiredStar">Holiday Date From</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control datepickerNotRange datepicker-custom limitDateStart date-picker-year" id="sh_date_from"
                            name="sh_date_from" placeholder="DD-MM-YYYY" required data-error="Select Date"
                            onchange="fnCheckDayEnd();" >
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title RequiredStar">Holiday Date To</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control datepickerNotRange datepicker-custom limitDateEnd date-picker-year" id="sh_date_to"
                            name="sh_date_to" placeholder="DD-MM-YYYY" required data-error="Select Date"
                            onchange="fnCheckDayEnd();" >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title">Description</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control " id="sh_description" name="sh_description" rows="2"
                            placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>
<!-- End Page -->

<script type="text/javascript">

    $(".date-picker-year").datepicker({
        dateFormat: 'dd-mm-yy',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1990:+5',
        reverseYearRange: true,
        onClose: function(dateText, inst) {
            // var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            // var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            // $(this).val($.datepicker.formatDate('dd-mm-yy', new Date(1, month, year)));
        }
    });

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../getData", 'get', {context:"CompanyDataBranchData"},
            function(response, textStatus, xhr) {

                // console.log(response);

                showCompanyData(response.companyData)
                showBranchData(response.branchData)

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );

    });


    function showCompanyData(response){

        let CompanyOption = "<option value='' selected disabled > Select Organization </option>";

        $.each(response, function(index, item) {
            CompanyOption += "<option value='"+ index +"' >"+ item +"</option>";
        });

        $('#company_id').html(CompanyOption);
    }


    function showBranchData(response){

        let BranchOption = "<option value='' selected disabled > Select Branch </option>";

        $.each(response, function(index, item) {
            BranchOption += "<option value='"+ index +"' >"+ item +"</option>";
        });

        $('#branch_id').html(BranchOption);
    }



    showModal({
        titleContent: "Add Special Holiday",
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


    $('#add_saveBtn').click(function(event) {
        $('#add_saveBtn_submit').click();
    });


    $('#special_holiday_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#special_holiday_add_form')[0]),
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

    $(".ApplicationFor").click(function () {

        console.log($(this).val());

        if($(this).val() == "org"){
            $("#company_id").attr('required', true);
            $("#branch_id").attr('required', false);
            $("#somity_id").attr('required', false);
        }
        else if($(this).val() == "branch"){
            $("#branch_id").attr('required', true);
            $("#company_id").attr('required', false);
            $("#somity_id").attr('required', false);
        }
        else if($(this).val() == "somity"){
            $("#somity_id").attr('required', true);
            $("#company_id").attr('required', false);
            $("#branch_id").attr('required', false);
        }

    });



    // Change views of dropdown box(Org,branch,Samity)
    $(document).ready(function () {

        if ($('.ApplicationFor').is(':checked')) {
            var idTxt = $('.ApplicationFor:checked').val();
            $('#' + idTxt).show();

            if(idTxt == "org"){
                $("#company_id").attr('required', true);
                $("#branch_id").attr('required', false);
                $("#somity_id").attr('required', false);
            }
            else if(idTxt == "branch"){
                $("#branch_id").attr('required', true);
                $("#company_id").attr('required', false);
                $("#somity_id").attr('required', false);
            }
            else if(idTxt == "somity"){
                $("#somity_id").attr('required', true);
                $("#company_id").attr('required', false);
                $("#branch_id").attr('required', false);
            }
        }

        $(".ApplicationFor").click(function () {
            var selIdTxt = $(this).val();

            $('.ApplicationFor').each(function () {

                if (selIdTxt === $(this).val()) {
                    $("#" + $(this).val()).show();
                } else {
                    $("#" + $(this).val()).hide();
                }
            });
        });

        $('form').submit(function (event) {
            $(this).find(':submit').attr('disabled', 'disabled');
        });
    });
    //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#sh_date_from').val();
        var startDateTo = $('#sh_date_to').val();
        var branchId = $('#branch_id').val();

        if (startDateFrom != '' && startDateTo != '') {
            $.ajax({
                type: "get",
                url: "{{url('hr/specialholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                    startDateTo: startDateTo,
                    branchId: branchId
                },
                dataType: "json",
                success: function (data) {

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        $('#sh_date_from').val('');
                        $('#sh_date_to').val('');

                    } else if (data.Table == "Holiday") {
                        swal({
                            title: "Holiday exist, please select another day !!",
                            icon: "error",
                        });

                        $('#sh_date_from').val('');
                        $('#sh_date_to').val('');
                    }
                }
            });
        }
    }

</script>
