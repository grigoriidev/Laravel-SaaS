@extends('layouts.auth_template')

@section('title', 'Sign Up')
@section('css_page_name', 'register-page')
@section('content')
<div class="register-box">
    <div class="register-logo">
        <a href="{{ url('/') }}"><b>Sign Up</b></a>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">Register a new membership</p>

        {{--<form action="" method="post">--}}
            {!! Form::open(['route' => 'register', 'method' => 'POST'] ) !!}
        {{ csrf_field() }}
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} has-feedback">
                {{--<input type="text" class="form-control" placeholder="Full name">--}}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Username', 'id' => 'name', 'required', 'autofocus']) !!}
                @if ($errors->has('name'))
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }} has-feedback">
                {{--<input type="email" class="form-control" placeholder="Email">--}}
                {!! Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'first_name']) !!}
                @if ($errors->has('first_name'))
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }} has-feedback">
                {{--<input type="password" class="form-control" placeholder="Password">--}}
                {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'last_name']) !!}
                @if ($errors->has('last_name'))
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @endif
            </div>
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
            {{--<input type="password" class="form-control" placeholder="Retype password">--}}
            {!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'E-Mail Address', 'required']) !!}
            @if ($errors->has('email'))
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
            {{--<input type="password" class="form-control" placeholder="Retype password">--}}
            {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Password', 'required']) !!}
            @if ($errors->has('password'))
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            @endif
        </div>
        <div class="form-group has-feedback">
            {{--<input type="password" class="form-control" placeholder="Retype password">--}}
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'password-confirm', 'placeholder' => 'Confirm Password', 'required']) !!}
        </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> I agree to the <a href="#">terms</a>
                        </label>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                </div>
                <!-- /.col -->
            </div>
        {{--</form>--}}
    {!! Form::close() !!}
        <!--div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
                Google+</a>
        </div-->

        <a href="{{ url('login') }}" class="text-center">I already have a membership</a>
        {{--@include('partials.socials')--}}
    </div>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->
@endsection
