{{--
    The site's sections as chips, from the primary menu: where the drawings offer "pick a section" (the search page's
    empty states, the not-found page). Draws nothing when the menu has no linked entry. A heading entry or one
    without an address is left out.

    Takes optionally $class (spacing) and $label (the list's name for assistive technology).

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $sectionLinks = collect($navigation['primary'] ?? [])
        ->filter(fn ($entry) => ($entry['url'] ?? null) !== null && ! ($entry['heading'] ?? false))
        ->values();
@endphp
@if ($sectionLinks->isNotEmpty())
    <ul class="flex flex-wrap items-center gap-2 {{ $class ?? '' }}" aria-label="{{ $label ?? __('Browse a section') }}">
        @foreach ($sectionLinks as $entry)
            <li><a href="{{ $entry['url'] }}" class="chip"@if ($entry['opensNewTab'] ?? false) target="_blank" rel="noopener"@endif>{{ $entry['label'] }}</a></li>
        @endforeach
    </ul>
@endif
