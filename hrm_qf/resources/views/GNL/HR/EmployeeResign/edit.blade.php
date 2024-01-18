<?php
use App\Services\HtmlService as HTML;
?>

<form id="resign_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden id="resign_id" name="resign_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <input hidden disabled id="other_edit_branch_id" name="branch_id">
                <input hidden disabled id="other_edit_employee_id" name="employee_id">

                <div id="branch_edit_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Branch</label>
                    <div class="input-group">
                        {!! HTML::forBranchFieldHr('edit_branch_id') !!}
                    </div>
                </div>

                <div id="employee_edit_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="edit_employee_id" name="employee_id" class="form-control" style="width: 100%">

                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Resign Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_resign_date" name="resign_date" type="text" style="z-index:99999 !important;"
                            class="form-control round datepicker" placeholder="DD-MM-YYYY">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Expected Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date"
                            type="text" class="form-control round datepicker" placeholder="DD-MM-YYYY">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Reason</label>
                    <div class="input-group">
                        <select id="edit_reason" name="reason" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select</option>
                            <option value="Reason 1">Reason 1</option>
                            <option value="Reason 2">Reason 2</option>
                            <option value="Reason 3">Reason 3</option>
                            <option value="Reason 4">Reason 4</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">

                        <input type="text" class="form-control round" readonly="">
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="edit_attachment" name="attachment"
                                    onchange="validate_fileupload(this.id,2);">
                            </span>
                        </div>

                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-12 form-group">
                    <label class="input-title">Description</label>
                    <div class="input-group">
                        <div class="input-group">
                            <textarea rows="5" id="edit_description" name="description" class="form-control"
                                style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>

<script>

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

        function(response, textStatus, xhr) {

            $('#edit_resign_date').val(response.result_data.resign_date);
            $('#edit_exp_effective_date').val(response.result_data.exp_effective_date);
            $('#edit_reason').val(response.result_data.reason);
            $('#edit_description').val(response.result_data.description);
            $('#resign_id').val("{{ $id }}");

            if (typeof current_emp !== "undefined" && !jQuery.isEmptyObject(current_emp) && response.result_data
                .emp_id == current_emp.id) {
                $('#edit_branch_id').prop('disabled', true);
                $('#edit_employee_id').prop('disabled', true);
                $('#edit_resign_date').prop('readonly', true);

                $('#employee_edit_div').hide();
                $('#branch_edit_div').hide();

                $('#other_edit_employee_id').prop('disabled', false);
                $('#other_edit_branch_id').prop('disabled', false);

                $('#other_edit_employee_id').val(response.result_data.emp_id);
                $('#other_edit_branch_id').val(response.result_data.branch_id);

                $('.clsSelect2').select2();
                $('.datepicker').datepicker();
            } else {

                $('#edit_branch_id').prop('disabled', false);
                $('#edit_employee_id').prop('disabled', false);

                $('#employee_edit_div').show();
                $('#branch_edit_div').show();

                $('#other_edit_employee_id').prop('disabled', true);
                $('#other_edit_branch_id').prop('disabled', true);
                $('#edit_resign_date').prop('readonly', false);

                $('#edit_branch_id').val(response.result_data.branch_id);
                $('#edit_branch_id').change();

                $('.clsSelect2').select2();
                $('.datepicker').datepicker();

                setTimeout(function() {

                    $("form .clsSelect2").select2({
                        dropdownParent: $("#commonModal")
                    });

                    $('#edit_employee_id').val(response.result_data.emp_id).trigger(
                        'change');

                }, 1200);
            }

            showModal({
                titleContent: "Edit Resign Application",
                footerContent: getModalFooterElement({
                    'btnNature': {
                        0: 'send',
                        1: 'save',
                    },
                    'btnName': {
                        0: 'Send',
                        1: 'Draft',
                    },
                    'btnId': {
                        0: 'edit_sendBtn',
                        1: 'edit_draftBtn',
                    }
                }),
            });

            configureActionEvents();

        },
        function(response) {
            showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
        }
    );

    $('#edit_branch_id').change(function(event) {
        callApi("{{ route('gnl_getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                $('#edit_employee_id').val(null).trigger('change');
                $('#edit_employee_id').select2({
                    dropdownParent: $("#commonModal"),
                    data: response,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: function(data) {
                        return data.html;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                });
            }
        );
    });

    function configureActionEvents() {

        $('#edit_sendBtn').click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../update/send/api", 'post', new FormData($(
                    '#resign_edit_form')[0]),
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

        $("#edit_draftBtn").click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../update/draft/api", 'post', new FormData($(
                    '#resign_edit_form')[0]),
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
