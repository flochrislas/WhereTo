<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('inline-javascript.google-analytics')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- viewport for responsive web design -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WhereTo') }}</title>
    <!-- Style -->
    <link href="/css/style-dark.css" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
     <div id="app">
        <!-- HEADER -->
        @include('layouts.header')
        <!-- CONTENT -->
        @yield('content')
        <!-- FOOTER -->
        @include('layouts.footer')
    </div>
</body>
</html>
