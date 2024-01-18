@extends('Layouts.erp_master')
@section('content')

<div class="row">
    <div class="col-lg-9 offset-lg-3">
        <div class="form-row align-items-center">
            <label class="col-lg-3 input-title RequiredStar" for="division_id">Division</label>
            <div class="col-lg-5 form-group">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="division_id" id="division_id" required data-error="Please select Division name." disabled>
                    <option value="{{$upazilaData->division_id}}" selected="selected">{{ (!empty($upazilaData->division['division_name']))? $upazilaData->division['division_name'] : '' }}</option>
                    </select>
                </div>
                <div class="help-block with-errors is-invalid"></div>
            </div>
        </div>

        <div class="form-row align-items-center">
            <label class="col-lg-3 input-title RequiredStar" for="district_id">District</label>
            <div class="col-lg-5 form-group">
                <div class="input-group">
                    <select class="form-control clsSelect2" name="district_id" id="district_id" required data-error="Please select District name." disabled>
                    <option value="{{$upazilaData->district_id}}" selected="selected">{{ (!empty($upazilaData->district['district_name']))? $upazilaData->district['district_name'] : '' }}</option>
                    </select>
                </div>
                <div class="help-block with-errors is-invalid"></div>
            </div>
        </div>

        <div class="form-row align-items-center">
            <label class="col-lg-3 input-title RequiredStar" for="upazila_name">Upazila</label>
            <div class="col-lg-5 form-group">
                <div class="input-group">
                    <input type="text" class="form-control round" value="{{$upazilaData->upazila_name}}" name="upazila_name" id="upazila_name" required data-error="Please enter Upszila name." readonly>
                </div>
                <div class="help-block with-errors is-invalid"></div>
            </div>
        </div>
        @include('elements.button.common_button', ['back' => true ])
    </div>
</div>

@endsection
