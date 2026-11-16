{{--
    A listing's pages, given $pagination (Teme's Pagination): Previous, up to seven page links with gaps, and Next,
    drawn as the drawing's round page numbers. Nothing when there is one page.

    @package egberipiribo
    @since   0.1.0
--}}
@if ($pagination->previous !== null || $pagination->next !== null)
    <nav class="flex flex-wrap items-center gap-2 mt-12" aria-label="{{ __('Pagination') }}">
        @if ($pagination->previous !== null)
            <a href="{{ $pagination->previous }}" class="page-num" rel="prev">@include('theme::icons.arrow-left')<span class="sr-only">{{ __('Previous page') }}</span></a>
        @else
            <span class="page-num" aria-disabled="true">@include('theme::icons.arrow-left')<span class="sr-only">{{ __('Previous page') }}</span></span>
        @endif
        @foreach ($pagination->pages() as $link)
            @if ($link['number'] === null)
                <span class="page-num" aria-hidden="true">…</span>
            @elseif ($link['current'])
                <span class="page-num" aria-current="page"><span class="sr-only">{{ __('Page') }} </span>{{ $link['number'] }}</span>
            @else
                <a href="{{ $link['url'] }}" class="page-num"><span class="sr-only">{{ __('Page') }} </span>{{ $link['number'] }}</a>
            @endif
        @endforeach
        @if ($pagination->next !== null)
            <a href="{{ $pagination->next }}" class="page-num gap-2 inline-flex items-center px-4" rel="next">{{ __('Next') }} @include('theme::icons.arrow-right')</a>
        @endif
    </nav>
@endif
