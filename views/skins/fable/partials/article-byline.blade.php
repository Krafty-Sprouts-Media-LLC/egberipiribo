{{--
    An article's byline row: the lead author's initials, "By" and the names, then the date, the reading time and the
    update, with the share buttons at the end. Takes $page (ArticleView), $showReadingTime and $showShare.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $authors = $page->authors;
@endphp
<div class="mt-7 flex flex-col sm:flex-row sm:items-center justify-between gap-5 border-y border-rule py-4">
    <div class="flex items-center gap-3.5">
        @if ($authors !== [])
            @include('theme::skins.fable.partials.article-avatar', ['name' => $authors[0]->name, 'class' => 'w-[3.25rem] h-[3.25rem] text-lg'])
        @endif
        <div>
            @if ($authors !== [])
                <p class="text-[.9375rem] font-bold">{{ __('By') }}
                    @foreach ($authors as $author)
                        @if ($author->url !== '')<a href="{{ $author->url }}" rel="author" class="text-rail hover:underline underline-offset-4">{{ $author->name }}</a>@else{{ $author->name }}@endif{{ $loop->last ? '' : ($loop->remaining === 1 ? ' '.__('and').' ' : ', ') }}
                    @endforeach
                </p>
            @endif
            <p class="meta mt-0.5"><time datetime="{{ $page->published->format(DATE_ATOM) }}">{{ __('Published :date', ['date' => $page->publishedText]) }}</time>@if ($showReadingTime) · {{ trans_choice(':count min read', $page->readingMinutes, ['count' => $page->readingMinutes]) }}@endif
                @if ($page->updatedText !== null) · <time datetime="{{ $page->updated?->format(DATE_ATOM) }}">{{ __('Updated :date', ['date' => $page->updatedText]) }}</time>@endif</p>
        </div>
    </div>
    @if ($showShare)
        @include('theme::skins.fable.partials.article-share', ['page' => $page])
    @endif
</div>
