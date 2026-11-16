{{--
    The "Keep reading" row under the article: the related stories as cards, with a link to the article's category.
    Takes $entries (a list of ['item' => ListingItem, 'reason' => string]), $heading, and $category (the article's
    TermLink, or null).

    @package egberipiribo
    @since   0.1.0
--}}
<section class="pt-8 pb-16" aria-labelledby="related-title">
    <div class="flex items-baseline justify-between gap-4 border-t border-ink pt-5">
        <h2 id="related-title" class="hed hed-lg">{{ $heading }}</h2>
        @if ($category !== null)
            <a href="{{ $category->url }}" class="text-link text-sm">{{ __('All :name', ['name' => $category->name]) }}@include('theme::icons.arrow-right')</a>
        @endif
    </div>
    <div class="grid sm:grid-cols-2 {{ count($entries) > 2 ? 'xl:grid-cols-3' : '' }} gap-x-6 gap-y-6 sm:gap-y-10 mt-7">
        @foreach ($entries as $entry)
            @include('theme::skins.fable.partials.card', ['item' => $entry['item'], 'level' => 3, 'context' => 'card'])
        @endforeach
    </div>
</section>
