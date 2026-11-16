{{--
    The search panel over the page: one plain GET form to the site's search, opened by the search buttons or "/".
    Without the script the buttons are links to the search page.

    @package egberipiribo
    @since   0.1.0
--}}
<div x-show="searchOpen" x-cloak x-transition.opacity.duration.150ms class="fixed inset-0 z-50 bg-ink/60 backdrop-blur-[2px] overflow-y-auto p-4 sm:p-6" role="dialog" aria-modal="true" aria-label="{{ __('Search :site', ['site' => $site->name]) }}" @click.self="closeSearch">
    <div class="max-w-[40rem] mx-auto mt-[6vh] bg-white rounded-[14px] overflow-hidden shadow-[0_24px_60px_rgb(18_19_23_/_.35)]">
        <form action="{{ $site->url }}" method="get" role="search" class="flex items-center gap-3 px-5 border-b border-rule">
            @include('theme::icons.magnifying-glass', ['class' => 'text-xl text-muted'])
            <label for="search-panel-q" class="sr-only">{{ __('Search stories') }}</label>
            <input id="search-panel-q" x-ref="q" name="s" type="search" autocomplete="off" placeholder="{{ __('Search stories, sections and writers') }}" class="flex-1 min-w-0 py-5 text-lg font-medium bg-transparent outline-none placeholder:text-muted">
            <button type="submit" class="btn py-2.5 px-4 text-sm">{{ __('Search') }}</button>
            <button type="button" class="chip min-h-0 py-1.5 px-2.5 text-xs" @click="closeSearch" aria-label="{{ __('Close search (Esc)') }}">Esc</button>
        </form>
        <p class="px-5 py-3.5 bg-surface meta">{{ __('Press Enter to see every result') }}</p>
    </div>
</div>
