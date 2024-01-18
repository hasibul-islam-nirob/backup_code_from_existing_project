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

    $donationSector = DB::table('hr_payroll_settings_donation')->where([['is_active', 1],['is_delete', 0]])->get(); // Donation Sector
    $getDonationFromWF = DB::table('hr_payroll_settings_wf_details')->where([['wf_id', $editData->id],['don_sector','!=', null]])->get(); // Get Donation Sector from WF Details

    
    $newArr = [];
    foreach($donationSector as $donSec){
        foreach($getDonationFromWF as $dwf){

            if($donSec->id == $dwf->don_sector){
                array_push($newArr, $dwf->don_sector);
            }

        }
    }

    // dd($donationSector, $getDonationFromWF, $newArr);

    $grade = DB::table('hr_config')->where([['title', 'grade']])->first()->content;
    $level = DB::table('hr_config')->where([['title', 'level']])->first()->content;

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

            <div class="row d-none">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title">Interest Rate</label>
                    <div class="input-group">
                        <input class="form-control" value="{{ $editData->interest_rate }}" name="interest_rate">
                    </div>
                </div>
    
                <div class="col-sm-5 form-group">
                    <label class="input-title">Method</label>
                    <div class="input-group">
                        <select name="method" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Method</option>
                            <option {{ ('flat' == $editData->method) ? 'selected' : '' }} value="flat">Flat</option>
                            <option {{ ('decline' == $editData->method) ? 'selected' : '' }} value="decline">Decline</option>
                        </select>
                    </div>
                </div>

            </div>

            
            @php
                $detailsData = $editData->wf_details;
                $detailsData = $detailsData->groupBy('data_type');
                // dd($detailsData, $donationSector);
            @endphp

            <br>

            <div class="row">

                <div class="col-sm-11" id="col_div_1">

                    <div class="row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Type</label>
                        </div>
            
                        <div class="col-sm-2 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Grade</label>
                        </div>
            
                        <div class="col-sm-2 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Level</label>
                        </div>
            
                        <div class="col-sm-2 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Calculation Type</label>
                        </div>
            
                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Amount</label>
                        </div>
            
                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            
                        </div>
                        
                    </div>

                    @foreach ($detailsData['calculation'] as $key => $val)
                    <div class="row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="type[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Type</option>
                                    <option {{ ($val->type == 'wf_rf') ? 'selected' : '' }} value="wf_rf">WF Returnable</option>
                                    <option {{ ($val->type == 'wf_nrf') ? 'selected' : '' }} value="wf_nrf">WF Not Returnable</option>
                                    {{-- <option {{ ($val->type == 'wf') ? 'selected' : '' }} value="wf">WF</option> --}}
                                    <option {{ ($val->type == 'wf_contri') ? 'selected' : '' }} value="wf_contri">WF Contri.</option>
                                </select>
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="grade[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Grade</option>
                                    <option {{ ($val->grade == 0) ? 'selected' : '' }} value="0">All</option>
                                    @for ($i = 1; $i <= $grade; $i++)
                                    <option {{ ($val->grade == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="level[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Level</option>
                                    <option {{ ($val->level == 0) ? 'selected' : '' }} value="0">All</option>
                                    @for ($i = 1; $i <= $level; $i++)
                                    <option {{ ($val->level == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="calculation_type[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Calculation Type</option>
                                    <option {{ ($val->calculation_type == 'fixed') ? 'selected' : '' }} value="fixed">Fixed</option>
                                    <option {{ ($val->calculation_type == 'percentage') ? 'selected' : '' }} value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="{{ $val->amount }}" name="amount[]">
                            </div>
                        </div>

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="removeRow(this)" class="">
                                    <i class="fa fa-minus-circle" style="color: red;"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    @endforeach

                    <div class="row" id="col_div_1_row">

                        <div class="col-sm-2 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="type[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Type</option>
                                    <option value="wf">WF</option>
                                    <option value="wf_contri">WF Contri.</option>
                                </select>
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="grade[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Grade</option>
                                    @for ($i = 1; $i <= $grade; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
            
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="level[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Level</option>
                                    @for ($i = 1; $i <= $level; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-2 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="calculation_type[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Calculation Type</option>
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="amount[]">
                            </div>
                        </div>
            
                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="addCol_1Row(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>

            <br>

            <div class="row">

                <div class="col-sm-11" id="col_div_2">

                    <div class="row">

                        <div class="col-sm-5 offset-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Donation Sector</label>
                        </div>
            
                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Amount</label>
                        </div>
            
                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            
                        </div>
                        
                    </div>

                    <!-- Start New Section  -->
                    @foreach ($getDonationFromWF as $dwf)

                        <div class="row" style="">

                            <div class="col-sm-5 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                                <div class="input-group">
                                    <select name="don_sector[]" class="form-control clsSelect2" style="width: 100%">
                                        <option value="" selected>Select Donation Sector</option>
                                       
                                        @foreach ($donationSector as $donSecData)

                                            @if ($dwf->don_sector == $donSecData->id)
                                                <option selected value="{{$donSecData->id}}" > {{$donSecData->sector_name}} </option>
                                            @else
                                                <option  value="{{$donSecData->id}}" > {{$donSecData->sector_name}} </option>
                                            @endif
                                
                                        @endforeach
                                        
                                    </select>
                                </div>
                            </div>
            
                            <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                                <div class="input-group">
                                    <input class="form-control" value="{{ $dwf->amount }}" name="don_amount[]">
                                </div>
                            </div>
    
                            <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                                <span class="addBtnClass">
                                    <a onclick="removeRow(this)" class="">
                                        <i class="fa fa-minus-circle" style="color: red;"></i>
                                    </a>
                                </span>
                            </div>
                            
                        </div>

                    @endforeach
                    <!-- End New Section  -->


                    <div class="row" id="col_div_2_row" style="">

                        <div class="col-sm-5 offset-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="don_sector[]" class="form-control clsSelect2" style="width: 100%">
                                    <option value="" selected disabled>Select Donation Sector</option>
                                    @foreach ($donationSector as $donationData)
                                        <option value="{{$donationData->id}}" > {{$donationData->sector_name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input class="form-control" value="" name="don_amount[]">
                            </div>
                        </div>
            
                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="addCol_2Row(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                </div>
                
            </div>

            <br>
            <br>

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

    function addCol_1Row(){
        
        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        $('#col_div_1').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#col_div_1_row').clone().find('.addBtnClass')
        .html(
            '<a onclick="addCol_1Row(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#col_div_1');

        $('.clsSelect2').select2();
    }

    function removeRow(node){
        $(node).parent().parent().parent().remove();
    }

    function addCol_2Row(){

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        $('#col_div_2').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#col_div_2_row').clone().find('.addBtnClass').html(
            '<a onclick="addCol_2Row(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#col_div_2');

        $('.clsSelect2').select2();

    }

    showModal({
        titleContent: "Edit WF",
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