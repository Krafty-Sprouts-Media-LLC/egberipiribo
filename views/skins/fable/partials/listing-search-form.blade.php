{{--
    The search form the listing pages share (the search header, the empty states, the not-found page): one plain GET
    form to the site's address with the field named `s`, drawn as the drawing's field with its magnifying glass.

    Takes optionally $id (the field's id, which must differ on a page holding two forms, 'search-term' by default),
    $value (what to prefill, '' by default), $class (spacing and width around the form) and $placeholder.

    Settings this partial reads:
      search_placeholder  string, "Search stories, sections and writers"  the words in the empty field (not yet declared
                          in the manifest; the default applies)

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $id = $id ?? 'search-term';
    $value = $value ?? '';
    $class = $class ?? 'mt-6 max-w-[40rem]';
    $placeholder = $placeholder ?? ($themeSettings['search_placeholder'] ?? __('Search stories, sections and writers'));
@endphp
<form class="flex flex-col sm:flex-row gap-2.5 {{ $class }}" role="search" action="{{ $site->url }}" method="get">
    <div class="relative flex-1">
        <label for="{{ $id }}" class="sr-only">{{ __('Search stories') }}</label>
        @include('theme::icons.magnifying-glass', ['class' => 'absolute start-4 top-1/2 -translate-y-1/2 text-lg text-muted pointer-events-none'])
        <input id="{{ $id }}" name="s" type="search" autocomplete="off" class="field ps-11" placeholder="{{ $placeholder }}" value="{{ $value }}">
    </div>
    <button type="submit" class="btn">{{ __('Search') }}</button>
</form>
