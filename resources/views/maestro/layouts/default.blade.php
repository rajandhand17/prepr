<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/fontawesome-free/css/all.min.css'}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/icheck-bootstrap/icheck-bootstrap.min.css'}}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/jqvmap/jqvmap.min.css'}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/dist/css/adminlte.min.css' }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/overlayScrollbars/css/OverlayScrollbars.min.css'}}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/daterangepicker/daterangepicker.css'}}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/summernote/summernote-bs4.min.css'}}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css'}}">
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/datatables-responsive/css/responsive.bootstrap4.min.css'}}">
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/datatables-buttons/css/buttons.bootstrap4.min.css'}}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'}}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/select2/css/select2.min.css'}}">
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'}}">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css'}}">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/bs-stepper/css/bs-stepper.min.css'}}">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/dropzone/min/dropzone.min.css'}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{config('site-settings.aws_url').'public/front/img/gif-loader.gif'}}" alt="Preprlabs logo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            @include('maestro.common.header')
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            @include('maestro.common.sidebar')
        </aside>
        <!-- /.sidebar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        <!-- footer -->
        @include('maestro.common.footer')

        @yield('scripts')
        <!-- footer -->

</body>

</html>
