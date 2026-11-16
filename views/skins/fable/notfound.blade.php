{{--
    Fable: notfound. The drawing's page-not-found: the big "404", the one h1, a search form, the way home and the
    site's sections as chips. Core keeps the response status at 404.

    Takes $page, a NotFoundView (only ->path, which is not printed) and reads the primary navigation for the section
    chips.

    Left out because Teme gives a not-found template no stories: the drawing's "Most read this week".

    Choices a later update can turn into theme settings:
      notfound_show_sections   bool, true   the "Browse a section" chips beside the way home

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'notfound')

@php
    $showSections = $themeSettings['notfound_show_sections'] ?? true;
@endphp

@section('main')
    <section class="py-12 pb-16 lg:py-16 grid lg:grid-cols-[minmax(0,1fr)_auto] gap-x-12 gap-y-8 items-center">
        <div>
            <p class="kicker">{{ __('Page not found') }}</p>
            <h1 class="hed hed-page mt-3">{{ __('That page has moved, or it was never here') }}</h1>
            <p class="deck text-[1.1875rem] mt-4 max-w-[36rem]">{{ __('The link may be out of date or mistyped. Search for the story you wanted, or start again from the front page.') }}</p>

            @include('theme::skins.fable.partials.listing-search-form', ['id' => 'notfound-term', 'class' => 'mt-7 max-w-[36rem]'])

            <div class="flex flex-wrap items-center gap-2 mt-6">
                <a href="{{ $site->url }}" class="btn btn-line">@include('theme::icons.arrow-left', ['class' => 'text-base']){{ __('Go to the home page') }}</a>
                @if ($showSections)
                    @include('theme::skins.fable.partials.listing-sections')
                @endif
            </div>
        </div>

        <p class="order-first lg:order-none font-display font-extrabold [font-stretch:75%] leading-[.8] tracking-[-.04em] text-rail text-[clamp(6.5rem,2rem+17vw,15rem)] lg:pr-2" aria-hidden="true">404</p>
    </section>
@endsection
