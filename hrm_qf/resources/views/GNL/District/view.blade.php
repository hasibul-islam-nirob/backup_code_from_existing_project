@extends('Layouts.erp_master')
@section('content')

<div class="row">
    <div class="col-lg-9 offset-lg-3">
        <div class="form-row align-items-center">
            <label class="col-lg-3 input-title RequiredStar" for="division_id">Division</label>
            <div class="col-lg-5 form-group">
                <div class="input-group">
                    <select class="form-control clsSelect2" disabled>
                        <option value="{{$DisData->division_id}}" selected="selected">{{(!empty($DisData->division['division_name']))? $DisData->division['division_name'] : '' }}</option>
                    </select>
                </div>
                <div class="help-block with-errors is-invalid"></div>
            </div>
        </div>

        <div class="form-row align-items-center">
            <label class="col-lg-3 input-title RequiredStar">District</label>
            <div class="col-lg-5 form-group">
                <div class="input-group">
                    <input type="text" class="form-control round" name="district_name" id="district_name" value="{{$DisData->district_name}}" required data-error="Please enter District name." readonly>
                </div>
                <div class="help-block with-errors is-invalid"></div>
            </div>
        </div>

        @include('elements.button.common_button', [
                        'back' => true
                    ])
    </div>
</div>


@endsection
