
<form id="branch_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Bank</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" style="width: 100%" id="bank_id" name="bank_id"
                                required data-error="Please Select a Bank" >
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Branch</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Enter Branch Name" required data-error="Please enter Branch name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="address" placeholder="Enter Branch Address">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Phone No</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control phoneNumber" name="phone" placeholder="Enter Branch's Phone No.">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Email Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" placeholder="Enter Branch's Email Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person" placeholder="Enter Contact Person's Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Designation</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person_designation" placeholder="Enter Contact Person's Designation">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right textNumber">Contact Person's Phone</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" name="contact_person_phone" placeholder="Enter Contact Person's Phone">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Email</label>
                <div class="col-lg-8 form-group">
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

        callApi("{{ url()->current() }}", 'post', new FormData($('#branch_edit_form')[0]), function(response, textStatus, xhr) {
                showBankOptionsData(response)
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
        
    });

    function showBankOptionsData(response){
        let option = "<option value='' selected disabled>Select Bank</option>";
        for(let i = 0; i < response.banks.length; i++){
            option += "<option value='"+response.banks[i].id+"' >"+response.banks[i].name+"</option>";
        }
        $('#bank_id').html(option);

    }

    showModal({
        titleContent: "Add Branch",
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

    $('#branch_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#branch_add_form')[
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