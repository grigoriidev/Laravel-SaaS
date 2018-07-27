@extends('layouts.admin_template')

@section('title', "{{ Lang::get('titles.activation') }}")
@section('content')
	<!-- Content Header (Page header) -->
	<section class="content-header">
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="box">
					<div class="box-header">{{ Lang::get('titles.activation') }}</div>
					<div class="box-body">
						<p>{{ Lang::get('auth.regThanks') }}</p>
						<p>{{ Lang::get('auth.anEmailWasSent',['email' => $email, 'date' => $date ] ) }}</p>
						<p>{{ Lang::get('auth.clickInEmail') }}</p>
						<p><a href='/activation' class="btn btn-primary">{{ Lang::get('auth.clickHereResend') }}</a></p>
					</div>
				</div>

@endsection