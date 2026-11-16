{{--
    The "Read next" strip after the body: one related story with the reason core gives for it. Takes $entry (one of
    the article's related: ['item' => ListingItem, 'reason' => string]). The article draws it only while its
    show_read_next setting is on (it reads that one, since it also decides which stories the other blocks list).

    Settings this partial reads (once, below; the default is the drawing's):
      fable_read_next_reason  on/off, on  the small line giving the reason under the headline

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $next = $entry['item'];
    $showReason = (bool) ($themeSettings['fable_read_next_reason'] ?? true);
@endphp
<article class="story grid grid-cols-[6.5rem_minmax(0,1fr)] sm:grid-cols-[11rem_minmax(0,1fr)] gap-4 sm:gap-6 items-center bg-surface rounded-[var(--radius-box)] p-3 sm:p-4">
    @if ($next->image !== null)
        <div class="media aspect-[4/3]"><x-teme::image :id="$next->image->id" context="thumb" :decorative="true" /></div>
    @else
        <div class="media aspect-[4/3]" aria-hidden="true"></div>
    @endif
    <div class="pr-2">
        <p class="kicker">{{ __('Read next') }}</p>
        <h2 class="hed hed-md sm:text-[1.5rem] mt-1.5"><a href="{{ $next->url }}">{{ $next->title }}</a></h2>
        @if ($showReason && ($entry['reason'] ?? '') !== '')
            <p class="meta mt-2">{{ $entry['reason'] }}</p>
        @endif
    </div>
</article>
