{{--
    The archive template. Teme chooses it; the active skin draws it. The skin is read in this one place, so a later
    update that adds skins (or a setting that chooses between them) changes nothing else.

    The lists this skin draws for it (the skin's own loops) place what plugins add between items with
    <x-teme::between-items :after="$loop->iteration" :last="$loop->last" />, through the skin's partials/between.

    @package egberipiribo
    @since   0.1.0
--}}
@include('theme::skins.'.($themeSettings['skin'] ?? 'fable').'.archive')
