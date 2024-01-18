{{-- Nominee --}}
<div id="Nominee" class="tab-pane show">
    <div class="row">
        <input hidden type="text" name="nomId[]">
        <div class="border {{ (isset($data['viewPage'])) ? 'col-lg-12' : 'col-lg-11'}}">

            <div class="row">

                <div class="col-lg-3 form-group">
                    <label class="input-title">Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="nom_name[]"
                               placeholder="Enter Nominee Name"
                               data-error="Please enter Nominee Name."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Relation</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="nom_relation[]"
                               placeholder="Enter Nominee Relation"
                               data-error="Please enter Nominee Relation."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Percentage</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control round" name="nom_percentage[]"
                               placeholder="Enter Nominee Percentage"
                               data-error="Please enter Nominee Percentage."
                        >%
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">NID</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="nom_nid[]"
                               placeholder="Enter Nominee NID"
                               data-error="Please enter Nominee NID."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-3 form-group">
                    <label class="input-title">Address</label>
                    <div class="input-group">
                        <textarea type="text" rows="2" class="form-control round" name="nom_address[]"
                                  placeholder="Enter Nominee Name"
                                  data-error="Please enter Nominee Name."></textarea>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Mobile</label>
                    <div class="input-group">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber" name="nom_mobile[]"
                               placeholder="Mobile Number (01*********)"
                               data-error="Please enter mobile number (01*********)"
                               minlength="11" maxlength="11">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Photo</label>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-group input-group-file">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file" name="nom_photo[]">
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-8 {{empty($data['photo']) ? 'd-none' : '' }}">
                            <img hidden class="demo_nom_photo" src="#" height="90px" width="100px">
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
                            <input onchange="readURL(this)" type="file" name="nom_photo[]">
                        </span>
                            <img hidden class="demo_nom_photo" src="#" height="39px">
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>--}}
                </div>

                <div class="col-lg-3 form-group">
                    <label class="col-lg-4 input-title">Signature</label>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-group input-group-file">
                                <span class="btn btn-success btn-file">
                                    <i class="icon wb-upload" aria-hidden="true"></i>
                                    <input onchange="readURL(this)" type="file" name="nom_signature[]">
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-8 {{empty($data['signature']) ? 'd-none' : '' }}">
                            <img hidden class="demo_nom_signature" src="#" height="90px" width="100px">
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
                                <input onchange="readURL(this)" type="file" name="nom_signature[]">
                            </span>
                            <img hidden class="demo_nom_signature" src="#" height="39px">
                        </div>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>--}}
                </div>

            </div>

        </div>
        @if(!isset($data['viewPage']))
            <div class="col-lg-1 d-flex align-items-center justify-content-center">

                <div class="row">
                    <button onclick="addNewNomineeField()" class="btn btn-primary btn-round" style="margin-top: 25%"><i class="fas fa-plus"></i></button>
                </div>

            </div>
        @endif

    </div>
</div>
<script>
    $(document).ready(function (){
        let empNomData = {!! json_encode((isset($empData['empNomData'])) ? $empData['empNomData'] : null) !!};
        if (empNomData !== null){
            for (let i=0; i<empNomData.length; i++){
                if (i !== 0){
                    addNewNomineeField();
                }
                //set id
                setEditData(document.querySelectorAll("[name='nomId[]']")[i], empNomData[i]['id']);

                setEditData(document.querySelectorAll("[name='nom_name[]']")[i], empNomData[i]['name']);
                setEditData(document.querySelectorAll("[name='nom_relation[]']")[i], empNomData[i]['relation']);
                setEditData(document.querySelectorAll("[name='nom_percentage[]']")[i], empNomData[i]['percentage']);
                setEditData(document.querySelectorAll("[name='nom_nid[]']")[i], empNomData[i]['nid']);
                setEditData(document.querySelectorAll("[name='nom_address[]']")[i], empNomData[i]['address']);
                setEditData(document.querySelectorAll("[name='nom_mobile[]']")[i], empNomData[i]['mobile']);
                //File
                setEditData(document.querySelectorAll("[name='nom_photo[]']")[i], empNomData[i]['photo'], document.querySelectorAll('.demo_nom_photo')[i]);
                setEditData(document.querySelectorAll("[name='nom_signature[]']")[i], empNomData[i]['signature'], document.querySelectorAll('.demo_nom_signature')[i]);
            }
        }
    });
    function addNewNomineeField(){
        let element = document.querySelector('#Nominee');
        let lastDiv = element.lastElementChild;

        let cloneNode = cleanCloneNode(lastDiv.cloneNode(true));
        let demoPhotoNode = cloneNode.querySelector('.demo_nom_photo');
        let demoSigNode = cloneNode.querySelector('.demo_nom_signature');
        demoPhotoNode.hidden = true;
        demoPhotoNode.src = '#';
        demoSigNode.hidden = true;
        demoSigNode.src = '#';

        element.append(cloneNode);
        if (!isViewPage){
            lastDiv.lastElementChild.lastElementChild.innerHTML = '<button onclick="removeNomineeField(this.parentNode.parentNode.parentNode)" class="btn btn-danger btn-round" style="margin-top: 25%"><i class="fas fa-minus"></i></button>';
        }
    }
    function removeNomineeField(node){
        node.remove();
    }
</script>
