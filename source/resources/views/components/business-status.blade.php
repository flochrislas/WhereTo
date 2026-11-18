{{-- The status of a place that is no longer in business, on its standalone page (doc/11-data-freshness.md, section 5).
     Prints nothing for an operational place. For a place gone for good (permanently closed, replaced) it also pushes
     a robots noindex into the layout's <head> (@stack('head')): the page stays reachable but has nothing to rank. --}}
@props(['place'])
{{-- @php(...) runs one PHP statement: keep the status in a variable for the lines below --}}
@php($status = $place->businessStatus())
@unless ($status->isOpen())
    @if ($status->isGone())
        {{-- @push adds to a stack the layout prints with @stack: the content section runs before the layout, so this
             reaches the <head> even though it is written here --}}
        @push('head')
            <meta name="robots" content="noindex">
        @endpush
    @endif
    {{-- ucfirst: "permanently closed" becomes "Permanently closed" --}}
    <p class="businessStatus">{{ ucfirst($status->label()) }}</p>
@endunless
