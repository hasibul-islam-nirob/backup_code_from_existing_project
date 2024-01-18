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

            <div class="form-row form-group align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Payment System</label>
                <div class="col-lg-5 input-group">
                    <select class="form-control clsSelect2" name="payment_system_id" id="payment_system_id"
                        required style="width: 100%;">

                        @foreach($PaySystemList as $PData)
                            <option value="{{ $PData->id }}" >{{ $PData->payment_system_name }}</option>
                        @endforeach

                    </select>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Provider Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="provider_name" id="provider_name" class="form-control round"
                            placeholder="Enter Bank/Provider name" required data-error="Please enter Bank/Provider name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Account Holder Name</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="acc_holder_name" id="acc_holder_name" class="form-control round"
                            placeholder="Enter Account Holder name" required data-error="Please enter Account Holder name.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Account No.</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="account_no" id="account_no" class="form-control round"
                            placeholder="Enter name Account No." required data-error="Please enter Account No.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>
            
            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">Ledger</label>
                <div class="col-lg-5">
                    <div class="form-group">
                        <div>
                            {!! HTML::forLedgerSelectFeild(null, "ledger_id","ledger_id") !!}
                        </div>
                        <div class="help-block with-errors is-invalid"></div>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title RequiredStar">APPLICABLE FOR</label>
                <div class="col-lg-5">
                    <div class="form-group">
                        <select class="form-control clsSelect2" name="status" id="status"
                            required style="width: 100%;">

                            <option value="0" >For both (supplier/sales)</option>
                            <option value="1" >For Supplier Payment  </option>
                            <option value="2" >For Sales </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title ">Mobile</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" pattern="[01][0-9]{10}" class="form-control round textNumber"
                            name="mobile" id="mobile" placeholder="Mobile Number (01*********) (optional)"
                            data-error="Please enter mobile number (01*********)" minlength="11" maxlength="11"
                            onblur="fnCheckDuplicate(
                                '{{base64_encode('pos_bank_acc')}}',
                                this.name+'&&is_delete',
                                this.value+'&&0',
                                '{{url('/ajaxCheckDuplicate')}}',
                                this.id,
                                'errMsgPhone',
                                'mobile number');">
                    </div>
                    <div class="help-block with-errors is-invalid" id="errMsgPhone"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Email</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="email" class="form-control round" id="email" name="email"
                            placeholder="Enter Email (optional) " data-error="Please enter correct email.">
                    </div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">routing_no</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <input type="text" name="routing_no" id="routing_no" class="form-control round"
                            placeholder="Enter Routing No. (optional)"  data-error="Please enter Routing No.">
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            <div class="form-row align-items-center">
                <label class="col-lg-3 input-title">Address</label>
                <div class="col-lg-5 form-group">
                    <div class="input-group">
                        <textarea class="form-control " name="address" id="address" rows="2"
                        placeholder="Enter address"  data-error="Please enter address." ></textarea>
                    </div>
                    <div class="help-block with-errors is-invalid"></div>
                </div>
            </div>

            @include('elements.button.common_button', ['back' => true, 'submit' => [
                            'action' => 'save',
                            'title' => 'Save',
                            'exClass' => 'float-right'
                        ]])
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
                    window.location.href = "./";
                });
            }

        })
        .fail(function () {
            console.log("error");
        });
    });

</script>

@endsection