<?php $__env->startSection('title'); ?>
  Showing Users
<?php $__env->stopSection(); ?>
<?php $__env->startSection('template_datatable_css'); ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo e(url('/')); ?>/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<?php $__env->stopSection(); ?>
<?php $__env->startSection('template_linked_css'); ?>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <style type="text/css" media="screen">
        .users-table {
            border: 0;
        }
        .users-table tr td:first-child {
            padding-left: 15px;
        }
        .users-table tr td:last-child {
            padding-right: 15px;
        }
        .users-table.table-responsive,
        .users-table.table-responsive table {
            margin-bottom: 0;
        }

    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Showing All Users
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <?php echo $__env->make('partials.search-users-form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                        <table class="table table-striped table-condensed data-table">
                            <thead class="thead">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th class="hidden-xs">Email</th>
                                    <th class="hidden-xs">First Name</th>
                                    <th class="hidden-xs">Last Name</th>
                                    <th>Role</th>
                                    <th class="hidden-sm hidden-xs hidden-md">Created</th>
                                    <th class="hidden-sm hidden-xs hidden-md">Updated</th>
                                    <th>Actions</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="users_table">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($user->id); ?></td>
                                        <td><?php echo e($user->name); ?></td>
                                        <td class="hidden-xs"><a href="mailto:<?php echo e($user->email); ?>" title="email <?php echo e($user->email); ?>"><?php echo e($user->email); ?></a></td>
                                        <td class="hidden-xs"><?php echo e($user->first_name); ?></td>
                                        <td class="hidden-xs"><?php echo e($user->last_name); ?></td>
                                        <td>
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user_role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                <?php if($user_role->name == 'User'): ?>
                                                    <?php $labelClass = 'primary' ?>

                                                <?php elseif($user_role->name == 'Admin'): ?>
                                                    <?php $labelClass = 'warning' ?>

                                                <?php elseif($user_role->name == 'Unverified'): ?>
                                                    <?php $labelClass = 'danger' ?>

                                                <?php else: ?>
                                                    <?php $labelClass = 'default' ?>

                                                <?php endif; ?>

                                                <span class="label label-<?php echo e($labelClass); ?>"><?php echo e($user_role->name); ?></span>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td class="hidden-sm hidden-xs hidden-md"><?php echo e($user->created_at); ?></td>
                                        <td class="hidden-sm hidden-xs hidden-md"><?php echo e($user->updated_at); ?></td>
                                        <td>
                                            <?php echo Form::open(array('url' => 'users/' . $user->id, 'class' => '', 'data-toggle' => 'tooltip', 'title' => 'Delete')); ?>

                                                <?php echo Form::hidden('_method', 'DELETE'); ?>

                                                <?php echo Form::button('<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm">Delete</span><span class="hidden-xs hidden-sm hidden-md"> User</span>', array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width: 100%;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete User', 'data-message' => 'Are you sure you want to delete this user ?')); ?>

                                            <?php echo Form::close(); ?>

                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-success btn-block" href="<?php echo e(URL::to('users/' . $user->id)); ?>" data-toggle="tooltip" title="Show">
                                                <i class="fa fa-eye fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm">Show</span><span class="hidden-xs hidden-sm hidden-md"> User</span>
                                            </a>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-info btn-block" href="<?php echo e(URL::to('users/' . $user->id . '/edit')); ?>" data-toggle="tooltip" title="Edit">
                                                <i class="fa fa-pencil fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm">Edit</span><span class="hidden-xs hidden-sm hidden-md"> User</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tbody id="search_results"></tbody>
                        </table>

                        <span id="user_count"></span>
                        <span id="user_pagination">
                            <?php echo e($users->links()); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('modals.modal-delete', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_level_js'); ?>

    <?php echo $__env->make('scripts.delete-modal-script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('scripts.save-modal-script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    

    
        <?php echo $__env->make('scripts.search-users', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_template', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>