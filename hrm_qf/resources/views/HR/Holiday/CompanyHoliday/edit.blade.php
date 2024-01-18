

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

    #checkboxes label {
        display: block;
    }
</style>

<!-- Page -->
<form id="company_holiday_edit_form"  enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator">
    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">
        <div class="col-lg-9 offset-lg-3">

            <!-- Html View Load  -->
            {{-- {!! HTML::forCompanyFeild($CompHolidayData->company_id) !!} --}}
            <input hidden id="company_id" name="company_id">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control " placeholder="Enter comp Holiday Title"
                            name="ch_title" id="ch_title" value="" required
                            data-error="Please enter comp Holiday Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Day</label>
                <div class="col-lg-5 form-group">
                    <div class="row" id="holodayDays">
                            
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Effective Date</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        
                        <input type="text" class="form-control  datepicker-custom common_effective_date" onchange="fnCheckDayEnd();" id="ch_eff_date" name="ch_eff_date" placeholder="DD-MM-YYYY" value="" required data-error="Please Select Date" autocomplete="off">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Description</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control " id="ch_description" name="ch_description" rows="2"
                            placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Branch</label>
                <div class="col-lg-5 form-group">
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
            <div class="row" id="BranchData">
                
            </div>
        </div>
    </div>

    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>

<!-- End Page -->
<script type="text/javascript">

$(document).ready(function(){

    window.attData = [];
    window.flag = 0;

    $("form .clsSelect2").select2({
        dropdownParent: $("#commonModal")
    });


    callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#department_edit_form')[0]),
        function(response, textStatus, xhr) {

            //console.log(response);

            showDaysData(response.days, response.getData.ch_day)
            showBranchData(response.branchData, response.getData.branch_arr)

            let flagCheck = response.getData.branch_arr;
            flagCheck = flagCheck.split(',');

            if(flagCheck.length > 1){
                $("#branch_id").val(-1);
            }
            else {
                $("#branch_id").val(response.getData.branch_arr);
            }

            $("#branch_id").trigger("change");
            // console.log(response.getData.branch_arr);

            
            $('#edit_id').val("{{ $id }}");
            $('#ch_title').val(response.getData.ch_title);
            $('#ch_eff_date').val(response.getData.ch_eff_date);
            $('#ch_description').val(response.getData.ch_description);
            $('#company_id').val(response.getData.company_id);


        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );

});


    showModal({
        titleContent: "Edit Company Holiday",
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
        $('#edit_updateBtn_submit').click();
    });

    $('#company_holiday_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#company_holiday_edit_form')[0]),
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



    function showBranchData(response, selectBranches){

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

        const branchArray = selectBranches.split(",");
        $.each(response, function(index, item) {
            for(let i=0; i<branchArray.length; i++){

                if( index == branchArray[i]){
                    let id = '#branch_array_'+branchArray[i];
                    const cb = document.querySelector(id);
                    cb.setAttribute('checked', true);
                }

            }
        });
        
    }



    function showDaysData(response, selectDays){

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

        const dayArray = selectDays.split(",");
        $.each(response, function(index, item) {
            for(let i=0; i<dayArray.length; i++){

                if( index == dayArray[i]){
                    let id = '#ch_day_'+dayArray[i];
                    const cb = document.querySelector(id);
                    cb.setAttribute('checked', true);
                }

            }
        });

    }


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


    // async function showDaysData(response){
    //     let checkbox = "";
    //     const DaysArr = [ "Saturday", "Sunday", "Monday", "Tuesday", "Thursday", "Friday", "Wednesday" ];

    //     let allDays = response.getData.ch_day;
    //     const dayArray = allDays.split(",");

    //     for(let i=0; i<DaysArr.length; i++){
    //         checkbox += "<div class='col-sm-4'>"+
    //             " <div class='input-group checkbox-custom checkbox-primary'>"+
    //                 "<input type='checkbox' name='ch_day[]' id='ch_day_"+DaysArr[i]+"' value='"+DaysArr[i]+"'>"+
    //                 "<label for='ch_day_'>"+DaysArr[i]+"</label>"+
    //             "</div>"+
    //         "</div>";
    //         $('#holodayDays').html(checkbox);
    //     }

    //     for(let i=0; i<DaysArr.length; i++){
    //         for(let j=0; j<DaysArr.length; j++){
    //             if(DaysArr[i] == dayArray[j]){
    //                 let id = '#ch_day_'+DaysArr[i];
    //                 const cb = document.querySelector(id);
    //                 cb.setAttribute('checked', true);
    //             }
    //         }
    //     }

    // }


    // async function showBranchData(response){


    //     let checkbox = "";
    //     let allBranchFromGlobal = response.AllBranchName;

    //     let allBranchInArray = response.getData.branch_arr;
    //     const branchInArray = allBranchInArray.split(",");


    //     for(let i = 0; i < allBranchFromGlobal.length; i++){
    //         checkbox += "<div class='col-sm-3'>"+
    //                 " <div class='input-group checkbox-custom checkbox-primary'>"+
    //                     "<input type='checkbox'  name='branch_array[]' id='branch_array_"+allBranchFromGlobal[i].id+"' value='"+allBranchFromGlobal[i].id+"'>"+
    //                     "<label for='ch_day_'>"+allBranchFromGlobal[i].branch_name+" ["+allBranchFromGlobal[i].branch_code+"]"+"</label>"+
    //                 "</div>"+
    //             "</div>";

    //         $('#BranchData').html(checkbox);
    //     }

    //     for(let i=0; i<allBranchFromGlobal.length; i++){
    //         for(let j=0; j<allBranchFromGlobal.length; j++){

    //             if(allBranchFromGlobal[i].id == branchInArray[j]){
    //                 let id = '#branch_array_'+allBranchFromGlobal[i].id;
    //                 const cb = document.querySelector(id);
    //                 cb.setAttribute('checked', true);

    //                 console.log(allBranchFromGlobal[i].id);
    //             }
    //         }

    //     }
    // }

    function showCheckboxes() {

        $('#checkboxes').hide();

        if($("#branch_id").val() == "-1"){
            $('#checkboxes').show();
        }

    }


</script>
