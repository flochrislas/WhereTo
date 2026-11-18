{{-- The JSON-LD block of a place page (doc/10-seo.md, item 5). Printed raw on purpose: JSON must not be HTML-escaped,
     and StructuredData::json() already encodes < > & so nothing from the database can close the script tag.
     The architecture tests allow the raw syntax and the inline script in this file only. --}}
@props(['data'])
<script type="application/ld+json">{!! \App\Support\StructuredData::json($data) !!}</script>
