{{--
    Who wrote it, when, and for how long to read. Takes $authors, $published and $publishedText, and optionally
    $readingMinutes and $updatedText. The first author's name is set heavier, as in the drawings; names link to the
    author's page when they have one.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $authors = $authors ?? [];
@endphp
@foreach ($authors as $author)
    @if ($loop->first)<span class="font-bold text-ink">@if ($author->url !== '')<a href="{{ $author->url }}" rel="author">{{ $author->name }}</a>@else{{ $author->name }}@endif</span>@else{{ $loop->last ? ' '.__('and').' ' : ', ' }}@if ($author->url !== '')<a href="{{ $author->url }}" rel="author">{{ $author->name }}</a>@else{{ $author->name }}@endif @endif
@endforeach
@if ($authors !== []) · @endif<time datetime="{{ $published->format(DATE_ATOM) }}">{{ $publishedText }}</time>
@if (($readingMinutes ?? null) !== null) · {{ trans_choice(':count min read', $readingMinutes, ['count' => $readingMinutes]) }}@endif
@if (($updatedText ?? null) !== null) · {{ __('Updated :date', ['date' => $updatedText]) }}@endif
