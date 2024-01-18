
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
    $permanentNonPermanentData = HRS::getPermanentNonPermanentData();
    // ss($permanentNonPermanentData);
    $permanentArr = !empty($editData->permanent) ? explode(',', $editData->permanent) : [];
    // dd($permanentArr, $permanentNonPermanentData);
    $id = $editData->id;
@endphp

<form id="payroll_config_edit_menu_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <input hidden name="edit_id" value="{{ $editData->id }}">
    <div class="row">

        <div class="col-sm-12">

            <div class="row">
                
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" id="company_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option value="{{ $val->id }}">{{ $val->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                

           
                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Permanent</label>
                    <div class="input-group">
                        <select multiple name="permanent[]" id="edit_permanent" class="form-control clsSelect2" style="width: 100%">
                            <option value=""  disabled>Select </option>
                            @foreach ($permanentNonPermanentData as $val)
                            <option value="{{ $val->value_field }}" >{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Non Permanent</label>
                    <div class="input-group">
                        <select multiple name="nonpermanent[]" id="edit_nonpermanent" class="form-control clsSelect2" style="width: 100%">
                            <option value=""  disabled>Select </option>
                            @foreach ($permanentNonPermanentData as $val)
                            <option value="{{ $val->value_field }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

           
            <div class="row">
                <div class="col-sm-5 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Effective Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="eff_date_start" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>

        </div>

    </div>

</form>

<script>

    // getPayrollSettingStatus('edit_permanent', '{{ url()->current() }}/..');
    // getPayrollSettingStatus('edit_nonpermanent', '{{ url()->current() }}/..');

    $(document).ready(function(){

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',

            function(response, textStatus, xhr) {
                
                $("#group_id").val(response.group_id).trigger('change');;
                $("#company_id").val(response.company_id).trigger('change');;
                $("#project_id").val(response.project_id).trigger('change');;
                // $("#rectuitment_type").val(response.);
                // $("#permanent").val(response.permanent);
                // $("#nonpermanent").val(response.nonpermanent);
                $("#add_exp_effective_date").val(response.eff_date_start);

                if (response.permanent != null) {
                    let permanentArr = response.permanent.split(',')
                    $("#edit_permanent").val(permanentArr);
                    $("#edit_permanent").trigger('change');
                    
                }else{
                    let permanentArr = [];
                }

                if (response.nonpermanent != null) {
                    let nonpermanentArr = response.nonpermanent.split(',')
                    $("#edit_nonpermanent").val(nonpermanentArr);
                    $("#edit_nonpermanent").trigger('change');

                }else{
                    let nonpermanentArr = [];
                }
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
        titleContent: "Edit  Payroll Configuration",
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
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#payroll_config_edit_menu_form')[
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