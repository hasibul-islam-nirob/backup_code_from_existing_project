@extends('Layouts.erp_master')
@section('content')
<?php
use App\Services\CommonService;
?>
<form enctype="multipart/form-data" method="post" class="form-horizontal" id="submit_form"
     autocomplete="off"> 
    @csrf                          


    <div class="row">
        <div class="col-lg-8 offset-lg-3">
            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Select Module</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="module_id[]" id="module_id" required
                         data-error="Please Select Module" multiple>
                        <option value="">Select</option>
                        @foreach($module_data as $row)
                        <option value="{{ $row->id }}">{{ $row->module_name }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
    </div>
   


  
    <!--Head Office Details -->
    <div class="panel panel-default">
        <div class="panel-heading p-2 mb-4">Configureation</div>
        <div class="panel-body">
           
            <div class="row"> 
                <input type="hidden" id="headtotalRow" value="0">
                <div class="col-lg-12" id="headDivID">
        
                   {{-- <div class="new-row" id="orginalrow"> --}}
                        <div class="row new-row" id="parentrow">
                            <div class="col-lg-2 form-group">
                                <label class="input-title RequiredStar">Title Name</label>
                                <div class="input-group">
                                    <input type="text"  class="form-control" name="head_title[]"
                                    data-error="Title name  required" id="head_title_0"
                                    placeholder="Enter Title">
                                </div>
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <div class="col-lg-2 form-group">
                                <label class="input-title RequiredStar">Applicable For</label>
                                
                                <div class="input-group">
                                    <select class="form-control clsSelect2" onchange="applicationvaluemove(this.value, 0)" 
                                         data-error="Please Select Branch" id="select_2_1_0">
                                        <option value="">Select</option>
                                        <option value="-1">All</option>
                                        <option value="1">Head Office</option>
                                        <option value="2">Branch Office</option>
                                    </select>
                                </div>
                                <input type="hidden" name="head_signatorApplicable[]" id="application_0" value="">
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <div class="col-lg-2 form-group">
                                <label class="input-title RequiredStar">Select Designation</label>
                                <div class="input-group">
                                    <select class="form-control clsSelect2"
                                         data-error="Please Select Designation" id="select_2_2_0"
                                         onchange="fnChangeEmployeeHead(0); loaddesignation(this.value, 0)"
                                        >
                                        <option value="">Select</option>
                                        <option value="-2">N/A</option>
                                        <option value="-1">Logged In User</option>
                                        @foreach($EmpDesignations as $des)
                                        <option value="{{ $des->id }}">{{ $des->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="head_signatorDesignationId[]" id="designation_0" value="">
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            <div class="col-lg-2 form-group">
                                <label class="input-title">Select Employee</label>
                                <div class="input-group">
                                    <select class="form-control clsSelect2 emp" id="select_2_3_0"
                                        data-error="Please Select Employee" onchange="loademployee(this.value, 0)">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <input type="hidden" name="head_signatorEmployeeId[]" id="employee_0" value="">
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            
                            <div class="col-lg-2 form-group">
                                <label class="input-title RequiredStar">Order </label>
                                <div class="input-group">
                                    <input type="text" class="form-control textNumber" name="head_positionOrder[]"
                                         data-error="Order is required" id="order_id_0"
                                        placeholder="Enter Order">
                                </div>
                                
                                <div class="help-block with-errors is-invalid"></div>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 newrowadddiv" style="margin-top: 21px">
                                <div class="col-lg-12 text-right">
                                    <a href="javascript:void(0)" id="btnSearch" class="addrow btn btn-primary"
                                      class="btn btn-primary btn-round">Add New Row</a>
                                </div>
                            </div>
                        </div>
                        
                   
                    <div class="list-rows">

                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Branch Details -->
{{-- <div class="panel panel-default">
    <div class="panel-heading p-2 mb-4">Branch Office</div>
    <div class="panel-body">
        <div class="row text-right p-10">
            <div class="col-lg-12 text-right">
             
                
                <a href="javascript:void(0)" id="btnSearch" onclick="btnAddNewRowbranch();"  class="btn btn-primary btn-round">Add New Row</a>
            </div>
        </div>
        <div class="row"> 
            <input type="hidden" id="branchtotalRow" value="0">
    
            <div class="col-lg-12 offset-lg-1" id="branchDivID">
    
                
                <div class="new-row2">
                    <div class="row" id="row_branch_0">
                        <div class="col-lg-2 form-group">
                            <label class="input-title RequiredStar">Title Name</label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="branch_title" name="branch_title[]"
                                 required data-error="Title name  required"
                                placeholder="Enter Title">
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                        <div class="col-lg-2 form-group">
                            <label class="input-title RequiredStar">Select Designation</label>
                            <div class="input-group">
                                <select class="form-control clsSelect2" name="branch_signatorDesignationId[]" id="branch_signatorDesignationId_0" 
                                    required data-error="Please Select Product" >
                                    <option value="">Select</option>
                                    @foreach($EmpDesignations as $des)
                                    <option value="{{ $des->id }}"}}>{{ $des->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                       
                        
                        <div class="col-lg-2 form-group">
                            <label class="input-title RequiredStar">Order </label>
                            <div class="input-group">
                                <input type="text" class="form-control round" id="branch_positionOrder_0" name="branch_positionOrder[]"
                                     required data-error="Order is required"
                                    placeholder="Enter Order">
                            </div>
                            
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                        <div class="col-lg-2 form-group academicTrashDiv">                            
                            <label class="input-title"></label>
                            <div class="input-group">
                                <a href="javascript:void(0)" onclick="btnRemoveRowBranch(0);"  class="btn btn-danger btn-round">Remove</a>
    
                            </div>
                        </div>
                       
                    </div>
                    
                </div>
                
                
            </div>
        </div>
        
    </div>
</div> --}}

    @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'id'    => 'send_btn',
                            'title' => 'Save',
                            'exClass' => 'float-right'
                        ]])
   
</form>
    
<script type="text/javascript">
// $(document).ready(function() {
//     $('.clsSelect2').select2();
// });

    let i = 0;
    function applicationvaluemove(value, id){
        var id = 'application_' + id;
        // console.log(id);
        var inputfield = document.getElementById(id);
        inputfield.value = value;
        console.log(inputfield.value);
    }

    function loademployee(value, id){
        var id = 'employee_' + id;
        var inputfield = document.getElementById(id);
        inputfield.value = value;
        console.log(inputfield.value);
    }

    function loaddesignation(value, id){
        var id = 'designation_' + id;
        var inputfield = document.getElementById(id);
        inputfield.value = value;
        console.log(inputfield.value);
    }
    function fnChangeEmployeeHead(Row){
    
        var id = "head_signatorEmployeeId_" + Row;
        var value = $("#head_signatorDesignationId_" + Row).val();
        if (value == '') {
            return false;
        }

        let sqlite = "{{ (CommonService::getDBConnection() == 'sqlite') ? 1 : 0 }}";

        if(sqlite == 1){
            fnAjaxSelectBox(id,
                value,
                '{{base64_encode("hr_employees")}}',
                '{{base64_encode("designation_id")}}',
                '{{base64_encode("id,employee_no,emp_name,emp_code")}}',
                '{{url("/ajaxSelectBox")}}', null, 'isActiveOff'
            );
        }
        else{
            fnAjaxSelectBox(id,
                value,
                '{{base64_encode("hr_employees")}}',
                '{{base64_encode("designation_id")}}',
                '{{base64_encode("id,emp_name,emp_code")}}',
                '{{url("/ajaxSelectBox")}}', null, 'isActiveOff'
            );
        }
    }

    function passportcheck() {
        var parentRow = document.getElementById('parentrow');
        var inputFields = parentRow.querySelectorAll('input[type="text"], select');
        var emptyFields = [];

        for (var i = 0; i < inputFields.length; i++) {
            var inputField = inputFields[i];

            if (inputField.classList.contains('clsSelect2') && inputField.classList.contains('emp')) {
                
                if (inputField.value === '') {
                
                }
            } 

            else if (!inputField.classList.contains('clsSelect2')) {
                if (inputField.value === '') {
                    emptyFields.push(inputField);
                }
            } else {
                var select2Value = $(inputField).val();
                if (select2Value === null || select2Value === '') {
                    emptyFields.push(inputField);
                }
            }
        }

        if (emptyFields.length > 0) {
            for (var j = 0; j < emptyFields.length; j++) {
                var msg = (document.getElementById(emptyFields[j].id).getAttribute('data-error'));
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                });
                break;
            }
            return false;
        }
        
        return true;
    }


    $('.academicTrashDiv').hide();


    $('.addrow').on('click', function() {
        var isPassportCheckValid = passportcheck();
        if (isPassportCheckValid){
            i++;
            var originalAcademicRow = $(this).closest('.new-row'); 
            var academicRow = $('#headDivID');
            var applicableFor = $('#select_2_1_0').val();
            var designation = $('#select_2_2_0').val();
            var employee = $('#select_2_3_0').val();
            // console.log($('#head_title_0').val()); 
            var newRow = ` <div class="row new-row" id="row_head_${i}">
                                    <div class="col-lg-2 form-group">
                                        <label class="input-title RequiredStar">Title Name</label>
                                        <div class="input-group">
                                            <input type="text"  class="form-control" name="head_title[]"
                                            data-error="Title name  required"
                                            placeholder="Enter Title" value = "${$('#head_title_0').val()}">
                                        </div>
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                    <div class="col-lg-2 form-group">
                                        <label class="input-title RequiredStar">Applicable For</label>
                                        
                                        <div class="input-group">
                                            <select class="form-control clsSelect2" onchange="applicationvaluemove(this.value, ${i})" 
                                                data-error="Please Select Branch" id="select_2_1_${i}">
                                                <option value="">Select</option>
                                                <option value="-1" ${applicableFor == -1 ? 'selected' : ''}>All</option>
                                                <option value="1" ${applicableFor == 1 ? 'selected' : ''}>Head Office</option>
                                                <option value="2" ${applicableFor == 2 ? 'selected' : ''}>Branch Office</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="head_signatorApplicable[]" id="application_${i}" value=" ${applicableFor}">
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                    <div class="col-lg-2 form-group">
                                        <label class="input-title RequiredStar">Select Designation</label>
                                        <div class="input-group">
                                            <select class="form-control clsSelect2" 
                                                data-error="Please Select Designation" id="select_2_2_${i}"
                                                onchange="fnChangeEmployeeHead(0); loaddesignation(this.value, ${i})"
                                                >
                                                <option value="" >Select</option>
                                                <option value="-2" ${designation == -2 ? 'selected' : ''}>N/A</option>
                                                <option value="-1" ${designation == -1 ? 'selected' : ''}>Logged In User</option>
                                                @foreach($EmpDesignations as $des)
                                                <option value="{{ $des->id }}" ${designation == "{{$des->id}}" ? 'selected' : ''}>{{ $des->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="head_signatorDesignationId[]" id="designation_${i}" value="${designation}">
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                    <div class="col-lg-2 form-group">
                                        <label class="input-title">Select Employee</label>
                                        <div class="input-group">
                                            <select class="form-control clsSelect2" id="select_2_3_${i}"
                                                data-error="Please Select Employee" onchange="loademployee(this.value, ${i})">
                                                <option value="" ${employee == "{{$des->id}}" ? 'selected' : ''}>Select</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="head_signatorEmployeeId[]" id="employee_${i}" value="${employee}">
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                    
                                    <div class="col-lg-2 form-group">
                                        <label class="input-title RequiredStar">Order </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control textNumber" name="head_positionOrder[]"
                                                data-error="Order is required" value= "${$('#order_id_0').val()}"
                                                placeholder="Enter Order">
                                        </div>
                                        
                                        <div class="help-block with-errors is-invalid"></div>
                                    </div>
                                    
                                    <div class="col-lg-2 col-md-2 form-group academicTrashDiv" style="margin-top:21px"> 
                                        <div class="col-lg-12 text-right"> 
                                            <a href="javascript:void(0)" style="" class="btn btn-danger academicTrash"> 
                                                <i class="icon fa fa-trash "></i>Remove 
                                            </a> 
                                        </div>
                                    </div>
                                </div>` ;

        
            academicRow.append(newRow);
            $('.clsSelect2').select2();
            // academicRow.find('.academicTrashDiv').show();
            // $('.list-rows').append(academicRow);
        
            var parentRow = $('#parentrow');
            parentRow.find('.form-control').val('');
            parentRow.find('.clsSelect2').val(null).trigger('change');
    
        }

    });

    $(document).on('click', '.academicTrash', function() {
        $(this).closest('.new-row').remove(); 
    });

    $('#send_btn').click(function(){
    event.preventDefault();
    var parentRow = document.getElementById('parentrow');

        if (!parentRow) {
            return false;
        }

        var inputFields = parentRow.querySelectorAll('.form-control');
        var select2Fields = parentRow.querySelectorAll('.clsSelect2');
        var isInputEmpty = true;
        var isSelect2Empty = true;

        for (var i = 0; i < inputFields.length; i++) {
            if (inputFields[i].value.trim() !== '') {
                isInputEmpty = false;
                break;
            }
        }

        for (var i = 0; i < select2Fields.length; i++) {
            var select2Value = $(select2Fields[i]).val(); // Using jQuery to get Select2 value

            if (select2Value !== null && select2Value.trim() !== '') {
                isSelect2Empty = false;
                break;
            }
        }

        if( isInputEmpty && isSelect2Empty){
            $('#submit_form').unbind('submit').submit();
        }
        else{
            swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Clear The Parent Input Field',
                });
        }
    });

    function btnAddNewRowhead() {


    
            var total_row = $('#headtotalRow').val();

            total_row ++;


            

            var html = '';

            html += ' <div class="row" id="row_head_'+total_row+'">';

            html += '<div class="col-lg-2 form-group"><label class="input-title RequiredStar">Title Name</label><div class="input-group">';
            html += '<input type="text" class="form-control round" id="head_title_'+total_row+'" name="head_title[]" required data-error="Title name  required" placeholder="Enter Title">';
            html += '</div><div class="help-block with-errors is-invalid"></div></div> <div class="col-lg-2 form-group"> <label class="input-title RequiredStar">Select Designation</label> <div class="input-group">';
            html += '<select class="form-control clsSelect2" name="head_signatorDesignationId[]" required id="head_signatorDesignationId_'+total_row+'"  data-error="Please Select Designation" onchange="fnChangeEmployeeHead('+total_row+');">';
            html += '<option value="">Select</option> @foreach($EmpDesignations as $des) <option value="{{ $des->id }}"}}>{{ $des->name }}</option>  @endforeach </select>';
            html += '</div><div class="help-block with-errors is-invalid"></div> </div><div class="col-lg-2 form-group"> <label class="input-title ">Select Employee</label>';
                            

            html += '<div class="input-group"><select class="form-control clsSelect2" name="head_signatorEmployeeId[]" id="head_signatorEmployeeId_'+total_row+'" data-error="Please Select Employee">';
            html += ' <option value="">Select</option> </select></div> <div class="help-block with-errors is-invalid"></div></div>';
                        
                        
            
            html += '<div class="col-lg-2 form-group"><label class="input-title RequiredStar">Order </label><div class="input-group">';
            html += '<input type="text" class="form-control round" id="head_positionOrder_'+total_row+'" name="head_positionOrder[]" required data-error="Order is required" placeholder="Enter Order">';               
            html += '</div><div class="help-block with-errors is-invalid"></div> </div>';               
                            
            html += '<div class="col-lg-2 form-group"><label class="input-title"></label> <div class="input-group">';
                html += '<a href="javascript:void(0)" onclick="btnRemoveRowHead('+total_row+');"  class="btn btn-danger btn-round">Remove</a></div></div></div>';

                                    
            $('#headtotalRow').val(total_row);
            $('#headDivID').append(html);

            
        
    }


    function btnRemoveRowBranch(RemoveID) {

        $('#row_branch_'+RemoveID).remove();
    }

    function btnRemoveRowHead(RemoveID) {
        //  console.log('dddddd');
        $('#row_head_'+RemoveID).remove();
    }
        
</script>

@endsection
