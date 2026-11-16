{{--
    An article's byline row: the lead author's initials, "By" and the names, then the date, the reading time and the
    update, with the share buttons at the end. Takes $page (ArticleView), $showReadingTime and $showShare (both read by
    the article). Nothing at all when every part is off, so no empty bordered band is left.

    Settings this partial reads (once, below; the default is the drawing's):
      fable_byline_avatar        on/off, on   the lead author's initials circle (needs an author)
      fable_byline_authors       on/off, on   "By" and the author names (needs an author)
      fable_byline_date          on/off, on   "Published <date>"
      fable_article_show_updated on/off, on   "Updated <date>" after the date
      show_updated_date          on/off, on   the same, site-wide: "Updated" shows only while both are on
      updated_after_days         1, 7, 14 or 30  how many days after publishing an edit counts as an update (1)

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $authors = $page->authors;
    $showAvatar = (bool) ($themeSettings['fable_byline_avatar'] ?? true) && $authors !== [];
    $showNames = (bool) ($themeSettings['fable_byline_authors'] ?? true) && $authors !== [];
    $showDate = (bool) ($themeSettings['fable_byline_date'] ?? true);
    $showUpdated = (bool) ($themeSettings['fable_article_show_updated'] ?? true) && (bool) ($themeSettings['show_updated_date'] ?? true);
    $updatedAfterDays = (int) ($themeSettings['updated_after_days'] ?? 1);

    // Core gives an update from a day after publishing; the site's rule may ask for longer.
    $isUpdated = $showUpdated && $page->updatedText !== null && $page->updated !== null
        && ($page->updated->getTimestamp() - $page->published->getTimestamp()) >= $updatedAfterDays * 86400;

    // The dated line's parts, in order, joined with a dot.
    $facts = [];
    if ($showDate) {
        $facts[] = '<time datetime="'.e($page->published->format(DATE_ATOM)).'">'.e(__('Published :date', ['date' => $page->publishedText])).'</time>';
    }
    if ($showReadingTime) {
        $facts[] = e(trans_choice(':count min read', $page->readingMinutes, ['count' => $page->readingMinutes]));
    }
    if ($isUpdated) {
        $facts[] = '<time datetime="'.e($page->updated->format(DATE_ATOM)).'">'.e(__('Updated :date', ['date' => $page->updatedText])).'</time>';
    }
    $hasDetails = $showAvatar || $showNames || $facts !== [];
@endphp
@if ($hasDetails || $showShare)
<div class="mt-7 flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-y border-rule py-4">
    @if ($hasDetails)
        <div class="flex items-center gap-3.5">
            @if ($showAvatar)
                @include('theme::skins.fable.partials.article-avatar', ['name' => $authors[0]->name, 'class' => 'w-[3.25rem] h-[3.25rem] text-lg'])
            @endif
            <div>
                @if ($showNames)
                    <p class="text-[.9375rem] font-bold">{{ __('By') }}
                        @foreach ($authors as $author)
                            @if ($author->url !== '')<a href="{{ $author->url }}" rel="author" class="text-rail hover:underline underline-offset-4">{{ $author->name }}</a>@else{{ $author->name }}@endif{{ $loop->last ? '' : ($loop->remaining === 1 ? ' '.__('and').' ' : ', ') }}
                        @endforeach
                    </p>
                @endif
                @if ($facts !== [])
                    <p class="meta{{ $showNames ? ' mt-0.5' : '' }}">{!! implode(' · ', $facts) !!}</p>
                @endif
            </div>
        </div>
    @endif
    @if ($showShare)
        @include('theme::skins.fable.partials.article-share', ['page' => $page])
    @endif
</div>
@endif
