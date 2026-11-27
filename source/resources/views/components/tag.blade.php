{{-- A tag chip, the same markup wherever a tag is shown. data-tag is the English label: what the search uses, and
     how a tag of the details finds its twin in the filter (whereto.js, moveTag); the text is the label in the
     page's language. Three uses:
       filter:  a button of the tag cloud, selected and unselected by moveTag. A real button: works with the
                keyboard and screen readers; aria-pressed tells them if the tag is selected
       details: a button in the details of a place, which selects the same tag in the filter
       static:  plain text, on the standalone page of a place (no filter there)
     The icon, for the tags that have one (config/tag_icons.php), decorates the label: aria-hidden, so a screen
     reader says the tag once, by its text. One sprite for all the icons, fetched once (doc/13-tag-icons.md). --}}
@props(['tag', 'use' => 'static'])
@php($icon = \App\Support\Tags::icon($tag->label))
@php($label = tag_label($tag->label))
@if ($use === 'filter')
    <button type="button" class="tag tagType{{ $tag->type }}"
          tagId="{{ $tag->id }}"
          id="tagShow{{ $tag->id }}"
          data-tag="{{ $tag->label }}"
          aria-pressed="false"
          onClick="moveTag(event)">
      @if ($icon)<svg class="tagIcon" aria-hidden="true" focusable="false"><use href="{{ \App\Support\Tags::sprite() }}#{{ $icon }}"/></svg>@endif{{ $label }}
    </button>
@elseif ($use === 'details')
    <button type="button" class="tag tagType{{ $tag->type }}" data-tag="{{ $tag->label }}" title="{{ __('Add this tag to the filter') }}" onclick="moveTag(event)">
      @if ($icon)<svg class="tagIcon" aria-hidden="true" focusable="false"><use href="{{ \App\Support\Tags::sprite() }}#{{ $icon }}"/></svg>@endif{{ $label }}
    </button>
@else
    <span class="tag tagType{{ $tag->type }}" data-tag="{{ $tag->label }}">
      @if ($icon)<svg class="tagIcon" aria-hidden="true" focusable="false"><use href="{{ \App\Support\Tags::sprite() }}#{{ $icon }}"/></svg>@endif{{ $label }}
    </span>
@endif
