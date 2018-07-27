    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">

        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">EPTI</a>.</strong> All rights reserved.
    </footer>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="<?php echo e(url('/')); ?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo e(url('/')); ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo e(url('/')); ?>/bower_components/admin-lte/dist/js/adminlte.min.js"></script>

<!-- DataTables -->
<script src="<?php echo e(url('/')); ?>/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo e(url('/')); ?>/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo e(url('/')); ?>/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo e(url('/')); ?>/bower_components/fastclick/lib/fastclick.js"></script>

<script src="<?php echo e(url('/')); ?>/bower_components/select2/dist/js/select2.full.min.js"></script>
<script src="<?php echo e(url('/')); ?>/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
  $(function () {
    $('#woocommerce-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : true
    });
    
    $('.select2').select2();
    $('span.select2.select2-container').css('width','100%');

     $('#datepicker').datepicker({
      autoclose: true
    });
     $('#datepicker2').datepicker({
      autoclose: true
    });
  })
</script>
<!-- BEGIN PAGE LEVEL Javascript -->
<?php echo $__env->yieldContent('page_level_js'); ?>
<!-- END PAGE LEVEL Javascript -->

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>