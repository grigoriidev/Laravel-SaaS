<div class="row">
    <div class="col-sm-6 col-sm-offset-6 col-md-6 col-md-offset-6 col-lg-5 col-lg-offset-7 col-xl-4 col-xl-offset-8">
        <?php echo Form::open(['route' => 'search-users', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation', 'id' => 'search_users']); ?>

            <?php echo csrf_field(); ?>

            <div class="input-group margin-bottom-2">
                <?php echo Form::text('user_search_box', NULL, ['id' => 'user_search_box', 'class' => 'form-control', 'placeholder' => trans('usersmanagement.search.search-users-ph'), 'aria-label' => trans('usersmanagement.search.search-users-ph'), 'required' => false]); ?>

                <a href="#" class="input-group-addon btn btn-warning clear-search" data-toggle="tooltip" title="<?php echo app('translator')->getFromJson('lusersmanagement.tooltips.clear-search'); ?>" style="display:none;">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <span class="sr-only">
                        <?php echo app('translator')->getFromJson('lusersmanagement.tooltips.clear-search'); ?>
                    </span>
                </a>
                <a href="#" class="input-group-addon btn btn-info" id="search_trigger">
                    <i class="fa fa-search fa-fw" aria-hidden="true"></i>
                    <span class="sr-only">
                        <?php echo e(trans('usersmanagement.tooltips.submit-search')); ?>

                    </span>
                </a>
            </div>
        <?php echo Form::close(); ?>

    </div>
</div>
