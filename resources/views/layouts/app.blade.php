<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Project Management System') }}</title>

    

    <!-- HEAD FONTS [START] -->
        {{-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> --}}
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}
    <!-- HEAD FONTS [END] -->

    <!-- HEAD STYLES [START] -->
        <!-- FONT AWESOME ICONS -->
        <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">

        <!-- APP CSS -->
        <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">

        <!-- CK EDITOR CSS -->
        <link href="{{ asset('public/plugins/ckeditor/samples/css/samples.css') }}" rel="stylesheet">
        <link href="{{ asset('public/plugins/ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css') }}" rel="stylesheet">

        <!-- JQUERY UI CSS -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />

        <!-- BOOTSTRAP MIN CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <!-- BOOTSTRAP-DATEPICKER -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
        <style>
            .pagination {
                justify-content: center;
            }
        </style>
    <!-- HEAD STYLES [END] -->


    <!-- REQUIRED FOOTER SCRIPTS [START] -->
        <!-- jQuery -->
        <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>

        <!-- App JS -->
        <script src="{{ asset('public/js/app.js') }}"></script>

        <!-- CK Editor JS -->
        <script src="{{ asset('public/plugins/ckeditor/ckeditor.js') }}"></script>
        <script src="{{ asset('public/plugins/ckeditor/samples/js/sample.js') }}"></script>

        <!-- Jquery UI JS -->
        <script src="https://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>

        <!-- Bootstrap Min JS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

        <!-- Bootstrap Datepicker JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

        <!-- Jquery Validation JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous"></script>
    <!-- REQUIRED FOOTER SCRIPTS [END] -->

</head>
<body>
    <div id="app">
        <main style="margin: 30px 0;">
            @yield('content')
        </main>
    </div>
</body>
</html>
