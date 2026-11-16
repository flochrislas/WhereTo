<nav id="header">
  <!-- Branding -->
  <div class="left">
      <a class="title" href="{{ url('/') }}">
          {{ config('app.name', 'FindItFast') }}
      </a>
  </div>

  <div class="right" style="font-size:.7rem">
      <i>SHIBUYA beta version</i>
  </div>

  <!-- TODO: Implement this -->
  <div class="right" style="display: none">
      <a class="tap" href="{{ route('info') }}"><strong>Info</strong></a>
      <a class="tap" href="{{ route('vip') }}"><strong>VIP</strong></a>
  </div>

  <!-- Admin links, hidden in the public design -->
  <div class="right" style="display: none">
    @auth
      <a href="{{ route('admin.dashboard') }}"><strong>DASHBOARD</strong></a>
      <a href="{{ route('restaurants.index') }}">Restos</a>
      {{ Auth::user()->name }}
      <a href="{{ route('logout') }}"
          onclick="event.preventDefault();
                   document.getElementById('logout-form').submit();">
          Logout
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
      </form>
    @else
      <a href="{{ route('login') }}">Login</a>
    @endauth
  </div>
</nav>
