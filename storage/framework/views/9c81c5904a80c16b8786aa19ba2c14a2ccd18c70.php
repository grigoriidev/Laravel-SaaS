<?php echo $__env->make('layouts.admin.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo $__env->make('layouts.admin.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->yieldContent('template_datatable_css'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="container">

    <?php echo $__env->make('partials.form-status', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>
<div class="content-wrapper">
    <?php echo $__env->yieldContent('content'); ?>
</div>
<!-- /.content-wrapper -->

<?php echo $__env->make('layouts.admin.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>