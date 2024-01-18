@extends('Layouts.erp_master')
@section('content')

<form enctype="multipart/form-data" method="post" class="form-horizontal" 
    data-toggle="validator" novalidate="true" autocomplete="off"> 
    @csrf                          
    <div class="row">
        <div class="col-lg-8 offset-lg-3">

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
                            window.location.href = "{{url('gnl/dynamic_type')}}"; 
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
