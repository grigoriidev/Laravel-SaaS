<?php $__env->startSection('title', 'Sign Up'); ?>
<?php $__env->startSection('css_page_name', 'register-page'); ?>
<?php $__env->startSection('content'); ?>
<div class="register-box">
    <div class="register-logo">
        <a href="<?php echo e(url('/')); ?>"><b>Sign Up</b></a>
    </div>

    <div class="register-box-body">
        <p class="login-box-msg">Register a new membership</p>

        
            <?php echo Form::open(['route' => 'register', 'method' => 'POST'] ); ?>

        <?php echo e(csrf_field()); ?>

            <div class="form-group<?php echo e($errors->has('name') ? ' has-error' : ''); ?> has-feedback">
                
                <?php echo Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Username', 'id' => 'name', 'required', 'autofocus']); ?>

                <?php if($errors->has('name')): ?>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
                <?php endif; ?>
            </div>
            <div class="form-group<?php echo e($errors->has('first_name') ? ' has-error' : ''); ?> has-feedback">
                
                <?php echo Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => 'First Name', 'id' => 'first_name']); ?>

                <?php if($errors->has('first_name')): ?>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                <?php endif; ?>
            </div>
            <div class="form-group<?php echo e($errors->has('last_name') ? ' has-error' : ''); ?> has-feedback">
                
                <?php echo Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => 'Last Name', 'id' => 'last_name']); ?>

                <?php if($errors->has('last_name')): ?>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                <?php endif; ?>
            </div>
        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?> has-feedback">
            
            <?php echo Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'E-Mail Address', 'required']); ?>

            <?php if($errors->has('email')): ?>
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            <?php endif; ?>
        </div>
        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?> has-feedback">
            
            <?php echo Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Password', 'required']); ?>

            <?php if($errors->has('password')): ?>
                <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
            <?php endif; ?>
        </div>
        <div class="form-group has-feedback">
            
            <?php echo Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'password-confirm', 'placeholder' => 'Confirm Password', 'required']); ?>

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
        
    <?php echo Form::close(); ?>

        <!--div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign up using
                Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign up using
                Google+</a>
        </div-->

        <a href="<?php echo e(url('login')); ?>" class="text-center">I already have a membership</a>
        
    </div>
    <!-- /.form-box -->
</div>
<!-- /.register-box -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>