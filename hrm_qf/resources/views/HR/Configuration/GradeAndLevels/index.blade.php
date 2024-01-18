@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>

<form enctype="multipart/form-data" method="post" id="gradeclassform"data-toggle="validator" novalidate="true">
    @csrf
    <div class="row row-lg">
        {{-- garde --}}
        <div class="col-lg-6">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title text-right">Grade</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round textNumber" name="grade" id="grade" placeholder="Enter Grade" value="{{$grades}}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
        {{-- level --}}
        <div class="col-lg-6">
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title text-right">Level</label>
                <div class="col-lg-8 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round textNumber" name="level" id="level" placeholder="Enter Level" value="{{$levels}}">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg">
            <div class="form-group d-flex justify-content-center">
                <div class="example example-buttons">
                    <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                    <button type="submit" class="btn btn-primary btn-round" id="btnSubmitCompany">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>

    $('#gradeclassform').submit(function (event) {
    event.preventDefault();
        $(this).find(':submit').attr('disabled', 'disabled');
        $.ajax({
                url: "{{ url()->current() }}",
                type: 'POST',
                dataType: 'json',
                // contentType: false,
                data: $('form').serialize(),
                // processData: false,
                success: function (response) {
                    console.log(response);
                    swal({
                        icon: 'success',
                        title: 'Success...',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = "{{ url()->current() }}";
                    });
                },
                error: function () {
                    alert('error!');
                }
            })
});
</script>
@endsection
