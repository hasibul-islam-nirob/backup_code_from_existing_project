<form id="reason_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" >

    <input hidden value="" name="r_id" id="edit_id">
    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div id="branch_add_div" class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="application" id="application" style="width: 100%;" required data-error="Select Organization">
                        
                        </select>
                    </div>
                </div>

                <div id="employee_add_div" class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Reason</label>
                    <div class="input-group">
                        <input type="text" name="reason" id="reason" value="" style="width: 100%;" required>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <button class="d-none" type="submit" id="edit_updateBtn_submit">edit</button>
</form>


<script>

    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/{{ $id }}/api", 'post', new FormData($('#reason_edit_form')[0]),
            function(response, textStatus, xhr) {
                // console.log(response);

                applicationData(response.appEvent,  response.appData.event_id)

                var result_data = response.appData;
                var formObject = document.forms[0].elements;

                $('#edit_id').val("{{ $id }}");
                $('#reason').val(response.appData.reason);
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );

    });


    function applicationData(response, eventID){

        let option = "<option disabled value=''>Select application</option>";
        $.each(response, function(index, item) {
            option += "<option value='"+item.id+"' >"+item.event_title+"</option>";
        });
        $('#application').html(option);
    
        $.each(response, function(index, item) {
            if( item.id == eventID){
                $('#application option[value= '+item.id+' ]').attr('selected', true);
            }
        });

    }

    showModal({
        titleContent: "Edit Application Reason",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'update',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'edit_updateBtn',
            }
        }),
    });

    $('#edit_updateBtn').click(function(event) {
        $('#edit_updateBtn_submit').click();
    });

    $('#reason_edit_form').submit(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#reason_edit_form')[
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