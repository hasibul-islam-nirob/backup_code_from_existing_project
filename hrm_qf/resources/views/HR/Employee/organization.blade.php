@php
use Illuminate\Support\Facades\DB;
$payscaleData = DB::table('hr_payroll_payscale')
            ->where([['is_delete', 0], ['is_active', 1]])->orderBy('eff_date_start', 'desc')->get();
           
@endphp
{{-- Organization --}}
<div id="Organization" class="tab-pane show">
    {{--Hr--}}
    <div class="panel panel-default">
        <div class="panel-heading p-2">HR</div>
        <div class="panel-body">
            <div class="row" style="margin-top: 15px">

                <div class="col-lg-6">

                    <div class="form-row form-group align-items-center d-none">
                        <label class="col-lg-4 input-title">Project Type</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select class="form-control clsSelect2" id="org_project_type" name="org_project_type_id" style="width: 100%">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Recruitment Type</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select class="form-control clsSelect2" name="org_rec_type_id" id="org_rec_type_id" style="width: 100%">
                                    <option value="">Select</option>
                                    @foreach($data['recType'] as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Department</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select class="form-control clsSelect2" name="org_department" style="width: 100%">
                                    <option value="">Select</option>
                                    @foreach($data['orgDepartment'] as $row)
                                        <option value="{{ $row->id }}">{{ $row->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div {{ (isset($empData['empOrgData'])) ? '' : '' }} class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Designation</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select class="form-control clsSelect2" name="org_position_id" style="width: 100%">
                                    <option value="">Select</option>
                                    @foreach($data['orgPosition'] as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Joining Date</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                                </div>
                                <input type="text" class="form-control round"
                                       id="org_join_date" name="org_join_date" autocomplete="off" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Provision Period <small>(month)</small> </label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" id="prov_period" name="prov_period"
                                       placeholder="Provision Period in Month"
                                       data-error="Please enter Provision Period in Month.">
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title RequiredStar">Provision End Date</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                                </div>
                                <input type="text" class="form-control round"
                                       id="org_permanent_date" name="org_permanent_date" autocomplete="off" placeholder="DD-MM-YYYY" disabled> 
                                <input type="text" name="org_permanent_date" id="org_permanent_date2" hidden>
                            </div>
                        </div>
                    </div>

                   {{-- <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Terminate/Reg Date</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                                </div>
                                <input type="text" class="form-control round"
                                       name="org_terminate_or_reg_date" id="org_terminate_or_reg_date" autocomplete="off" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
                    </div>--}}

                    @if(isset($data['viewPage']))
                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Job Status</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_job_status" data-error="Please Select Marital Status" style="width: 100%">
                                        <option value="">Select</option>
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                        <option value="2">Resign</option>
                                        <option value="4">Terminated</option>
                                        <option value="5">Retirement</option>
                                        <option value="3 ">Dismissed </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif


                </div>

                <div class="col-lg-6">

                    {{-- <div class="form-row form-group align-items-center">
                        &nbsp;
                    </div> --}}

                    {{-- <div class="form-row form-group align-items-center">
                        &nbsp;
                    </div> --}}
                    <div class="form-row form-group align-items-center  d-none">
                        <label class="col-lg-4 input-title">Project</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select onchange="loadSelectBox({'projectId' : this.value}, 'getProjectType', $('#org_project_type')[0])" class="form-control clsSelect2" name="org_project_id" style="width: 100%">
                                    <option value="">Select</option>
                                    @foreach($data['orgProject'] as $row)
                                        <option value="{{ $row->id }}" selected>{{ $row->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Org. Phone</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" name="org_phone"
                                       placeholder="Enter Phone"
                                       data-error="Please enter phone no.">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Org. Fax</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" name="org_fax"
                                       placeholder="Enter Fax"
                                       data-error="Please enter fax">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>
                    

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Org. Location</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" name="org_location"
                                       placeholder="Enter Location"
                                       data-error="Please enter Employee Location">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Room No.</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" name="org_room_no"
                                       placeholder="Enter Employee Room No"
                                       data-error="Please enter Employee Room No">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Org. Mobile</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" name="org_mobile"
                                       placeholder="Enter Employee Mobile" data-error="Please enter Employee Mobile.">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Org. Email</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" name="org_email"
                                       placeholder="Enter Employee Email"
                                       data-error="Please enter Employee Email.">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Attandance Device ID</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" name="org_device_id"
                                       placeholder="Enter Employee Device ID"
                                       data-error="Please enter Employee Device ID">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>

                    @if(isset($data['viewPage']))
                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Status</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_status" style="width: 100%">
                                        <option value="">Select</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>

    {{--Payroll--}}
    @if ($data['hasPayRoll'])
        <div class="panel panel-default">
            <div class="panel-heading p-2">Payroll</div>
            <div class="panel-body">
                <div class="row" style="margin-top: 15px">

                    <div class="col-lg-6">

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title RequiredStar">Grade</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_grade" id="org_grade" style="width: 100%">
                                        <option value="">Select</option>
                                        @for($i = 1; $i<=$data['orgGrade']->content; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title RequiredStar">Level</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_level" id="org_level" style="width: 100%">
                                        <option value="">Select</option>
                                        @for($i = 1; $i<=$data['orgLevel']->content; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title RequiredStar">Payscale Year</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_fiscal_year_id" id="org_fiscal_year_id" style="width: 100%">
                                        <option value="">Select </option>
                                        @foreach($payscaleData as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title RequiredStar">Step</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_step" id="org_step" style="width: 100%">
                                        <option value="">Select Step</option>
                                        @for($i = 1; $i<=12; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <input type="text" name="salary_structure_id" id="salary_structure_id" value="" hidden>

                    </div>

                    <div class="col-lg-6">

                        <div class="row">
                            <div class=" col-sm-12 col-md-6">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="" id="" >Particulars</th>
                                            <th class="" id="" >Amount</th>
                                        </tr>
                                    </thead>
                        
                                    <tbody>
                                        <tr>
                                            <th>&ensp;Step</th>
                                            <td id="t_step" style="font-weight: 400">0</td>
                                        </tr>
                                        <tr>
                                            <th>&ensp;Basic</th>
                                            <td id="t_basic" style="font-weight: 600">0</td>
                                        </tr>
                                        <tr>
                                            <th>&ensp;Increment &ensp; <span id="incrementPer">(0%)</span></th>
                                            <td id="t_increment" style="font-weight: 600">0</td>
                                        </tr>

                                        <tr>
                                            <th>&ensp;Total Basic</th>
                                            <td id="t_total_basic" style="font-weight: 600">0</td>
                                        </tr>

                                        <tr>
                                            <th>&ensp;Net Salary</th>
                                            <td>
                                                <tr>
                                                    <th> &ensp; &ensp; &ensp; Type- A</th>
                                                    <td id="t_net_salary_a" style="font-weight: 600">0</td>
                                                </tr>
                                                <tr>
                                                    <th> &ensp; &ensp; &ensp; Type- B</th>
                                                    <td id="t_net_salary_b" style="font-weight: 600">0</td>
                                                </tr>
                                                <tr>
                                                    <th> &ensp; &ensp; &ensp; Type- C</th>
                                                    <td id="t_net_salary_c" style="font-weight: 600">0</td>
                                                </tr>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
    
    
                            <div class=" col-sm-12 col-md-6">
                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="" id="" width="60%">Particulars</th>
                                            <th class="" id="" >Amount</th>
                                        </tr>
                                    </thead>
                        
                                    <tbody>

                                        <tr id="BenefitTypeA">
                                            <th  >&ensp;Benefit Type- A</th>
                                            <th id="grossBenTypeA" style="font-weight: 600">0</th>
                                        </tr>

                                        <tr id="BenefitTypeB">
                                            <th  >&ensp;Benefit Type- B</th>
                                            <th id="grossBenTypeB" style="font-weight: 600">0</th>
                                        </tr>

                                        <tr id="BenefitTypeC">
                                            <th  >&ensp;Benefit Type- C</th>
                                            <th id="grossBenTypeC" style="font-weight: 600">0</th>
                                        </tr>

                                        <tr id="t_Deduction">
                                            <th  >&ensp;Deduction</th>
                                            <th id="grossDeduction" style="font-weight: 600">0</th>
                                        </tr>
                                        

                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    @endif



</div>


<div class="row d-none" id="tempSalaryTable">
    <div class="col-lg-12">
        <table class="table w-full table-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th class="" id="org_salary_table_h_1" style="width:5%; background-color: #81b847;">Step</th>
                    <th class="" id="org_salary_table_h_2" style="background-color: #81b847;">Basic</th>
                    <th class="" id="org_salary_table_h_3" style="background-color: #81b847;">Increment</th>
                    <th class="" id="org_salary_table_h_4" style="background-color: #81b847;">Total Basic</th>
                    
                    {{-- Ben A --}}
                    <th class="calBenTypeA" id="org_salary_table_h_5" colspan="" style="background-color: #41a4c9;">Benefit Type- A <br> Gross Salary</th>
                    {{-- Ben B --}}
                    <th class="calBenTypeB" id="org_salary_table_h_6" colspan="" style="background-color: #a396b1;">Benefit Type- B <br> Gross Salary</th>
                    {{-- Ben C --}}
                    <th class="calBenTypeC" id="org_salary_table_h_7" colspan="" style="background-color: #D99795;">Benefit Type- C <br> Gross Salary</th>
                       
                    <th class="" id="org_salary_table_h_8" colspan="" style="background-color: #e0995f; ">Total <br> Deduction</th>
                    
                    <th class="calBenTypeA" id="org_salary_table_h_9" style="background-color: #41a4c9;">Net Salary <br> Type- A</th>
                    <th class="calBenTypeB" id="org_salary_table_h_10" style="background-color: #a396b1;">Net Salary <br> Type- B</th>
                    <th class="calBenTypeC" id="org_salary_table_h_11" style="background-color: #D99795;">Net Salary <br> Type- C</th>
                </tr>
                
            </thead>

            <tbody>

                <tr>
                    <td id="org_salary_table_f_1" class="text-center"></td>
                    <td id="org_salary_table_f_2" class="text-center"></td>
                    <td id="org_salary_table_f_3" class="text-center"></td>
                    <td id="org_salary_table_f_4" class="text-center"></td>
                    <td id="org_salary_table_f_5" class="text-center calBenTypeA"></td>
                    <td id="org_salary_table_f_6" class="text-center calBenTypeB"></td>
                    <td id="org_salary_table_f_7" class="text-center calBenTypeC"></td>
                    <td id="org_salary_table_f_8" class="text-center"></td>
                    <td id="org_salary_table_f_9" class="text-center calBenTypeA"></td>
                    <td id="org_salary_table_f_10" class="text-center calBenTypeB"></td>
                    <td id="org_salary_table_f_11" class="text-center calBenTypeC"></td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>


<script>

    function getCurrentTabId(){
        let currTabNode = document.querySelector('.tabList').querySelector('.active').href;
        let id = '';
        for (let i=currTabNode.length-1; i>0; i--){
            if (currTabNode[i] === '#'){
                break;
            }
            id+=currTabNode[i];
        }
        return id.split("").reverse().join("");
    }
    
    $("#org_grade, #org_level").change(function(event){
        event.preventDefault();

        let currntURL =" {{ url()->current() }}";
        let urlSplit = currntURL.split('/')
        let findAction = urlSplit[urlSplit.length - 2];
        let actionUrlForStep = "";
        if (findAction == 'edit' || findAction == 'view') {
            actionUrlForStep = "{{ url()->current() }}/../../getStepData";
        }else{
            actionUrlForStep = "{{ url()->current() }}/../getStepData";
        }
        $.ajax({
            type: "POST",
            url: actionUrlForStep,
            data: {context : 'getStepData', org_grade : $("#org_grade").val(), org_level : $("#org_level").val()},
            dataType: "json",
            success: function (response) {
                if(response.steps){
                    $('#org_step').empty().append($('<option>', {
                            value: '',
                            text: 'Select Step'
                    }));
                    for (let index = 1; index <= (response.steps.no_of_inc + 1); index++) {
                        $('#org_step').append($('<option>', {
                            value: index,
                            text: index
                        }));
                    }
                }
            },
            error: function(){
                alert('error!');
            }
        });
    })

    $("#org_step").change(function(event){
        setSalaryInformation(event);
    })

    function setSalaryInformation(event) {
        event.preventDefault();

        let currntURL =" {{ url()->current() }}";
        let urlSplit = currntURL.split('/')
        let findAction = urlSplit[urlSplit.length - 2];
        let actionUrl = "";
        if (findAction == 'edit' || findAction == 'view') {
            actionUrl = "{{ url()->current() }}/../../getSalaryInformation";
        }else if (findAction == 'add'){
            actionUrl = "{{ url()->current() }}/../getSalaryInformation";
        }

        let currTabId = getCurrentTabId();

        let org_rec_type_id = $("#org_rec_type_id").val();
        let org_grade = $("#org_grade").val();
        let org_level = $("#org_level").val();
        let org_fiscal_year_id = $("#org_fiscal_year_id").val();
        let org_step = $("#org_step").val();
        let joiningDate = $("#org_join_date").val();

        setZero();
        if(org_rec_type_id == '' && currTabId == 'Organization'){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Recruitment type is empty..",
            });
            $("#org_rec_type_id").val('');
        }else if(org_fiscal_year_id == '' && currTabId == 'Organization'){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Joining date is empty..",
            });
        }else if(org_grade == '' && currTabId == 'Organization'){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Grade is empty..",
            });
        }else if(org_level == '' && currTabId == 'Organization'){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Level is empty..",
            });
        }else if(org_step == '' && currTabId == 'Organization'){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Step is empty..",
            });
        }else{

            $.ajax({
                type: "POST",
                url: actionUrl,
                data: {
                    context : 'getData', 
                    org_grade : $("#org_grade").val(),
                    org_level : $("#org_level").val(),
                    org_step : $("#org_step").val(),
                    org_fiscal_year_id : $("#org_fiscal_year_id").val(),
                    org_rec_type_id : $("#org_rec_type_id").val(),
                },
                dataType: "json",
                success: function (response) {

                    // console.log(response);

                    if (response.salaryInfo && response.salaryInfo != null) {
                        let basic = response.salaryInfo.basic;
                        let total_basic = response.salaryInfo.total_basic;
                        
                        $("#salary_structure_id").val(response.salaryInfo.salary_structure_id);
                        $("#incrementPer").html("("+response.salaryInfo.incrementPer+"%)");

                        $("#org_salary_table_f_1").html(response.salaryInfo.year);
                        $("#org_salary_table_f_2").html(basic);
                        $("#org_salary_table_f_3").html(response.salaryInfo.increment);
                        $("#org_salary_table_f_4").html(total_basic);

                        $("#t_step").html(response.salaryInfo.year);
                        $("#t_basic").html(basic);
                        $("#t_increment").html(response.salaryInfo.increment);
                        $("#t_total_basic").html(total_basic);


                        let benTypeA = 0;
                        let benTypeB = 0;
                        let benTypeC = 0;
                        let deduction = 0;
                        if (response.salaryInfo.allowance) {

                            $.each(response.salaryInfo.allowance, function(i, item) { 
                                if(i==1){
                                    $(".benAData").remove();
                                    $.each(item, function(j, j_val) {
                                        let benAData = '<tr class="benAData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ j +'</th>'+
                                                            '<td>'+j_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeA").after(benAData);
                                        if(j_val == null){
                                            j_val = 0;
                                        } 
                                        benTypeA += parseInt(j_val);
                                    });
                                    $("#grossBenTypeA").html(benTypeA);
                                }
                                if(i==2){
                                    $(".benBData").remove();
                                    $.each(item, function(k, k_val) { 
                                        
                                        let benBData = '<tr class="benBData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ k +'</th>'+
                                                            '<td>'+k_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeB").after(benBData);
                                        if(k_val == null){
                                            k_val = 0;
                                        } 
                                        benTypeB += parseInt(k_val);
                                    });
                                    $("#grossBenTypeB").html(benTypeB);
                                }
                                if(i==3){
                                    $(".benCData").remove();
                                    $.each(item, function(l, l_val) { 
                                        
                                        let benCData = '<tr class="benCData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ l +'</th>'+
                                                            '<td>'+l_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeC").after(benCData);
                                        if(l_val == null){
                                            l_val = 0;
                                        } 
                                        benTypeC += parseInt(l_val);
                                    });
                                    $("#grossBenTypeC").html(benTypeC);
                                }
                            });
                            
                            

                        }

                        if(response.salaryInfo.deduction){
                            $(".deductionRmData").remove();
                            $.each(response.salaryInfo.deduction, function(i, val) { 
                                let DeductionData = '<tr class="deductionRmData">'+
                                                    '<th> &ensp; &ensp; &ensp; '+ i +'</th>'+
                                                    '<td>'+val+'</td>'+
                                                '</tr>';
                                $("#t_Deduction").after(DeductionData);
                                if(val == null){
                                    val = 0;
                                } 
                                deduction += parseInt(val);
                            });
                            $("#grossDeduction").html(deduction);
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

                        $("#t_net_salary_a").html(netSalaryA);
                        $("#t_net_salary_b").html(netSalaryB);
                        $("#t_net_salary_c").html(netSalaryC);

                    }else{
                        setZero();
                        $("#org_basic_salary").val(00);
                        $("#org_tot_salary").val(00);

                        $("#grossBenTypeA").html(00);
                        $("#grossBenTypeB").html(00);
                        $("#grossBenTypeC").html(00);
                        $("#grossDeduction").html(0);
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
    }


    // Set 0 Some Field
    function setZero(){
        $("#t_step").html(0);
        $("#t_basic").html(0);
        $("#t_increment").html(0);
        $("#t_total_basic").html(0);

        $("#grossBenTypeA").html(0);
        $("#grossBenTypeB").html(0);
        $("#grossBenTypeC").html(0);
        $("#grossDeduction").html(0);

        $("#t_net_salary_a").html(0);
        $("#t_net_salary_b").html(0);
        $("#t_net_salary_c").html(0);

        $("#salary_structure_id").val('');
        $("#incrementPer").html(0);

        $("#org_salary_table_f_1").html(0);
        $("#org_salary_table_f_2").html(0);
        $("#org_salary_table_f_3").html(0);
        $("#org_salary_table_f_4").html(0);
        $("#org_salary_table_f_5").html(0);
        $("#org_salary_table_f_6").html(0);
        $("#org_salary_table_f_7").html(0);
        $("#org_salary_table_f_8").html(0);
        $("#org_salary_table_f_9").html(0);
        $("#org_salary_table_f_10").html(0);
        $("#org_salary_table_f_11").html(0);


        $(".benAData").remove();
        $(".benBData").remove();
        $(".benCData").remove();
        $(".deductionRmData").remove();
    }

    
    // Set Pay Scal Year Start
    $("#org_join_date").change(function(event){
        event.preventDefault();

        let currntURL =" {{ url()->current() }}";
        let urlSplit = currntURL.split('/')
        let findAction = urlSplit[urlSplit.length - 2];
        let actionUrl = "";
        if (findAction == 'edit' || findAction == 'view') {
            actionUrl = "{{ url()->current() }}/../../getPayScale";
        }else{
            actionUrl = "{{ url()->current() }}/../getPayScale";
        }

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: {
                context : 'getData', 
                joinDate : $("#org_join_date").val()
            },
            dataType: "json",
            success: function (response) {
                // org_fiscal_year_id
                if(response){
                    $('#org_fiscal_year_id').empty().append($('<option>', {
                            value: '',
                            text: 'Select'
                    }));;
                    $.each(response, function(i, item) {
                        $('#org_fiscal_year_id').append($('<option>', {
                            value: item.id,
                            text: item.name
                        }));

                        $('#org_fiscal_year_id').val(item.id).trigger('change');
                    });
                }

            },
            error: function(){
                alert('error!');
            }
        });
    });
    // Set Pay Scal Year End


    $(document).ready(function (){
        let empOrgData = {!! json_encode((isset($empData['empOrgData'])) ? $empData['empOrgData'] : null) !!};
        let eData = {!! json_encode((isset($empData['emp'])) ? $empData['emp'] : null) !!};

        // console.log(eData);

        if(eData != null){
            

            setEditData(document.querySelector('[name=org_position_id]'), eData['designation_id']);
            setEditData(document.querySelector('[name=org_department]'), eData['department_id']);
            setEditData(document.querySelector('[name=org_join_date]'), convertDateFormatTwo(eData['join_date']));
            setEditData(document.querySelector('[name=org_permanent_date]'), convertDateFormatTwo(eData['permanent_date']));
            setEditData(document.querySelector('[name=prov_period]'), eData['prov_period']);
            setEditData(document.querySelector('[name=org_mobile]'), eData['org_mobile']);
            setEditData(document.querySelector('[name=org_email]'), eData['org_email']);
            setEditData(document.querySelector('[name=org_basic_salary]'), eData['basic_salary']);
            if (isViewPage){
                setEditData(document.querySelector('[name=org_status]'), empOrgData[0]['status']);
                setEditData(document.querySelector('[name=org_job_status]'), eData['is_active']);
            }
        }

        if (empOrgData != null && empOrgData.length != 0){

            setEditData(document.querySelector('[name=org_project_id]'), empOrgData[0]['project_id']);

            $.when(loadSelectBox({'projectId' : document.querySelector("[name=org_project_id]").value}, 'getProjectType', $('#org_project_type')[0]))
                .then(function (){
                    setEditData(document.querySelector('[name=org_project_type_id]'), empOrgData[0]['project_type_id']);
                });

            setEditData(document.querySelector('[name=org_rec_type_id]'), empOrgData[0]['rec_type_id']);
            setEditData(document.querySelector('[name=org_level]'), empOrgData[0]['level']);
            setEditData(document.querySelector('[name=org_grade]'), empOrgData[0]['grade']);
            setEditData(document.querySelector('[name=org_step]'), empOrgData[0]['step']);
            setEditData(document.querySelector('[name=payscal_id]'), empOrgData[0]['fiscal_year_id']);
            setEditData(document.querySelector('[name=salary_structure_id]'), empOrgData[0]['salary_structure_id']);

            setEditData(document.querySelector('[name=org_phone]'), empOrgData[0]['phone_no']);
            setEditData(document.querySelector('[name=org_fax]'), empOrgData[0]['fax_no']);
            setEditData(document.querySelector('[name=org_fiscal_year_id]'), empOrgData[0]['fiscal_year_id']);
            setEditData(document.querySelector('[name=org_last_inc_date]'), empOrgData[0]['last_inc_date']);
            setEditData(document.querySelector('[name=org_security_amount]'), empOrgData[0]['security_amount']);
            setEditData(document.querySelector('[name=org_adv_security_amount]'), empOrgData[0]['adv_security_amount']);
            setEditData(document.querySelector('[name=org_installment_amount]'), empOrgData[0]['installment_amount']);
            setEditData(document.querySelector('[name=org_edps_start_month]'), empOrgData[0]['edps_start_month']);

            if (isViewPage){
                setEditData(document.querySelector('[name=org_status]'), empOrgData[0]['status']);
            }
            //setEditData(document.querySelector('[name=org_status]'), empOrgData[0]['status']);
            //setEditData(document.querySelector('[name=org_job_status]'), empOrgData[0]['job_status']);
            setEditData(document.querySelector('[name=org_location]'), empOrgData[0]['location']);
            setEditData(document.querySelector('[name=org_room_no]'), empOrgData[0]['room_no']);
            setEditData(document.querySelector('[name=org_device_id]'), empOrgData[0]['device_id']);
            setEditData(document.querySelector('[name=org_tot_salary]'), empOrgData[0]['tot_salary']);
            setEditData(document.querySelector('[name=org_salary_inc_year]'), empOrgData[0]['salary_inc_year']);
            setEditData(document.querySelector('[name=org_security_amount_location]'), empOrgData[0]['security_amount_location']);
            setEditData(document.querySelector('[name=org_edps_amount]'), empOrgData[0]['edps_amount']);
            setEditData(document.querySelector('[name=org_edps_lifetime]'), empOrgData[0]['edps_lifetime']);
            setEditData(document.querySelector('[name=org_no_of_installment]'), empOrgData[0]['no_of_installment']);
            //setEditData(document.querySelector('[name=org_terminate_or_reg_date]'), empOrgData[0]['terminate_or_reg_date']);
            setEditData(document.querySelector('[name=org_has_house_allowance]'), empOrgData[0]['has_house_allowance']);
            setEditData(document.querySelector('[name=org_has_travel_allowance]'), empOrgData[0]['has_travel_allowance']);
            setEditData(document.querySelector('[name=org_has_daily_allowance]'), empOrgData[0]['has_daily_allowance']);
            setEditData(document.querySelector('[name=org_has_medical_allowance]'), empOrgData[0]['has_medical_allowance']);
            setEditData(document.querySelector('[name=org_has_utility_allowance]'), empOrgData[0]['has_utility_allowance']);
            setEditData(document.querySelector('[name=org_has_mobile_allowance]'), empOrgData[0]['has_mobile_allowance']);
            setEditData(document.querySelector('[name=org_has_welfare_fund]'), empOrgData[0]['has_welfare_fund']);

            //setEditData(document.querySelector('[name=org_username]'), empOrgData[0]['org_username']);
            //setEditData(document.querySelector('[name=org_password]'), empOrgData[0]['org_password']);
        }
    });

    $('#org_join_date,#org_last_inc_date,#org_terminate_or_reg_date,#org_permanent_date').datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        /*maxDate: systemDate*/
    }).keydown(false);

    $('#org_edps_start_month').datepicker({
        dateFormat: 'yy-mm',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        /*maxDate: systemDate*/
    }).keydown(false);



    function calculateProvDate() {
        let startDateStr = $("#org_join_date").val();
        let provTimeMonths = $("#prov_period").val();

        if (provTimeMonths != '') {
            
            // Split the start date string to get day, month, and year values
            let [day, month, year] = startDateStr.split("-");
    
            // Create a Date object with the start date
            let startDate = new Date(year, parseInt(month) - 1, day);
    
            // Calculate the end date
            let endDate = new Date(startDate);
            endDate.setMonth(endDate.getMonth() + parseInt(provTimeMonths));
    
            let endDateStr = endDate.toLocaleDateString("en-GB");
            
            // Format the end date as "dd-mm-yyyy"
            let finalEndDate = convertDateFormat(endDateStr);
    
            $("#org_permanent_date").val(finalEndDate);
            $("#org_permanent_date2").val(finalEndDate);
        }else{
            $("#org_permanent_date").val(startDateStr);
            $("#org_permanent_date2").val(startDateStr);
        }

    }
    // Usage example
    
    $("#org_join_date, #prov_period").change(function() {
        calculateProvDate();
    })


    let currntURL =" {{ url()->current() }}";
    let urlSplit = currntURL.split('/')
    let findAction = urlSplit[urlSplit.length - 2];
    if (findAction != 'view') {
        $(window).on("load", function(event) {
            setSalaryInformation(event);
        });
    }

    
   
</script>
