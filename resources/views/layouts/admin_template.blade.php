@include('layouts.admin.header')

@include('layouts.admin.sidebar')
@yield('template_datatable_css')
<!-- Content Wrapper. Contains page content -->
<div class="container">

    @include('partials.form-status')

</div>
<div class="content-wrapper">
    @yield('content')
</div>
<!-- /.content-wrapper -->

@include('layouts.admin.footer')