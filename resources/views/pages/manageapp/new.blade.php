@extends('layouts.admin_template')

@section('title', 'Add New Application')

@section('page_level_css')
    <link href="{{ url('/') }}/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_level_js')
    <script src="{{ url('/') }}/js/pages/manageapp/new.js" type="text/javascript"></script>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add New Application</h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <form role="form">
                        <div class="col-md-5">
                            <div class="form-group">
                                <input type="text" name="app_search" id="app_search" class="form-control" placeholder="Type to search...">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12 appdiv">
                        <div class="app-box" data-app="visma-eaccounting" data-app-connect="{{ $visma_authorize_url }}">
                        <span class="app-box-icon">
                            <img class="appicon" src="{{ url('/images/apps/visma-eaccounting.png') }}">
                        </span>
                            <div class="info-box-content">
                                <span class="info-box-number"><b>Visma eAccounting</b></span>
                                <span class="info-box-text"><b>Accounting</b></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 appdiv">

                        <div class="app-box" data-app="shopify">
                        <span class="app-box-icon">
                            <img class="appicon" src="{{ url('/images/apps/shopify.png') }}">
                        </span>
                            <div class="info-box-content">
                                <span class="info-box-number"><b>Shopify</b></span>
                                <span class="info-box-text"><b>E-commerce</b></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 appdiv">
                        <div class="app-box" data-app="stripe" data-app-connect="{{ $stripe_authorize_url }}">
                        <span class="app-box-icon">
                            <img class="appicon" src="{{ url('/images/apps/stripe.png') }}">
                        </span>
                            <div class="info-box-content">
                                <span class="info-box-number"><b>Stripe</b></span>
                                <span class="info-box-text"><b>Stripe sales</b></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 appdiv">

                        <div class="app-box" data-app="woocommerce" data-app-connect="{{ $woocommerce_authorize_url }}">
                        <span class="app-box-icon">
                            <img class="appicon" src="{{ url('/images/apps/woocommerce.png') }}">
                        </span>
                            <div class="info-box-content">
                                <span class="info-box-number"><b>WooCommerce</b></span>
                                <span class="info-box-text"><b>E-commerce</b></span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection