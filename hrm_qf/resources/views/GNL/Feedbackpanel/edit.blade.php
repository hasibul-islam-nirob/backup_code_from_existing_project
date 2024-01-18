
<form id="feedback_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    
    <input type="hidden" name="id" value="{{$id}}">
    
    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Feedback Title</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                        </div>
                            <input id="f_title" name="f_title" type="text" 
                           class="form-control round " 
                            placeholder="Feedback Title">       
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar "> Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="date" style="z-index:99999 !important;" name="date"
                            type="text" class="form-control round " placeholder="DD-MM-YYYY" readonly>
                    </div>
                </div>
                <div class="col-sm-5  form-group">
                    <label class="input-title">Attachment</label>
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">

                        <input type="text" class="form-control round" readonly="">
                        <div class="input-group-append">
                            <span class="btn btn-success btn-file">
                                <i class="icon wb-upload" aria-hidden="true"></i>
                                <input type="file" id="add_attachment" name="attachment"
                                    onchange="validate_fileupload(this.id,2);">
                            </span>
                        </div>

                    </div>
                    <span style="font-size: 14px; color: green;">(Maximum file size 1 Mb)</span>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 form-group">
                    <label class="input-title">Description</label>
                    <div class="input-group">
                        <div class="input-group">
                            <textarea rows="5" id="f_description" name="f_description" class="ckeditor form-control"
                                style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    $(document).ready(function(){
        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
        // $('.ckeditor').ckeditor();
        CKEDITOR.replace( 'f_description' );

    });

    callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',
        function(response, textStatus, xhr) {

            $('#f_title').val(response.result_data.f_title);
            // $('#f_description').val(response.result_data.f_description);
            var description = CKEDITOR.instances['f_description'].setData(response.result_data.f_description);

            $('#f_description').val(f_description);
            $('#date').val(response.result_data.date);

            $('#edit_branch_id').val(response.result_data.branch_id);

            showModal({
                        titleContent: "Edit Feedback",
                        footerContent: getModalFooterElement({
                            'btnNature': {
                                0: 'send',
                                1: 'save',
                            },
                            'btnName': {
                                0: 'Send',
                                1: 'Draft',
                            },
                            'btnId': {
                                0: 'edit_sendBtn',
                                1: 'edit_draftBtn',
                            }
                        }),
                    });
                    configureActionEvents();
        },
        function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
    );

    function configureActionEvents() {

        $('#edit_sendBtn').click(function(e) {
            e.preventDefault();
            
            let formData = new FormData($('#feedback_edit_form')[0]);

            let descriptioon = $('#f_description').val();

            formData.append('f_description', CKEDITOR.instances['f_description'].getData());
            //  console.log(descriptioon);

            for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
            }
            // let formData = new FormData($('#feedback_edit_form')[0]);

            $.each(attData, function(key, file){
                if(file != null && file instanceof File){
                    formData.append('attachment[]', file, file.name);
                }
                else if(file != null){
                    formData.append('fileIds[]', file.id);
                }
            });

            callApi("{{ url()->current() }}/../../update/send/api", 'post', formData,
                function(response, textStatus, xhr) {
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            );
        });

        $("#edit_draftBtn").click(function(e) {
            e.preventDefault();

            let formData = new FormData($('#feedback_edit_form')[0]);
            let descriptioon = $('#f_description').val();

            formData.append('f_description', CKEDITOR.instances['f_description'].getData());

            $.each(attData, function(key, file){
                if(file != null && file instanceof File){
                    formData.append('attachment[]', file, file.name);
                }
                else if(file != null){
                    formData.append('fileIds[]', file.id);
                }
            });

            callApi("{{ url()->current() }}/../../update/draft/api", 'post', formData,
                function(response, textStatus, xhr) {
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            );
        });
    }
</script>
