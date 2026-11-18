{{-- The JSON-LD block of a place page (doc/10-seo.md, item 5). Printed raw on purpose: JSON must not be HTML-escaped,
     and StructuredData::json() already encodes < > & so nothing from the database can close the script tag.
     The architecture tests allow the raw syntax and the inline script in this file only.
     Nothing for a place gone for good (permanently closed, replaced): the markup describes a live business with our
     review, and the page is noindex anyway. A temporarily closed place keeps it. --}}
@props(['place'])
@unless ($place->businessStatus()->isGone())
<script type="application/ld+json">{!! \App\Support\StructuredData::json($place->structuredData(\App\Support\PublicUrl::to(request()->path()))) !!}</script>
@endunless
