{{--
    The breadcrumb trail, given $crumbs (a list of Crumb, or null when the theme draws none). The last step is the
    page itself and is not a link. Nothing for fewer than two steps.

    @package egberipiribo
    @since   0.1.0
--}}
@if (is_array($crumbs ?? null) && count($crumbs) > 1)
    <nav aria-label="{{ __('Breadcrumb') }}">
        <ol class="flex flex-wrap items-center gap-x-2 text-[.8125rem] font-semibold text-muted">
            @foreach ($crumbs as $crumb)
                <li>@if ($loop->last)<span aria-current="page" class="text-rail">{{ $crumb->name }}</span>@else<a href="{{ $crumb->url }}" class="hover:text-ink hover:underline underline-offset-4">{{ $crumb->name }}</a>@endif</li>
                @if (! $loop->last)<li aria-hidden="true">/</li>@endif
            @endforeach
        </ol>
    </nav>
@endif
