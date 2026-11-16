{{--
    Fable: a later page of an item's comments (the story's address with /comments/page/N). The story is named and
    linked, then the thread and its form as under an article; page 1 of the thread is the story itself. The comments
    section is the shared thread partial, so the data-teme-* hooks core's script finds are the same on both.

    Takes $page (CommentsPageView: ->title, ->url, ->kicker, ->comments), $site.

    Choices a later update can turn into theme settings (read once, below; the default is the drawing's):
      comments_page_title  text, "Comments on “:title”"  the page's h1; :title is the story's title

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@php
    $commentsPageTitle = $themeSettings['comments_page_title'] ?? __('Comments on “:title”');
@endphp

@section('template', 'comments')

@section('main')
    <div class="article-main pt-8 pb-16">
        <header>
            @if ($page->kicker !== null)
                <p class="kicker mb-2"><a href="{{ $page->kicker->url }}" class="hover:underline underline-offset-4">{{ $page->kicker->name }}</a></p>
            @endif
            <h1 class="hed hed-xl">{{ str_replace(':title', $page->title, $commentsPageTitle) }}</h1>
            <p class="meta mt-4 flex flex-wrap items-center gap-x-3">
                <a href="{{ $page->url }}" class="text-link">{{ __('Read the story') }}@include('theme::icons.arrow-right')</a>
                <span>{{ __('Page :current of :total', ['current' => $page->comments->current, 'total' => $page->comments->total]) }}</span>
            </p>
        </header>

        @include('theme::skins.fable.partials.comments-thread', ['comments' => $page->comments])
    </div>
@endsection
