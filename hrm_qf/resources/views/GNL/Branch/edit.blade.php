@extends('Layouts.erp_master')

@section('content')
<!-- Page -->
<?php 

use App\Services\CommonService as Common;

    $hasAccModule = Common::checkActivatedModule('acc');
    if($hasAccModule)
        $accSysDate = (new DateTime(Common::systemCurrentDate($BranchData->id, 'acc')))->format('Y-m-d');
    $hasMfnModule = Common::checkActivatedModule('mfn');
    if($hasMfnModule)
        $mfnSysDate = (new DateTime(Common::systemCurrentDate($BranchData->id, 'mfn')))->format('Y-m-d');
    $hasInvModule = Common::checkActivatedModule('inv');
    if($hasInvModule)
        $invSysDate = (new DateTime(Common::systemCurrentDate($BranchData->id, 'inv')))->format('Y-m-d');
    $hasPosModule = Common::checkActivatedModule('pos');
    if($hasPosModule)
        $posSysDate = (new DateTime(Common::systemCurrentDate($BranchData->id, 'pos')))->format('Y-m-d');

?>
    <form enctype="multipart/form-data" method="POST" data-toggle="validator" novalidate="true" autocomplete="off">
        @csrf

        

        <div class="row">
            <div class="col-lg-9 offset-lg-3">
                @if(Common::getBranchId() ==  1 || Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                    @if(Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >GROUP</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="group_id" id="group_id">
                                        @foreach ($GroupData as $Row)
                                        {{-- for selected id edit  --}}
                                        <option value="{{$Row->id}}" {{ ($BranchData->group_id == $Row->id) ? 'selected="selected"' : '' }} >{{$Row->group_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >COMPANY</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="company_id" id="company_id">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Project</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select  class="form-control clsSelect2"name="project_id" id="project_id">
                                        <option value="{{$BranchData->project_id}}" selected>
                                            {{$BranchData->project['project_name']? $BranchData->project['project_name'] : 'n/a'}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >Project Type</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="project_type_id" id="project_type_id" >
                                        <option value="{{$BranchData->project_type_id}}" selected> 
                                            {{$BranchData->projectType['project_type_name']? $BranchData->projectType['project_type_name'] : 'n/a'}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >GROUP</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2 readonlySelect" name="group_id" id="group_id">
                                        @foreach ($GroupData as $Row)
                                        {{-- for selected id edit  --}}
                                        <option value="{{$Row->id}}" {{ ($BranchData->group_id == $Row->id) ? 'selected="selected"' : '' }} >{{$Row->group_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >COMPANY</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2 readonlySelect" name="company_id" id="company_id">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Project</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select  class="form-control clsSelect2 readonlySelect"name="project_id" id="project_id">
                                        <option value="{{$BranchData->project_id}}" selected>
                                            {{$BranchData->project['project_name']? $BranchData->project['project_name'] : 'n/a'}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar" >Project Type</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2 readonlySelect" name="project_type_id" id="project_type_id" >
                                        <option value="{{$BranchData->project_type_id}}" selected> 
                                            {{$BranchData->projectType['project_type_name']? $BranchData->projectType['project_type_name'] : 'n/a'}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Branch Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Branch Name" name="branch_name" id="textbranch_name"
                                   value="{{$BranchData->branch_name}}" required  data-error="Please enter branch name." @if(Common::getBranchId() ==  1 || Common::isSuperUser() == true) @else readonly @endif>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Branch Code</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Branch Code"
                                   name="branch_code" id="textbranch_code" required
                                   value="{{$BranchData->branch_code}}" data-error="Please enter branch code." @if(Common::getBranchId() ==  1 || Common::isSuperUser() == true) @else readonly @endif>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Contact Person</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Branch contact Person"
                                   name="contact_person" id="textcontact_person" required
                                   value="{{$BranchData->contact_person}}" data-error="Please enter branch contact person">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Email</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Branch Email" name="branch_email"
                                   id="textbranch_email"
                                   value="{{$BranchData->branch_email}}" data-error="Please enter branch email.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Mobile</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber"
                            name="branch_phone" id="textbranch_phone" placeholder="Mobile Number (01*********)" 
                            value="{{$BranchData->branch_phone}}"
                            required data-error="Please enter mobile number (01*********)" minlength="11" maxlength="11"
                            onblur="fnCheckDuplicate(
                                '{{base64_encode('gnl_branchs')}}',
                                this.name+'&&is_delete',
                                this.value+'&&0',
                                '{{url('/ajaxCheckDuplicate')}}',
                                this.id,
                                'errMsgPhone',
                                'mobile number');">
                        </div>
                        <div class="help-block with-errors is-invalid" id="errMsgPhone"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Address</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <textarea type="text" class="form-control round" placeholder="Enter Branch Address" name="branch_addr"
                                    id="textbranch_addr"  data-error="Please enter branch address.">{{$BranchData->branch_addr}}</textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                @if(Common::getBranchId() ==  1 || Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                    <div class="form-row align-items-center">
                        <label class="col-lg-3 input-title">Branch Opening Date</label>
                        <div class="col-lg-5 form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar round" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control round datepicker-branchOpen"  
                                        id="textbranch_opening_date" name="branch_opening_date" placeholder="DD-MM-YYYY"
                                        value="{{ (new Datetime($BranchData->branch_opening_date))->format('d-m-Y') }}">
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                    </div>

                @else
                <input type="hidden" class="form-control round datepicker-branchOpen"  
                                        id="textbranch_opening_date" name="branch_opening_date" placeholder="DD-MM-YYYY"
                                        value="{{ (new Datetime($BranchData->branch_opening_date))->format('d-m-Y') }}">

                @endif
                
                    <?php
                        if (!empty(Session::get('LoginBy.user_role.role_module'))) {
                        $SysModules = Session::get('LoginBy.user_role.role_module');
                        }
                    ?>

                    @if(Common::isSuperUser() == true || Common::isDeveloperUser() == true)

                        @if(count($SysModules) > 0)
                            @foreach ($SysModules as $module)
                                @if($module['short_name'] == 'pos')
                                <div class="form-row align-items-center">
                                    <label class="col-lg-3 input-title">POS Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            @if(isset($BranchData->soft_start_date) && $BranchData->soft_start_date == $posSysDate)
                                                <input type="text" class="form-control round datepicker-branchOpen"  
                                                id="soft_start_date" name="soft_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ (new Datetime($BranchData->soft_start_date))->format('d-m-Y') }}">
                                            @else
                                                <input type="text" class="form-control round readonlySelect datepicker-branchOpen"  
                                                id="soft_start_date" name="soft_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ (new Datetime($BranchData->soft_start_date))->format('d-m-Y') }}">
                                            @endif
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'acc')
                                <div class="form-row align-items-center">
                                    <label class="col-lg-3 input-title">Accounting Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            @if(isset($BranchData->acc_start_date) && $BranchData->acc_start_date == $accSysDate)
                                                <input type="text" class="form-control round datepicker-branchOpen"  
                                                id="acc_start_date" name="acc_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->acc_start_date ? (new Datetime($BranchData->acc_start_date))->format('d-m-Y') : ''}}">
                                            @else
                                                <input type="text" class="form-control round readonlySelect datepicker-branchOpen"  
                                                id="acc_start_date" name="acc_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->acc_start_date ? (new Datetime($BranchData->acc_start_date))->format('d-m-Y') : ''}}">
                                            @endif
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'mfn')
                                <div class="form-row align-items-center">
                                    <label class="col-lg-3 input-title">Microfinance Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            @if(isset($BranchData->mfn_start_date) && $BranchData->mfn_start_date == $mfnSysDate)
                                                <input type="text" class="form-control round datepicker-branchOpen"  
                                                id="mfn_start_date" name="mfn_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->mfn_start_date ? (new Datetime($BranchData->mfn_start_date))->format('d-m-Y') : '' }}">
                                            @else
                                                <input type="text" class="form-control round readonlySelect datepicker-branchOpen"  
                                                id="mfn_start_date" name="mfn_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->mfn_start_date ? (new Datetime($BranchData->mfn_start_date))->format('d-m-Y') : '' }}">
                                            @endif
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'fam')
                                <div class="form-row align-items-center hideDiv">
                                    <label class="col-lg-3 input-title">Fixed Asset Management Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control round readonlySelect datepicker-branchOpen"  
                                            id="fam_start_date" name="fam_start_date" placeholder="DD-MM-YYYY"
                                            value="{{ $BranchData->fam_start_date ? (new Datetime($BranchData->fam_start_date))->format('d-m-Y') : '' }}">
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'inv')
                                <div class="form-row align-items-center">
                                    <label class="col-lg-3 input-title">Inventory Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            @if(isset($BranchData->inv_start_date) && $BranchData->inv_start_date == $invSysDate)
                                                <input type="text" class="form-control round datepicker-branchOpen" 
                                                id="inv_start_date" name="inv_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->inv_start_date ? (new Datetime($BranchData->inv_start_date))->format('d-m-Y') : '' }}">
                                            @else
                                                <input type="text" class="form-control round readonlySelect datepicker-branchOpen" 
                                                id="inv_start_date" name="inv_start_date" placeholder="DD-MM-YYYY"
                                                value="{{ $BranchData->inv_start_date ? (new Datetime($BranchData->inv_start_date))->format('d-m-Y') : '' }}">
                                            @endif
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'proc')
                                <div class="form-row align-items-center hideDiv">
                                    <label class="col-lg-3 input-title">Procurement Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control round readonlySelect datepicker-branchOpen" 
                                            id="proc_start_date" name="proc_start_date" placeholder="DD-MM-YYYY"
                                            value="{{ $BranchData->proc_start_date ? (new Datetime($BranchData->proc_start_date))->format('d-m-Y') : '' }}">
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'bill')
                                <div class="form-row align-items-center hideDiv">
                                    <label class="col-lg-3 input-title">Billing System Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control round readonlySelect datepicker-branchOpen" 
                                            id="bill_start_date" name="bill_start_date" placeholder="DD-MM-YYYY"
                                            value="{{ $BranchData->bill_start_date ? (new Datetime($BranchData->bill_start_date))->format('d-m-Y') : '' }}">
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                                @if($module['short_name'] == 'HR')
                                <div class="form-row align-items-center hideDiv" >
                                    <label class="col-lg-3 input-title">Human Resource Opening Date</label>
                                    <div class="col-lg-5 form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control round readonlySelect datepicker-branchOpen"
                                            id="hr_start_date" name="hr_start_date" placeholder="DD-MM-YYYY"
                                            value="{{ $BranchData->hr_start_date ? (new 
                                            Datetime($BranchData->hr_start_date))->format('d-m-Y') : '' }}">
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif


                    @endif
                    @if(Common::getBranchId() ==  1 || Common::isSuperUser() == true || Common::isDeveloperUser() == true)
                
                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title" >Zone</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" name="zone_id" id="zoneId" >
                                        <option value = "">Select One</option>
                                        @foreach ($ZoneData as $Row)
                                            <option value="{{$Row->id}}" {{ ($BranchData->zone_id == $Row->id) ? 'selected="selected"' : '' }}>{{$Row->zone_name}} [{{$Row->zone_code}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title" >Region</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2 " name="region_id" id="regionId">
                                        <option value = "">Select One</option>
                                        @foreach ($RegionData as $Row)
                                                <option value="{{$Row->id}}" {{ ($BranchData->region_id == $Row->id) ? 'selected="selected"' : '' }}>{{$Row->region_name}} [{{$Row->region_code}}]</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title" >Area</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2 " name="area_id" id="areaId" >
                                        <option value = ""> Select One</option>
                                        @foreach ($AreaData as $Row)
                                            <option value="{{$Row->id}}" {{ ($BranchData->area_id == $Row->id) ? 'selected="selected"' : '' }}>{{$Row->area_name}} [{{$Row->area_code}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                 
                @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'exClass' => 'float-right'
                        ]])
            </div>
        </div>
    </form>
<!-- End Page -->
<script>
//   all company load
    $(document).ready(function () {
        $('.hideDiv').hide();
        // fnFilterRegion();
        // fnFilterArea();
        fnAjaxSelectBox(
                "company_id",
                "{{$BranchData->group_id }}",
                '{{base64_encode("gnl_companies")}}',
                '{{base64_encode("group_id")}}',
                '{{base64_encode("id,comp_name")}}',
                '{{url("/ajaxSelectBox")}}',
                '{{$BranchData->company_id}}'
                );
        fnAjaxSelectBox(
                "project_id",
                "{{ $BranchData->company_id }}",
                '{{base64_encode("gnl_projects")}}',
                '{{base64_encode("company_id")}}',
                '{{base64_encode("id,project_name")}}',
                '{{url("/ajaxSelectBox")}}',
                '{{$BranchData->project_id}}'
                );
        fnAjaxSelectBox(
                "project_type_id",
                "{{ $BranchData->project_id }}",
                '{{base64_encode("gnl_project_types")}}',
                '{{base64_encode("project_id")}}',
                '{{base64_encode("id,project_type_name")}}',
                '{{url("/ajaxSelectBox")}}',
                '{{ $BranchData->project_type_id}}'
                );

                $('form').submit(function (event) {
                    // event.preventDefault();
                    $(this).find(':submit').attr('disabled', 'disabled');
                    // $(this).submit();
                });

        @if($BranchData->is_approve == 1)
            $('.readonlySelect').prop('disabled', true);
        @endif
        $('#zoneId').change(function() {

            // if ($(this).val() != '') {
                var zoneId = $('#zoneId').val();
                $("#regionId").empty().append($('<option>', {
                    value: "",
                    text: "Select One"
                }));

                $.ajax({
                    method: "GET",
                    url: "{{ route('getRegion') }}",
                    dataType: "json",
                    async:false,
                    data: {
                        zoneId: zoneId,
                        returnType: 'json'
                    },
                    success: function (response) {
                        if (response['status'] == 'success') {
                            let result_data = response['result_data'];
                            let idArr = [];   // New code for zone,area

                            $.each(result_data, function (i, item) {

                                idArr.push($(this).attr("id"));  // New code for zone,area

                                $("#regionId").append($('<option>', {
                                    value: item.id,
                                    text: item.region_name + " [" + item.region_code + "]",
                                    // defaultSelected: false,
                                    // selected: true
                                }));
                            });
                        }
                    }
                });
            // }
        });

        $('#regionId').change(function() {
            var regionId = $('#regionId').val();
            $("#areaId").empty().append($('<option>', {
                value: "",
                text: "Select One"
            }));

            $.ajax({
                method: "GET",
                url: "{{ route('getArea') }}",
                dataType: "json",
                async:false,
                data: {
                    regionId: regionId,
                    returnType: 'json'
                },
                success: function (response) {
                    if (response['status'] == 'success') {
                        let result_data = response['result_data'];
                        let idArr = [];   // New code for zone,area

                        $.each(result_data, function (i, item) {

                            idArr.push($(this).attr("id"));  // New code for zone,area

                            $("#areaId").append($('<option>', {
                                value: item.id,
                                text: item.area_name + " [" + item.area_code + "]",
                                // defaultSelected: false,
                                // selected: true
                            }));
                        });
                    }
                }
            });
        });
    });

    // function fnFilterRegion()
    // {
    //     var zoneId = $('#zoneId').val();
    //     var regionId = $('#regionId').val();
    //     $("#regionId").empty().append($('<option>', {
    //         value: "",
    //         text: "Select One"
    //     }));

    //     $.ajax({
    //         method: "GET",
    //         url: "{{ route('getRegion') }}",
    //         dataType: "json",
    //         async:false,
    //         data: {
    //             zoneId: zoneId,
    //             returnType: 'json'
    //         },
    //         success: function (response) {
    //             if (response['status'] == 'success') {
    //                 let result_data = response['result_data'];
    //                 let idArr = [];   // New code for zone,area

    //                 $.each(result_data, function (i, item) {

    //                     idArr.push($(this).attr("id"));  // New code for zone,area

    //                     $("#regionId").append($('<option>', {
    //                         value: item.id,
    //                         text: item.region_name + " [" + item.region_code + "]",
    //                         // defaultSelected: false,
    //                         selected: (regionId == item.id)? true: false
    //                     }));
    //                 });
    //             }
    //         }
    //     });
    // }
    // function fnFilterArea()
    // {
    //     var regionId = $('#regionId').val();
    //     var areaId = $('#areaId').val();
    //     $("#areaId").empty().append($('<option>', {
    //         value: "",
    //         text: "Select One"
    //     }));

    //     $.ajax({
    //         method: "GET",
    //         url: "{{ route('getArea') }}",
    //         dataType: "json",
    //         async:false,
    //         data: {
    //             regionId: regionId,
    //             returnType: 'json'
    //         },
    //         success: function (response) {
    //             if (response['status'] == 'success') {
    //                 let result_data = response['result_data'];
    //                 let idArr = [];   // New code for zone,area

    //                 $.each(result_data, function (i, item) {

    //                     idArr.push($(this).attr("id"));  // New code for zone,area

    //                     $("#areaId").append($('<option>', {
    //                         value: item.id,
    //                         text: item.area_name + " [" + item.area_code + "]",
    //                         // defaultSelected: false,
    //                         selected: (areaId == item.id)? true: false
    //                     }));
    //                 });
    //             }
    //         }
    //     });
    // }

</script>
@endsection
