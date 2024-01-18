<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="contract_conclude_add_form" enctype="multipart/form-data" method="post" data-toggle="validator"
    novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div id="branch_add_div" class="col-sm-5 form-group">
                    {{-- <label class="input-title RequiredStar">Branch</label> --}}
                    <div class="input-group">
                        {{-- {!! HTML::forBranchFieldHr('add_branch_id') !!} --}}
                        {!! HTML::forBranchFeildNew(true, 'branch_id', 'add_branch_id','','','Branch') !!}
                    </div>
                </div>

                <div id="employee_add_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Contract Conclude Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_contract_conclude_date" name="contract_conclude_date" type="text"
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
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date" type="text" class="form-control round datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Reason</label>
                    <div class="input-group">
                        <select id="add_reason" name="reason" class="form-control clsSelect2" style="width: 100%">
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
                                <input type="file" id="add_attachment" name="attachment"
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
                            <textarea rows="5" id="add_description" name="description" class="form-control"
                                style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>

<script>
    $(document).ready(function() {
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Contract Conclude Application",
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
                0: 'add_sendBtn',
                1: 'add_draftBtn',
            }
        }),
    });

    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                $('#add_employee_id').val(null).trigger('change');
                $('#add_employee_id').select2({
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

    $('#add_sendBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/send/api", 'post', new FormData($(
                '#contract_conclude_add_form')[0]),
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

    $('#add_draftBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/draft/api", 'post', new FormData($(
                '#contract_conclude_add_form')[0]),
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
