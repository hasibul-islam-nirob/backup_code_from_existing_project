@extends('Layouts.erp_masterlogin')

@section('content')

@php

if(isset($_COOKIE['login_username']) && isset($_COOKIE['login_password'])){

    $login_user = $_COOKIE['login_username'];
    $login_password = base64_decode($_COOKIE['login_password']);

    $is_remember = "checked='checked'";

}else {
    $login_user = '';
    $login_password = '';
    $is_remember = "";

}

@endphp
<form action="{{url('post-login')}}" method="POST" id="logForm" class="m-0">
    {{ csrf_field() }}
    <div class="form-label-group">
        <input type="text" name="username" id="inputUsername" value="{{$login_user}}" class="form-control" placeholder="Username" >
        <label for="inputUsername">Username</label>
        @if ($errors->has('username'))
        <span class="error">{{ $errors->first('username') }}</span>
        @endif
    </div>
    <div class="form-label-group">
        <input type="password" name="password" id="inputPassword" value="{{$login_password}}" class="form-control" placeholder="Password">
        <label for="inputPassword">Password</label>
        @if ($errors->has('password'))
        <span class="error">{{ $errors->first('password') }}</span>
        @endif
      </div>
    <div class="form-group clearfix">
        <div class="checkbox-custom checkbox-inline checkbox-primary float-left">
            <input type="checkbox" id="remember" name="remember" {{$is_remember}}>
            <label for="remember">Remember me</label>
        </div>
        @if (Route::has('password.request'))
        <a class="btn btn-link" >
            {{-- {{ __('Forgot Your Password?') }} --}}
            <a class="float-right orange-800" href="{{ route('password.request') }}">Forgot password?</a>
        </a>
    @endif

    </div>
    <button type="submit" class="btn btn-block bg-teal-800 text-white btn-round">Log in</button>
</form>
@endsection
