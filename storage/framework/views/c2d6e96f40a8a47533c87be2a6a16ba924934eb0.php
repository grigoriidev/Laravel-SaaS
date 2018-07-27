<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
        </h1>
    </section>

    <!-- Main content -->
    <style>
        section.content div.box div.box-body table#example2 thead tr th {
            width: 19%;
            height: 40px;
            color: red;
            font-weight: bold;
            font-size: 20px;
            border: 1px dotted gray;
        }
        section.content div.box div.box-body table#example2 tbody tr td {
            width: 19%;
            height: 40px;
            color: blue;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            border: 1px dotted gray;
        }
    </style>
    <section class="content container-fluid">
        <?php if($visma_data != null): ?>
            <div class="box">
            <div class="box-body">
                <div class="row">
                   <div class="box">
                        <div class="box-header">
                          <h3 class="box-title" style="font-size: 25px">Onging Projects</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                          <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                  <th >Project no</th>
                                  <th >Project name</th>
                                  <th >Customer</th>
                                  <th >Start Date</th>
                                  <th >End Date</th>
                                  <th >Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $visma_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                      <td><?php echo e($data->Number); ?></td>
                                      <td><?php echo e($data->Name); ?></td>
                                      <td><?php echo e($data->CustomerName); ?></td>
                                      <td><?php echo e($data->StartDate); ?></td>
                                      <td><?php echo e($data->EndDate); ?></td>
                                      <td><?php if($data->Status == 0): ?> Ongoing <?php endif; ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                          </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <!-- /.content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>