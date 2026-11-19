{{-- The JSON-LD block of a place page (doc/10-seo.md, item 5). Printed raw on purpose: JSON must not be HTML-escaped,
     and StructuredData::json() already encodes < > & so nothing from the database can close the script tag.
     The architecture tests allow the raw syntax and the inline script in this file only.
     Only on a page the search engines may index (Place::isIndexableIn() for the page's language): nothing for a place
     gone for good (permanently closed, replaced: the markup describes a live business with our review), nor on a
     Japanese page without Japanese content (noindex, and its URL must not be named as the place's page).
     A temporarily closed place keeps it. --}}
@props(['place'])
@if ($place->isIndexableIn(app()->getLocale()))
<script type="application/ld+json">{!! \App\Support\StructuredData::json($place->structuredData(\App\Support\PublicUrl::to(request()->path()))) !!}</script>
@endif
