<!-- Stored in resources/views/layouts/html.blade.php -->
<!-- Doc: https://laravel.com/docs/master/blade -->
<!-- Work as parent of html layout -->

<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
    	<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Needed for restaurants CRUD 5.5 tuto -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
        
        <!-- Needed for restaurantType CRUD tuto -->
        <!-- bootstrap minified css -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <!-- bootstrap minified js -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>        
        <!-- custom CSS -->
        <style>
            h1{font-size:23px;}
            .pull-left h2{margin-top:0;font-size:20px;}
        </style>
        
        <title>{{ config('app.name') }} - @yield('title')</title>
    </head>
    <body>
    
        @section('sidebar')
            @if(config('app.debug') == 1)
        		[Sidebar Section][0]<BR>
                app.name = {{ config('app.name') }}<BR>
                app.env = {{ config('app.env') }}<BR>
                app.debug = {{ config('app.debug') }}<BR>
				@if(env('POORLY_COPIED') != 'nope')
					NOT A PROPER COPY OF THE APP!<BR>
				@endif
    		@endif        	
        @show

        <div class="container">
            @yield('content')
        </div>
        
    </body>
</html>


