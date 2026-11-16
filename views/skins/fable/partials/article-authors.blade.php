{{--
    The author card (the drawing's "Written by" block) for each author who has a bio or a page of their own: initials,
    name, bio and a link to their stories. Takes $authors (a list of Person).

    @package egberipiribo
    @since   0.1.0
--}}
@foreach ($authors as $author)
    @if (($author->bio ?? '') !== '' || $author->url !== '')
        <section class="mt-9 border-t border-ink pt-6 grid sm:grid-cols-[6rem_minmax(0,1fr)] gap-5" aria-labelledby="author-{{ $loop->index }}">
            @include('theme::skins.fable.partials.article-avatar', ['name' => $author->name, 'class' => 'w-24 h-24 text-3xl'])
            <div>
                <p class="kicker">{{ __('Written by') }}</p>
                <h2 id="author-{{ $loop->index }}" class="hed hed-lg mt-1">{{ $author->name }}</h2>
                @if (($author->bio ?? '') !== '')
                    <p class="deck mt-2">{{ $author->bio }}</p>
                @endif
                @if ($author->url !== '')
                    <a href="{{ $author->url }}" class="text-link inline-block mt-3">{{ __('More from :name', ['name' => $author->name]) }}@include('theme::icons.arrow-right')</a>
                @endif
            </div>
        </section>
    @endif
@endforeach
