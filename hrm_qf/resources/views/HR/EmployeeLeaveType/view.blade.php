@extends('Layouts.erp_master')
@section('content')
    <form id="leave_type_form" enctype="multipart/form-data" method="post" data-toggle="validator" novalidate="true">
        @csrf
        <div class="row">
            <div class="col-sm-12 offset-sm-3">

                <div class="form-row form-group align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Leave Name</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input readonly value="{{ $data['leave_name'] }}" id="leave_name" name="leave_name" type="text" class="form-control round" style="width: 100%">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-sm-3 input-title RequiredStar">Leave Name</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input readonly value="{{ $data['short_name'] }}" id="short_name" name="short_name" type="text" class="form-control round" style="width: 100%">
                        </div>
                    </div>
                </div>

                <div class="form-row form-group align-items-center">
                    <label class="col-lg-3 input-title">Leave Type</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <div class="radio-custom radio-primary">
                                <input readonly {{ ($data['leave_type'] == 'Pay') ? 'checked' : '' }} type="radio" name="leave_type" value="Paid">
                                <label for="g1">Pay &nbsp &nbsp </label>
                            </div>
                            <div class="radio-custom radio-primary">
                                <input readonly {{ ($data['leave_type'] == 'Non Pay') ? 'checked' : '' }} type="radio" name="leave_type" value="Unpaid">
                                <label for="g2">Non Pay &nbsp &nbsp </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </form>
@endsection

