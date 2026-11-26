{{-- The sentence is cut at its :place placeholder, where the link goes, so each language orders it its own way and
     the text stays escaped. The type ("Ramen") is translated like a tag (the restaurant types are tag words, lang/ja/tags.php);
     the name is the Japanese one on a Japanese page when the place has one, else the English one (then lang="en") --}}
@php([$before, $after] = explode(':place', __('How about a nice :type restaurant, :place?', ['type' => tag_label(strtolower($place->type ?? ''))]), 2) + [1 => ''])
@php($translatedName = $place->translatedName(app()->getLocale()))
@if ($translatedName === null)
{{ $before }}<a href="{{ \App\Support\LocaleUrl::route('restaurants.details.single', $place->id) }}" lang="en">{{ $place->name }}</a>{{ $after }}
@else
{{ $before }}<a href="{{ \App\Support\LocaleUrl::route('restaurants.details.single', $place->id) }}">{{ $translatedName }}</a>{{ $after }}
@endif
