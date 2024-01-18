{{-- Experience --}}
<div id="Experience" class="tab-pane show">
    <div class="row">
        <div class="border {{ (isset($data['viewPage'])) ? 'col-lg-12' : 'col-lg-11'}}">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Organization Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="exp_org_name[]"
                               placeholder="Enter Organization Name"
                               data-error="Please enter Organization Name."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Organization Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="exp_org_type[]" style="width: 100%">
                            <option value="">Select</option>
                            <option value="Domestic">Domestic</option>
                            <option value="International">International</option>
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Location</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="exp_org_location[]"
                               placeholder="Enter Location"
                               data-error="Please enter Location."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Designation</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="exp_designation[]"
                               placeholder="Enter Designation"
                               data-error="Please enter Designation."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Department/Project Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="exp_department[]"
                               placeholder="Enter Department"
                               data-error="Please enter Department."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Job Responsibility</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="exp_job_responsibility[]"
                               placeholder="Enter Responsibility"
                               data-error="Please enter Responsibility."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title"> Area of Experience</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="exp_area_of_experience[]"
                               placeholder="Enter Area of Experience"
                               data-error="Please enter Area of Experience."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Duration</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="exp_duration[]"
                               placeholder="Enter Duration"
                               data-error="Please enter Duration."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Start Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control round" name="exp_start_date[]" placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">End Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control round" name="exp_end_date[]" placeholder="DD-MM-YYYY" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Address</label>
                    <div class="input-group">
                    <textarea type="text" class="form-control round" name="exp_address[]"
                              placeholder="Enter Address"
                              data-error="Please enter Address."></textarea>
                    </div>
                </div>
            </div>
        </div>
        @if(!isset($data['viewPage']))
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <div class="row">
                    <button onclick="addNewExperienceField()" class="btn btn-primary btn-round" style="margin-top: 25%"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    $(document).ready(function (){
        let empExpData = {!! json_encode((isset($empData['empExpData'])) ? $empData['empExpData'] : null) !!};
        if (empExpData !== null){
            for (let i=0; i<empExpData.length; i++){
                if (i !== 0){
                    addNewExperienceField();
                }
                setEditData(document.querySelectorAll("[name='exp_org_name[]']")[i], empExpData[i]['org_name']);
                setEditData(document.querySelectorAll("[name='exp_org_type[]']")[i], empExpData[i]['org_type']);
                setEditData(document.querySelectorAll("[name='exp_org_location[]']")[i], empExpData[i]['org_location']);
                setEditData(document.querySelectorAll("[name='exp_designation[]']")[i], empExpData[i]['designation']);
                setEditData(document.querySelectorAll("[name='exp_department[]']")[i], empExpData[i]['department']);
                setEditData(document.querySelectorAll("[name='exp_job_responsibility[]']")[i], empExpData[i]['job_responsibility']);
                setEditData(document.querySelectorAll("[name='exp_area_of_experience[]']")[i], empExpData[i]['area_of_experience']);
                setEditData(document.querySelectorAll("[name='exp_duration[]']")[i], empExpData[i]['duration']);
                setEditData(document.querySelectorAll("[name='exp_start_date[]']")[i], empExpData[i]['start_date']);
                setEditData(document.querySelectorAll("[name='exp_end_date[]']")[i], empExpData[i]['end_date']);
                setEditData(document.querySelectorAll("[name='exp_address[]']")[i], empExpData[i]['address']);
            }
        }
        console.log()
    });

    function addNewExperienceField(){
        $('.clsSelect2').select2('destroy');
        let element = document.querySelector('#Experience');
        let lastDiv = element.lastElementChild;
        element.append(cleanCloneNode(lastDiv.cloneNode(true)));
        if (!isViewPage){
            lastDiv.lastElementChild.lastElementChild.innerHTML = '<button onclick="removeExperienceField(this.parentNode.parentNode.parentNode)" class="btn btn-danger btn-round" style="margin-top: 25%"><i class="fas fa-minus"></i></button>';
        }
        $('.clsSelect2').select2();
    }

    function removeExperienceField(node){
        node.remove();
    }

    $("[name='exp_end_date[]'], [name='exp_start_date[]']").datepicker({
        dateFormat: 'dd-mm-yy',
        orientation: 'bottom',
        autoclose: true,
        todayHighlight: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:+10',
        /*maxDate: systemDate*/
    }).keydown(false);


</script>
