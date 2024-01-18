<style type="text/css">
     .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
</style>

<!-- Page -->
<form id="gov_holiday_add_form" enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator">

    <div class="row">
        <div class="col-sm-9 offset-sm-3">
            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control " placeholder="Enter Govt Holiday Title"
                            name="gh_title" id="gh_title" required data-error="Please enter Govt Holiday Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control  dateMonthPicker" id="gh_date" name="gh_date"
                            placeholder="DD-MM" required data-error="Please enter Date">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>


            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Effective Start Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control  datepicker-custom limitDateStart common_effective_date" id="efft_start_date"
                            name="efft_start_date" placeholder="DD-MM-YYYY" required
                            data-error="Please enter Effective Date" >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title">Effective End Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control  datepicker-custom limitDateEnd common_effective_date" id="efft_end_date"
                            name="efft_end_date" placeholder="DD-MM-YYYY" >
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title">Description</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <textarea class="form-control " id="gh_description" name="gh_description" rows="2"
                            placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    </div>

    </div>
    </div>
    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>
<!-- End Page -->


<script type="text/javascript">

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Govt Holiday",
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


    $('#gov_holiday_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#gov_holiday_add_form')[
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

    $('.dateMonthPicker').datepicker({
        dateFormat: 'dd-mm',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
    }).keydown(false);

</script>