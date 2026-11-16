{{--
    The footer: the newsletter band (the `newsletter` region, which an editor fills with a sign-up form from a
    Custom HTML card, and which is left out while it is empty), then the site's name and tagline with two menus
    (`footer` and `network`), then the copyright. It complements the rail and never repeats its nav.

    Choices a later update can turn into theme settings, each read in one place here:
      footer_newsletter_heading, footer_newsletter_text, footer_blurb

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $newsletterHeading = $themeSettings['footer_newsletter_heading'] ?? __('Never miss a story.');
    $newsletterText = $themeSettings['footer_newsletter_text'] ?? __('The best stories of the week, picked by an editor and sent once. You can leave in one click.');
    $footerBlurb = $themeSettings['footer_blurb'] ?? $site->tagline;
    $company = $navigation['footer'] ?? [];
    $network = $navigation['network'] ?? [];
@endphp
<footer class="bg-ink text-white">
    {{-- The region draws nothing while it is empty; the band then hides itself (see .newsletter-band in the skin's CSS). --}}
    <div id="newsletter" class="newsletter-band wrap py-14 lg:py-16 grid lg:grid-cols-[minmax(0,1fr)_minmax(0,27rem)] gap-x-16 gap-y-8 items-end scroll-mt-16">
        <div>
            <h2 class="hed hed-page">{{ $newsletterHeading }}</h2>
            <p class="deck text-white/80 text-[1.125rem] mt-4 max-w-[34rem]">{{ $newsletterText }}</p>
        </div>
        <div class="newsletter-slot"><x-teme::region name="newsletter" /></div>
    </div>

    <div class="border-t border-white/15">
        <div class="wrap py-10 grid sm:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)] gap-x-12 gap-y-8 text-[.9375rem]">
            <div>
                <p class="wordmark text-[2.25rem]">{{ $site->name }}</p>
                @if ($footerBlurb !== '')
                    <p class="text-white/70 leading-relaxed mt-3 max-w-xs">{{ $footerBlurb }}</p>
                @endif
            </div>
            @if ($company !== [])
                <nav aria-label="{{ __('Company') }}">
                    <p class="rail-label mb-3.5">{{ __('Company') }}</p>
                    <ul class="space-y-2.5 font-medium">
                        @foreach ($company as $item)
                            <li>@if ($item['url'] !== null)<a href="{{ $item['url'] }}" class="hover:underline underline-offset-4"@if ($item['opensNewTab']) target="_blank" rel="noopener"@endif>{{ $item['label'] }}</a>@else{{ $item['label'] }}@endif</li>
                        @endforeach
                    </ul>
                </nav>
            @endif
            @if ($network !== [])
                <nav aria-label="{{ __('Our network') }}">
                    <p class="rail-label mb-3.5">{{ __('Our network') }}</p>
                    <ul class="space-y-2.5 font-medium">
                        @foreach ($network as $item)
                            <li>@if ($item['url'] !== null)<a href="{{ $item['url'] }}" class="hover:underline underline-offset-4"@if ($item['opensNewTab']) target="_blank" rel="noopener"@endif>{{ $item['label'] }}</a>@else{{ $item['label'] }}@endif</li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>
    </div>

    <div class="border-t border-white/15">
        <div class="wrap py-5 text-[.8125rem] text-white/60">© {{ $site->year }} {{ $site->name }}. {{ __('All rights reserved.') }}</div>
    </div>
</footer>
