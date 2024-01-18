
<style>
    .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
</style>
<!-- Page -->
<form id="holiday_reschedule_edit_form" enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" >
 <input hidden value="" id="edit_id" name="edit_id">
 <input hidden value="" id="returnValueData" >
    <div class="row">
        <div class="col-sm-9 offset-lg-3">
            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Applicable For</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="radio-custom radio-primary">
                            <input type="radio" name="app_for" class="ApplicationFor" value="org">
                            <label for="company_id">Organization &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary">
                            <input type="radio" name="app_for" class="ApplicationFor" value="branch">
                            <label for="branch_id">Branch &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary" style="display: none">
                            <input type="radio" name="app_for" class="ApplicationFor" value="somity">
                            <label for="somity_id">Somity &nbsp &nbsp </label>
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            
            <div class="form-row align-items-center desc" id="org" style="display: none;">
                <label for="company_id" class="col-sm-3 input-title RequiredStar">Organization</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="company_id" id="company_id" required
                            data-error="Select Organization" style="width: 100%">
                            
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center desc" id="branch" style="display: none;">
                <label class="col-sm-3 input-title RequiredStar">Branch</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="branch_id" id="branch_id"
                            data-error="Select Branch" style="width: 100%">
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center desc" id="somity" style="display: none;">
                <label class="col-sm-3 input-title RequiredStar">Somity</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="somity_id" id="somity_id"
                            data-error="Select Somity">
                            <option value="" selected="selected">Select Somity</option>

                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control " placeholder="Enter Reschedule Holiday Title" name="title"
                            id="title" value="">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title RequiredStar">Off-Day Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control date-picker-year datepicker datepicker-custom " id="working_date" name="working_date"
                            placeholder="DD-MM-YYYY" value="" required data-error="Select Date" onchange="fnCheckDayEnd()"> 
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title RequiredStar">Working-Day Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control date-picker-year datepicker datepicker-custom " id="reschedule_date" name="reschedule_date"
                            placeholder="DD-MM-YYYY" value="" required data-error="Select Date" onchange="fnCheckDayEnd()" >
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title">Description</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control " id="description" name="description"rows="2"></textarea>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
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

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#holiday_reschedule_edit_form')[0]),
            function(response, textStatus, xhr) {

                // console.log(response);

                showCompanyData(response.companyData, response.getData.branch_id, response.getData.app_for)
                showBranchData(response.branchData, response.getData.branch_id, response.getData.app_for)

                let flagCheck = response.getData.app_for;
                if(flagCheck == "org"){
                    $("#org").show();
                    $("input[name=app_for][value=org]").attr('checked', 'checked');

                }else if(flagCheck == "branch"){
                    $("#branch").show();
                   $("input[name=app_for][value=branch]").attr('checked', 'checked');
                   
                }
                $('input:radio[name=app_for]').trigger("change");

                
                $('#edit_id').val("{{ $id }}");
                $('#title').val(response.getData.title);
                $('#working_date').val(response.getData.working_date);
                $('#reschedule_date').val(response.getData.reschedule_date);
                $('#description').val(response.getData.description);
                $('#company_id').val(response.getData.company_id);

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    });



    function showCompanyData(response, BranchID, applicationType){

        let CompanyOption = "<option value='' selected disabled > Select Organization </option>";
        $.each(response, function(index, item) {
            CompanyOption += "<option value='"+ index +"' >"+ item +"</option>";
        });
        $('#company_id').html(CompanyOption);

        if(applicationType == "org"){
            $.each(response, function(index, item) {

                if( index == BranchID){
                    $("#company_id").val(index);
                }

            });
        }

    }


    function showBranchData(response, BranchID, applicationType){

        let BranchOption = "<option value='' selected disabled > Select Branch </option>";
        $.each(response, function(index, item) {
            BranchOption += "<option value='"+ index +"' >"+ item +"</option>";
        });
        $('#branch_id').html(BranchOption);

        if(applicationType == "branch"){
            $.each(response, function(index, item) {

                if( index == BranchID){
                    $("#branch_id").val(index);
                }

            });
        }
    }


    showModal({
        titleContent: "Edit Reschedule Holiday",
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



    $('#edit_updateBtn').click(function(event) {

        fnCheckDayEnd();
        let tempData = $("#returnValueData").val();

        if(tempData == '0' ){
            $('#edit_updateBtn_submit').click();
        }
    });

    $('#holiday_reschedule_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#holiday_reschedule_edit_form')[0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });


    // Change views of dropdown box(Org,branch,Samity)
    $(document).ready(function () {

        if ($('.ApplicationFor').is(':checked')) {
            var idTxt = $('.ApplicationFor:checked').val();
            $('#' + idTxt).show();
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
            // event.preventDefault();
            $(this).find(':submit').attr('disabled', 'disabled');
            // $(this).submit();
        });
    });

    //check Day End value on database in real time

    function fnCheckDayEnd() {
        var startDateFrom = $('#working_date').val();
        var startDateTo = $('#reschedule_date').val();
        var tergateId = $('#edit_id').val();

        if (startDateFrom != '' && startDateTo != '') {

            return $.ajax({
                type: "get",
                url: "{{url('hr/specialholiday/CheckDayEnd')}}",
                data: {
                    startDateFrom: startDateFrom,
                    startDateTo: startDateTo,
                    tergateId: tergateId
                },
                dataType: "json",
                success: function (data) {

                    // console.log("test",data);

                    if (data.Table == "DayEnd") {
                        swal({
                            title: "Day end exist, please select another day !!",
                            icon: "error",
                        });

                        // $('#working_date').val('');
                        // $('#reschedule_date').val('');

                    } else if (data.Table == "Holiday") {
                        swal({
                            title: "Holiday exist, please select another day !!",
                            icon: "error",
                        });

                        // $('#working_date').val('');
                        // $('#reschedule_date').val('');
                    }else {
                        $("#returnValueData").val('0');
                        // return false;
                    }
                }
            });
        }
        else{
            return false;
        }
    }

</script>

