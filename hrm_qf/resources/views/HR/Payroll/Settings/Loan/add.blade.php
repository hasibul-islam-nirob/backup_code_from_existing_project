
<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

<form id="loan_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Vehicle Type</label>
                    <div class="input-group">
                        <select name="vehicle_type" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Vehicle Type</option>
                            <option value="bycycle">By-Cycle</option>
                            <option value="motorcycle">Motor-Cycle</option>
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Maximum Installment</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="max_installment">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Maximum Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="max_amount">
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title">Settlement Fee (%)</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="settlement_fee">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Intrest Rate (%)</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="intrest_rate">
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title">Interest Method</label>
                    <div class="input-group">
                        <select name="intrest_method" class="form-control clsSelect2" style="width: 100%">
                            <option value="decline">Decline</option>
                            <option value="flate ">Flate </option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input value="" style="z-index:99999 !important;" name="effective_date"
                            type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

                <div class="col-sm-5 form-group  d-none">
                    <label class="input-title">Salary Structure</label>
                    <div class="input-group">
                        <select name="salary_structure" class="form-control clsSelect2" style="width: 100%">
                            <option value="1">Enable</option>
                            <option selected value="0">Disable </option>
                        </select>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>

<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    showModal({
        titleContent: "Add Loan",
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
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#loan_form')[
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
</script>
