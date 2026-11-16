{{--
    Fable: error. A placeholder until the page is built.

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('main')
    <h1 class="hed hed-page py-8">{{ $page->title ?? $page->subject?->title ?? $site->name }}</h1>
@endsection
