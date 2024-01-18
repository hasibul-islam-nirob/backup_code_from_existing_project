@extends('Layouts.erp_master')
@section('content')
<?php
use App\Services\CommonService as Common;
use App\Services\HtmlService as HTML;

$PaySystemList = Common::ViewTableOrder('gnl_payment_system',
    [['is_delete', 0], ['is_active', 1]],
    ['id', 'payment_system_name'],
    ['order_by', 'ASC']);
?>

<form method="post" data-toggle="validator" novalidate="true">
    @csrf
   

    <div class="row">
        <div class="col-lg-9 offset-lg-3">

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Payment System</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group"> 
                        <input type="text" name="payment_system_name" id="payment_system_name" class="form-control round" value="{{$TargetData->payment_system_name}}" 
                            placeholder="Enter Payment System name" required data-error="Please enter Payment System name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Short Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="short_name" id="short_name" class="form-control round" value="{{$TargetData->short_name}}" 
                            placeholder="Enter short name" required data-error="Please enter short name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Order</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="order_by" id="order_by" class="form-control round" value="{{$TargetData->order_by}}" 
                            placeholder="Enter Order" required data-error="Please Order.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>


            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Applicable for</label>
                <div class="col-lg-5 input-group">
                    <select class="form-control clsSelect2" name="status" id="status"
                        required style="width: 100%;">
                        <option value="0" {{($TargetData->status==0)? 'selected':''}} >For both (supplier/sales)</option>
                        <option value="1"  {{($TargetData->status==1)? 'selected':''}}>For Supplier Payment  </option>
                        <option value="2"  {{($TargetData->status==2)? 'selected':''}} >For Sales </option>

                    

                    </select>
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