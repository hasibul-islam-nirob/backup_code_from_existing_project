<form id="payscale_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" >

    <input hidden value="" id="edit_id" name="edit_id">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">
                <div id="employee_add_div" class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Name</label>
                    <div class="input-group">
                        <input type="text" name="name" value="" style="width: 100%;" required>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 offset-sm-3 form-group">
                <label class="input-title RequiredStar">Effective Date</label>
                <div class="input-group">
                    <div class="input-group-prepend ">
                        <span class="input-group-text ">
                            <i class="icon wb-calendar" aria-hidden="true"></i>
                        </span>
                    </div>
                    <input value="" style="z-index:99999 !important;" name="eff_date_start"
                        type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                </div>
            </div>

        </div>

    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>


<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
        

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#payscale_edit_form')[0]),
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
        titleContent: "Edit Pay Scale",
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

    $('#payscale_edit_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#payscale_edit_form')[
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