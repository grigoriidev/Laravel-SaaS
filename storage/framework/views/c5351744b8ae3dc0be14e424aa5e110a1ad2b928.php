<?php $__env->startSection('title', 'Applications'); ?>

<?php $__env->startSection('page_level_css'); ?>
    <link href="<?php echo e(url('/')); ?>/css/pages/manageapp/index.css" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
       <img src="https://www.vismaonline.com/Resources/Images/visma-logo.svg">
    </section>

    <!-- Main content -->
   <style>
        section.content div.box div.box-body table#example2 thead tr th {
            width: 19%;
            height: 40px;
            color: gray;
            font-weight: bold;
            font-size: 18px;
          /*  border: 1px solid lightgray;*/
            text-align: center;
        }
        section.content div.box div.box-body table#example2 tbody tr td {
            width: 19%;
            height: 40px;
            color: black;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            /*border: 1px solid lightgray;*/
            font-family: initial;
        }
        table.visma-table {
          margin-bottom: 30px !important;
        }
        .table-header, .table-row td {
          text-align: center;
        }
    </style>
    <section class="content container-fluid">
 
     <div class="box">
          <div class="box-header">
            <h3 class="box-title" style="font-size: 28px">WooCommerce Orders</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <?php if($woocommerce_data == null): ?>
            <h3 style="text-align: center;">No Data</h3>
            <?php else: ?>
            <div class="box-body">
              <table id="woocommerce-table" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th class="table-header">Order</th>
                  <th class="table-header">Status</th>
                  <th class="table-header">Billing</th>
                  <th class="table-header">Send to</th>
                  <th class="table-header">Overall</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $woocommerce_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-row">
                  <td style="color:#0073aa;font-size:13px;">#<?php echo e($data->id); ?><?php echo e(($data->billing)->first_name); ?><?php echo e(($data->billing)->last_name); ?></td>
                  <td>
                    <?php if($data->status === 'completed'): ?>
                      <button class="btn btn-info"><?php echo e($data->status); ?></button>
                    <?php endif; ?>
                    <?php if($data->status === 'on-hold'): ?>
                      <button class="btn btn-warning"><?php echo e($data->status); ?></button>
                    <?php endif; ?>
                    <?php if($data->status === 'processing'): ?>
                      <button class="btn btn-success"><?php echo e($data->status); ?></button>
                    <?php endif; ?>
                    <?php if($data->status === 'failed'): ?>
                      <button class="btn btn-danger"><?php echo e($data->status); ?></button>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo e(($data->billing)->first_name); ?>  
                    <?php echo e(($data->billing)->last_name); ?>,
                    <?php echo e(($data->billing)->address_1); ?>

                    <?php echo e(($data->billing)->address_2); ?>,
                    <?php echo e(($data->billing)->postcode); ?>

                    <?php echo e(($data->billing)->city); ?>

                    <br/>
                    <b>payment</b>
                    <br/>
                    <i>(<?php echo e($data->payment_method); ?>)</i>
                  </td>
                  <td style="color:#0073aa;font-size:13px;">
                    <?php echo e(($data->billing)->first_name); ?>  
                    <?php echo e(($data->billing)->last_name); ?>,
                    <?php echo e(($data->billing)->address_1); ?>

                    <?php echo e(($data->billing)->address_2); ?>,
                    <?php echo e(($data->billing)->postcode); ?>

                    <?php echo e(($data->billing)->city); ?>

                    <br/>
                    <?php echo e(($data->billing)->email); ?>

                  </td>
                  <td>
                    <?php echo e($data->total); ?><?php echo e($data->currency); ?>

                    <br/>
                    <b style="color:#999;"><i>MVA</i></b><br/>
                    <?php echo e($data->total_tax); ?><?php echo e($data->currency); ?>

                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                <tr>
                  <th>Rendering engine</th>
                  <th>Browser</th>
                  <th>Platform(s)</th>
                  <th>Engine version</th>
                  <th>CSS grade</th>
                </tr>
                </tfoot>
              </table>
            </div>        
          <?php endif; ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    
    </section>
    
    <!-- /.content -->
<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>