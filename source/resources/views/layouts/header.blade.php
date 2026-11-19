<nav id="header">
  <!-- Branding -->
  <div class="left">
      <a class="title" href="{{ \App\Support\LocaleUrl::route('home') }}">
          {{ config('app.name', 'FindItFast') }}
      </a>
  </div>

  <div class="right" style="font-size:.7rem">
      <i>{{ __('SHIBUYA beta version') }}</i>
      <!-- Language switch: the same page in each other language. data-twin-base: whereto.js adds the state of a
           filter page (?tags=...&place=12) to the link as it changes; data-banner and data-close: the words of
           the language banner, in that language (whereto.js shows it when the browser prefers that language) -->
      @foreach (\App\Support\LocaleUrl::locales() as $locale)
          @if ($locale !== app()->getLocale())
              <a class="langSwitch" href="{{ \App\Support\LocaleUrl::twin($locale) }}" hreflang="{{ $locale }}" lang="{{ $locale }}"
                 data-twin-base="{{ \App\Support\LocaleUrl::twin($locale) }}"
                 data-banner="{{ __('Read this page in :language', ['language' => \App\Support\LocaleUrl::nativeName($locale)], $locale) }}"
                 data-close="{{ __('Close', [], $locale) }}">{{ \App\Support\LocaleUrl::nativeName($locale) }}</a>
          @endif
      @endforeach
      <!-- Dark/light switch; whereto.js fills the icon and the label for the current theme -->
      <button id="themeButton" class="themeButton" type="button" onclick="toggleTheme()" aria-label="{{ __('Switch theme') }}">&#9790;</button>
  </div>

  <!-- TODO: Implement this -->
  <div class="right" style="display: none">
      <a class="tap" href="{{ \App\Support\LocaleUrl::route('info') }}"><strong>Info</strong></a>
      <a class="tap" href="{{ \App\Support\LocaleUrl::route('vip') }}"><strong>VIP</strong></a>
  </div>
</nav>
