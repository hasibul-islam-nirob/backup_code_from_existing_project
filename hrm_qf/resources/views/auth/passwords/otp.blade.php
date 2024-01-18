@extends('Layouts.erp_masterlogin')

@section('content')
<form method="POST" action="{{ route('varify_otp') }}">
    {{ csrf_field() }}

    <div class="form-label-group">
        <input id="otp" type="otp" class="form-control @error('otp') is-invalid @enderror" name="otp" required autofocus placeholder="OTP" >
        <label for="otp">OTP</label>
        @error('otp')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>


    <button type="submit" class="btn btn-block bg-teal-800 text-white btn-round">Reset Request 
    </button>
</form>
@endsection

