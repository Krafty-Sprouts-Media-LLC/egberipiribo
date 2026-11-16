{{--
    Fable: notfound. The drawing's page-not-found: the big "404", the one h1, a search form, the way home and the
    site's sections as chips. Core keeps the response status at 404.

    Takes $page, a NotFoundView (only ->path, which is not printed) and reads the primary navigation for the section
    chips.

    Left out because Teme gives a not-found template no stories: the drawing's "Most read this week".

    Settings this template reads (each once, at the top):
      fable_404_big_number     bool, true   the big "404" beside the message
      fable_404_search_form    bool, true   the search field
      fable_404_home_button    bool, true   the "Go to the home page" button
      notfound_show_sections   bool, true   the "Browse a section" chips beside the way home

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'notfound')

@php
    $showBigNumber = (bool) ($themeSettings['fable_404_big_number'] ?? true);
    $showSearchForm = (bool) ($themeSettings['fable_404_search_form'] ?? true);
    $showHomeButton = (bool) ($themeSettings['fable_404_home_button'] ?? true);
    $showSections = (bool) ($themeSettings['notfound_show_sections'] ?? true);
@endphp

@section('main')
    <div class="pt-8">@include('theme::skins.fable.partials.pattern', ['area' => '404', 'mode' => 'band'])</div>
    <section class="py-12 pb-16 lg:py-16 grid {{ $showBigNumber ? 'lg:grid-cols-[minmax(0,1fr)_auto]' : '' }} gap-x-12 gap-y-8 items-center">
        <div>
            <p class="kicker">{{ __('Page not found') }}</p>
            <h1 class="hed hed-page mt-3">{{ __('That page has moved, or it was never here') }}</h1>
            <p class="deck text-[1.1875rem] mt-4 max-w-[36rem]">{{ __('The link may be out of date or mistyped. Search for the story you wanted, or start again from the front page.') }}</p>

            @if ($showSearchForm)
                @include('theme::skins.fable.partials.listing-search-form', ['id' => 'notfound-term', 'class' => 'mt-7 max-w-[36rem]'])
            @endif

            @if ($showHomeButton || $showSections)
                <div class="flex flex-wrap items-center gap-2 mt-6">
                    @if ($showHomeButton)
                        <a href="{{ $site->url }}" class="btn btn-line">@include('theme::icons.arrow-left', ['class' => 'text-base']){{ __('Go to the home page') }}</a>
                    @endif
                    @if ($showSections)
                        @include('theme::skins.fable.partials.listing-sections')
                    @endif
                </div>
            @endif
        </div>

        @if ($showBigNumber)
            <p class="order-first lg:order-none font-display font-extrabold [font-stretch:75%] leading-[.8] tracking-[-.04em] text-rail text-[clamp(6.5rem,2rem+17vw,15rem)] lg:pr-2" aria-hidden="true">404</p>
        @endif
    </section>
@endsection
