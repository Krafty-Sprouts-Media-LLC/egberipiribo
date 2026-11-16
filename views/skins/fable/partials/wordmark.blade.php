{{--
    The site's name set as the wordmark, a link home. Takes $class for its size. Never a heading: the page's one h1
    belongs to the page.

    @package egberipiribo
    @since   0.1.0
--}}
<a href="{{ $site->url }}" class="wordmark {{ $class ?? 'block' }}" aria-label="{{ __(':site home', ['site' => $site->name]) }}">{{ $site->name }}</a>
