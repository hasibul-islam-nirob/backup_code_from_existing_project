@extends('Layouts.erp_master')

@section('content')

@php
use App\Services\CommonService as Common;
@endphp

<!-- Page -->
    <form id="comForm" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf

        {{-- Section --}}
        <div class="panel panel-default" style="box-shadow:0 0px 0px rgb(0 0 0 / 0%);">
            <div class="panel-heading p-2 mb-4"><h4 class="text-uppercase">{{$module->module_name}} (MODULE)</h4></div>
            <input type="hidden" name="module_id" value="{{$module->id}}">
            <div class="panel-body">
                <!-- Configaration Configaration  -->
                <div class="row">

                    <div class="col-sm-6 border-right">
                        <div class="row justify-content-center" id="dynamic_div_left">
                        </div>
                    </div>
                    <div class="col-sm-6 border-left">
                        <div class="" id="dynamic_div_right">
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

          

        @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'nextTab',
                            'exClass' => 'float-right'
                        ]])
    </form>
<!-- End Page -->

<script type="text/javascript">

    $(document).ready(function(){
        $('.page-header-actions').hide();
        let formRows = {!! json_encode($dFormRows) !!};
        createDynamicform(formRows);
        // activaTab('Basic');
    });


    $('form#comForm').submit(function (e){
        e.preventDefault();

        
        var formData = new FormData(this);
        $.ajax({
            method: "post",
            url: "{{ url()->current() }}",
            datatype: "json",
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            success: function (response){
                if (response.alert_type === 'success'){

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
                else{
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = response['message'];
                    
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        content: wrapper,
                    });
                }
            }
        });
        

    });

  

    function createDynamicform(row){
        $.each(row, function (index, value){
            if (value.input_type === 'select'){
                makeSelectBox(value);
            }
            else if (value.input_type === 'radio'){
                makeRadioButton(value);
            }
            else if (value.input_type === 'text'){
                makeTextBox(value);
            }
            else if (value.input_type === 'textarea'){
                makeTextArea(value);
                
            }
            else if (value.input_type === 'checkbox'){
                makeCheckBox(value);
            }
        });
    }

    function makeTextBox(row){
        let html = "";
        let val = row.pre_value ? row.pre_value : "";

        html += '<div class="col-sm-7 form-group">';
        html += '<label class="input-title">'+ row.name +'</label>';

        
        html += '<div class="input-group">';
        html += '<input type="text" class="form-control round" value="'+ val +'" name="dynamic_form_'+ row.uid +'" placeholder="Enter '+ row.name +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        html += '</div>';
        // console.log(row);
        if(row.note != null){
           html += '<p style="color:black;"><strong style="color:red;">N.B : </strong> '+row.note+'</p>';
        }
        //html += '<div class="help-block with-errors is-invalid"></div>';
        html += '</div>';

        $('#dynamic_div_left').append(html);
    }

    function makeSelectBox(row){

        let html = "";
        let endFiscalHidden = "";

        html += '<div class="col-sm-7 form-group">';
        html += '<label class="input-title">'+ row.name +'</label>';
        
        html += '<div class="input-group">';

        if (row.name === "Fiscal year start"){
            html += '<select class="form-control clsSelect2" onchange="fiscalYearStartChanged(this.value)" name="dynamic_form_'+ row.uid +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        }
        else if (row.name === "Fiscal year end") {
            html += '<select disabled class="form-control clsSelect2" id="endFiscalYear" data-error="Select '+ row.name +'" style="width: 100%;" >';
            endFiscalHidden += '<input id="hiddenEndFiscalYear" value="'+ row.pre_value +'" type="hidden" name="dynamic_form_'+ row.uid +'">';
        }
        else {
            html += '<select class="form-control clsSelect2" name="dynamic_form_'+ row.uid +'" data-error="Select '+ row.name +'" style="width: 100%;" >';
        }
        //html += '<select class="form-control clsSelect2" name="dynamic_form_'+ row.id +'" required data-error="Select '+ row.name +'" style="width: 100%;" >';


        html += '<option value="">Select One</option>';

        $.each(row.form_values, function (index, value){
            if (value.value_field === row.pre_value){
                html += '<option selected value="'+ value.value_field +'">'+ value.name +'</option>';
            }
            else{
                html += '<option value="'+ value.value_field +'">'+ value.name +'</option>';
            }
        });

        html += '</select>';

        html+= endFiscalHidden;

        html += '</div>';
        if(row.note != null){
           html += '<p style="color:black;"><strong style="color:red;">N.B : </strong> '+row.note+'</p>';
        }
        //html += '<div class="help-block with-errors is-invalid"></div>';
        // html += '</div>';
        html += '</div>';

        $('#dynamic_div_left').append(html);
    }

    function makeRadioButton(row){
        let html = "";

        html += '<div class="form-row align-items-center">';
        html += '<label class="col-sm-3 offset-lg-1 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-5 form-group">';
        html += '<div class="input-group">';
        html += '<label class="switch">';

        if (row.pre_value == 1){
            html += '<input checked type="checkbox" name="dynamic_form_'+ row.uid +'" style="width: 100%;" value="1">';
        }
        else {
            html += '<input type="checkbox" class="checkbox_class" name="dynamic_form_'+ row.uid +'" style="width: 100%;">';
        }

        html += '<span class="slider round"></span>';
        html += '</label>';
      
        html += '</div>';
        if(row.note != null){
           html += '<p style="color:black;"><strong style="color:red;">N.B : </strong> '+row.note+'</p>';
        }
        html += '</div>';
        html += '</div>';

        $('#dynamic_div_right').append(html);
    }

    function makeCheckBox(row){
        let html = "";

        html += '<div class="row align-items-center">';
        html += '<label class="col-sm-2 offset-lg-1 input-title">'+ row.name +'</label>';
        html += '<div class="col-sm-9 form-group" style="padding-left:5%;">';
        html += '<div class="row">';



        $.each(row.form_values, function (index, value){

            let check = "";

            if (typeof row.pre_values !== 'undefined'){
                if(row.pre_values.includes(value.value_field)){
                    check = "checked"; 
                }
                
            }

            html += '<div class="col-sm-4">';
            html += '<div class="checkbox-custom checkbox-primary">';
            html += '<input type="checkbox" id="'+ value.name +'" '+check+' value="'+ value.value_field +'" class="checkboxs" name="dynamic_form_'+ row.uid +'[]">';
            html += '<label for="'+ value.name +'">'+ value.name +'</label>';
            html += '</div>';
            html += '</div>';
        });

        html += '</div>';
        if(row.note != null){
           html += '<p style="color:black;"><strong style="color:red;">N.B : </strong> '+row.note+'</p>';
        }
        html += '</div>';
        html += '</div>';

        $('#dynamic_div_right').append(html);
    }

    function makeTextArea(row){

    }
    function fiscalYearStartChanged(val){
        var startVal = val;
        if (startVal === '01-01') {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("31-12");

            $('#hiddenEndFiscalYear').val("31-12");

        }
        else {
            $('#endFiscalYear').find('option[value="31-12"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');

        }

        if (startVal === '01-07') {
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', true);
            $('#endFiscalYear').trigger('change');
            $('#endFiscalYearI').val("30-06");

            $('#hiddenEndFiscalYear').val("30-06");

        }
        else{
            $('#endFiscalYear').find('option[value="30-06"]').attr('selected', false);
            $('#endFiscalYear').trigger('change');

        }
        $('form').submit(function (event) {
            // event.preventDefault();
            $(this).find(':submit').attr('disabled', 'disabled');
            // $(this).submit();
        });
    }

 
   

    $(document).on('click', '.checkbox_class', function () {
        var value = 0;
        if(this.checked) // if changed state is "CHECKED"
        {
            value = 1;
        }else{
            value = 0;
        }
        $('.checkbox_class').val(value);
        
    });


</script>
@endsection
