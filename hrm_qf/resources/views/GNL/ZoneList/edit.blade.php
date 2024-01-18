@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

    <form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-lg-9 offset-lg-3">
              {!! HTML::forCompanyFeild($ZoneData->company_id) !!}
                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="zone_name">Zone Name</label>
                    <div class="col-lg-5 form-group">
                        <div class="">
                            <input type="hidden" id="zone_id" value="{{$ZoneData->id}}">
                            <input type="text" class="form-control round" value="{{$ZoneData->zone_name}}" name="zone_name" id="zone_name" required data-error="Please enter Zone name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-3 input-title RequiredStar" for="zone_code">Zone Code</label>
                    <div class="col-lg-5 form-group">
                        <div class="input-group">
                            <input type="text" class="form-control round" value="{{$ZoneData->zone_code}}" name="zone_code" id="zone_code" required data-error="Please enter Zone Code.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>

                <div class="form-row align-items-center">
                    <label class="col-lg-12 input-title">
                        Regions
                    </label>

                    <div class="col-lg-12" id="RegionDiv">

                    </div>
                </div>
                @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'updateButtonForZone',
                            'exClass' => 'float-right'
                        ]])
            </div>
        </div>
    </form>

<script>
function ajaxLoadarea(){
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
    success: function (data) {
        if (data) {
            $('#RegionDiv').html(data);
        }
    }
});
}

$(document).ready(function () {
  ajaxLoadarea();

    $('#company_id').change(function () {

        ajaxLoadarea();
    });
    $('form').submit(function (event) {
        // event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        // $(this).submit();
    });
});
  
</script>
<!-- End Page -->


@endsection
