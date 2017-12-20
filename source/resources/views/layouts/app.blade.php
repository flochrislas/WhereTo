<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WhereTo') }}</title>

    <!-- Styles -->
    <!-- includes bootstrap 3.3.7 -->

    <link href="/css/app.css" rel="stylesheet">
  

    <!-- custom CSS -->
    <style>
        h1{font-size:23px;}
        .pull-left h2{margin-top:0;font-size:20px;}
    </style>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>


      <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
      <script src="//code.jquery.com/jquery-1.10.2.js"></script>
      <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

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
