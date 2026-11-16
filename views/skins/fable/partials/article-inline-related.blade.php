{{--
    The small "Related" card inside the body (the drawing puts it after the body's second paragraph): a thumbnail, the
    word "Related" and the story's title. Takes $entry (one of the article's related: ['item' => ListingItem,
    'reason' => string]). Drawn by article-body when the article's inline-related setting is on.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $related = $entry['item'];
@endphp
<aside class="story inline-related" aria-label="{{ __('Related') }}">
    @if ($related->image !== null)
        <div class="media"><x-teme::image :id="$related->image->id" context="thumb" :decorative="true" /></div>
    @else
        <div class="media" aria-hidden="true"></div>
    @endif
    <div>
        <p class="kicker">{{ __('Related') }}</p>
        <p class="hed hed-sm mt-1"><a href="{{ $related->url }}">{{ $related->title }}</a></p>
    </div>
</aside>
