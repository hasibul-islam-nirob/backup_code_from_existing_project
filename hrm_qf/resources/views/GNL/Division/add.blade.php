@extends('Layouts.erp_master')
@section('content')

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row ">
        <div class="col-lg-9 offset-lg-3">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="division_name">Division Name</label>

                <div class="col-lg-5">
                    <div class="form-group">
                        <div class="">
                            <input type="text" class="form-control round" id="division_name" name="division_name" placeholder="Enter Division Name" required="true" required data-error="Please enter division Name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title" for="short_name">Short name</label>
                <div class="col-lg-5">
                    <div class="form-group">
                        <div class="input-group ">
                            <input type="text" class="form-control round" id="short_name" name="short_name" placeholder="Enter Short Name"  >
                        </div>

                    </div>
                </div>
            </div>
          
            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'submitButtonforArea',
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
