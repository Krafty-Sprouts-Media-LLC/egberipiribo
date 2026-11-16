{{--
    The author card (the drawing's "Written by" block) for each author who has something to show in it: initials,
    name, bio and a link to their stories. Takes $authors (a list of Person). The article draws it only while its
    show_author_card setting is on.

    A card needs a bio (while the bio is on) or a link (while the link is on); an author with neither gets no card,
    since a card of only a name would repeat the byline.

    Settings this partial reads (once, below; the default is the drawing's):
      fable_author_card_avatar  on/off, on  the initials circle
      fable_author_card_bio     on/off, on  the author's bio
      fable_author_card_link    on/off, on  the "More from ..." link to the author's page

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $cardAvatar = (bool) ($themeSettings['fable_author_card_avatar'] ?? true);
    $cardBio = (bool) ($themeSettings['fable_author_card_bio'] ?? true);
    $cardLink = (bool) ($themeSettings['fable_author_card_link'] ?? true);
@endphp
@foreach ($authors as $author)
    @php
        $hasBio = $cardBio && ($author->bio ?? '') !== '';
        $hasLink = $cardLink && $author->url !== '';
    @endphp
    @if ($hasBio || $hasLink)
        <section class="mt-9 border-t border-ink pt-6 grid {{ $cardAvatar ? 'sm:grid-cols-[6rem_minmax(0,1fr)] ' : '' }}gap-5" aria-labelledby="author-{{ $loop->index }}">
            @if ($cardAvatar)
                @include('theme::skins.fable.partials.article-avatar', ['name' => $author->name, 'class' => 'w-24 h-24 text-3xl'])
            @endif
            <div>
                <p class="kicker">{{ __('Written by') }}</p>
                <h2 id="author-{{ $loop->index }}" class="hed hed-lg mt-1">{{ $author->name }}</h2>
                @if ($hasBio)
                    <p class="deck mt-2">{{ $author->bio }}</p>
                @endif
                @if ($hasLink)
                    <a href="{{ $author->url }}" class="text-link inline-block mt-3">{{ __('More from :name', ['name' => $author->name]) }}@include('theme::icons.arrow-right')</a>
                @endif
            </div>
        </section>
    @endif
@endforeach
