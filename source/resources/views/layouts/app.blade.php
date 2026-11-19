<!DOCTYPE html>
<!-- data-css-version / data-js-version: the ?v= of the two files below; whereto.js registers the service worker
     with them, so the worker pre-caches the exact URLs the page uses (and a new version means a new worker).
     data-locale-prefixes: the languages under a prefix ("ja"), so the worker also pre-caches /ja/restaurants... -->
<html lang="{{ app()->getLocale() }}" data-css-version="{{ filemtime(public_path('css/style.css')) }}" data-js-version="{{ filemtime(public_path('js/whereto.js')) }}" data-locale-prefixes="{{ implode(',', \App\Support\LocaleUrl::prefixed()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- viewport for responsive web design. viewport-fit=cover: draw under the notch/home bar too,
         the CSS then pads with env(safe-area-inset-*) -->
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <!-- Colour of the browser/status bar around the page (Android Chrome, Safari tabs), same as the page background.
         whereto.js updates it when the theme changes -->
    <meta name="theme-color" content="#141422">
    <!-- Tell the browser the page can be dark or light, so its own UI (scrollbars, form controls) matches -->
    <meta name="color-scheme" content="dark light">
    <!-- Theme: dark or light. Inline on purpose and tiny: it runs before the first paint, so a visitor who chose the
         light theme never sees a dark flash. localStorage holds the choice; without one the OS preference wins.
         The stylesheet reads data-theme on <html>; the button in the header changes it (whereto.js, theme section) -->
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('theme') || (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                theme = theme === 'light' ? 'light' : 'dark'; // anything else is dark
                document.documentElement.dataset.theme = theme;
                document.querySelector('meta[name="theme-color"]').content = theme === 'light' ? '#f4f4f8' : '#141422';
            } catch (e) {}
        })();
    </script>
    <!-- Progressive Web App: the manifest describes the app for "Add to home screen" (name, icons, colours) -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Icons: browser tab, and the one iOS uses on the home screen -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SEO: each view gives its 'title' and 'description' sections; everything below derives from them -->
    <title>{{ config('app.name') }} - @yield('title')</title>
    <meta name="description" content="@yield('description')">
    <!-- The one URL of this page for the search engines (APP_URL + path, no query string): the same place is
         also served as a fragment, this says which one is the page -->
    {{-- The canonical page is the one on APP_URL, whatever hostname this request used --}}
    <link rel="canonical" href="{{ \App\Support\PublicUrl::to(request()->path()) }}">
    <!-- Open Graph and Twitter cards: what a link to this page shows when shared (Slack, LINE, Facebook, X...) -->
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ \App\Support\PublicUrl::to(request()->path()) }}">
    <meta property="og:title" content="@yield('title')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:image" content="{{ asset('icons/icon-512.png') }}">
    {{-- The language of this page in the Open Graph form (ja_JP), and the other languages of the site --}}
    <meta property="og:locale" content="{{ \App\Support\LocaleUrl::ogLocale(app()->getLocale()) }}">
    @foreach (\App\Support\LocaleUrl::locales() as $locale)
        @if ($locale !== app()->getLocale())
            <meta property="og:locale:alternate" content="{{ \App\Support\LocaleUrl::ogLocale($locale) }}">
        @endif
    @endforeach
    <meta name="twitter:card" content="summary">
    {{-- Anything a view pushes to the 'head' stack, e.g. the noindex of a closed place (components/business-status).
         A Blade comment, not an HTML one: Blade would run a directive written inside an HTML comment --}}
    @stack('head')
    {{-- The messages whereto.js writes in the page, in the language of the page (lang/<locale>.json); whereto.js
         reads them with t('key'). The JSON_HEX flags escape < > & ' " so no text can close the script tag;
         JSON_UNESCAPED_UNICODE keeps the Japanese readable in the page source instead of \uXXXX. The array is built
         in a variable first: @json cannot take an array written over several lines. (A Blade comment: Blade would
         run the directive inside an HTML comment.) --}}
    @php
        $strings = [
            'geolocationUnsupported' => __('Geolocation is not supported by this browser. Distances are from the station.'),
            'locating' => __('Locating you... distances are from the station until then.'),
            'fromPosition' => __('Distances are from your position.'),
            'geolocationDenied' => __('You denied the request for geolocation.'),
            'geolocationUnavailable' => __('Location information is unavailable.'),
            'geolocationTimeout' => __('The request to get your location timed out.'),
            'geolocationUnknown' => __('An unknown geolocation error occurred (:error).'),
            'geolocationHttps' => __('Please use the HTTPS version of the service to enable geolocation.'),
            'fromStation' => __('Distances are from the station.'),
            'loadingAll' => __('Loading ALL entries. Please wait or select a tag...'),
            'searchError' => __('Sorry, an error occurred during the search. Check your Internet connection and try again.'),
            'showAll' => __('Show All'),
            'noResult' => __('No result'),
            'showResults' => __('Show :count results'),
            'loading' => __('Loading...'),
            'detailsError' => __('Sorry, detailed data could not be retrieved. Check your Internet connection and try again.'),
            'tryAgain' => __('Try again'),
            'suggestionError' => __('Err(or), nothing from the top of my head, sorry.'),
            'switchToLight' => __('Switch to the light theme'),
            'switchToDark' => __('Switch to the dark theme'),
        ];
    @endphp
    <script>
        window.whereToStrings = @json($strings, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    </script>
    <!-- Style -->
    <!-- ?v= is the file modification time: a changed stylesheet has a new URL, so the browser cache cannot serve the old one -->
    <link href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" rel="stylesheet">
    <!-- Scripts: one static file, cached by the browser. "defer" runs it after the HTML is parsed.
         The ?v= query string changes with the file, so a new version is not served from the cache. -->
    <script src="{{ asset('js/whereto.js') }}?v={{ filemtime(public_path('js/whereto.js')) }}" defer></script>
</head>
<body>
     <div id="app">
        <!-- HEADER -->
        @include('layouts.header')
        <!-- The suggestion to read the page in the browser's language, filled by whereto.js (language banner section) -->
        <div id="languageBanner" hidden></div>
        <!-- The heading of the page for the search engines and the screen readers: the design shows the header's
             logo instead, so it is visually hidden (style.css). A view may give a 'heading' section (the place name) -->
        @hasSection('heading')
            <h1 class="visuallyHidden">@yield('heading')</h1>
        @else
            <h1 class="visuallyHidden">@yield('title')</h1>
        @endif
        <!-- Geolocalisation messages (and possibly info) -->
        <div id="position-report" aria-live="polite"></div>
        <!-- CONTENT -->
        @yield('content')
        <!-- FOOTER -->
        @include('layouts.footer')
    </div>
</body>
</html>
