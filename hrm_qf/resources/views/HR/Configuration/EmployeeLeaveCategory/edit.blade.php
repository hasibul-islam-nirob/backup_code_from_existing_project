<style>
    .modal-lg {
        max-width: 80%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

@php
    $lv_d = [];
    foreach ($edit_data->leave_details as $key => $value) {
        $lv_d[$value->rec_type_id][] = $value;
    }
@endphp

{{-- Pay div to clone --}}
<div id="pay_clone" class="row pay_sub_row_div" style="border: 1px solid black; display: none">

    <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 consume_policy" style="width: 100%">
                <option value="yearly_allocated">Yearly Allocated</option>
                <option value="eligible">Eligible</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 remaining_leave_policy" style="width: 100%">
                <option value="flash">Flash</option>
                <option value="add_next_year">Add next year</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 app_submit_policy" style="width: 100%">
                <option value="before">Before</option>
                <option value="after">After</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 capable_of_provision" style="width: 100%">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <div class="input-group-prepend ">
                <span class="input-group-text ">
                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                </span>
            </div>
            <input readonly type="text" style="z-index:99999 !important;"
                class="form-control round effective_date_from" placeholder="DD-MM-YYYY">
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
        <div class="input-group d-flex align-items-center">
            <input type="number" class="form-control allocated_leave" style="width: 65%">
            <h6 style="color: #000; width: 20%"><i><b>&nbsp;days</b></i></h6>
            <span class="pl-2 add-rmv-btn d-flex align-items-center" style="width: 15%">
                <a type="button" onclick="removeSubRow(this)"
                    class="btn btn-xs btn-danger btn-round">
                    <i class="fas fa-minus" style="color:#fff;"></i>
                </a>
            </span>
        </div>
    </div>

</div>
{{-- Pay div to clone --}}

{{-- Earn div to clone --}}
<div id="earn_clone" class="row earn_sub_row_div" style="border: 1px solid black; display: none;">

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 eligibility_counting_from" style="width: 100%;">
                <option value="joining_date">Joining date</option>
                <option value="permanent_date">Permanent date</option>
            </select>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <input type="number" class="form-control allocated_leave" style="width: 85%">
            <h6 style="color: #000; width: 15%"><i><b>&nbsp;days</b></i></h6>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <input type="number" class="form-control max_leave_entitle" style="width: 85%">
            <h6 style="color: #000; width: 15%"><i><b>&nbsp;days</b></i></h6>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <input type="number" class="form-control consume_after" style="width: 70%">
            <h6 style="color: #000; width: 15%"><i><b>&nbsp;years</b></i></h6>
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark" 
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <div class="input-group-prepend ">
                <span class="input-group-text ">
                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                </span>
            </div>
            <input readonly type="text" style="z-index:99999 !important;" class="form-control round effective_date_from" placeholder="DD-MM-YYYY">
        </div>
    </div>

    <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
        <div class="input-group">
            <select class="form-control clsSelect2 leave_withdrawal_policy" style="width: 85%">
                <option value="cash">Cash</option>
                <option value="non_cash">Non-Cash</option>
            </select>
            <span class="pl-2 add-rmv-btn" style="width: 15%">

                <a type="button" onclick="removeSubRow(this)"
                    class="btn btn-xs btn-danger btn-round">
                    <i class="fas fa-minus" style="color:#fff;"></i>
                </a>
                
            </span>
        </div>
    </div>
    
</div>
{{-- Earn div to clone --}}

{{-- Parental div to clone --}}
<div id="parental_clone" class="row parental_sub_row_div" style="border: 1px solid black; display: none;">

    <div class="col-sm-4 text-center text-dark"
        style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <input type="number" class="form-control allocated_leave" style="width: 85%">
            <h6 style="color: #000; width: 15%"><i><b>&nbsp;days</b></i></h6>
        </div>
    </div>

    <div class="col-sm-4 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
        <div class="input-group">
            <div class="input-group-prepend ">
                <span class="input-group-text ">
                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                </span>
            </div>
            <input readonly type="text" style="z-index:99999 !important;" class="form-control round effective_date_from" placeholder="DD-MM-YYYY">
        </div>
    </div>

    <div class="col-sm-4 text-center text-dark" style="padding: 1%;">
        <div class="input-group">
            <input type="number" class="form-control times_of_leave" style="width: 85%">
            <span class="pl-2 add-rmv-btn" style="width: 15%">
                <a type="button" onclick="removeSubRow(this)"
                    class="btn btn-xs btn-danger btn-round">
                    <i class="fas fa-minus" style="color:#fff;"></i>
                </a>
            </span>
        </div>
    </div>

</div>
{{-- Parental div to clone --}}

<form id="leave_type_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input value="{{ $edit_data->id }}" hidden name="leave_cat_id">
    <div class="row">

        <div class="col-sm-12" style="padding-right: 4%; padding-left: 4%">

            <div class="row">

                <div class="col-sm-4 form-group">
                    <label class="input-title">Category Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" value="{{ $edit_data->name }}">
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Short Form</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="short_form"
                            value="{{ $edit_data->short_form }}">
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Leave Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="leave_type_id" name="leave_type_uid"
                            style="width: 100%">
                            <option value="">Select one</option>
                            @foreach ($leave_type as $lt)
                                <option {{ $edit_data->leave_type_uid == $lt->uid ? 'selected' : '' }}
                                    value="{{ $lt->uid }}">{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            @if (!empty($rec_type))

                @if (count($rec_type) != count($lv_d))
                <div class="row recType" style="display: none;">
                    <div class="col-sm-4 offset-sm-4 form-group">
                        <label class="input-title">Recruitment Type</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" id="rec_type_id" style="width: 100%">
                                <option value="">Select one</option>
                                @foreach ($rec_type as $rt)
                                    <option value="{{ $rt->id }}">{{ $rt->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <label class="input-title text-white">&nbsp;</label>
                        <div class="input-group">
                            <a style="color: #fff;" onclick="addRow(event);" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> &nbsp; Add
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div id="payTypeDiv" style="display: none;">

                    <div class="row" style="border: 1px solid black; background-color: #17b3a3;">
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Consume Policy</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Application Submit Policy</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Is Capable Of Provision Period?</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Effective Date from</label>
                        </div>
                        <div class="col-sm-2 text-center">
                            <label class="input-title text-white">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></label>
                        </div>
                    </div>

                    <div class="mb-20" id="pay_con_div">
                        @if ($edit_data->leave_type_uid == 1)
                            @foreach ($lv_d as $rec_type_id => $leave_details)
                                <div class="pay_con_row" id="pay_con_row_{{ $rec_type_id }}">

                                    <div class="row">
                                        <div class="col-sm-12 text-center text-dark">
                                            Leave configuration for <span class="rec_type_text"
                                                style="color:blue; text-transform: lowercase;">{{ $rec_type->where('id', $rec_type_id)->first()->title }}</span>
                                            recruitment
                                        </div>
                                    </div>

                                    @foreach ($leave_details as $key => $ld)
                                        <div class="pay_main_row_div">
                                            <div class="row pay_sub_row_div" style="border: 1px solid black;">
                                                
                                                {{-- Hidden inputs --}}
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][consume_policy][]" value="{{ $ld->consume_policy }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][remaining_leave_policy][]" value="{{ $ld->remaining_leave_policy }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][app_submit_policy][]" value="{{ $ld->app_submit_policy }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][capable_of_provision][]" value="{{ $ld->capable_of_provision }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][effective_date_from][]" value="{{ $ld->effective_date_from }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][allocated_leave][]" value="{{ $ld->allocated_leave }}">
                                                {{-- Hidden inputs --}}

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->consume_policy == 'yearly_allocated' ? 'Yearly Allocated' : 'Eligible' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->remaining_leave_policy == 'flash' ? 'Flash' : 'Add Next Year' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->app_submit_policy == 'before' ? 'Before' : 'After' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->capable_of_provision == '1' ? 'Yes' : 'No' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ (!empty($ld->effective_date_from)) ? (new DateTime($ld->effective_date_from))->format('d-m-Y') : '' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark">
                                                    {{ $ld->allocated_leave }} days
                                                    
                                                    <span class="pl-2 add-rmv-btn" style="width: 15%">
                                                        @if (count($leave_details) == ($key + 1))
                                                            <a type="button" onclick="addSubRow(this, 'pay', '{{ $ld->rec_type_id }}')"
                                                                class="btn btn-xs btn-primary btn-round">
                                                                <i class="fas fa-plus" style="color:#fff;"></i>
                                                            </a>
                                                        @endif
                                                    </span>

                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

                <div id="earnTypeDiv" style="display: none;">

                    <div class="row" style="border: 1px solid black; background-color: #17b3a3;">
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Eligibility Start From</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Max Leaves Entitled</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Consume After</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Effective Date from</label>
                        </div>
                        <div class="col-sm-2 text-center">
                            <label class="input-title text-white">Withdrawal Policy</label>
                        </div>
                    </div>

                    <div class="mb-20" id="earn_con_div">
                        @if ($edit_data->leave_type_uid == 3)
                            @foreach ($lv_d as $rec_type_id => $leave_details)

                                <div class="earn_con_row" id="earn_con_row_{{ $rec_type_id }}">

                                    <div class="row">
                                        <div class="col-sm-12 text-center text-dark">
                                            Leave configuration for <span class="rec_type_text"
                                                style="color:blue; text-transform: lowercase;">{{ $rec_type->where('id', $rec_type_id)->first()->title }}</span>
                                            recruitment
                                        </div>
                                    </div>
                                    @foreach ($leave_details as $key => $ld)
                                        <div class="earn_main_row_div">
                                            <div class="row earn_sub_row_div" style="border: 1px solid black;">

                                                {{-- Hidden inputs --}}
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][eligibility_counting_from][]" value="{{ $ld->eligibility_counting_from }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][allocated_leave][]" value="{{ $edit_data->leave_type_uid }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][max_leave_entitle][]" value="{{ $ld->max_leave_entitle }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][consume_after][]" value="{{ $ld->consume_after }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][effective_date_from][]" value="{{ $ld->effective_date_from }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][leave_withdrawal_policy][]" value="{{ $ld->leave_withdrawal_policy }}">
                                                {{-- Hidden inputs --}}

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ ($ld->eligibility_counting_from == 'joining_date') ? 'Joining Date' : 'Permanent Date' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->allocated_leave }} days
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->max_leave_entitle }} days
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->consume_after }} years
                                                </div>

                                                <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ (!empty($ld->effective_date_from)) ? (new DateTime($ld->effective_date_from))->format('d-m-Y') : '' }}
                                                </div>

                                                <div class="col-sm-2 text-center text-dark">
                                                    {{ $ld->leave_withdrawal_policy == 'cash' ? 'Cash' : 'Non-Cash' }}
                                                    <span class="pl-2 add-rmv-btn" style="width: 15%">
                                                        @if (count($leave_details) == ($key + 1))
                                                            <a type="button" onclick="addSubRow(this, 'earn', '{{ $ld->rec_type_id }}')"
                                                                class="btn btn-xs btn-primary btn-round">
                                                                <i class="fas fa-plus" style="color:#fff;"></i>
                                                            </a>
                                                        @endif
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                    
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

                <div id="parentalTypeDiv" style="display: none;">

                    <div class="row" style="border: 1px solid black; background-color: #17b3a3;">
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Allocated Leaves</label>
                        </div>
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Effective Date from</label>
                        </div>
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Times of Leaves</label>
                        </div>
                    </div>

                    <div class="mb-20" id="parental_con_div">
                        @if ($edit_data->leave_type_uid == 4)
                            @foreach ($lv_d as $rec_type_id => $leave_details)

                                <div class="parental_con_row" id="parental_con_row_{{ $rec_type_id }}">

                                    <div class="row">
                                        <div class="col-sm-12 text-center text-dark">
                                            Leave configuration for <span class="rec_type_text"
                                                style="color:blue; text-transform: lowercase;">{{ $rec_type->where('id', $rec_type_id)->first()->title }}</span>
                                            recruitment
                                        </div>
                                    </div>

                                    @foreach ($leave_details as $key => $ld)
                                        <div class="parental_main_row_div">
                                            <div class="row parental_sub_row_div" style="border: 1px solid black;">

                                                {{-- Hidden inputs --}}
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][allocated_leave][]" value="{{ $ld->allocated_leave }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][effective_date_from][]" value="{{ $ld->effective_date_from }}">
                                                <input hidden name="data[{{ $edit_data->leave_type_uid }}][{{ $ld->rec_type_id }}][times_of_leave][]" value="{{ $ld->times_of_leave }}">
                                                {{-- Hidden inputs --}}

                                                <div class="col-sm-4 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ $ld->allocated_leave }} days
                                                </div>

                                                <div class="col-sm-4 text-center text-dark" style="border-right: 1px solid black;">
                                                    {{ (!empty($ld->effective_date_from)) ? (new DateTime($ld->effective_date_from))->format('d-m-Y') : '' }}
                                                </div>

                                                <div class="col-sm-4 text-center text-dark">
                                                    {{ $ld->times_of_leave }}
                                                    <span class="pl-2 add-rmv-btn" style="width: 15%">
                                                        @if (count($leave_details) == ($key + 1))
                                                        <a type="button" onclick="addSubRow(this, 'parental', '{{ $ld->rec_type_id }}')"
                                                            class="btn btn-xs btn-primary btn-round">
                                                            <i class="fas fa-plus" style="color:#fff;"></i>
                                                        </a>
                                                        @endif
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

            @endif


        </div>

    </div>

</form>



<script>
    $(document).ready(function() {

        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

        $('#leave_type_id').change(function(event) {
            $('#payTypeDiv').hide();
            $('#earnTypeDiv').hide();
            $('#parentalTypeDiv').hide();
            $('.recType').hide();

            if ($(this).val() == '1') {
                $('#payTypeDiv').show();
                $('.recType').show();
            } else if ($(this).val() == '3') {
                $('#earnTypeDiv').show();
                $('.recType').show();
            } else if ($(this).val() == '4') {
                $('#parentalTypeDiv').show();
                $('.recType').show();
            }
        });

        $('#leave_type_id').trigger("change");

    });

    function addSubRow(node, rowName, rec_type_id, source = null){

        let leave_type_id = $('#leave_type_id').val();

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }
        
        let row = null;

        if(rowName == 'pay'){
            row = $('#pay_clone').clone()
            .find('.consume_policy').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][consume_policy][]').end()
            .find('.remaining_leave_policy').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][remaining_leave_policy][]').end()
            .find('.app_submit_policy').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][app_submit_policy][]').end()
            .find('.capable_of_provision').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][capable_of_provision][]').end()
            .find('.effective_date_from').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][effective_date_from][]').end()
            .find('.allocated_leave').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][allocated_leave][]').end();
        }
        else if(rowName == 'earn'){
            row = $('#earn_clone').clone()
            .find('.eligibility_counting_from').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][eligibility_counting_from][]').end()
            .find('.max_leave_entitle').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][max_leave_entitle][]').end()
            .find('.consume_after').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][consume_after][]').end()
            .find('.leave_withdrawal_policy').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][leave_withdrawal_policy][]').end()
            .find('.effective_date_from').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][effective_date_from][]').end()
            .find('.allocated_leave').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][allocated_leave][]').end();
        }
        else if(rowName == 'parental'){
            row = $('#parental_clone').clone()
            .find('.times_of_leave').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][times_of_leave][]').end()
            .find('.effective_date_from').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][effective_date_from][]').end()
            .find('.allocated_leave').attr('name', 'data['+ leave_type_id +']['+ rec_type_id +'][allocated_leave][]').end();
        }

        if(source == 'manual'){
            row.find('.effective_date_from').datepicker({ dateFormat: 'dd-mm-yy' });
            row.show().appendTo(node);
        }
        else{

            let x = $(node).closest('.' + rowName + '_sub_row_div');

            let max_eff_date = x.find('input[name="data['+ leave_type_id +']['+ rec_type_id +'][effective_date_from][]"]').val();

            row.find('.effective_date_from').datepicker({ dateFormat: 'dd-mm-yy', minDate: new Date(new Date().setDate(new Date(max_eff_date).getDate() + 1)) });

            row.show().appendTo($(node).closest('.' + rowName + '_main_row_div'));

            x.find('.add-rmv-btn').hide().end();

        }

        $('.clsSelect2').select2();
        
    }

    showModal({
        titleContent: "Edit Leave Type Application",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'updateBtn',
            }
        }),
    });

    function addRow(event) {
        let leave_type_id = $('#leave_type_id').val();
        let rec_type_id = $('#rec_type_id').val();

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        if (leave_type_id == '1' && isValidAction('pay_con_row_', 'pay_con_div', rec_type_id)) {

            let node = $('.pay_con_row').first().clone()
            .find('.pay_main_row_div').remove().end()
            
            addSubRow(node, 'pay', rec_type_id, 'manual');

            node.find('.rec_type_text').html($('#rec_type_id').children(':selected').text()).end()
            .attr('id', 'pay_con_row_' + $('#rec_type_id').val())
            .appendTo('#pay_con_div');

        } else if (leave_type_id == '3' && isValidAction('earn_con_row_', 'earn_con_div', rec_type_id)) {

            let node = $('.earn_con_row').first().clone()
            .find('.earn_main_row_div').remove().end()
            
            addSubRow(node, 'earn', rec_type_id, 'manual');

            node.find('.rec_type_text').html($('#rec_type_id').children(':selected').text()).end()
            .attr('id', 'earn_con_row_' + $('#rec_type_id').val())
            .appendTo('#earn_con_div');

        } else if (leave_type_id == '4' && isValidAction('parental_con_row_', 'parental_con_div', rec_type_id)) {

            let node = $('.parental_con_row').first().clone()
            .find('.parental_main_row_div').remove().end()
            
            addSubRow(node, 'parental', rec_type_id, 'manual');

            node.find('.rec_type_text').html($('#rec_type_id').children(':selected').text()).end()
            .attr('id', 'parental_con_row_' + $('#rec_type_id').val())
            .appendTo('#parental_con_div');

        }
        $('.clsSelect2').select2();
    }

    function isValidAction(dynamic_row_id, dynamic_div_id, rec_type_id) {

        if (rec_type_id == '') {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = "Please select recruitment type";
            swal({
                icon: 'warning',
                title: 'Oops...',
                content: wrapper,
            });
            return false;
        }

        if (rec_type_id == 'all') {
            if ($('[id^=' + dynamic_row_id + ']').length >= 1) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML =
                    "You can't configure all recruitment type when any other recruitment type is alredy added";
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    content: wrapper,
                });
                return false;
            }
        }

        if ($('#' + dynamic_row_id + 'all').length >= 1) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = "All configuration is set up";
            swal({
                icon: 'warning',
                title: 'Oops...',
                content: wrapper,
            });
            return false;
        }

        if ($('#' + dynamic_div_id).find('#' + dynamic_row_id + rec_type_id).length >= 1) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = "Recruitment type " + $('#rec_type_id').children(':selected').text() +
                " is alredy added";
            swal({
                icon: 'warning',
                title: 'Oops...',
                content: wrapper,
            });
            return false;
        }

        return true;
    }

    function removeRow(node) {

        let leave_type = $('#leave_type_id').val();
        let prefix = "";

        if (leave_type == '1') {
            prefix += 'pay';
        } else if (leave_type == '3') {
            prefix += 'earn';
        } else if (leave_type == '4') {
            prefix += 'parental';
        }
        $(node).parents('[id^=' + prefix + '_con_row_]').remove();
    }

    function removeSubRow(node) {

        let leave_type = $('#leave_type_id').val();

        if (leave_type == '1') {

            $(node).closest('.pay_sub_row_div').prev().find('.add-rmv-btn').show();
            $(node).parents('.pay_sub_row_div').remove();

        } else if (leave_type == '3') {

            $(node).closest('.earn_sub_row_div').prev().find('.add-rmv-btn').show();
            $(node).parents('.earn_sub_row_div').remove();

        } else if (leave_type == '4') {
            $(node).closest('.parental_sub_row_div').prev().find('.add-rmv-btn').show();
            $(node).parents('.parental_sub_row_div').remove();
        }
        
    }

    $('#updateBtn').click(function(e) {

        e.preventDefault();

        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($(
                '#leave_type_edit_form')[
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
