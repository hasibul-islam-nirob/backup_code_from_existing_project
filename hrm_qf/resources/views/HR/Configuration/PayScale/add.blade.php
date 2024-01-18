
<form id="payscale_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">
                <div id="employee_add_div" class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title RequiredStar">Name</label>
                    <div class="input-group">
                        <input type="text" name="name" style="width: 100%;" required>
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
    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css-js/timepicker-master/timepicker.css')}}">
<script src="{{asset('assets/css-js/timepicker-master/timepicker.js')}}"></script>

<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    showModal({
        titleContent: "Add Pay Scale",
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

    $('#payscale_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#payscale_add_form')[
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
