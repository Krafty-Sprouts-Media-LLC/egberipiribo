{{--
    One story as a card: a picture, its kicker, headline, standfirst and byline. The headline link stretches over the
    whole card (the drawing's `.story`). Phones show a compact row; from 640px an image card; in the list layout
    (`card-row`) a wide row with the standfirst.

    Takes $item (a ListingItem) and optionally:
      $level      the headline's heading level, 3 by default
      $context    the image context from the manifest, 'card' by default
      $priority   true for the page's one most important image
      $rowClass   an Alpine-bound class for the list layout, e.g. ':class="rowClass"' written by the caller as $bind
      $bind       extra attributes for the <article>, such as ':class="rowClass"'
      $kicker     false to leave the kicker out
      $snippetHtml  a search result's snippet, shown in place of the standfirst

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $level = $level ?? 3;
    $context = $context ?? 'card';
    $showKicker = ($kicker ?? true) !== false;
    $hasImage = $item->image !== null;
@endphp
<article class="story card"{!! isset($bind) ? ' '.$bind : '' !!}>
    @if ($hasImage)
        <div class="media"><x-teme::image :id="$item->image->id" :context="$context" :priority="$priority ?? false" :decorative="true" /></div>
    @else
        <div class="media" aria-hidden="true"></div>
    @endif
    <div class="min-w-0">
        @if ($showKicker && $item->kicker !== null)
            <p class="kicker">{{ $item->kicker->name }}</p>
        @endif
        <h{{ $level }} class="hed card-title"><a href="{{ $item->url }}">{{ $item->title }}</a></h{{ $level }}>
        @if (($snippetHtml ?? null) !== null)
            {{-- The one raw output here: Teme escaped the snippet before it marked the matches. --}}
            <p class="deck card-deck">{!! $snippetHtml !!}</p>
        @elseif ($item->standfirst !== null && $item->standfirst !== '')
            <p class="deck card-deck">{{ $item->standfirst }}</p>
        @endif
        <p class="meta mt-2">@include('theme::skins.fable.partials.byline', ['authors' => $item->authors, 'publishedText' => $item->publishedText, 'published' => $item->published])</p>
    </div>
</article>
