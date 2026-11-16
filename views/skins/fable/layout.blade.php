{{--
    Fable: the frame of every page. Core's document, then the page shell (rail, small-screen header, canvas, search
    panel, back to top). A page fills these sections:
      - main       the page's one <main>, its content (required)
      - before     what sits above <main>, inside the canvas (the top stories strip, an advertisement)
      - toc        the "In this story" list in the rail, an article only
      - progress   the reading bar, an article only

    @package egberipiribo
    @since   0.1.0
--}}
<x-teme::document>
    <div x-data="fableShell" :class="shellClass" @keydown.escape.window="closeAll" @keydown.window="onSlash">
        <div x-ref="top" class="absolute top-0 h-[70vh] w-px pointer-events-none" aria-hidden="true"></div>

        @yield('progress')

        @include('theme::skins.fable.partials.rail')
        @include('theme::skins.fable.partials.mobile-header')

        <div class="lg:ml-[17rem]">
            @yield('before')

            <main id="main" class="wrap" tabindex="-1">
                @yield('main')
            </main>

            @include('theme::skins.fable.partials.footer')
        </div>

        @include('theme::skins.fable.partials.search-panel')

        <button type="button" x-show="scrolled" x-cloak x-transition.opacity.duration.200ms @click="backToTop"
                class="btn fixed z-30 bottom-5 right-5 w-12 h-12 p-0 text-lg bg-ink hover:bg-rail shadow-[0_0_0_2px_#fff]"
                aria-label="{{ __('Back to top') }}">
            @include('theme::icons.arrow-up')
        </button>
    </div>
</x-teme::document>
