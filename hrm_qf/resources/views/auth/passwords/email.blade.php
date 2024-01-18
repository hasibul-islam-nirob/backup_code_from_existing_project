@extends('Layouts.erp_masterlogin')

@section('content')
<form method="POST" action="{{ route('password.email') }}">
    {{ csrf_field() }}

    <div class="form-label-group">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email" >
        <label for="email">Email</label>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group clearfix">
        <div class="float-left">
           
        <a class="btn btn-link" >
            <a class="float-right orange-800" href="{{ route('reset_password_by_mobile') }}">Reset Password by Mobile</a>
        </a>
          
        </div>
    </div>

    <button type="submit" class="btn btn-block bg-teal-800 text-white btn-round">Reset Request 
    </button>
</form>
@endsection

