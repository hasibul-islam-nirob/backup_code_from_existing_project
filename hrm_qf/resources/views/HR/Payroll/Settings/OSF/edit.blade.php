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
@endphp

<form id="wf_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{ $editData->id }}">

    <div class="row">

        <div class="col-sm-12">

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

                <div class="col-sm-5  form-group">
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

            </div>

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
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

                <div class="col-sm-5  form-group">
                    <label class="input-title">Calculation Type</label>
                    <div class="input-group">
                        <select name="calculation_type" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Calculation Type</option>
                            <option {{ ($editData->calculation_type == 'percentage') ? 'selected' : '' }} value="percentage">Percentage</option>
                            <option {{ ($editData->calculation_type == 'fixed') ? 'selected' : '' }} value="fixed">Fixed</option>
                        </select>
                    </div>
                </div>


            </div>

            <div class="row">

                
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Calculation Amount</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->calculation_amount }}" name="calculation_amount">
                    </div>
                </div>

                <div class="col-sm-5  form-group">
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
        titleContent: "Edit OSF Settings",
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