<style>
    .modal-lg {
        max-width: 90%;
    }
    .select2-container {
        z-index: 100000;
    }
</style>

{{-- Dynamic rows to clone --}}
<div style="display: none;" id="pay_con_row">

    <input hidden name="rec_type_id[]" class="rec_type_h">

    <div class="row">
        <div class="col-sm-12 text-center text-dark">
            Leave configuration for <span class="rec_type_text" style="color:blue; text-transform: lowercase;"></span>
            recruitment
        </div>
    </div>

    <div class="pay_main_row_div">
        <div class="row pay_sub_row_div" style="border: 1px solid black;">
            <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="pay_consume_policy[]" style="width: 100%">
                        <option value="yearly_allocated">Yearly Allocated</option>
                        <option value="eligible">Eligible</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="pay_remaining_leave_policy[]" style="width: 100%">
                        <option value="flash">Flash</option>
                        <option value="add_next_year">Add next year</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="pay_app_submit_policy[]" style="width: 100%">
                        <option value="before">Before</option>
                        <option value="after">After</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="pay_capable_of_provision[]" style="width: 100%">
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
                    <input name="pay_effective_date_from[]" type="text" style="z-index:99999 !important;"
                        class="form-control round lv-datepicker" placeholder="DD-MM-YYYY">
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
                <div class="input-group d-flex align-items-center">
                    <input type="number" name="pay_allocated_leave[]" class="form-control"
                        style="width: 65%">
                    <h6 style="color: #000; width: 20%"><i><b>&nbsp;days</b></i></h6>
                    <span class="pl-2 d-flex align-items-center" style="width: 15%">
                        <a type="button" onclick="removeRow(this, 'pay')" class="btn btn-xs btn-danger btn-round">
                            <i class="fas fa-minus" style="color:#fff;"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;" id="earn_con_row">

    <input hidden name="rec_type_id[]" class="rec_type_h">

    <div class="row">
        <div class="col-sm-12 text-center text-dark">
            Leave configuration for <span class="rec_type_text" style="color:blue; text-transform: lowercase;"></span>
            recruitment
        </div>
    </div>

    <div class="earn_main_row_div">

        <div class="row earn_sub_row_div" style="border: 1px solid black;">
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="earn_eligibility_counting_from[]" style="width: 100%">
                        <option value="joining_date">Joining date</option>
                        <option value="permanent_date">Permanent date</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <input type="number" name="earn_allocated_leave[]" class="form-control"
                        style="width: 85%">
                    <h6 style="color: #000; width: 15%"><i><b>&nbsp;days</b></i></h6>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <input type="number" name="earn_max_leave_entitle[]" class="form-control" style="width: 85%">
                    <h6 style="color: #000; width: 15%"><i><b>&nbsp;days</b></i></h6>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <input type="number" name="earn_consume_after[]" class="form-control" style="width: 70%">
                    <h6 style="color: #000; width: 15%"><i><b>&nbsp;years</b></i></h6>
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <div class="input-group-prepend ">
                        <span class="input-group-text ">
                            <i class="icon wb-calendar round" aria-hidden="true"></i>
                        </span>
                    </div>
                    <input name="earn_effective_date_from[]" type="text" style="z-index:99999 !important;"
                        class="form-control round lv-datepicker" placeholder="DD-MM-YYYY">
                </div>
            </div>
            <div class="col-sm-2 text-center text-dark" style="padding: 1%;">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="earn_leave_withdrawal_policy[]" style="width: 85%">
                        <option value="cash">Cash</option>
                        <option value="non_cash">Non-Cash</option>
                    </select>
                    <span class="pl-2" style="width: 15%">
                        <a type="button" onclick="removeRow(this, 'earn')" class="btn btn-xs btn-danger btn-round">
                            <i class="fas fa-minus" style="color:#fff;"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>

<div style="display: none;" id="parental_con_row">

    <input hidden name="rec_type_id[]" class="rec_type_h">

    <div class="row">
        <div class="col-sm-12 text-center text-dark">
            Leave configuration for <span class="rec_type_text" style="color:blue; text-transform: lowercase;"></span>
            recruitment
        </div>
    </div>
    <div class="parental_main_row_div">
        <div class="row parental_sub_row_div" style="border: 1px solid black;">
            <div class="col-sm-4 text-center text-dark" style="border-right: 1px solid black; padding: 1%;">
                <div class="input-group">
                    <input type="number" name="parental_allocated_leave[]" class="form-control"
                        style="width: 85%">
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
                    <input name="pay_effective_date_from[]" type="text" style="z-index:99999 !important;"
                        class="form-control round lv-datepicker" placeholder="DD-MM-YYYY">
                </div>
            </div>
            <div class="col-sm-4 text-center text-dark" style="padding: 1%;">
                <div class="input-group">
                    <input type="number" name="parental_times_of_leave[]" class="form-control" style="width: 85%">
                    <span class="pl-2" style="width: 15%">
                        <a type="button" onclick="removeRow(this, 'parental')" class="btn btn-xs btn-danger btn-round">
                            <i class="fas fa-minus" style="color:#fff;"></i>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
</div>
{{-- Dynamic rows to clone --}}

<form id="leave_type_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-12" style="padding-right: 4%; padding-left: 4%">

            <div class="row">

                <div class="col-sm-4 form-group">
                    <label class="input-title">Category Name</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Short Form</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="short_form">
                    </div>
                </div>

                <div class="col-sm-4 form-group">
                    <label class="input-title">Leave Type</label>
                    <div class="input-group">
                        <select class="form-control clsSelect2" id="leave_type_id" name="leave_type_uid"
                            style="width: 100%">
                            <option value="">Select one</option>
                            @foreach ($leave_type as $lt)
                                <option value="{{ $lt->uid }}">{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            @if (!empty($rec_type))

                <div class="row recType" style="display: none;">
                    <div class="col-sm-4 offset-sm-4 form-group">
                        <label class="input-title">Recruitment Type</label>
                        <div class="input-group">
                            <select class="form-control clsSelect2" id="rec_type_id" style="width: 100%">
                                <option value="">Select one</option>
                                <option value="all">All</option>
                                @foreach ($rec_type as $rt)
                                    <option value="{{ $rt->id }}">{{ $rt->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <label class="input-title">&nbsp;</label>
                        <div class="input-group">
                            <a style="color: #fff;" onclick="addRow(event);" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> &nbsp; Add
                            </a>
                        </div>
                    </div>
                </div>

                <div id="payTypeDiv" style="display: none;">

                    <div class="row" style="border: 1px solid black; background-color: #17b3a3;">
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Consume Policy</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white"> Application Submit Policy</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Is Capable Of Provision Period?</label>
                        </div>
                        <div class="col-sm-2 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Effective date from</label>
                        </div>
                        <div class="col-sm-2 text-center">
                            <label class="input-title text-white">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></label>
                        </div>
                    </div>

                    <div class="mb-20" id="pay_con_div">

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
                            <label class="input-title text-white">Effective date from</label>
                        </div>
                        <div class="col-sm-2 text-center">
                            <label class="input-title text-white">Withdrawal Policy</label>
                        </div>
                    </div>

                    <div class="mb-20" id="earn_con_div">

                    </div>

                </div>

                <div id="parentalTypeDiv" style="display: none;">

                    <div class="row" style="border: 1px solid black; background-color: #17b3a3;">
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Allocated Leaves <small style="text-transform: lowercase;">(yearly)</small></label>
                        </div>
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Effective date from</label>
                        </div>
                        <div class="col-sm-4 text-center" style="border-right: 1px solid black;">
                            <label class="input-title text-white">Times of Leaves</label>
                        </div>
                    </div>

                    <div class="mb-20" id="parental_con_div">

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

    });

    function addRow(event) {
        let leave_type_id = $('#leave_type_id').val();

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        if (leave_type_id == '1' && isValidAction('pay_con_row_', 'pay_con_div')) {
            addConfig('pay_con_row', 'pay_con_div', leave_type_id);
        } else if (leave_type_id == '3' && isValidAction('earn_con_row_', 'earn_con_div')) {
            addConfig('earn_con_row', 'earn_con_div', leave_type_id);
        } else if (leave_type_id == '4' && isValidAction('parental_con_row_', 'parental_con_div')) {
            addConfig('parental_con_row', 'parental_con_div', leave_type_id);
        }
        $('.clsSelect2').select2();
    }

    function isValidAction(dynamic_row_id, dynamic_div_id) {

        let rec_type = $('#rec_type_id').val();

        if (rec_type == '') {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = "Please select recruitment type";
            swal({
                icon: 'warning',
                title: 'Oops...',
                content: wrapper,
            });
            return false;
        }

        if (rec_type == 'all') {
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

        if ($('#' + dynamic_div_id).find('#' + dynamic_row_id + rec_type).length >= 1) {
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

    function addConfig(dynamic_row_id, dynamic_div_id, leave_type_id) {
        let row = $('#' + dynamic_row_id).clone();
        row.find('.rec_type_text').html($('#rec_type_id').children(':selected').text());
        row.attr('id', dynamic_row_id + '_' + $('#rec_type_id').val());
        row.find('.rec_type_h').attr('name', 'rec_type_id_' + leave_type_id + '[]');
        row.find('.rec_type_h').val($('#rec_type_id').val());
        row.show();
        row.appendTo('#' + dynamic_div_id);
    }

    function removeRow(node) {
        let leave_type = $('#leave_type_id').val();
        let prefix = "";
        if (leave_type == '1') {
            prefix += 'pay_con_row_';
        } else if (leave_type == '3') {
            prefix += 'earn_con_row_';
        } else if (leave_type == '4') {
            prefix += 'parental_con_row_';
        }
        $(node).parents('[id^=' + prefix + ']').remove();
    }

    showModal({
        titleContent: "Add Leave Types",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'saveBtn',
            }
        }),
    });

    $('#saveBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#leave_type_add_form')[0]),
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
