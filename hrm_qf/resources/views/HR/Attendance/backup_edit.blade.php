<?php
use App\Services\HtmlService as HTML;
?>
<form id="attendance_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">
                <div class="col-sm-3 form-group">
                    <label class="input-title RequiredStar">Branch</label>
                    <div class="input-group">
                        {!! HTML::forBranchFieldHr('emp_branch_id') !!}
                    </div>
                </div>

                <div class="col-sm-3 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('emp_department_id') !!}
                    </div>
                </div>

                <div class="col-sm-3 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Designation</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('emp_designation_id') !!}
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Device</label>
                    <div class="input-group">
                        <select id="edit_device_id" name="device_id" class="form-control clsSelect2"
                            style="width: 100%">
                            <option value="">Select device</option>
                            <option value="1">Device 1 </option>
                            <option value="2">Device 2 </option>
                            <option value="3">Device 3 </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="edit_employee_id" name="employee_id" class="form-control" style="width: 100%">

                        </select>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Time & Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_time_and_date" name="time_and_date" type="text"
                            style="z-index:99999 !important;" class="form-control round datepicker"
                            placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>

<script>
    function loadEditData() {
        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {

                $('#edit_time_and_date').val(response.result_data.time_and_date);
                $('#edit_employee_id').val(response.result_data.emp_id).trigger('change');
                $('#edit_device_id').val(response.result_data.device_id);
                showModal({
                    titleContent: "Edit attendance",
                    footerContent: getModalFooterElement({
                        'btnNature': {
                            0: 'update',

                        },
                        'btnName': {
                            0: 'Update',
                        },
                        'btnId': {
                            0: 'updateBtn',
                        }
                    }),
                });

                configureActionEvents();

            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );
    }

    $(document).ready(function() {
        $('#emp_branch_id').trigger('change');
    });

    $('#emp_designation_id, #emp_department_id, #emp_branch_id').change(function(e) {

        let data = new FormData();
        data.append('branch_id', $('#emp_branch_id').val());
        data.append('department_id', $('#emp_department_id').val());
        data.append('designation_id', $('#emp_designation_id').val());

        callApi("{{ route('searchEmployeeAndGetOptions') }}", 'post', data,
            function(response, textStatus, xhr) {
                $('#edit_employee_id').val(null).trigger('change');
                $('#edit_employee_id').select2({
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
        ).then(loadEditData());
    });

    $('#updateBtn').click(function(e) {
        e.preventDefault();
        callApi("{{ url()->current() }}/../../update/{{ $id }}/api", 'post', new FormData($(
                '#attendance_edit_form')[0]),
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
