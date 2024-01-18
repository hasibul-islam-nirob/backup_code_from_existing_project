<?php
use App\Services\HtmlService as HTML;
// $loginUserInfo = Auth::user();
$loginUserInfo = Auth::user();
$preFix = last(request()->segments()) == 'others' ? '../../' : '../';
?>

<form id="transfer_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row" id="applicationForOthers">

                <input hidden disabled id="other_add_branch_id" name="add_branch_from_id">
                <input hidden disabled id="other_add_employee_id" name="employee_id">

                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'elementTitle' => 'Branch From',
                    'elementName' => 'add_branch_from_id',
                    'elementId' => 'branch_from_id',
                    'divClass'=> "col-sm-5 form-group",
                    'formStyle'=> "vertical"
                ]) !!}

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">

                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'elementTitle' => 'Branch To',
                    'elementName' => 'add_branch_to_id',
                    'elementId' => 'branch_to_id',
                    'divClass'=> "col-sm-5 form-group",
                    'formStyle'=> "vertical",
                    'transferToLoadFromBranch' => true
                ]) !!}

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Transfer Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_transfer_date" name="transfer_date" type="text" style="z-index:99999 !important;"
                            class="form-control round datepicker-custom" placeholder="DD-MM-YYYY">
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
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date" type="text" class="form-control round datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file">

                        {!! HTML::forAttachmentFieldHr('add_attachment') !!}

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

    $(document).ready(function(){
        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    window.appFor = "{{ $appFor }}";
    if (appFor == "others") {
        configureOthersApplicationForm();
    } else {
        if ("{{ $loginUserInfo->emp_id }}" !== "") {
            configureSelfApplicationForm();
        } else {
            swal(
                'Sorry!!!',
                'This user is not valid employee.',
                'error'
            )
        }
    }

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

    // showModal({
    //     titleContent: "Add Transfer Application",
    //     footerContent: getModalFooterElement({
    //         'btnNature': {
    //             0: 'send',
    //             1: 'save',
    //         },
    //         'btnName': {
    //             0: 'Send',
    //             1: 'Draft',
    //         },
    //         'btnId': {
    //             0: 'add_sendBtn',
    //             1: 'add_draftBtn',
    //         }
    //     }),
    // });



    $('#branch_from_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                // $('#add_employee_id').val(null).trigger('change');
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

    $("#branch_to_id, #branch_from_id").on('change', function(){
        let barnchToId = $("#branch_to_id").val();
        let branchFromID = $('#branch_from_id').val();

        if(barnchToId == branchFromID){

            swal({
                icon: 'warning',
                title: 'Oops...',
                text: 'Branch from and branch to both are same'
            });

            $('#add_sendBtn').addClass("disabled")
            $('#add_draftBtn').addClass("disabled")
        }else{
            $('#add_sendBtn').removeClass("disabled")
            $('#add_draftBtn').removeClass("disabled")
        }
    })



    $('#add_sendBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#transfer_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/send/api", 'post', formData, function(response, textStatus, xhr) {
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

        let formData = new FormData($('#transfer_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/draft/api", 'post', formData, function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });

    function configureSelfApplicationForm() {

        // $('#branch_from_id').prop('disabled', true);
        $('#add_employee_id').prop('disabled', true);
        $('#other_add_employee_id').prop('disabled', false);
        // $('#other_add_branch_id').prop('disabled', false);

        setTimeout(function() {
            $('#branch_from_id').val({{ $loginUserInfo->branch_id }}).trigger('change');
        }, 500);

        $('#other_add_employee_id').val({{ $loginUserInfo->emp_id }});

        $("#applicationForOthers").hide();

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Transfer Application For Me",
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

    }

    function configureOthersApplicationForm() {

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Transfer Application Others",
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
    }

</script>
