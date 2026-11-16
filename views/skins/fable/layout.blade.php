{{--
    Fable: the frame of every page. Core's document, then the page shell (rail, small-screen header, canvas, search
    panel, back to top). A page fills these sections:
      - template   the kind of page, written on <main> as data-template (home, article, page, archive, ...)
      - main       the page's one <main>, its content (required)
      - before     what sits above <main>, inside the canvas (the top stories strip, an advertisement)
      - toc        the "In this story" list in the rail, an article only
      - progress   the reading bar, an article only

    Settings this template reads (once, below; the partials get them as parameters):
      fable_search_panel      on/off, on          the search panel; off: the search buttons are plain links
      fable_search_shortcut   on/off, on          the "/" key opens the search (and the hint in the rail); the key's
                                                  handler (onSlash) is only attached while this and the panel are on
      show_newsletter, fable_footer_newsletter   on/off, on and on   the footer's newsletter band and the buttons
                                                  that point to it (both must be on)
      pattern and the fable_pattern_* switches    the icon pattern's tile, set once on the page shell
                                                  (partials/pattern.blade.php, area "shell"); the areas wear it

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $searchPanel = (bool) ($themeSettings['fable_search_panel'] ?? true);
    $searchShortcut = $searchPanel && (bool) ($themeSettings['fable_search_shortcut'] ?? true);
    $newsletterBand = (bool) ($themeSettings['show_newsletter'] ?? true) && (bool) ($themeSettings['fable_footer_newsletter'] ?? true);
@endphp
<x-teme::document>
    <div data-skin="fable" x-data="fableShell" :class="shellClass" @keydown.escape.window="closeAll"@if ($searchShortcut) @keydown.window="onSlash"@endif @include('theme::skins.fable.partials.pattern', ['area' => 'shell', 'mode' => 'style'])>
        <div x-ref="top" class="absolute top-0 h-[70vh] w-px pointer-events-none" aria-hidden="true"></div>

        @yield('progress')

        @include('theme::skins.fable.partials.rail', ['searchPanel' => $searchPanel, 'searchShortcut' => $searchShortcut, 'newsletterBand' => $newsletterBand])
        @include('theme::skins.fable.partials.mobile-header', ['searchPanel' => $searchPanel, 'newsletterBand' => $newsletterBand])

        <div class="lg:ml-[17rem]">
            @yield('before')

            <main id="main" class="wrap" tabindex="-1" data-template="@yield('template')">
                @yield('main')
            </main>

            @include('theme::skins.fable.partials.footer', ['newsletterBand' => $newsletterBand])
        </div>

        @include('theme::skins.fable.partials.search-panel', ['searchPanel' => $searchPanel])

        <button type="button" x-show="scrolled" x-cloak x-transition.opacity.duration.200ms @click="backToTop"
                class="btn fixed z-30 bottom-5 right-5 w-12 h-12 p-0 text-lg bg-ink hover:bg-rail shadow-[0_0_0_2px_#fff]"
                aria-label="{{ __('Back to top') }}">
            @include('theme::icons.arrow-up')
        </button>
    </div>
</x-teme::document>
