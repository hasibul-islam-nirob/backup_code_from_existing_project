@extends('Layouts.erp_master')
@section('content')

<?php
use App\Services\CommonService as Common;
?>
<!-- Page -->
    <form enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf


        <ul class="nav nav-tabs nav-tabs-reverse nav-fill" id="TabID">
            <li class="nav-item">
                <a href="#Basic" class="nav-link active" data-toggle="tab">Basic</a>
            </li>

            <li class="nav-item">
                <a href="#Configaration" class="nav-link" data-toggle="tab">Configaration</a>
            </li>


        </ul>

        <div class="tab-content" style="background:none;">

            <!-- Basic Basic  -->
            <div class="tab-pane active" id="Basic">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3">


                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Group</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <select class="form-control clsSelect2"
                                     name="group_id" id="selgroup_id" required
                                    data-error="Select Group">
                                <option value="">Select Group</option>
                                @foreach ($GroupData as $Row)
                                <option value="{{$Row->id}}" >{{$Row->group_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Company Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="comp_name" placeholder="Enter Company Name"
                            required data-error="Please enter Company name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Company Code</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" name="comp_code" id="checkDuplicateCode"
                                   class="form-control round" placeholder="Enter Company Code"
                                   required data-error="Please enter company code."
                                   onblur="fnCheckDuplicate(
                                    '{{base64_encode('gnl_companies')}}',
                                    this.name+'&&is_delete',
                                    this.value+'&&0',
                                    '{{url('/ajaxCheckDuplicate')}}',
                                    this.id,
                                    'txtCodeError',
                                    'company code');">
                        </div>
                        <div class="help-block with-errors is-invalid" id="txtCodeError"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Company Phone</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber"
                            name="comp_phone" id="comp_phone" placeholder="Mobile Number (01*********)"
                            data-error="Please enter mobile number (01*********)" minlength="0" maxlength="11"
                            onblur="fnCheckDuplicate(
                                '{{base64_encode('gnl_companies')}}',
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
                    <label class="col-lg-3 input-title">Email</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="email" class="form-control round" name="comp_email" id="txtCompanyEmail" placeholder="Enter Company Email">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Address</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <textarea class="form-control round" name="comp_addr" id="txtCompanyAddress" rows="2" placeholder="Enter Address"></textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Website</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text"  class="form-control round" name="comp_web_add" id="txtCompanyWeb" placeholder="Example www.example.com">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>


                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Company logo</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group input-group-file" data-plugin="inputGroupFile">
                            <input type="text" class="form-control round" readonly="">
                            <div class="input-group-append">
                                <span class="btn btn-success btn-file" style="height: 30px">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input type="file" id="CompanyImage" name="comp_logo"
                                    onchange="validate_fileupload(this.id, 1, 'image');">
                                </span>
                            </div>
                        </div>
                        <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                    </div>
                </div>


                @if(Common::isSuperUser() == true)
                <div class="form-row align-items-center">
                    <label class="col-lg-12 input-title">
                        Module Selection
                    </label>
                    <div class="col-lg-12">
                        <div class="row">
                        <?php
                            $sysModules = Common::ViewTableOrder('gnl_sys_modules',
                            [['is_active', 1], ['is_delete', 0]],
                            ['id', 'module_name', 'module_short_name'],
                            ['id', 'ASC']
                            );
                            $i = 0;
                            foreach($sysModules as $module){
                                $i++;
                                ?>
                                <div class="col-lg-4">
                                    <div class="checkbox-custom checkbox-primary">
                                        <input type="checkbox" class="checkboxs" name="module_arr[]" id="module_arr_{{$i}}" value="{{$module->id}}" />
                                        <label for="module_arr_{{$i}}" style="color:#000;">{{$module->module_name}}</label>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                        </div>
                    </div>
                </div>
                @endif
                    </div>
                </div>
            </div>
            <!-- End Basic Basic  -->

            <!-- Configaration Configaration  -->
            <div class="tab-pane fade" id="Configaration">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3">


                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title">Company Type</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">

                                    <select class="form-control clsSelect2" name="company_type" id="company_type" required
                                            data-error="Select Print type" style="width: 100%;" >
                                        <option value="">Select One</option>
                                        @foreach ($CompanyType as $Row)
                                        <option value="{{$Row->id}}" >{{$Row->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title">Print Type</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">

                                    <select class="form-control clsSelect2" name="print_type" id="print_type" required
                                            data-error="Select Print type" style="width: 100%;">
                                        <option value="">Select One</option>
                                        <option value="A4">A4 Paper</option>
                                        <option value="POS">Pos Paper</option>
                                    </select>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Max Bill Print</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="max_bill_print" placeholder="Enter maximum bill print permission"
                                    required data-error="Please enter max bill print .">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Return Policy</label>
                            <div class="col-lg-3 form-group">
                                <div class="input-group">
                                    <input type="number" class="form-control round" name="return_policy_day" placeholder="Enter days"
                                    required data-error="Please enter days.">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <label class="col-lg-2"> Day's</label>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Vat</label>
                            <div class="col-lg-3 form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control round" name="vat" placeholder="Enter vat"
                                    required data-error="Please enter vat.">

                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <label class="col-lg-2"> %</label>
                        </div>


                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Fiscal Year Start</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select class="form-control clsSelect2" style="width: 100%;"
                                             name="fy_start_date" id="selectFiscalYearStart" required data-error="Select start fiscal year date">
                                        <option value="">Select Start Fiscal Year</option>
                                        <option value="01-01" >01-Jan</option>
                                        <option value="01-07" >01-July</option>
                                    </select>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                                @error('fy_start_date')
                                <div class="help-block with-errors is-invalid">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Fiscal Year End</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select id="endFiscalYear" class="form-control clsSelect2"
                                    required data-error="Select Group" disabled="true" style="width: 100%;">
                                        <option value="">Select End Fiscal Year</option>
                                        <option value="31-12">31-Dec</option>
                                        <option value="30-06">30-June</option>
                                    </select>
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <input type="hidden" name="fy_end_date" id="endFiscalYearI">
                        </div>

                        <div class="form-row align-items-center">
                            <label class="col-lg-3 input-title RequiredStar">Processing fee editable</label>
                            <div class="col-lg-5 form-group">
                                <div class="input-group">
                                    <select id="processing_fee_editable" name="processing_fee_editable" class="form-control clsSelect2"
                                            required data-error="Select Group" style="width: 100%;">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Configaration Configaration  -->

        </div>




    <div class="row">
        <div class="col-lg-9">
            <div class="form-group d-flex justify-content-center">
                <div class="example example-buttons">
                    <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                    <button type="submit" class="btn btn-primary btn-round" id="btnSubmitCompany">Save</button>
                </div>
            </div>
        </div>
    </div>


        </div>
    </form>
<!-- End Page -->

<script type="text/javascript">
    $('#selectFiscalYearStart').change(function(){

    // console.log($('#selectFiscalYearStart'));

    var startVal = $(this).children("option:selected").val();
    if (startVal === '01-01') {
    $('#endFiscalYear').find('option[value="31-12"]').attr('selected', true);
    $('#endFiscalYear').trigger('change');
    $('#endFiscalYearI').val("31-12");
    }
    else {
    $('#endFiscalYear').find('option[value="31-12"]').attr('selected', false);
    $('#endFiscalYear').trigger('change');
    }

    if (startVal === '01-07') {
    $('#endFiscalYear').find('option[value="30-06"]').attr('selected', true);
    $('#endFiscalYear').trigger('change');
    $('#endFiscalYearI').val("30-06");
    }
    else{
    $('#endFiscalYear').find('option[value="30-06"]').attr('selected', false);
    $('#endFiscalYear').trigger('change');
    }
    $('form').submit(function (event) {
        // event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        // $(this).submit();
    });
    });
    // $('btnSubmitCompany').click(function(){

    // });

</script>


@endsection
