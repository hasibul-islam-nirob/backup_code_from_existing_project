
<?php 
use App\Services\HtmlService as HTML;
?>

<!-- Page -->
    <form id="designation_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
        @csrf
        <div class="row">
            <div class="col-sm-9 offset-sm-3">

                <!-- Html View Load  -->
               {!! HTML::forCompanyFeild() !!}

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Name</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="name" placeholder="Enter Name" required data-error="Please enter name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-sm-3 input-title">Short Name</label>
                    <div class="col-sm-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="short_name" placeholder="Enter Name">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
    </form>
<!-- End Page -->

<script type="text/javascript">

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Designation",
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


    $('#designation_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#designation_add_form')[
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



    // $('form').submit(function (event) {
    //     $(this).find(':submit').attr('disabled', 'disabled');
    // });
</script>

