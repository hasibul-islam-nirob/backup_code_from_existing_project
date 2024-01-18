<style type="text/css">
     .datepicker-custom,.dateMonthPicker{
         z-index:9999 !important; 
     }
</style>
@extends('Layouts.erp_master')
@section('content')
<!-- Page -->
<form enctype="multipart/form-data" method="post" class="form-horizontal"
    data-toggle="validator" novalidate="true" autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-lg-9 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Holiday Title</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" class="form-control round" placeholder="Enter Govt Holiday Title"
                            name="gh_title" id="gh_title" value="{{$GovtHolidayData->gh_title}}" required
                            data-error="Please enter Govt Holiday Title.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Date</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control round dateMonthPicker" id="gh_date" name="gh_date"
                            value="{{$GovtHolidayData->gh_date}}" placeholder="DD-MM" required
                            data-error="Please enter Date">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center ">
                <label class="col-lg-3 input-title RequiredStar">Effective Start Date</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>

                        <?php
                        if(!empty($GovtHolidayData->efft_start_date)){
                          $date = new DateTime($GovtHolidayData->efft_start_date);
                        }
                        else{
                          $date = new DateTime();
                        }

                        $date = $date->format('d-m-Y');

                        ?>
                        <input type="text" class="form-control round datepicker-custom" id="efft_start_date"
                            name="efft_start_date" value="{{ $date }}" placeholder="DD-MM-YYYY" autocomplete="off"
                            required data-error="Please enter Effective Date">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            <div class="form-row align-items-center ">
                <label class="col-lg-3 input-title">Effective End Date</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <span class="input-group-text ">
                                <i class="icon wb-calendar round" aria-hidden="true"></i>
                            </span>
                        </div>
                        <?php
                        if(!empty($GovtHolidayData->efft_end_date)){
                          $date = new DateTime($GovtHolidayData->efft_end_date);
                        }
                        else{
                          $date = new DateTime();
                        }

                        $date = $date->format('d-m-Y');

                        ?>
                        <input type="text" class="form-control round datepicker-custom" id="efft_end_date" value="{{$date}}"
                            name="efft_end_date" placeholder="DD-MM-YYYY">
                    </div>
                </div>
            </div>
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Description</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group ">
                        <textarea class="form-control round" id="gh_description" name="gh_description" rows="2"
                            placeholder="Enter Description">{{$GovtHolidayData->gh_description}}</textarea>
                    </div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'update',
                            'title' => 'update',
                            'exClass' => 'float-right'
                        ]])

        </div>
    </div>
</form>
<!-- End Page -->

<script type="text/javascript">
$('form').submit(function(event) {
    $(this).find(':submit').attr('disabled', 'disabled');
});
</script>
@endsection
