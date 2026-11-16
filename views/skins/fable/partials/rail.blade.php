{{--
    The rail, on screens from 1024px: the wordmark and tagline, search, the one nav, and the sign-up and social
    links. On an article it also holds the "In this story" list (the page's `toc` section).

    Takes $searchPanel, $searchShortcut and $newsletterBand (bool each; layout.blade.php reads the settings once and
    passes them in).

    Settings this template reads:
      fable_search_panel      on/off, on   the search button opens the panel; off: it is a plain link to the search page
      fable_search_shortcut   on/off, on   the "/" hint on the search button (the key itself is the layout's)
      show_newsletter, fable_footer_newsletter   on/off, on and on   the "Get our newsletter" button (off removes it)
      fable_pattern_rail      on/off, on   the icon pattern on the rail (only while the pattern is not "none")

    @package egberipiribo
    @since   0.1.0
--}}
<aside class="on-rail fixed inset-y-0 left-0 z-40 hidden lg:flex flex-col w-[17rem] bg-rail text-white rail-scroll overflow-y-auto @include('theme::skins.fable.partials.pattern', ['area' => 'rail', 'mode' => 'class'])" aria-label="{{ __('Site') }}">
    <div class="px-6 pt-7">
        @include('theme::skins.fable.partials.wordmark', ['class' => 'block'])
        @if ($site->tagline !== '')
            <p class="text-[.875rem] font-medium text-white/75 mt-2">{{ $site->tagline }}</p>
        @endif
    </div>

    <div class="px-4 mt-6">
        <a href="{{ $site->url }}?s=" class="rail-search"@if ($searchPanel) @click.prevent="openSearch"@endif>
            @include('theme::icons.magnifying-glass', ['class' => 'text-base'])
            {{ __('Search') }}@if ($searchShortcut)<kbd aria-hidden="true">/</kbd>@endif
        </a>
    </div>

    @hasSection('toc')
        @yield('toc')
    @endif

    <nav class="px-4 mt-7 flex-1" aria-label="{{ __('Sections') }}">
        <p class="rail-label px-2 mb-2">{{ __('Sections') }}</p>
        @include('theme::skins.fable.partials.nav-sections', ['mobile' => false])
    </nav>

    <div class="px-4 pb-4 pt-6">
        @if ($newsletterBand)
            <a href="#newsletter" class="btn btn-white w-full">{{ __('Get our newsletter') }}</a>
        @endif
        @include('theme::skins.fable.partials.social-links', ['class' => $newsletterBand ? 'mt-3' : ''])
    </div>
</aside>
