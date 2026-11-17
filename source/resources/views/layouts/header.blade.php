<nav id="header">
  <!-- Branding -->
  <div class="left">
      <a class="title" href="{{ url('/') }}">
          {{ config('app.name', 'FindItFast') }}
      </a>
  </div>

  <div class="right" style="font-size:.7rem">
      <i>SHIBUYA beta version</i>
      <!-- Dark/light switch; whereto.js fills the icon and the label for the current theme -->
      <button id="themeButton" class="themeButton" type="button" onclick="toggleTheme()" aria-label="Switch theme">&#9790;</button>
  </div>

  <!-- TODO: Implement this -->
  <div class="right" style="display: none">
      <a class="tap" href="{{ route('info') }}"><strong>Info</strong></a>
      <a class="tap" href="{{ route('vip') }}"><strong>VIP</strong></a>
  </div>
</nav>
