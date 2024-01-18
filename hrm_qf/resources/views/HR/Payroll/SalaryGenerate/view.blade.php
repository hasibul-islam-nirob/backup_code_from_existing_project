
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
                $identityName = $keyMonth.'-'.$tmpTargetYear;
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
                        <select name="company_id" id="company_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Company</option>
                            @foreach ($companies as $val)
                            <option value="{{ $val->id }}">{{ $val->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5  form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="project_id" class="form-control clsSelect2" style="width: 100%" disabled>
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
                        <select name="salary_month" id="salary_month" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Month</option>
                            @foreach ($salaryMonthArr as $key => $val)
                            <option value="{{ $val }}">{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
            </div>

            {{-- <div class="row">

                <div class="col-sm-5 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Fiscal Year</label>
                    <div class="input-group">
                        <select name="payscale_year_id" id="payscale_year_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            <option value="">Select Fiscal Year</option>
                            @foreach ($fiscalYear as $ps)
                                <option value="{{ $ps->id }}">{{ $ps->fy_name }}</option>
                            @endforeach
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
                        <input id="approved_date" style="z-index:99999 !important;" name="approved_date" type="text" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY" disabled>
                    </div>
                </div>

            </div> --}}

        </div>

    </div>

</form>

<div class="row">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped clsDataTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th width="20%">Name</th>
                    <th>Grade</th>
                    <th>Level</th>
                    <th>Step</th>
                    <th>Basic Salary</th>
                    <th>Total Benefit A</th>
                    <th>Total Benefit B</th>
                    <th>Total Benefit C</th>
                    <th>Gross Total</th>
                    <th>Total Deduction</th>
                    <th>Net Payable Salary</th>
                </tr>
            </thead>

            <tbody id="salaryInfoTable">
            </tbody>

        </table>
    </div>
</div>


<script>

    $(document).ready(function(){
        $("#branch_id").attr("disabled", true);
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.clsSelect2').select2();
    });

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api" , 'post', '',
        function(response, textStatus, xhr) {

            let jsonParse = JSON.parse(response.salary_details);
            var allData = jsonParse[0];

            let sl = 1;
            $.each(allData, function( index, item) {
                let ba = 0;
                let bb = 0;
                let bc = 0;
                if(typeof item.benefit_info.total_benefit_bb != 'undefined'){
                    bb = item.benefit_info.total_benefit_bb;
                }else{
                    bb = '-';
                }
                if(typeof item.benefit_info.total_benefit_bc != 'undefined'){
                    bc = item.benefit_info.total_benefit_bc;
                }else{
                    bc = '-';
                }

                let tableRow = "<tr>"+
                                "<td class='text-center'>"+ sl++ +"</td>"+
                                "<td>"+item.emp_name+"</td>"+
                                "<td class='text-center'>"+item.grade+"</td>"+
                                "<td class='text-center'>"+item.level+"</td>"+
                                "<td class='text-center'>"+item.step+"</td>"+
                                "<td class='text-center'>"+item.basic_salary+"</td>"+
                                "<td class='text-center'>"+item.benefit_info.total_benefit_ba+"</td>"+
                                "<td class='text-center'>"+ bb +"</td>"+
                                "<td class='text-center'>"+ bc +"</td>"+
                                "<td class='text-center'>"+item.gross_total+"</td>"+
                                "<td class='text-center'>"+item.self_deduction_total+"</td>"+
                                "<td class='text-center'>"+item.net_payable_salary+"</td>"+
                            "</tr>";

                $("#salaryInfoTable").append(tableRow);

                console.log(item);
            });

            $("#salary_month").val(response.salary_month).trigger('change');
            $("#company_id").val(response.company_id).trigger('change');
            $("#branch_id").val(response.branch_id).trigger('change');
            $("#project_id").val(response.project_id).trigger('change');
            $("#payscale_year_id").val(response.payscale_year_id).trigger('change');
            $("#approved_date").val(response.approved_date);
            
        },
        function(response) {
            showApiResponse(response.status, JSON.parse(response.responseText).message);
        }
    );
    showModal({
        titleContent: "View Salary Genarate",
        
    });

   
</script>
