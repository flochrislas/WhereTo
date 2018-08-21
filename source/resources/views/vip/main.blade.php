@extends('layouts.app')

@section('title', 'VIP')

@section('content')
  @if (Auth::guest())
    <a class="tap" href="{{ route('about') }}"><strong>About</strong></a>
    <a href="{{ url('/login') }}">Login</a>
    <a href="{{ url('/register') }}">Register</a>
  @else
    <a href="{{ route('admin.dashboard') }}"><strong>DASHBOARD</strong></a>
    <a href="{{ route('restaurants.index') }}">Restos</a>
    <a href="{{ route('test') }}"><strong>Test</strong></a>
    <a href="{{ route('laravel') }}"><strong>Laravel</strong></a>
    {{ Auth::user()->name }}
    <a href="{{ route('logout') }}"
        onclick="event.preventDefault();
                 document.getElementById('logout-form').submit();">
        Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
  @endif
@endsection
