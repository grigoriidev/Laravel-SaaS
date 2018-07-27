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
    </style>
    <section class="content container-fluid">
 
                   <div class="box">
                        <div class="box-header">
                          <h3 class="box-title" style="font-size: 28px">Transferred Orders</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                          <?php if($visma_data == null): ?>
                          <h3 style="text-align: center;">No Data</h3>
                          <?php else: ?>
                            <?php $index = 0 ?>
                            <?php $__currentLoopData = $visma_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visma_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                              <div class="box-header">
                                <h3 class="box-title" style="font-size: 22px; color: #3c8dbc;">Entry no.<?php echo e($visma_data_title[$index]); ?></h3>
                              </div>
                              <table id="example2" class="table table-bordered table-hover visma-table">
                                <thead>
                                    <tr>
                                      <th >Account</th>
                                      <th >Transaction Text</th>
                                      <th >Vat Code</th>
                                      <th >Debit</th>
                                      <th >Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $visma_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                          <td style="text-align: left;"><?php echo e($data->AccountNumber); ?><li class="fa  fa-long-arrow-right"></li><?php echo e($data->AccountDescription); ?></td>
                                          <td style="text-align: left;"><?php echo e($data->TransactionText); ?></td>
                                          <td><?php echo e($data->VatCodeAndPercent); ?></td>
                                          <td><?php echo e($data->DebitAmount); ?></td>
                                          <td><?php echo e($data->CreditAmount); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                              </table>
                              <?php $index++ ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          <?php endif; ?>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
    
    </section>
    <!-- /.content -->
<?php $__env->stopSection(); ?>






<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>