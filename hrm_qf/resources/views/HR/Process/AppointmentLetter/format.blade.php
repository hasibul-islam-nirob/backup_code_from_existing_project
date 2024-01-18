@extends('Layouts.erp_master')
@section('content')
<style>
    .page-header-actions{
        display: none;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-markdown/bootstrap-markdown.css') }}">
<div class="row">
    <div class="col-lg">

      <div class="card card-inverse bg-info">
        <div class="card-block">
          <h4 class="card-title">Appointment Letter Format</h4>
          <p class="card-text">use {} notations to replace associate values.<br>
            {employee_name} :- It will replace Employee Name<br>
            {designation} :- It will replace Employee Designation<br>
            {joining_date} :- It will replace Employee Joining Date<br><br>
            Your appointment letter may Like this:-<br><br>
            Dear {employee_name},<br>
            We are pleased to inform you that you passed your interview and we are hereby offering you employment on contract basis for the position of a {designation} at <strong>Your Company Name</strong>. The terms and conditions of your employment are as follows...................................</p>
        </div>
      </div>
    </div>
</div>
  <div class="row">
    <div class="col-lg">
      <!-- Panel Standard Editor -->
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title text-center">Appointment Letter</h3>
        </div>
        <div class="panel-body">
          <div class="example">
            <form enctype="multipart/form-data" method="post" class="form-horizontal">
                <textarea name="content" data-provide="markdown" data-iconlibrary="fa" rows="11">@php
                      if ($appontment_letter_data->content == '')
                      {
                        echo 'Text Here...';
                      } else {
                        echo $appontment_letter_data->content;
                      }
                  @endphp
                </textarea>
                <div class="row">
                    <div class="col-lg">
                        <div class="form-group d-flex justify-content-center">
                            <div class="example example-buttons">
                                <a href="javascript:void(0)" onclick="goBack();"
                                    class="btn btn-default btn-round d-print-none">Back</a>
                                <button type="submit" class="btn btn-primary btn-round">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
      <!-- End Panel Standard Editor -->
    </div>
  </div>

<script src="{{asset('assets/vendor/summernote/summernote.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap-markdown/bootstrap-markdown.js')}}"></script>
<script src="{{asset('assets/vendor/marked/marked.js')}}"></script>
<script src="{{asset('assets/vendor/to-markdown/to-markdown.js')}}"></script>
<script>
    $('form').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            url: "{{ url()->current() }}",
            data: $('form').serialize(),
            dataType: "json",
        })
        .done(function (response) {
        if (response['alert-type'] == 'error') {
            swal({
                icon: 'error',
                title: 'Oops..',
                text: response['message'],
            });
            $('form').find(':submit').prop('disabled', false);
        } else {
            // $('form').trigger("reset");
            swal({
                icon: 'success',
                title: 'Success...',
                text: response['message'],
            }).then(function () {
                window.location.reload();
            });
        }

    })
    .fail(function () {
        console.log("error");
    });
});
</script>
@endsection
