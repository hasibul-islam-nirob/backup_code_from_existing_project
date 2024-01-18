<?php
    use App\Services\HtmlService as HTML;
    use App\Services\TmsService as TMS;
    use App\Services\CommonService as Common;

    $branchId = Common::getBranchId();
    $empData = DB::table('hr_employees')
        ->where([['branch_id', $branchId], ['status', 1], ['is_active', 1], ['is_delete', 0]])
        ->selectRaw('CONCAT(emp_name, " [", emp_code, "]") AS emp_name, id')
        ->orderBy('emp_code', 'ASC')
        ->get();

    $moduleData = DB::table('gnl_sys_modules')
        // ->where([['is_delete', 0], ['is_active', 1]])
        ->where([['is_delete', 0]])
        ->get();

    $taskType = TMS::fnGetAllTaskType();
?>

<form id="task_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true" style="padding: 0px 0.6rem 0px 0.6rem">
    @csrf

    <div class="row">
        <div class="col-lg-8 offset-lg-3">
            <!-- Html View Load  -->
            {!! HTML::forCompanyFeild() !!}
        </div>
    </div>

    <input hidden id="task_id" name="id">
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row">
                <div id="module_add_div" class="col-sm-5 form-group">
                    <div class="col-lg-8 offset-lg-3">
                        {!! HTML::forBranchFeild(false,'branch_id','branch_id',null,'','For Branch') !!}
                    </div>
                    <label class="input-title RequiredStar">Module &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="module_id" name="module_id" style="width: 100%">
                            <option value="">Select Type</option>
                            @foreach ($moduleData as $Row)
                                <option value="{{$Row->id}}">{{$Row->module_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="task_type_add_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Task Type &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <select id="task_type_id" name="task_type_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Type</option>
                            @foreach ($taskType as $Row)
                                <option value="{{$Row->id}}">{{$Row->type_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">title &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="task_title" name="task_title" placeholder="Write task title here" value="">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Task Date &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input class="form-control datepicker" id="task_date" name="task_date" type="text" placeholder="DD-MM-YYYY" style="z-index:99999 !important;" >
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Assigned By &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="assigned_by" name="assigned_by" style="width: 100%">
                            <option value="">Select Type</option>
                            @foreach ($empData as $Row)
                                <option value="{{$Row->id}}">{{$Row->emp_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" id="branch_id" value="{{$branchId}}">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Assigned To &nbsp;<span class="red-800">*</span></label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="assigned_to" name="assigned_to" style="width: 100%">
                            <option value="">Select Type</option>
                            @foreach ($empData as $Row)
                                <option value="{{$Row->id}}">{{$Row->emp_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title">Task Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="task_code" id="task_code" value="" readonly="">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 form-group">
                    <label class="input-title">Description &nbsp;<span class="red-800">*</span></label>
                    <textarea class="form-control ckeditor" id="description" name="description" rows="1">

                    </textarea>
                </div>

                <div class="col-sm-12 form-group">
                    <label class="input-title">Instruction &nbsp;<span class="red-800">*</span></label>
                    <textarea class="form-control ckeditor" id="instruction" name="instruction" rows="1">

                    </textarea>
                </div>

                <input type="hidden" name="status" id="status" value="">
            </div>

            <div class="row">
                <div class="col-sm-8 form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control" readonly>
                        <div class="input-group-append" style="height: 4vh">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="attachment" name="attachment" value=""
                                    onchange="validate_fileupload(this.id,2);">
                            </span>
                        </div>
                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>

                <div class="col-sm-4 align-items-center" style="padding-top:5%;">
                    <a href="javascript:void(0)" target="_blank" style="display: none;" id="attachment_view">View Attachment</a>
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

        /* Add Ckeditor to Specific Id And Must Define ckeditor js file in master blade head section*/
        if($('#description, #instruction').length ) {
            CKEDITOR.replace( 'description' );
            CKEDITOR.replace( 'instruction' );
        }

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',
            function(response, textStatus, xhr) {
                $('#task_id').val("{{ $id }}");
                // var taskDate = (response.result_data.task_date).split(' ')[0];
                // $('#task_date').val(viewDateFormat(response.result_data.task_date));

                setTimeout(function() {
                    $("form .clsSelect2").select2({
                        dropdownParent: $("#commonModal")
                    });

                    $('#module_id').val(response.result_data.module_id).trigger('change');
                    $('#task_type_id').val(response.result_data.task_type_id).trigger('change');
                    $('#task_title').val(response.result_data.task_title);
                    $('#task_date').val(response.result_data.task_date);
                    $('#assigned_by').val(response.result_data.assigned_by).trigger('change');
                    $('#assigned_to').val(response.result_data.assigned_to).trigger('change');
                    $('#task_code').val(response.result_data.task_code);
                    // $('#attachment').val(response.result_data.attachment);

                    /* Set ckeditor data from response start */
                    var description = setTimeout(CKEDITOR.instances['description'].setData(response.result_data.description), 10);
                    var instruction = setTimeout(CKEDITOR.instances['instruction'].setData(response.result_data.instruction),  10);
                    $('#description').val(description);
                    $('#instruction').val(instruction);
                    /* Set ckeditor data from response end */

                    if(response.result_data.attachment != null){
                        $('#attachment_view').show();
                        $("#attachment_view").attr("href", response.result_data.attachment);
                    }
                    else {
                        $('#attachment_view').hide();
                    }
                    //

                }, 200);

                showModal({
                    titleContent: "Edit Task",
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

    });

    $('#assigned_by').change(function () {
        if(($('#assigned_by').val() != "") && $('#assigned_by').val() == $('#assigned_to').val()){
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Can not assigne same person!',
            })
        }
    })

    $('#assigned_to').change(function () {
        if(($('#assigned_by').val() != "") && $('#assigned_by').val() == $('#assigned_to').val()){
            swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Can not assigne same person!',
            })
        }
    })

    function configureActionEvents() {

        $('#edit_saveBtn').click(function(e) {
            e.preventDefault();
            let formData = new FormData($('#task_edit_form')[0]);
            let description = $('#description').val();
            let instruction = $('#instruction').val();

            formData.append('description', CKEDITOR.instances['description'].getData());
            formData.append('instruction', CKEDITOR.instances['instruction'].getData());

            callApi("{{ url()->current() }}/../../update/save/api", 'post', formData,
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
