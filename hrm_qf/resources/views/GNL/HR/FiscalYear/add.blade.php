@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">

                <!-- Html View Load  -->
               {!! HTML::forCompanyFeild() !!}

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="fy_name">Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Fiscal Year Name" name="fy_name" id="fy_name" required data-error="Please enter Fiscal year name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="fy_start_date">Start Date</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <div class="input-group-prepend ">
                                <span class="input-group-text ">
                                    <i class="icon wb-calendar round" aria-hidden="true"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control round datepicker-custom" id="fy_start_date" name="fy_start_date" placeholder="DD-MM-YYYY">
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
        // event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        // $(this).submit();
    });
</script>
@endsection
