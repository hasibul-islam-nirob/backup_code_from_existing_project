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
                {!! HTML::forCompanyFeild($DepartmentData->company_id,'disabled') !!}

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="dept_name"
                             placeholder="Enter Name" required data-error="Please enter name."
                             value="{{$DepartmentData->dept_name}}" readonly >
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title">Short Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" name="short_name" 
                            placeholder="Enter Name" value="{{$DepartmentData->short_name}}" readonly>
                        </div>
                    </div>
                </div>
                @include('elements.button.common_button', [
                        'back' => true
                    ])

            </div>
        </div>
    </form>
<!-- End Page -->


@endsection