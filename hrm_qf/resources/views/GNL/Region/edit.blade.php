@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <!-- Html View Load  -->
            {!! HTML::forCompanyFeild($RegionData->company_id) !!}

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="region_name">Region Name</label>
                <div class="col-lg-5 form-group">
                    <div class="">
                        <input type="hidden" id="region_id" value="{{$RegionData->id}}">
                        <input type="text" class="form-control round" value="{{$RegionData->region_name}}"
                            name="region_name" id="region_name" required data-error="Please enter Region name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="region_code">Region Code</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" value="{{$RegionData->region_code}}"
                            name="region_code" id="region_code" required data-error="Please enter Region Code.">
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

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'submitButtonForRegion',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>


<script>
function ajaxLoadarea() {
    var CompanyID = $('#company_id').val();
    var RegionId = $('#region_id').val();

    $.ajax({
        method: "GET",
        url: "{{url('gnl/region/ajaxAreaLoad')}}",
        dataType: "text",
        data: {
            CompanyID: CompanyID,
            RegionId: RegionId
        },
        success: function(data) {
            if (data) {
                $('#AreaDiv').html(data);
            }
        }
    });
}

$(document).ready(function() {
    ajaxLoadarea();

    $('#company_id').change(function() {

        ajaxLoadarea();
    });
    $('form').submit(function(event) {
        // event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        // $(this).submit();
    });
});
</script>

@endsection