{{-- Reference --}}
<div id="Reference" class="tab-pane show">
    <div class="row">
        <div class="border {{ (isset($data['viewPage'])) ? 'col-lg-12' : 'col-lg-11'}}">
            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="ref_name[]"
                               placeholder="Enter Reference Name"
                               data-error="Please enter Reference Name."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Designation</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="ref_designation[]"
                               placeholder="Enter Designation Name"
                               data-error="Please enter Designation Name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Relation</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="ref_relation[]"
                               placeholder="Enter Relation "
                               data-error="Please enter Relation ."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">NID</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="ref_nid[]"
                               placeholder="Enter NID"
                               data-error="Please enter NID."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Mobile</label>
                    <div class="input-group">
                        <input type="text" class="form-control round textNumber" name="ref_mobile[]"
                               placeholder="Mobile Number (01*********)"
                               data-error="Please enter mobile number (01*********)"
                               minlength="11" maxlength="11">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Phone</label>
                    <div class="input-group">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="ref_phone[]"
                               placeholder="Phone Number (01*********)"
                               data-error="Please enter Phone number (01*********)"
                               minlength="11" maxlength="11">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Email</label>
                    <div class="input-group">
                        <input type="email" class="form-control round" name="ref_email[]"
                               placeholder="Enter Email"
                               data-error="Please enter Email."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Occupation</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="ref_occupation[]"
                               placeholder="Enter Occupation "
                               data-error="Please enter Occupation ."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Working Address</label>
                    <div class="input-group">
                        <textarea type="text" class="form-control round" name="ref_working_address[]"
                                  placeholder="Enter Nominee Name"
                                  data-error="Please enter Nominee Name."
                                  rows="2"></textarea>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
        @if(!isset($data['viewPage']))
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <div class="row">
                    <button onclick="addNewReferenceField()" class="btn btn-primary btn-round" style="margin-top: 25%"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    $(document).ready(function (){
        let empRefData = {!! json_encode((isset($empData['empRefData'])) ? $empData['empRefData'] : null) !!};
        if (empRefData !== null){
            for (let i=0; i<empRefData.length; i++){
                if (i !== 0){
                    addNewReferenceField();
                }
                setEditData(document.querySelectorAll("[name='ref_name[]']")[i], empRefData[i]['name']);
                setEditData(document.querySelectorAll("[name='ref_designation[]']")[i], empRefData[i]['designation']);
                setEditData(document.querySelectorAll("[name='ref_relation[]']")[i], empRefData[i]['relation']);
                setEditData(document.querySelectorAll("[name='ref_nid[]']")[i], empRefData[i]['nid']);
                setEditData(document.querySelectorAll("[name='ref_mobile[]']")[i], empRefData[i]['mobile']);
                setEditData(document.querySelectorAll("[name='ref_phone[]']")[i], empRefData[i]['phone']);
                setEditData(document.querySelectorAll("[name='ref_email[]']")[i], empRefData[i]['email']);
                setEditData(document.querySelectorAll("[name='ref_occupation[]']")[i], empRefData[i]['occupation']);
                setEditData(document.querySelectorAll("[name='ref_working_address[]']")[i], empRefData[i]['working_address']);
            }
        }
    });
    function addNewReferenceField(){
        let element = document.querySelector('#Reference');
        let lastDiv = element.lastElementChild;
        element.append(cleanCloneNode(lastDiv.cloneNode(true)));
        if (!isViewPage){
            lastDiv.lastElementChild.lastElementChild.innerHTML = '<button onclick="removeReferenceField(this.parentNode.parentNode.parentNode)" class="btn btn-danger btn-round" style="margin-top: 25%"><i class="fas fa-minus"></i></button>';
        }
    }
    function removeReferenceField(node){
        node.remove();
    }
</script>
