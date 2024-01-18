
<style>
    .modal-lg {
        max-width: 50%;
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
@endphp

<form id="pf_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Group</label>
                    <div class="input-group">
                        <select name="group_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Group</option>
                            @foreach ($groups as $val)
                            <option value="{{ $val->id }}">{{ $val->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Company</label>
                    <div class="input-group">
                        <select name="company_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option value="{{ $val->id }}">{{ $val->comp_name }}</option>
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
                            <option value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Rectuitment Type</label>
                    <div class="input-group">
                        <select multiple name="rec_type_id[]" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Rectuitment Type</option>
                            @foreach ($rec_type as $val)
                            <option value="{{ $val->id }}">{{ $val->title }}</option>
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
                            <option value="percentage">Percentage</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Calculation Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="calculation_amount">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Employee Contribution After (month)</label>
                    <div class="input-group">
                        <select name="emp_cont_after_m" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=11; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Org. Contribution After (month)</label>
                    <div class="input-group">
                        <select name="org_cont_after_m" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
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
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Loan Withdraw (Percentage)</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="loan_wit_percentage">
                    </div>
                </div>

            </div>
            
            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Loan Early Settlement Percentage</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="loan_early_sett_percentage">
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Org. PF Withdraw Min Job year</label>
                    <div class="input-group">
                        <select name="org_wit_min_job_y" class="form-control clsSelect2" style="width: 100%">
                            @for($i = 0; $i<=10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
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
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Interest Rate</label>
                    <div class="input-group">
                        <input class="form-control" value="" name="interest_rate">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Method</label>
                    <div class="input-group">
                        <select name="method" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Method</option>
                            <option value="flat">Flat</option>
                            <option value="decline">Decline</option>
                        </select>
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
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="effective_date"
                            type="text" class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                    </div>
                </div>

            </div>

            <div class="row d-none">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Salary Structure</label>
                    <div class="input-group">
                        <select name="salary_structure" class="form-control clsSelect2" style="width: 100%">
                            <option value="1">Enable</option>
                            <option selected value="0">Disable </option>
                        </select>
                    </div>
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

        $('.clsSelect2').select2();
    });

    showModal({
        titleContent: "Add PF",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'add_saveBtn',
            }
        }),
    });

    $('#add_saveBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#pf_form')[
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
