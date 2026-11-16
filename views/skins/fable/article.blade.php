{{--
    Fable: an article (a post, or any item the single template draws). Top to bottom, as the drawing has it: the reading
    bar, the banner ad (above <main>), the breadcrumb, the headline, deck, byline and share row, the hero image, the
    body with an ad inside it, the second ad, the after-content region, the "Read next" strip, the tags, the share row
    again, the author cards, the comments, and beside all of it on wide screens the sticky half-page ad. Under the
    article: the related stories. The rail's "In this story" list (the `toc` section) is built from the body's h2
    headings that carry an id.

    Takes $page (ArticleView), $site, $themeSettings and $isFront. There is exactly one <h1>, on the front page too.
    Every block leaves itself out when its data is missing: no hero without a lead image that is the featured image, no
    byline name without an author, no comments when they are off, no ad while its region is empty.

    The ad slots are the regions ad-leaderboard, ad-in-article, ad-in-article-2 and ad-half-page (each hides itself
    while empty); after-content is the site editors' place for a callout, a button, Custom HTML or featured posts.

    Choices a later update can turn into theme settings (read once, below; the default is the drawing's):
      show_reading_progress  on/off, on            the reading bar across the top
      show_share             on/off, on            the share buttons, at the byline and after the tags
      show_reading_time      on/off, on            "N min read" in the byline
      show_contents          on/off, on            the "In this story" list in the rail
      contents_min_headings  number, 2             the fewest h2 headings that earn the list
      article_lede           on/off, on            the body's first paragraph set larger, as the drawing's lede
      show_ads               on/off, on            every advertisement slot on the article
      ad_in_article_after    number, 4             the in-article ad sits after this many top-level blocks of the body
      ad_in_article_min      number, 8             the fewest top-level blocks a body needs to get the ad inside it
      show_read_next         on/off, on            the "Read next" strip (the first related story)
      show_tags              on/off, on            the "Filed under" tags
      show_author_card       on/off, on            the author cards
      show_related           on/off, on            the "Keep reading" row
      related_heading        text, "Keep reading"  the heading of that row
      show_comments          on/off, on            the comments, when Teme gives the article a thread

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@php
    $showProgress = (bool) ($themeSettings['show_reading_progress'] ?? true);
    $showShare = (bool) ($themeSettings['show_share'] ?? true);
    $showReadingTime = (bool) ($themeSettings['show_reading_time'] ?? true);
    $showContents = (bool) ($themeSettings['show_contents'] ?? true);
    $contentsMinHeadings = (int) ($themeSettings['contents_min_headings'] ?? 2);
    $ledeFirstParagraph = (bool) ($themeSettings['article_lede'] ?? true);
    $showAds = (bool) ($themeSettings['show_ads'] ?? true);
    $adInArticleAfter = (int) ($themeSettings['ad_in_article_after'] ?? 4);
    $adInArticleMin = (int) ($themeSettings['ad_in_article_min'] ?? 8);
    $showReadNext = (bool) ($themeSettings['show_read_next'] ?? true);
    $showTags = (bool) ($themeSettings['show_tags'] ?? true);
    $showAuthorCard = (bool) ($themeSettings['show_author_card'] ?? true);
    $showRelated = (bool) ($themeSettings['show_related'] ?? true);
    $relatedHeading = $themeSettings['related_heading'] ?? __('Keep reading');
    $showComments = (bool) ($themeSettings['show_comments'] ?? true);

    $hasCrumbs = is_array($page->breadcrumbs) && count($page->breadcrumbs) > 1;
    $readNext = $showReadNext ? ($page->related[0] ?? null) : null;
    // With the strip on, the row under the article lists the rest, so no story is offered twice.
    $moreRelated = $showRelated ? array_slice($page->related, $readNext !== null ? 1 : 0) : [];
    $hasLead = $page->lead !== null && $page->leadIsFeatured;
@endphp

@section('template', 'single')

@section('progress')
    @if ($showProgress)
        <div class="progress" data-progress aria-hidden="true"></div>
    @endif
@endsection

@section('before')
    @if ($showAds)
        @include('theme::skins.fable.partials.ad', ['region' => 'ad-leaderboard', 'class' => 'wrap pt-5'])
    @endif
@endsection

@section('main')
    <div class="article-cols">
        <article class="article-main pt-7 pb-6">
            @if ($hasCrumbs)
                @include('theme::skins.fable.partials.breadcrumbs', ['crumbs' => $page->breadcrumbs])
            @endif

            <header class="{{ $hasCrumbs ? 'mt-5' : '' }}">
                @if (! $hasCrumbs && $page->kicker !== null)
                    <p class="kicker mb-2"><a href="{{ $page->kicker->url }}" class="hover:underline underline-offset-4">{{ $page->kicker->name }}</a></p>
                @endif
                <h1 class="hed hed-page">{{ $page->title }}</h1>
                @if ($page->standfirst !== null && $page->standfirst !== '')
                    <p class="deck text-[1.3125rem] leading-[1.45] mt-5">{{ $page->standfirst }}</p>
                @endif
                @include('theme::skins.fable.partials.article-byline', ['page' => $page, 'showReadingTime' => $showReadingTime, 'showShare' => $showShare])
            </header>

            @if ($hasLead)
                <figure class="mt-7">
                    <div class="media aspect-[16/10]"><x-teme::image :id="$page->lead->id" context="hero" :priority="true" :alt="$page->lead->alt" :decorative="$page->lead->decorative" /></div>
                    @if (($page->lead->caption ?? '') !== '' || ($page->lead->credit ?? '') !== '')
                        <figcaption class="meta mt-2.5">{{ $page->lead->caption }}@if (($page->lead->credit ?? '') !== '') <span class="whitespace-nowrap">{{ $page->lead->credit }}</span>@endif</figcaption>
                    @endif
                </figure>
            @endif

            @include('theme::skins.fable.partials.article-fields', ['fields' => $page->shownFields('before'), 'class' => 'mt-7'])

            <div class="mt-9">
                @include('theme::skins.fable.partials.article-body', ['html' => $page->bodyHtml, 'after' => $adInArticleAfter, 'min' => $adInArticleMin, 'lede' => $ledeFirstParagraph, 'ads' => $showAds, 'contents' => $showContents, 'contentsMin' => $contentsMinHeadings])
            </div>

            @include('theme::skins.fable.partials.article-fields', ['fields' => $page->shownFields('after'), 'class' => 'mt-9'])

            @if ($showAds)
                @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-article-2', 'class' => 'my-10'])
            @endif

            <div class="article-after mt-10"><x-teme::region name="after-content" /></div>

            @if ($readNext !== null)
                <div class="mt-10">
                    @include('theme::skins.fable.partials.article-readnext', ['entry' => $readNext])
                </div>
            @endif

            @if ($showTags && $page->tags !== [])
                <div class="flex flex-wrap items-center gap-2 mt-9">
                    <span class="meta font-bold text-ink mr-1">{{ __('Filed under') }}</span>
                    @foreach ($page->tags as $tag)
                        <a href="{{ $tag->url }}" rel="tag" class="chip">{{ $tag->name }}</a>
                    @endforeach
                </div>
            @endif

            @if ($showShare)
                @include('theme::skins.fable.partials.article-share', ['page' => $page, 'label' => __('Share this story'), 'class' => 'mt-5'])
            @endif

            @if ($showAuthorCard)
                @include('theme::skins.fable.partials.article-authors', ['authors' => $page->authors])
            @endif

            @if ($showComments)
                @include('theme::skins.fable.partials.comments-thread', ['comments' => $page->comments])
            @endif
        </article>

        {{-- The sticky half-page ad: wide screens only, in a column of its own, never over the text. --}}
        <aside class="ad-side" aria-label="{{ __('Advertising') }}">
            @if ($showAds)
                <div class="sticky top-6">
                    @include('theme::skins.fable.partials.ad', ['region' => 'ad-half-page'])
                </div>
            @endif
        </aside>
    </div>

    @if ($moreRelated !== [])
        @include('theme::skins.fable.partials.article-related', ['entries' => $moreRelated, 'heading' => $relatedHeading, 'category' => $page->kicker])
    @endif
@endsection
