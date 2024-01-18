

<style>
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

<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Company Holiday Details'])

    <div class="row">
        <div class="col-lg-9 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter comp Holiday Title"
                            name="ch_title" id="ch_title" value="" readonly>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Day</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <div class="row" id="holodayDays">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Effective Date</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        
                        
                        <input type="text" class="form-control round datepicker" id="ch_eff_date" name="ch_eff_date"
                            placeholder="DD-MM-YYYY" autocomplete="off" value="" disabled>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Description</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <textarea class="form-control " id="ch_description" name="ch_description" rows="2"
                            placeholder="No Description" readonly></textarea>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Branch</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">

                        <select class="form-control clsSelect2" style="width: 100%" id="branch_id" name="branch_id" onchange="showCheckboxes();" readonly>
                            <option value="0">All</option>
                            <option value="1">Head Office</option>
                            <option value="-1">Branches</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-sm-12 form-group">
            <div class="form-row align-items-center"  >
                <div class="row" id="BranchData">
                    
                </div>
            </div>
        </div>


    </div>
</div>
    

<script>

    $("#draftView").show();
    showModal({
        titleContent: "View Company Holiday",
    });

    
    callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#department_edit_form')[0]),
        function(response, textStatus, xhr) {

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
            $('#ch_eff_date').val( viewDateFormat(response.getData.ch_eff_date) );
            $('#ch_description').val(response.getData.ch_description);
            $('#company_id').val(response.getData.company_id);


        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );


    function showBranchData(response, selectBranches){

        const branchArray = selectBranches.split(",");
        if(branchArray[0] == 1){
            $('#checkboxes').hide();
            
        }else if(branchArray[0] == 0){
            $('#checkboxes').hide();

        }else{

            let checkbox = "";
            $.each(response, function(index, item) {
                checkbox += "<div class='col-sm-2'>"+
                        "<div class='input-group checkbox-custom checkbox-primary'>"+
                            "<input type='checkbox' disabled name='branch_array[]' id='branch_array_" + index + "' value='" + index + "'>"+
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

        /*
        let checkbox = "";
        $.each(response, function(index, item) {
            checkbox += "<div class='col-sm-2'>"+
                    "<div class='input-group checkbox-custom checkbox-primary'>"+
                        "<input type='checkbox' disabled name='branch_array[]' id='branch_array_" + index + "' value='" + index + "'>"+
                        "<label for='branch_array_" + index + "'>" + item + "</label>"+
                    "</div>"+
                "</div>";
        });
        $('#BranchData').html(checkbox);

        //const branchArray = selectBranches.split(",");
        $.each(response, function(index, item) {
            for(let i=0; i<branchArray.length; i++){

                if( index == branchArray[i]){
                    let id = '#branch_array_'+branchArray[i];
                    const cb = document.querySelector(id);
                    cb.setAttribute('checked', true);
                }

            }
        });
        */


        
    }



    function showDaysData(response, selectDays){

        let checkbox = "";
        $.each(response, function(index, item) {

            checkbox += "<div class='col-sm-4'>"+
                " <div class='input-group checkbox-custom checkbox-primary'>"+
                    "<input type='checkbox' disabled name='ch_day[]' id='ch_day_" + index + "' value='" + index + "'>"+
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

    function showCheckboxes() {

        $('#checkboxes').hide();

        if($("#branch_id").val() == "-1"){

            $('#checkboxes').show();
        } 
    }

</script>