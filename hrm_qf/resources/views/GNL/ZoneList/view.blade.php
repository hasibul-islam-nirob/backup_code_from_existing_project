@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
          {!! HTML::forCompanyFeild($ZoneData->company_id,'disabled') !!}
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="zone_name">Zone Name</label>
                <div class="col-lg-5 form-group">
                    <div class="">
                        <input type="hidden" id="zone_id" value="{{$ZoneData->id}}">
                        <input type="text" class="form-control round" value="{{$ZoneData->zone_name}}" name="zone_name" id="zone_name" required data-error="Please enter Zone name."readonly>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="zone_code">Zone Code</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" value="{{$ZoneData->zone_code}}" name="zone_code" id="zone_code" required data-error="Please enter Zone Code." readonly>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <!-- Html View Load  -->
                    <!-- {!! HTML::forCompanyFeild($ZoneData->company_id) !!} -->
                    <div class="form-row align-items-center">
                        <label class="col-lg-3 input-title RequiredStar" for="company_id">COMPANY</label>
                        <div class="col-lg-5 form-group">
                            <div class="input-group">
                                <select class="form-control round browser-default clsCompany clsSelect2" name="company_id" id="company_id" required data-error="Please select Company name."disabled>
                                    <option value="">Select Company</option>
                                    @foreach ($Companies as $Row)
                                    <option value="{{$Row->id}}"
                                            {{ ($ZoneData->company_id == $Row->id) ? 'selected="selected"' : ''}} >
                                        {{$Row->comp_name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="help-block with-errors is-invalid"></div>
                        </div>
                    </div>

            <div class="form-row align-items-center">
                <label class="col-lg-12 input-title">
                    Areas
                </label>

                <div class="col-lg-12" id="AreaDiv">

                </div>
            </div>

            @include('elements.button.common_button', [
                        'back' => true 
                    ])
        </div>
    </div>
</form>

<script>

    $(document).ready(function() {

        var CompanyID = $('#company_id').val();
        var ZoneID = $('#zone_id').val();

        $.ajax({
            method: "GET",
            url: "{{url('gnl/zone/ajaxZoneList')}}",
            dataType: "text",
            data: {
                CompanyID: CompanyID,
                ZoneID: ZoneID
            },
            success: function(data) {
                if (data) {
                    $('#AreaDiv').html(data);
                }
            }
        });

    });
</script>
<!-- End Page -->


@endsection
