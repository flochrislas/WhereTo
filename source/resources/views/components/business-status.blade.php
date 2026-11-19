{{-- The status of a place that is no longer in business, on its standalone page (doc/11-data-freshness.md, section 5).
     Prints nothing for an operational place. The robots noindex of a place gone for good (permanently closed,
     replaced) comes from the indexing component (components/indexing.blade.php), with the languages rule. --}}
@props(['place'])
{{-- @php(...) runs one PHP statement: keep the status in a variable for the lines below --}}
@php($status = $place->businessStatus())
@unless ($status->isOpen())
    {{-- ucfirst: "permanently closed" becomes "Permanently closed" (the label is already in the page's language) --}}
    <p class="businessStatus">{{ ucfirst($status->label()) }}</p>
@endunless
