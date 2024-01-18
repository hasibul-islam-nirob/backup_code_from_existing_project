@extends('Layouts.erp_master')
@section('content')

<form action="">
    
        <div class="form-row d-flex justify-content-center">
            <div class="col-lg-3 form-group">
                <div class="input-group">
                    <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                    <button onclick="addFieldFirstRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center" id="row-2"> </div>
        <div class="d-flex justify-content-center" id="row-3"> </div>
        <div class="d-flex justify-content-center" id="row-4"> </div>
        <div class="d-flex justify-content-center" id="row-5"> </div>
        <div class="d-flex justify-content-center" id="row-6"> </div>
        <div class="d-flex justify-content-center" id="row-7"> </div>
    
</form>

<script>
    function addFieldFirstRow() {
        $('#row-2').append(
        `<div class="col-lg-2 form-group">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldSecondRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
    function addFieldSecondRow() {
        $('#row-3').append(
        `<div class="col-lg-2 form-group align-items-center">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldThirdRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
    function addFieldThirdRow() {
        $('#row-4').append(
            `<div class="col-lg-2 form-group align-items-center">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldFourthRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
    function addFieldFourthRow() {
        $('#row-5').append(
            `<div class="col-lg-2 form-group align-items-center">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldFifthRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
    function addFieldFifthRow() {
        $('#row-6').append(
            `<div class="col-lg-2 form-group align-items-center">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldSixthRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
    function addFieldSixthRow() {
        $('#row-7').append(
            `<div class="col-lg-2 form-group align-items-center">
            <div class="input-group">
                <input type="text" class="form-control round" placeholder="" name="" style="width: 30%">
                <button onclick="addFieldSeventhRow();" type="button" class="btn btn-primary btn-round"><i class="fas fa-plus"></i></button>
            </div>
        </div>`
        );
    }
</script>
@endsection