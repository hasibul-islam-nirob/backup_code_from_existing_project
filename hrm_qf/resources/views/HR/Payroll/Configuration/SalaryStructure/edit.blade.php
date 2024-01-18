<style>
    .modal-lg {
        max-width: 60%;
    }

</style>

<form id="salary_structure_edit_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf
    <input hidden name="edit_id" value="{{ $editData->id }}">

    <div class="row">

        <div class="col-sm-11 offset-sm-1">

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" class="form-control clsSelect2" style="width: 100%">
                            @foreach ($companies as $c)
                                <option {{ ($editData->company_id == $c->id) ? 'selected' : '' }} value="{{ $c->id }}">{{ $c->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="edit_project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select project</option>
                            @foreach ($projects as $p)
                                <option {{ ($editData->project_id == $p->id) ? 'selected' : '' }} value="{{ $p->id }}">{{ $p->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Grade</label>
                            <div class="input-group">
                                <select name="grade" id="edit_grade_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Grade</option>
                                    @for ($i = 1; $i<=$gradeLevel['grade']; $i++)
                                    <option {{ ($editData->grade == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
        
                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Level</label>
                            <div class="input-group">
                                <select name="level" id="edit_level_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select Level</option>
                                    @for ($i = 1; $i<=$gradeLevel['level']; $i++)
                                    <option {{ ($editData->level == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-5 form-group offset-sm-1">
                    <label class="input-title RequiredStar">Pay Scale</label>
                    <div class="input-group">
                        <select name="pay_scale_id" id="edit_pay_scale_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Pay Scale</option>
                            @foreach ($payScale as $ps)
                                <option {{ ($editData->pay_scale_id == $ps->id) ? 'selected' : '' }} value="{{ $ps->id }}">{{ $ps->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Designations</label>
                    <div class="input-group">
                        <select multiple name="designations[]" class="form-control clsSelect2" style="width: 100%">
                            @foreach ($designations as $d)
                                <option {{ (in_array($d->id, explode(',',$editData->designations))) ? 'selected' : '' }} value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @php
                    // dd($recruitmrntType, $editData);
                @endphp

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Recruitment Type</label>
                    <div class="input-group">
                        <select multiple name="recruitment_type_id[]" id="edit_recruitment_type_id" class="form-control clsSelect2" style="width: 100%" disabled>
                            @foreach ($recruitmrntType as $r)

                                {{-- <option value="{{ $r->employee_type }}" {{$r->employee_type == $editData->recruitment_type_id ? 'selected' : '' }} >{{ $r->title }}</option> --}}
                                <option {{ (in_array($r->id, explode(',',$editData->recruitment_type_id))) ? 'selected' : '' }} value="{{ $r->id }}">{{ $r->title }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="recruitment_type_id[]" value="{{$editData->recruitment_type_id}}" hidden>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Basic Salary</label>
                    <div class="input-group">
                        <input value="{{ $editData->basic }}" type="number" name="basic" id="edit_basic_salary" style="width: 100%;">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Acting Benefit Amount</label>
                    <div class="input-group">
                        <input value="{{ $editData->acting_benefit_amount }}" type="number" name="acting_benefit_amount" style="width: 100%;">
                    </div>
                </div>

            </div>

            <!-- new start -->
            <div class="row">
                <input hidden type="text" value="{{$editData->pf_id}} " name="pf_id"  id="edit_pf_id_alt" >
                <input hidden type="text" value="{{$editData->wf_id}} " name="wf_id"  id="edit_wf_id_alt" >
                <input hidden type="text" value="{{$editData->ps_id}} " name="ps_id"  id="edit_eps_id_alt">
                <input hidden type="text" value="{{$editData->osf_id}}" name="osf_id" id="edit_osf_id_alt">
                <input hidden type="text" value="{{$editData->inc_id}}" name="inc_id" id="edit_inc_id_alt">
            </div>
            <!-- new end -->



            @php
                $detailsData = $editData->salary_structure_details;
                $detailsData = $detailsData->groupBy('data_type');
            @endphp

            <br>

            <div class="row">

                <div class="col-sm-11" id="inc_div">

                    <div class="row">

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Increment Percentage</label>
                        </div>

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Increment Amount</label>
                        </div>

                        <div class="col-sm-3 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">No. Of Increment</label>
                        </div>

                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            
                        </div>
                        
                    </div>

                    @foreach ($detailsData['increment'] as $key => $inc)
                    <div class="row" {{-- {{ ($key == 0) ? 'id = inc_div_row' : '' }} --}}>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->inc_percentage }}" type="number" name="inc_percentage[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->amount }}" type="number" name="inc_amount[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $inc->no_of_inc }}" type="number" name="inc_number_of_inc[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="removeRow(this)" class="">
                                    <i class="fa fa-minus-circle" style="color: red;"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    @endforeach

                    <div class="row inc_div_row" id="inc_div_row">

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input type="number" name="inc_percentage[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input type="number" name="inc_amount[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input type="number" name="inc_number_of_inc[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="addIncRow(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>

            <br>

            <div class="row">

                <div class="col-sm-11" id="benefit_div">

                    <div class="row">

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Allowance</label>
                        </div>

                        <div class="col-sm-4 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Calculation Nature</label>
                        </div>

                        <div class="col-sm-3 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            <label style="color:#fff; font-size: 12px;">Amount</label>
                        </div>

                        <div class="col-sm-1 text-center" style="background-color: #4f4e4eb8; border: 1px solid #fff;">
                            
                        </div>
                        
                    </div>

                    @foreach ($detailsData['allowance'] as $key => $alw)
                    <div class="row" {{-- {{ ($key == 0) ? 'id = benefit_div_row' : '' }} --}} style="">

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                
                                <select name="allowance_id[]" class="form-control clsSelect2" style="width: 100%;">
                                    <option value="">Select allowance</option>
                                    @foreach ($allowance as $al)
                                        <option {{ ($alw->allowance_type_id == $al->id) ? 'selected' : '' }} value="{{ $al->id }}">{{ $al->name . ' [' .  strtoupper($al->value_field) . ']' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="allowance_calculation_type[]" class="form-control clsSelect2" style="width: 100%;">
                                    <option value="">Select calculation nature</option>
                                    <option {{ ($alw->calculation_type == 1) ? 'selected' : '' }} value="1">Percentage</option>
                                    <option {{ ($alw->calculation_type == 2) ? 'selected' : '' }} value="2">Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input value="{{ $alw->amount }}" type="number" name="allowance_amount[]" style="width: 100%;">
                            </div>
                        </div>

                        {{-- <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                @if ($key == (count($detailsData['allowance']) - 1))
                                <a onclick="addBenifitRow(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                                @else
                                <a onclick="removeRow(this)" class="">
                                    <i class="fa fa-minus-circle" style="color: red;"></i>
                                </a>
                                @endif
                            </span>
                        </div> --}}

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="removeRow(this)" class="">
                                    <i class="fa fa-minus-circle" style="color: red;"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>
                    @endforeach

                    <div class="row" id="benefit_div_row" style="">

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">

                                <select name="allowance_id[]" class="form-control clsSelect2" style="width: 100%;">
                                    <option value="">Select allowance</option>
                                    @foreach ($allowance as $al)
                                        <option value="{{ $al->id }}">{{ $al->name . ' [' .  strtoupper($al->value_field) . ']' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <select name="allowance_calculation_type[]" class="form-control clsSelect2" style="width: 100%;">
                                    <option value="">Select calculation nature</option>
                                    <option value="1">Percentage</option>
                                    <option value="2">Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3 text-center" style="border: 1px solid #fff; padding: 0;">
                            <div class="input-group">
                                <input type="number" name="allowance_amount[]" style="width: 100%;">
                            </div>
                        </div>

                        <div class="col-sm-1 text-center" style="border: 1px solid #fff; padding: 0;">
                            <span class="addBtnClass">
                                <a onclick="addBenifitRow(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a>
                            </span>
                        </div>
                        
                    </div>

                </div>
                
            </div>

            <br>
            <br>

        </div>

    </div>

</form>


<script>


    $(document).ready(function(){
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });
        
    });

    $("#edit_basic_salary").change(function(){
        $('.inc_div_row').find('input:eq(0)').val('');
    })

    // First input field and value
    function salaryPercentage() {
        var firstInput = $('.inc_div_row').find('input:eq(0)');
        $('.inc_div_row').find('input:eq(0)').focusout(function(){
            let firstInputValue = firstInput.val();
            let basicSalary = $("#edit_basic_salary").val();
            let incSalary = (firstInputValue / 100) * basicSalary;

            let secondInput = $('.inc_div_row').find('input:eq(1)');
            secondInput.prop('readonly', true);
            secondInput.val(incSalary);
        })
    }
    salaryPercentage();

    function addIncRow(){
        $('#inc_div').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#inc_div_row').clone().find('.addBtnClass')
        .html(
            '<a onclick="addIncRow(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#inc_div');
    }

    function removeRow(node){
        $(node).parent().parent().parent().remove();
    }

    function addBenifitRow(){

        if ($('.clsSelect2').hasClass("select2-hidden-accessible")) {
            $(".clsSelect2").select2('destroy');
        }

        $('#benefit_div').find('.addBtnClass')
        .html(
            '<a onclick="removeRow(this)" class="">' +
                '<i class="fa fa-minus-circle" style="color: red;"></i>' +
            '</a>'
        );

        $('#benefit_div_row').clone().find('.addBtnClass').html(
            '<a onclick="addBenifitRow(this)" class="">' +
                '<i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>' +
            '</a>'
        ).end()
        .find("input").val("").end()
        .appendTo('#benefit_div');

        // $(".clsSelect2").select2({
        //     dropdownParent: $("#commonModal")
        // });
        $("form .clsSelect2").select2({
            dropdownParent: $("#commonModal")
        });

    }

    showModal({
        titleContent: "Edit Salary Structure",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'update',
            },
            'btnName': {
                0: 'Update',
            },
            'btnId': {
                0: 'edit_updateBtn',
            }
        }),
    });

    $('#edit_updateBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../../update/api", 'post', new FormData($('#salary_structure_edit_form')[
                0]),
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


    
    // $('#edit_project_id').change(function(event) {
    //     if ( ($('#edit_project_id').val()) < 1) {
    //         $("#edit_pf_div").addClass('d-none');
    //         $("#edit_wf_div").addClass('d-none');
    //         $("#edit_ps_div").addClass('d-none');
    //     }
    // })

</script>