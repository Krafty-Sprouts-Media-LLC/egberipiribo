{{--
    One comment, given $comment (CommentView) and $withReply: the initials picture, the writer's name (with an
    "Author" badge for someone the story credits) and when, then the words, then the actions line, which is core's
    place for Edit and holds Reply while it is offered. Its words are core's, already escaped. Core's script finds
    data-teme-actions and data-teme-reply, never these classes.

    Settings this partial reads (once, below; the default is the drawing's):
      fable_comments_avatar        on/off, on  the initials circle beside each comment
      fable_comments_author_badge  on/off, on  the "Author" badge on a credited author's comment

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $showCommentAvatar = (bool) ($themeSettings['fable_comments_avatar'] ?? true);
    $showAuthorBadge = (bool) ($themeSettings['fable_comments_author_badge'] ?? true);
@endphp
<article class="c-comment flex gap-3.5 sm:gap-4"@if ($comment->legacyId !== null) id="comment-{{ $comment->legacyId }}"@endif aria-labelledby="c-{{ $comment->id }}-by c-{{ $comment->id }}-at">
    @if ($showCommentAvatar)
        <span class="c-avatar grid place-items-center shrink-0 w-11 h-11 rounded-full text-sm" data-hue="{{ $comment->hue }}" aria-hidden="true">{{ $comment->initials }}</span>
    @endif
    <div class="c-body min-w-0 flex-1">
        @if ($comment->pending)
            <p class="c-state" id="c-{{ $comment->id }}-state"><strong>{{ __('Awaiting moderation.') }}</strong> {{ __('Only you can see it until a moderator approves it.') }}</p>
        @endif
        <p class="text-[.9375rem] flex flex-wrap items-center gap-x-2 gap-y-0.5">
            <b id="c-{{ $comment->id }}-by">@if ($comment->url !== null)<a href="{{ $comment->url }}" rel="ugc nofollow" class="hover:underline underline-offset-4">{{ $comment->name }}</a>@else{{ $comment->name }}@endif</b>
            @if ($showAuthorBadge && $comment->byAuthor)
                <span class="text-[.6875rem] font-bold uppercase tracking-[.06em] bg-rail text-white rounded px-1.5 py-0.5">{{ __('Author') }}</span>
            @endif
            @if ($comment->pending)
                <span class="meta"><time id="c-{{ $comment->id }}-at" datetime="{{ $comment->iso }}">{{ $comment->dateText }}</time></span>
            @else
                <a class="meta hover:text-ink hover:underline underline-offset-4" href="{{ $comment->permalink }}"><time id="c-{{ $comment->id }}-at" datetime="{{ $comment->iso }}">{{ $comment->dateText }}</time><span class="sr-only">, {{ __(':name’s comment', ['name' => $comment->name]) }}</span></a>
            @endif
        </p>
        @if ($comment->isReply)
            <p class="meta mt-0.5">{{ $comment->parentName === null ? __('In reply to a removed comment') : __('In reply to :name', ['name' => $comment->parentName]) }}</p>
        @endif
        <div class="comment-body mt-1.5">{!! $comment->bodyHtml !!}</div>
        @if ($withReply && ! $comment->pending)
            @php($canReply = $comment->replyUrl !== null)
            <p class="c-actions" data-teme-actions="{{ $comment->name }}"@unless ($canReply) hidden @endunless>@if ($canReply)<a class="text-link" href="{{ $comment->replyUrl }}" rel="nofollow" data-teme-reply="{{ $comment->id }}">{{ __('Reply') }}<span class="sr-only"> {{ __('to :name', ['name' => $comment->name]) }}</span></a>@endif</p>
        @endif
    </div>
</article>
