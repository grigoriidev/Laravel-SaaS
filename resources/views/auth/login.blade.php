@extends('layouts.auth_template')

@section('title', 'Log in')
@section('css_page_name', 'login-page')
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="{{ url('/') }}"><b>EPTI</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="{{ route('login') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                <input id="email" type="email" class="form-control" placeholder="Email"  name="email" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                <span class="glyphicon glyphicon-envelope form-control-feedback">
                  {{--<strong>{{ $errors->first('email') }}</strong>--}}
                </span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                <input id="password" type="password" class="form-control" placeholder="Password" name="password" required>
                @if ($errors->has('password'))
                <span class="glyphicon glyphicon-lock form-control-feedback">
{{--                    <strong>{{ $errors->first('password') }}</strong>--}}
                </span>
                @endif
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <!--div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                Google+</a>
        </div-->
        <!-- /.social-auth-links -->

        <a class="text-center" href="{{ route('password.request') }}">I forgot my password</a><br>
        <a href="{{ url('register') }}" class="text-center">Register a new membership</a>
        {{--@include('partials.socials-icons')--}}
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
@endsection