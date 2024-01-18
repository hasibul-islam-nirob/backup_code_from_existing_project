<?php
use App\Services\HtmlService as HTML;
?>

<style>
    .modal-lg {
        max-width: 50%;
    }
</style>

{{-- novalidate="true" --}}
<form id="leave_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    @csrf

    {{-- <div class="row">

        <div class="col-sm-10 offset-sm-1"> --}}

            <div class="row p-15">

                <div id="apl_div" class="col-sm-12">

                    <div class="row">

                        <div id="branch_add_div" class="col-sm-4 form-group">
                            {{-- <label class="input-title RequiredStar">Branch</label> --}}
                            <div class="input-group">
                                {{-- {!! HTML::forBranchFieldHr('add_branch_id', 'branch_id') !!} --}}
                                {!! HTML::forBranchFeildNew(true, 'branch_id', 'add_branch_id','','','Branch') !!}
                            </div>
                        </div>
        
                        <div id="employee_add_div" class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Employee</label>
                            <div class="input-group">
                                <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select employee</option>
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Application Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="add_leave_date" name="leave_date" type="text" value="{{ date('d-m-Y') }}" style="z-index:99999 !important;"
                                    class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
        
                    </div>
        
                    <div class="row">
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Leave Category</label>
                            <div class="input-group">
                                {!! HTML::forLeaveCategoryHr('add_leave_cat_id', 'leave_cat_id') !!}
                            </div>
                        </div>
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Date From</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="add_date_from" style="z-index:99999 !important;" name="date_from"
                                    type="text" class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                            </div>

                        </div>
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Date To</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="add_date_to" style="z-index:99999 !important;" name="date_to"
                                    type="text" class="form-control datepicker-custom" placeholder="DD-MM-YYYY">
                            </div>
                        </div>
        
                    </div>
        
                    <div class="row">
        
                        <div class="col-sm-4 form-group">
                            <label class="input-title RequiredStar">Reason</label>
                            <div class="input-group">
                                {!! HTML::forReasonFieldHr(5, 'add_reason') !!}
                            </div>
                        </div>
        
                        <div id="employee_add_div" class="col-sm-4 form-group">

                            <label class="input-title RequiredStar">Responsible Person</label>
                            <div class="input-group">
                                <select id="add_resp_employee_id" name="resp_employee_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select employee</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-sm-4 form-group">

                            <label class="input-title">File Attachment</label>
                            <div class="input-group input-group-file">
        
                                {!! HTML::forAttachmentFieldHr('add_attachment') !!}
        
                            </div>

                        </div>
        
                    </div>
        
                    <div id="attachment" class="row" style="padding-bottom: 5%;">
                        
                    </div>
        
                    <div class="row">
        
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
    
                <div id="summery_div" class="col-sm-4" style="border-left: double black; margin-bottom: 20px; display: none">

                    <div class="row">
                        <div class="col-sm-12">
                            <h4  id="summary_table_header" class="text-center">Leave Summary</h4>

                            <table class="table w-full table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Cat. Name</th>
                                        <th>Allocated</th>
                                        <th>Eligible</th>
                                        <th>Consumed</th>
                                        <th>Remaining</th>
                                    </tr>
                                </thead>
        
                                <tbody id="lv_details_table"></tbody>
                            </table>

                            <h6 style="color: #000">
                                Leave Applied For : 
                                    <span id="num_of_leaves_div" style="color: green"> 0 </span> <i>days</i> 
                                
                            </h6>
                        </div>
                    </div>

                </div>

            </div>

        {{-- </div>

    </div> --}}

</form>

<script>

    // $('.datepicker-custom').datepicker({
    
    //     dateFormat: 'dd-mm-yy',
    //     orientation: 'bottom',
    //     autoclose: true,
    //     todayHighlight: true,
    //     changeMonth: true,
    //     changeYear: true,
    //     yearRange: '1900:+10',
    //     showButtonPanel: true,
    //     todayButton: false,
    //     onClose: function(dateText, inst) {
    //         // var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
    //         // var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
    //         // var date = $("#ui-datepicker-div .ui-datepicker-date :selected").val();
    //         // $(this).val($.datepicker.formatDate('dd-mm-yy', new Date(year, month, date)));
    //     },
        
    //     // beforeShow: function() {
    //     //     if ((selDate = $(this).val()).length > 0){

    //     //         year = selDate.substring(selDate.length - 4, selDate.length);
    //     //         month = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
    //     //         $(this).datepicker('option', 'defaultDate', new Date(year, month));
    //     //         $(this).datepicker('setDate', new Date(year, month, 1));
                
    //     //     }
    //     // }
    // }).keydown(false);

    $(document).ready(function(){
        window.attData = [];
        window.flag = 0;
          
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('#add_date_from, #add_date_to').change(function(event){
            let from_date = $('#add_date_from').val();
            let to_date = $('#add_date_to').val();

            if(from_date != "" && to_date != ""){
                let f_arr = from_date.split('-');
                let t_arr = to_date.split('-');

                let from = new Date(f_arr[2], f_arr[1]-1, f_arr[0]);
                let to = new Date(t_arr[2], t_arr[1]-1, t_arr[0]);

                let days = (to.getTime() - from.getTime())/ (1000 * 3600 * 24);

                if(days >= 0){
                    $('#num_of_leaves_div').html(days + 1);
                }
                else if(from_date != null && to_date != null){
                    swal({
                        icon: 'error',
                        title: 'Invalid date range!',
                    });
                    $(this).val('');
                }
            }
        });
        
    });

    $('#add_attachment').change(function(event){
        let files = event.target.files;

        $.each(files, function(key, file){
            
            attData.push(file);

            let html = '<div class="col-sm-2">' +
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

    showModal({
        titleContent: "Add Leave Application",
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

    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                $('#add_employee_id, #add_resp_employee_id').val(null).trigger('change');

                $('#add_employee_id, #add_resp_employee_id').select2({
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

    $('#add_employee_id, #add_leave_date').change(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../getLeaveInfo/"+ $('#add_employee_id').val() +"/"+ $('#add_leave_date').val() +"/api", 'post', new FormData($('#leave_add_form')[0]),
            function(response, textStatus, xhr) {
                
                let html = '';

                $('#summary_table_header').html('Leave Summary (' + response.emp.emp_name + ' [' + response.emp.emp_code + '])');

                $.each(response.lv_info.allocated, function(key, alloc){
                    html += '<tr>' +
                                '<td>'+ key +'</td>' +
                                '<td class="text-center">'+ alloc +'</td>' +
                                '<td class="text-center">'+ response.lv_info.eligible[key] +'</td>' +
                                '<td class="text-center">'+ response.lv_info.consumed[key] +'</td>' +
                                '<td class="text-center">'+ (response.lv_info.eligible[key] - response.lv_info.consumed[key]) +'</td>' +
                            '</tr>';
                });

                if(html != ''){
                    $('#commonModal > .modal-dialog').css('max-width', '70%');

                    $('#apl_div').removeClass('col-sm-12').addClass('col-sm-8');
                    // $('#summery_div').addClass('col-sm-5');
                    
                    $('#summery_div').show('slow');
                    $('#lv_details_table').html(html);
                }
            },
            function(response){
                $('#lv_details_table').html('');
                if(response.status == 400){
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            }
        );
    });

    $('#add_sendBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#leave_add_form')[0]);

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

        let formData = new FormData($('#leave_add_form')[0]);

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
