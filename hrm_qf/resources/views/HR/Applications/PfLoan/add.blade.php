<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
$preFix = last(request()->segments()) == 'others' ? '../../' : '../';
?>

<form id="pf_loan_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                {{-- <div id="branch_add_div" class="col-sm-5 form-group">
                    <div class="input-group">
                        {!! HTML::forCompanyFeild(null, 'false', true, 'company_id', 'company_id') !!}
                    </div>
                </div> --}}
                <div id="branch_add_div" class="col-sm-5 form-group">
                    <div class="input-group">
                        {!! HTML::forBranchFeildNew(true, 'branch_id', 'add_branch_id','','','Branch') !!}
                    </div>
                </div>

                <div id="employee_add_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select id="add_project_id" name="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Project</option>
                            <option value="1">Project 1</option>
                            <option value="2">Project 2</option>
                        </select>
                    </div>
                </div>

            
                <div id="employee_add_div" class="col-sm-5  form-group">
                    <label class="input-title RequiredStar">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>


                <div class="col-sm-5 form-group offset-sm-2">
                    <label class="input-title RequiredStar">Employment Age</label>
                    <div class="input-group">
                        <input id="add_employment_age" name="employment_age" type="text" style="z-index:99999 !important;" class="form-control" placeholder="">
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Org. Contribution Amount</label>
                    <div class="input-group">
                        <input id="add_org_contribution_amount"  name="org_contribution_amount" style="z-index:99999 !important;" type="text" class="form-control" placeholder="">
                    </div>
                </div>
            
                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Employee PF Amount</label>
                    <div class="input-group">
                        <input id="add_emp_pf_amount" name="emp_pf_amount" type="text" style="z-index:99999 !important;" class="form-control" placeholder="">
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Eligible Amount</label>
                    <div class="input-group">
                        <input id="add_eligible_amount"  name="eligible_amount" style="z-index:99999 !important;" type="text" class="form-control" placeholder="">
                    </div>
                </div>
            
                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Interest Amount</label>
                    <div class="input-group">
                        <input id="add_interest_amount" name="interest_amount" type="text" style="z-index:99999 !important;" class="form-control" placeholder="">
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title RequiredStar">Requested Loan Amount</small> </label>
                    <div class="input-group">
                        <input id="add_requested_loan_amount"  name="requested_loan_amount" style="z-index:99999 !important;" type="text" class="form-control" placeholder="">
                    </div>
                </div>
            
                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Requested No of Loan Installment</label>
                    <div class="input-group">
                        <input id="add_requested_no_of_loan_installment" name="requested_no_of_loan_installment" type="text" style="z-index:99999 !important;" class="form-control" placeholder="">
                    </div>
                </div>

                
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_application_date_tmp" name="application_date_tmp" type="text"
                            style="z-index:99999 !important;" class="form-control  datepicker-custom"
                            placeholder="DD-MM-YYYY" readonly disabled>
                        <input type="text" id="add_application_date" name="application_date" value="" hidden>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Expected Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date" type="text" class="form-control  datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>


                <div class="col-sm-5 form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file">
                        {!! HTML::forAttachmentFieldHr('add_attachment') !!}
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-12  form-group">
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
        $("#add_application_date").val("{{ date('d-m-Y') }}");
        $("#add_application_date_tmp").val("{{ date('d-m-Y') }}");

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
            configureSelfApplicationForm();
            // swal(
            //     'Sorry!!!',
            //     'This user is not valid employee.',
            //     'error'
            // )
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


    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {

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

                if (appFor == "others") {
                    $('#add_employee_id').val('').trigger('change');
                } else {
                    $('#add_employee_id').val({{ $loginUserInfo->emp_id }}).trigger('change');
                }
                

            }
        );
    });

    $('#add_sendBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#pf_loan_add_form')[0]);

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

        let formData = new FormData($('#pf_loan_add_form')[0]);

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
    });

    function configureSelfApplicationForm() {

        // $('#other_add_employee_id').prop('disabled', false);
        // $('#other_add_branch_id').prop('disabled', false);
        // $('#add_branch_id').prop('disabled', true);
        // $('#add_employee_id').prop('disabled', true);
        

        // $('#other_add_employee_id').val({{ $loginUserInfo->emp_id }});
        // $('#other_add_branch_id').val({{ $loginUserInfo->branch_id }});

        // setTimeout(function() {
        //     $('#add_branch_id').val({{ $loginUserInfo->branch_id }}).trigger('change');
        // }, 500);

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add PF Loan Application",
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
            titleContent: "Add PF Loan Application",
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


    $("#add_exp_effective_date").on('change', function() {
        let appDate = $("#add_promotion_date").val();
        let effDate = $("#add_exp_effective_date").val();

        if (effDate < appDate) {
            $("#add_exp_effective_date").val(' ');
            swal(
                'Sorry!!!',
                'Effected date is not less',
                'error'
            )
        }
    })
</script>
