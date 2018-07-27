<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <?php if (Auth::check() && Auth::user()->hasRole('admin')): ?>
            <li class="treeview">
                <a href="#"><i class="fa fa-user-circle"></i> <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><?php echo HTML::link(url('/users'), Lang::get('titles.adminUserList')); ?></li>
                    <li><?php echo HTML::link(url('/users/create'), Lang::get('titles.adminNewUser')); ?></li>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (Auth::check() && Auth::user()->hasRole('user')): ?>
            <li><a href="<?php echo e(url('/manageapp')); ?>"><i class="fa fa-th"></i> <span>Applications</span></a></li>
            <?php endif; ?>
            
            <li><a href="<?php echo e(url('/visma')); ?>"><i class="fa  fa-vine"></i><span>Visma</span></a></li>
            <li><a href="<?php echo e(url('/woocommerce')); ?>"><i class="fa fa-wordpress"></i><span>Woo-Commerce</span></a></li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
