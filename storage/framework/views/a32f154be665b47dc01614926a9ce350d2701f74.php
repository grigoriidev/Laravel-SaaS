<?php $__env->startSection('title', 'Applications'); ?>

<?php $__env->startSection('page_level_css'); ?>
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
    <style>
        div.setting-box {
            width:30px; 
            height:30px;
            position: relative; 
            bottom: 10px; 
            right: 5px; 
            border: 1px solid rgba(0, 0, 0, 0.12); 
            float: right;
            white-space: nowrap;
            text-align: center;
            align-items: center;
            background: #F5F5F5;
        }

        span.setting {
            font-size: 24px;
        }
        .setting {
            color: #219ddb;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page_level_js'); ?>
    <script src="<?php echo e(url('/')); ?>/js/pages/manageapp/new.js" type="text/javascript"></script>
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
                <?php if($assigned_app != null): ?>
                <div class="row">
                    <?php $index = 0 ?>
                    <?php $__currentLoopData = $assigned_app; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 col-sm-4 col-xs-6">
                            <div class="app-box">
                            <span class="app-box-icon">
                                <img class="appicon" src="<?php echo e(url('/images/apps/visma-eaccounting.png')); ?>">
                            </span>
                                <div class="info-box-content">
                                    <span class="info-box-number"><b><?php echo e($app->name); ?></b></span>
                                    <span class="info-box-text"><b><?php echo e($app->app); ?></b></span>
                                </div>
                                <div class="setting-box">
                                    <p><span class="fa fa-check setting" aria-hidden="true"></span></p>
                                    <div id="index" style="display: none;"><?php echo e($index); ?></div>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            <!-- /.info-box -->
                        </div>
                        <?php $index++ ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div id="number" style="display: none;"><?php echo($index++) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            var length = $("div#number").html();
            $("p").css("visibility","hidden");
            var check_array = new Array();
            for (var i = 0; i < length; i++) {
                check_array[i] = false;

            }
            $(".setting-box").click(function(){
                console.log(check_array);
                if(check_array[$(this).find("div#index").html()] == false) {
                   
                    check_array[$(this).find("div#index").html()] = true; 
                    $(this).find("p").css("visibility","visible");   
                } else {
                    
                    check_array[$(this).find("div#index").html()] = false;
                    $(this).find("p").css("visibility","hidden"); 
                }
                
           });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>