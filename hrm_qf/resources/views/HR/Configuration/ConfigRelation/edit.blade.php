

<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="relation_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">
        <div class="col-lg-10 text-right">
            <div class="form-row align-items-center">
                <label class="col-lg-4 input-title RequiredStar">Relation Name</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" id="name" value="" placeholder="Enter Relation Name" required data-error="Please enter Relation name.">
                        <div class="help-block with-errors is-invalid"></div>
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


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#relation_edit_form')[0]),
            function(response, textStatus, xhr) {
                

                var result_data = response;
                var formObject = document.forms[0].elements;
                
                $.each(formObject, function () {
                    $('#edit_id').val("{{ $id }}");
                    var nameElement = $(this).attr('name');
                    $(this).val(result_data[nameElement]);

                });
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    });

    showModal({
        titleContent: "Edit Relation",
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

    $('#relation_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#relation_edit_form')[0]),
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
