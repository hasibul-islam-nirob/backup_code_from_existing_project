<?php
use App\Services\HtmlService as HTML;
?>

<form id="transfer_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden id="transfer_id" name="transfer_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div id="branch_edit_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Branch From</label>
                    <div class="input-group">
                        {!! HTML::forBranchFieldHr('edit_branch_id', 'branch_from_id') !!}
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
                    <label class="input-title RequiredStar">Transfer Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_transfer_date" name="transfer_date" type="text"
                            style="z-index:99999 !important;" class="form-control round datepicker-custom"
                            placeholder="DD-MM-YYYY">
                    </div>
                </div>


                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Branch To</label>
                    <div class="input-group">
                        {!! HTML::forBranchFieldHr('edit_branch_to_id', 'branch_to_id') !!}
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Expected Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date"
                            type="text" class="form-control round datepicker-custom" placeholder="DD-MM-YYYY">
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

            $('#edit_transfer_date').val(response.result_data.transfer_date);
            $('#edit_exp_effective_date').val(response.result_data.exp_effective_date);
            $('#edit_description').val(response.result_data.description);
            $('#transfer_id').val("{{ $id }}");
            $('#edit_branch_to_id').val(response.result_data.branch_to_id);

            $('#edit_branch_id').val(response.result_data.branch_id).trigger('change');
            setTimeout(function() {
                $("form .clsSelect2").select2({
                    dropdownParent: $("#commonModal")
                });

                $('#edit_employee_id').val(response.result_data.emp_id).trigger(
                    'change');
            }, 1200);

            showModal({
                titleContent: "Edit Transfer Application",
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
                .val()), 'get', {},
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
                    '#transfer_edit_form')[
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

        $("#edit_draftBtn").click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../update/draft/api", 'post', new FormData($(
                    '#transfer_edit_form')[0]),
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
