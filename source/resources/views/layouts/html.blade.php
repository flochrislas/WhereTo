<!-- Stored in resources/views/layouts/html.blade.php -->
<!-- Doc: https://laravel.com/docs/master/blade -->
<!-- Work as parent of html layout -->

<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} - @yield('title') ({{ config('app.env') }})</title>
    </head>
    <body>
        @section('sidebar')
            @if(config('app.debug') == 1)
        		[Sidebar Section][0]<BR>
                app.name = {{ config('app.name') }}<BR>
                app.env = {{ config('app.env') }}<BR>
                app.debug = {{ config('app.debug') }}<BR>
				@if(env('POORLY_COPIED') != 'nope')
					NOT A PROPER COPY OF THE APP<BR>
				@endif
    		@endif        	
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>


