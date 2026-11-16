{{--
    Fable: author. A writer's page: the drawing's profile block (portrait, the one h1 with their name, their bio) and
    the stories they wrote as rows, then the pages.

    Takes $page, a ListingView of kind author: ->subject (title = the name, description = the bio, image = the
    portrait, url), ->items, ->pagination, ->breadcrumbs.

    Left out because Teme cannot give it: the role line ("Animals editor"), the e-mail and social buttons, the
    facts panel (covers, with us since, training, story count) and the sort control. Without a portrait the page
    draws the name's initials in a round tile.

    Choices a later update can turn into theme settings:
      author_show_portrait   bool, true   the writer's portrait (or initials) beside the name

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'author')

@php
    $showPortrait = $themeSettings['author_show_portrait'] ?? true;

    $subject = $page->subject;
    $name = $subject?->title ?? $site->name;
    $portrait = $subject?->image;
    $initials = collect(preg_split('/\s+/u', trim($name)) ?: [])
        ->filter()
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->take(2)
        ->implode('');

    $crumbs = $page->breadcrumbs;
    if (! is_array($crumbs) || count($crumbs) < 2) {
        $crumbs = [(object) ['name' => $site->name, 'url' => $site->url], (object) ['name' => $name, 'url' => '']];
    }

    $items = array_values($page->items);
@endphp

@section('main')
    <header class="pt-8 pb-9 border-b border-ink">
        @include('theme::skins.fable.partials.breadcrumbs', ['crumbs' => $crumbs])

        <div class="grid {{ $showPortrait ? 'md:grid-cols-[10rem_minmax(0,1fr)]' : '' }} gap-x-10 gap-y-7 mt-7">
            @if ($showPortrait)
                @if ($portrait !== null)
                    <div class="media w-28 h-28 md:w-40 md:h-40 rounded-full"><x-teme::image :id="$portrait->id" context="avatar" :decorative="true" :priority="true" /></div>
                @else
                    <div class="w-28 h-28 md:w-40 md:h-40 rounded-full bg-rail text-white grid place-items-center font-display font-extrabold text-4xl md:text-5xl tracking-tight" aria-hidden="true">{{ $initials }}</div>
                @endif
            @endif
            <div class="min-w-0">
                <p class="kicker">{{ __('Writer') }}</p>
                <h1 class="hed hed-page mt-2">{{ $name }}</h1>
                @if (($subject?->description ?? '') !== '')
                    <p class="deck text-[1.125rem] mt-4 max-w-[38rem]">{{ $subject->description }}</p>
                @endif
            </div>
        </div>
    </header>

    @if ($items !== [])
        <section class="pt-9 pb-16" aria-labelledby="author-stories">
            <h2 id="author-stories" class="hed hed-lg">{{ __('Latest from :name', ['name' => $name]) }}</h2>
            <div class="listing-rows mt-4">
                @foreach ($items as $item)
                    @include('theme::skins.fable.partials.card', ['item' => $item])
                @endforeach
            </div>

            @include('theme::skins.fable.partials.pagination', ['pagination' => $page->pagination])
        </section>
    @else
        <section class="py-12 pb-16" aria-labelledby="author-empty">
            <h2 id="author-empty" class="hed hed-lg">{{ __('No stories yet') }}</h2>
            <p class="deck text-[1.125rem] mt-3 max-w-[34rem]">{{ __(':name has not published anything here yet.', ['name' => $name]) }}</p>
            <p class="mt-6"><a href="{{ $site->url }}" class="btn btn-line">@include('theme::icons.arrow-left', ['class' => 'text-base']){{ __('Go to the home page') }}</a></p>
        </section>
    @endif
@endsection
