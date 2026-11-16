{{--
    Fable: the icon pattern for the home page's long read. A thin wrapper around the shared partial
    (partials/pattern.blade.php), which builds the tile: this one prints the bare CSS value of --fable-pattern (the
    pages put it on a solid area carrying the `.patterned` class), or nothing when the pattern is off or the long
    read's switch (fable_pattern_long_read) is off, so the area stays solid.

    Takes (all optional; what is left out is the pattern's default, not the site's setting):
      $pattern    none (off), sections, pets, home or food: the set of seven icons the tile is made of
      $strength   subtle, medium or bold: how visible the icons are
      $density    sparse, regular or dense: how close together they sit
      $accent     a #rrggbb colour: one icon in seven takes it (white and accent); empty keeps every icon white
    The icon colour mix, the layout and the outline or solid style come from the site's settings.

    @package egberipiribo
    @since   0.1.0
--}}
@include('theme::skins.fable.partials.pattern', [
    'area' => 'long_read',
    'mode' => 'value',
    'patternOverrides' => [
        'pattern' => $pattern ?? 'none',
        'pattern_strength' => $strength ?? 'subtle',
        'pattern_density' => $density ?? 'regular',
        'pattern_accent' => $accent ?? '',
    ],
])