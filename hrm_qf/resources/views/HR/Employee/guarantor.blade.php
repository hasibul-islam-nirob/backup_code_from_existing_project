{{-- Guarantor --}}
<div id="Guarantor" class="tab-pane show">
    <div class="row">
        <input type="text" hidden name="govtGuarId">
        <input type="text" hidden name="relGuarId">
            <div class="col-lg-6">

                <h4>Government Guarantor</h4>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Name</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="govt_guar_name"
                                   placeholder="Enter Govt. Guarantor Name"
                                   data-error="Please enter Govt. Guarantor Name."
                            >
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Designation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="govt_guar_designation"
                                   placeholder="Enter Govt. Guarantor Designation"
                                   data-error="Please enter Govt. Guarantor Designation.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Occupation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="govt_guar_occupation"
                                   placeholder="Enter Govt. Guarantor Occupation"
                                   data-error="Please enter Govt. Guarantor Occupation.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Email</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="email" class="form-control round" name="govt_guar_email"
                                   placeholder="Enter Email">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Working Address</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <textarea class="form-control round " name="govt_guar_working_address"
                                      rows="2" placeholder="Enter Working Address" rows="3"
                                      data-error="Please Enter Address"></textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Permanent Address</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                                        <textarea class="form-control round " name="govt_guar_par_address"
                                                  rows="2" placeholder="Enter Permanent Address" rows="3"
                                                  data-error="Please Enter Address"></textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>



                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">NID</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control round"
                                   name="govt_guar_nid" placeholder="Enter NID number">
                        </div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Relation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="govt_guar_relation" style="width: 100%">
                                <option value="">Select Relation</option>
                                @foreach($data['relation'] as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Mobile</label>
                    <div class="col-lg-7">
                        <input type="text" class="form-control round textNumber" name="govt_guar_mobile"
                               pattern="[01][0-9]{10}" placeholder="Mobile Number (01*********)"
                               data-error="Please enter mobile number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Phone</label>
                    <div class="col-lg-7">
                        <input type="text" class="form-control round textNumber" name="govt_guar_phone"
                               pattern="[01][0-9]{10}" placeholder="Phone Number (01*********)"
                               data-error="Please enter Phone number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
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
                                    <input onchange="readURL(this)" type="file"  name="govt_guar_photo">
                                </span>
                                </div>
                            </div>
                            <div class="col-lg-8 {{empty($data['photo']) ? 'd-none' : '' }}">
                                <img hidden class="demo_govt_guar_photo" src="#" height="120px">
                            </div>
                        </div>
                        <div class="row">
                            <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                        </div>
                        {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                            <input type="text" class="form-control round" readonly="">
                            <div class="input-group-append">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file"  name="govt_guar_photo">
                                </span>
                                <img hidden class="demo_govt_guar_photo" src="#" height="39px">
                            </div>
                        </div>
                        <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-4 input-title">Signature</label>
                    <div class="col-lg-7 form-group">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="input-group input-group-file">
                                    <span class="btn btn-success btn-file">
                                        <i class="icon wb-upload" aria-hidden="true"></i>
                                        <input onchange="readURL(this)" type="file" name="govt_guar_signature">
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-8 {{empty($data['signature']) ? 'd-none' : '' }}">
                                <img hidden class="demo_govt_guar_signature" src="#" height="120px">
                            </div>
                        </div>
                        <div class="row">
                            <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                        </div>

                        {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                            <input type="text" class="form-control round" readonly="">
                            <div class="input-group-append">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file" name="govt_guar_signature">
                                </span>
                                <img hidden class="demo_govt_guar_signature" src="#" height="39px">
                            </div>
                        </div>
                        <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h4>Relative Guarantor</h4>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Name</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="rel_guar_name"
                                   placeholder="Enter Relative Guarantor Name"
                                   data-error="Please enter Relative Guarantor Name."
                            >
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Designation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="rel_guar_designation"
                                   placeholder="Enter Relative Guarantor Designation"
                                   data-error="Please enter Relative Guarantor Designation.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Occupation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="rel_guar_occupation"
                                   placeholder="Enter Relative Guarantor Occupation"
                                   data-error="Please enter Relative Guarantor Occupation.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Email</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="email" class="form-control round" name="rel_guar_email"
                                   placeholder="Enter Email">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Working Address</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                                        <textarea class="form-control round " name="rel_guar_working_address"
                                                  rows="2" placeholder="Enter Working Address" rows="3"
                                                  data-error="Please Enter Address"></textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Permanent Address</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                                        <textarea class="form-control round " name="rel_guar_par_address"
                                                  rows="2" placeholder="Enter Permanent Address" rows="3"
                                                  data-error="Please Enter Address"></textarea>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>



                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">NID</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input type="number" class="form-control round"
                                   name="rel_guar_nid" placeholder="Enter NID number">
                        </div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Relation</label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="rel_guar_relation" style="width: 100%">
                                <option value="">Select Relation</option>
                                @foreach($data['relation'] as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Mobile</label>
                    <div class="col-lg-7">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="rel_guar_mobile"
                               placeholder="Mobile Number (01*********)"
                               data-error="Please enter mobile number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row form-group align-items-center">
                    <label class="col-lg-4 input-title">Phone</label>
                    <div class="col-lg-7">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="rel_guar_phone"
                               placeholder="Phone Number (01*********)"
                               data-error="Please enter Phone number (01*********)"
                               minlength="11" maxlength="11">
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-4 input-title">Photo</label>
                    <div class="col-lg-7 form-group">
                        {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                            <input type="text" class="form-control round" readonly="">
                            <div class="input-group-append">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file" name="rel_guar_photo">
                                </span>
                                <img hidden class="demo_rel_guar_photo" src="#" height="39px">
                            </div>
                        </div>
                        <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="input-group input-group-file">
                                    <span class="btn btn-success btn-file">
                                        <i class="icon wb-upload" aria-hidden="true"></i>
                                        <input onchange="readURL(this)" type="file" name="rel_guar_photo">
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-8 {{empty($data['photo']) ? 'd-none' : '' }}">
                                <img hidden class="demo_rel_guar_photo" src="#" height="120px">
                            </div>
                        </div>
                        <div class="row">
                            <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                        </div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-4 input-title">Signature</label>
                    <div class="col-lg-7 form-group">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="input-group input-group-file">
                                    <span class="btn btn-success btn-file">
                                        <i class="icon wb-upload" aria-hidden="true"></i>
                                        <input onchange="readURL(this)" type="file" name="rel_guar_signature">
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-8 {{empty($data['signature']) ? 'd-none' : '' }}">
                                <img hidden class="demo_rel_guar_signature" src="#" height="120px">
                            </div>
                        </div>
                        <div class="row">
                            <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                        </div>

                        {{--<div class="input-group input-group-file" data-plugin="inputGroupFile">
                            <input type="text" class="form-control round" readonly="">
                            <div class="input-group-append">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file" name="rel_guar_signature">
                                </span>
                                <img hidden class="demo_rel_guar_signature" src="#" height="39px">
                            </div>
                        </div>
                        <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>--}}
                    </div>
                </div>
            </div>
        </div>
</div>
<script>
    $(document).ready(function (){
        let empGuarData = {!! json_encode((isset($empData['empGuarData'])) ? $empData['empGuarData'] : null) !!};
        if (empGuarData !== null){
            for (let i=0; i<empGuarData.length; i++){
                if (empGuarData[i]['guarantor_type'] === 'Govt'){
                    //Set id
                    setEditData(document.querySelector('[name=govtGuarId]'), empGuarData[i]['id']);

                    setEditData(document.querySelector('[name=govt_guar_name]'), empGuarData[i]['name']);
                    setEditData(document.querySelector('[name=govt_guar_designation]'), empGuarData[i]['designation']);
                    setEditData(document.querySelector('[name=govt_guar_occupation]'), empGuarData[i]['occupation']);
                    setEditData(document.querySelector('[name=govt_guar_email]'), empGuarData[i]['email']);
                    setEditData(document.querySelector('[name=govt_guar_working_address]'), empGuarData[i]['working_address']);
                    setEditData(document.querySelector('[name=govt_guar_par_address]'), empGuarData[i]['par_address']);
                    setEditData(document.querySelector('[name=govt_guar_nid]'), empGuarData[i]['nid']);
                    setEditData(document.querySelector('[name=govt_guar_relation]'), empGuarData[i]['relation']);
                    setEditData(document.querySelector('[name=govt_guar_mobile]'), empGuarData[i]['mobile']);
                    setEditData(document.querySelector('[name=govt_guar_phone]'), empGuarData[i]['phone']);
                    //File
                    setEditData(document.querySelector('[name=govt_guar_photo]'), empGuarData[i]['photo'], document.querySelector('.demo_govt_guar_photo'));
                    setEditData(document.querySelector('[name=govt_guar_signature]'), empGuarData[i]['signature'], document.querySelector('.demo_govt_guar_signature'));
                }
                else if (empGuarData[i]['guarantor_type'] === 'Relative'){
                    //Set id
                    setEditData(document.querySelector('[name=relGuarId]'), empGuarData[i]['id']);

                    setEditData(document.querySelector('[name=rel_guar_name]'), empGuarData[i]['name']);
                    setEditData(document.querySelector('[name=rel_guar_designation]'), empGuarData[i]['designation']);
                    setEditData(document.querySelector('[name=rel_guar_occupation]'), empGuarData[i]['occupation']);
                    setEditData(document.querySelector('[name=rel_guar_email]'), empGuarData[i]['email']);
                    setEditData(document.querySelector('[name=rel_guar_working_address]'), empGuarData[i]['working_address']);
                    setEditData(document.querySelector('[name=rel_guar_par_address]'), empGuarData[i]['par_address']);
                    setEditData(document.querySelector('[name=rel_guar_nid]'), empGuarData[i]['nid']);
                    setEditData(document.querySelector('[name=rel_guar_relation]'), empGuarData[i]['relation']);
                    setEditData(document.querySelector('[name=rel_guar_mobile]'), empGuarData[i]['mobile']);
                    setEditData(document.querySelector('[name=rel_guar_phone]'), empGuarData[i]['phone']);
                    //File
                    setEditData(document.querySelector('[name=rel_guar_photo]'), empGuarData[i]['photo'], document.querySelector('.demo_rel_guar_photo'));
                    setEditData(document.querySelector('[name=rel_guar_signature]'), empGuarData[i]['signature'], document.querySelector('.demo_rel_guar_signature'));
                }
            }
        }
    });

</script>
