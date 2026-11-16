{{--
    Share buttons: up to ten, each a plain link built from the page's address and title (Facebook, X, Pinterest,
    LinkedIn, WhatsApp, Telegram, Reddit, email), a button for the phone's own share sheet (the shareNative component:
    it needs JavaScript, so it stays hidden without it or where the device has none) and a button that copies the
    address (the copyLink component, hidden without JavaScript too). Which ones, where and how they look are the
    site's settings, all read once here.

    Takes $page (ArticleView) and optionally $label (a line before the buttons) and $class. The article draws this
    partial twice: with no $label it is the row at the byline ("top"); with a $label it is the row after the tags
    ("bottom"), and the line it prints is the site's fable_share_label, not the text passed in.

    Settings this template reads:
      show_share              on/off, on         every share row (off draws nothing)
      fable_share_facebook    on/off, on         the Facebook button
      fable_share_x           on/off, on         the X button
      fable_share_pinterest   on/off, off        the Pinterest button
      fable_share_linkedin    on/off, off        the LinkedIn button
      fable_share_whatsapp    on/off, off        the WhatsApp button
      fable_share_telegram    on/off, off        the Telegram button
      fable_share_reddit      on/off, off        the Reddit button
      fable_share_email       on/off, on         the share-by-email button
      fable_share_native      on/off, off        the phone's own share sheet (needs the shareNative component)
      fable_share_copy        on/off, on         the copy-link button
      fable_share_placement   top, bottom, both, floating, both
                              top: the byline row only; bottom: the row after the tags only; both: both rows;
                              floating: a sticky column beside the article from 96rem wide (icons only), and below
                              that the row after the tags
      fable_share_style       icons or labels, icons   round icon buttons, or buttons with the network's name
      fable_share_label       text, "Share this story"  the line before the row after the tags (empty: none)

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $shareOn = (bool) ($themeSettings['show_share'] ?? true);
    $sharePlacement = $themeSettings['fable_share_placement'] ?? 'both';
    $shareLabelled = ($themeSettings['fable_share_style'] ?? 'icons') === 'labels';
    $shareLine = (string) ($themeSettings['fable_share_label'] ?? __('Share this story'));
    $shareCopy = (bool) ($themeSettings['fable_share_copy'] ?? true);
    $shareNative = (bool) ($themeSettings['fable_share_native'] ?? false);

    $isBottom = ($label ?? null) !== null;
    $floating = $sharePlacement === 'floating';
    $draws = match ($sharePlacement) {
        'top' => ! $isBottom,
        'bottom' => $isBottom,
        default => true, // both, and floating (its byline copy is the column, its bottom copy the small-screen row)
    };

    $address = str_starts_with($page->url, 'http') ? $page->url : rtrim($site->url, '/').'/'.ltrim($page->url, '/');
    $encodedUrl = urlencode($address);
    $encodedTitle = urlencode($page->title);

    // key, default, link, the words the link carries for a reader who cannot see the icon, the network's name, the icon.
    $networks = [
        ['fable_share_facebook', true, 'https://www.facebook.com/sharer/sharer.php?u='.$encodedUrl, __('Share on Facebook'), __('Facebook'), 'facebook-logo', true],
        ['fable_share_x', true, 'https://x.com/intent/post?url='.$encodedUrl.'&text='.$encodedTitle, __('Share on X'), __('X'), 'x-logo', true],
        ['fable_share_pinterest', false, 'https://www.pinterest.com/pin/create/button/?url='.$encodedUrl.'&description='.$encodedTitle, __('Share on Pinterest'), __('Pinterest'), 'pinterest-logo', true],
        ['fable_share_linkedin', false, 'https://www.linkedin.com/sharing/share-offsite/?url='.$encodedUrl, __('Share on LinkedIn'), __('LinkedIn'), 'linkedin-logo', true],
        ['fable_share_whatsapp', false, 'https://api.whatsapp.com/send?text='.$encodedTitle.'%20'.$encodedUrl, __('Share on WhatsApp'), __('WhatsApp'), 'whatsapp-logo', true],
        ['fable_share_telegram', false, 'https://t.me/share/url?url='.$encodedUrl.'&text='.$encodedTitle, __('Share on Telegram'), __('Telegram'), 'telegram-logo', true],
        ['fable_share_reddit', false, 'https://www.reddit.com/submit?url='.$encodedUrl.'&title='.$encodedTitle, __('Share on Reddit'), __('Reddit'), 'reddit-logo', true],
        ['fable_share_email', true, 'mailto:?subject='.$encodedTitle.'&body='.$encodedUrl, __('Share by email'), __('Email'), 'envelope-simple', false],
    ];
    $links = array_values(array_filter($networks, fn (array $network): bool => (bool) ($themeSettings[$network[0]] ?? $network[1])));

    // The floating column is always round icons; the row is the site's choice.
    $withNames = $shareLabelled && ! ($floating && ! $isBottom);
    $ring = $withNames
        ? 'chip hover:bg-ink hover:text-white hover:shadow-none'
        : 'icon-btn shadow-[inset_0_0_0_1.5px_var(--color-rule)] hover:bg-ink hover:text-white hover:shadow-none';
    $iconClass = $withNames ? 'text-base' : '';
    $iconCopy = $floating && ! $isBottom;
@endphp
@if ($shareOn && $draws && ($links !== [] || $shareNative || $shareCopy))
    @if ($iconCopy)
        <div class="share-float" role="group" aria-label="{{ __('Share') }}"><div class="share-float-rail flex flex-col items-center gap-1.5">
    @else
        <div class="share-row {{ $floating && $isBottom ? 'share-float-fallback' : '' }} {{ $class ?? '' }}">
            @if ($isBottom && $shareLine !== '')
                <span class="meta font-bold text-ink mr-1.5">{{ $shareLine }}</span>
            @endif
    @endif
    @foreach ($links as $network)
        <a href="{{ $network[2] }}"@if ($network[6]) target="_blank" rel="noopener"@endif class="{{ $ring }}" aria-label="{{ $network[3] }}">@include('theme::icons.'.$network[5], ['class' => $iconClass])@if ($withNames)<span>{{ $network[4] }}</span>@endif</a>
    @endforeach
    @if ($shareNative)
        <span x-data="shareNative" x-show="supported" x-cloak class="inline-flex">
            <button type="button" class="{{ $ring }}" @click="share" aria-label="{{ __('Share with your apps') }}">@include('theme::icons.share-network', ['class' => $iconClass])@if ($withNames)<span>{{ __('Share') }}</span>@endif</button>
        </span>
    @endif
    @if ($shareCopy)
        <span x-data="copyLink" x-cloak class="inline-flex">
            @if ($iconCopy)
                <button type="button" class="{{ $ring }}" @click="copy" aria-live="polite" aria-label="{{ __('Copy link') }}">
                    <span x-show="idle" class="inline-flex">@include('theme::icons.link')</span>
                    <span x-show="copied" x-cloak class="inline-flex">@include('theme::icons.check')</span>
                </button>
            @else
                <button type="button" class="chip" @click="copy" aria-live="polite">
                    <span x-show="idle" class="inline-flex items-center gap-1.5">@include('theme::icons.link', ['class' => 'text-base']){{ __('Copy link') }}</span>
                    <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5">@include('theme::icons.check', ['class' => 'text-base']){{ __('Link copied') }}</span>
                </button>
            @endif
        </span>
    @endif
    @if ($iconCopy)
        </div></div>
    @else
        </div>
    @endif
@endif
