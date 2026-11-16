<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- viewport for responsive web design -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SEO -->
    @yield('metatags')    
    <!-- Style -->
    <link href="/css/style-dark.css" rel="stylesheet">
    <!-- Scripts: one static file, cached by the browser. "defer" runs it after the HTML is parsed.
         The ?v= query string changes with the file, so a new version is not served from the cache. -->
    <script src="{{ asset('js/whereto.js') }}?v={{ filemtime(public_path('js/whereto.js')) }}" defer></script>
</head>
<body>
     <div id="app">
        <!-- HEADER -->
        @include('layouts.header')
        <!-- Geolocalisation messages (and possibly info) -->
        <div id="position-report"></div>
        <!-- CONTENT -->
        @yield('content')
        <!-- FOOTER -->
        @include('layouts.footer')
    </div>
</body>
</html>
