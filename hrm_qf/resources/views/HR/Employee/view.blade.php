@extends('Layouts.erp_master')
@section('content')

@php
    use App\Services\CommonService as Common;
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;
@endphp
    <!-- Page -->
    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="nav-tabs-horizontal tabList" >
                    <ul class="nav nav-tabs nav-tabs-reverse nav-fill d-print-none" role="tablist">
                        <li class="nav-item mr-3" ><a class="nav-link active" data-toggle="tab" role="tab" href="#General">General</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Organization">Organization</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Account">Account</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Education">Education</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Training">Training</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Experience">Experience</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Guarantor">Guarantor</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Nominee">Nominee</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link" data-toggle="tab" role="tab" href="#Reference">Reference</a></li>
                    </ul>
                </div>
                <div class="tab-content">

                    {{-- <input type="text" name="emp_id" value="" id="emp_id" hidden> --}}

                    @include('HR.Employee.general')

                    @include('HR.Employee.organization')

                    @include('HR.Employee.account')

                    @include('HR.Employee.education')

                    @include('HR.Employee.training')

                    @include('HR.Employee.experience')

                    @include('HR.Employee.guarantor')

                    @include('HR.Employee.nominee')

                    @include('HR.Employee.reference')

                </div>

            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="form-group d-flex justify-content-center">
                    <div class="example example-buttons">
                        <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                        <button class="btn btn-primary btn-round" id="prevBtn">Previous</button>
                        <button class="btn btn-primary btn-round" id="nextBtn">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- End Page -->
    <script>
        let projectType = {!! json_encode((isset($data['projectType'])) ? $data['projectType'] : '') !!};
        let bankBranch = {!! json_encode((isset($data['bankBranch'])) ? $data['bankBranch'] : '') !!};
        $(document).ready(function (){
            $('.ajaxRequest').hide();
            $('.httpRequest').hide(); // new entry button hide
            //switchTab('Training', 'next');
            makeInputDisable();
        });

        console.log((typeof projectType[0] !== "undefined") ? projectType[0]['project_type_name'] : "yyyy");

        function makeInputDisable(){
            let tabIdArr = ['General','Organization','Account','Education','Training','Experience','Guarantor','Nominee','Reference'];
            for (let i=0; i<tabIdArr.length; i++){
                let inputNodes = $('#'+ tabIdArr[i] +' :input');
                for (let i=0; i<inputNodes.length; i++){
                    if (inputNodes[i].tagName === 'SELECT'){
                        let value = inputNodes[i].options[inputNodes[i].selectedIndex].text;
                        value = value === 'Select' ? '' : value;
                        let parentNode = inputNodes[i].parentNode;

                        if (inputNodes[i].name === 'org_project_type_id'){
                            parentNode.innerHTML = '<input disabled type="text" value="'+ ((typeof projectType[0] !== "undefined") ? projectType[0]['project_type_name'] : "") +'" class="form-control round">';
                        }
                        else if (inputNodes[i].name === 'acc_bank_branch_id'){
                            parentNode.innerHTML = '<input disabled type="text" value="'+ ((typeof bankBranch[0] !== "undefined") ? bankBranch[0]['name'] : "") +'" class="form-control round">';
                        }
                        else {
                            parentNode.innerHTML = '<input disabled type="text" value="'+ value +'" class="form-control round">';
                        }
                    }
                    if (inputNodes[i].type === 'file'){
                        let inpDiv  = inputNodes[i].parentElement.parentElement;
                        inpDiv.innerHTML = '';
                        inpDiv.parentElement.parentElement.nextElementSibling.innerHTML = '';
                    }
                    inputNodes[i].disabled =true;
                    inputNodes[i].placeholder = '';
                }
            }
        }

        $('#nextBtn').click(function (event) {

            event.preventDefault();
            let currTabId = getCurrentTabId();
            switchTab(currTabId,'next');
        });

        function cleanCloneNode(node){
            let inputNode = node.querySelectorAll('input');
            let textAreaNode = node.querySelectorAll('textarea');
            let errDivNode = node.querySelectorAll('.has-error');
            //let selectNode = node.querySelectorAll('select');

            //console.log(selectNode[0]);

            for (let n of inputNode){
                n.value = '';
            }
            for (let n of textAreaNode){
                n.value = '';
            }
            for (let n of errDivNode){
                n.classList.remove("has-error");
                n.classList.remove("has-danger");
                n.querySelector('.help-block').textContent = '';
            }
            /*for (let n of selectNode){
                let child = n.children;
                for (let i=0; i<child.length; i++){
                    //console.log(child[i]);
                    if (i !== 0){
                        child[i].remove();
                    }
                }
            }*/
            return node;
        }


        $('#prevBtn').click(function (event){
            event.preventDefault();
            switchTab(getCurrentTabId(),'prev');
            window.scrollTo(0,0);
        });

        function getCurrentTabId(){
            let currTabNode = document.querySelector('.tabList').querySelector('.active').href;
            let id = '';
            for (let i=currTabNode.length-1; i>0; i--){
                if (currTabNode[i] === '#'){
                    break;
                }
                id+=currTabNode[i];
            }
            return id.split("").reverse().join("");
        }

        function switchTab(currentTab, action){
            let tabs = {'General':0,'Organization':1,'Account':2,'Education':3,'Training':4,'Experience':5,'Guarantor':6,'Nominee':7,'Reference':8};
            let arr = ['General','Organization','Account','Education','Training','Experience','Guarantor','Nominee','Reference'];
            if (action === 'next'){
                if (typeof arr[tabs[currentTab]+1] !== 'undefined'){
                    let nextTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]+1] +'"]');
                    /*let currTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]] +'"]');
                    nextTabNode.removeClass('disabled');
                    if (arr[tabs[currentTab]+1] === 'Reference'){
                        $('#nextBtn').text('Update');
                    }*/
                    nextTabNode.tab('show');
                    //currTabNode.addClass('disabled');
                    return "success";
                }
            }
            else if (action === 'prev'){
                let prevTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]-1] +'"]');
                //let currTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]] +'"]');
                if (typeof arr[tabs[currentTab]-1] !== 'undefined'){
                    /*prevTabNode.removeClass('disabled');
                    if (arr[tabs[currentTab]] === 'Reference'){
                        $('#nextBtn').text('Next');
                    }*/
                    prevTabNode.tab('show');
                    //currTabNode.addClass('disabled');
                    return "success";
                }
            }
        }

        function loadSelectBox(postData, url, targetNode){

            return $.ajax({
                method : 'POST',
                url : "../../" + url,
                dataType : 'json',
                data : postData,
                success : function (response){
                    let child = targetNode.children;
                    for (let i=0; i<child.length; i++){
                        if (i !== 0){
                            child[i].remove();
                        }
                    }
                    $.each(response, function (index, value){
                        var option = document.createElement("option");
                        option.text = value;
                        option.value = index;
                        targetNode.add(option);
                        //targetNode.innerHTML += '<option value="'+ index +'">'+ value +'</option>';
                    });
                    $('.clsSelect2').select2();
                }
            });
        }

        function loadInputField(postData, url, targetNode){

            return $.ajax({
                method : 'POST',
                url : "../../" + url,
                dataType : 'json',
                data : postData,
                success : function (response){
                    // return response;
                }
            });
        }


        $(window).on("load", function(event) {
            setSalaryInformation(event);
        });

        function setSalaryInformation(event) {
            event.preventDefault();

            let empOrgData = {!! json_encode((isset($empData['empOrgData'])) ? $empData['empOrgData'] : null) !!};


            setZero();
            $.ajax({
                type: "POST",
                url: "{{ url()->current() }}/../../getSalaryInformation",
                data: {
                    context : 'getData',
                    org_grade : empOrgData[0]['grade'],
                    org_level : empOrgData[0]['level'],
                    org_step : empOrgData[0]['step'],
                    org_fiscal_year_id : empOrgData[0]['payscal_id'],
                    org_rec_type_id : empOrgData[0]['rec_type_id'],
                },
                dataType: "json",
                success: function (response) {

                    // console.log(response);

                    if (response.salaryInfo && response.salaryInfo != null) {
                        let basic = response.salaryInfo.basic;
                        let total_basic = response.salaryInfo.total_basic;

                        $("#salary_structure_id").val(response.salaryInfo.salary_structure_id);
                        $("#incrementPer").html("("+response.salaryInfo.incrementPer+"%)");

                        $("#org_salary_table_f_1").html(response.salaryInfo.year);
                        $("#org_salary_table_f_2").html(basic);
                        $("#org_salary_table_f_3").html(response.salaryInfo.increment);
                        $("#org_salary_table_f_4").html(total_basic);

                        $("#t_step").html(response.salaryInfo.year);
                        $("#t_basic").html(basic);
                        $("#t_increment").html(response.salaryInfo.increment);
                        $("#t_total_basic").html(total_basic);


                        let benTypeA = 0;
                        let benTypeB = 0;
                        let benTypeC = 0;
                        let deduction = 0;
                        if (response.salaryInfo.allowance) {

                            $.each(response.salaryInfo.allowance, function(i, item) {
                                if(i==1){
                                    $(".benAData").remove();
                                    $.each(item, function(j, j_val) {
                                        let benAData = '<tr class="benAData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ j +'</th>'+
                                                            '<td>'+j_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeA").after(benAData);
                                        if(j_val == null){
                                            j_val = 0;
                                        }
                                        benTypeA += parseInt(j_val);
                                    });
                                    $("#grossBenTypeA").html(benTypeA);
                                }
                                if(i==2){
                                    $(".benBData").remove();
                                    $.each(item, function(k, k_val) {

                                        let benBData = '<tr class="benBData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ k +'</th>'+
                                                            '<td>'+k_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeB").after(benBData);
                                        if(k_val == null){
                                            k_val = 0;
                                        }
                                        benTypeB += parseInt(k_val);
                                    });
                                    $("#grossBenTypeB").html(benTypeB);
                                }
                                if(i==3){
                                    $(".benCData").remove();
                                    $.each(item, function(l, l_val) {

                                        let benCData = '<tr class="benCData">'+
                                                            '<th> &ensp; &ensp; &ensp; '+ l +'</th>'+
                                                            '<td>'+l_val+'</td>'+
                                                        '</tr>';
                                        $("#BenefitTypeC").after(benCData);
                                        if(l_val == null){
                                            l_val = 0;
                                        }
                                        benTypeC += parseInt(l_val);
                                    });
                                    $("#grossBenTypeC").html(benTypeC);
                                }
                            });



                        }

                        if(response.salaryInfo.deduction){
                            $(".deductionRmData").remove();
                            $.each(response.salaryInfo.deduction, function(i, val) {
                                let DeductionData = '<tr class="deductionRmData">'+
                                                    '<th> &ensp; &ensp; &ensp; '+ i +'</th>'+
                                                    '<td>'+val+'</td>'+
                                                '</tr>';
                                $("#t_Deduction").after(DeductionData);
                                if(val == null){
                                    val = 0;
                                }
                                deduction += parseInt(val);
                            });
                            $("#grossDeduction").html(deduction);
                        }

                        let netSalaryA = 0;
                        if(benTypeA != 0){
                            netSalaryA = ((total_basic + benTypeA) - deduction);
                        }


                        let netSalaryB = 0;
                        if(benTypeB != 0){
                            netSalaryB = ((total_basic + benTypeA + benTypeB) - deduction);
                        }

                        let netSalaryC = 0;
                        if(benTypeC != 0){
                            netSalaryC = ((total_basic + benTypeA + benTypeB + benTypeC) - deduction);
                        }

                        $("#t_net_salary_a").html(netSalaryA);
                        $("#t_net_salary_b").html(netSalaryB);
                        $("#t_net_salary_c").html(netSalaryC);

                    }else{
                        setZero();
                        $("#org_basic_salary").val(00);
                        $("#org_tot_salary").val(00);

                        $("#grossBenTypeA").html(00);
                        $("#grossBenTypeB").html(00);
                        $("#grossBenTypeC").html(00);
                        $("#grossDeduction").html(0);
                    }

                },
                error: function(error){
                    $("#tempSalaryTable").addClass('d-none');
                    swal({
                        icon: 'Error',
                        title: 'Oops...',
                        text: "Error: "+error,
                    });
                }
            });
        }

    </script>
@endsection
