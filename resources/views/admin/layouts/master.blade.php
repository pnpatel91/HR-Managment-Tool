<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Project Management System') }}</title>

    <!-- HEAD STYLES [START]-->
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('public/adminLTE/plugins/font-awesome/css/font-awesome.min.css')}}">

        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('public/adminLTE/plugins/Ionicons/css/ionicons.min.css')}}">

        <!-- DATATABLE CSS -->
        <link rel="stylesheet" href="{{asset('public/adminLTE/plugins/datatables.net-bs/css/dataTables.bootstrap4.css')}}">
        
        <!-- Theme style -->
        <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css"> -->

        <!-- jQuery UI CSS -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />

        <!-- DATEPICKER.CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
        
        <link rel="stylesheet" href="{{ asset('plugins/datetimepicker/css/jquery.datetimepicker.min.css') }}">

        <!-- Select2 CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

        <!-- Date Range Picker CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">

        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('public/dist/css/adminlte.min.css') }}">

        <!-- CUSTOM STYLE -->
        <link rel="stylesheet" href="{{ asset('public/css/admin/custom.css') }}">

        <!-- Google Font: Source Sans Pro -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> --}}

    <!-- HEAD STYLES [END]-->


    <!-- REQUIRED HEAD SCRIPTS [START]-->

        <!-- jQuery -->
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        
        <!-- jQuery -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->

        <!-- JQUERY UI JS -->
        <script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
        

        <!-- BOOTSTRAP DATEPICKER -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="{{ asset('plugins/datetimepicker/js/jquery.datetimepicker.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

        <!-- CK Editor JS -->
        <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('plugins/ckeditor/samples/js/sample.js') }}"></script>

        <!-- JQUERY UI JS -->
        <script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

        <!-- BOOTSTRAP DATEPICKER -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

        <!-- DataTables  & Plugins -->
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>

        <!-- Select2 JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

        <!-- Date Range Picker JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- REQUIRED HEAD SCRIPTS [END]-->
    
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- PAGE LOADER -->
        <div id="pageloader">
           <img src="http://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="processing..." />
        </div>

        <!-- Navbar -->
        @include('admin.layouts.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('admin.layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                @if(Route::current()->getName()=='admin.')
                    <div class="col-md-12">
                        @include('message.alert')
                    </div>
                @endif
                @yield('content')
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- .model-popup [START] -->
        <div class="modal fade" id="popup-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="popup-modal-body" class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <!-- .model-popup [END] -->

        <!-- .model-popup User & Role [START] -->
        <div class="modal fade" id="popup-modalUserRole" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="popup-modal-bodyUserRole" class="modal-body">
                    </div>
                </div>
            </div>
        </div>
        <!-- .model-popup User & Role [END] -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
                
            </div>
            <!-- Default to the left -->
            <div class="float-left d-none d-sm-inline">
                
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED FOOTER SCRIPTS [START]-->
        <!-- jQuery -->
        <!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script> -->

        <!-- Bootstrap 4 -->
        <script src="{{ asset('public/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

        <!-- AdminLTE App -->
        <script src="{{ asset('public/dist/js/adminlte.min.js') }}"></script>

        <!-- JQUERY VALIDATION -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous"></script>

        <!-- CUSTOM JS -->
        <script src="{{ asset('public/js/admin/custom.js') }}"></script>
        <script src="{{ asset('public/js/admin/customUserRole.js') }}"></script>

        <script type="text/javascript">
            function loadNotificationsDropdownMenu(){
                $('#Notifications-Dropdown-Menu').load("{{ route('admin.notifications-dropdown-menu') }}");
            }

            loadNotificationsDropdownMenu();
            setInterval(function(){
                loadNotificationsDropdownMenu()
            }, 10000);
        </script>

    <!-- REQUIRED FOOTER SCRIPTS [END]-->

    <style type="text/css">.error{color: red;}</style>

    
</body>

</html>