{{--
    The search template. Teme chooses it; the active skin draws it. The skin is read in this one place, so a later
    update that adds skins (or a setting that chooses between them) changes nothing else.

    @package egberipiribo
    @since   0.1.0
--}}
@include('theme::skins.'.($themeSettings['skin'] ?? 'fable').'.search')
