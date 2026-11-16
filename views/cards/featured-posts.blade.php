{{--
    Fable: core's "Featured posts" card, drawn the theme's way: an optional heading, then each story through the
    theme's own card. Every featured-posts card on the site uses this one file, in every region, and it cannot know
    which region it is in, so it draws a plain, well-structured list and lets the page's wrapper class change its
    look (see fable-home.css: .region-also-today, .region-long-read, .region-sections and .region-most-read).

    Takes $heading (?string), $items (list of ListingItem) and $images (bool, whether the editor wants pictures).
    Headings: the editor's heading is an h2 and the stories under it are h3; with no heading the stories are h2.
    The list is a <ul>; a ranked look (the "most read" numbers) is drawn by CSS counters, so it adds no words.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $level = ($heading ?? null) !== null && $heading !== '' ? 3 : 2;
@endphp
@if (count($items) > 0)
    <section class="card-featured-posts{{ $images ? '' : ' fp-noimg' }}">
        @if ($level === 3)
            <h2 class="hed fp-heading">{{ $heading }}</h2>
        @endif
        <ul class="fp-list">
            @foreach ($items as $item)
                <li class="fp-item">
                    @include('theme::skins.fable.partials.card', ['item' => $item, 'context' => 'thumb', 'level' => $level])
                </li>
            @endforeach
        </ul>
    </section>
@endif
