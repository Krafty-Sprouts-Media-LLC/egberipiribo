{{--
    Fable: index. The floor every kind of page without a template of its own falls to. It reads nothing from $page:
    a plain Fable page saying so, with a search and a way home.

    Choices a later update can turn into theme settings: none.

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'index')

@section('main')
    <section class="py-12 pb-16 lg:py-16 max-w-[40rem]">
        <p class="kicker">{{ $site->name }}</p>
        <h1 class="hed hed-page mt-3">{{ __('This page has no design of its own yet') }}</h1>
        <p class="deck text-[1.1875rem] mt-4">{{ __('The site has not set this kind of page up, so there is nothing to show here. Search for what you were looking for, or start again from the front page.') }}</p>

        @include('theme::skins.fable.partials.listing-search-form', ['id' => 'index-term', 'class' => 'mt-7'])

        <p class="mt-6"><a href="{{ $site->url }}" class="btn btn-line">@include('theme::icons.arrow-left', ['class' => 'text-base']){{ __('Go to the home page') }}</a></p>
    </section>
@endsection
