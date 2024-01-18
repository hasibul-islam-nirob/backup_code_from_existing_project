<?php
use App\Services\HtmlService as HTML;
$loginUserInfo = Auth::user();
?>

<form id="attendance_add_by_file_form" enctype="multipart/form-data" method="post" data-toggle="validator"
    novalidate="true">
    @csrf

    <div class="row">
        <div class="col-sm-10 offset-sm-1">

            <div class="row">
                <div class="col-sm-4 form-group">
                    {!! HTML::forBranchFeildNew(true) !!}

                    {!! HTML::forCompanyFeild() !!}

                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('department_id') !!}
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Designation</label>
                    <div class="input-group">
                        {!! HTML::forDesignationFieldHr('designation_id') !!}
                    </div>
                </div>
            </div>


        </div>
    </div>



    <div class="row d-flex justify-content-center">
        <div class="col-sm-5 form-group" style="padding-top: 50px;">
            <label class="d-flex justify-content-center h5">------ Select a file ------</label>
            <div class="input-group input-group-file" data-plugin="inputGroupFile">
                <input type="text" class="form-control round" readonly="">
                <div class="input-group-append">
                    <span class="btn btn-success btn-file">
                        <i class="icon wb-upload" aria-hidden="true"></i>
                        <input type="file" id="attendance_file" onchange="validate_fileupload(this.id, 2);"
                            name="attendance_file" >
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center" style="padding-bottom: 50px;">

        <div class="col-sm-5">
            <p style="color: #000;"><span id="add_char_count"></span></p>
            <p style="color: firebrick;">[ <strong>N.B:</strong> Supported file formats are<strong> : csv, xls, xlsx, xml</strong> and
                maximum file size is <strong>2Mb</strong>.]</p>
        </div>
    </div>
</form>

<script>

    $(document).ready(function() {
       
    //    $('#department_id').attr('required', false)
    //    $('#designation_id').attr('required', false)
   });


    showModal({
        titleContent: "File Upload",
        footerContent: getModalFooterElement({
            'btnNature': {
                1: 'save',
            },
            'btnName': {
                1: 'Submit',
            },
            'btnId': {
                1: 'fileSubmitBtn',
            }
        }),
    });

    $('#fileSubmitBtn').click(function(e) {
        callApi("{{ url()->current() }}/../insertByFile/api", 'post', new FormData($(
                '#attendance_add_by_file_form')[
                0]),
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
</script>
