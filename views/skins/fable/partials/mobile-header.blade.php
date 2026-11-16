{{--
    The header below 1024px: a menu button, the wordmark and a search button. The menu holds the same nav as the
    rail. The search button is a plain link to the search page without the script.

    @package egberipiribo
    @since   0.1.0
--}}
<header class="on-rail lg:hidden sticky top-0 z-40 bg-rail text-white">
    <div class="px-2 h-14 flex items-center justify-between">
        <button type="button" class="icon-btn" @click="toggleMenu" :aria-expanded="menuOpen" aria-controls="mobile-menu" aria-label="{{ __('Sections menu') }}">
            <span x-show="menuClosed">@include('theme::icons.list', ['class' => 'text-xl'])</span>
            <span x-show="menuOpen" x-cloak>@include('theme::icons.x', ['class' => 'text-xl'])</span>
        </button>
        @include('theme::skins.fable.partials.wordmark', ['class' => 'text-[1.875rem]'])
        <a href="{{ $site->url }}?s=" class="icon-btn" @click.prevent="openSearch" aria-label="{{ __('Search') }}">
            @include('theme::icons.magnifying-glass', ['class' => 'text-xl'])
        </a>
    </div>
    <nav id="mobile-menu" x-show="menuOpen" x-cloak x-transition.opacity.duration.150ms class="px-3 pb-4 pt-1 max-h-[75vh] overflow-y-auto border-t border-white/20" aria-label="{{ __('Sections') }}">
        @include('theme::skins.fable.partials.nav-sections', ['mobile' => true])
        <a href="#newsletter" class="btn btn-white w-full mt-3" @click="closeMenu">{{ __('Get our newsletter') }}</a>
    </nav>
</header>
