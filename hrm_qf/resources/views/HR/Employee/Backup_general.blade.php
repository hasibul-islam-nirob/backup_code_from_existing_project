{{-- General Start--}}
<?php
use App\Services\CommonService as Common;
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;
?>
<div id="General" class="tab-pane show active"  role="tabpanel">
    @if(isset($data['viewPage']) || isset($data['editPage']))
        <div class="row">

            {{-- {!! HTML::forBranchFeildTTL([
                'selectBoxShow'=> true,
                'isRequired'=> true,
                'elementId' => 'branch_id',
                'divClass'=> "col-sm-7 input-group",
                'formStyle'=> "horizontal"
            ]) !!} --}}

            <div class="col-sm-6" >
                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'isRequired'=> true,
                    'elementId' => 'branch_id',
                    'divClass'=> "col-sm-12 input-group",
                    'formStyle'=> "horizontal"
                ]) !!}
            </div>
            <div class="col-sm-6">
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Employee Code</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control" id="emp_code" name="emp_code"
                                placeholder="Enter Employee Code" value=""
                                data-error="Please enter Employee Code." readonly>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    @endif
        <div class="row">
            <div class="col-lg-6">

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Employee Name (In English)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control " name="emp_name_eng"
                                   placeholder="Enter Employee Name (In English)"
                                   data-error="Please enter Employee name.">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Father Name (In English)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_fathers_name_eng" placeholder="Enter Father Name (In English)">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Mother Name (In English)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_mothers_name_eng" placeholder="Enter Mother Name (In English)">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    &nbsp;
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Gender</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <div class="radio-custom radio-primary">
                                <input type="radio" name="emp_gender" value="Male">
                                <label for="g1">Male &nbsp &nbsp </label>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input type="radio" name="emp_gender" value="Female">
                                <label for="g2">Female &nbsp &nbsp </label>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input type="radio" name="emp_gender" value="Others">
                                <label for="g3">Others &nbsp &nbsp</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Date of Birth</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="icon wb-calendar " aria-hidden="true"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control " name="emp_dob" id="emp_dob" autocomplete="off" placeholder="DD-MM-YYYY">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Marital Status</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="emp_marital_status"
                                    data-error="Please Select Marital Status">
                                <option value="">Select</option>
                                <option value="Married">Married</option>
                                <option value="Unmarried">Unmarried</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widow">Widow</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="display: none" class="emp-married form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Spouse Name (In English)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_spouse_name_en" placeholder="Enter Spouse Name (In English)">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    &nbsp;
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">NID</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control "
                                   name="emp_nid_no" placeholder="Enter NID number">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Birth Certificate No.</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control "
                                   name="emp_birth_certificate_no" placeholder="Enter Birth Certificate number">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Passport NO.</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control "
                                   name="emp_passport_no" placeholder="Enter Passport number">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">TIN</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control "
                                   name="emp_tin_no" placeholder="Enter TIN number">
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6">

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Employee Name (In Bangla)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control " name="emp_name_ban"
                                   placeholder="Enter Employee Name (In Bangla)"
                                   data-error="Please enter Employee name.">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Father Name (In Bangla)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_fathers_name_ban" placeholder="Enter Father Name (In Bangla)">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Mother Name (In Bangla)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_mothers_name_ban" placeholder="Enter Mother Name (In Bangla)">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    &nbsp;
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Religion</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <select class="form-control clsSelect2" data-error="Please select Religion"
                                    name="emp_religion">
                                <option value="">Select Religion</option>
                                <option value="Islam">Islam</option>
                                <option value="Hinduism">Hinduism</option>
                                <option value="Buddhists">Buddhists</option>
                                <option value="Christians">Christians</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Blood Group</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="emp_blood_group">
                                <option value="">Select Blood Group</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B-">B+</option>
                                <option value="B-">B-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="emp_children_div" style="display: none" class="emp-married form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Number of children</label>
                    <div class="col-lg-7">
                        <input type="text" class="form-control  textNumber" name="emp_children"
                               placeholder="Enter number of children">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div style="display: none"33 class="emp-married form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Spouse Name (In Bangla)</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control "
                                   name="emp_spouse_name_bn" placeholder="Enter Spouse Name (In Bangla)">
                        </div>
                    </div>
                </div>


                <div id="emp_children_div_spc" class="form-row form-group align-items-center">&nbsp;</div>
                <div class="form-row form-group align-items-center">&nbsp;</div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Driving License No.</label>
                    <div class="col-lg-7">
                        <input type="text" class="form-control  textNumber" name="emp_driving_license_no"
                               placeholder="Driving license"
                               data-error="Please enter driving license no">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Mobile No.</label>
                    <div class="col-lg-7">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control  textNumber" name="emp_mobile_no"
                               placeholder="Mobile Number (01*********)"
                               data-error="Please enter mobile number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Phone No.</label>
                    <div class="col-lg-7">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control  textNumber" name="emp_phone_no"
                               placeholder="Phone Number (01*********)"
                               data-error="Please enter Phone number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Email</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="email" class="form-control " name="emp_email"
                                   placeholder="Enter Email">
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Contact Details -->
        <div class="panel panel-default">
            <div class="panel-heading p-2">Contact Details</div>
            <div class="panel-body">

                <!-- Present Address -->
                <div class="input-title form-group mt-4 border-bottom">Present Address</div>
                <div class="presentAddressDiv">
                    <div class="row">

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Division</label>
                            <div class="input-group">
                                <select name="emp_pre_addr_division_id" class="form-control clsSelect2 division"
                                >
                                    <option value="">Select</option>
                                    @foreach ($data['divisions'] as $division)
                                    <option value="{{ $division->id }}">{{ $division->division_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">District</label>
                            <div class="input-group">
                                <select name="emp_pre_addr_district_id" class="form-control clsSelect2 district">
                                    <option value="">Select</option>
                                    @if(!empty($data['preAddress']))
                                        @foreach ($data['preAddress']['districts'] as $district)
                                            <option value="{{ $district->id }}">{{ $district->district_name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Thana/Upazila</label>
                            <div class="input-group">
                                <select name="emp_pre_addr_thana_id" class="form-control clsSelect2 upazila">
                                    <option value="">Select</option>
                                    @if(!empty($data['preAddress']))
                                        @foreach ($data['preAddress']['upazilas'] as $upazila)
                                            <option value="{{ $upazila->id }}">{{ $upazila->upazila_name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Ward/Union</label>
                            <div class="input-group">
                                <select name="emp_pre_addr_union_id" class="form-control clsSelect2 union">
                                    <option value="">Select</option>
                                    @if(!empty($data['preAddress']))
                                        @foreach ($data['preAddress']['unions'] as $union)
                                            <option value="{{ $union->id }}">{{ $union->union_name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Village/Ward</label>
                            <div class="input-group">
                                <select name="emp_pre_addr_village_id" class="form-control clsSelect2 village">
                                    <option value="">Select</option>
                                    @if(!empty($data['preAddress']))
                                        @foreach ($data['preAddress']['villages'] as $village)
                                            <option value="{{ $village->id }}">{{ $village->village_name }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Street & Holding No</label>
                            <div class="input-group">
                                <textarea class="form-control  streetHolding" name="emp_pre_addr_street" rows="2" placeholder="Enter Address" data-error="Please Enter Address"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Permanent Address -->
                <div class="input-title form-group mt-2 border-bottom">Permanent Address</div>

                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group checkbox-custom checkbox-primary">
                            <input type="checkbox" id="sameAsPreesent" name="sameAsPreesent">
                            <label class="input-title">Same As Present Address</label>
                        </div>
                    </div>
                </div>

                <div class="permanentAddressDiv">

                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <label class="input-title">Division</label>
                            <div class="input-group">
                                <select name="emp_par_addr_division_id" class="form-control clsSelect2 division">
                                    <option value="">Select</option>
                                    @foreach ($data['divisions'] as $division)
                                    <option value="{{ $division->id }}">{{ $division->division_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">District</label>
                            <div class="input-group">
                                <select name="emp_par_addr_district_id" class="form-control clsSelect2 district">
                                    <option value="">Select</option>
                                    @if(!empty($data['perAddress']))
                                        @foreach ($data['perAddress']['districts'] as $district)
                                            <option value="{{ $district->id }}">{{ $district->district_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Thana/Upazila</label>
                            <div class="input-group">
                                <select name="emp_par_addr_thana_id" class="form-control clsSelect2 upazila">
                                    <option value="">Select</option>
                                    @if(!empty($data['perAddress']))
                                        @foreach ($data['perAddress']['upazilas'] as $upazila)
                                            <option value="{{ $upazila->id }}">{{ $upazila->upazila_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Ward/Union</label>
                            <div class="input-group">
                                <select name="emp_par_addr_union_id" class="form-control clsSelect2 union">
                                    <option value="">Select</option>
                                    @if(!empty($data['perAddress']))
                                        @foreach ($data['perAddress']['unions'] as $union)
                                            <option value="{{ $union->id }}">{{ $union->union_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>


                    </div>

                    <div class="row">

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Village/Ward</label>
                            <div class="input-group">
                                <select name="emp_par_addr_village_id" class="form-control clsSelect2 village">
                                    <option value="">Select</option>
                                    @if(!empty($data['perAddress']))
                                        @foreach ($data['perAddress']['villages'] as $village)
                                            <option value="{{ $village->id }}">{{ $village->village_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label class="input-title">Street & Holding No</label>
                            <div class="input-group">
                                <textarea class="form-control  streetHolding" name="emp_par_addr_street"
                                          rows="2" placeholder="Enter Address" rows="3"
                                          data-error="Please Enter Address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Image -->
        <div class="panel panel-default">
            <div class="panel-heading p-2">Image</div>
            <div class="panel-body">

                <div style="margin-top: 20px" class="row">
                    <div class="col-lg-6">
                        <div class="form-row align-items-center">
                            <label class="col-lg-4 input-title">Signature</label>
                            <div class="col-lg-7 form-group">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-group input-group-file">
                                            <span class="btn btn-success btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input onchange="readURL(this)" type="file" name="emp_signature">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 {{empty($data['signature']) ? 'd-none' : '' }}">
                                        <img hidden class="demo_emp_signature" src="#" width="150px" height="120px">
                                    </div>
                                </div>
                                <div class="row">
                                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                                </div>
                                {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                                    <input type="text" class="form-control " readonly="">
                                    <div class="input-group-append">
                                            <span class="btn btn-success btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input onchange="readURL(this)" type="file" name="emp_signature">
                                            </span>
                                        <img hidden class="demo_emp_signature" src="#" height="39px">
                                    </div>
                                </div>
                                <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-lg-4 input-title">Photo</label>
                            <div class="col-lg-7 form-group">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-group input-group-file">
                                            <span class="btn btn-success btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input onchange="readURL(this)" type="file" name="emp_photo">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 {{empty($data['photo']) ? 'd-none' : '' }}">
                                        <img hidden class="demo_emp_photo" src="#" width="150px" height="120px">
                                    </div>
                                </div>
                                <div class="row">
                                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                                </div>
                                {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                                    <input type="text" class="form-control " readonly="">
                                    <div class="input-group-append">
                                            <span class="btn btn-success btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input onchange="readURL(this)" type="file" name="emp_photo">
                                            </span>
                                        <img hidden class="demo_emp_photo" src="#" height="39px">
                                    </div>
                                </div>
                                <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-row align-items-center">
                            <label class="col-lg-4 input-title">NID Signature</label>
                            <div class="col-lg-7 form-group">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-group input-group-file">
                                            <span class="btn btn-success btn-file">
                                                <i class="icon wb-upload" aria-hidden="true"></i>
                                                <input onchange="readURL(this)" type="file" name="emp_nid_signature">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 {{empty($data['nid_signature']) ? 'd-none' : '' }}">
                                        <img hidden class="demo_emp_nid_signature" src="#" width="150px" height="120px">
                                    </div>
                                </div>
                                <div class="row">
                                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                                </div>



                                {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                                    --}}{{--<input type="text" class="form-control " readonly="">--}}{{--
                                    <div class="input-group-append">
                                        <span class="btn btn-success btn-file">
                                            <i class="icon wb-upload" aria-hidden="true"></i>
                                            <input onchange="readURL(this)" type="file" name="emp_nid_signature">
                                        </span>
                                    </div>
                                </div>
                                <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
</div>

<script>
    let empPerData = {!! json_encode((isset($empData['empPerData'])) ? $empData['empPerData'] : null) !!};

    let empSysData = {!! json_encode((isset($empData['empSysUserData'])) ? $empData['empSysUserData'] : null) !!};

    let empData = {!! json_encode((isset($empData['emp'])) ? $empData['emp'] : null) !!};
    let isViewPage = {!!  json_encode(isset($data['viewPage'])) !!};

    $(document).ready(function (){
        if (empPerData !== null && empData !== null){
            setEditData(document.querySelector('[name=branch_id]'), empSysData[0]['branch_id']);

            setEditData(document.querySelector('[name=emp_code]'), empData['emp_code']);
            setEditData(document.querySelector('[name=emp_name_eng]'), empData['emp_name']);
            setEditData(document.querySelectorAll('[name=emp_gender]'), empData['gender']);


            setEditData(document.querySelector('[name=emp_name_ban]'), empPerData[0]['emp_name_bn']);
            setEditData(document.querySelector('[name=emp_fathers_name_eng]'), empPerData[0]['father_name_en']);
            setEditData(document.querySelector('[name=emp_fathers_name_ban]'), empPerData[0]['father_name_bn']);
            setEditData(document.querySelector('[name=emp_mothers_name_eng]'), empPerData[0]['mother_name_en']);
            setEditData(document.querySelector('[name=emp_mothers_name_ban]'), empPerData[0]['mother_name_bn']);

            setEditData(document.querySelector('[name=emp_spouse_name_en]'), empPerData[0]['spouse_name_en']);
            setEditData(document.querySelector('[name=emp_spouse_name_bn]'), empPerData[0]['spouse_name_bn']);
            setEditData(document.querySelector('[name=emp_driving_license_no]'), empPerData[0]['driving_license_no']);

            // setEditData(document.querySelectorAll('[name=emp_gender]'), empPerData[0]['gender']);
            setEditData(document.querySelector('[name=emp_dob]'), empPerData[0]['dob']);
            setEditData(document.querySelector('[name=emp_nid_no]'), empPerData[0]['nid_no']);

            setEditData(document.querySelector('[name=emp_marital_status]'), empPerData[0]['marital_status']);
            $('[name=emp_marital_status]').trigger('change');
            setEditData(document.querySelector('[name=emp_children]'), empPerData[0]['num_of_children']);

            setEditData(document.querySelector('[name=emp_religion]'), empPerData[0]['religion']);
            setEditData(document.querySelector('[name=emp_blood_group]'), empPerData[0]['blood_group']);
            setEditData(document.querySelector('[name=emp_birth_certificate_no]'), empPerData[0]['birth_certificate_no']);
            setEditData(document.querySelector('[name=emp_passport_no]'), empPerData[0]['passport_no']);
            setEditData(document.querySelector('[name=emp_tin_no]'), empPerData[0]['tin_no']);
            setEditData(document.querySelector('[name=emp_phone_no]'), empPerData[0]['phone_no']);
            setEditData(document.querySelector('[name=emp_mobile_no]'), empPerData[0]['mobile_no']);
            setEditData(document.querySelector('[name=emp_email]'), empPerData[0]['email']);

            setEditData(document.querySelector('[name=emp_pre_addr_division_id]'), empPerData[0]['pre_addr_division_id']);
            setEditData(document.querySelector('[name=emp_pre_addr_district_id]'), empPerData[0]['pre_addr_district_id']);
            setEditData(document.querySelector('[name=emp_pre_addr_thana_id]'), empPerData[0]['pre_addr_thana_id']);
            setEditData(document.querySelector('[name=emp_pre_addr_union_id]'), empPerData[0]['pre_addr_union_id']);
            setEditData(document.querySelector('[name=emp_pre_addr_village_id]'), empPerData[0]['pre_addr_village_id']);
            setEditData(document.querySelector('[name=emp_pre_addr_street]'), empPerData[0]['pre_addr_street']);
            setEditData(document.querySelector('[name=emp_par_addr_division_id]'), empPerData[0]['par_addr_division_id']);
            setEditData(document.querySelector('[name=emp_par_addr_district_id]'), empPerData[0]['par_addr_district_id']);
            setEditData(document.querySelector('[name=emp_par_addr_thana_id]'), empPerData[0]['par_addr_thana_id']);
            setEditData(document.querySelector('[name=emp_par_addr_union_id]'), empPerData[0]['par_addr_union_id']);
            setEditData(document.querySelector('[name=emp_par_addr_village_id]'), empPerData[0]['par_addr_village_id']);
            setEditData(document.querySelector('[name=emp_par_addr_street]'), empPerData[0]['par_addr_street']);

            //File
            setEditData(document.querySelector('[name=emp_photo]'), empPerData[0]['photo'], document.querySelector('.demo_emp_photo'));
            setEditData(document.querySelector('[name=emp_signature]'), empPerData[0]['signature'], document.querySelector('.demo_emp_signature'));
            setEditData(document.querySelector('[name=emp_nid_signature]'), empPerData[0]['nid_signature'], document.querySelector('.demo_emp_nid_signature'));

            var sameAsPreesentAddress = "{{ isset($data['sameAsPreesentAddress']) ? $data['sameAsPreesentAddress'] : false }}";
            if (sameAsPreesentAddress) {
                $('#sameAsPreesent').prop('checked', true);
            }
        }
    });

    $('[name=emp_marital_status]').change(function (e){
        //let empCldDivNode = document.querySelector('#emp_children_div');
        let empCldDivSpcNode = document.querySelector('#emp_children_div_spc');
        //empCldDivNode.hidden = true;
        $('.emp-married').hide();
        empCldDivSpcNode.hidden = false;

        if (this.value !== 'Unmarried' && this.value !== ''){
            //empCldDivNode.hidden = false;
            $('.emp-married').show();
            empCldDivSpcNode.hidden = true;
        }
    });

    /* address */
    // $('.division,.district,.upazila,.union').change(function (e) {
    $('.division,.district,.upazila,.union').on("select2:select", function(e) {
        var source = $(this);
        var url = '';

        if ($(this).hasClass('division')) {
            var target = source.parents().eq(2).find('.district');
            var url = 'getDistricts';
            var data = {
                divisionId: source.val()
            };

            source.parents().eq(2).find('.district,.upazila,.union').find('option:gt(0)').remove();
            source.parents().eq(3).find('.village').find('option:gt(0)').remove();
        }
        else if ($(this).hasClass('district')) {
            var target = source.parents().eq(2).find('.upazila');
            var url = 'getUpazilas';
            var data = {
                districtId: source.val()
            };

            source.parents().eq(2).find('.upazila,.union').find('option:gt(0)').remove();
            source.parents().eq(3).find('.village').find('option:gt(0)').remove();
        }
        else if ($(this).hasClass('upazila')) {
            var target = source.parents().eq(2).find('.union');
            var url = 'getUnions';
            var data = {
                upazilaId: source.val()
            };

            source.parents().eq(2).find('.union').find('option:gt(0)').remove();
            source.parents().eq(3).find('.village').find('option:gt(0)').remove();
        }
        else if ($(this).hasClass('union')) {
            var target = source.parents().eq(3).find('.village');
            var url = 'getVillages';
            var data = {
                unionId: source.val()
            };

            target.find('option:gt(0)').remove();
        }

        if (source.val() == '' || url == '') {
            return false;
        }
        let pUrl = './../' + url;
        if (empPerData !== null){
            pUrl = '../' + url;
        }

        $.ajax({
            type: "POST",
            url: pUrl,
            data: data,
            dataType: "json",
            success: function(options) {
                $.each(options, function(index, value) {
                    target.append("<option value=" + index + ">" + value +
                        "</option>");
                });
            },
            error: function() {
                alert('error!');
            }
        });

        $('#sameAsPreesent').trigger('change');

    });

    $('.village').on("select2:select", function(e) {
        $('#sameAsPreesent').trigger('change');
    });

    $('.streetHolding').on('input', function() {
        $('#sameAsPreesent').trigger('change');
    });
    /* end address */

    /* when click same as present address */
    $('#sameAsPreesent').change(function(e) {

        if ($(this).is(":checked")) {
            $(".permanentAddressDiv").css('pointer-events', 'none');

            cloneSelect('emp_pre_addr_division_id', 'emp_par_addr_division_id');
            cloneSelect('emp_pre_addr_district_id', 'emp_par_addr_district_id');
            cloneSelect('emp_pre_addr_thana_id', 'emp_par_addr_thana_id');
            cloneSelect('emp_pre_addr_union_id', 'emp_par_addr_union_id');
            cloneSelect('emp_pre_addr_village_id', 'emp_par_addr_village_id');
            $('textarea[name="emp_par_addr_street"]').val($('textarea[name="emp_pre_addr_street"]').val());

        } else {
            $(".permanentAddressDiv").css('pointer-events', '');
        }
    });
    /* end when click same as present address */

    /* clone select options */
    function cloneSelect(sourceName, targetName) {
        var source = $("select[name='" + sourceName + "']");
        var target = $("select[name='" + targetName + "']");

        target.empty();

        $("select[name='" + sourceName + "'] > option").each(function() {
            target.append("<option value=" + this.value + ">" + this.text + "</option>");
        });
        target.val(source.val());
    }
    /* end cloning select options */

    $('#emp_dob').datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        /*maxDate: systemDate*/
    }).keydown(false);

    function setEditData(node, value, imageTarget = null){
        //console.log(node.name + ' = ' + value);
        if (node === null){
            //console.log("nullll");
        }
        else if (node.tagName === 'INPUT' && (node.type === 'text' || node.type === 'number' || node.type === 'email')) {
            node.value = value;
        }
        else if (node.tagName === 'INPUT' && node.type === 'checkbox') {
            if (value === 0){
                node.checked = true;
            }
        }
        else if (node.tagName === 'INPUT' && node.type === 'file') {
            if (imageTarget !== null){
                var baseUrl = "{{ asset('/') }}";
                imageTarget.hidden = false;
                imageTarget.src = baseUrl + value;
            }
        }
        else if (node.tagName === 'TEXTAREA') {
            node.value = value;
        }
        else if (node.tagName === 'SELECT') {
            for (let o of node.options) {
                if (o.value == value) {
                    o.selected = true;
                }
            }
            $('.clsSelect2').select2();
        }
        else if (node.length > 1) {
            for (let n of node) {
                if (n.value === value) {
                    n.setAttribute('checked', 'checked');
                }
            }
        }
        else {

        }
    }

    function readURL(input, target) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                let imageNode = input.parentNode.parentNode.parentNode.nextElementSibling.firstElementChild;
                imageNode.hidden = false;
                imageNode.src = e.target.result;
                //input.parentNode.nextElementSibling.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
