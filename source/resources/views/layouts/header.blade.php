<nav id="header">
  <!-- Branding Image -->
  <div class="left">
      <a class="title" href="{{ url('/') }}">
          {{ config('app.name', 'WhereTo') }}
      </a>
  </div>

  <div class="right">
      <a class="tap" href="{{ route('info') }}"><strong>Info</strong></a>
      <a class="tap" href="{{ route('vip') }}"><strong>VIP</strong></a>
  </div>

  <!-- Decide what to do with those -->
  <div class="right" style="display: none">
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
  </div>

</nav>
