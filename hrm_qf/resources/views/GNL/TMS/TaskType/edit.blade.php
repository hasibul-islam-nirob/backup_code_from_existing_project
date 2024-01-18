
<form id="task_type_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true" style="padding: 0px 0.6rem 0px 0.6rem">
    @csrf

    <input hidden id="task_type_id" name="id">
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row justify-content-center">
                <div class="col-lg-4 form-group">
                    <label class="input-title RequiredStar">Task Type Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="type_name" name="type_name"
                        value="" placeholder="Enter Task Type Name" required
                            data-error="Please enter Task Type name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-4 form-group">
                    <label class="input-title RequiredStar">Type Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="task_type_code" name="task_type_code" value=""
                            placeholder="Enter Task Type Code" required data-error="Please enter Task Type Code.">

                            {{-- onblur="fnCheckDuplicate('{{base64_encode('tms_task_types')}}', this.name+'&&is_delete', this.value+'&&0','{{url('/ajaxCheckDuplicate')}}',this.id,'txtCodeError', 'task type code');" --}}
                    </div>
                    <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',
        function(response, textStatus, xhr) {
            $('#task_type_id').val("{{ $id }}");

            setTimeout(function() {
                $("form .clsSelect2").select2({
                    dropdownParent: $("#commonModal")
                });

                $('#type_name').val(response.result_data.type_name);
                $('#task_type_code').val(response.result_data.task_type_code);
            }, 200);

            showModal({
                titleContent: "Edit Task Type",
                footerContent: getModalFooterElement({
                    'btnNature': {
                        0: 'save',
                    },
                    'btnName': {
                        0: 'Save',
                    },
                    'btnId': {
                        0: 'edit_saveBtn',
                    }
                }),
            });

            configureActionEvents();

        },
        function(response) {
            showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
        }
    );

    function configureActionEvents() {

        $('#edit_saveBtn').click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../update/save/api", 'post', new FormData($(
                    '#task_type_edit_form')[
                    0]),
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

    }

</script>
