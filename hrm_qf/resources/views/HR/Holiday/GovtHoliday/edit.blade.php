<style type="text/css">
     .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
</style>

<!-- Page -->
<form id="gov_holiday_edit_form" enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator">
    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">
        <div class="col-sm-9 offset-sm-3">

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter Govt Holiday Title" name="gh_title" id="gh_title" value="" required data-error="Please enter Govt Holiday Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title RequiredStar">Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control  dateMonthPicker" id="gh_date" name="gh_date" value="" placeholder="DD-MM" required data-error="Please enter Date">
                        <input type="text" hidden  name="gh_date" value="">

                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title RequiredStar">Effective Start Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>

                        <input type="text" class="form-control  datepicker-custom limitDateStart common_effective_date" id="efft_start_date"
                            name="efft_start_date" value="" placeholder="DD-MM-YYYY" autocomplete="off"
                            required data-error="Please enter Effective Date">
                            {{-- <input type="text" name="efft_start_date" value="" hidden> --}}
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center ">
                <label class="col-sm-3 input-title">Effective End Date</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                       
                        <input type="text" class="form-control datepicker-custom limitDateEnd common_effective_date" id="efft_end_date" value=""
                            name="efft_end_date"  placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-3 input-title">Description</label>
                <div class="col-sm-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control" id="gh_description" name="gh_description" rows="2"
                            placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>
<!-- End Page -->

<script type="text/javascript">


//======== Date Start ================
var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();

var currentDate = yyyy + '-' + mm + '-' + dd;
var dayMonth = dd + '-' + mm;
//========= Date End  ================

    $(document).ready(function(){

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#gov_holiday_edit_form')[0]),
            function(response, textStatus, xhr) {

                var result_data = response;
                var formObject = document.forms[0].elements;
            
                $.each(formObject, function () {
                    $('#edit_id').val("{{ $id }}");
                    var nameElement = $(this).attr('name');
                    $(this).val(result_data[nameElement]);
                });

                if( response.gh_date != null){
                    $("#gh_date").attr('disabled', true);
                }
                if( response.efft_start_date <= currentDate && response.efft_start_date != null ){
                    $("#efft_start_date").attr('disabled', true);

                }else if(response.efft_start_date == null || response.efft_start_date >= currentDate){
                    $("#efft_start_date").attr('disabled', false);
                }


            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    });

    showModal({
        titleContent: "Edit Govt Holiday",
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

    $('#gov_holiday_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#gov_holiday_edit_form')[0]),
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
