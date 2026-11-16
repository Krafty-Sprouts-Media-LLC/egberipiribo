{{--
    Fable: error. Any other HTTP error on a public page (403, 429, 500...), in the look of the not-found page: the
    status as the big number, the one h1 and a way home. Takes $page, an ErrorView, and reads only its ->code, with
    a fallback if it is missing, so the page has little to fail on. The status itself stays core's.

    Settings this template reads (each once, at the top):
      fable_error_big_number   bool, true    the big status number beside the message
      fable_error_home_button  bool, true    the "Go to the home page" button
      fable_error_search_form  bool, false   a search field under the message (the current page has none)
      fable_error_sections     bool, false   the section chips under the way home (the current page has none)

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'error')

@php
    $showBigNumber = (bool) ($themeSettings['fable_error_big_number'] ?? true);
    $showHomeButton = (bool) ($themeSettings['fable_error_home_button'] ?? true);
    $showSearchForm = (bool) ($themeSettings['fable_error_search_form'] ?? false);
    $showSections = (bool) ($themeSettings['fable_error_sections'] ?? false);

    $code = (int) data_get($page, 'code', 500);
    [$errorTitle, $errorText] = match (true) {
        $code === 403 => [__('You can’t open that page'), __('This page is not open to you. If you think it should be, tell the site’s team.')],
        $code === 429 => [__('Too many requests'), __('You have made a lot of requests in a short time. Wait a minute, then try again.')],
        $code >= 500 => [__('Something went wrong on our side'), __('The page could not be shown. It is not you. Try again in a moment.')],
        default => [__('That page can’t be shown'), __('The page could not be shown. Try again, or start from the front page.')],
    };
@endphp

@section('main')
    <section class="py-12 pb-16 lg:py-16 grid {{ $showBigNumber ? 'lg:grid-cols-[minmax(0,1fr)_auto]' : '' }} gap-x-12 gap-y-8 items-center">
        <div>
            <p class="kicker">{{ __('Error :code', ['code' => $code]) }}</p>
            <h1 class="hed hed-page mt-3">{{ $errorTitle }}</h1>
            <p class="deck text-[1.1875rem] mt-4 max-w-[36rem]">{{ $errorText }}</p>

            @if ($showSearchForm)
                @include('theme::skins.fable.partials.listing-search-form', ['id' => 'error-term', 'class' => 'mt-7 max-w-[36rem]'])
            @endif

            @if ($showHomeButton)
                <p class="mt-7"><a href="{{ $site->url }}" class="btn">{{ __('Go to the home page') }}@include('theme::icons.arrow-right', ['class' => 'text-base'])</a></p>
            @endif

            @if ($showSections)
                @include('theme::skins.fable.partials.listing-sections', ['class' => 'mt-6'])
            @endif
        </div>

        @if ($showBigNumber)
            <p class="order-first lg:order-none font-display font-extrabold [font-stretch:75%] leading-[.8] tracking-[-.04em] text-rail text-[clamp(6.5rem,2rem+17vw,15rem)] lg:pr-2" aria-hidden="true">{{ $code }}</p>
        @endif
    </section>
@endsection
