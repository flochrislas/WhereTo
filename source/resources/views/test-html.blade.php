<!-- Stored in resources/views/test-html.blade.php -->
<!-- Doc: https://laravel.com/docs/master/blade -->
<!-- Work as child of html layout -->
<!-- Can be accessible with return view('test-html'); -->


@extends('layouts.html')

@section('title', 'Page Title (section)')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar (section).</p>
@endsection

@section('content')
    <p>This is my body content (section).</p>
@endsection