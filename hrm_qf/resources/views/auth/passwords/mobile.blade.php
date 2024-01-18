@extends('Layouts.erp_masterlogin')

@section('content')
<form method="POST" action="{{ route('reset_password_by_mobile') }}">
    {{ csrf_field() }}

    <div class="form-label-group">
        <input id="mobile" type="mobile" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" required autocomplete="mobile" autofocus placeholder="mobile" >
        <label for="mobile">Mobile</label>
        @error('mobile')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group clearfix">
        <div class="float-left">
            <a class="float-right orange-800" href="{{ route('password.request') }}">Reset Password by Email</a>
        </div>
    </div>

    <button type="submit" class="btn btn-block bg-teal-800 text-white btn-round">Reset Request 
    </button>
</form>
@endsection

