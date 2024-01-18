@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

<!-- Page -->
    <form enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">

                <!-- Html View Load  -->
                {!! HTML::forCompanyFeild($roomData->company_id) !!}

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Department</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="dept_id" id="dept_id"
                                required data-error="Please select Department">
                                <option value="">Select Department</option>
                                @foreach($departmentData as $dep)
                                    <option value="{{ $dep->id }}" @if($dep->id == $roomData->dept_id) selected 
                                        @endif>{{ $dep->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="room_name" id="room_name" 
                            placeholder="Enter Name" required data-error="Please enter name."
                            value="{{ $roomData->room_name }}">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Code</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="room_code" id="room_code" 
                            placeholder="Enter Code" value="{{ $roomData->room_code }}">
                        </div>
                    </div>
                </div>
                @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'btnSubmitCompany',
                            'exClass' => 'float-right'
                        ]])
            </div>
        </div>
    </form>
<!-- End Page -->

<script type="text/javascript">
    $('form').submit(function (event) {
        $(this).find(':submit').attr('disabled', 'disabled');
    });
</script>
@endsection
