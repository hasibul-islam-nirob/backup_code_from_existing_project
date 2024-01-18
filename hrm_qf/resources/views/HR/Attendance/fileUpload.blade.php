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
                    {!! HTML::forBranchFeildNew($SelectBox = true, $FeildName = 'branch_id', $FeildID = 'branch_id',
                        $SelectedValue = null, $DisableFeild = '', $Title = 'Branch', $IgnoreHO = false,
                        $TransferToLoadFromBranch = false, $isRequired = false) !!}

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


            <div class="row d-flex justify-content-center">

                <div class="col-sm-6 form-group">
                    <label class="input-title ">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="emp_id" class="form-control clsSelect2" style="width: 100%">

                        </select>
                    </div>
                </div>

                <div class="col-sm-6 form-group">
                    <label class="input-title RequiredStar">Select a file </label>
                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                        <input type="text" class="form-control " readonly="">
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


        </div>
    </div>


    <div class="row d-flex justify-content-center" style="padding-bottom: 20px;">
        <div class="col-sm-10">
            <p style="color: #000;"><span id="add_char_count"></span></p>
            <p style="color: firebrick;">[ <strong>N.B:</strong> Supported file formats are<strong> : csv, xls, xlsx</strong> and maximum file size is <strong>2Mb</strong>.]</p>
        </div>
    </div>

    <div class="row d-flex justify-content-center" style="padding-bottom: 30px;">
        <div class="col-sm-5 form-group">
            <p style="color: rgb(9, 114, 23);">[ <strong>N.B:</strong> Download for example file formats<strong>
                <a href="{{url('hr/employee_attendance/downloadExampleFile')}}" >Click here</a> </strong>]</p>

        </div>

        <div class="col-sm-5 form-group">
            <p style="color: rgb(160, 12, 12);"><strong>Remember,</strong></p>
            <p style="color: rgb(160, 12, 12);">Please, the excel file "ID Number" and "Date/Time" field name don't change.</p>
            <p style="color: rgb(9, 114, 23);">'ID Number' it's Employee Code for this system, and <br>
                'Date/Time' it's Employee Entry Time & Date. </p>


        </div>
    </div>
</form>

<script>

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

    $(document).ready(function() {

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
        getEmployeeOptions();
    });

    $('form .clsSelect2').on('change', function(){
        getEmployeeOptions();
    })


    function getEmployeeOptions()
    {
        var selEmp = $('#add_employee_id').val();

        callApi("{{ url()->current() }}/../getData", 'get',
            {
                context:"employeeData",
                branchId: $("#branch_id").val(),
                departmentId: $("#department_id").val(),
                designationId: $("#designation_id").val(),
            },
            function(response, textStatus, xhr) {

                $('#add_employee_id').select2({
                    dropdownParent: $("#commonModal"),
                    data: response,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: function(data) {
                        return data.html;
                    },
                    templateSelection: function(data) {
                        return data.text;
                    }
                });

                $('#add_employee_id').val();

            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }, false, true
        );
    }


    $("#add_employee_id").on('change', function(){
        // autoSetValue()
    })

    function autoSetValue()
    {
        let id = $("#add_employee_id").val();

        callApi("{{ url()->current() }}/../../employee_attendance/employeeInfo/"+ id +"/api", 'post', '',

            function(response, textStatus, xhr) {

                $("#branch_id").val(response.branch_id).change();
                $("#department_id").val(response.department_id).change();
                $("#designation_id").val(response.designation_id).change();

            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );

    }

    $('#fileSubmitBtn').click(function(e) {
        callApi("{{ url()->current() }}/../insertByFile/api", 'post', new FormData($(
                '#attendance_add_by_file_form')[0]),
            function(response, textStatus, xhr) {

                if(response == 400){
                    swal({
                        icon: 'warning',
                        title: 'Warning...',
                        text: "Attendance rules not set. Please configure attendance rules. Go to Configuration > attendance rules.",
                    });
                }else{
                    showApiResponse(xhr.status, '');
                    hideModal();
                    ajaxDataLoad();
                }
                
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

</script>
