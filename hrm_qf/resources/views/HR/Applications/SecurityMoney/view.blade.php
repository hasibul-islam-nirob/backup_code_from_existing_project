<?php
    use App\Services\HtmlService as HTML;
    $loginUserInfo = Auth::user();
    $preFix = last(request()->segments()) == 'others' ? '../../' : '../';
?>


<form id="security_money_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">


            <div class="row">
                <input hidden id="security_money_id" name="security_money_id">

                {{-- <div id="branch_add_div" class="col-sm-5 form-group">
                    <div class="input-group">
                        {!! HTML::forCompanyFeild(null, 'false', true, 'company_id', 'company_id') !!}
                    </div>
                </div> --}}

                <div id="branch_edit_div" class="col-sm-5 form-group">
                    <div class="input-group">
                        {!! HTML::forBranchFeildNew(true, 'branch_id', 'edit_branch_id','','','Branch','','',false) !!}
                    </div>
                </div>

                <div id="project_edit_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title ">Project</label>
                    <div class="input-group">
                        <select id="edit_project_id" disabled name="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="" selected >Select Project</option>
                            <option value="1">Project 1</option>
                            <option value="2">Project 2</option>
                        </select>
                    </div>
                </div>


                <div id="employee_edit_div" class="col-sm-5 form-group">
                    <label class="input-title ">Employee</label>
                    <div class="input-group">
                        <select id="edit_employee_id" disabled name="employee_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>


                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title ">Collection Amount</label>
                    <div class="input-group">
                        <input id="edit_collection_amount" name="collection_amount" type="text" style="z-index:99999 !important;" class="form-control" placeholder="" disabled>
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title ">Collection Month</label>
                    <div class="input-group">
                        <input id="edit_collection_month"  name="collection_month" style="z-index:99999 !important;" type="text" class="form-control" placeholder="" disabled>
                    </div>
                </div>
            
                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title ">Collection Type</label>
                    <div class="input-group">
                        <select name="collection_type" id="edit_collection_type" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="" selected disabled>Select One</option>
                            <option value="advanced">Advanced</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>

                <div id="collection_by_edit_div" class="col-sm-5 form-group">
                    <label class="input-title ">Collection By</label>
                    <div class="input-group">
                        <select id="edit_collection_by_employee_id" name="collection_by" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>


                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title ">Application Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_application_date_tmp" name="application_date_tmp" type="text"
                            style="z-index:99999 !important;" class="form-control  datepicker-custom"
                            placeholder="DD-MM-YYYY" readonly disabled>

                        <input type="text" id="edit_application_date" name="application_date" value="" hidden>
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title">Expected Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar " aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="exp_effective_date"
                            type="text" class="form-control  datepicker-custom" placeholder="DD-MM-YYYY" disabled>
                    </div>
                </div>


                <div class="col-sm-5 offset-sm-2 form-group  d-none">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file">

                        {!! HTML::forAttachmentFieldHr('add_attachment') !!}

                    </div>
                    
                </div>

            </div>

            <div class="row">

                <div class="col-sm-12 form-group">
                    <label class="input-title">Description</label>
                    <div class="input-group">
                        <div class="input-group">
                            <textarea rows="5" id="edit_description" name="description" class="form-control"
                                style="width: 100%" disabled></textarea>
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
    $("#edit_branch_id").attr('disabled', true);
    $(document).ready(function(){
        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {

                $('#edit_branch_id').val(response.result_data.branch_id).trigger('change');
                $('#edit_department_id').val(response.result_data.department_id).trigger('change');
                $('#edit_project_id').val(response.result_data.project_id).trigger('change');

                setTimeout(function() {
                    $("form .clsSelect2").select2({
                        dropdownParent: $("#commonModal")
                    });
                    $('#edit_employee_id').val(response.result_data.emp_id).trigger('change');
                    $('#edit_collection_by_employee_id').val(response.result_data.collection_by).trigger('change');
                }, 500);

                $('#edit_collection_amount').val(response.result_data.collection_amount);
                $('#edit_collection_month').val(response.result_data.collection_month);
                $('#edit_collection_type').val(response.result_data.collection_type).trigger('change');

                $('#edit_application_date_tmp').val(response.result_data.application_date);
                $('#edit_application_date').val(response.result_data.application_date);
                $('#edit_exp_effective_date').val(response.result_data.effective_date);
                $('#edit_appl_date').val(response.result_data.appl_date);
                $('#edit_description').val(response.result_data.description);

                $('#security_money_id').val("{{ $id }}");
               
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
                }, 1200);


                showModal({
                    titleContent: "View Security Money Application",
                });


            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );


    })

    $('#edit_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()), 'get', {},
            function(response, textStatus, xhr) {

                $('#edit_employee_id, #edit_collection_by_employee_id').select2({
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
                $('#edit_employee_id').val(null).trigger('change');
                $('#edit_collection_by_employee_id').val(null).trigger('change');
            }

        );
    });

</script>