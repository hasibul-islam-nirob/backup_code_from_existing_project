<?php
use Illuminate\Support\Facades\DB;
use App\Services\HtmlService as HTML;
use App\Services\HrService as HRS;
$loginUserInfo = Auth::user();
$preFix = last(request()->segments()) == 'others' ? '../../' : '../';
// dd($loginUserInfo);
// dd($preFix, $loginUserInfo, $loginUserInfo->emp_id);

$fiscalYearData = HRS::getFiscalYearData(1, 'LFY');
$allMonth = DB::table('hr_months')->get();
?>

<style>
    .modal-lg {
        max-width: 80%;
    }
</style>

{{-- novalidate="true" --}}
<form id="leave_adjustment_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    @csrf

    {{-- <div class="row">

        <div class="col-sm-10 offset-sm-1"> --}}

            <div class="row p-15">

                <div id="apl_div" class="col-sm-7">

                    <div class="row">

                        <input hidden id="leave_adjustment_id" name="adjustment_id">
                        <input hidden disabled id="other_add_branch_id" name="branch_id">
                        <input hidden disabled id="other_add_employee_id" name="employee_id">


                        {!! HTML::forBranchFeildTTL([
                            'selectBoxShow'=> true,
                            'isRequired'=> true,
                            'elementId' => 'edit_branch_id',
                            'divClass'=> "col-sm-6 form-group",
                            'formStyle'=> "vertical"
                        ]) !!}


                        <div id="employee_add_div" class="col-sm-6 form-group">
                            <label class="input-title ">Employee</label>
                            <div class="input-group">
                                <select id="edit_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                                    {{-- <option value="">Select employee</option> --}}
                                </select>
                            </div>
                        </div>


                        <div id="" class="col-sm-6 form-group">
                            <label class="input-title ">Fiscal Year</label>
                            <div class="input-group">
                                <select id="edit_fiscal_year_id" name="fiscal_year_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Fiscal Year</option>
                                    @foreach ($fiscalYearData as $ffy)
                                        <option value="{{$ffy->id}}">{{$ffy->fy_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Adjustment For</label>
                            <div class="input-group">
                                <select name="adjustment_for" id="edit_adjustment_for" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Option</option>
                                    <option value="1">Leave Adjustment</option>
                                    <option value="2">Salary Deduction</option>
                                </select>
                            </div>
                        </div>

                        <div id="" class="col-sm-6 form-group">
                            <label class="input-title ">Adjustment Month</label>
                            <div class="input-group">
                                <select id="edit_adjustment_month" name="adjustment_month" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Month</option>
                                    @foreach ($allMonth as $month)
                                    <option value="{{$month->id}}">{{$month->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="" class="col-sm-6 form-group">
                            <label class="input-title ">Total Adjustment</label>
                            <div class="input-group">
                                <input type="text" id="edit_adjustment_value" name="adjustment_value" class="form-control" style="width: 100%">
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Application Date</label>
                            <div class="input-group">
                                <div class="input-group-prepend ">
                                    <span class="input-group-text ">
                                        <i class="icon wb-calendar" aria-hidden="true"></i>
                                    </span>
                                </div>
                                <input id="edit_adjustment_date" name="application_date" type="text" value="{{ date('d-m-Y') }}" style="z-index:99999 !important;"
                                    class="form-control datepicker-custom" placeholder="DD-MM-YYYY" disabled>
                                <input id="edit_adjustment_date_conf" name="application_date" type="text" value="" hidden>
                            </div>
                        </div>

                        <div class="col-sm-12 form-group">
                            <label class="input-title ">Note</label>
                            <div class="input-group">
                               <textarea class="form-control" placeholder="Enter your note" id="leave_note" name="note" rows="3"></textarea>
                            </div>
                        </div>


                    </div>


                    <div class="row d-none">
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

                <div id="summery_div" class="col-sm-4 d-none" style="border-left: double black; margin-bottom: 20px;">

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

                            <div id="noticeDiv">
                                
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        {{-- </div>

    </div> --}}

</form>

<script>

    $(document).ready(function(){
        window.attData = [];
        window.flag = 0;
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        callApi("{{ url()->current() }}/../../get/" + "{{ $id }}/api", 'post', '',
            function(response, textStatus, xhr) {
                
                $("#leave_adjustment_id").val(response.result_data.id);
                $("#edit_branch_id").val(response.result_data.branch_id).trigger('change');

                setTimeout(() => {
                    $("#edit_employee_id").val(response.result_data.emp_id).trigger('change');
                }, 1000);
                $("#edit_fiscal_year_id").val(response.result_data.fiscal_year_id).trigger('change');
                $("#edit_adjustment_for").val(response.result_data.adjustment_for).trigger('change');
                $("#edit_adjustment_month").val(response.result_data.adjustment_month).trigger('change');
                $("#edit_adjustment_value").val(response.result_data.adjustment_value);
                $("#edit_adjustment_date").val(response.result_data.application_date);
                $("#edit_adjustment_date_conf").val(response.result_data.application_date);
                $("#leave_note").val(response.result_data.note);

            },
            function(response) {
                showApiResponse(response.result_data.status, JSON.parse(response.responseText).message);
            }
        );


        let newOptionBranch = '<option value="" data-select2-id="-1" selected>Select Branch</option>';
            newOptionBranch += '<option value="0" data-select2-id="-2">All Branch</option>';
            $('#edit_branch_id').prepend(newOptionBranch).trigger('change');
            $('#edit_branch_id option:eq(2)').remove();
    });

    showModal({
        titleContent: "Edit Employee Leave Adjustment",
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



    $('#edit_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                // $('#edit_employee_id, #add_resp_employee_id').val(null).trigger('change');

                $('#edit_employee_id, #add_resp_employee_id').select2({

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

                // let newOptionEmployee = '<option value="" data-select2-id="-5">Select Employee</option>';
                let newOptionEmployee = '<option value="0" data-select2-id="-6">All Employee</option>';
                $('#edit_employee_id').prepend(newOptionEmployee).trigger('change');
                // $('#edit_employee_id option:eq(2)').remove();
            }
        );

    });

    $('#edit_employee_id').change(function(event) {
        event.preventDefault();

        let empID = '';
        let url = '';
        empID = $('#edit_employee_id').val();
        url = "/../../";
        if (empID == '') {
            return;
        }
        

        callApi("{{ url()->current() }}"+url+"getLeaveInfo/"+ empID +"/"+ $('#edit_adjustment_date').val() +"/api", 'post', new FormData($('#leave_adjustment_edit_form')[0]),
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

                    $('#summery_div').removeClass('d-none');
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


    $('#edit_sendBtn').click(function(e) {
        e.preventDefault();

        let formData = new FormData($('#leave_adjustment_edit_form')[0]);
        
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
            console.log('ok');
    });

    $("#edit_draftBtn").click(function(e) {
        e.preventDefault();

        let formData = new FormData($('#leave_adjustment_edit_form')[0]);

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

</script>
