{{-- Account --}}
{{-- @dd($data['viewPage']) --}}
<style>
    .autocomplete {
        position: relative;
        display: inline-block;
    }
    .autocomplete-items {
        position: absolute;
        z-index: 99;
        top: 100%;
        left: 0;
        right: 0;
    }
</style>

<div id="Account" class="tab-pane show">

    <div class="row">

        <div class="border {{ (isset($data['viewPage'])) ? 'col-lg-12' : 'col-lg-11'}}">

            <div class="row bankBranchArea" style="margin-top: 15px;">


                <div class="col-lg-6 form-group">
                    <div class="row">
                        <label class="col-lg-4 input-title">Bank</label>
                        <div class="input-group col-lg-8">
                            {{-- onchange="loadSelectBox({'bankId' : this.value},'getBankBranches',$('#acc_branch__' + findIdNo(this))[0])" --}}
                            <select class="form-control clsSelect2 empBank" id="acc_bank__1" name="acc_bank_id[]" style="width: 100%">
                                <option value="" selected>Select</option>
                                @foreach($data['banks'] as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>

                            {{-- <select onchange="loadSelectBox({'bankId' : this.value},'getBankBranches',$('#acc_branch__' + findIdNo(this))[0])" class="form-control clsSelect2 empBank" id="acc_bank__1" name="acc_bank_id[]" style="width: 100%">
                                <option value="" selected>Select</option>
                                @foreach($data['banks'] as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select> --}}
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 form-group">
                    <div class="row">
                        <label class="col-lg-4 input-title">Branch</label>
                        <div class="input-group col-lg-8">
                            {{-- <select class="form-control clsSelect2" id="acc_branch__1" name="acc_bank_branch_id[]" style="width: 100%">
                                <option value="" selected>Select</option>
                            </select> --}}

                            <input type='text' class="form-control bankBranch" onclick="branchOnChange(this)" id="acc_branch__1" name="acc_bank_branch_id[]" style="width: 100%">
                        </div>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-lg-6 form-group">
                    <div class="row">
                        <label class="col-lg-4 input-title">Account Type</label>
                        <div class="input-group col-lg-8">
                            <select class="form-control clsSelect2" name="acc_bank_acc_type[]" style="width: 100%">
                                <option value="">Select</option>
                                <option value="regular">Regular</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 form-group">
                    <div class="row">
                        <label class="col-lg-4 input-title">Account Number</label>
                        <div class="input-group col-lg-8">
                            <input type="text" class="form-control round" name="acc_bank_acc_number[]"
                                   placeholder="Enter Account Number">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

            </div>

        </div>

        @if(!isset($data['viewPage']))

            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <div class="row">
                    <button onclick="addNewAccountField();" class="btn btn-primary btn-round" style="margin-top: 25%"><i class="fas fa-plus"></i></button>
                </div>
            </div>

        @endif

    </div>

</div>

<script>
    let accCurrentId = 1;
    $(document).ready(function (){
        let empAccData = {!! json_encode((isset($empData['empAccData'])) ? $empData['empAccData'] : null) !!};

        if(empAccData !== null && empAccData.length > 0){

            for (let i=0; i<empAccData.length; i++){

                if (i !== 0){
                    addNewAccountField();
                }

                

                setEditData(document.querySelectorAll("[name='acc_bank_id[]']")[i], empAccData[i]['bank_id']);
                setEditData(document.querySelectorAll("[name='acc_bank_branch_id[]']")[i], empAccData[i]['bank_branch_id']);

                $.when(loadInputField({'bankId' : document.querySelectorAll("[name='acc_bank_id[]']")[i].value},'getBankBranches',document.querySelectorAll("[name='acc_bank_branch_id[]']")[i])).then(function (response){
                    setEditData(document.querySelectorAll("[name='acc_bank_branch_id[]']")[i], empAccData[i]['bank_branch_name']);
                });

                setEditData(document.querySelectorAll("[name='acc_bank_acc_type[]']")[i], empAccData[i]['bank_acc_type']);
                setEditData(document.querySelectorAll("[name='acc_bank_acc_number[]']")[i], empAccData[i]['bank_acc_number']);

            }
        }
    });

    function addNewAccountField(){
        $('.clsSelect2').select2('destroy');

        let accElement = document.querySelector('#Account');
        let lastDiv = accElement.lastElementChild;

        let cloneNode = cleanCloneNode(lastDiv.cloneNode(true));
        let bankSelNode = cloneNode.querySelector('#acc_bank__'+ accCurrentId);
        let braSelNode = cloneNode.querySelector('#acc_branch__'+ accCurrentId);
        let child = braSelNode.children;
        for (let i=0; i<child.length; i++){
            if (i !== 0){
                child[i].remove();
            }
        }
        accCurrentId++;
        bankSelNode.removeAttribute("id");
        braSelNode.removeAttribute("id");
        bankSelNode.setAttribute('id','acc_bank__'+accCurrentId);
        braSelNode.setAttribute('id','acc_branch__'+accCurrentId);

        accElement.append(cloneNode);

        if (!isViewPage){
            lastDiv.lastElementChild.innerHTML = '<button onclick="removeAccountField(this.parentNode.parentNode)" class="btn btn-danger btn-round" style="margin-top: 25%"><i class="fas fa-minus"></i></button>';
        }

        $('.clsSelect2').select2();
    }

    function removeAccountField(node){
        node.remove();
    }

    function findIdNo(node){
        let id = node.id;
        let idS = '';
        for (let i = (id.length -1); i > 9; i--){
            idS += id[i];
        }
        return idS.split("").reverse().join("");
    }




    let autocomplete = (inp, arr) => {
        inp.addEventListener("input", function(e) {
            let a, //OUTER html: variable for listed content with html-content
            b, // INNER html: filled with array-Data and html
            i, //Counter
            val = this.value;

            closeAllLists();

            if (!val) {
            return false;
            }

            currentFocus = -1;

            a = document.createElement("DIV");

            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items list-group text-left");

            this.parentNode.appendChild(a);

            for (i = 0; i < arr.length; i++) {
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                b = document.createElement("DIV");
                b.setAttribute("class","list-group-item list-group-item-action");
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                b.addEventListener("click", function(e) {
                inp.value = this.getElementsByTagName("input")[0].value;
                closeAllLists();
                });
                a.appendChild(b);
            }
            }
        });

        /*execute a function presses a key on the keyboard:*/
        inp.addEventListener("keydown", function(e) {
            var x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
            } else if (e.keyCode == 38) {
            currentFocus--;
            addActive(x);
            } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
            }
        });

        let addActive = (x) => {
            if (!x) return false;
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = x.length - 1;
            x[currentFocus].classList.add("active");
        }

        let removeActive = (x) => {
            for (let i = 0; i < x.length; i++) {
            x[i].classList.remove("active");
            }
        }

        let closeAllLists = (elmnt) => {
            var x = document.getElementsByClassName("autocomplete-items");
            for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
            }
        }

        document.addEventListener("click", function(e) {
            closeAllLists(e.target);
        });

    };

   
    var bankBranchArray = [];
    function branchOnChange(inputElement){

        let nearestBankDiv = $(inputElement).closest('.bankBranchArea');
        let nearestBankSelect = nearestBankDiv.find('select.empBank');

        let selectBankId = nearestBankSelect.attr("id");
        let bankValue = nearestBankSelect.val();

        let bankBranchId = $(inputElement).attr("id");

        let currntURL ="{{ url()->current() }}";
        let urlSplit = currntURL.split('/');
        let findAction = urlSplit[urlSplit.length - 2];
        let actionUrl = "";
        if (findAction == 'edit' || findAction == 'view') {
            actionUrl = "{{ url()->current() }}/../../getBranchData";
        }else{
            actionUrl = "{{ url()->current() }}/../getBranchData";
        }

        while (bankBranchArray.length > 0) {
            bankBranchArray.pop();
        }

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: {context : 'getBranchData', bankId : bankValue},
            dataType: "json",
            success: function (response) {

                $.each(response, function( index, value ) {
                    bankBranchArray.push(value.name);
                });

            },
            error: function(err){
                alert('error! :'+err);
            }
        });

        autocomplete(document.getElementById(bankBranchId), bankBranchArray);

    }


</script>
