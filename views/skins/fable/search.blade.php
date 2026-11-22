{{--
    Fable: search. The drawing's search header (the one h1, the form, how many were found) and its results as rows,
    each with the snippet where the matches are marked, then the pages. The drawing's live client search becomes a
    server-rendered one: the form is a plain GET to the site's address with the field `s`, so a result page is
    /?s=word. Empty states: a blank query, and a query with no results; both offer the form and the sections.

    Takes $page, a ListingView of kind search: ->subject->title, ->search (SearchDetails: query, results, total,
    totalCapped, empty), ->pagination. The results are $page->search->results, not $page->items.

    Left out because Teme cannot give it: the facets by section, the sort control, and the "Most read this week"
    list under the empty states (no popularity data).

    Settings this template reads (each once, at the top):
      fable_search_form              bool, true    the search field under the results heading
      fable_search_count             bool, true    the "N results" line
      fable_search_snippets          bool, true    the matched passage under each result, words marked (off: the
                                                   card shows the story's standfirst instead, if it has one)
      search_show_sections           bool, true    the section chips in the empty states
      fable_search_ad_leaderboard    bool, false   a leaderboard under the header (the drawing has none)
      fable_search_ad_in_feed        bool, false   an ad at the end of the results list (the drawing has none)
    Not read here: search_placeholder (string, in partials.listing-search-form) and the master show_ads (the ad
    partial's).

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'search')

@php
    $showForm = (bool) ($themeSettings['fable_search_form'] ?? true);
    $showCount = (bool) ($themeSettings['fable_search_count'] ?? true);
    $showSnippets = (bool) ($themeSettings['fable_search_snippets'] ?? true);
    $showSections = (bool) ($themeSettings['search_show_sections'] ?? true);
    $showAdLeaderboard = (bool) ($themeSettings['fable_search_ad_leaderboard'] ?? false);
    $showAdInFeed = (bool) ($themeSettings['fable_search_ad_in_feed'] ?? false);

    $search = $page->search;
    $query = $search?->query ?? '';
    $blank = $search === null || $search->empty;
    $results = $search?->results ?? [];
    $heading = $page->subject?->title ?? ($blank ? __('Search :site', ['site' => $site->name]) : __('Results for “:query”', ['query' => $query]));

    $countText = '';
    if (! $blank && $results !== []) {
        $formatted = \Illuminate\Support\Number::format($search->total, locale: app()->getLocale());
        $countText = $search->totalCapped
            ? __('More than :count results', ['count' => $formatted])
            : ($search->total === 1 ? __('1 result') : __(':count results', ['count' => $formatted]));
    }
@endphp

@section('main')
    <header class="pt-8 pb-6 border-b border-ink">
        <h1 class="hed hed-page">{{ $heading }}</h1>
        @if ($showForm)
            @include('theme::skins.fable.partials.listing-search-form', ['id' => 'search-term', 'value' => $query])
        @endif
        @if ($showCount && $countText !== '')
            <p class="meta mt-5">{{ $countText }}</p>
        @endif
    </header>

    @if ($showAdLeaderboard)
        @include('theme::skins.fable.partials.ad', ['region' => 'ad-leaderboard', 'class' => 'mt-7'])
    @endif

    @if ($blank)
        <section class="py-12 pb-16" aria-labelledby="search-blank">
            <h2 id="search-blank" class="hed hed-lg">{{ __('Type a word to start searching') }}</h2>
            <p class="deck text-[1.125rem] mt-3 max-w-[34rem]">{{ __('Search the titles and text of every story, or pick a section.') }}</p>
            @if ($showSections)
                @include('theme::skins.fable.partials.listing-sections', ['class' => 'mt-6'])
            @endif
        </section>
    @elseif ($results === [])
        <section class="py-12 pb-16" aria-labelledby="search-none">
            <h2 id="search-none" class="hed hed-lg">{{ __('No stories match that search') }}</h2>
            <p class="deck text-[1.125rem] mt-3 max-w-[34rem]">{{ __('Check the spelling, try one shorter word, or pick a section below.') }}</p>
            @if ($showSections)
                @include('theme::skins.fable.partials.listing-sections', ['class' => 'mt-6'])
            @endif
        </section>
    @else
        <section class="pb-16" aria-label="{{ __('Search results') }}">
            <div class="listing-rows">
                @foreach ($results as $result)
                    @include('theme::skins.fable.partials.card', ['item' => $result->item, 'level' => 2, 'snippetHtml' => ($showSnippets && $result->snippetHtml !== '') ? $result->snippetHtml : null])
                    @include('theme::skins.fable.partials.between', ['after' => $loop->iteration, 'last' => $loop->last])
                @endforeach

                @if ($showAdInFeed)
                    @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-feed'])
                @endif
            </div>

            @include('theme::skins.fable.partials.pagination', ['pagination' => $page->pagination])
        </section>
    @endif
@endsection
