<?php
use App\Services\HtmlService as HTML;
?>

<form id="demotion_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
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

                <div id="current_designation_add_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Current Designation</label>
                    <div class="input-group">
                        <input hidden id="add_current_designation_id_hidden" name="current_designation_id">
                        <input readonly id="add_current_designation_id" type="text" class="form-control round">
                    </div>
                </div>

                <div id="designation_to_demote_add_div" class="col-sm-5 form-group offset-sm-2">
                    <label class="input-title RequiredStar">Designation To Demote</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('add_designation_to_demote_id', 'designation_to_demote_id') !!}
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Demotion Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_demotion_date" name="demotion_date" type="text" style="z-index:99999 !important;"
                            class="form-control round datepicker-custom" placeholder="DD-MM-YYYY">
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
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file">

                        {!! HTML::forAttachmentFieldHr('add_attachment') !!}

                    </div>
                    
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title">Description</label>
                    <div class="input-group">
                        <div class="input-group">
                            <textarea rows="5" id="add_description" name="description" class="form-control"
                                style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div id="attachment" class="row" style="padding-bottom: 5%;">
                        
            </div>

        </div>

    </div>

</form>

<script>
    $(document).ready(function(){

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Demotion Application",
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

    $('#add_attachment').change(function(event){
        let files = event.target.files;

        $.each(files, function(key, file){
            
            attData.push(file);

            let html = '<div class="col-sm-2">' +
                            // '<a onClick="removeAttachment(this)" data-flag = '+ flag +' class="btn btn-xs float-right rmv-att-btn">&times;<a/>' +
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

    function removeAttachment(node){

        window.attData.forEach((element, index) => {
            if(index == $(node).data('flag')){
                window.attData[index] = null;
            }
        });

        $(node).closest('div').remove();

    }

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

        let formData = new FormData($('#demotion_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });
        
        callApi("{{ url()->current() }}/../insert/send/api", 'post', new FormData($('#demotion_add_form')[0]),
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

        let formData = new FormData($('#demotion_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/../insert/draft/api", 'post', formData,
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

    $('#add_employee_id').change(function(event) {

        if ($(this).val()) {
            let url = "{{ route('get_des_by_emp_id', ':boId') }}";
            url = url.replace(':boId', $(this).val());
            callApi(url, 'post', "",
                function(response, textStatus, xhr) {
                    $('#add_current_designation_id').val(response.result_data.name);
                    $('#add_current_designation_id_hidden').val(response.result_data.id);
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            );
        } else {
            $('#add_current_designation_id').val("");
            $('#add_current_designation_id_hidden').val("");
        }
    })
</script>
