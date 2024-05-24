<footer class="main-footer">
    <strong>Copyright &copy; {{ date('Y')}} <a href="https://preprlabs.org">Preprlabs.org</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0.0
    </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jquery/jquery.min.js'}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jquery-ui/jquery-ui.min.js'}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<!-- Bootstrap 4 -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/bootstrap/js/bootstrap.bundle.min.js'}}"></script>
<!-- ChartJS -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/chart.js/Chart.min.js'}}"></script>
<!-- Sparkline -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/sparklines/sparkline.js'}}"></script>
<!-- JQVMap -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jqvmap/jquery.vmap.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jqvmap/maps/jquery.vmap.usa.js'}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jquery-knob/jquery.knob.min.js'}}"></script>
<!-- daterangepicker -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/moment/moment.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/daterangepicker/daterangepicker.js'}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'}}"></script>
<!-- Summernote -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/summernote/summernote-bs4.min.js'}}"></script>
<!-- overlayScrollbars -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js'}}"></script>
<!-- AdminLTE App -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/dist/js/adminlte.js'}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/dist/js/demo.js'}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/dist/js/pages/dashboard.js'}}"></script>

<!-- DataTables  & Plugins -->
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables/jquery.dataTables.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-responsive/js/dataTables.responsive.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-responsive/js/responsive.bootstrap4.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-buttons/js/dataTables.buttons.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-buttons/js/buttons.bootstrap4.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/jszip/jszip.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/pdfmake/pdfmake.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/pdfmake/vfs_fonts.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-buttons/js/buttons.html5.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-buttons/js/buttons.print.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/plugins/datatables-buttons/js/buttons.colVis.min.js'}}"></script>
<script src="{{config('site-settings.maestro_cdn_url').'public/maestro/dist/js/adminlte.min.js'}}"></script>