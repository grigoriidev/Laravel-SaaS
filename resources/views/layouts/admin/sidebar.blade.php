<!-- Left side column. contains the logo and sidebar -->
<style>
    span {
        font-size: 18px;
        font-family: sans-serif;
    }
</style>
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            @role('admin')
            <li class="treeview">
                <a href="#"><i class="fa fa-user-circle"></i> <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>{!! HTML::link(url('/users'), Lang::get('titles.adminUserList')) !!}</li>
                    <li>{!! HTML::link(url('/users/create'), Lang::get('titles.adminNewUser')) !!}</li>
                </ul>
            </li>
            @endrole

            @role('user')
            <li><a href="{{ url('/manageapp') }}"><i class="fa fa-th"></i> <span>Applications</span></a></li>
            @endrole
            {{--<li><a href="{{ url('/workflows')}}"><i class="fa fa-gear"></i><span>Workflows</span></a></li>--}}
            {{--<li><a href="{{ url('/visma')}}"><i class="fa  fa-vine"></i><span>Visma</span></a></li>--}}
            {{--<li><a href="{{ url('/woocommerce')}}"><i class="fa fa-wordpress"></i><span>Woo-Commerce</span></a></li>--}}
            <li><a href="{{ url('/logs')}}"><i class="fa fa-file-text-o"></i><span>Logs</span></a></li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
