<style>
    .modal-lg {
        max-width: 60%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get(); //title

    //dd($editData);
@endphp

<form id="wf_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{ $editData->id }}">

    <div class="row">

        <div class="col-sm-11 offset-sm-1">

            <div class="row">

               
    
                <div class="col-sm-5 offset-sm-1  form-group">
                    <label class="input-title">Company</label>
                    <div class="input-group">
                        <select name="company_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option {{ ($val->id == $editData->company_id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Project</label>
                    <div class="input-group">
                        <select name="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option {{ ($val->id == $editData->project_id) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Rectuitment Type</label>
                    <div class="input-group">
                        <select multiple name="rec_type_id[]" id="edit_recruitment_type_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Rectuitment Type</option>
                            @foreach ($rec_type as $val)
                            <option {{ (in_array($val->id, explode(',', $editData->rec_type_ids))) ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Calculation Type</label>
                    <div class="input-group">
                        <select name="calculation_type" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Calculation Type</option>
                            <option {{ ('percentage' == $editData->calculation_type) ? 'selected' : '' }} value="percentage">Percentage</option>
                            <option {{ ('fixed' == $editData->calculation_type) ? 'selected' : '' }} value="fixed">Fixed</option>
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Calculation Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->calculation_amount }}" name="calculation_amount">
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Employee Contribution After (month)</label>
                    <div class="input-group">
                        <select name="emp_cont_after_m" class="form-control clsSelect2" style="width: 100%">
                            <option value="join_date" {{$editData->emp_cont_after_m == 'join_date' ? 'selected' : ''}}>From Joing</option>
                            <option value="join_permanent" {{$editData->emp_cont_after_m == 'join_permanent' ? 'selected' : ''}}>From Permanent</option>
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Org. Contribution After (month)</label>
                    <div class="input-group">
                        <select name="org_cont_after_m" class="form-control clsSelect2" style="width: 100%">
                            <option value="join_date" {{$editData->org_cont_after_m == 'join_date' ? 'selected' : ''}}>From Joing</option>
                            <option value="join_permanent" {{$editData->org_cont_after_m == 'join_permanent' ? 'selected' : ''}}>From Permanent</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Emp. PF Withdraw Min Job year</label>
                    <div class="input-group">
                        <select name="emp_wit_min_job_y" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=10; $i++)
                            <option {{ ($i == $editData->emp_wit_min_job_y) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title">Org. PF Withdraw Min Job year</label>
                    <div class="input-group">
                        <select name="org_wit_min_job_y" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=10; $i++)
                            <option {{ ($i == $editData->org_wit_min_job_y) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

            </div>

            
            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Loan Withdraw Min Month</label>
                    <div class="input-group">
                        <select name="loan_wit_min_m" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=12; $i++)
                            <option {{ ($i == $editData->loan_wit_min_m) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Loan Withdraw (Percentage)</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->loan_wit_percentage }}" name="loan_wit_percentage">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Loan Interest Rate</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->interest_rate }}" name="interest_rate">
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title">Loan Interest Rate Calculation Method</label>
                    <div class="input-group">
                        <select name="method" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Method</option>
                            <option {{ ('flat' == $editData->method) ? 'selected' : '' }} value="flat">Flat</option>
                            <option {{ ('decline' == $editData->method) ? 'selected' : '' }} value="decline">Decline</option>
                        </select>
                    </div>
                </div>

            </div>
            
            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Loan Early Settlement Percentage</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->loan_early_sett_percentage }}" name="loan_early_sett_percentage">
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input value="{{ $editData->effective_date }}" style="z-index:99999 !important;" name="effective_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>


            </div>

            
           

        </div>

    </div>

</form>


<script>

    // checkRecruitment('edit_recruitment_type_id');
    
    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    showModal({
        titleContent: "Edit PF",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'update',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'edit_updateBtn',
            }
        }),
    });

    $('#edit_updateBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#wf_form')[
                0]),
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