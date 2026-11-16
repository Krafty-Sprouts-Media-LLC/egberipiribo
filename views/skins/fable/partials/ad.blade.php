{{--
    An advertisement slot: a region an editor fills with a Custom HTML card, under the drawing's "Advertisement"
    label. The whole slot, label included, hides itself while the region is empty (see .ad-slot in the skin's CSS),
    so no page ever shows a label over nothing.

    Takes $region (the region's slug from the manifest) and optionally $class for spacing.

    Settings this template reads:
      show_ads   on/off, on   the master switch: off draws no slot at all, whatever the regions hold

    @package egberipiribo
    @since   0.1.0
--}}
@if ($themeSettings['show_ads'] ?? true)
<div class="ad-slot {{ $class ?? '' }}">
    <p class="ad-label">{{ __('Advertisement') }}</p>
    <div class="ad-body"><x-teme::region :name="$region" /></div>
</div>
@endif
