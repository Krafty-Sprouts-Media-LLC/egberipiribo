{{--
    One place in the thread, given $comment (CommentView): a comment, a tombstone (a removed comment whose replies
    stay) or a ping kept from WordPress, with its replies nested under it. A tombstone or ping keeps its old
    #comment-N anchor. The data-teme-* hooks are core's script's, so they stay exactly as they are.

    Settings this partial reads (the comment card reads the same one for a comment; this is for the tombstone's ring):
      fable_comments_avatar  on/off, on  the initials circle, and with it a removed comment's empty ring

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $showCommentAvatar = (bool) ($themeSettings['fable_comments_avatar'] ?? true);
@endphp
<li id="comment-{{ $comment->id }}" data-teme-comment="{{ $comment->id }}">
    @if ($comment->removed)
        <div class="c-comment c-removed flex items-center gap-3.5 sm:gap-4"@if ($comment->legacyId !== null) id="comment-{{ $comment->legacyId }}"@endif>@if ($showCommentAvatar)<span class="c-avatar is-empty shrink-0 w-11 h-11 rounded-full" aria-hidden="true"></span>@endif<p>{{ __('This comment was removed.') }}</p></div>
    @elseif ($comment->kind !== 'comment')
        <p class="c-comment c-ping"@if ($comment->legacyId !== null) id="comment-{{ $comment->legacyId }}"@endif>{{ __('Linked from') }} <a href="{{ $comment->url }}" rel="ugc nofollow" class="text-rail underline underline-offset-4">{{ $comment->name }}</a> · <time datetime="{{ $comment->iso }}">{{ $comment->dateText }}</time></p>
    @else
        @include('theme::skins.fable.partials.comment-card', ['comment' => $comment, 'withReply' => true])
    @endif
    @if ($comment->replies !== [])
        <ol data-teme-replies>
            @foreach ($comment->replies as $reply)
                @include('theme::skins.fable.partials.comment-item', ['comment' => $reply])
            @endforeach
        </ol>
    @endif
</li>
