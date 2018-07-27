@extends('layouts.admin_template')

@section('title', 'Applications')

@section('page_level_css')
    <link href="{{ url('/') }}/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
@endsection
@section('page_level_js')
    <script src="{{ url('/') }}/js/pages/manageapp/new.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
@section('content')
<style>
        div.setting-box {
            width:30px; 
            height:30px;
            position: absolute; 
            bottom: 20px; 
            right: 20px; 
            border: 1px solid rgba(0, 0, 0, 0.12); 
            float: right;
            white-space: nowrap;
            text-align: center;
            align-items: center;
            background: #F5F5F5;
        }

        span.setting {
            font-size: 28px;
        }
        .setting {
            color: #219ddb;
           
        }
        span.setting-big {
            font-size: 32px;
            color: #219ddb; 
            margin-left: 10px;
        }
        .alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
        }

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .closebtn:hover {
            color: black;
        }

        .status-list, .vatcode-list {
            width: 300px;
            height:20px;
            font-size: 15px;
            color: black;
            border: 1px solid lightgray;
            margin-bottom: 25px;
        }
        .account-list {
            width: 220px;
            font-size: 15px;
            color: black;
            border: 1px solid lightgray;
            margin-bottom: 25px;
        
        }

        div.select-group {
            width: min-content;
            display: grid;
            height: 35px;
            border:1px solid #d2d6de;
            
        }
        label {
            color: lightgray;
        }
        span, h3, select {
            font-family: sans-serif;
            font-weight: 100;
            font-size: 18px;
        }
        /*select:focus {
            outline: none;
        }*/
    </style>
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
                <div class="row">
                    <div class="col-md-5">
                        <form action="" role="form" method="GET">
                            <div class="form-group">
                                <input type="text" name="app_search" id="app_search" class="form-control" placeholder="Type to search...">
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('manageapp/new') }}" id="app_search_butt" class="btn btn-block btn-success"><span class="fa fa-plus"></span> Add New Application</a>
                    </div>
                    
                    
                </div>

                @if($assigned_app_visma != null)
                <div class="row visma">
                    <?php $index_visma = 0 ?>
                    @foreach($assigned_app_visma as $app)
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">

                            <span class="app-box-icon">
                                <img class="appicon" src="{{ url('/images/apps/visma-eaccounting.png') }}">
                            </span>
                                <div class="info-box-content">
                                    <span class="info-box-number"><b>{{$app->name}}</b></span>
                                    <span class="info-box-text"><b>{{$app->app}}</b></span>
                                    <span class="info-box-text" style="color:pink; display:none;"><b>(current user - {{$current_user->name}})</b></span>
                                </div>

                                <span class=" app-setting fa fa-gear setting-big" app-data="app-setting-visma" data-app-connect="{{url('manageapp/app/credentials/').'/'.$app->app.'/'.$app->id.'/'.$app->name}}"></span>

                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;">{{$index_visma}}</div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index_visma++ ?>
                    @endforeach
                     <div id="number" style="display: none;"><?php echo($index_visma++) ?></div>
                     <div id="number_current" style="display: none;"></div>
                     @foreach($vatcodes as $vatcode)
                        <div class="vatcode-list" style="display: none;">{{$vatcode->Description}}-{{$vatcode->VatRate}}</div>
                     @endforeach
                     
                </div>
                @endif
                @if($assigned_app_woocommerce != null)
                <div class="row woocommerce">
                    <?php $index_woocommerce = 0 ?>
                    @foreach($assigned_app_woocommerce as $app)
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">
                            <span class="app-box-icon">
                                <img class="appicon" src="{{ url('/images/apps/woocommerce.png') }}">
                            </span>
                     
                                <div class="info-box-content">
                                    <span class="info-box-number"><b>{{$app->name}}</b></span>
                                    <span class="info-box-text"><b>{{$app->app}}</b></span>
                                    <span class="info-box-text" style="color:pink;display:none;"><b>(current user - {{$current_user->name}})</b></span>
                                </div>
                                <span class=" app-setting fa fa-gear setting-big" app-data="app-setting-woocommerce" data-app-connect="{{url('manageapp/app/credentials/').'/'.$app->app.'/'.$app->id.'/'.$app->name}}"></span>
                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;">{{$index_woocommerce}}</div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index_woocommerce++ ?>
                    @endforeach
                     <div id="number" style="display: none;"><?php echo($index_woocommerce++) ?></div>
                     <div id="number_current" style="display: none;"></div>
                </div>
                @endif
                <!-- /. stripe box -->
                @if($assigned_app_stripe != null)
                <div class="row stripe">
                    <?php $index_stripe = 0 ?>
                    @foreach($assigned_app_stripe as $app)
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">

                            <span class="app-box-icon">
                                <img class="appicon" src="{{ url('/images/apps/stripe.png') }}">
                            </span>
                                <div class="info-box-content">
                                    <span class="info-box-number"><b>{{$app->name}}</b></span>
                                    <span class="info-box-text"><b>Payment Service Provider</b></span>
                                    <span class="info-box-text" style="color:pink;display:none;"><b>(current user - {{$current_user->name}})</b></span>
                                </div>

                                <span class=" app-setting fa fa-gear setting-big" app-data="app-setting-stripe" data-app-connect="{{url('manageapp/app/credentials/').'/'.$app->app.'/'.$app->id.'/'.$app->name}}"></span>

                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;">{{$index_stripe}}</div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index_stripe++ ?>
                    @endforeach

                    <div id="number" style="display: none;"><?php echo($index_stripe++) ?></div>
                    <div id="number_current" style="display: none;"></div>
                     
                </div>
                @endif
                <!-- /. woocommerce schedule box -->
                 <div class="row confirm-box" style="display: none; border: 1px solid rgba(211, 211, 211, 0.6); border-radius: 5px;width: 99.9%;
                margin-left: 0%; padding: 5px;">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        {{  Form::open(array('action'=>'WooCommerceAuthController@saveScheduleData', 'method' => 'post')) }}
                            <span><h3 style="color: #219ddb">Schedule/Timer</h3></span>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Time Interval</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    {{  Form::select('time_interval', ['Every 5 minutes', 'Every 15 minutes', 'Once an hour','Once a day', 
                                       'Once a week'],$woo_schedule_interval, array('class' => 'account-list', )) }}
                                    
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Start Date</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                   {{ Form::text('start_date',$woo_schedule_start, array('class' => 'account-list pull-left', 'id' => 'datepicker', 'autocomplete' => 'off')) }}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Last Successful Run</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                   <p id ="last-run">{{$woo_schedule_last}}</p>
                                </div>
                            </div>
                            <br>
                            <div class="row">                              
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                  {{  Form::submit('Save and Run Now',['class' => 'btn btn-success connect-apps'])  }}
                                </div>
                            </div>

                        {{  Form::close()  }} 
                    </div>
                </div>
                <br>
                <!-- /. woocommerce confirm box -->
                <div class="row confirm-box" style="display: none; border: 1px solid rgba(211, 211, 211, 0.6); border-radius: 5px;width: 99.9%;
                margin-left: 0%; padding: 5px;">
                    @if($vatcodearray)
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        {{  Form::open(array('action'=>'WooCommerceAuthController@woocommerceData', 'method' => 'post')) }}
                        <!-- /.Status -->
                            <span><h3 style="color: #219ddb">Status</h3></span>  
                            <div class="row">
                                <div class="col-lg-4 col-lg-4 col-md-4 col-sm-6">
                                    {{  Form::select('status', ['Completed', 'On-Hold', 'Processing','Failed', 'Refunded', 'Pending payment', 'Cancelled'],$status, array('required','class' => 'status-list form-control select2','multiple' => 'multiple','name' => 'status[]' )) }}
                                
                                </div>
                            </div>
                        <!-- /.Status end -->   
                        <!-- /.Payment method --> 
                            <?php $index = 0; ?>
                            <span><h3 style="color: #219ddb">Payment methods</h3></span>
                            @foreach($payment_gateway as $payment_item)
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">{{(($payment_item->settings)->title)->default}} - {{(($payment_item->settings)->title)->value}}</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    {{  Form::select('payment'.$index, $accounts,$payment, array('class' => 'account-list', )) }}
                                </div>
                            </div>
                            <?php $index++ ?>
                            @endforeach
                            <!-- /.Payment method end -->
                            <!-- /.Vat Rate -->
                            <?php $index = 0 ?>
                            <span><h3 style="color: #219ddb">Sales</h3></span>
                            @foreach($tax_class as $tax_item)
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Sale of products, {{$tax_item->class}} {{intval($tax_item->rate)}}%</span>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="padding:0px;">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        {{  Form::select('product'.$index, $accounts,$product, array('class' => 'account-list', )) }}
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        {{  Form::select('sales'.$index, $accounts,$sales, array('class' => 'account-list', )) }}
                                    </div>
                                </div>
                            </div>
                            <?php $index++ ?>
                            @endforeach
                            <!-- /.Vat Rate end -->
                            <!-- /.Shipping -->
                            <?php $index = 0 ?>
                            <span><h3 style="color: #219ddb">Shipping</h3></span>
                            @foreach($shipping_method as $shipping_item)
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Shipping/Freight,{{$shipping_item->class}} {{intval($shipping_item->rate)}}%</span>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="padding:0px;">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        {{  Form::select('shippinggoods'.$index, $accounts,$shipping_goods, array('class' => 'account-list', )) }}
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        {{  Form::select('shippingvat'.$index, $accounts,$shipping_vat, array('class' => 'account-list', )) }}
                                    </div>
                                </div>
                            </div>
                            <?php $index++ ?>
                            @endforeach
                            <!-- /.Shipping end -->
                            
                            {{  Form::text('first-app',null,['id' => 'first-app', 'style' => 'display:none'])  }}
                            {{  Form::text('second-app',null,['id' => 'second-app', 'style' => 'display:none'])  }}
                            {{  Form::submit('Confirm',['class' => 'btn btn-success connect-apps'])  }}

                        {{  Form::close()  }} 

                    </div>
                    @endif
                </div>

                <!-- /. stripe schedule box -->
                <div class="row confirm-box-stripe" style="display: none; border: 1px solid rgba(211, 211, 211, 0.6); border-radius: 5px;width: 99.9%;margin-left: 0%; padding: 5px;">
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        {{  Form::open(array('action'=>'StripeAuthController@saveScheduleData', 'method' => 'post')) }}
                            <span><h3 style="color: #219ddb">Schedule/Timer</h3></span>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Time Interval</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    {{  Form::select('time_interval', ['Every 5 minutes', 'Every 15 minutes', 'Once an hour','Once a day', 
                                       'Once a week'],$stripe_schedule_interval, array('class' => 'account-list')) }}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Start Date</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                   {{ Form::text('start_date',$stripe_schedule_start, array('class' => 'account-list pull-left', 'id' => 'datepicker2','autocomplete' => 'off')) }}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12" style="padding-top: 3px;">
                                    <span style="font-size: 15px;">Last Successful Run</span>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                   <p id ="last-run">{{$stripe_schedule_last}}</p>
                                </div>
                            </div>
                            <br>
                            <div class="row">                              
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                  {{  Form::submit('Save and Run Now',['class' => 'btn btn-success connect-apps'])  }}
                                </div>
                            </div>

                        {{  Form::close()  }} 
                    </div>
                </div>
                <br>
                <!-- /. stripe confirm box -->
                <div class="row confirm-box-stripe" style="display: none; border: 1px solid rgba(211, 211, 211, 0.6); border-radius: 5px;
                            width: 99.9%; margin-left: 0%; padding: 5px;">
                    @if($vatcodearray)
                    <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
                        {{  Form::open(array('action'=>'StripeAuthController@stripeData', 'method' => 'post')) }}
                            <!-- /.Gross Amount --> 
                            <span><h3 style="color: #219ddb">Gross Amount</h3></span>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    {{  Form::select('gross', $accounts,$gross_amount, array('class' => 'account-list', )) }}
                                </div>
                            </div>
                            <!-- /.Gross Amount end -->
                            <!-- /.charge Amount --> 
                            <span><h3 style="color: #219ddb">Charges</h3></span>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    {{  Form::select('charges', $accounts,$charges, array('class' => 'account-list', )) }}
                                </div>
                            </div>
                            <!-- /.charge Amount end -->
                            <!-- /.net Amount --> 
                            <span><h3 style="color: #219ddb">Net Amount</h3></span>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    {{  Form::select('net', $accounts,$net_amount, array('class' => 'account-list', )) }}
                                </div>
                            </div>
                            <!-- /.net Amount end -->
                            {{  Form::text('first-app',null,['id' => 'first-app', 'style' => 'display:none'])  }}
                            {{  Form::text('second-app',null,['id' => 'second-app', 'style' => 'display:none'])  }}
                            {{  Form::submit('Confirm',['class' => 'btn btn-success connect-apps'])  }}

                        {{  Form::close()  }} 

                    </div>
                    @endif
                </div>
                <div class="connect-info">
                    <p id="first-app"></p>
                    <p id="second-app"></p>
                </div>
            </div>

            @if(isset($result))
            <div class="alert">
              <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
              <strong>Danger!</strong> Indicates a dangerous or potentially negative action.
            </div>
            @endif
        </div>
    </section>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>

        $(document).ready(function(){

            /*initialize*/
            $('div.visma div#number_current').html('-1');
            $('div.woocommerce div#number_current').html('-1');
            $('div.stripe div#number_current').html('-1');
            /*confirm button*/
            function setConfirm() {

                var visma_index = $('div.visma div#number_current').html();
                var woocommerce_index = $('div.woocommerce div#number_current').html(); 
                var stripe_index = $('div.stripe div#number_current').html();

                if(visma_index >= 0 && woocommerce_index >= 0) {
                    /*$('.confirm-box').css("opacity", "0.0").animate({opacity: 1}, 800, function(){
                        $('.confirm-box').css("visibility", "visible");
                    });*/
                    $('.confirm-box').css("display", "block");
                }

                if(!(visma_index >= 0 && woocommerce_index >= 0)) {
                    /*$('.confirm-box').css("opacity", "1.0").animate({opacity: 0}, 800, function(){
                        $('.confirm-box').css("visibility", "hidden");
                    });*/
                    $('.confirm-box').css("display", "none");
                }

                if(visma_index >= 0 && stripe_index >= 0) {
                    /*$('.confirm-box').css("opacity", "0.0").animate({opacity: 1}, 800, function(){
                        $('.confirm-box').css("visibility", "visible");
                    });*/
                    $('div.confirm-box-stripe').css("display", "block");
                }

                if(!(visma_index >= 0 && stripe_index >= 0)) {
                    /*$('.confirm-box').css("opacity", "1.0").animate({opacity: 0}, 800, function(){
                        $('.confirm-box').css("visibility", "hidden");
                    });*/
                    $('div.confirm-box-stripe').css("display", "none");
                }

            }

            /*visma*/
            var length_visma = $("div.visma div#number").html();
            console.log(length_visma);
            $("div.visma p").css("visibility","hidden");
            var check_array_visma = new Array();
            for (var i = 0; i < length_visma; i++) {
                check_array_visma[i] = false;
            }

            $("div.visma .setting-box").click(function(){
                
                if(check_array_visma[$(this).find("div#index").html()] == false) {
                    $("div.visma p").css("visibility","hidden");
                    for (var i = 0; i < length_visma; i++) {
                        check_array_visma[i] = false;
                    }
                    check_array_visma[$(this).find("div#index").html()] = true; 
                    $(this).find("p").css("visibility","visible"); 
                    $('div.visma div#number_current').html($(this).find("div#index").html());  
                    $('input#first-app').html('visma');

                } else {

                    $('div.visma div#number_current').html('-1');  
                    check_array_visma[$(this).find("div#index").html()] = false;
                    $(this).find("p").css("visibility","hidden"); 
                    $('input#first-app').html('');
                    // $('div.visma div#number_current').html($(this).find("div#index").html());  
                }

                setConfirm();
            });
            /*woocommerce*/
            var length_woocommerce = $("div.woocommerce div#number").html();
            console.log(length_woocommerce);
            $("div.woocommerce p").css("visibility","hidden");
            var check_array_woocommerce = new Array();
            for (var i = 0; i < length_woocommerce; i++) {
                check_array_woocommerce[i] = false;
            }

            $("div.woocommerce .setting-box").click(function(){
               
                if(check_array_woocommerce[$(this).find("div#index").html()] == false) {
                    $("div.woocommerce p").css("visibility","hidden");
                    for (var i = 0; i < length_woocommerce; i++) {
                        check_array_woocommerce[i] = false;
                    }
                    check_array_woocommerce[$(this).find("div#index").html()] = true; 
                    $(this).find("p").css("visibility","visible"); 
                    $('div.woocommerce div#number_current').html($(this).find("div#index").html());  
                    $('input#second-app').html('woocommerce');
                } else {
                    
                    $('div.woocommerce div#number_current').html('-1'); 
                    check_array_woocommerce[$(this).find("div#index").html()] = false;
                    $(this).find("p").css("visibility","hidden"); 
                    $('input#second-app').html('');
                    // $('div.woocommerce div#number_current').html($(this).find("div#index").html());  
                }
                setConfirm();
            });
            /*stripe*/
            var length_stripe = $("div.stripe div#number").html();
            console.log(length_stripe);
            $("div.stripe p").css("visibility","hidden");
            var check_array_stripe = new Array();
            for (var i = 0; i < length_stripe; i++) {
                check_array_stripe[i] = false;
            }

            $("div.stripe .setting-box").click(function(){
               
                if(check_array_stripe[$(this).find("div#index").html()] == false) {
                    $("div.stripe p").css("visibility","hidden");
                    for (var i = 0; i < length_stripe; i++) {
                        check_array_stripe[i] = false;
                    }
                    check_array_stripe[$(this).find("div#index").html()] = true; 
                    $(this).find("p").css("visibility","visible"); 
                    $('div.stripe div#number_current').html($(this).find("div#index").html());  
                    $('input#second-app').html('stripe');
                } else {
                    
                    $('div.stripe div#number_current').html('-1'); 
                    check_array_stripe[$(this).find("div#index").html()] = false;
                    $(this).find("p").css("visibility","hidden"); 
                    $('input#second-app').html('');
                    // $('div.woocommerce div#number_current').html($(this).find("div#index").html());  
                }
                setConfirm();
            });

        });
    </script>
@endsection
