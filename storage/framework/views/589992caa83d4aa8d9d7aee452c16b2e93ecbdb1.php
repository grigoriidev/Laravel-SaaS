<?php $__env->startSection('title', 'Applications'); ?>

<?php $__env->startSection('page_level_css'); ?>
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/new.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page_level_js'); ?>
    <script src="<?php echo e(url('/')); ?>/js/pages/manageapp/new.js" type="text/javascript"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
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
            Transfer Logs
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <?php if($log_array != null): ?>
            <?php $__currentLoopData = $log_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php if(strpos($log_item, 'successfully') == true || strpos($log_item, 'transferred')): ?>
                    <p><span><?php echo e($log_item); ?></span></p>
                    
                <?php elseif(strpos($log_item, 'failed') == true): ?>
                    <p><span><?php echo((explode("\t",$log_item))[0])?></span><span data-toggle="tooltip" title='<?php echo((explode("\t",$log_item))[1])?>'>error code</span><span><?php echo((explode("\t",$log_item))[2])?></span></p>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <?php endif; ?>
    </section>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>