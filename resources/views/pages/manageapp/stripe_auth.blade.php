@extends('layouts.admin_template')

@section('title', 'Applications')

@section('page_level_css')
    <link href="{{ url('/') }}/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
    <style>
        ul {
            padding: 0;
            margin: 0 0 10px 25px;
        }
        ul>li {
            padding: 5px;
            display: list-item;
            font-size: 18px;
            text-align: -webkit-match-parent;
        }
        input[name="public-key"],[name="secret-key"] {
            width: 600px;
            height: 30px;
        }
        input[value="Authenticate"] {
            background-color: #6AB025;
            border-radius: 5px;
            border-color: #6AB025;
            width: 200px;
            height: 40px;
            margin-top: 50px;
            margin-left: 50px;
            color: white;
            font-size: 20px;
            margin-bottom: 50px;
        }
        h4 {
            color: red !important;
        }
    </style>
@endsection
@section('page_level_js')
    <script src="{{ url('/') }}/js/pages/manageapp/new.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Applications
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box">
            <div class="box-body">
                <h2>To register Stripe you must provide your API keys and shop URL.</h2>
                <ul class="ng-scope" style="padding: 0;margin: 0 0 10px 25px;">
                    <li>Enable REST API in Woo Commerce under <b>Stripe&gt;Settings&gt;API&gt;Keys/Apps</b> tab.</li>
                    <li>Generate <b>Public Key and Secret</b> keys under <b>Add Key</b>.</li>
                </ul>
                <br/>
                <div style="width:50%" class="ng-scope">
                    {{  Form::open(array('action'=>'StripeAuthController@index', 'method' => 'post')) }}  
                    <div>
                        <h4>Public Key : *</h4>
                        {{  Form::text('public-key')  }}
                    </div> 
                    <div>
                        <h4>Secret Key : *</h4>
                        {{  Form::text('secret-key')  }}
                    </div> 
                    <div>
                         {{  Form::submit('Authenticate')  }}
                    </div>
                    {{  Form::close()  }} 
                </div>
            </div>

        </div>
    </section>
   
@endsection
