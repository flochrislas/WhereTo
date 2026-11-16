<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- viewport for responsive web design. viewport-fit=cover: draw under the notch/home bar too,
         the CSS then pads with env(safe-area-inset-*) -->
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <!-- Colour of the browser/status bar around the page (Android Chrome, Safari tabs), same as the page background -->
    <meta name="theme-color" content="#141422">
    <!-- Tell the browser the page is dark, so its own UI (scrollbars, form controls) matches -->
    <meta name="color-scheme" content="dark">
    <!-- Progressive Web App: the manifest describes the app for "Add to home screen" (name, icons, colours) -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Icons: browser tab, and the one iOS uses on the home screen -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SEO -->
    @yield('metatags')    
    <!-- Style -->
    <!-- ?v= is the file modification time: a changed stylesheet has a new URL, so the browser cache cannot serve the old one -->
    <link href="{{ asset('css/style-dark.css') }}?v={{ filemtime(public_path('css/style-dark.css')) }}" rel="stylesheet">
    <!-- Scripts: one static file, cached by the browser. "defer" runs it after the HTML is parsed.
         The ?v= query string changes with the file, so a new version is not served from the cache. -->
    <script src="{{ asset('js/whereto.js') }}?v={{ filemtime(public_path('js/whereto.js')) }}" defer></script>
</head>
<body>
     <div id="app">
        <!-- HEADER -->
        @include('layouts.header')
        <!-- Geolocalisation messages (and possibly info) -->
        <div id="position-report" aria-live="polite"></div>
        <!-- CONTENT -->
        @yield('content')
        <!-- FOOTER -->
        @include('layouts.footer')
    </div>
</body>
</html>
