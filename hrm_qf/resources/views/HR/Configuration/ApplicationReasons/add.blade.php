
<form id="reason_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" >

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div id="branch_add_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="application" id="application" style="width: 100%;" required>
                            
                        </select>
                    </div>
                </div>

                <div id="employee_add_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Reason</label>
                    <div class="input-group">
                        <input type="text" name="reason" style="width: 100%;" required>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <button class="d-none" type="submit" id="add_saveBtn_submit">save</button>
</form>

<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });


        callApi("{{ url()->current() }}/../getData", 'get', {context:"ApplicationData"}, 
            function(response, textStatus, xhr) {
                applicationData(response.appEvent)
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );


    });


    function applicationData(response){
        // console.log(response);
        let option = "<option value=''>Select application</option>";

        $.each(response, function(index, item) {
            option += "<option value='"+item.id+"' >"+item.event_title+"</option>";
        });
        // console.log(option);
        $('#application').html(option);
    }

    showModal({
        titleContent: "Add Application Reason",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'add_saveBtn',
            }
        }),
    });

    $('#add_saveBtn').click(function(event) {
        $('#add_saveBtn_submit').click();
    });

    $('#reason_add_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#reason_add_form')[
                0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });
</script>
