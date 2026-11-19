{{-- What the search engines may do with this page, language by language (doc/12-multilingual-ui.md, doc/10-seo.md).
     Without a place (home and filter pages): indexable in every language, so the <head> gets one hreflang link per
     language plus x-default (the English page, for any other visitor). With a place: only the languages the place
     is indexable in (Place::isIndexableIn(): not gone for good, has content in that language). A page that is not
     indexable in its own language gets a robots noindex instead, and is in no hreflang list. A page written in some
     languages only says which with :languages (the English-only info page: <x-indexing :languages="['en']" />).
     Both go into the layout's <head> through @push('head'). --}}
@props(['place' => null, 'languages' => null])
{{-- ?? : every language of the site unless the page gave its own list --}}
@php($locales = $languages ?? \App\Support\LocaleUrl::locales())
{{-- array_filter keeps the languages the function says yes to; array_values renumbers them 0, 1, 2... --}}
@php($indexable = $place === null ? $locales : array_values(array_filter($locales, fn (string $locale) => $place->isIndexableIn($locale))))
@if (! in_array(app()->getLocale(), $indexable, true))
    @push('head')
        <meta name="robots" content="noindex">
    @endpush
@elseif (count($indexable) > 1)
    {{-- One version only (an English place page today): nothing to pair it with, no hreflang --}}
    @php($alternates = \App\Support\LocaleUrl::alternates($indexable))
    @push('head')
        @foreach ($alternates as $locale => $url)
            <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
        @endforeach
        @isset($alternates[\App\Support\LocaleUrl::default()])
            <link rel="alternate" hreflang="x-default" href="{{ $alternates[\App\Support\LocaleUrl::default()] }}">
        @endisset
    @endpush
@endif
