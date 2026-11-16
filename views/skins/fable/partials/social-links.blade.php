{{--
    The social menu as icon buttons. Each item's icon is chosen from its address: Instagram, X, Pinterest, Facebook, or
    a feed; anything else gets a link icon. The item's label is the button's name, so an editor writes "Instagram", not
    an icon. Takes $class for the row.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $links = $navigation['social'] ?? [];
    $iconFor = function (?string $url): string {
        $host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));
        $path = strtolower((string) parse_url((string) $url, PHP_URL_PATH));

        return match (true) {
            str_contains($host, 'instagram.') => 'instagram-logo',
            $host === 'x.com' || str_ends_with($host, '.x.com') || str_contains($host, 'twitter.') => 'x-logo',
            str_contains($host, 'pinterest.') => 'pinterest-logo',
            str_contains($host, 'facebook.') => 'facebook-logo',
            str_contains($path, 'feed') || str_contains($path, 'rss') => 'rss',
            default => 'link',
        };
    };
@endphp
@if ($links !== [])
    <ul class="flex gap-1 {{ $class ?? '' }}" aria-label="{{ __('Social links') }}">
        @foreach ($links as $link)
            @if ($link['url'] !== null)
                <li><a href="{{ $link['url'] }}" class="icon-btn" aria-label="{{ $link['label'] }}" rel="me noopener"@if ($link['opensNewTab']) target="_blank"@endif>@include('theme::icons.'.$iconFor($link['url']))</a></li>
            @endif
        @endforeach
    </ul>
@endif
