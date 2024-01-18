@php
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;
$loginUserInfo = Auth::user();
$preFix = last(request()->segments()) == 'others' ? '../../' : '../';
// dd($loginUserInfo);

## 14 is Employee Movement
$applicationPurpose = DB::table("hr_app_reasons")->where([["event_id", 14], ["is_delete", 0]])->get();

@endphp

<form id="movement_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            {{-- Application for others --}}
            <div class="row" id="applicationForOthers">

                <input hidden disabled id="other_add_branch_id" name="branch_id">
                <input hidden disabled id="other_add_employee_id" name="employee_id">
                {{-- <input hidden disabled id="other_add_department_id" name="department_id"> --}}

                {{-- <div id="branch_add_div" class="col-sm-4 form-group">
                    <label class="input-title RequiredStar">Branch</label>
                    <div class="input-group">

                        {!! HTML::forBranchFeildNew(true, 'branch_id', 'add_branch_id') !!}
                    </div>

                </div> --}}

                {!! HTML::forBranchFeildTTL([
                    'selectBoxShow'=> true,
                    'isRequired'=> true,
                    'elementId' => 'add_branch_id',
                    'divClass'=> "col-sm-4 form-group",
                    'formStyle'=> "vertical"
                ]) !!}


                <div class="col-sm-4 form-group">
                    <label class="input-title">Department</label>
                    <div class="input-group">
                        {!! HTML::forDepartmentFieldHr('add_department_id','department_id') !!}
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title ">Employee</label>
                    <div class="input-group">
                        <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select employee</option>
                        </select>
                    </div>
                </div>

            </div>


            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input name="appl_date" id="app_date_1" type="text" value="{{ date('d-m-Y') }}" style="z-index:99999 !important;" class="form-control datepicker-custom" placeholder="DD-MM-YYYY" readonly disabled>

                        <input name="appl_date" id="app_date_2" type="text" value="{{ date('d-m-Y') }}" style="z-index:99999 !important;" class="form-control datepicker-custom" placeholder="DD-MM-YYYY" hidden>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">Movement/Tour Date</label>
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input id="add_movement_date" name="movement_date" type="text" style="z-index:99999 !important;" class="form-control datepicker-custom common_effective_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Start Time</label>
                    <div class="input-group">
                        <input name="start_time" type="text" style="z-index:99999 !important;"
                            class="form-control timePicker" placeholder=" -- : -- ">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2 form-group">
                    <label class="input-title RequiredStar">End Time</label>
                    <div class="input-group">
                        <input name="end_time" type="text" style="z-index:99999 !important;"
                            class="form-control timePicker" placeholder=" -- : -- ">
                    </div>
                </div>

            </div>


            {{-- arnab part --}}
            <div class="row">

                <div class="col-sm-5 col-md-5 form-group">
                    <label class="input-title RequiredStar" >
                        Movement/Tour Location
                    </label>
                    <div class="d-flex" id="AddMovementTourLocationDiv">
                        <div class="radio-custom radio-primary" >
                            <input class="mx-3" type="radio" name="flexRadioDefault" id="flexRadioDefault2" value="0" checked>
                            <label class="form-check-label" for="flexRadioDefault2">
                                Others
                            </label>
                        </div>
                        <div class="radio-custom radio-primary" style="margin-inline-start: 20px;
                        writing-mode: horizontal-tb;">
                            <input class="mx-3" type="radio" name="flexRadioDefault" id="flexRadioDefault1" value="1">
                            <label class="form-check-label" for="flexRadioDefault1">
                                Branch Office
                            </label>
                        </div>

                    </div>
                    <div class="part1" style="width:100%;">
                        {!! HTML::forBranchFeildTTL([
                            'selectBoxShow'=> true,
                            'allBranchs'=>true,
                            'isRequired'=> true,
                            'elementTitle' => 'Branch To',
                            'elementId' => 'location_to_branch',
                            'elementName' => 'location_to_branch',
                            'divClass'=> "col-sm-12 form-group",
                            'formStyle'=> "vertical"
                        ]) !!}

                        {{-- <div class="" style="width:100%;">
                            {!! HTML::forBranchFeildNew(true, 'location_to_branch', 'location_to_branch','','','Branch To') !!}
                        </div> --}}
                    </div>
                    <div class="part2">
                        <div class="">
                            <input type="text" class="form-control mt-5" id="area" placeholder="Enter Area" name="location_to" required>
                        </div>
                    </div>

                    <div class=" mt-5">
                        <label class="input-title">Attachment</label>
                        <div class="input-group input-group-file">
                            {!! HTML::forAttachmentFieldHr('add_attachment') !!}
                        </div>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-2  form-group">
                    <label class="input-title RequiredStar">Purpose</label>
                    <div class="input-group">
                        {{-- <select name="reason" id="reason" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Purpose </option>
                            <option value="official">Official</option>
                            <option value="personal">Personal</option>
                        </select> --}}
                        {!! HTML::forReasonFieldHr(14, 'reason') !!}
                    </div>

                </div>

            </div>


            <div class="row" id="application_for_div" style="display: none;">
                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Application For</label>
                    <div class="input-group">
                        <select name="application_for" id="application_for" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Application For</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                            <option value="early">Early</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="attachment" class="row" style="padding-bottom: 5%;">

            </div>

            {{-- style="display: none;" --}}
            <div class="row" id="description_div">

                <div class="col-sm-12 form-group">
                    <label class="input-title">Description</label>
                    <div class="input-group">
                        <div class="input-group">
                            <textarea rows="5" id="add_description" name="description" class="form-control"
                                style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</form>

<script>

    var optionCount = $("#location_to_branch option").length;
    if (optionCount < 1) {
        $("#AddMovementTourLocationDiv").removeClass('d-flex');
        $("#AddMovementTourLocationDiv").addClass('d-none');
    }else{
        $("#AddMovementTourLocationDiv").removeClass('d-none');
        $("#AddMovementTourLocationDiv").addClass('d-flex');
    }

    $(document).ready(function(){
        $('.part1').hide();
        // $('.part2').hide();

        // arnab part
        $('input[name="flexRadioDefault"]').change(function() {
            var selectedValue = $('input[name="flexRadioDefault"]:checked').val();
            if(selectedValue == 1){
                $('.part1').show('2');
                $('.part2').show('2');
            }
            else if(selectedValue == 0){
                $('.part1').hide('2')
                $('.part2').show('2');
            }
        });

        window.attData = [];
        window.flag = 0;

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('.timePicker').datetimepicker({
            datepicker:false,
            format:'H:i'
        });

        // $('#reason').change(function(){
        //     if($(this).val() == 'official'){
        //         $('#description_div').show();
        //         // $('#application_for_div').show();
        //     }
        //     else{
        //         $('#description_div').hide();
        //         // $('#application_for_div').hide();
        //         // $('#application_for').val('');
        //     }
        // });


        if (appFor == "others"){

            let newOptionBranch = '<option value="" data-select2-id="-1" selected>Select Branch</option>';
            newOptionBranch += '<option value="0" data-select2-id="-2">All Branch</option>';
            $('#add_branch_id').prepend(newOptionBranch).trigger('change');
            $('#add_branch_id option:eq(2)').remove();

            let newOptionDepartment = '<option value="" data-select2-id="-3" selected>Select Department</option>';
            newOptionDepartment += '<option value="0" data-select2-id="-4">All Department</option>';
            $('#add_department_id').prepend(newOptionDepartment).trigger('change');
            $('#add_department_id option:eq(2)').remove();

            let newOptionEmployee = '<option value="" data-select2-id="-5">All Present Employee</option>';
                // newOptionEmployee += '<option value="0" data-select2-id="-6">All Employee</option>';
            $('#add_employee_id').prepend(newOptionEmployee).trigger('change');
            $('#add_employee_id option:eq(1)').remove();


        }


    });


    window.appFor = "{{ $appFor }}";
    if (appFor == "others") {
        $('#app_date_1').prop('disabled', false);
        $('#app_date_2').prop('disabled', true);
        configureOthersApplicationForm();
    } else {
        if ("{{ $loginUserInfo->emp_id }}" !== "") {
            $('#app_date_1').prop('disabled', true);
            $('#app_date_2').prop('disabled', false);
            configureSelfApplicationForm();
        } else {
            swal(
                'Sorry!!!',
                'This user is not valid employee.',
                'error'
            )
        }
    }

    $('#add_attachment').change(function(event){
        let files = event.target.files;

        $.each(files, function(key, file){

            attData.push(file);

            let html = '<div class="col-sm-2">' +
                            // '<a onClick="removeAttachment(this)" data-flag = '+ flag +' class="btn btn-xs float-right rmv-att-btn">&times;<a/>' +
                            '<a onClick="removeAttachment(this)" data-flag = '+ flag +' class="float-right rmv-att-btn"><i class="fa fa-times-circle" style="color:red; cursor: pointer;" aria-hidden="true"></i><a/>' +
                            '<iframe class="myiFrame" frameBorder="0" scrolling="auto" style="height:100%; width:100%;" src="'+ URL.createObjectURL(event.target.files[key]) +'"></iframe>' +
                        '</div>';
            flag ++;

            $('#attachment').append(html);

            $(".myiFrame").on("load", function() {
                let head = $(".myiFrame").contents().find("head");
                let css = '<style>img {  width: 100%;} </style>';
                $(head).append(css);
            });

        });

    });

    function removeAttachment(node){

        window.attData.forEach((element, index) => {
            if(index == $(node).data('flag')){
                window.attData[index] = null;
            }
        });

        $(node).closest('div').remove();

    }

    // showModal({
    //     titleContent: "Add Movement Application",
    //     footerContent: getModalFooterElement({
    //         'btnNature': {
    //             0: 'send',
    //             1: 'save',
    //         },
    //         'btnName': {
    //             0: 'Send',
    //             1: 'Draft',
    //         },
    //         'btnId': {
    //             0: 'add_sendBtn',
    //             1: 'add_draftBtn',
    //         }
    //     }),
    // });

    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                $('#add_employee_id').val(null).trigger('change');
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
            }
        );
    });

    // $('#add_sendBtn').click(function(event) {
    //     event.preventDefault();

    //     let formData = new FormData($('#movement_add_form')[0]);

    //     $.each(attData, function(key, file){
    //         if(file != null){
    //             formData.append('attachment[]', file, file.name);
    //         }
    //     });

    //     callApi("{{ url()->current() }}/../insert/send/api", 'post', formData,
    //         function(response, textStatus, xhr) {
    //             showApiResponse(xhr.status, '');
    //             hideModal();
    //             ajaxDataLoad();
    //         },
    //         function(response) {
    //             showApiResponse(response.status, JSON.parse(response.responseText).message);
    //         }
    //     );
    // });

    // $('#add_draftBtn').click(function(event) {
    //     event.preventDefault();

    //     let formData = new FormData($('#movement_add_form')[0]);

    //     $.each(attData, function(key, file){
    //         if(file != null){
    //             formData.append('attachment[]', file, file.name);
    //         }
    //     });

    //     callApi("{{ url()->current() }}/../insert/draft/api", 'post', formData,
    //         function(response, textStatus, xhr) {
    //             showApiResponse(xhr.status, '');
    //             hideModal();
    //             ajaxDataLoad();
    //         },
    //         function(response) {
    //             showApiResponse(response.status, JSON.parse(response.responseText).message);
    //         }
    //     )
    //     // callApi("{{ url()->current() }}/../insert/draft/api", 'post', formData,
    //     //     function(response, textStatus, xhr) {
    //     //         showApiResponse(xhr.status, '');
    //     //         hideModal();
    //     //         ajaxDataLoad();
    //     //     },
    //     //     function(response) {
    //     //         showApiResponse(response.status, JSON.parse(response.responseText).message);
    //     //     }
    //     // )
    // });

    $('#add_sendBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#movement_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/send/api", 'post', formData, function(response, textStatus, xhr) {
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

        let formData = new FormData($('#movement_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/draft/api", 'post', formData, function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
                hideModal();
                ajaxDataLoad();
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });


    function configureSelfApplicationForm() {

        $('#add_branch_id').prop('disabled', true);
        $('#add_employee_id').prop('disabled', true);
        $('#other_add_employee_id').prop('disabled', false);
        $('#other_add_branch_id').prop('disabled', false);

        // $('#row_department_id').hide();

        $('#other_add_employee_id').val({{ $loginUserInfo->emp_id }});
        $('#other_add_branch_id').val({{ $loginUserInfo->branch_id }});

        // $('#employee_add_div').addClass('col-sm-5 offset-sm-2 d-none');
        // $('#branch_add_div').addClass('col-sm-5 d-none');
        $('#applicationForOthers').hide();

        setTimeout(function() {

            $("form .clsSelect2").select2({
                dropdownParent: $("#commonModal")
            });

            $('#add_branch_id').val({{ $loginUserInfo->branch_id }}).trigger('change');
            // $('#add_resp_employee_id').val(response.result_data.resp_emp_id).trigger('change');

        }, 1200);

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Movement/Tour Application",
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

    }

    function configureOthersApplicationForm() {

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Movement/Tour Application",
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
    }
</script>
