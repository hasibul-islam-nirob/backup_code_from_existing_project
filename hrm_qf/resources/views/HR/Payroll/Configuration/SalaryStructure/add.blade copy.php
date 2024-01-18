
<style>
    .modal-lg {
        max-width: 60%;
    }

    .select2-container {
        z-index: 100000;
    }

</style>

<form id="salary_structure_add_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-11 offset-sm-1">

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Company</label>
                    <div class="input-group">
                        <select name="company_id" id="company_id" class="form-control clsSelect2" style="width: 100%">
                            @foreach ($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->comp_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Project</label>
                    <div class="input-group">
                        <select name="project_id" id="project_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select project</option>
                            @foreach ($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Pay Scale</label>
                    <div class="input-group">
                        <select name="pay_scale_id" class="form-control clsSelect2" style="width: 100%">
                            <option value="">Select Pay Scale</option>
                            @foreach ($payScale as $ps)
                                <option value="{{ $ps->id }}">{{ $ps->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <div class="row">

                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Grade</label>
                            <div class="input-group">
                                <select name="grade" id="grade_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select grade</option>
                                    @for ($i = 1; $i<=$gradeLevel['grade']; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 form-group">
                            <label class="input-title RequiredStar">Level</label>
                            <div class="input-group">
                                <select name="level" id="level_id" class="form-control clsSelect2" style="width: 100%">
                                    <option value="">Select level</option>
                                    @for ($i = 1; $i<=$gradeLevel['level']; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
        
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Designations</label>
                    <div class="input-group">
                        <select multiple name="designations[]" class="form-control clsSelect2" style="width: 100%">
                            @foreach ($designations as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Recruitment Type</label>
                    <div class="input-group">
                        <select multiple name="recruitment_type_id[]" class="form-control clsSelect2" style="width: 100%">
                            @foreach ($recruitmrntType as $r)
                                <option value="{{ $r->id }}">{{ $r->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-5 form-group">
                    <label class="input-title RequiredStar">Basic Salary</label>
                    <div class="input-group">
                        <input type="number" name="basic" id="add_basic_salary" style="width: 100%;">
                    </div>
                </div>

                <div class="col-sm-5 offset-sm-1 form-group">
                    <label class="input-title RequiredStar">Acting Benefit Amount</label>
                    <div class="input-group">
                        <input type="number" name="acting_benefit_amount" style="width: 100%;">
                    </div>
                </div>

            </div>

            <!-- new start -->
            <div class="row">
                <input type="text" value="" name="pf_id" id="pf_id_alt" hidden>
                <input type="text" value="" name="wf_id" id="wf_id_alt" hidden>
                <input type="text" value="" name="ps_id" id="ps_id_alt" hidden>
                <input type="text" value="" name="osf_id" id="osf_id_alt" hidden>
                <input type="text" value="" name="inc_id" id="inc_id_alt" hidden>

                <div id="pf_div" class="col-sm-5 form-group d-none">
                    <label class="input-title">Select PF</label>
                    <div class="input-group">
                        <select name="pf_id" id="pf_id" style="width: 100%;" class="form-control clsSelect2"></select>
                    </div>
                </div>

                <div id="wf_div" class="col-sm-5  form-group d-none">
                    <label class="input-title">Select WF</label>
                    <div class="input-group">
                        <select name="wf_id" id="wf_id" style="width: 100%;" class="form-control clsSelect2"></select>
                    </div>
                </div>

                <div id="ps_div" class="col-sm-5 form-group d-none">
                    <label class="input-title">Select EPS</label>
                    <div class="input-group">
                        <select name="ps_id" id="ps_id" style="width: 100%;" class="form-control clsSelect2"></select>
                    </div>
                </div>

                <div id="osf_div" class="col-sm-5  form-group d-none">
                    <label class="input-title">Select OSF</label>
                    <div class="input-group">
                        <select name="osf_id" id="osf_id" style="width: 100%;" class="form-control clsSelect2"></select>
                    </div>
                </div>

                <div id="inc_two_div" class="col-sm-5 form-group d-none">
                    <label class="input-title">Select Insurance</label>
                    <div class="input-group">
                        <select name="inc_id" id="inc_id" style="width: 100%;" class="form-control clsSelect2"></select>
                    </div>
                </div>
            </div>
            <!-- new end -->

            <div class="row">

                <div class="inc_div col-sm-11" id="inc_div" >

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
                                {{-- <a onclick="addIncRow(this)" class="">
                                    <i class="fa fa-plus-circle" style="color: rgb(139, 137, 137);"></i>
                                </a> --}}
                                <a onclick="" class="">
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

        $('.clsSelect2').select2();
    });


    $("#add_basic_salary").change(function(){
        $('.inc_div_row').find('input:eq(0)').val('');
        salaryPercentage();
    })

    // First input field and value
    function salaryPercentage() {
        var firstInput = $('.inc_div_row').find('input:eq(0)');
        $('.inc_div_row').find('input:eq(0)').focusout(function(){
            let firstInputValue = firstInput.val();
            let basicSalary = $("#add_basic_salary").val();
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

        $('.clsSelect2').select2();

    }

    showModal({
        titleContent: "Add Salary Structure",
        footerContent: getModalFooterElement({
            'btnNature': {
                0: 'save',
            },
            'btnName': {
                0: 'Save',
            },
            'btnId': {
                0: 'add_saveBtn',
            }
        }),
    });

    $('#add_saveBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/../insert/api", 'post', new FormData($('#salary_structure_add_form')[
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

//offset-sm-1
    $('#project_id').change(function(event) {
        event.preventDefault();
        if (($('#project_id').val()) == '') {
            $("#grade_id").val('').trigger("change");;
            $("#level_id").val('').trigger("change");;

            $("#pf_div").addClass('d-none');
            $("#wf_div").addClass('d-none');
            $("#ps_div").addClass('d-none');
            $("#osf_div").addClass('d-none');
            $("#inc_two").addClass('d-none');
        }else{
            callApi("{{ url()->current() }}/../getData", 'post', new FormData($('#salary_structure_add_form')[0]),
                function(response, textStatus, xhr) {
                    
                    // Start For PF
                    if (response.pfData.length > 1) {
                        $("#pf_id_alt").attr("disable", true);
                        let selectPfOption = "<option value='' selected >Select</option>";
                        $.each(response.pfData, function(index, item) {
                            selectPfOption += "<option value='"+ item.id +"'>"+ item.calculation_amount+"% ( "+ item.effective_date +") </option>";
                        });
                        $('#pf_id').html(selectPfOption);
                        $("#pf_div").removeClass('d-none');
                    } else {
                        $("#pf_id_alt").attr("disable", false);
                        $("#pf_id_alt").val(response.pfData.id);
                        $("#pf_div").addClass('d-none');
                    }
                    // End For PF

                    // Start For PS
                    if (response.epsData.length > 1) {
                        $("#ps_id_alt").attr("disable", true);
                        let selectPsOption = "<option value='' selected >Select</option>";
                        $.each(response.epsData, function(index, item) {
                            selectPsOption += "<option value='"+ item.id +"'>"+ item.amount+" ( "+ item.effective_date +") </option>";
                        });
                        $('#ps_id').html(selectPsOption);
                        $("#ps_div").removeClass('d-none');
                    } else {
                        $("#ps_id_alt").attr("disable", false);
                        $("#ps_id_alt").val(response.epsData.id);
                        $("#ps_div").addClass('d-none');
                    }
                    // End For PS => Pension Sceama


                    // Start For OSF
                    if (response.osfData.length > 1) {
                        $("#osf_id_alt").attr("disable", true);
                        let selectOsfOption = "<option value='' selected >Select</option>";
                        $.each(response.osfData, function(index, item) {
                            selectOsfOption += "<option value='"+ item.id +"'>"+ item.calculation_amount+"% ( "+ item.effective_date +") </option>";
                        });
                        $('#osf_id').html(selectOsfOption);
                        $("#osf_div").removeClass('d-none');
                    } else {
                        $("#osf_id_alt").attr("disable", false);
                        $("#osf_id_alt").val(response.osfData.id);
                        $("#osf_div").addClass('d-none');
                    }
                    // End For OSF

                    // Start For Insurance
                    if (response.incData.length > 1) {
                        $("#inc_id_alt").attr("disable", true);
                        let selectIncOption = "<option value='' selected >Select</option>";
                        $.each(response.incData, function(index, item) {
                            selectIncOption += "<option value='"+ item.id +"'>"+ item.calculation_amount+"% ( "+ item.effective_date +") </option>";
                        });
                        $('#inc_id').html(selectIncOption);
                        $("#inc_two").removeClass('d-none');
                    } else {
                        $("#inc_id_alt").attr("disable", false);
                        $("#inc_id_alt").val(response.incData.id);
                        $("#inc_two").addClass('d-none');
                    }
                    // End For Insurance

                    console.log(response);
                },
                function(response) {
                    showApiResponse(response.status, JSON.parse(response.responseText).message);
                }
            )
        }

        if ( ($("#grade_id").val()) == '' ) {
            $("#ps_div").addClass('offset-sm-1');
            $("#inc_two").addClass('offset-sm-1');
        }
    });

    

    $("#grade_id, #level_id").change(function(event){
        event.preventDefault();

        if ( ($("#grade_id").val()) == '' ) {
            $("#ps_div").addClass('offset-sm-1');
            $("#inc_two").addClass('offset-sm-1');
        }else{
            $("#wf_div").addClass('offset-sm-1');
            $("#osf_div").addClass('offset-sm-1');
            $("#ps_div").removeClass('offset-sm-1');
            $("#inc_two").removeClass('offset-sm-1');
        }

        callApi("{{ url()->current() }}/../getWfData", 'post', new FormData($('#salary_structure_add_form')[0]),
            function(response, textStatus, xhr) {
                
                // Start For WF
                if (response.wfData != null) {
                    
                    if (response.wfData.length > 1) {
                        $("#wf_id_alt").attr("disable", true);
                        let selectWfOption = "<option value='' selected >Select</option>";
                        $.each(response.wfData, function(index, item) {

                            let grade = '';
                            if(item.grade == 0){
                                grade = 'All';
                            }else{
                                grade = item.grade;
                            }

                            if (item.interest_rate == null && item.amount != null ) {
                                selectWfOption += "<option value='"+ item.id +"'>"+ item.amount+" BDT ( "+ item.effective_date +") / Grade -"+ grade +"</option>";

                            }else if (item.interest_rate != null && item.amount == null ){
                                selectWfOption += "<option value='"+ item.id +"'>"+ item.interest_rate+"% ( "+ item.effective_date +") / Grade -"+ grade +"</option>";
                            }
                            
                        });
                        $('#wf_id').html(selectWfOption);
                        $("#wf_div").removeClass('d-none');

                    } else {
                        let selectWfOption = "<option value=''  selected>Select</option>";
                        $.each(response.wfData, function(index, item) {
                            if (item.interest_rate == null && item.amount != null ) {
                                selectWfOption += "<option selected value='"+ item.id +"'>"+ item.amount+" BDT ( "+ item.effective_date +") </option>";

                            }else if (item.interest_rate != null && item.amount == null ){
                                selectWfOption += "<option selected value='"+ item.id +"'>"+ item.interest_rate+"% ( "+ item.effective_date +") </option>";
                            }
                            
                        });
                        $('#wf_id').html(selectWfOption);
                        $("#wf_div").removeClass('d-none');

                    }
                } else {
                    $("#wf_div").addClass('d-none');
                    $("#ps_div").addClass('offset-sm-1');
                    $("#inc_two").addClass('offset-sm-1');
                    $("#osf_div").removeClass('offset-sm-1');
                }
                
                // End For WF
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
        
    });

    

    // let company_id = $("#company_id").val();
    // console.log(company_id);
    //     callApi("{{ url()->current() }}/../getData", 'get',{companyId:company_id}, function(response, textStatus, xhr) {
                
    //         console.log();

    //         },
    //         function(response) {
    //             showApiResponse(response.status, JSON.parse(response.responseText).message);
    //         }
    //     );
</script>
