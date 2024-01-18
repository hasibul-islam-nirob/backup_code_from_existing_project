          
<form id="feedback_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">
        <div class="col-sm-10 offset-sm-1">
            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Feedback Title</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                        </div>
                        <input id="f_title" name="f_title" type="text" 
                            class="form-control" placeholder="Feedback Title">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="date" name="date" type="text" style="z-index:99999 !important;"
                            class="form-control round  " placeholder="DD-MM-YYYY" readonly >
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
                    <div class="input-group">  
                        <label class="input-title">Description</label>
                        <div class="input-group">
                            <textarea class="form-control ckeditor" rows="5"  id="f_description" name="f_description" 
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

        CKEDITOR.replace( 'f_description' );

    });

   
    showModal({
        titleContent: "Add Feedback",
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
                0: 'add_sendBtn',
                1: 'add_draftBtn',
            }
        }),
        
    });

  
    $('#add_sendBtn').click(function(event) {
        event.preventDefault();
       
        let formData = new FormData($('#feedback_add_form')[0]);

        let descriptioon = $('#f_description').val();
        
        formData.append('f_description', CKEDITOR.instances['f_description'].getData());

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });


        callApi("{{ url()->current() }}/../insert/send/api", 'post', formData,
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

    $('#add_draftBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#feedback_add_form')[0]);

        let descriptioon = $('#f_description').val();
        
        formData.append('f_description', CKEDITOR.instances['f_description'].getData());

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/../insert/draft/api", 'post', formData,
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

