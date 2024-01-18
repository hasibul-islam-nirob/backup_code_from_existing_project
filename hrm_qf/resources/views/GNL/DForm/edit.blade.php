@extends('Layouts.erp_master')
@section('content')

<form enctype="multipart/form-data" method="post" class="form-horizontal" 
    data-toggle="validator" novalidate="true" autocomplete="off"> 
    @csrf                          
    <div class="row">
        <div class="col-lg-8 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Dynamic Type</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="type_id" id="type_id" required data-error="Select Type">
                            <option value="">Select Type</option>
                            @foreach ($typeData as $Row)
                            <option {{($queryData->type_id == $Row->id) ? "selected" : ""}} value="{{$Row->id}}">{{$Row->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Module</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="module_id" id="module_id" required data-error="Select Type">
                            <option value="">Select module</option>
                            @foreach ($module as $Row)
                            <option {{($queryData->module_id == $Row->id) ? "selected" : ""}} value="{{$Row->id}}">{{$Row->module_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Title</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="text" name="name" id="name" class="form-control round"
                        placeholder="Enter Title" required data-error="Please Enter Title" value="{{$queryData->name}}" />
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Input Type</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="input_type" id="input_type" required data-error="Select Type">
                            <option value="">Select Type</option>
                            <option {{($queryData->input_type == "text") ? "selected" : ""}} value="text">Text Field</option>
                            <option {{($queryData->input_type == "select") ? "selected" : ""}} value="select">Select Box</option>
                            <option {{($queryData->input_type == "checkbox") ? "selected" : ""}} value="checkbox">Checkbox</option>
                            <option {{($queryData->input_type == "radio") ? "selected" : ""}} value="radio">Radio Button</option>
                            <option {{($queryData->input_type == "textarea") ? "selected" : ""}} value="textarea">Textarea Field</option>
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Order</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <input type="number" name="order_by" id="order_by" class="form-control round"
                        placeholder="Enter Order" required data-error="Please Enter Order" value="{{$queryData->order_by}}" />
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title">Note</label>
                <div class="col-lg-5">
                    <div class="input-group">
                        <textarea type="text" name="note" id="note" class="form-control round"
                        placeholder="Enter Note" data-error="Please Enter Note">{{$queryData->note}}</textarea> 
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'submitBtn',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>

<script type="text/javascript">

    $(document).ready(function($) {

        /////////////////////////

        $('form').submit(function (event) {
            //disable Multiple Click
            event.preventDefault();
            // $(this).find(':submit').attr('disabled', 'disabled');
            $('#submitBtn').prop('disabled', true);

            $.ajax({
                url: "{{ url()->current() }}",
                type: 'POST',
                dataType: 'json',
                data: $('form').serialize(),
            })
            .done(function(response) {
                $('#submitBtn').prop('disabled', false);
                if (response['alert-type']=='error') {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = response['message'];
                    
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        content: wrapper,
                    });

                    // $('form').find(':submit').prop('disabled', false);
                }
                else{
                    $('form').trigger("reset");
                    swal({
                        icon: 'success',
                        title: 'Success...',
                        text: response['message'],
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                            window.location.href = "{{url('gnl/dynamic_form')}}"; 
                        });
                    }
                
                })
            .fail(function() {
                $('#submitBtn').prop('disabled', false);
                console.log("error");
            })
            .always(function() {
                $('#submitBtn').prop('disabled', false);
                console.log("complete");
            });
            
        });

    });
    
</script>

@endsection
