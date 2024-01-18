
<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php
    use App\Services\HrService as HRS;

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get(); //title

    $rectuitmentType = HRS::getRectuitmentTypeData();
    $id = $viewData->id;
    // ss($rectuitmentType);
@endphp

<form id="payroll_config_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden name="edit_id" value="{{ $viewData->id }}">
    <div class="row">

        <div class="col-sm-12">

            <div class="row">
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Group</label>
                    <div class="input-group">
                        <select name="group_id" id="view_group_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Group</option>
                            @foreach ($groups as $val)
                            <option value="{{ $val->id }}">{{ $val->group_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" id="view_company_id" class="form-control clsSelect2" style="width: 100%" disabled>
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
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="view_project_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Rectuitment Type</label>
                    <div class="input-group">
                        <select name="rectuitment_type" id="view_rectuitment_type" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="" selected disabled>Select Rectuitment Type</option>
                            @foreach ($rectuitmentType as $val)
                            <option value="{{ $val->value_field }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Employee Type</label>
                    <div class="input-group">
                        <select name="employee_type" id="view_employee_type" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="" selected disabled>Select Employee Type</option>
                            <option value="permanent">Regular</option>
                            <option value="contractual">Contractual</option>
                            <option value="internship">Internship</option>
                            <option value="provisional">Provisional</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group ">
                    <label class="input-title RequiredStar">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="edit_exp_effective_date" style="z-index:99999 !important;" name="eff_date_start"
                            type="text" class="form-control datepicker-custom" placeholder="DD-MM-YYYY" disabled>
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>

<script>

    $(document).ready(function(){

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {
                
                $("#view_group_id").val(response.group_id).trigger('change');
                $("#view_company_id").val(response.company_id).trigger('change');
                $("#view_project_id").val(response.project_id).trigger('change');

                $("#view_rectuitment_type").val(response.rectuitment_type).trigger('change');
                $("#view_employee_type").val(response.employee_type).trigger('change');
                
                $("#edit_exp_effective_date").val(response.eff_date_start);

                
            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    showModal({
        titleContent: "View Payroll Configuration"
    });

    

</script>
