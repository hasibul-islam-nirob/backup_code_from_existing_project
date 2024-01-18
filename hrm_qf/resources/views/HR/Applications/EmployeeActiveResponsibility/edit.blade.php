<?php
use App\Services\HtmlService as HTML;
?>

<form id="active_responsibility_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator"
    novalidate="true">
    @csrf

    <input hidden id="active_responsibility_id" name="active_responsibility_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div id="branch_edit_div" class="col-sm-5 form-group">
                    {{-- <label class="input-title RequiredStar">Branch</label> --}}
                    <div class="input-group">
                        {{-- {!! HTML::forBranchFieldHr('edit_branch_id') !!} --}}
                        {!! HTML::forBranchFeildNew(true, 'branch_id', 'edit_branch_id','','','Branch') !!}
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

                <div id="current_designation_edit_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Current Designation</label>
                    <div class="input-group">
                        <input hidden id="edit_current_designation_id_hidden" name="current_designation_id">
                        <input readonly id="edit_current_designation_id" type="text" class="form-control round">
                    </div>
                </div>

                <div id="designation_to_promote_edit_div" class="col-sm-5 form-group offset-sm-2">
                    <label class="input-title RequiredStar">Designation To Promote</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('edit_designation_to_promote_id', 'designation_to_promote_id') !!}
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Active Responsibility Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_active_responsibility_date" name="active_responsibility_date" type="text"
                            style="z-index:99999 !important;" class="form-control round datepicker-custom"
                            placeholder="DD-MM-YYYY">
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
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date" type="text" class="form-control round datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

            </div>

            <div class="row">


                <div class="col-sm-5 form-group">
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

                <div class="col-sm-5 offset-sm-2 form-group">
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

            $('#edit_active_responsibility_date').val(response.result_data.active_responsibility_date);
            $('#edit_exp_effective_date').val(response.result_data.exp_effective_date);
            $('#edit_reason').val(response.result_data.reason);
            $('#edit_description').val(response.result_data.description);
            $('#active_responsibility_id').val("{{ $id }}");
            $('#edit_designation_to_promote_id').val(response.result_data.designation_to_promote_id);
            $('#edit_current_designation_id_hidden').val(response.result_data.current_designation_id);

            $('#edit_branch_id').val(response.result_data.branch_id).trigger('change');
            setTimeout(function() {

                $("form .clsSelect2").select2({
                    dropdownParent: $("#commonModal")
                });

                $('#edit_employee_id').val(response.result_data.emp_id).trigger(
                    'change');
            }, 1200);

            showModal({
                titleContent: "Edit Active Responsibility Application",
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
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
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

    $('#edit_employee_id').change(function(event) {
        let url = "{{ route('get_des_by_emp_id', ':boId') }}";
        url = url.replace(':boId', $(this).val());
        if ($(this).val()) {
            callApi(url, 'post', "",
                function(response, textStatus, xhr) {
                    $('#edit_current_designation_id').val(response.result_data.name);
                    $('#edit_current_designation_id_hidden').val(response.result_data.id);
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            );
        } else {
            $('#edit_current_designation_id').val("");
            $('#edit_current_designation_id_hidden').val("");
        }

    });

    function configureActionEvents() {

        $('#edit_sendBtn').click(function(e) {
            e.preventDefault();
            callApi("{{ url()->current() }}/../../update/send/api", 'post', new FormData($(
                    '#active_responsibility_edit_form')[0]),
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
                    '#active_responsibility_edit_form')[0]),
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
