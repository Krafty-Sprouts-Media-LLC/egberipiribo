{{--
    Fable: archive. One template for a section or topic (a term), a date and a content type: the drawing's section
    header (breadcrumb, kind label, the one h1, description, topic chips), a leaderboard advertisement, the lead
    story of the section, then the rest as a grid or list the reader chooses, an in-feed advertisement and the
    pages.

    Takes $page, a ListingView: ->subject (kind, title, description, image, taxonomy, parent, children), ->items,
    ->pagination, ->breadcrumbs. The lead story is the first item of page 1 only; later pages are the grid alone.

    Left out because Teme cannot give it: the story count (only this page's items are known, never the total), the
    sort control (a listing has one order, newest first) and "show more" (real pagination replaces it).

    Settings this template reads (each once, at the top; a switch whose parent is off is ignored):
      fable_archive_breadcrumb      bool, true   the breadcrumb above the kind label
      fable_archive_kind_label      bool, true   the small label ("Section", "Topic", "Writer") above the title
      fable_archive_description     bool, true   the subject's description under the title
      archive_header_image          bool, true   the subject's own image beside the header, when it has one
      archive_topic_chips           bool, true   a term's child topics as chips in the header
      fable_archive_chip_all        bool, true   the "All" chip that starts the row of topics (needs the chips)
      archive_ads                   bool, true   both advertisement slots (leaderboard and in-feed)
      fable_archive_ad_leaderboard  bool, true   the leaderboard under the header (needs archive_ads)
      fable_archive_ad_in_feed      bool, true   the wide ad at the end of the grid (needs archive_ads)
      archive_lead_story            bool, true   the first story of page 1 is set large above the grid
      fable_archive_lead_standfirst bool, true   the lead story's standfirst (needs the lead story)
      fable_archive_lead_byline     bool, true   the lead story's byline (needs the lead story)
      archive_layout_toggle         bool, true   the reader's grid / list buttons
    The master switch show_ads belongs to the ad partial; this page reads only its own per-spot switches.

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'archive')

@php
    $showBreadcrumb = (bool) ($themeSettings['fable_archive_breadcrumb'] ?? true);
    $showKindLabel = (bool) ($themeSettings['fable_archive_kind_label'] ?? true);
    $showDescription = (bool) ($themeSettings['fable_archive_description'] ?? true);
    $showHeaderImage = (bool) ($themeSettings['archive_header_image'] ?? true);
    $showChips = (bool) ($themeSettings['archive_topic_chips'] ?? true);
    $showChipAll = $showChips && (bool) ($themeSettings['fable_archive_chip_all'] ?? true);
    $showAds = (bool) ($themeSettings['archive_ads'] ?? true);
    $showAdLeaderboard = $showAds && (bool) ($themeSettings['fable_archive_ad_leaderboard'] ?? true);
    $showAdInFeed = $showAds && (bool) ($themeSettings['fable_archive_ad_in_feed'] ?? true);
    $showLead = (bool) ($themeSettings['archive_lead_story'] ?? true);
    $showLeadStandfirst = $showLead && (bool) ($themeSettings['fable_archive_lead_standfirst'] ?? true);
    $showLeadByline = $showLead && (bool) ($themeSettings['fable_archive_lead_byline'] ?? true);
    $showToggle = (bool) ($themeSettings['archive_layout_toggle'] ?? true);

    $subject = $page->subject;
    $title = $subject?->title ?? $site->name;
    $kindLabel = match ($subject?->kind) {
        'term' => ($subject->taxonomy === 'category' && $subject->parent === null) ? __('Section') : __('Topic'),
        'author' => __('Writer'),
        'date' => __('Archive'),
        'type' => __('Collection'),
        default => __('Latest'),
    };

    $crumbs = $page->breadcrumbs;
    if (! is_array($crumbs) || count($crumbs) < 2) {
        $crumbs = [(object) ['name' => $site->name, 'url' => $site->url], (object) ['name' => $title, 'url' => '']];
    }

    $items = array_values($page->items);
    $lead = ($showLead && $page->pagination->current === 1) ? ($items[0] ?? null) : null;
    $rest = $lead !== null ? array_slice($items, 1) : $items;
    $children = $showChips ? ($subject?->children ?? []) : [];
    $headerImage = $showHeaderImage ? $subject?->image : null;
@endphp

@section('main')
    <header class="pt-8 pb-7 border-b border-ink">
        @include('theme::skins.fable.partials.pattern', ['area' => 'sections', 'mode' => 'band'])
        @if ($showBreadcrumb)
            @include('theme::skins.fable.partials.breadcrumbs', ['crumbs' => $crumbs])
        @endif
        <div class="{{ $showBreadcrumb ? 'mt-4 ' : '' }}grid gap-x-10 gap-y-5 items-end {{ $headerImage !== null ? 'lg:grid-cols-[minmax(0,1fr)_20rem]' : '' }}">
            <div class="min-w-0">
                @if ($showKindLabel)
                    <p class="kicker">{{ $kindLabel }}</p>
                @endif
                <h1 class="hed hed-page{{ $showKindLabel ? ' mt-2' : '' }}">{{ $title }}</h1>
                @if ($showDescription && ($subject?->description ?? '') !== '')
                    <p class="deck text-[1.125rem] mt-3 max-w-[40rem]">{{ $subject->description }}</p>
                @endif
                @if ($children !== [])
                    <ul class="flex gap-2 mt-5 overflow-x-auto [scrollbar-width:none] -mx-1 px-1 py-1" aria-label="{{ __('Topics in :title', ['title' => $title]) }}">
                        @if ($showChipAll && $subject?->url !== null)
                            <li><a href="{{ $subject->url }}" class="chip" aria-current="page">{{ __('All') }}</a></li>
                        @endif
                        @foreach ($children as $child)
                            <li><a href="{{ $child->url }}" class="chip">{{ $child->name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if ($headerImage !== null)
                <div class="media aspect-[16/10]"><x-teme::image :id="$headerImage->id" context="card" :decorative="true" /></div>
            @endif
        </div>
    </header>

    @if ($showAdLeaderboard)
        @include('theme::skins.fable.partials.ad', ['region' => 'ad-leaderboard', 'class' => 'mt-7'])
    @endif

    @if ($lead !== null)
        <article class="story grid {{ $lead->image !== null ? 'lg:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]' : '' }} gap-x-10 gap-y-5 items-center mt-9" aria-label="{{ __('Latest in :title', ['title' => $title]) }}">
            @if ($lead->image !== null)
                <div class="media aspect-[16/10]"><x-teme::image :id="$lead->image->id" context="hero" :priority="true" :decorative="true" /></div>
            @endif
            <div class="min-w-0">
                <p class="kicker">{{ __('Latest') }}</p>
                <h2 class="hed hed-xl mt-2.5"><a href="{{ $lead->url }}">{{ $lead->title }}</a></h2>
                @if ($showLeadStandfirst && ($lead->standfirst ?? '') !== '')
                    <p class="deck text-[1.125rem] mt-3.5">{{ $lead->standfirst }}</p>
                @endif
                @if ($showLeadByline)
                    <p class="meta mt-3.5">@include('theme::skins.fable.partials.byline', ['authors' => $lead->authors, 'publishedText' => $lead->publishedText, 'published' => $lead->published])</p>
                @endif
            </div>
        </article>
    @endif

    @if ($rest !== [])
        <section class="pt-12 pb-16" aria-labelledby="listing-title" x-data="layoutToggle" data-columns="3">
            <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 border-t border-ink pt-5">
                <h2 id="listing-title" class="hed hed-lg">{{ __('All stories in :title', ['title' => $title]) }}</h2>
                @if ($showToggle)
                    <div class="seg hidden sm:inline-flex" role="group" aria-label="{{ __('Layout') }}" x-cloak>
                        <button type="button" :aria-pressed="isGrid" @click="setGrid" aria-label="{{ __('Grid layout') }}">@include('theme::icons.squares-four', ['class' => 'text-base'])</button>
                        <button type="button" :aria-pressed="isList" @click="setList" aria-label="{{ __('List layout') }}">@include('theme::icons.rows', ['class' => 'text-base'])</button>
                    </div>
                @endif
            </div>

            <div class="listing-feed mt-7" :class="listClass">
                @foreach ($rest as $item)
                    @include('theme::skins.fable.partials.card', ['item' => $item, 'bind' => ':class="rowClass"'])
                    @include('theme::skins.fable.partials.between', ['after' => $loop->iteration, 'last' => $loop->last])
                @endforeach

                @if ($showAdInFeed)
                    @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-feed', 'class' => 'py-2 sm:py-0'])
                @endif
            </div>

            @include('theme::skins.fable.partials.pagination', ['pagination' => $page->pagination])
        </section>
    @elseif ($lead !== null)
        <div class="pb-16">
            @include('theme::skins.fable.partials.pagination', ['pagination' => $page->pagination])
        </div>
    @else
        <section class="py-12 pb-16" aria-labelledby="listing-empty">
            <h2 id="listing-empty" class="hed hed-lg">{{ __('Nothing here yet') }}</h2>
            <p class="deck text-[1.125rem] mt-3 max-w-[34rem]">{{ __('No stories have been published here. Search for something, or pick a section.') }}</p>
            @include('theme::skins.fable.partials.listing-search-form', ['id' => 'archive-search', 'class' => 'mt-6 max-w-[36rem]'])
            @include('theme::skins.fable.partials.listing-sections', ['class' => 'mt-6'])
        </section>
    @endif
@endsection
