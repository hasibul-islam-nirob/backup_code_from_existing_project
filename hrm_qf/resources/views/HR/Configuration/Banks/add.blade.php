
<form id="banks_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <div class="row">
        <div class="col-sm-10 text-right">
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title RequiredStar">Bank Name</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Enter Bank Name" required data-error="Please enter Bank name.">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>

                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Bank Address</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="address" placeholder="Enter Bank Address">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Bank Email Address</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" placeholder="Enter Bank's Email Address">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Bank Phone No</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="phone" class="form-control phoneNumber" name="phone" placeholder="Enter Bank's Phone No.">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Contact Person</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person's Name">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Contact Person's Designation</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person_designation" placeholder="Enter Contact Person's Designation">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title textNumber">Contact Person's Phone</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" name="contact_person_phone" placeholder="Enter Contact Person's Phone">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title">Contact Person's Email</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="contact_person_email" placeholder="Enter Contact Person's Email">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>

<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Bank",
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

    $('#banks_add_form').submit(function (event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#banks_add_form')[0]),
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
