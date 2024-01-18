
@extends('Layouts.erp_master')
@section('content')
<link href="{{ asset('assets/css-js/datetimepicker-master/jquery.datetimepicker.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/css-js/datetimepicker-master/build/jquery.datetimepicker.full.min.js') }}"></script>
<form id="attendance_rules_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
    @csrf

    <div class="row">

        <div class="col-sm-10 offset-sm-1">

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title">Start Time</label>
                    <div class="input-group">
                        <input id="start_time" class="timePicker" type="text" name="start_time" style="width: 100%;">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title">End Time</label>
                    <div class="input-group">
                        <input id="end_time" class="timePicker" type="text" name="end_time" style="width: 100%;">
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6 offset-sm-3 form-group">
                    <label class="input-title">Extendent Entry Time</label>
                    <div class="input-group">
                        <input id="ext_start_time" class="timePicker" type="text" name="ext_start_time" style="width: 100%;">
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="row align-items-center">
    
        <div class="col-sm-5 offset-sm-4">
            <div class="form-group d-flex justify-content-center">
                <div class="example example-buttons">
                    <a href="javascript:void(0)" onclick="goBack();" class="btn btn-default btn-round">Back</a>
                </div>
                <div class="example example-buttons">
                    <a href="javascript:void(0)" id="edit_updateBtn" class="btn btn-primary btn-round">Update</a>
                </div>
            </div>
        </div>
    
    </div>

</form>

<script>

    $(document).ready(function(){
        $('.timePicker').datetimepicker({
            datepicker:false,
            format:'H:i'
        });

        callApi("{{ url()->current() }}/get/api", 'post', new FormData($('#attendance_rules_form')[0]),
            function(response, textStatus, xhr) {
                $('#ext_start_time').val(response.ext_start_time);
                $('#end_time').val(response.end_time);
                $('#start_time').val(response.start_time);
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        );
    });

    $('#edit_updateBtn').click(function(event) {
        event.preventDefault();
        callApi("{{ url()->current() }}/update/api", 'post', new FormData($('#attendance_rules_form')[
                0]),
            function(response, textStatus, xhr) {
                showApiResponse(xhr.status, '');
            },
            function(response) {
                showApiResponse(response.status, JSON.parse(response.responseText).message);
            }
        )
    });
</script>

@endsection
