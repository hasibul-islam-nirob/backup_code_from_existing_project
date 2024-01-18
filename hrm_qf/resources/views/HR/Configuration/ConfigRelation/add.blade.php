
<form id="relation_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <div class="row">
        <div class="col-sm-10 text-right">
            <div class="form-row align-items-center">
                <label class="col-sm-4 input-title RequiredStar">Relation Name</label>
                <div class="col-sm-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Relation Name" required data-error="Please Relation name.">
                        <div class="help-block with-errors is-invalid"></div>
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
        titleContent: "Add Relation",
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

    $('#relation_add_form').submit(function (event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#relation_add_form')[0]),
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
