

<!-- Page -->
    <form id="designation_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
        <input hidden value="" id="edit_id" name="edit_id">
        <div class="row">
            <div class="col-sm-9 offset-sm-3">

                <!-- Html View Load  -->
                {{-- {!! HTML::forCompanyFeild($DesignationData->company_id) !!} --}}
                {{-- <input hidden value="" id="company_id" name="company_id"> --}}

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Name</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" id="name" name="name"
                             placeholder="Enter Name" required data-error="Please enter name."
                             value="" >
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title">Short Name</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" id="short_name" name="short_name" 
                            placeholder="Enter Name" value="">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
    </form>
<!-- End Page -->

<script type="text/javascript">

    $(document).ready(function(){

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });


        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'get', new FormData($('#designation_edit_form')[0]),
            function(response, textStatus, xhr) {
                
                // $('#edit_id').val("{{ $id }}");
                // $('#name').val(response['name']);
                // $('#short_name').val(response['short_name']);
                // $('#company_id').val(response['company_id']);
                var result_data = response;
                var formObject = document.forms[0].elements;
                
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
        titleContent: "Edit Designation",
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

    $('#designation_edit_form').submit(function(event) {
        event.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#designation_edit_form')[0]),
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


    // $('form').submit(function (event) {
    //     $(this).find(':submit').attr('disabled', 'disabled');
    // });
</script>
