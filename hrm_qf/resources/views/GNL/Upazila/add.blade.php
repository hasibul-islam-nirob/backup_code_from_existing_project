@extends('Layouts.erp_master')
@section('content')

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="division_id">Devision</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2"
                        name="division_id" id="division_id"
                        required data-error="Please select Division name."
                        onchange="fnAjaxSelectBox(
                                            'district_id',
                                            this.value,
                                '{{base64_encode('gnl_districts')}}',
                                '{{base64_encode('division_id')}}',
                                '{{base64_encode('id,district_name')}}',
                                '{{url('/ajaxSelectBox')}}'
                                        );">
                            <option value="">Select Division</option>
                            @foreach ($divData as $row)
                            <option value="{{$row->id}}">{{$row->division_name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="district_id">District</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <select class="form-control clsSelect2" name="district_id" id="district_id" 
                        required data-error="Please select District name.">
                            <option value="">Select District</option>
                        </select>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="upazila_name">Upazila</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Upzilla Name" name="upazila_name" id="upazila_name" required data-error="Please enter Upazila name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            
            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'id' => 'submitButtonforUpazila',
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
