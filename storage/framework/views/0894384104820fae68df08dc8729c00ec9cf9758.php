<?php $__env->startSection('title', "<?php echo e(Lang::get('titles.activation')); ?>"); ?>
<?php $__env->startSection('content'); ?>
	<!-- Content Header (Page header) -->
	<section class="content-header">
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="box">
					<div class="box-header"><?php echo e(Lang::get('titles.activation')); ?></div>
					<div class="box-body">
						<p><?php echo e(Lang::get('auth.regThanks')); ?></p>
						<p><?php echo e(Lang::get('auth.anEmailWasSent',['email' => $email, 'date' => $date ] )); ?></p>
						<p><?php echo e(Lang::get('auth.clickInEmail')); ?></p>
						<p><a href='/activation' class="btn btn-primary"><?php echo e(Lang::get('auth.clickHereResend')); ?></a></p>
					</div>
				</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>