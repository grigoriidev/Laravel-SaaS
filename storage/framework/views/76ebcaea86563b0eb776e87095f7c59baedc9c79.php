<?php $__env->startSection('title', 'Applications'); ?>

<?php $__env->startSection('page_level_css'); ?>
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
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

        .status-list, .vatcode-list, .account-list {
            width: 300px;
            height: 30px;
            font-size: 15px;
            color: green;
            border: 1px solid lightgray;
            margin-bottom: 25px;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page_level_js'); ?>
    <script src="<?php echo e(url('/')); ?>/js/pages/manageapp/new.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
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
                    <div class="col-md-7">
                        <a href="<?php echo e(url('manageapp/new')); ?>" id="app_search_butt" class="btn btn-block btn-success"><span class="fa fa-plus"></span> Add New Application</a>
                    </div>
                </div>

                <?php if($assigned_app_visma != null): ?>
                <div class="row visma">
                    <?php $index_visma = 0 ?>
                    <?php $__currentLoopData = $assigned_app_visma; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">

                            <span class="app-box-icon">
                                <img class="appicon" src="<?php echo e(url('/images/apps/visma-eaccounting.png')); ?>">
                            </span>
                                <div class="info-box-content">
                                    <span class="info-box-number"><b><?php echo e($app->name); ?></b></span>
                                    <span class="info-box-text"><b><?php echo e($app->app); ?><?php echo e($app->id); ?></b></span>
                                    <span class="info-box-text" style="color:pink"><b>(current user - <?php echo e($current_user->name); ?>)</b></span>
                                </div>

                                <span class=" app-setting fa fa-gear setting-big" app-data="app-setting-visma" data-app-connect="<?php echo e(url('manageapp/app/credentials/').'/'.$app->app.'/'.$app->id.'/'.$app->name); ?>"></span>

                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;"><?php echo e($index_visma); ?></div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index_visma++ ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     <div id="number" style="display: none;"><?php echo($index_visma++) ?></div>
                     <div id="number_current" style="display: none;"></div>
                     <?php $__currentLoopData = $vatcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vatcode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="vatcode-list" style="display: none;"><?php echo e($vatcode->Description); ?>-<?php echo e($vatcode->VatRate); ?></div>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accountlist" style="display: none;"><?php echo e($account->Name); ?>-<?php echo e($account->Number); ?></div>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
                <?php if($assigned_app_woocommerce != null): ?>
                <div class="row woocommerce">
                    <?php $index_woocommerce = 0 ?>
                    <?php $__currentLoopData = $assigned_app_woocommerce; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">
                            <span class="app-box-icon">
                                <img class="appicon" src="<?php echo e(url('/images/apps/woocommerce.png')); ?>">
                            </span>
                     
                                <div class="info-box-content">
                                    <span class="info-box-number"><b><?php echo e($app->name); ?></b></span>
                                    <span class="info-box-text"><b><?php echo e($app->app); ?><?php echo e($app->id); ?></b></span>
                                    <span class="info-box-text" style="color:pink"><b>(current user - <?php echo e($current_user->name); ?>)</b></span>
                                </div>
                                <span class=" app-setting fa fa-gear setting-big" app-data="app-setting-woocommerce" data-app-connect="<?php echo e(url('manageapp/app/credentials/').'/'.$app->app.'/'.$app->id.'/'.$app->name); ?>"></span>
                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;"><?php echo e($index_woocommerce); ?></div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index_woocommerce++ ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     <div id="number" style="display: none;"><?php echo($index_woocommerce++) ?></div>
                     <div id="number_current" style="display: none;"></div>
                </div>
                <?php endif; ?>

                <!-- /.connect apps confirm -->
                <div class="row confirm-box" style="visibility: hidden;">
                    
                    <div class="col-md-3 col-sm-4 col-xs-6">
                        <?php echo e(Form::open(array('action'=>'WooCommerceAuthController@woocommerceData', 'method' => 'post'))); ?>  
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <?php echo Form::label('Status', 'Status:'); ?>

                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <?php echo e(Form::select('status', ['Completed', 'On-Hold', 'Processing','Failed', 'Refunded', 'Pending payment', 'Cancelled'],null, array('class' => 'status-list', ))); ?>

                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <?php echo Form::label('Account', 'Account:'); ?>

                                </div>
                                <div class="col-md-8 col-sm-8">
                                    <?php echo e(Form::select('account', $accountarray,null, array('class' => 'account-list', ))); ?>

                                </div>
                            </div>
                            
                            <?php echo e(Form::text('first-app',null,['id' => 'first-app', 'style' => 'display:none'])); ?>

                            <?php echo e(Form::text('second-app',null,['id' => 'second-app', 'style' => 'display:none'])); ?>

                            <?php echo e(Form::submit('Confirm',['class' => 'btn btn-success connect-apps'])); ?>


                        <?php echo e(Form::close()); ?> 
                        
                    </div>
                </div>
                <div class="connect-info">
                    <p id="first-app"></p>
                    <p id="second-app"></p>
                </div>
            </div>

            <?php if(isset($result)): ?>
            <div class="alert">
              <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
              <strong>Danger!</strong> Indicates a dangerous or potentially negative action.
            </div>
            <?php endif; ?>
        </div>
    </section>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>

        $(document).ready(function(){

            /*initialize*/
            $('div.visma div#number_current').html('-1');
            $('div.woocommerce div#number_current').html('-1');

            /*confirm button*/
            function setConfirm() {

                var visma_index = $('div.visma div#number_current').html();
                var woocommerce_index = $('div.woocommerce div#number_current').html(); 

                if(visma_index >= 0 && woocommerce_index >= 0) {
                    $('.confirm-box').css("opacity", "0.0").animate({opacity: 1}, 800, function(){
                        $('.confirm-box').css("visibility", "visible");
                    });
                }

                if(!(visma_index >= 0 && woocommerce_index >= 0)) {
                    $('.confirm-box').css("opacity", "1.0").animate({opacity: 0}, 800, function(){
                        $('.confirm-box').css("visibility", "hidden");
                    });
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

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>