
<form id="branch_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <input hidden  value="" id="edit_id" name="edit_id">
    <div class="row">
        <div class="col-lg-10">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Bank</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group" id="BankOptionID">
                        <select class="form-control  text-right clsSelect2" name="bank_id" id="bank_id"
                                required data-error="Please Select a Bank" style="width: 100%">
                        </select> 
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right RequiredStar">Branch Name</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="name" name="name" value="" required placeholder="Enter Branch Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="address" name="address" value="" placeholder="Enter Branch Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Email Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" id="email" name="email" value="" placeholder="Enter Branch's Email Address">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Branch Phone No</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="phone" name="phone" value="" placeholder="Enter Branch's Phone No.">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="" placeholder="Enter Contact Person's Name">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Designation</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" id="contact_person_designation" name="contact_person_designation" value="" placeholder="Enter Contact Person's Designation">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right textNumber">Contact Person's Phone</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" id="contact_person_phone" name="contact_person_phone" value="" placeholder="Enter Contact Person's Phone">
                        
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title text-right">Contact Person's Email</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" id="contact_person_email" name="contact_person_email" value="" placeholder="Enter Contact Person's Email">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>


<script>


    $(document).ready(function(){

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#branch_edit_form')[0]),
            function(response, textStatus, xhr) {
                
                // $('#edit_id').val("{{ $id }}");
                // $('#name').val(response.branchData['name']);
                // $('#address').val(response.branchData['address']);
                // $('#email').val(response.branchData['email']);
                // $('#phone').val(response.branchData['phone']);
                // $('#contact_person').val(response.branchData['contact_person']);
                // $('#contact_person_designation').val(response.branchData['contact_person_designation']);
                // $('#contact_person_phone').val(response.branchData['contact_person_phone']);
                // $('#contact_person_email').val(response.branchData['contact_person_email']);

                var result_data = response.branchData;
                var formObject = document.forms[0].elements;
                
                $.each(formObject, function () {
                    $('#edit_id').val("{{ $id }}");
                    var nameElement = $(this).attr('name');
                    $(this).val(result_data[nameElement]);

                    //console.log(nameElement);
                });

                showOptionsData(response)

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }

        );


    });

    function showOptionsData(response){
        let option = "<option value='' selected disabled>Select Bank</option>";
        for(let i = 0; i < response.banks.length; i++){
            if(response.branchData['bank_id'] == response.banks[i].id){
                option += "<option selected value='"+response.banks[i].id+"' class='w-100'>"+response.banks[i].name+"</option>";
            }else{
                option += "<option value='"+response.banks[i].id+"' class='w-100'>"+response.banks[i].name+"</option>";
            }
        }
        $('#bank_id').html(option);
    }

    showModal({
        titleContent: "Edit Branch",
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

    $('#branch_edit_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#branch_edit_form')[
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

