@extends('Layouts.erp_master')
@section('content')

    <?php
    use App\Services\CommonService as Common;
    use App\Services\HtmlService as HTML;
    use App\Services\HrService as HRS;
    ?>
    <!-- Page -->
    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true" autocomplete="off">
        @csrf

        <div class="row">
            <div class="col-lg-12">
                <div class="nav-tabs-horizontal tabList" >
                    <ul class="nav nav-tabs nav-tabs-reverse nav-fill d-print-none" role="tablist">
                        <li class="nav-item mr-3" ><a class="nav-link active" data-toggle="tab" role="tab" href="#General">General</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Organization">Organization</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Account">Account</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Education">Education</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Training">Training</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Experience">Experience</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Guarantor">Guarantor</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Nominee">Nominee</a></li>
                        <li class="nav-item mr-3" ><a class="nav-link disabled" data-toggle="tab" role="tab" href="#Reference">Reference</a></li>
                    </ul>
                </div>
                <div class="tab-content">

                    <input type="text" name="emp_id" value="" id="emp_id" hidden>

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
                        {{-- <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a> --}}
                        <button class="btn btn-primary btn-round" id="prevBtn">Previous</button>
                        <button class="btn btn-primary btn-round" id="draftBtn">Draft</button>
                        <button class="btn btn-primary btn-round" id="nextBtn">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- End Page -->
    <script>
        $(document).ready(function (){
            $('.ajaxRequest').hide();
            $('.httpRequest').hide(); // new entry button hide

            //switchTab('Training', 'next');
            makeFieldRequired();
            makeInputDisable();
        });

        function makeInputDisable(){
            let tabIdArr = ['General'];
            for (let i=0; i<tabIdArr.length; i++){
                let inputNodes = $('#'+ tabIdArr[i] +' :input');

                for (let i=0; i<inputNodes.length; i++){
                    if (inputNodes[i].tagName === 'SELECT'){
                        let value = inputNodes[i].options[inputNodes[i].selectedIndex].text;
                        value = value === 'Select' ? '' : value;
                        let parentNode = inputNodes[i].parentNode;

                        // if (inputNodes[i].name === 'branch_id'){
                        //     parentNode.innerHTML = '<input  type="text" value="'+ value +'" class="form-control round">';
                        // }
                    }
                }
            }
        }

        $('#nextBtn').click(function (event) {
            event.preventDefault();

            if( $("#branch_id").val() == '' ){
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    text: "Branch is empty.",
                });
                return;
            }else if( $("#emp_code").val() == '' ){
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    text: "Employee code is empty.",
                });
                return;
            }

            let currTabId = getCurrentTabId();
            let postArr = [];
            if (currTabId === 'Reference'){
                postArr = ['General','Organization','Account','Education','Training','Experience','Guarantor','Nominee','Reference'];
            }
            else {
                postArr = [currTabId];
            }

            getValidationResult(postArr).done(function(response) {
                    if (response['alert-type'] === 'success' && response['action'] === 'saved') {
                        $('form').trigger("reset");
                        swal({
                            icon: 'success',
                            title: 'Success...',
                            text: response['message'],
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.href = "../";
                        });
                    }
                    else if (response['alert-type']==='success' && response['action'] === 'next') {
                        switchTab(currTabId,'next');
                        window.scrollTo(0,0);
                    }
                    else{
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = response['message'];
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            content: wrapper,
                        });
                    }
                })
                .fail(function() {
                    $('#nextBtn').prop('disabled', false);
                    console.log("error");
                })
                .always(function() {
                    $('#nextBtn').prop('disabled', false);
                    console.log("complete");
                });
        });

        function getValidationResult(postArr){
            return $.ajax({
                url: "{{ url()->current() }}",
                type: 'POST',
                contentType : false,
                processData: false,
                dataType: 'json',
                data: makeFormDataObject(postArr),
            })
        }

        $('#prevBtn').click(function (event){
            event.preventDefault();
            switchTab(getCurrentTabId(),'prev');
            window.scrollTo(0,0);
        });

        function makeFormDataObject(divArr){
            let formData = new FormData();
            for (let i=0; i<divArr.length; i++){
                let divData = $('#'+ divArr[i] +' :input').serializeArray();
                for (let i = 0; i<divData.length; i++){
                    formData.append(divData[i].name,divData[i].value);
                }
                let fileData = $('#'+ divArr[i] +' :input[type=file]');

                let nomPhoto = [], nomSig = [];
                for (let j = 0; j<fileData.length; j++){
                    if (divArr[i] === 'Nominee'){

                        if (typeof fileData[j].files[0] === 'undefined'){
                            formData.append(fileData[j].name, new File([""], "not_file"));
                        }
                        else{
                            formData.append(fileData[j].name, fileData[j].files[0]);
                        }
                    }
                    else{
                        formData.append(fileData[j].name, (typeof fileData[j].files[0] === 'undefined') ? '' : fileData[j].files[0]);
                    }
                }
                //console.log(nomPhoto);

                formData.append('submittedFrom', divArr[i]);
            }
            if (divArr.length > 1){
                // formData.append('branch_id',$('#branch_id').find(":selected").val());
                formData.append('branch_id',$('#branch_id').val());
                formData.append('emp_id',$('#emp_id').val());
            }else{
                // formData.append('branch_id',$('#branch_id').find(":selected").val());
                formData.append('branch_id',$('#branch_id').val());
                formData.append('emp_id',$('#emp_id').val());
            }
            return formData;
        }

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
                    let currTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]] +'"]');
                    nextTabNode.removeClass('disabled');
                    if (arr[tabs[currentTab]+1] === 'Reference'){
                        $('#nextBtn').text('Update');
                    }
                    nextTabNode.tab('show');
                    currTabNode.addClass('disabled');
                    return "success";
                }
            }
            else if (action === 'prev'){
                let prevTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]-1] +'"]');
                let currTabNode = $('.nav-tabs a[href="#'+ arr[tabs[currentTab]] +'"]');
                if (typeof arr[tabs[currentTab]-1] !== 'undefined'){
                    prevTabNode.removeClass('disabled');
                    if (arr[tabs[currentTab]] === 'Reference'){
                        $('#nextBtn').text('Next');
                    }
                    prevTabNode.tab('show');
                    currTabNode.addClass('disabled');
                    return "success";
                }
            }
        }

        function cleanCloneNode(node){
            let inputNode = node.querySelectorAll('input');
            let textAreaNode = node.querySelectorAll('textarea');
            let errDivNode = node.querySelectorAll('.has-error');
            let selectNode = node.querySelectorAll('select');

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
            for (let n of selectNode){
                for (let i = 0; i<n.options.length; i++){
                    if (n.options[i].selected){
                        n.options[i].selected = false;
                    }
                }
            }
            return node;
        }

        function makeFieldRequired(){
            let requiredField = {!! json_encode($data['requiredField']) !!};
            for (const reqFld in requiredField){
                if (requiredField[reqFld] === 'required'){
                    let inputField = $(':input[name^='+ reqFld +']');
                    if (typeof inputField[0] !== 'undefined'){
                        inputField.prop('required',true);
                        let labelNode = findAssociateLabel(inputField[0].parentNode.parentNode);
                        if (labelNode !== null){
                            labelNode.classList.add('RequiredStar');
                        }
                    }
                }
            }
        }

        function findAssociateLabel(node){
            if (node.tagName === 'BODY'){
                return null;
            }
            let child = node.children;
            if (parent.tagName === 'LABEL'){
                return parent;
            }
            for (let c of child){
                if (c.tagName === 'LABEL'){
                    return c;
                }
            }
            if (typeof node === 'undefined'){
                console.log('Found undefined node!!!');
            }
            return findAssociateLabel(node.parentNode);
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

        $('.nav-link').click(function (){
            let tabHref = this.href;
            let targetTabId = '';
            for (let i=tabHref.length-1; i>0; i--){
                if (tabHref[i] === '#'){
                    break;
                }
                targetTabId+=tabHref[i];
            }
            targetTabId = targetTabId.split("").reverse().join("");

            let tabs = {'General':0,'Organization':1,'Account':2,'Education':3,'Training':4,'Experience':5,'Guarantor':6,'Nominee':7,'Reference':8};
            let arr = ['General','Organization','Account','Education','Training','Experience','Guarantor','Nominee','Reference'];

            let currentTabNo = tabs[getCurrentTabId()];
            let targetTabNo = tabs[targetTabId];

            getTabAccessPermission(arr,currentTabNo,targetTabNo).then(
                function (value){
                    if (value.status === "permit"){
                        targetTabNo - 1 === -1 ? switchTab(arr[1],'prev') : switchTab(arr[targetTabNo - 1],'next');
                    }
                    else {
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = 'Please fill the current tab required fields first.';
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            content: wrapper,
                        });
                        let targetTabNo = tabs[value.tabName];
                        targetTabNo - 1 === -1 ? switchTab(arr[1],'prev') : switchTab(arr[targetTabNo - 1],'next');
                    }
                },
                function (error){
                    alert(error);
                }
            )
        });

        async function getTabAccessPermission(tabArr, iteratorStart, iteratorEnd){
            if (iteratorStart >= iteratorEnd){
                return {"status" : "permit", "message" : "ok", "tabName" : tabArr[iteratorStart]};
            }
            const response = await getValidationResult([tabArr[iteratorStart]]);
            if (response['alert-type'] === 'success'){
                if (iteratorStart >= iteratorEnd){
                    return {"status" : "permit", "message" : "ok", "tabName" : tabArr[iteratorStart]};
                }
                else {
                    iteratorStart++;
                    return await getTabAccessPermission(tabArr, iteratorStart, iteratorEnd);
                }
            }
            else {
                return {"status" : "denied", "message" : `Please fill ${tabArr[iteratorStart]} tab first`, "tabName" : tabArr[iteratorStart]};
            }
        }

        function postDataAsDraft(postArr){
            return $.ajax({
                url: "{{ url()->current() }}/draft",
                type: 'POST',
                contentType : false,
                processData: false,
                dataType: 'json',
                data: makeFormDataObject(postArr),
            })
        }

        $('#draftBtn').click(function (event) {
            event.preventDefault();

            if( $("#branch_id").val() == '' ){
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    text: "Branch is empty.",
                });
                return;
            }else if( $("#emp_code").val() == '' ){
                swal({
                    icon: 'warning',
                    title: 'Oops...',
                    text: "Employee code is empty.",
                });
                return;
            }

            let currTabId = getCurrentTabId();
            let postArr = [];
            if (currTabId === 'Reference'){
                postArr = ['General','Organization','Account','Education','Training','Experience','Guarantor','Nominee','Reference'];
            }
            else {
                postArr = [currTabId];
            }

            postDataAsDraft(postArr).done(function(response) {
                if (response['alert-type'] === 'success' && response['action'] === 'saved') {
                    $('form').trigger("reset");
                    swal({
                        icon: 'success',
                        title: 'Success...',
                        text: response['message'],
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = "../";
                    });
                }
                else if (response['alert-type']==='success' && response['action'] === 'next') {
                    switchTab(currTabId,'next');
                    window.scrollTo(0,0);

                    if (typeof response.emp_id !== 'undefined' && response.emp_id !== null) {
                        $("#emp_id").val(response.emp_id);
                    }
                }
                else{
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = response['message'];
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        content: wrapper,
                    });
                }
            })
            .fail(function() {
                $('#nextBtn').prop('disabled', false);
                console.log("error");
            })
            .always(function() {
                $('#nextBtn').prop('disabled', false);
                console.log("complete");
            });
        });

    </script>
@endsection
