<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="retirement_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden id="retirement_id" name="retirement_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <input hidden disabled id="other_edit_branch_id" name="branch_id">
                <input hidden disabled id="other_edit_employee_id" name="employee_id">

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

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_retirement_date" name="retirement_date" type="text"
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
                    <label class="input-title RequiredStar">Reason</label>
                    <div class="input-group">
                        {!! HTML::forReasonFieldHr(7, 'edit_reason') !!}
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">

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

    </div>

</form>

<script>
    window.attData = [];
    window.flag = 0;

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

        function(response, textStatus, xhr) {

            $('#edit_retirement_date').val(response.result_data.retirement_date);
            $('#edit_exp_effective_date').val(response.result_data.exp_effective_date);
            $('#edit_reason').val(response.result_data.reason);
            $('#edit_description').val(response.result_data.description);
            $('#retirement_id').val("{{ $id }}");

            $('#edit_branch_id').val(response.result_data.branch_id).trigger('change');

            /*  ==================  */
            let logedInUserId = "{{ $loginUserInfo->emp_id }}";
            if (logedInUserId == response.result_data.emp_id) {

                $('#other_edit_employee_id').prop('disabled', false);
                $('#other_edit_branch_id').prop('disabled', false);
                $('#edit_branch_id').prop('disabled', true);
                $('#edit_employee_id').prop('disabled', true);

                $('#other_edit_employee_id').val({{ $loginUserInfo->emp_id }});
                $('#other_edit_branch_id').val({{ $loginUserInfo->branch_id }});
                
            } else {
                $('#other_edit_employee_id').prop('disabled', true);
                $('#other_edit_branch_id').prop('disabled', true);
                $('#edit_branch_id').prop('disabled', false);
                $('#edit_employee_id').prop('disabled', false);
            }
            /*  ==================  */

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

            setTimeout(function() {

                $("form .clsSelect2").select2({
                    dropdownParent: $("#commonModal")
                });

                $('#edit_employee_id').val(response.result_data.emp_id).trigger(
                    'change');
            }, 1200);

            showModal({
                titleContent: "Edit Retirement Application",
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

    function removeAttachment(node){

        window.attData.forEach((element, index) => {
            if(index == $(node).data('flag')){
                window.attData[index] = null;
            }
        });
        console.log(window.attData);
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

    function configureActionEvents() {
        $('#edit_sendBtn').click(function(e) {
            e.preventDefault();

            let formData = new FormData($('#retirement_edit_form')[0]);

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
        });

        $("#edit_draftBtn").click(function(e) {
            e.preventDefault();

            let formData = new FormData($('#retirement_edit_form')[0]);

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

    $("#edit_exp_effective_date").on('change', function() {
        let appDate = $("#edit_retirement_date").val();
        let effDate = $("#edit_exp_effective_date").val();
        
        if (effDate < appDate) {
            $("#edit_exp_effective_date").val(' ');
            swal(
                'Sorry!!!',
                'Effected date is not less',
                'error'
            )
        }
    })
</script>
