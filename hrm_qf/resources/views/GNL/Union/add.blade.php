@extends('Layouts.erp_master')
@section('content')
    <form method="POST" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="division_id">Division</label>
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
                                @foreach ($divisionData as $row)
                                <option value="{{$row->id}}">{{$row->division_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="district_id">DISTRICT</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <select class="form-control clsSelect2"
                            name="district_id" id="district_id"
                            required data-error="Please select District name."
                            onchange="fnAjaxSelectBox(
                                                'upazila_id',
                                                this.value,
                                    '{{base64_encode('gnl_upazilas')}}',
                                    '{{base64_encode('district_id')}}',
                                    '{{base64_encode('id,upazila_name')}}',
                                    '{{url('/ajaxSelectBox')}}'
                                            );">
                                <option value="">Select District</option>
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="upazila_id">Upazila</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <select class="form-control clsSelect2" name="upazila_id" id="upazila_id" required data-error="Please select Upazila name.">
                                <option value="">Select Upazila</option>
                            </select>
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="union_name">Union</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" placeholder="Enter Union Name" name="union_name" id="union_name" required data-error="Please enter Union name.">
                        </div>
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
    // event.preventDefault();
    $(this).find(':submit').attr('disabled', 'disabled');
    // $(this).submit();
});
</script>
<!-- End Page -->
@endsection
