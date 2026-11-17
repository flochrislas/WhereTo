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
</nav>
