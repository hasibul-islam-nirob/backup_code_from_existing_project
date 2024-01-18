@extends('Layouts.erp_masterlogin')

@section('content')
<form method="POST" action="{{ route('password.update_mobile') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-label-group">
        <input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" name="contact_no" value="{{ $mobile ?? old('mobile') }}" required autofocus>
        <label for="mobile">Mobile</label>
        @error('mobile')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
   
    

    <div class="form-label-group">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            <label for="password">Password</label>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
    </div>

    <div class="form-label-group">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            <label for="password-confirm" >Confirm Password</label>
    </div>

    <button type="submit" class="btn btn-block bg-teal-800 text-white btn-round">
        Reset Password
    </button>

   
</form>
@endsection
