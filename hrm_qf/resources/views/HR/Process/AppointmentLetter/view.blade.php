@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>

<div class="row justify-content-center col-lg-10" style="padding-left: 80px;">
    <div class="row justify-content-center">
        @foreach ($emp_letter_data as $data){{$data}}<br />@endforeach
    </div>
    <div class="row">
        <div class="col-lg">
            <div class="form-group d-flex justify-content-center">
                <div class="example example-buttons">
                    <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round d-print-none">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
