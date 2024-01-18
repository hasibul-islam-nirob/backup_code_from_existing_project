@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>
@php
    $GLOBALS['requiredFields'] = $requiredFields;
    
    function isRequired($fieldName)
    {
        if (isset($GLOBALS['requiredFields'][$fieldName])) {
            if ($GLOBALS['requiredFields'][$fieldName] == 'required') {
                echo 'value="required" checked';
            }
        }
        
    }
@endphp
<div class="panel">
    <div class="panel-body">
        <form class="form-horizontal" id="exampleConstraintsForm" autocomplete="off">
<div class="p-5 bg-white rounded shadow mb-5">
    <!-- Rounded tabs -->
    <ul id="myTab" role="tablist" class="nav nav-tabs nav-pills flex-column flex-sm-row text-center bg-light border-0 rounded-nav">
      <li class="nav-item flex-sm-fill">
        <a id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true" class="nav-link border-0 text-uppercase font-weight-bold active">General</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="organization-tab" data-toggle="tab" href="#organization" role="tab" aria-controls="organization" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Organization</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Account-tab" data-toggle="tab" href="#Account" role="tab" aria-controls="Account" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Account</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Education-tab" data-toggle="tab" href="#Education" role="tab" aria-controls="Education" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Education</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Training-tab" data-toggle="tab" href="#Training" role="tab" aria-controls="Training" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Training</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Experience-tab" data-toggle="tab" href="#Experience" role="tab" aria-controls="Experience" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Experience</a>
      </li>
      <li class="nav-item flex-sm-fill">
      
        <a id="Guarantor-tab" data-toggle="tab" href="#Guarantor" role="tab" aria-controls="Guarantor" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Guarantor</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Nominee-tab" data-toggle="tab" href="#Nominee" role="tab" aria-controls="Nominee" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Nominee</a>
      </li>
      <li class="nav-item flex-sm-fill">
        <a id="Reference-tab" data-toggle="tab" href="#Reference" role="tab" aria-controls="Reference" aria-selected="false" class="nav-link border-0 text-uppercase font-weight-bold">Reference</a>
      </li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div id="general" role="tabpanel" aria-labelledby="general-tab" class="tab-pane fade px-4 py-5 show active">
        <div class="row row-lg">
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                    <h4 class="example-title">General</h4>
                    <div class="example">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">EMPLOYEE CODE</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_code" id="emp_code" data-plugin="switchery" {{ isRequired('emp_code') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">EMPLOYEE NAME (IN BANGLA)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_name_ban" id="emp_name_ban" data-plugin="switchery" {{ isRequired('emp_name_ban') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">EMPLOYEE NAME (IN ENGLISH)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_name_eng" id="emp_name_eng" data-plugin="switchery" {{ isRequired('emp_name_eng') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">FATHER'S NAME (IN BANGLA)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_fathers_name_ban" id="emp_fathers_name_ban" data-plugin="switchery" {{ isRequired('emp_fathers_name_ban') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">FATHER'S NAME (IN ENGLISH)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_fathers_name_eng" id="emp_fathers_name_eng" data-plugin="switchery" {{ isRequired('emp_fathers_name_eng') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">MOTHER'S NAME (IN BANGLA)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_mothers_name_ban" id="emp_mothers_name_ban" data-plugin="switchery" {{ isRequired('emp_mothers_name_ban') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">MOTHER'S NAME (IN ENGLISH)</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_mothers_name_eng" id="emp_mothers_name_eng" data-plugin="switchery" {{ isRequired('emp_mothers_name_eng') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">NID</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_nid_no" id="emp_nid_no" data-plugin="switchery" {{ isRequired('emp_nid_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">GENDER</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_gender" id="emp_gender"  data-plugin="switchery" {{ isRequired('emp_gender') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">BIRTH CERTIFICATE NO.</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_birth_certificate_no" id="emp_birth_certificate_no" data-plugin="switchery" {{ isRequired('emp_birth_certificate_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">DATE OF BIRTH</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_dob" id="emp_dob" data-plugin="switchery" {{ isRequired('emp_dob') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">PASSPORT NO.</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_passport_no" id="emp_passport_no" data-plugin="switchery" {{ isRequired('emp_passport_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">MARITAL STATUS</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_marital_status" id="emp_marital_status" data-plugin="switchery" {{ isRequired('emp_marital_status') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">TIN</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_tin_no" id="emp_tin_no" data-plugin="switchery" {{ isRequired('emp_tin_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">RELIGION</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_religion" id="emp_religion" data-plugin="switchery" {{ isRequired('emp_religion') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">MOBILE NO.</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_mobile_no" id="emp_mobile_no" data-plugin="switchery" {{ isRequired('emp_mobile_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">BLOOD GROUP</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_blood_group" id="emp_blood_group" data-plugin="switchery" {{ isRequired('emp_blood_group') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">PHONE NO.</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_phone_no" id="emp_phone_no" data-plugin="switchery" {{ isRequired('emp_phone_no') }}/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label">EMAIL</label>
                            <div class="col-md-9">
                            <input type="checkbox" name="emp_email" id="emp_email" data-plugin="switchery" {{ isRequired('emp_email') }}/>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Contact Details(Present)</h4>
                        <div class="example">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DIVISION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_division_id" id="emp_pre_addr_division_id" data-plugin="switchery" {{ isRequired('emp_pre_addr_division_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DISTRICT</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_district_id" id="emp_pre_addr_district_id" data-plugin="switchery" {{ isRequired('emp_pre_addr_district_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">THANA/UPAZILA</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_thana_id" id="emp_pre_addr_thana_id" data-plugin="switchery" {{ isRequired('emp_pre_addr_thana_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">WARD/UNION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_union_id" id="emp_pre_addr_union_id" data-plugin="switchery" {{ isRequired('emp_pre_addr_union_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">VILLAGE/WARD</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_village_id" id="emp_pre_addr_village_id" data-plugin="switchery" {{ isRequired('emp_pre_addr_village_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">STREET & HOLDING NO</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_pre_addr_street" id="emp_pre_addr_street" data-plugin="switchery" {{ isRequired('emp_pre_addr_street') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Contact Details(Permanent)</h4>
                        <div class="example">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DIVISION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_division_id" id="emp_par_addr_division_id" data-plugin="switchery" {{ isRequired('emp_par_addr_division_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DISTRICT</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_district_id" id="emp_par_addr_district_id" data-plugin="switchery" {{ isRequired('emp_par_addr_district_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">THANA/UPAZILA</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_thana_id" id="emp_par_addr_thana_id" data-plugin="switchery" {{ isRequired('emp_par_addr_thana_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">WARD/UNION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_union_id" id="emp_par_addr_union_id" data-plugin="switchery" {{ isRequired('emp_par_addr_union_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">VILLAGE/WARD</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_village_id" id="emp_par_addr_village_id" data-plugin="switchery" {{ isRequired('emp_par_addr_village_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">STREET & HOLDING NO</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_par_addr_street" id="emp_par_addr_street" data-plugin="switchery" {{ isRequired('emp_par_addr_street') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Photos</h4>
                        <div class="example">
                            
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">PHOTO</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_photo" id="emp_photo" data-plugin="switchery" {{ isRequired('emp_photo') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">NID SIGNATURE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_nid_signature" id="emp_nid_signature" data-plugin="switchery" {{ isRequired('emp_nid_signature') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">SIGNATURE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="emp_signature" id="emp_signature" data-plugin="switchery" {{ isRequired('emp_signature') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="organization" role="tabpanel" aria-labelledby="organization-tab" class="tab-pane fade px-4 py-5">
        <div class="row row-lg">
                    <div class="col-lg-4">
                        <div class="example-wrap m-md-0">
                            <h4 class="example-title">Organization</h4>
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PROJECT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_project_id" id="org_project_id" data-plugin="switchery" {{ isRequired('org_project_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">RECRUITMENT TYPE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_rec_type_id" id="org_rec_type_id" data-plugin="switchery" {{ isRequired('org_rec_type_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">LEVEL</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_level" id="org_level" data-plugin="switchery" {{ isRequired('org_level') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">POSITION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_position_id" id="org_position_id" data-plugin="switchery" {{ isRequired('org_position_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHONE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_phone" id="org_phone" data-plugin="switchery" {{ isRequired('org_phone') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">FAX</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_fax" id="org_fax" data-plugin="switchery" {{ isRequired('org_fax') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">JOINING DATE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_join_date" id="org_join_date" data-plugin="switchery" {{ isRequired('org_join_date') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">BASIC SALARY</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_basic_salary" id="org_basic_salary" data-plugin="switchery" {{ isRequired('org_basic_salary') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">FISCAL YEAR</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_fiscal_year_id" id="org_fiscal_year_id" data-plugin="switchery" {{ isRequired('org_fiscal_year_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">LAST INCREMENT DATE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_last_inc_date" id="org_last_inc_date" data-plugin="switchery" {{ isRequired('org_last_inc_date') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SECURITY AMOUNT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_security_amount" id="org_security_amount" data-plugin="switchery" {{ isRequired('org_security_amount') }}/>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="example-wrap m-md-0">
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">ADVANCED SECURITY AMOUNT </label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_adv_security_amount" id="org_adv_security_amount" data-plugin="switchery" {{ isRequired('org_adv_security_amount') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">INSTALLMENT AMOUNT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_installment_amount" id="org_installment_amount" data-plugin="switchery" {{ isRequired('org_installment_amount') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EDPS START MONTH</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_edps_start_month" id="org_edps_start_month" data-plugin="switchery" {{ isRequired('org_edps_start_month') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">STATUS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_status" id="org_status" data-plugin="switchery" {{ isRequired('org_status') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">JOB STATUS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_job_status" id="org_job_status" data-plugin="switchery" {{ isRequired('org_job_status') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PROJECT TYPE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_project_type_id" id="org_project_type_id" data-plugin="switchery" {{ isRequired('org_project_type_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">LOCATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_location" id="org_location" data-plugin="switchery" {{ isRequired('org_location') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">DEPARTMENT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_department" id="org_department" data-plugin="switchery" {{ isRequired('org_department') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">ROOM NO.</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_room_no" id="org_room_no" data-plugin="switchery" {{ isRequired('org_room_no') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">MOBILE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_mobile" id="org_mobile" data-plugin="switchery" {{ isRequired('org_mobile') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EMAIL</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_email" id="org_email" data-plugin="switchery" {{ isRequired('org_email') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="example-wrap m-md-0">
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">DEVICE ID</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_device_id" id="org_device_id" data-plugin="switchery" {{ isRequired('org_device_id') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">TOTAL SALARY</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_tot_salary" id="org_tot_salary" data-plugin="switchery" {{ isRequired('org_tot_salary') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SALARY INCREMENT YEAR</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_salary_inc_year" id="org_salary_inc_year" data-plugin="switchery" {{ isRequired('org_salary_inc_year') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SECURITY AMOUNT LOCATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_security_amount_location" id="org_security_amount_location" data-plugin="switchery" {{ isRequired('org_security_amount_location') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EDPS AMOUNT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_edps_amount" id="org_edps_amount" data-plugin="switchery" {{ isRequired('org_edps_amount') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EDPS LIFETIME (YEAR)</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_edps_lifetime" id="org_edps_lifetime" data-plugin="switchery" {{ isRequired('org_edps_lifetime') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NO OF INSTALLMENT</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_no_of_installment" id="org_no_of_installment" data-plugin="switchery" {{ isRequired('org_no_of_installment') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">TERMINATE / REGISTRATION DATE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="org_terminate_or_reg_date" id="org_terminate_or_reg_date" data-plugin="switchery" {{ isRequired('org_terminate_or_reg_date') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">USERNAME</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="username" id="username" data-plugin="switchery" {{ isRequired('username') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PASSWORD</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="password" id="password" data-plugin="switchery" {{ isRequired('password') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div id="Account" role="tabpanel" aria-labelledby="Account-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Account</h4>
                        <div class="example">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">BANK</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="acc_bank_id" id="acc_bank_id" data-plugin="switchery" {{ isRequired('acc_bank_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ACCOUNT TYPE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="acc_bank_acc_type" id="acc_bank_acc_type" data-plugin="switchery" {{ isRequired('acc_bank_acc_type') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">BRANCH</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="acc_bank_branch_id" id="acc_bank_branch_id" data-plugin="switchery" {{ isRequired('acc_bank_branch_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ACCOUNT NUMBER</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="acc_bank_acc_number" id="acc_bank_acc_number" data-plugin="switchery" {{ isRequired('acc_bank_acc_number') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="Education" role="tabpanel" aria-labelledby="Education-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                                <h4 class="example-title">Education</h4>
                                <div class="example">
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Exam Title</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_exam_title" id="edu_exam_title" data-plugin="switchery" {{ isRequired('edu_exam_title') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Department</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_department" id="edu_department" data-plugin="switchery" {{ isRequired('edu_department') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Institute Name</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_institute_name" id="edu_institute_name" data-plugin="switchery" {{ isRequired('edu_institute_name') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Board / University</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_board" id="edu_board" data-plugin="switchery" {{ isRequired('edu_board') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Result Type</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_res_type" id="edu_res_type" data-plugin="switchery" {{ isRequired('edu_res_type') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Result</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_result" id="edu_result" data-plugin="switchery" {{ isRequired('edu_result') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Out Of</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_res_out_of" id="edu_res_out_of" data-plugin="switchery" {{ isRequired('edu_res_out_of') }}/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 form-control-label">Passing Year</label>
                                        <div class="col-md-9">
                                        <input type="checkbox" name="edu_passing_year" id="edu_passing_year" data-plugin="switchery" {{ isRequired('edu_passing_year') }}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>
        </div>
        <div id="Training" role="tabpanel" aria-labelledby="Training-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Training</h4>
                        <div class="example">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">TRAINING TITLE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_title" id="train_title" data-plugin="switchery" {{ isRequired('train_title') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ORGANIZER</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_organizer" id="train_organizer" data-plugin="switchery" {{ isRequired('train_organizer') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">COUNTRY</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_country_id" id="train_country_id" data-plugin="switchery" {{ isRequired('train_country_id') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ADDRESS</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_address" id="train_address" data-plugin="switchery" {{ isRequired('train_address') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">TOPIC</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_topic" id="train_topic" data-plugin="switchery" {{ isRequired('train_topic') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">TRAINING YEAR</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_training_year" id="train_training_year" data-plugin="switchery" {{ isRequired('train_training_year') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DURATION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="train_duration" id="train_duration" data-plugin="switchery" {{ isRequired('train_duration') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="Experience" role="tabpanel" aria-labelledby="Experience-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                <div class="col-lg-6">
                    <div class="example-wrap m-md-0">
                        <h4 class="example-title">Experience</h4>
                        <div class="example">
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ORGANIZATION NAME</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_org_name" id="exp_org_name" data-plugin="switchery" {{ isRequired('exp_org_name') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ORGANIZATION TYPE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_org_type" id="exp_org_type" data-plugin="switchery" {{ isRequired('exp_org_type') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">LOCATION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_org_location" id="exp_org_location" data-plugin="switchery" {{ isRequired('exp_org_location') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DESIGNATION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_designation" id="exp_designation" data-plugin="switchery" {{ isRequired('exp_designation') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DEPARTMENT / PROJECT NAME</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_department" id="exp_department" data-plugin="switchery" {{ isRequired('exp_department') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">JOB RESPONSIBILITY</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_job_responsibility" id="exp_job_responsibility" data-plugin="switchery" {{ isRequired('exp_job_responsibility') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">AREA OF EXPERIENCE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_area_of_experience" id="exp_area_of_experience" data-plugin="switchery" {{ isRequired('exp_area_of_experience') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">DURATION</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_duration" id="exp_duration" data-plugin="switchery" {{ isRequired('exp_duration') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">START DATE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_start_date" id="exp_start_date" data-plugin="switchery" {{ isRequired('exp_start_date') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">END DATE</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_end_date" id="exp_end_date" data-plugin="switchery" {{ isRequired('exp_end_date') }}/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 form-control-label">ADDRESS</label>
                                <div class="col-md-9">
                                <input type="checkbox" name="exp_address" id="exp_address" data-plugin="switchery" {{ isRequired('exp_address') }}/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="Guarantor" role="tabpanel" aria-labelledby="Guarantor-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                    <div class="col-lg-6">
                        <div class="example-wrap m-md-0">
                            <h4 class="example-title">Relative Guarantor</h4>
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NAME</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_name" id="rel_guar_name" data-plugin="switchery" {{ isRequired('rel_guar_name') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">DESIGNATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_designation" id="rel_guar_designation" data-plugin="switchery" {{ isRequired('rel_guar_designation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">OCCUPATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_occupation" id="rel_guar_occupation" data-plugin="switchery" {{ isRequired('rel_guar_occupation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EMAIL</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_email" id="rel_guar_email" data-plugin="switchery" {{ isRequired('rel_guar_email') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">WORKING ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_working_address" id="rel_guar_working_address" data-plugin="switchery" {{ isRequired('rel_guar_working_address') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PERMANENT ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_par_address" id="rel_guar_par_address" data-plugin="switchery" {{ isRequired('rel_guar_par_address') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NID</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_nid" id="rel_guar_nid" data-plugin="switchery" {{ isRequired('rel_guar_nid') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">RELATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_relation" id="rel_guar_relation" data-plugin="switchery" {{ isRequired('rel_guar_relation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">MOBILE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_mobile" id="rel_guar_mobile" data-plugin="switchery" {{ isRequired('rel_guar_mobile') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHONE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_phone" id="rel_guar_phone" data-plugin="switchery" {{ isRequired('rel_guar_phone') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHOTO</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_photo" id="rel_guar_photo" data-plugin="switchery" {{ isRequired('rel_guar_photo') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SIGNATURE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="rel_guar_signature" id="rel_guar_signature" data-plugin="switchery" {{ isRequired('rel_guar_signature') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="example-wrap m-md-0">
                            <h4 class="example-title">Government Guarantor</h4>
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NAME</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_name" id="govt_guar_name" data-plugin="switchery" {{ isRequired('govt_guar_name') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">DESIGNATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_designation" id="govt_guar_designation" data-plugin="switchery" {{ isRequired('govt_guar_designation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">OCCUPATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_occupation" id="govt_guar_occupation" data-plugin="switchery" {{ isRequired('govt_guar_occupation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EMAIL</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_email" id="govt_guar_email" data-plugin="switchery" {{ isRequired('govt_guar_email') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">WORKING ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_working_address" id="govt_guar_working_address" data-plugin="switchery" {{ isRequired('govt_guar_working_address') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PERMANENT ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_par_address" id="govt_guar_par_address" data-plugin="switchery" {{ isRequired('govt_guar_par_address') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NID</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_nid" id="govt_guar_nid" data-plugin="switchery" {{ isRequired('govt_guar_nid') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">RELATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_relation" id="govt_guar_relation" data-plugin="switchery" {{ isRequired('govt_guar_relation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">MOBILE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_mobile" id="govt_guar_mobile" data-plugin="switchery" {{ isRequired('govt_guar_mobile') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHONE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_phone" id="govt_guar_phone" data-plugin="switchery" {{ isRequired('govt_guar_phone') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHOTO</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_photo" id="govt_guar_photo" data-plugin="switchery" {{ isRequired('govt_guar_photo') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SIGNATURE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="govt_guar_signature" id="govt_guar_signature" data-plugin="switchery" {{ isRequired('govt_guar_signature') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div id="Nominee" role="tabpanel" aria-labelledby="Nominee-tab" class="tab-pane fade px-4 py-5">
            <div class="col-lg-6">
                        <div class="example-wrap m-md-0">
                            <h4 class="example-title">Nominee</h4>
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NAME</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_name" id="nom_name" data-plugin="switchery" {{ isRequired('nom_name') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">RELATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_relation" id="nom_relation" data-plugin="switchery" {{ isRequired('nom_relation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PERCENTAGE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_percentage" id="nom_percentage" data-plugin="switchery" {{ isRequired('nom_percentage') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NID</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_nid" id="nom_nid" data-plugin="switchery" {{ isRequired('nom_nid') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_address" id="nom_address" data-plugin="switchery" {{ isRequired('nom_address') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">MOBILE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_mobile" id="nom_mobile" data-plugin="switchery" {{ isRequired('nom_mobile') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHOTO</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_photo" id="nom_photo" data-plugin="switchery" {{ isRequired('nom_photo') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">SIGNATURE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="nom_signature" id="nom_signature" data-plugin="switchery" {{ isRequired('nom_signature') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
        <div id="Reference" role="tabpanel" aria-labelledby="Reference-tab" class="tab-pane fade px-4 py-5">
            <div class="row row-lg">
                    <div class="col-lg-6">
                        <div class="example-wrap m-md-0">
                            <h4 class="example-title">Reference</h4>
                            <div class="example">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NAME</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_name" id="ref_name" data-plugin="switchery" {{ isRequired('ref_name') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">DESIGNATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_designation" id="ref_designation" data-plugin="switchery" {{ isRequired('ref_designation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">RELATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_relation" id="ref_relation" data-plugin="switchery" {{ isRequired('ref_relation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">NID</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_nid" id="ref_nid" data-plugin="switchery" {{ isRequired('ref_nid') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">MOBILE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_mobile" id="ref_mobile" data-plugin="switchery" {{ isRequired('ref_mobile') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">PHONE</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_phone" id="ref_phone" data-plugin="switchery" {{ isRequired('ref_phone') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">EMAIL</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_email" id="ref_email" data-plugin="switchery" {{ isRequired('ref_email') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">OCCUPATION</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_occupation" id="ref_occupation" data-plugin="switchery" {{ isRequired('ref_occupation') }}/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">WORKING ADDRESS</label>
                                    <div class="col-md-9">
                                    <input type="checkbox" name="ref_working_address" id="ref_working_address" data-plugin="switchery" {{ isRequired('ref_working_address') }}/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <!-- End rounded tabs -->
</div>
<div class="row">
                    <div class="col-lg">
                        <div class="form-group d-flex justify-content-center">
                            <div class="example example-buttons">
                                <a href="javascript:void(0)" onclick="goBack();"
                                    class="btn btn-default btn-round d-print-none">Back</a>
                                <button type="submit" class="btn btn-primary btn-round">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
<script> 

$('form').submit(function (event) {
    event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        $.ajax({
                url: "{{ url()->current() }}",
                type: 'POST',
                dataType: 'json',
                // contentType: false,
                data: $('form').serialize(),
                // processData: false,
                success: function (response) {
                    swal({
                        icon: 'success',
                        title: 'Success...',
                        text: 'Data Has Been Updated Successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = "{{ url()->current() }}";
                    });
                },
                error: function () {
                    alert('error!');
                }
            })
});
</script>
@endsection

