{{-- Organization --}}
<div id="Organization" class="tab-pane show">
    {{--Hr--}}
    <div class="panel panel-default">
        <div class="panel-heading p-2">HR</div>
        <div class="panel-body">
            <div class="row" style="margin-top: 15px">

                <div class="col-lg-6">

                    <div {{ (isset($empData['empOrgData'])) ? 'hidden' : '' }} class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Employee Code</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" id="emp_code" name="emp_code"
                                       placeholder="Enter Employee Code" value=""
                                       data-error="Please enter Employee Code.">
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
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
                        <label class="col-lg-4 input-title">Project</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select onchange="loadSelectBox({'projectId' : this.value}, 'getProjectType', $('#org_project_type')[0])" class="form-control clsSelect2" name="org_project_id" style="width: 100%">
                                    <option value="">Select</option>
                                    @foreach($data['orgProject'] as $row)
                                        <option value="{{ $row->id }}">{{ $row->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Recruitment Type</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <select class="form-control clsSelect2" name="org_rec_type_id" style="width: 100%">
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

                    <div {{ (isset($empData['empOrgData'])) ? 'hidden' : '' }} class="form-row form-group align-items-center">
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
                        <label class="col-lg-4 input-title">Provision Period</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="number" class="form-control round" name="prov_period"
                                       placeholder="Provision Period in Days"
                                       data-error="Please enter Provision Period in Days.">
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
                        <label class="col-lg-4 input-title RequiredStar">Provision End Date</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                                </div>
                                <input type="text" class="form-control round"
                                       id="org_permanent_date" name="org_permanent_date" autocomplete="off" placeholder="DD-MM-YYYY">
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

                    <div class="form-row form-group align-items-center">
                        &nbsp;
                    </div>

                    {{-- <div class="form-row form-group align-items-center">
                        &nbsp;
                    </div> --}}

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
                            <label class="col-lg-4 input-title">Basic Salary</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="org_basic_salary"
                                        placeholder="Enter basic salary"
                                        data-error="Please enter basic salary">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Grade</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_grade" style="width: 100%">
                                        <option value="">Select</option>
                                        @for($i = 1; $i<=$data['orgGrade']->content; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Security Amount</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="org_security_amount"
                                        placeholder="Enter Security Amount"
                                        data-error="Please enter Security Amount">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Advanced Security Amount</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="org_adv_security_amount"
                                        placeholder="Enter Advanced Security Amount"
                                        data-error="Please enter Advanced Security Amount">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">EDPS Amount</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="org_edps_amount"
                                        placeholder="Enter Employee EDPS Amount"
                                        data-error="Please enter Employee EDPS Amount.">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">EDPS Start Month</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                </span>
                                    </div>
                                    <input type="text" class="form-control round" id="org_edps_start_month" name="org_edps_start_month" autocomplete="off" placeholder="MM-YYYY">
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">EDPS Lifetime (Year)</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="org_edps_lifetime"
                                        placeholder="Enter Employee EDPS Lifetime"
                                        data-error="Please enter Employee EDPS Lifetime">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Fiscal Year</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_fiscal_year_id" style="width: 100%">
                                        <option value="">Select</option>
                                        @foreach($data['orgFiscalYear'] as $row)
                                            <option value="{{ $row->id }}">{{ $row->fy_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Total Salary</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="org_tot_salary"
                                        placeholder="Enter Employee Total Salary"
                                        data-error="Please enter Employee Total Salary">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Level</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_level" style="width: 100%">
                                        <option value="">Select</option>
                                        @for($i = 1; $i<=$data['orgLevel']->content; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Security Amount Location</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="org_security_amount_location"
                                        placeholder="Enter Employee Salary Increment Location"
                                        data-error="Please enter Employee Salary Increment Location">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Installment Amount</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="org_installment_amount"
                                        placeholder="Enter Installment Amount"
                                        data-error="Please Installment Amount">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">No Of Installment</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="org_no_of_installment"
                                        placeholder="Enter Employee No Of Installment"
                                        data-error="Please enter Employee No Of Installment">
                                </div>
                                <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Salary Increment Year</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="org_salary_inc_year" style="width: 100%">
                                        <option value="">Select</option>
                                        @for($i = 0, $yr = date('Y')-1; $i<5; $i++)
                                            <option value="{{ ++$yr }}">{{ $yr }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row form-group align-items-center">
                            <label class="col-lg-4 input-title">Last Increment Date</label>
                            <div class="col-lg-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                </span>
                                    </div>
                                    <input type="text" class="form-control round"
                                        id="org_last_inc_date" name="org_last_inc_date" autocomplete="off" placeholder="DD-MM-YYYY">
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <strong><u>Benefit Type A</u></strong>
                <div class="row">
                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_house_allowance">
                            <label>House Allowance</label>
                        </div>
                    </div>

                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_travel_allowance">
                            <label>Travel Allowance</label>
                        </div>
                    </div>

                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_daily_allowance">
                            <label>Daily Allowance</label>
                        </div>
                    </div>

                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_medical_allowance">
                            <label>Medical Allowance</label>
                        </div>
                    </div>
                </div>

                <strong><u>Benefit Type B</u></strong>
                <div class="row">
                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_utility_allowance">
                            <label>Utility Allowance</label>
                        </div>
                    </div>

                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_mobile_allowance">
                            <label>Mobile Allowance</label>
                        </div>
                    </div>
                </div>

                <strong><u>Benefit Type Others</u></strong>
                <div class="row">
                    <div class="col-lg-3 form-group">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" name="org_has_welfare_fund">
                            <label>Welfare Fund</label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{--<!-- Benefit Type A -->
    <div class="panel panel-default">
        <div class="panel-heading p-2">Benefit Type A</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_house_allowance">
                        <label>House Allowance</label>
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_travel_allowance">
                        <label>Travel Allowance</label>
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_daily_allowance">
                        <label>Daily Allowance</label>
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_medical_allowance">
                        <label>Medical Allowance</label>
                    </div>
                </div>
            </div>

        </div>
    </div>--}}

    {{--<!-- Benefit Type B -->
    <div class="panel panel-default">
        <div class="panel-heading p-2">Benefit Type B</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_utility_allowance">
                        <label>Utility Allowance</label>
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_mobile_allowance">
                        <label>Mobile Allowance</label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Others -->
    <div class="panel panel-default">
        <div class="panel-heading p-2">Others</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <div class="input-group checkbox-custom checkbox-primary">
                        <input type="checkbox" name="org_has_welfare_fund">
                        <label>Welfare Fund</label>
                    </div>
                </div>
            </div>

        </div>
    </div>--}}

    <!-- Login Info -->
    <div {{-- {{ (isset($empData['empOrgData'])) ? 'hidden' : '' }} --}} hidden class="panel panel-default">
        <div class="panel-heading p-2">Login Info</div>
        <div class="panel-body">
            <div class="row mt-2">

                <div class="col-lg-6">
                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Username</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" name="org_username"
                                       placeholder="Enter Username"
                                       data-error="Please enter Username">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-row form-group align-items-center">
                        <label class="col-lg-4 input-title">Password</label>
                        <div class="col-lg-7">
                            <div class="input-group">
                                <input type="text" class="form-control round" name="org_password"
                                       placeholder="Enter Password"
                                       data-error="Please enter Password">
                            </div>
                            <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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
    
    $(document).ready(function (){
        let empOrgData = {!! json_encode((isset($empData['empOrgData'])) ? $empData['empOrgData'] : null) !!};
        let eData = {!! json_encode((isset($empData['emp'])) ? $empData['emp'] : null) !!};

        if(eData != null){
            setEditData(document.querySelector('[name=org_position_id]'), eData['designation_id']);
            setEditData(document.querySelector('[name=org_department]'), eData['department_id']);
            setEditData(document.querySelector('[name=org_join_date]'), eData['join_date']);
            setEditData(document.querySelector('[name=org_permanent_date]'), eData['permanent_date']);
            setEditData(document.querySelector('[name=prov_period]'), eData['prov_period']);
            setEditData(document.querySelector('[name=org_mobile]'), eData['org_mobile']);
            setEditData(document.querySelector('[name=org_email]'), eData['org_email']);
            setEditData(document.querySelector('[name=org_basic_salary]'), eData['basic_salary']);
            if (isViewPage){
                setEditData(document.querySelector('[name=org_status]'), empOrgData[0]['status']);
                setEditData(document.querySelector('[name=org_job_status]'), eData['is_active']);
            }
        }

        if (empOrgData.length != 0){

            setEditData(document.querySelector('[name=org_project_id]'), empOrgData[0]['project_id']);

            $.when(loadSelectBox({'projectId' : document.querySelector("[name=org_project_id]").value}, 'getProjectType', $('#org_project_type')[0]))
                .then(function (){
                    setEditData(document.querySelector('[name=org_project_type_id]'), empOrgData[0]['project_type_id']);
                });

            setEditData(document.querySelector('[name=org_rec_type_id]'), empOrgData[0]['rec_type_id']);
            setEditData(document.querySelector('[name=org_level]'), empOrgData[0]['level']);
            setEditData(document.querySelector('[name=org_grade]'), empOrgData[0]['grade']);

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



        
    $("#orgProcessBtn").click(function(event){
        event.preventDefault();

        let org_rec_type_id = $("#org_rec_type_id").val();
        let org_grade = $("#org_grade").val();
        let org_level = $("#org_level").val();
        let org_fiscal_year_id = $("#org_fiscal_year_id").val();
        let org_step = $("#org_step").val();

        setZero();
        if(org_rec_type_id == ''){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Recruitment type is empty..",
            });
            $("#org_rec_type_id").val('');
        }else if(org_fiscal_year_id == ''){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Joining date is empty..",
            });
        }else if(org_grade == ''){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Grade is empty..",
            });
        }else if(org_level == ''){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Level is empty..",
            });
        }else if(org_step == ''){
            $("#tempSalaryTable").addClass('d-none');
            swal({
                icon: 'warning',
                title: 'Oops...',
                text: "Step is empty..",
            });
        }else{

            $.ajax({
                type: "POST",
                url: "{{ url()->current() }}/../getSalaryInformation",
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

                    console.log(response);

                    if (response.salaryInfo && response.salaryInfo != null) {
                        let basic = response.salaryInfo.basic;
                        let total_basic = response.salaryInfo.total_basic;
                        
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
                                    $.each(item, function(j, j_val) {
                                        if(j_val == null){
                                            j_val = 0;
                                        } 
                                        benTypeA += parseInt(j_val);
                                    });
                                    $("#org_salary_table_f_5").html(benTypeA);
                                }
                                if(i==2){
                                    $.each(item, function(k, k_val) { 
                                        if(k_val == null){
                                            k_val = 0;
                                        } 
                                        benTypeB += parseInt(k_val);
                                    });
                                    $("#org_salary_table_f_6").html(benTypeB);
                                }
                                if(i==3){
                                    $.each(item, function(l, l_val) { 
                                        if(l_val == null){
                                            l_val = 0;
                                        } 
                                        benTypeC += parseInt(l_val);
                                    });
                                    $("#org_salary_table_f_7").html(benTypeC);
                                }
                            });
                            
                            if (benTypeA == 0) {
                                $(".calBenTypeA").addClass('d-none');
                            }else{
                                $(".calBenTypeA").removeClass('d-none');
                            }

                            if (benTypeB == 0) {
                                $(".calBenTypeB").addClass('d-none');
                            }else{
                                $(".calBenTypeB").removeClass('d-none');
                            }

                            if (benTypeC == 0) {
                                $(".calBenTypeC").addClass('d-none');
                            }else{
                                $(".calBenTypeC").removeClass('d-none');
                            }

                        }

                        if(response.salaryInfo.deduction){
                            $.each(response.salaryInfo.deduction, function(i, val) { 
                                if(val == null){
                                    val = 0;
                                } 
                                deduction += parseInt(val);
                            });
                            $("#org_salary_table_f_8").html(deduction);
                        }

                        let netSalaryA = 0;
                        if(benTypeA != 0){
                            netSalaryA = ((total_basic + benTypeA) - deduction);
                            $("#org_salary_table_f_9").html(netSalaryA);
                        }

                        let netSalaryB = 0;
                        if(benTypeB != 0){
                            netSalaryB = ((total_basic + benTypeA + benTypeB) - deduction);
                            $("#org_salary_table_f_10").html(netSalaryB);
                        }

                        let netSalaryC = 0;
                        if(benTypeC != 0){
                            netSalaryC = ((total_basic + benTypeA + benTypeB + benTypeC) - deduction);
                            $("#org_salary_table_f_11").html(netSalaryC);
                        }

                        $("#tempSalaryTable").removeClass('d-none');
                    }else{
                        setZero();
                        $("#tempSalaryTable").addClass('d-none');
                        $("#org_basic_salary").val(000);
                        $("#org_tot_salary").val(000);
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


    })


    // Set 0 Some Field
    function setZero(){
        $("#t_step").html(0);
        $("#t_basic").html(0);
        $("#t_increment").html(0);
        $("#t_total_basic").html(0);

        $("#t_ba_ha").html(0);
        $("#t_ba_ta").html(0);
        $("#t_ba_da").html(0);
        $("#t_ba_ma").html(0);

        $("#t_bb_ca").html(0);
        $("#t_bb_hra").html(0);
        $("#t_bb_mba").html(0);

        $("#t_bc_wha").html(0);

        $("#t_ded_pf").html(0);
        $("#t_ded_wf").html(0);
        $("#t_ded_eps").html(0);
        $("#t_ded_osf").html(0);
        $("#t_ded_inc").html(0);

        $("#t_net_salary_a").html(0);
        $("#t_net_salary_b").html(0);
        $("#t_net_salary_c").html(0);

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
    }
</script>
