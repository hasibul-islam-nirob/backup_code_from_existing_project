
<style>
    .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
</style>
<!-- Page -->

<div id="draftView" style="display: none;">
    @include('HR.CommonBlade.detailsGrid',['title' => 'Special Holiday Details'])


    <div class="row">
        <div class="col-lg-9 offset-lg-3">
    
    
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Applicable For</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <div class="radio-custom radio-primary">
                            <input type="radio" id="checkGroup" name="sh_app_for" value="org">
                            <label for="checkGroup">Organization &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary">
                            <input type="radio" id="checkBranch" name="sh_app_for" value="branch">
                            <label for="checkBranch">Branch &nbsp &nbsp </label>
                        </div>
                        <div class="radio-custom radio-primary" style="display: none">
                            <input type="radio" id="checkSomity" name="sh_app_for" value="somity">
                            <label for="checkSomity">Somity &nbsp &nbsp </label>
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
    
            <div class="form-row align-items-center desc" id="org" style="display: none;">
                    <label class="col-sm-3 input-title RequiredStar">Organization</label>
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
                <label class="col-lg-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control " placeholder="Enter Holiday Title" name="sh_title"
                            id="sh_title" value="" readonly>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
    
            <div class="form-row align-items-center ">
                <label class="col-lg-3 input-title RequiredStar">Holiday Date From</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control " id="sh_date_from" name="sh_date_from"
                            data-plugin="datepicker" placeholder="DD-MM-YYYY" value=""
                            required data-error="Select Date" disabled>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
    
    
            <div class="form-row align-items-center ">
                <label class="col-lg-3 input-title RequiredStar">Holiday Date To</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ghdatepicker">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control " id="sh_date_to" name="sh_date_to"
                            data-plugin="datepicker" placeholder="DD-MM-YYYY" value=""
                            required data-error="Select Date" disabled>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
    
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Description</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control " id="sh_description" name="sh_description" rows="2"
                            placeholder="Enter Description" required data-error="Enter Description"
                            readonly></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>



<!-- End Page -->

<script type="text/javascript">


    $(document).ready(function(){

        $("#draftView").show();
        showModal({
            titleContent: "View Special Holiday",
        });

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#special_holiday_edit_form')[0]),
            function(response, textStatus, xhr) {

                showCompanyData(response.companyData, response.getData.branch_id, response.getData.sh_app_for)
                showBranchData(response.branchData, response.getData.branch_id, response.getData.sh_app_for)

                let flagCheck = response.getData.sh_app_for;
                if(flagCheck == "org"){
                    $("#org").show();
                    $("input[name=sh_app_for][value=org]").attr('checked', 'checked');

                }else if(flagCheck == "branch"){
                    $("#branch").show();
                    $("input[name=sh_app_for][value=branch]").attr('checked', 'checked');
                }

                
                $('#edit_id').val("{{ $id }}");
                $('#sh_title').val(response.getData.sh_title);
                $('#sh_date_from').val( viewDateFormat(response.getData.sh_date_from) );
                $('#sh_date_to').val( viewDateFormat(response.getData.sh_date_to) );
                $('#sh_description').val(response.getData.sh_description);
                $('#company_id').val(response.getData.company_id);


            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    })

    
    function showCompanyData(response, BranchID, applicatinType){

        let CompanyOption = "<option value='' selected disabled > Select Organization </option>";
        $.each(response, function(index, item) {
            CompanyOption += "<option value='"+ index +"' >"+ item +"</option>";
        });
        $('#company_id').html(CompanyOption);


        if(applicatinType == "org"){
            $.each(response, function(index, item) {

                if( index == BranchID){
                    $("#company_id").val(index);
                }

            });
        }


    }


    function showBranchData(response, BranchID, applicatinType){

        let BranchOption = "<option value='' selected disabled > Select Branch </option>";
        $.each(response, function(index, item) {
            BranchOption += "<option value='"+ index +"' >"+ item +"</option>";
        });
        $('#branch_id').html(BranchOption);


        if(applicatinType == "branch"){
            $.each(response, function(index, item) {

                if( index == BranchID){
                    $("#branch_id").val(index);
                }

            });
        }
    }



// Disable radio button
$(':radio:not(:checked)').attr('disabled', true);
</script>

