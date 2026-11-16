{{--
    The thread, given $comments (CommentsView or null), under a story or on a later page of comments: the count as a
    heading, the conversations, the pages of the thread, then core's form while it is open or a line saying it is
    closed. Nothing when $comments is null (comments off, the story hidden, or its type takes none), and nothing
    when comments are closed and there are none. The form is core's. Core's script finds the data-teme-* hooks,
    never the classes.

    @package egberipiribo
    @since   0.1.0
--}}
@if ($comments !== null && ($comments->open || $comments->count > 0))
    @php
        $commentCount = $comments->count;
        $countText = $commentCount === 0 ? __('Comments') : ($commentCount === 1 ? __('1 comment') : __(':count comments', ['count' => \Illuminate\Support\Number::format($commentCount, locale: app()->getLocale())]));
    @endphp
    <section id="comments" class="mt-12 border-t border-ink pt-6 scroll-mt-20" aria-labelledby="comments-title">
        <h2 id="comments-title" class="hed hed-lg" data-teme-count="{{ $commentCount }}">{{ $countText }}</h2>
        @if ($commentCount === 0)
            <p class="deck mt-3" data-teme-empty>{{ __('No comments yet. Start the conversation.') }}</p>
        @endif
        <ol class="mt-6" data-teme-thread data-teme-depth="{{ $comments->depth }}"@if ($comments->items === []) hidden @endif>
            @foreach ($comments->items as $comment)
                @include('theme::skins.fable.partials.comment-item', ['comment' => $comment])
            @endforeach
        </ol>
        @if ($comments->total > 1)
            {{-- Oldest first, so back is "Older" and on is "Newer". --}}
            <nav class="flex flex-wrap items-center gap-2 mt-8" aria-label="{{ __('Pages of comments') }}">
                @if ($comments->pagination->previous !== null)
                    <a href="{{ $comments->pagination->previous }}" class="page-num gap-2 inline-flex items-center px-4" rel="prev">@include('theme::icons.arrow-left'){{ __('Older') }}<span class="sr-only"> {{ __('comments') }}</span></a>
                @endif
                @foreach ($comments->pagination->pages() as $link)
                    @if ($link['number'] === null)
                        <span class="page-num" aria-hidden="true">…</span>
                    @elseif ($link['current'])
                        <span class="page-num" aria-current="page"><span class="sr-only">{{ __('Page') }} </span>{{ $link['number'] }}</span>
                    @else
                        <a href="{{ $link['url'] }}" class="page-num"><span class="sr-only">{{ __('Page') }} </span>{{ $link['number'] }}</a>
                    @endif
                @endforeach
                @if ($comments->pagination->next !== null)
                    <a href="{{ $comments->pagination->next }}" class="page-num gap-2 inline-flex items-center px-4" rel="next">{{ __('Newer') }}<span class="sr-only"> {{ __('comments') }}</span>@include('theme::icons.arrow-right')</a>
                @endif
            </nav>
        @endif
        @if ($comments->form !== null)
            <x-teme::comment-form :form="$comments->form" />
        @else
            <p class="font-semibold mt-8 pt-6 border-t border-rule">{{ __('Comments are closed.') }}</p>
            <x-teme::thread-script />
        @endif
    </section>
@endif
