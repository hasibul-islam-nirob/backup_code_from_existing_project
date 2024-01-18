{{-- @extends('Layouts.erp_master')
@section('content') --}}

<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="banks_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">
        <div class="col-lg-10 text-right">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title RequiredStar">Bank Name</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" id="name" value="" placeholder="Enter Bank Name" required data-error="Please enter Bank name.">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>

                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Bank Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="address" id="address" value="" placeholder="Enter Bank Address">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Bank Email Address</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" id="email" value="" placeholder="Enter Bank's Email Address">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Bank Phone No</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="phone" class="form-control" name="phone" id="phone" value="" placeholder="Enter Bank's Phone No.">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Contact Person</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person" id="contact_person" value="" placeholder="Enter Contact Person's Name">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Contact Person's Designation</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact_person_designation" id="contact_person_designation"   value="" placeholder="Enter Contact Person's Designation">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title textNumber">Contact Person's Phone</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control textNumber" name="contact_person_phone" id="contact_person_phone"    value="" placeholder="Enter Contact Person's Phone">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title">Contact Person's Email</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" name="contact_person_email" id="contact_person_email" 
                        value="" placeholder="Enter Contact Person's Email">
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


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#banks_edit_form')[0]),
            function(response, textStatus, xhr) {
                
                // $('#edit_id').val("{{ $id }}");
                // $('#name').val(response['name']);
                // $('#address').val(response['address']);
                // $('#phone').val(response['phone']);
                // $('#email').val(response['email']);
                // $('#contact_person').val(response['contact_person']);
                // $('#contact_person_designation').val(response['contact_person_designation']);
                // $('#contact_person_phone').val(response['contact_person_phone']);
                // $('#contact_person_email').val(response['contact_person_email']);

                var result_data = response;
                var formObject = document.forms[0].elements;
                // console.log(formObject);
                
                $.each(formObject, function () {
                    $('#edit_id').val("{{ $id }}");
                    var nameElement = $(this).attr('name');
                    $(this).val(result_data[nameElement]);

                    //console.log(nameElement);
                });
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    });

    showModal({
        titleContent: "Edit Bank",
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

    $('#banks_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#banks_edit_form')[0]),
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

</script>
