@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

<form enctype="multipart/form-data" method="post" class="form-horizontal" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row ">
        <div class="col-lg-9 offset-lg-3">
          {!! HTML::forCompanyFeild($AreaData->company_id) !!}
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="area_name">Area Name</label>

                <div class="col-lg-5">
                    <div class="form-group">
                        <div class="">
                            <input type="hidden" id="area_id" value="{{$AreaData->id}}">

                            <input type="text" class="form-control round" id="area_name" name="area_name" value="{{$AreaData->area_name}}" required="true" required data-error="Please select Area Name.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar" for="area_code">Area Code</label>
                <div class="col-lg-5">
                    <div class="form-group">
                        <div class="input-group ">
                            <input type="text" class="form-control round" id="area_code" name="area_code" value="{{$AreaData->area_code}}" required="true" required data-error="Please select Area Code.">
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
            </div>



            <div class="form-row align-items-center">
                <label class="col-lg-12 input-title">
                    Branchs
                </label>

                <div class="col-lg-12" id="BranchDiv">

                </div>
            </div>
          
            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'id' => 'updateButtonforArea',
                            'exClass' => 'float-right'
                        ]])
        </div>
    </div>
</form>

<script>
    function ajaxLoadBranch(){
        var CompanyID = $('#company_id').val();
        var AreaID = $('#area_id').val();

        $.ajax({
            method: "GET",
            url: "{{url('gnl/area/ajaxAreaList')}}",
            dataType: "text",
            data: {
                CompanyID: CompanyID,
                AreaID: AreaID
            },
            success: function (data) {
                if (data) {
                    $('#BranchDiv').html(data);
                }
            }
        });
    }

    $(document).ready(function () {
      ajaxLoadBranch();

        $('#company_id').change(function () {

            ajaxLoadBranch();
        });
        $('form').submit(function (event) {
            // event.preventDefault();
            $(this).find(':submit').attr('disabled', 'disabled');
            // $(this).submit();
        });
    });
</script>
@endsection
