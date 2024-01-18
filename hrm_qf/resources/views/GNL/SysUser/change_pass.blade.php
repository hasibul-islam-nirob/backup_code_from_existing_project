@extends('Layouts.erp_master')
@section('content')

<form method="post" enctype="multipart/form-data" data-toggle="validator" novalidate="true">
	@csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Password</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="password" class="form-control round" placeholder="Enter Password" name="password" id="password" required data-error="Please Password">
                        
                    </div>
                    @error('password')
                        <div class="help-block with-errors is-invalid">{{ $message }}</div>
                    @enderror
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="roleName">Confirm Password</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="password" class="form-control round" placeholder="Enter Password" name="conf_password" id="password" required data-error="Please Password">
                        
                    </div>
                    @error('password')
                        <div class="help-block with-errors is-invalid">{{ $message }}</div>
                    @enderror
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'validateButton2',
                            'exClass' => 'float-right'
                        ]])

        </div>
    </div>
</form>

<script type="text/javascript">
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });
</script>

@endsection