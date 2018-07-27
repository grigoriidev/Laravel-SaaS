<?php $__env->startSection('title', 'Log in'); ?>
<?php $__env->startSection('css_page_name', 'login-page'); ?>
<?php $__env->startSection('content'); ?>
<div class="login-box">
    <div class="login-logo">
        <a href="<?php echo e(url('/')); ?>"><b>EPTI</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <form action="<?php echo e(route('login')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?> has-feedback">
                <input id="email" type="email" class="form-control" placeholder="Email"  name="email" value="<?php echo e(old('email')); ?>" required autofocus>
                <?php if($errors->has('email')): ?>
                <span class="glyphicon glyphicon-envelope form-control-feedback">
                  
                </span>
                <?php endif; ?>
            </div>
            <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?> has-feedback">
                <input id="password" type="password" class="form-control" placeholder="Password" name="password" required>
                <?php if($errors->has('password')): ?>
                <span class="glyphicon glyphicon-lock form-control-feedback">

                </span>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Remember Me
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

        <a class="text-center" href="<?php echo e(route('password.request')); ?>">I forgot my password</a><br>
        <a href="<?php echo e(url('register')); ?>" class="text-center">Register a new membership</a>
        
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>