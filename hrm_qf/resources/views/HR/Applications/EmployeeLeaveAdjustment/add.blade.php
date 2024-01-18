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
<form id="leave_adjustment_add_form" enctype="multipart/form-data" method="post" data-toggle="validator">
    @csrf

    {{-- <div class="row">

        <div class="col-sm-10 offset-sm-1"> --}}

            <div class="row p-15">

                <div id="apl_div" class="col-sm-7">

                    <div class="row">

                        <input hidden disabled id="other_add_branch_id" name="branch_id">
                        <input hidden disabled id="other_add_employee_id" name="employee_id">


                        {!! HTML::forBranchFeildTTL([
                            'selectBoxShow'=> true,
                            'isRequired'=> true,
                            'elementId' => 'add_branch_id',
                            'divClass'=> "col-sm-6 form-group",
                            'formStyle'=> "vertical"
                        ]) !!}


                        <div id="employee_add_div" class="col-sm-6 form-group">
                            <label class="input-title ">Employee</label>
                            <div class="input-group">
                                <select id="add_employee_id" name="employee_id" class="form-control clsSelect2" style="width: 100%">
                                    {{-- <option value="">Select employee</option> --}}
                                </select>
                            </div>
                        </div>


                        <div id="" class="col-sm-6 form-group">
                            <label class="input-title ">Fiscal Year</label>
                            <div class="input-group">
                                <select id="add_fiscal_year_id" name="fiscal_year_id" class="form-control clsSelect2" style="width: 100%">
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
                                <select name="adjustment_for" id="add_adjustment_for" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Option</option>
                                    <option value="1">Leave Adjustment</option>
                                    <option value="2">Salary Deduction</option>
                                </select>
                            </div>
                        </div>

                        <div id="" class="col-sm-6 form-group">
                            <label class="input-title ">Adjustment Month</label>
                            <div class="input-group">
                                <select id="add_adjustment_month" name="adjustment_month" class="form-control clsSelect2" style="width: 100%">
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
                                <input type="text" id="add_adjustment_value" name="adjustment_value" class="form-control" style="width: 100%">
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
                                <input id="add_adjustment_date" name="application_date" type="text" value="{{ date('d-m-Y') }}" style="z-index:99999 !important;"
                                    class="form-control datepicker-custom" placeholder="DD-MM-YYYY" disabled>
                                <input  name="application_date" type="text" value="{{ date('d-m-Y') }}" hidden>
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

        if (appFor == "others"){
            let newOptionBranch = '<option value="" data-select2-id="-1" selected>Select Branch</option>';
            newOptionBranch += '<option value="0" data-select2-id="-2">All Branch</option>';
            $('#add_branch_id').prepend(newOptionBranch).trigger('change');
            $('#add_branch_id option:eq(2)').remove();
        }

    });

    window.appFor = "{{ $appFor }}";

    if (appFor == "others") {
        configureOthersApplicationForm();
    } else {
        if ("{{ $loginUserInfo->emp_id }}" !== "") {
            configureSelfApplicationForm();
        } else {
            swal(
                'Sorry!!!',
                'This user is not valid employee.',
                'error'
            )
        }
    }

    $('#add_branch_id').change(function(event) {
        callApi("{{ route('getEmployeesOptionsByBranch', '') }}/" + ($(this).val() === '' ? '-1' : $(this)
                .val()),
            'get', {},
            function(response, textStatus, xhr) {
                // $('#add_employee_id, #add_resp_employee_id').val(null).trigger('change');

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

                // let newOptionEmployee = '<option value="" data-select2-id="-5">Select Employee</option>';
                let newOptionEmployee = '<option value="0" data-select2-id="-6">All Employee</option>';
                $('#add_employee_id').prepend(newOptionEmployee).trigger('change');
                // $('#add_employee_id option:eq(2)').remove();
            }
        );

    });

    $('#add_employee_id').change(function(event) {
        event.preventDefault();

        let empID = '';
        let url = '';
        if (appFor == "others"){
            empID = $('#add_employee_id').val();
            url = "/../../";
            if (empID == '') {
                return;
            }
        }else{
            empID = " {{ $loginUserInfo->emp_id }}";
            url = "/../";
        }

        callApi("{{ url()->current() }}"+url+"getLeaveInfo/"+ empID +"/"+ $('#add_adjustment_date').val() +"/api", 'post', new FormData($('#leave_adjustment_add_form')[0]),
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

    
    $('#add_sendBtn').click(function(event) {
        event.preventDefault();

        let formData = new FormData($('#leave_adjustment_add_form')[0]);

        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/send/api", 'post', formData,
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

        let formData = new FormData($('#leave_adjustment_add_form')[0]);
        $.each(attData, function(key, file){
            if(file != null){
                formData.append('attachment[]', file, file.name);
            }
        });

        callApi("{{ url()->current() }}/{{ $preFix }}insert/draft/api", 'post', formData,
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

    

    function configureSelfApplicationForm() {

        $('#add_branch_id').prop('disabled', true);
        $('#add_employee_id').prop('disabled', true);

        $('#other_add_employee_id').prop('disabled', false);
        $('#other_add_branch_id').prop('disabled', false);

        $('#other_add_employee_id').val({{ $loginUserInfo->emp_id }});
        $('#other_add_branch_id').val({{ $loginUserInfo->branch_id }});


        $('#row_department_id').hide();
        $('#branch_add_div').hide();
        $('#employee_add_div').hide();
        // $('#resp_employee_add_div').hide();


        setTimeout(function() {

            $("form .clsSelect2").select2({
                dropdownParent: $("#commonModal")
            });

            $('#add_branch_id').val({{ $loginUserInfo->branch_id }}).trigger('change');
            // $('#add_resp_employee_id').val(response.result_data.resp_emp_id).trigger('change');

        }, 1200);



        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Employee Leave Adjustment",
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

        $(".modal-lg").css({"maxWidth": "50%"});
        $("#apl_div").addClass("col-sm-12");

        $('#row_department_id').removeClass('d-none');

        $('.clsSelect2').select2();

        showModal({
            titleContent: "Add Employee Leave Adjustment",
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
