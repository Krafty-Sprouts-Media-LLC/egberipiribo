{{--
    Fable: a static page (About, Contact, a policy). The article's frame without what a page leaves out: no reading bar,
    no byline or share row, no ads, no "Read next", tags, author card or related stories. Top to bottom: the breadcrumb,
    the title, the deck and "Last updated", the featured image when there is one, the shown fields, the body, the pages
    under this one, and the comments when Teme gives the page a thread. The rail's "In this story" list (the `toc`
    section) is built from the body's h2 headings that carry an id.

    Takes $page (ArticleView), $site, $themeSettings and $isFront. There is exactly one <h1>, on the front page too.

    Settings this template reads (once, below; the default is the drawing's, and the manifest's):
      fable_page_breadcrumb  on/off, on          the breadcrumb above the title
      fable_page_standfirst  on/off, on          the deck under the title
      show_updated           on/off, on          the "Last updated" line under the title
      show_updated_date      on/off, on          the same line, site-wide: it shows only while both are on
      fable_page_hero        on/off, on          the featured picture under the title
      fable_page_fields      on/off, on          the custom fields set to show on a page (before and after the body)
      show_contents          on/off, on          the "In this story" list in the rail
      contents_min_headings  number, 2           the fewest h2 headings that earn the list
      page_lede              on/off, on          the body's first paragraph set larger, as the drawing's lede
      show_children          on/off, on          the list of pages under this one
      children_heading       text, "In this section"  the heading of that list
      show_page_comments     on/off, on          the comments, when Teme gives the page a thread
      show_comments          on/off, on          the site-wide comments switch: a page's thread shows only while both are on

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@php
    $showBreadcrumb = (bool) ($themeSettings['fable_page_breadcrumb'] ?? true);
    $showStandfirst = (bool) ($themeSettings['fable_page_standfirst'] ?? true);
    $showHero = (bool) ($themeSettings['fable_page_hero'] ?? true);
    $showFields = (bool) ($themeSettings['fable_page_fields'] ?? true);
    $showContents = (bool) ($themeSettings['show_contents'] ?? true);
    $contentsMinHeadings = (int) ($themeSettings['contents_min_headings'] ?? 2);
    $ledeFirstParagraph = (bool) ($themeSettings['page_lede'] ?? true);
    $showUpdated = (bool) ($themeSettings['show_updated'] ?? true) && (bool) ($themeSettings['show_updated_date'] ?? true);
    $showChildren = (bool) ($themeSettings['show_children'] ?? true);
    $childrenHeading = $themeSettings['children_heading'] ?? __('In this section');
    $showPageComments = (bool) ($themeSettings['show_page_comments'] ?? true) && (bool) ($themeSettings['show_comments'] ?? true);

    $hasCrumbs = $showBreadcrumb && is_array($page->breadcrumbs) && count($page->breadcrumbs) > 1;
    $lastChange = $page->updated ?? $page->published;
    $lastChangeText = $page->updatedText ?? $page->publishedText;
    $hasLead = $showHero && $page->lead !== null && $page->leadIsFeatured;
@endphp

@section('template', 'single-page')

@section('main')
    <article class="article-main pt-8 pb-16">
        @if ($hasCrumbs)
            @include('theme::skins.fable.partials.breadcrumbs', ['crumbs' => $page->breadcrumbs])
        @endif

        <header class="{{ $hasCrumbs ? 'mt-5' : '' }} pb-7 border-b border-ink">
            <h1 class="hed hed-page">{{ $page->title }}</h1>
            @if ($showStandfirst && $page->standfirst !== null && $page->standfirst !== '')
                <p class="deck text-[1.3125rem] leading-[1.45] mt-5">{{ $page->standfirst }}</p>
            @endif
            @if ($showUpdated)
                <p class="meta mt-4"><time datetime="{{ $lastChange->format(DATE_ATOM) }}">{{ __('Last updated :date', ['date' => $lastChangeText]) }}</time></p>
            @endif
        </header>

        @if ($hasLead)
            <figure class="mt-8">
                <div class="media aspect-[16/10]"><x-teme::image :id="$page->lead->id" context="hero" :priority="true" :alt="$page->lead->alt" :decorative="$page->lead->decorative" /></div>
                @if (($page->lead->caption ?? '') !== '' || ($page->lead->credit ?? '') !== '')
                    <figcaption class="meta mt-2.5">{{ $page->lead->caption }}@if (($page->lead->credit ?? '') !== '') <span class="whitespace-nowrap">{{ $page->lead->credit }}</span>@endif</figcaption>
                @endif
            </figure>
        @endif

        @include('theme::skins.fable.partials.article-fields', ['fields' => $showFields ? $page->shownFields('before') : [], 'class' => 'mt-8'])

        <div class="mt-8">
            @include('theme::skins.fable.partials.article-body', ['html' => $page->bodyHtml, 'lede' => $ledeFirstParagraph, 'ads' => false, 'contents' => $showContents, 'contentsMin' => $contentsMinHeadings])
        </div>

        @include('theme::skins.fable.partials.article-fields', ['fields' => $showFields ? $page->shownFields('after') : [], 'class' => 'mt-8'])

        @if ($showChildren && $page->children !== [])
            <nav class="mt-12 border-t border-ink pt-5" aria-labelledby="children-title">
                <h2 id="children-title" class="hed hed-lg">{{ $childrenHeading }}</h2>
                <ul class="mt-4 divide-y divide-rule border-y border-rule">
                    @foreach ($page->children as $child)
                        <li><a href="{{ $child->url }}" class="flex items-center justify-between gap-4 py-3.5 font-bold hover:text-rail">{{ $child->title }}@include('theme::icons.arrow-right', ['class' => 'text-rail shrink-0'])</a></li>
                    @endforeach
                </ul>
            </nav>
        @endif

        @if ($showPageComments)
            @include('theme::skins.fable.partials.comments-thread', ['comments' => $page->comments])
        @endif
    </article>
@endsection
