<nav id="header">
  <!-- Branding Image -->
  <div>
      <a href="{{ url('/') }}">
          {{ config('app.name', 'WhereTo') }}
      </a>
  </div>

  <div>
      <!-- Left Side Of Navbar -->
      @if (!Auth::guest())
        <ul>
            <li><a href="{{ route('admin.dashboard') }}"><strong>DASHBOARD</strong></a></li>
            <li><a href="{{ route('restaurants.index') }}">Restos</a></li>
            <li><a href="{{ route('test') }}"><strong>Test</strong></a></li>
            <li><a href="{{ route('laravel') }}"><strong>Laravel</strong></a></li>
        </ul>
      @endif

      <!-- Right Side Of Navbar -->
      <ul>
          <!-- Authentication Links -->
          @if (Auth::guest())
              <li><a href="{{ url('/login') }}">Login</a></li>
              <li><a href="{{ url('/register') }}">Register</a></li>
          @else
              <li>
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                      {{ Auth::user()->name }} <span class="caret"></span>
                  </a>

                  <ul role="menu">
                      <li>
                          <a href="{{ route('logout') }}"
                              onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                              Logout
                          </a>

                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              {{ csrf_field() }}
                          </form>
                      </li>
                  </ul>
              </li>
          @endguest
          <li><a href="{{ route('about') }}"><strong>About</strong></a></li>
      </ul>
  </div>
</nav>
