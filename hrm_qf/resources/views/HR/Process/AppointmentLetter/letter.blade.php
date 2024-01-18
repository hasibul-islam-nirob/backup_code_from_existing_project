@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>
<div class="col-lg">
<div class="row">
    <div class="col-lg">
        <div class="form-group text-right">
            <div class="example example-buttons">
                <a href="javascript:void(0)" onclick="saveData();window.print();" class="btn btn-primary btn-round clsPrint d-print-none">Print</a>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center col-lg-10">
    <div class="row justify-content-center">
            <p id="appointment_letter">@foreach ($letter_data as $data){{$data}}<br />@endforeach
                <input type="text" id="emp_id" value="{{$id}}" hidden>
            </p>
    </div>
    <div class="row">
        <div class="col-lg">
            <div class="form-group d-flex justify-content-center">
                <div class="example example-buttons">
                    <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round d-print-none">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    function saveData() {
        var letter = $('#appointment_letter').text();
        var emp_id = $('#emp_id').val();
        $.ajax({
            type: "post",
            url: "{{ route('appointmentLetters') }}",
            data: {
                letter : letter,
                emp_id : emp_id,
            },
            dataType: "json",
        })
        .done(function (response) {
                if (response['alert-type'] == 'error') {
                    toastr.error("Appointment letter has not been saved!");
                } else {
                    toastr.success(response['message']);
                    console.log(response['message']);
                }

            });
    }
</script>
@endsection
