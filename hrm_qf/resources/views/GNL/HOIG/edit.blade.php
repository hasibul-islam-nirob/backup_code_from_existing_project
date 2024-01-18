@extends('Layouts.erp_master')

@section('content')

<!-- Page -->
    <form enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar">Table Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                        <input type="text" class="form-control round" name="table_name" placeholder="Enter Table Name" value="{{$HOtable->table_name}}" required data-error="Please enter Table name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
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
@endsection