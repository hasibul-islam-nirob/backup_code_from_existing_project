@extends('Layouts.erp_master')
@section('content')

<?php 
use App\Services\HtmlService as HTML;
?>

<form method="post" data-toggle="validator" novalidate="true">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">
            <!-- Html View Load  -->
            {!! HTML::forCompanyFeild($TargetData->company_id, null, true) !!}

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="name" id="name" value="{{ $TargetData->name }}"
                            class="form-control round" placeholder="Enter name" required
                            data-error="Please enter name." disabled>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            @include('elements.button.common_button', ['back' => true ])
        </div>
    </div>
</form>

<!-- End Page -->
<script type="text/javascript">
  $('form').submit(function (event) {
            event.preventDefault();
            // $(this).find(':submit').attr('disabled', 'disabled');

            $.ajax({
                    url: "{{ url()->current() }}",
                    type: 'POST',
                    dataType: 'json',
                    contentType: false,
                    data: new FormData(this),
                    processData: false,
                })
                .done(function (response) {
                    if (response['alert-type'] == 'error') {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            text: response['message'],
                        });
                        $('form').find(':submit').prop('disabled', false);
                    } else {
                        $('form').trigger("reset");
                        swal({
                            icon: 'success',
                            title: 'Success...',
                            text: response['message'],
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.href = "./../";
                        });
                    }

                })
                .fail(function () {
                    console.log("error");
                });
        });
</script>

@endsection