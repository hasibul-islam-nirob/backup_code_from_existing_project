
@php
    use App\Services\HrService as HRS;
    use App\Services\HtmlService as HTML;
    use Illuminate\Support\Facades\DB;

    $payscaleData = DB::table('hr_payroll_payscale')
            ->where([['is_delete', 0], ['is_active', 1]])->orderBy('eff_date_start', 'desc')->get();

    $companies = DB::table('gnl_companies')->where([['is_active', 1],['is_delete', 0]])->get(); //comp_name
    $projects = DB::table('gnl_projects')->where([['is_active', 1],['is_delete', 0]])->get(); //project_name
    $groups = DB::table('gnl_groups')->where([['is_active', 1],['is_delete', 0]])->get();
    $rec_type = DB::table('hr_recruitment_types')->where([['is_active', 1],['is_delete', 0]])->get(); //title

    $rectuitmentType = HRS::getRectuitmentTypeData();
    $permanentNonPermanentData = HRS::getPermanentNonPermanentData();
    // ss($permanentNonPermanentData);
@endphp

<form id="payroll_pay_scale_migration_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf


    <div class="row">
        <div class="col-sm-5 offset-sm-1">
            <div>
                {!! HTML::forBranchFeildNew(true, 'branch_id', 'add_branch_id') !!}
            </div> 
        </div> 

        <div class="col-sm-5">
            <div id="employee_add_div" class="form-group">
                <label class="input-title ">Employee</label>
                <div class="input-group">
                    <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                        {{-- <option value="">Select employee</option> --}}
                    </select>
                </div>
            </div> 
        </div> 

        <input type="text" value name="rectuitment_type_id" id="rectuitment_type_id" hidden>
    </div>

    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">Before Migration</h4>
    </div>

    <div class="row">

        <div class="col-sm-6" style="margin-left: 2rem;">
            

            <div class="row">
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title ">Grade</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="be_grade" id="be_grade" style="width: 100%" disabled>
                                <option value="">Select</option>
                                @for($i = 1; $i<= 7; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title ">Level</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="be_level" id="be_level" style="width: 100%" disabled>
                                <option value="">Select</option>
                                @for($i = 1; $i<= 4; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title ">Payscale Year</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="be_org_payscal_year_id" id="be_payscal_year_id" style="width: 100%" disabled>
                                <option value="">Select </option>
                                @foreach($payscaleData as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" value name="be_payscal_year_id" id="be_payscal_year_id_org" hidden>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title ">Step</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="be_step" id="be_step" style="width: 100%" disabled>
                                <option value="">Select Step</option>
                                @for($i = 1; $i<=12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

            </div>



        </div> 

        <div class="col-sm-5">
            
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="form-group" id="" >Particulars</th>
                        <th class="form-group" id="" >Amount</th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <th>&ensp;Step</th>
                        <td id="be_t_step" style="font-weight: 400">0</td>
                    </tr>
                    <tr>
                        <th>&ensp;Basic</th>
                        <td id="be_t_basic" style="font-weight: 600">0</td>
                    </tr>
                    <tr>
                        <th>&ensp;Increment &ensp; <span id="be_incrementPer">(0%)</span></th>
                        <td id="be_t_increment" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Total Basic</th>
                        <td id="be_t_total_basic" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- A</th>
                        <td id="be_grossBenTypeA" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- B</th>
                        <td id="be_grossBenTypeB" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- C</th>
                        <td id="be_grossBenTypeC" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Total Deduction</th>
                        <td id="be_grossDeduction" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Net Salary</th>
                        <td>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- A</th>
                                <td id="be_t_net_salary_a" style="font-weight: 600">0</td>
                            </tr>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- B</th>
                                <td id="be_t_net_salary_b" style="font-weight: 600">0</td>
                            </tr>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- C</th>
                                <td id="be_t_net_salary_c" style="font-weight: 600">0</td>
                            </tr>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>   


    </div>


    <div class="col-lg-12">
        <h4 id="details_grid_header" class="" style="background-color: rgba(23,179,163); color:#fff; padding:10px 0 10px 10px;">After Migration</h4>
    </div>

    <div class="row">
        <div class="col-sm-6" style="margin-left: 2rem;">

            <div class="row">
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title RequiredStar">Grade</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="af_grade" id="af_grade" style="width: 100%">
                                <option value="">Select</option>
                                @for($i = 1; $i<= 7; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title RequiredStar">Level</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="af_level" id="af_level" style="width: 100%">
                                <option value="">Select</option>
                                @for($i = 1; $i<= 4; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row">
                
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title RequiredStar">Payscale Year</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="af_org_payscale_year_id" id="af_payscale_year_id" style="width: 100%">
                                <option value="">Select </option>
                                @foreach($payscaleData as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="input-title RequiredStar">Step</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="af_org_step" id="af_step" style="width: 100%">
                                <option value="">Select Step</option>
                                @for($i = 1; $i<=12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <input type="text" value name="salaray_structure_id" id="salaray_structure_id" hidden>
                
            </div>

        </div> 

        <div class="col-sm-5">
            
            <table class="table table-hover table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="form-group" id="" >Particulars</th>
                        <th class="form-group" id="" >Amount</th>
                    </tr>
                </thead>
    
                <tbody>
                    <tr>
                        <th>&ensp;Step</th>
                        <td id="af_t_step" style="font-weight: 400">0</td>
                    </tr>
                    <tr>
                        <th>&ensp;Basic</th>
                        <td id="af_t_basic" style="font-weight: 600">0</td>
                    </tr>
                    <tr>
                        <th>&ensp;Increment &ensp; <span id="af_incrementPer">(0%)</span></th>
                        <td id="af_t_increment" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Total Basic</th>
                        <td id="af_t_total_basic" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- A</th>
                        <td id="af_grossBenTypeA" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- B</th>
                        <td id="af_grossBenTypeB" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Benefit Type- C</th>
                        <td id="af_grossBenTypeC" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Total Deduction</th>
                        <td id="af_grossDeduction" style="font-weight: 600">0</td>
                    </tr>

                    <tr>
                        <th>&ensp;Net Salary</th>
                        <td>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- A</th>
                                <td id="af_t_net_salary_a" style="font-weight: 600">0</td>
                            </tr>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- B</th>
                                <td id="af_t_net_salary_b" style="font-weight: 600">0</td>
                            </tr>
                            <tr>
                                <th> &ensp; &ensp; &ensp; Type- C</th>
                                <td id="af_t_net_salary_c" style="font-weight: 600">0</td>
                            </tr>
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>   


    </div>

    <br><br>

</form>

<script>

    $("#af_step").change(function(){
        let empId = $("#add_employee_id").val();
        let af_grade = $("#af_grade").val();
        let af_level = $("#af_level").val();
        let af_step = $("#af_step").val();
        let af_payscale = $("#af_payscale_year_id").val();

        console.log(empId);
        setZeroAf()
        if ((empId == '' || empId == null)) {
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Employee is empty..",
            });

        }else if (empId != '' && af_step != '') {
            $.ajax({
                type: "POST",
                url: "{{ url()->current() }}/../getAfterSsData",
                data: {
                    context : 'getAfterSsData', 
                    empId : empId,
                    af_grade : af_grade,
                    af_level : af_level,
                    af_step : af_step,
                    af_payscale : af_payscale,
                },
                dataType: "json",
                success: function (response) {

                    console.log(response);

                    if (response.After_SS_Data && response.After_SS_Data != null) {
                        let af_basic = response.After_SS_Data.basic;
                        let af_total_basic = response.After_SS_Data.total_basic;
                        
                        $("#salaray_structure_id").val(response.After_SS_Data.salary_structure_id);

                        $("#af_incrementPer").html("("+response.After_SS_Data.incrementPer+"%)");
                        $("#af_t_step").html(response.After_SS_Data.year);
                        $("#af_t_basic").html(af_basic);
                        $("#af_t_increment").html(response.After_SS_Data.increment);
                        $("#af_t_total_basic").html(af_total_basic);

                        

                        let af_benTypeA = 0;
                        let af_benTypeB = 0;
                        let af_benTypeC = 0;
                        let af_deduction = 0;

                        if (response.After_SS_Data.allowance) {

                            $.each(response.After_SS_Data.allowance, function(i, item) { 
                                if(i==1){
                                    // $(".benAData").remove();
                                    $.each(item, function(j, j_val) {
                                        // let benAData = '<tr class="benAData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ j +'</th>'+
                                        //                     '<td>'+j_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeA").after(benAData);
                                        if(j_val == null){
                                            j_val = 0;
                                        } 
                                        af_benTypeA += parseInt(j_val);
                                    });
                                    $("#af_grossBenTypeA").html(af_benTypeA);
                                }
                                if(i==2){
                                    // $(".benBData").remove();
                                    $.each(item, function(k, k_val) { 
                                        
                                        // let benBData = '<tr class="benBData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ k +'</th>'+
                                        //                     '<td>'+k_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeB").after(benBData);
                                        if(k_val == null){
                                            k_val = 0;
                                        } 
                                        af_benTypeB += parseInt(k_val);
                                    });
                                    $("#af_grossBenTypeB").html(af_benTypeB);
                                }
                                if(i==3){
                                    // $(".benCData").remove();
                                    $.each(item, function(l, l_val) { 
                                        
                                        // let benCData = '<tr class="benCData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ l +'</th>'+
                                        //                     '<td>'+l_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeC").after(benCData);
                                        if(l_val == null){
                                            l_val = 0;
                                        } 
                                        af_benTypeC += parseInt(l_val);
                                    });
                                    $("#af_grossBenTypeC").html(af_benTypeC);
                                }
                            });
                            
                        }


                        if(response.After_SS_Data.totalDeduction){
                            $("#af_grossDeduction").html(response.After_SS_Data.totalDeduction);
                            af_deduction = response.After_SS_Data.totalDeduction;
                        }

                        

                        let af_netSalaryA = 0;
                        if(af_benTypeA != 0){
                            af_netSalaryA = ((af_total_basic + af_benTypeA) - af_deduction);
                        }

                        let af_netSalaryB = 0;
                        if(af_benTypeB != 0){
                            af_netSalaryB = ((af_total_basic + af_benTypeA + af_benTypeB) - af_deduction);
                        }

                        let af_netSalaryC = 0;
                        if(af_benTypeC != 0){
                            af_netSalaryC = ((af_total_basic + af_benTypeA + af_benTypeB + af_benTypeC) - af_deduction);
                        }

                        $("#af_t_net_salary_a").html(af_netSalaryA);
                        $("#af_t_net_salary_b").html(af_netSalaryB);
                        $("#af_t_net_salary_c").html(af_netSalaryC);

                    }else{
                        // setZero();
                       
                    }
                    
                },
                error: function(error){
                    swal({
                        icon: 'Error',
                        title: 'Oops...',
                        text: "Error: "+error,
                    });
                }
            });
        }
        
    });

    $("#add_employee_id").change(function(){
        let empId = $("#add_employee_id").val();
        console.log(empId);
        setZeroBe()
        if (empId != '') {
            $.ajax({
                type: "POST",
                url: "{{ url()->current() }}/../getBeforeSsData",
                data: {
                    context : 'getBeforeSsData', 
                    empId : empId,
                },
                dataType: "json",
                success: function (response) {

                    console.log(response);

                    $("#be_grade").val(response.be_grade).trigger("change");
                    $("#be_level").val(response.be_level).trigger("change");
                    $("#be_payscal_year_id").val(response.be_payscal_id).trigger("change");
                    $("#be_step").val(response.be_steps).trigger("change");

                    $("#be_payscal_year_id_org").val(response.be_payscal_id);
                    $("#rectuitment_type_id").val(response.recruitmentId);

                    if (response.Before_SS_Data && response.Before_SS_Data != null) {
                        let basic = response.Before_SS_Data.basic;
                        let total_basic = response.Before_SS_Data.total_basic;
                        
                        $("#be_incrementPer").html("("+response.Before_SS_Data.incrementPer+"%)");

                        $("#be_t_step").html(response.Before_SS_Data.year);
                        $("#be_t_basic").html(basic);
                        $("#be_t_increment").html(response.Before_SS_Data.increment);
                        $("#be_t_total_basic").html(total_basic);

                        let benTypeA = 0;
                        let benTypeB = 0;
                        let benTypeC = 0;
                        let deduction = 0;
                        if (response.Before_SS_Data.allowance) {

                            $.each(response.Before_SS_Data.allowance, function(i, item) { 
                                if(i==1){
                                    // $(".benAData").remove();
                                    $.each(item, function(j, j_val) {
                                        // let benAData = '<tr class="benAData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ j +'</th>'+
                                        //                     '<td>'+j_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeA").after(benAData);
                                        if(j_val == null){
                                            j_val = 0;
                                        } 
                                        benTypeA += parseInt(j_val);
                                    });
                                    $("#be_grossBenTypeA").html(benTypeA);
                                }
                                if(i==2){
                                    // $(".benBData").remove();
                                    $.each(item, function(k, k_val) { 
                                        
                                        // let benBData = '<tr class="benBData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ k +'</th>'+
                                        //                     '<td>'+k_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeB").after(benBData);
                                        if(k_val == null){
                                            k_val = 0;
                                        } 
                                        benTypeB += parseInt(k_val);
                                    });
                                    $("#be_grossBenTypeB").html(benTypeB);
                                }
                                if(i==3){
                                    // $(".benCData").remove();
                                    $.each(item, function(l, l_val) { 
                                        
                                        // let benCData = '<tr class="benCData">'+
                                        //                     '<th> &ensp; &ensp; &ensp; '+ l +'</th>'+
                                        //                     '<td>'+l_val+'</td>'+
                                        //                 '</tr>';
                                        // $("#BenefitTypeC").after(benCData);
                                        if(l_val == null){
                                            l_val = 0;
                                        } 
                                        benTypeC += parseInt(l_val);
                                    });
                                    $("#be_grossBenTypeC").html(benTypeC);
                                }
                            });
                            
                        }

                        if(response.Before_SS_Data.deduction){
                            // $(".deductionRmData").remove();
                            $.each(response.Before_SS_Data.deduction, function(i, val) { 
                                // let DeductionData = '<tr class="deductionRmData">'+
                                //                     '<th> &ensp; &ensp; &ensp; '+ i +'</th>'+
                                //                     '<td>'+val+'</td>'+
                                //                 '</tr>';
                                // $("#t_Deduction").after(DeductionData);
                                if(val == null){
                                    val = 0;
                                } 
                                deduction += parseInt(val);
                            });
                            $("#be_grossDeduction").html(deduction);
                        }

                        let netSalaryA = 0;
                        if(benTypeA != 0){
                            netSalaryA = ((total_basic + benTypeA) - deduction);
                        }

                        let netSalaryB = 0;
                        if(benTypeB != 0){
                            netSalaryB = ((total_basic + benTypeA + benTypeB) - deduction);
                        }

                        let netSalaryC = 0;
                        if(benTypeC != 0){
                            netSalaryC = ((total_basic + benTypeA + benTypeB + benTypeC) - deduction);
                        }

                        $("#be_t_net_salary_a").html(netSalaryA);
                        $("#be_t_net_salary_b").html(netSalaryB);
                        $("#be_t_net_salary_c").html(netSalaryC);

                    }else{
                        // setZero();
                       
                    }
                    
                },
                error: function(error){
                    $("#tempSalaryTable").addClass('d-none');
                    swal({
                        icon: 'Error',
                        title: 'Oops...',
                        text: "Error: "+error,
                    });
                }
            });
        }

        
    });





    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
    });

    showModal({
        titleContent: "Add Payroll Pay Scale Migration",
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
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#payroll_pay_scale_migration_add_form')[
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

    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                // $('#add_employee_id, #add_resp_employee_id').val(null).trigger('change');

                $('#add_employee_id, #add_resp_employee_id').select2({

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

                // let newOptionEmployee = '<option value="" data-select2-id="-5">Select Employee</option>';
                let newOptionEmployee = '<option value="0" data-select2-id="-6">All Employee</option>';
                $('#add_employee_id').prepend(newOptionEmployee).trigger('change');
                // $('#add_employee_id option:eq(2)').remove();
            }
        );

    });


    // Set 0 Some Field
    function setZeroBe(){
        $("#be_t_step").html(0);
        $("#be_t_basic").html(0);
        $("#be_t_increment").html(0);
        $("#be_t_total_basic").html(0);

        $("#be_grossBenTypeA").html(0);
        $("#be_grossBenTypeB").html(0);
        $("#be_grossBenTypeC").html(0);
        $("#be_grossDeduction").html(0);

        $("#be_t_net_salary_a").html(0);
        $("#be_t_net_salary_b").html(0);
        $("#be_t_net_salary_c").html(0);
    }

    function setZeroAf(){
        $("#af_t_step").html(0);
        $("#af_t_basic").html(0);
        $("#af_t_increment").html(0);
        $("#af_t_total_basic").html(0);

        $("#af_grossBenTypeA").html(0);
        $("#af_grossBenTypeB").html(0);
        $("#af_grossBenTypeC").html(0);
        $("#af_grossDeduction").html(0);

        $("#af_t_net_salary_a").html(0);
        $("#af_t_net_salary_b").html(0);
        $("#af_t_net_salary_c").html(0);
    }


</script>
