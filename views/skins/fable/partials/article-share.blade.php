{{--
    Share buttons: Facebook, X, Pinterest and email, each a plain link built from the page's address and title, and a
    button that copies the address (the copyLink component; it needs JavaScript, so it stays hidden without it).
    Takes $page (ArticleView) and optionally $label (a line before the buttons) and $class.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $address = str_starts_with($page->url, 'http') ? $page->url : rtrim($site->url, '/').'/'.ltrim($page->url, '/');
    $encodedUrl = urlencode($address);
    $encodedTitle = urlencode($page->title);
    $ring = 'icon-btn shadow-[inset_0_0_0_1.5px_var(--color-rule)] hover:bg-ink hover:text-white hover:shadow-none';
@endphp
<div class="flex flex-wrap items-center gap-1.5 {{ $class ?? '' }}">
    @if (($label ?? null) !== null)
        <span class="meta font-bold text-ink mr-1.5">{{ $label }}</span>
    @endif
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener" class="{{ $ring }}" aria-label="{{ __('Share on Facebook') }}">@include('theme::icons.facebook-logo')</a>
    <a href="https://x.com/intent/post?url={{ $encodedUrl }}&amp;text={{ $encodedTitle }}" target="_blank" rel="noopener" class="{{ $ring }}" aria-label="{{ __('Share on X') }}">@include('theme::icons.x-logo')</a>
    <a href="https://www.pinterest.com/pin/create/button/?url={{ $encodedUrl }}&amp;description={{ $encodedTitle }}" target="_blank" rel="noopener" class="{{ $ring }}" aria-label="{{ __('Share on Pinterest') }}">@include('theme::icons.pinterest-logo')</a>
    <a href="mailto:?subject={{ $encodedTitle }}&amp;body={{ $encodedUrl }}" class="{{ $ring }}" aria-label="{{ __('Share by email') }}">@include('theme::icons.envelope-simple')</a>
    <span x-data="copyLink" x-cloak class="inline-flex">
        <button type="button" class="chip" @click="copy" aria-live="polite">
            <span x-show="idle" class="inline-flex items-center gap-1.5">@include('theme::icons.link', ['class' => 'text-base']){{ __('Copy link') }}</span>
            <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5">@include('theme::icons.check', ['class' => 'text-base']){{ __('Link copied') }}</span>
        </button>
    </span>
</div>
