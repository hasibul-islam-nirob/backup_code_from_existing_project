{{-- Training --}}
<div id="Training" class="tab-pane show">
    <div class="row">

        <div class="border {{ (isset($data['viewPage'])) ? 'col-lg-12' : 'col-lg-11'}}">

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Training Title</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="train_title[]"
                               placeholder="Enter Training Title"
                               data-error="Please enter Training Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Organizer</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="train_organizer[]"
                               placeholder="Enter Organizer"
                               data-error="Please enter Organizer.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Country</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="train_country_id[]"
                               placeholder="Enter Training County"
                               data-error="Please enter Training County."
                        >
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Address</label>
                    <div class="input-group">
                        <textarea type="text" class="form-control round" name="train_address[]" placeholder="Enter address" data-error="Please enter address." rows="2"></textarea>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 form-group">
                    <label class="input-title">Topic</label>
                    <div class="input-group">
                        <input type="text" class="form-control round" name="train_topic[]"
                               placeholder="Enter Training Topic"
                               data-error="Please enter Training Topic.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Training Year</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="train_training_year[]"
                               placeholder="Enter Training Year"
                               data-error="Please enter Training Year.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>

                <div class="col-lg-3 form-group">
                    <label class="input-title">Duration</label>
                    <div class="input-group">
                        <input type="number" class="form-control round" name="train_duration[]"
                               placeholder="Enter Training Duration"
                               data-error="Please enter Training Duration.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
        @if(!isset($data['viewPage']))
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <div class="row">
                    <button onclick="addNewTrainingField()" class="btn btn-primary btn-round" style="margin-top: 25%"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    $(document).ready(function (){
        let empTrainData = {!! json_encode((isset($empData['empTrainData'])) ? $empData['empTrainData'] : null) !!};
        if (empTrainData !== null){
            for (let i=0; i<empTrainData.length; i++){
                if (i !== 0){
                    addNewTrainingField();
                }
                setEditData(document.querySelectorAll("[name='train_title[]']")[i], empTrainData[i]['title']);
                setEditData(document.querySelectorAll("[name='train_organizer[]']")[i], empTrainData[i]['organizer']);
                setEditData(document.querySelectorAll("[name='train_country_id[]']")[i], empTrainData[i]['country_id']);
                setEditData(document.querySelectorAll("[name='train_address[]']")[i], empTrainData[i]['address']);
                setEditData(document.querySelectorAll("[name='train_topic[]']")[i], empTrainData[i]['topic']);
                setEditData(document.querySelectorAll("[name='train_training_year[]']")[i], empTrainData[i]['training_year']);
                setEditData(document.querySelectorAll("[name='train_duration[]']")[i], empTrainData[i]['duration']);
            }
        }
    });
    function addNewTrainingField(){
        let element = document.querySelector('#Training');
        let lastDiv = element.lastElementChild;
        element.append(cleanCloneNode(lastDiv.cloneNode(true)));
        if (!isViewPage){
            lastDiv.lastElementChild.lastElementChild.innerHTML = '<button onclick="removeTrainingField(this.parentNode.parentNode.parentNode)" class="btn btn-danger btn-round" style="margin-top: 25%"><i class="fas fa-minus"></i></button>';
        }
    }
    function removeTrainingField(node){
        node.remove();
    }


</script>
