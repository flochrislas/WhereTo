{{-- A tag chip, the same markup wherever a tag is shown. data-tag is the English label: what the search uses, and
     how a tag of the details finds its twin in the filter (whereto.js, moveTag); the text is the label in the
     page's language. Three uses:
       filter:  a button of the tag cloud, selected and unselected by moveTag. A real button: works with the
                keyboard and screen readers; aria-pressed tells them if the tag is selected
       details: a button in the details of a place, which selects the same tag in the filter
       static:  plain text, on the standalone page of a place (no filter there) --}}
@props(['tag', 'use' => 'static'])
@if ($use === 'filter')
    <button type="button" class="tag tagType{{ $tag->type }}"
          tagId="{{ $tag->id }}"
          id="tagShow{{ $tag->id }}"
          data-tag="{{ $tag->label }}"
          aria-pressed="false"
          onClick="moveTag(event)">
      {{ tag_label($tag->label) }}
    </button>
@elseif ($use === 'details')
    <button type="button" class="tag tagType{{ $tag->type }}" data-tag="{{ $tag->label }}" title="{{ __('Add this tag to the filter') }}" onclick="moveTag(event)">
      {{ tag_label($tag->label) }}
    </button>
@else
    <span class="tag tagType{{ $tag->type }}" data-tag="{{ $tag->label }}">
      {{ tag_label($tag->label) }}
    </span>
@endif
