{{--
    Fable: core's page for an item protected by a password: the item's title, core's line saying why the form is here,
    and core's password form. The words are core's, from $page, so the theme cannot misstate what happened; this lays
    them out like the rest of Fable. Never stored, never indexed.

    Takes $page (ProtectedView: ->title, ->lead(), the form is <x-teme::password-form>), $site.

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'protected')

@section('main')
    <div class="article-main pt-8 pb-16">
        <header>
            <h1 id="page-h" tabindex="-1" class="hed hed-xl">{{ $page->title }}</h1>
            <p class="deck mt-3">{{ $page->lead() }}</p>
        </header>
        <div class="protected-form mt-8">
            <x-teme::password-form :page="$page" />
        </div>
    </div>
@endsection
