<!-- Stored in resources/views/layouts/html.blade.php -->
<!-- Doc: https://laravel.com/docs/master/blade -->
<!-- Work as parent of html layout -->

<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WhereTo - @yield('title')</title>
    </head>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>