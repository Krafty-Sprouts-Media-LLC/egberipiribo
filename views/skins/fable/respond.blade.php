{{--
    Fable: core's page about writing a comment: the reply page when JavaScript is off, and the answer to a comment form
    posted without it. Its words come from $page, in core's own, so the theme cannot misstate what happened; this lays
    them out like the rest of Fable: the heading, core's opening lines and hint, the comment answered, what the reader
    wrote, the form, and where to go next. Never stored, never indexed.

    Takes $page (RespondView: ->heading, ->lead, ->hint, ->context, ->echoHtml, ->keep, ->form, ->links), $site.

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@section('template', 'respond')

@section('main')
    <div class="article-main pt-8 pb-16">
        <header>
            <p class="kicker mb-2">{{ __('Comments') }}</p>
            <h1 id="page-h" tabindex="-1" class="hed hed-xl">{{ $page->heading }}</h1>
            {{-- Core built each opening line and escaped every word in it. --}}
            @foreach ($page->lead as $line)
                <p class="deck mt-3">{!! $line !!}</p>
            @endforeach
            @if ($page->hint !== null)
                <p class="meta mt-3">{{ $page->hint }}</p>
            @endif
        </header>

        @if ($page->context !== null)
            <div class="mt-8 rounded-[var(--radius-box)] bg-surface p-5">
                @include('theme::skins.fable.partials.comment-card', ['comment' => $page->context, 'withReply' => false])
            </div>
        @endif

        @if ($page->echoHtml !== null)
            <section class="mt-8" aria-labelledby="echo-title">
                <h2 id="echo-title" class="hed hed-md">{{ __('What you wrote') }}</h2>
                {{-- Core escaped what the reader wrote. --}}
                <div class="comment-body mt-2">{!! $page->echoHtml !!}</div>
            </section>
        @endif

        @if ($page->keep !== null)
            <div class="mt-8">
                <label for="comment-keep" class="form-label">{{ __('Your comment') }}</label>
                <textarea id="comment-keep" rows="6" readonly class="field">{{ $page->keep }}</textarea>
            </div>
        @endif

        @if ($page->form !== null)
            <x-teme::comment-form :form="$page->form" />
        @endif

        @if ($page->links !== [])
            <ul class="mt-8 flex flex-wrap gap-x-6 gap-y-2">
                @foreach ($page->links as $link)
                    <li><a href="{{ $link['href'] }}" class="text-link">{{ $link['text'] }}</a></li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
