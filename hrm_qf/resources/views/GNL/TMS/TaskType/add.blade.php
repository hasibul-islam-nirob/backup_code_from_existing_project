
<form id="task_type_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true" style="padding: 0px 0.6rem 0px 0.6rem">
    @csrf

    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row justify-content-center">
                <div class="col-lg-4 form-group">
                    <label class="input-title RequiredStar">Task Type Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="type_name" name="type_name"
                            placeholder="Enter Task Type Name" required
                            data-error="Please enter Task Type name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-4 form-group">
                    <label class="input-title RequiredStar">Type Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="task_type_code" name="task_type_code"
                            placeholder="Enter Task Type Code" required
                            data-error="Please enter Task Type Code.">
                            {{-- onblur="fnCheckDuplicate('{{base64_encode('tms_task_types')}}', this.name+'&&is_delete', this.value+'&&0','{{url('/ajaxCheckDuplicate')}}',this.id,'txtCodeError', 'task type code');"> --}}
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Task Type",
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
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/save/api", 'post', new FormData($('#task_type_add_form')[0]),
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
