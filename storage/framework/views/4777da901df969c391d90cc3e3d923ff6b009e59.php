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
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            App Settings
        </h1>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
          <!-- Custom Tabs -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-key" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;Credentials</a></li>
              <li><a href="#tab_2" data-toggle="tab"><i class="fa fa-pencil" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;Rename</a></li>
              <li><a href="#tab_3" data-toggle="tab"><i class="fa fa-remove" style="color:#219ddb; font-size: 18px"></i>&nbsp;&nbsp;&nbsp;&nbspDelete</a></li>
              
              <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab_1">
                <h3><b>Credentials</b></h3>
                <h4>Here you can test the credentials ConnectMyApps currently uses to connect to "<?php echo e($app); ?>".</h4>
                <br/>
                <button class="btn btn-success" style="color: white;">Test</button>
                <br/>
                <h3><b>Change Credentials</b></h3>
                <h4>Here you can update the credentials ConnectMyApps uses to connect to "<?php echo e($app); ?>".</h4>
                <br/>
                <h4>To register your <?php echo e($app); ?> account please do the following:</h4>
                
                <div style="width:80%" class="ng-scope">
                    <ul>
                        <li style="margin-bottom: 5px;">Press <b>Authenticate</b> to open the <?php echo e($app); ?> login page.</li>
                        <li style="margin-bottom: 5px;">Login to <?php echo e($app); ?> .</li>
                        <li style="margin-bottom: 5px;"><b>Important!</b> This process requires your browser to allow popup windows. If prompted allow your browser to display pop ups.</li>
                    </ul>
                </div>
                <br/>
                <button class="btn btn-success">Authenticate</button>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_2">
                <h3><b>Application Name</b></h3>
                <h4>Here you can change the name of your app.</h4>
                <br/>
                <input type="text" name="app-name" style="width: 25%; height: 30px; margin-bottom: 20px">
                <br>
                <button class="btn btn-success"><i class="fa fa-save" style="font-size: 15px"></i>&nbsp;&nbsp;Save</button>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="tab_3">
                <h3><b>Delete Application</b></h3>
                <h4>Here you can delete "<?php echo e($app); ?>". Deleted apps will no longer appear in your Applications list.</h4>
                <br/>
                
                <br>
                <button class="btn btn-danger"><i class="fa fa-remove" style="font-size: 15px"></i>&nbsp;&nbsp;Delete</button>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- nav-tabs-custom -->
        </div>
                </div>
            </div>

        </div>
    </section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>