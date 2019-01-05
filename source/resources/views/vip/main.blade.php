@extends('layouts.app')

@section('title', 'VIP')

@section('content')

  <style type="text/css">
    .flexColumns {
      display: flex;
      flex-direction: column;
    }
    .loginContainer {
      border-color: grey;
      border-style:  solid;
      border-width: .2rem;
      border-radius: 1rem;
      padding: .3rem;
      margin-top: 1.5rem;
    }
    .registerContainer {
      border-color: grey;
      border-style:  solid;
      border-width: .2rem;
      border-radius: 1rem;
      padding: .3rem;
      margin-top: 1.5rem;
    }
  </style>

  @if (Auth::guest())
    <div class="flexColumns">
        <div class="loginContainer">


  <form method="POST" action="{{ route('login') }}">
      {{ csrf_field() }}

      <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <label for="email">E-Mail Address</label>

          <div>
              <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

              @if ($errors->has('email'))
                  <span class="help-block">
                      <strong>{{ $errors->first('email') }}</strong>
                  </span>
              @endif
          </div>
      </div>

      <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <label for="password">Password</label>

          <div>
              <input id="password" type="password" name="password" required>

              @if ($errors->has('password'))
                  <span class="help-block">
                      <strong>{{ $errors->first('password') }}</strong>
                  </span>
              @endif
          </div>
      </div>

      <div class="form-group">
          <div class="col-md-6 col-md-offset-4">
              <div class="checkbox">
                  <label>
                      <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                  </label>
              </div>
          </div>
      </div>

      <div class="form-group">
          <div class="col-md-8 col-md-offset-4">
              <button type="submit" class="btn btn-primary">
                  Login
              </button>

              <a class="btn btn-link" href="{{ route('password.request') }}">
                  Forgot Your Password?
              </a>
          </div>
      </div>
  </form>




        </div>

        <div class="registerContainer">
            <a href="{{ url('/register') }}">Register</a>
        </div>
    </div>
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
