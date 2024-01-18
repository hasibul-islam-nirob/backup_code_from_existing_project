
<style>
    .modal-lg {
        max-width: 50%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;

    $months = DB::table('hr_months')->pluck('name', 'id')->toArray();
    $fiscalYear = DB::table('gnl_fiscal_year')->where([['is_delete', 0],['is_active', 1]])->orderBy('fy_name','desc')->first();
    $startDate = $fiscalYear->fy_start_date;
    $endDate = $fiscalYear->fy_end_date;
    
    ## get year start
    $salaryMonthArr = [];
    $getMonthNameDatesDaysData = HRS::getMonthNameDatesDaysData($fiscalYear);
    // foreach($getMonthNameDatesDaysData as $keyMonth => $monthDateArr){
    //     $monthDatesKeysArr = array_keys($monthDateArr);
    //     $monthStartDate = reset($monthDatesKeysArr);
    //     $monthEndDate = end($monthDatesKeysArr);
    //     $tmpTargetYear = intval((date("Y", strtotime($monthEndDate))));
    //     $identityName = $keyMonth.'-'.$tmpTargetYear;
    //     $salaryMonthArr[$identityName] = $monthEndDate;
    // }
    foreach ($months as $key => $value) {
        foreach($getMonthNameDatesDaysData as $keyMonth => $monthDateArr){
            if($keyMonth == $value){
                $monthDatesKeysArr = array_keys($monthDateArr);
                $monthStartDate = reset($monthDatesKeysArr);
                $monthEndDate = end($monthDatesKeysArr);
                $tmpTargetYear = intval((date("Y", strtotime($monthEndDate))));
                // $identityName = $keyMonth.'-'.$tmpTargetYear;
                $identityName = $keyMonth;
                $salaryMonthArr[$identityName] = $monthEndDate;
            }
        }
    }
    // dd($fiscalYear, $salaryMonthArr);
    ## get year start

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get(); //title
    $religions = DB::table('mfn_member_religions')->get();

@endphp

<form id="salary_generate_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" class="form-control clsSelect2" style="width: 100%">
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
                        <select name="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Project</option>
                            @foreach ($projects as $val)
                            <option value="{{ $val->id }}">{{ $val->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'allBranchs'=>true,
                    'isRequired'=> true,
                    'elementTitle' => 'Branch',
                    'elementId' => 'branch_id',
                    'elementName' => 'branch_id',
                    'divClass'=> "col-sm-5 offset-sm-1 form-group",
                    'formStyle'=> "vertical"
                ]) !!}


                <div class="col-sm-5  form-group">
                    <label class="input-title RequiredStar">Month</label>
                    <div class="input-group">
                        <select name="salary_month" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Month</option>
                            @foreach ($salaryMonthArr as $key => $val)
                            <option value="{{ $val }}">{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="text" name="fiscal_year_id" value={{$fiscalYear->id}} hidden>
                </div>
    
            </div>

            <!--
            <div class="row">
                <div class="col-sm-5 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Fiscal Year</label>
                    <div class="input-group">
                        <select name="payscale_year_id" id="add_pay_scale_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Fiscal Year</option>
                            {{-- @foreach ($fiscalYear as $ps)
                                <option value="{{ $ps->id }}">{{ $ps->fy_name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title">Approved Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_exp_effective_date" style="z-index:99999 !important;" name="approved_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>
            -->

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

    // ##############################
    // let fiscalYear = "{{$fiscalYear->fy_name}}";
    // if (fiscalYear != '') {
    //     swal({
    //         title: "Are you sure to ?",
    //         text: fiscalYear+" this is your current fiscal year.",
    //         icon: "warning",
    //         // buttons: true,
    //         dangerMode: true,
    //         buttons: ["No", "Yes"],
    //     })
    //     .then((yes) => {
    //         console.log(yes);
    //         if (!yes) {
    //             swal(
    //                 'Opps....',
    //                 'Set your new fiscal year',
    //                 'warning'
    //             )
    //             setTimeout(() => {
    //                 window.location.href = '../fiscal_year';
    //             }, 2000);
    //         }
    //     });
    // }
    // ##############################


    showModal({
        titleContent: "Add Salary Genarate",
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
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#salary_generate_add_form')[
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
