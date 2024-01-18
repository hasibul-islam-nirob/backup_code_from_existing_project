<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
$preFix = last(request()->segments()) == 'others' ? '../../' : '../';
?>

<style>
    .modal-lg {
        max-width: 80%;
    }
</style>

<form id="leave_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden id="leave_id" name="leave_id">

    {{-- <div class="row">

        <div class="col-sm-10 offset-sm-1"> --}}

            <div class="row p-15">

                <div id="apl_div" class="col-sm-8">

                    <div class="row">

                        <input hidden disabled id="other_edit_branch_id" name="branch_id">
                        <input hidden disabled id="other_edit_employee_id" name="employee_id">

                        {{-- <div id="branch_edit_div" class="col-sm-4 form-group">
                            <div class="input-group">
                                {!! HTML::forBranchFeildNew(true, 'branch_id', 'edit_branch_id') !!}
                            </div>
                        </div> --}}

                        {!! HTML::forBranchFeildTTL([
                            'selectBoxShow'=> true,
                            'isRequired'=> true,
                            'elementId' => 'edit_branch_id',
                            'isDisabled' =>false,
                            'divClass'=> "col-sm-4 form-group",
                            'formStyle'=> "vertical"
                        ]) !!}

                        <div class="col-sm-4 form-group" id="department_edit_div">
                            <label class="input-title ">Department</label>
                            <div class="input-group">
                                {!! HTML::forDepartmentFieldHr('edit_department_id','department_id') !!}
                            </div>
                        </div>
        
                        <div id="employee_edit_div" class="col-sm-4 form-group">
                            <label class="input-title ">Employee</label>
                            <div class="input-group">
                                <select id="edit_employee_id" name="employee_id" class="form-control" style="width: 100%">
        
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Application Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="edit_leave_date" name="leave_date" type="text" style="z-index:99999 !important;"
                                    class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
        
                    
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Leave Category</label>
                            <div class="input-group">
                                {!! HTML::forLeaveCategoryHr('edit_leave_cat_id', 'leave_cat_id') !!}
                            </div>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Date From</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="edit_date_from" style="z-index:99999 !important;" name="date_from"
                                    type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    

                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Date To</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="edit_date_to" style="z-index:99999 !important;" name="date_to"
                                    type="text" class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                            </div>
                        </div>

                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Reason</label>
                            <div class="input-group">
                                {!! HTML::forReasonFieldHr(5, 'edit_reason') !!}
                            </div>
                        </div>
        
                        <div id="res_employee_edit_div" class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Responsible Person</label>
                            <div class="input-group">
                                <select id="edit_resp_employee_id" name="resp_employee_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select employee</option>
                                </select>
                            </div>
                        </div>

        
                    

                        <div class="col-sm-4 form-group">

                            <label class="input-title">File Attachment</label>

                            <div class="input-group input-group-file">

                                {!! HTML::forAttachmentFieldHr('edit_attachment') !!}
        
                            </div>
                        </div>
        
                    </div>

                    <div id="attachment" class="row" style="padding-bottom: 5%;">
                        
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

                <div id="summery_div" class="col-sm-4" style="border-left: double black; margin-bottom: 20px;">

                    <div class="row">
                        <div class="col-sm-12">
                            <h4  id="summary_table_header" class="text-center">Leave Summary</h4>

                            <table class="table w-full table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cat. Name</th>
                                        <th>Allocated</th>
                                        <th>Eligible</th>
                                        <th>Consumed</th>
                                        <th>Remaining</th>
                                    </tr>
                                </thead>
        
                                <tbody id="lv_details_table"></tbody>
                            </table>

                            <h6 style="color: #000">
                                Leave Applied For : 
                                    <span id="num_of_leaves_div" style="color: green"> 0 </span> <i>days</i> 
                                
                            </h6>
                        </div>
                    </div>

                </div>

            </div>

        {{-- </div>

    </div> --}}

</form>

<script>


    $(document).ready(function(event){
        window.attData = [];
        window.flag = 0;

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {

                 /*  ==================  */
                let logedInUserId = "{{ $loginUserInfo->emp_id }}";
                if (logedInUserId !== response.result_data.emp_id) {
                    $(".modal-lg").css({"maxWidth": "50%"});
                    $("#apl_div").addClass("col-sm-12");
                    $("#summery_div").addClass("d-none");
    
                } 
                /*  ==================  */
                
                $('#edit_leave_date').val(convertDate(response.result_data.leave_date)); //Application date
                $('#edit_leave_cat_id').val(response.result_data.leave_category.id);
                $('#edit_reason').val(response.result_data.reason);
                $('#edit_description').val(response.result_data.description);
                $('#leave_id').val("{{ $id }}");
                $('#edit_date_from').val(convertDate(response.result_data.date_from));
                $('#edit_date_to').val(convertDate(response.result_data.date_to));


                if (typeof current_emp !== "undefined" && !jQuery.isEmptyObject(current_emp) && response.result_data
                .emp_id == current_emp.id) {
                    console.log('yes');

                    $('#edit_branch_id').prop('disabled', true);
                    $('#edit_employee_id').prop('disabled', true);
                    // $('#edit_resign_date').prop('readonly', true);

                    $('#other_edit_employee_id').prop('disabled', false);
                    $('#other_edit_branch_id').prop('disabled', false);

                    $('#other_edit_employee_id').val(response.result_data.emp_id);
                    $('#other_edit_branch_id').val(response.result_data.branch_id);

                    $('#department_edit_div').hide();
                    $('#branch_edit_div').hide();
                    $('#employee_edit_div').hide();
                    $('#resp_employee_edit_div').hide();

                    // $('.clsSelect2').select2();
                    // $('.datepicker-custom').datepicker();

                }else{
                    console.log('no');

                    if (response.result_data.emp_id > 0) {
                        $('#edit_branch_id').prop('disabled', true);
                        $('#edit_employee_id').prop('disabled', true);
                        // $('#edit_resign_date').prop('readonly', true);

                        $('#other_edit_employee_id').prop('disabled', false);
                        $('#other_edit_branch_id').prop('disabled', false);

                        $('#other_edit_employee_id').val(response.result_data.emp_id);
                        $('#other_edit_branch_id').val(response.result_data.branch_id);

                        $('#department_edit_div').hide();
                        $('#branch_edit_div').hide();
                        $('#employee_edit_div').show();
                        $('#resp_employee_edit_div').hide();

                        // $('.clsSelect2').select2();
                        // $('.datepicker-custom').datepicker();


                    }else{
                        $('#edit_branch_id').prop('disabled', false);
                        $('#edit_employee_id').prop('disabled', false);

                        $('#other_edit_employee_id').prop('disabled', true);
                        $('#other_edit_branch_id').prop('disabled', true);
                        // $('#edit_resign_date').prop('readonly', false);

                        $('#department_edit_div').show();
                        $('#branch_edit_div').show();
                        $('#employee_edit_div').show();
                        $('#resp_employee_edit_div').show();

                        let newOptionBranch = '<option value="" data-select2-id="-1" selected>Select Branch</option>';
                        newOptionBranch += '<option value="1" data-select2-id="-2">All Branch</option>';
                        $('#edit_branch_id').prepend(newOptionBranch).trigger('change');
                        $('#edit_branch_id option:eq(2)').remove();

                        let newOptionDepartment = '<option value="" data-select2-id="-3" selected>Select Department</option>';
                        newOptionDepartment += '<option value="0" data-select2-id="-4">All Department</option>';
                        $('#edit_department_id').prepend(newOptionDepartment).trigger('change');
                        $('#edit_department_id option:eq(2)').remove();

                        let newOptionEmployee = '<option value="0" data-select2-id="-5">All Employee</option>';
                            // newOptionEmployee += '<option value="1" data-select2-id="-6">All Employee</option>';
                        $('#edit_employee_id').prepend(newOptionEmployee).trigger('change');
                        $('#edit_employee_id option:eq(1)').remove();


                   

                    }

                    
                    $('#edit_branch_id').val(response.result_data.branch_id);
                    $('#edit_branch_id').change();

                    // $('.clsSelect2').select2();
                    // $('.datepicker-custom').datepicker();

                    

                }

                $.each(response.result_data.attachments, function(key, file){

                    attData.push(file);

                    let html = '<div class="col-sm-2">' +
                                    '<a onClick="removeAttachment(this)" data-flag = '+ flag +' class="float-right rmv-att-btn"><i class="fa fa-times-circle" style="color:red; cursor: pointer;" aria-hidden="true"></i><a/>' +
                                    '<iframe class="myiFrame" frameBorder="0" scrolling="auto" style="height:100%; width:100%;" src="'+ '{{ url()->current() }}/../../../../' + file.path +'"></iframe>' +
                                '</div>';
                    flag ++;

                    $('#attachment').append(html);

                    $(".myiFrame").on("load", function() {
                        let head = $(".myiFrame").contents().find("head");
                        let css = '<style>img {  width: 100%;} </style>';
                        $(head).append(css);
                    });

                });

                

                showModal({
                    titleContent: "Edit Leave Application",
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

                setTimeout(function() {

                    $("form .clsSelect2").select2({
                        dropdownParent: $("#commonModal")
                    });

                    $('#edit_employee_id').val(response.result_data.emp_id).trigger('change');
                    $('#edit_resp_employee_id').val(response.result_data.resp_emp_id).trigger('change');

                    $('#edit_department_id').val(response.result_data.department_id).trigger('change');

                }, 500);

                configureActionEvents();

            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );


        // $('#edit_date_from, #edit_date_to').change(function(event){
        //     let from_date = $('#edit_date_from').val();
        //     let to_date = $('#edit_date_to').val();

        //     if(from_date != "" && to_date != ""){
        //         let f_arr = from_date.split('-');
        //         let t_arr = to_date.split('-');

        //         let from = new Date(f_arr[2], f_arr[1]-1, f_arr[0]);
        //         let to = new Date(t_arr[2], t_arr[1]-1, t_arr[0]);

        //         let days = (to.getTime() - from.getTime())/ (1000 * 3600 * 24);

        //         if(days >= 0){
        //             $('#num_of_leaves_div').html(days + 1);
        //         }
        //         else if(from_date != null && to_date != null){
        //             swal({
        //                 icon: 'error',
        //                 title: 'Invalid date range!',
        //             });
        //             $(this).val('');
        //         }
        //     }
        // });

    });

    function removeAttachment(node){

        window.attData.forEach((element, index) => {
            if(index == $(node).data('flag')){
                window.attData[index] = null;
            }
        });
        $(node).closest('div').remove();

    }

    $('#edit_attachment').change(function(event){
        let files = event.target.files;

        $.each(files, function(key, file){
            
            attData.push(file);

            let html = '<div class="col-sm-2">' +
                            '<a onClick="removeAttachment(this)" data-flag = '+ flag +' class="float-right rmv-att-btn"><i class="fa fa-times-circle" style="color:red; cursor: pointer;" aria-hidden="true"></i><a/>' +
                            '<iframe class="myiFrame" frameBorder="0" scrolling="auto" style="height:100%; width:100%;" src="'+ URL.createObjectURL(event.target.files[key]) +'"></iframe>' +
                        '</div>';
            flag ++;

            $('#attachment').append(html);

            $(".myiFrame").on("load", function() {
                let head = $(".myiFrame").contents().find("head");
                let css = '<style>img {  width: 100%;} </style>';
                $(head).append(css);
            });
            
        });

    });

    $('#edit_date_from, #edit_employee_id, #edit_leave_date').change(function(event) {
        event.preventDefault();

        // let getEditEmpID = $('#edit_employee_id').val();
        let getEditEmpID =  " {{ $loginUserInfo->emp_id }}";
        if (getEditEmpID > 0) {
            if($(this).val() != null){
                callApi("{{ url()->current() }}/../../getLeaveInfo/"+ " {{ $loginUserInfo->emp_id }}" +"/"+ $('#edit_leave_date').val() +"/api", 'post', new FormData($('#leave_add_form')[0]),
                    function(response, textStatus, xhr) {
                        
                        let html = '';

                        $('#summary_table_header').html('Leave Summary (' + response.emp.emp_name + ')');

                        $.each(response.lv_info.allocated, function(key, alloc){
                            html += '<tr>' +
                                        '<td>'+ key +'</td>' +
                                        '<td>'+ alloc +'</td>' +
                                        '<td>'+ response.lv_info.eligible[key] +'</td>' +
                                        '<td>'+ response.lv_info.consumed[key] +'</td>' +
                                        '<td>'+ (response.lv_info.eligible[key] - response.lv_info.consumed[key]) +'</td>' +
                                    '</tr>';
                        });
                        $('#lv_details_table').html(html);
                    },
                    function(response){
                        $('#lv_details_table').html('');
                        if(response.status == 400){
                            showApiResponse(response.status, JSON.parse(response.responseText).message);
                        }
                    }
                );
            }
        
        }

        
    });
    

    $('#edit_branch_id').change(function(event, emp_id, resp_emp_id) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()), 'get', {},
            function(response, textStatus, xhr) {
                $('#edit_employee_id').val(null).trigger('change');
                $('#edit_resp_employee_id').val(null).trigger('change');

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

                $('#edit_resp_employee_id').select2({
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

            let formData = new FormData($('#leave_edit_form')[0]);
            
            $.each(attData, function(key, file){
                if(file != null && file instanceof File){
                    formData.append('attachment[]', file, file.name);
                }
                else if(file != null){
                    formData.append('fileIds[]', file.id);
                }
            });
            
            callApi("{{ url()->current() }}/../../update/send/api", 'post', formData,
                function(response, textStatus, xhr) {
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            );
            console.log('ok');
        });

        $("#edit_draftBtn").click(function(e) {
            e.preventDefault();

            let formData = new FormData($('#leave_edit_form')[0]);

            $.each(attData, function(key, file){
                if(file != null && file instanceof File){
                    formData.append('attachment[]', file, file.name);
                }
                else if(file != null){
                    formData.append('fileIds[]', file.id);
                }
            });

            callApi("{{ url()->current() }}/../../update/draft/api", 'post', formData,
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
