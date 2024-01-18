<?php
use App\Services\HtmlService as HTML;
?>

<form id="transfer_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden id="transfer_id" name="transfer_id">

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                {{-- <div id="branch_edit_div" class="col-sm-5 form-group">
                    <div class="input-group">
                        {!! HTML::forBranchFeildNew(true, 'edit_branch_id', 'branch_from_id','','','Branch From') !!}
                    </div>
                </div> --}}
                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'elementTitle' => 'Branch From',
                    'elementName' => 'add_branch_from_id',
                    'elementId' => 'branch_from_id',
                    'divClass'=> "col-sm-5 form-group",
                    'formStyle'=> "vertical"
                ]) !!}

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


                {{-- <div class="col-sm-5 offset-sm-2 form-group">
                    <div class="input-group">
                        {!! HTML::forBranchFeildNew(true, 'edit_branch_to_id', 'branch_to_id','','','Branch To') !!}
                    </div>
                </div> --}}
                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'elementTitle' => 'Branch To',
                    'elementName' => 'add_branch_to_id',
                    'elementId' => 'branch_to_id',
                    'divClass'=> "col-sm-5 offset-sm-2 form-group",
                    'formStyle'=> "vertical",
                    'transferToLoadFromBranch' => true
                ]) !!}

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
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date" type="text" class="form-control round datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
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

            $('#edit_transfer_date').val(response.result_data.transfer_date);
            $('#edit_exp_effective_date').val(response.result_data.exp_effective_date);
            $('#edit_description').val(response.result_data.description);
            $('#transfer_id').val("{{ $id }}");
            $('#branch_to_id').val(response.result_data.branch_to_id).trigger('change');

            $('#branch_from_id').val(response.result_data.branch_id).trigger('change');

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

    $('#branch_from_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
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

    $("#branch_to_id, #branch_from_id").on('change', function(){
        let barnchToId = $("#branch_to_id").val();
        let branchFromID = $('#branch_from_id').val();
        
        if(barnchToId == branchFromID){

            swal({
                icon: 'warning',
                title: 'Oops...',
                text: 'Branch from and branch to both are same'
            });

            $('#edit_sendBtn').addClass("disabled")
            $('#edit_draftBtn').addClass("disabled")
        }else{
            $('#edit_sendBtn').removeClass("disabled")
            $('#edit_draftBtn').removeClass("disabled")
        }
    })

    function configureActionEvents() {

        $('#edit_sendBtn').click(function(e) {
            e.preventDefault();

            let formData = new FormData($('#transfer_edit_form')[0]);

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

            let formData = new FormData($('#transfer_edit_form')[0]);

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
