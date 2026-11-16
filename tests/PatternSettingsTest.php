<?php

declare(strict_types=1);

/**
 * The icon pattern: the shared partial (partials/pattern.blade.php) prints a class, a style attribute or the bare CSS
 * value only when the pattern is on AND the area's own switch is on; the page shell carries the tile once, and the
 * rail, the phone header and the footer wear it. The tile follows the strength, density, accent, colour, layout and
 * style settings. Off by default, so nothing about a page changes until an administrator picks a pattern.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** The pattern settings this file proves and the default each one draws. */
const PATTERN_DEFAULTS = [
    'pattern' => 'none',
    'pattern_strength' => 'subtle',
    'pattern_density' => 'regular',
    'pattern_accent' => '',
    'fable_pattern_colour' => 'mixed',
    'fable_pattern_layout' => 'scatter',
    'fable_pattern_style' => 'regular',
    'fable_pattern_rail' => true,
    'fable_pattern_phone_header' => true,
    'fable_pattern_footer' => true,
    'fable_pattern_long_read' => true,
    'fable_pattern_sections' => false,
    'fable_pattern_404' => false,
];

/** Renders the shared partial for an area and mode against the settings last published (Teme adds themeSettings to the view itself). */
function patternPartial(string $area, string $mode): string
{
    return trim(view('theme::skins.fable.partials.pattern', ['area' => $area, 'mode' => $mode])->render());
}

/** The tile's SVG as the page shell carries it, decoded; null when the page has none. */
function patternTile(string $html): ?string
{
    if (preg_match('/<div data-skin="fable"[^>]*style="--fable-pattern: ([^"]*)"/', $html, $found) !== 1) {
        return null;
    }

    $value = html_entity_decode($found[1]);
    $start = strpos($value, 'svg+xml,');

    return rawurldecode(substr($value, $start + 8, -2));
}

/** Whether the opening tag of the first <aside>, <header> or <footer> of the page carries the class `patterned`. */
function patternWorn(string $html, string $tag): bool
{
    return preg_match('/<'.$tag.' class="[^"]*\bpatterned\b[^"]*"/', $html) === 1;
}

it('declares every pattern setting in the manifest with the default the template uses', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);
    $declared = [];
    foreach ($manifest['settings'] as $setting) {
        $declared[$setting['key']] = $setting['default'] ?? null;
    }

    foreach (PATTERN_DEFAULTS as $key => $default) {
        expect($declared)->toHaveKey($key)
            ->and($declared[$key])->toBe($default, "default of {$key}");
    }
});

it('prints nothing while the pattern is none, and only for an area whose switch is on', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    $areas = ['rail', 'phone_header', 'footer', 'long_read', 'sections', '404', 'shell'];

    // Defaults (the pattern is none): nothing, in every mode.
    foreach ($areas as $area) {
        expect(patternPartial($area, 'class'))->toBe('')
            ->and(patternPartial($area, 'style'))->toBe('')
            ->and(patternPartial($area, 'value'))->toBe('');
    }

    // A pattern picked, every switch at its default: the rail, the phone header, the footer and the long read wear it;
    // the section headings and the 404 do not.
    fableSettings($this, ['pattern' => 'sections'] + PATTERN_DEFAULTS);
    foreach (['rail', 'phone_header', 'footer', 'long_read', 'shell'] as $area) {
        expect(patternPartial($area, 'class'))->toBe('patterned');
    }
    expect(patternPartial('sections', 'class'))->toBe('')
        ->and(patternPartial('404', 'class'))->toBe('')
        // An area the partial does not know draws nothing.
        ->and(patternPartial('no-such-area', 'class'))->toBe('');

    // Each switch turns off its own area only; the shell keeps the tile while any chrome area wears it.
    fableSettings($this, ['pattern' => 'pets', 'fable_pattern_rail' => false, 'fable_pattern_sections' => true, 'fable_pattern_404' => true] + PATTERN_DEFAULTS);
    expect(patternPartial('rail', 'class'))->toBe('')
        ->and(patternPartial('phone_header', 'class'))->toBe('patterned')
        ->and(patternPartial('footer', 'class'))->toBe('patterned')
        ->and(patternPartial('shell', 'class'))->toBe('patterned')
        ->and(patternPartial('sections', 'class'))->toBe('patterned')
        ->and(patternPartial('404', 'class'))->toBe('patterned');

    fableSettings($this, ['pattern' => 'food', 'fable_pattern_phone_header' => false, 'fable_pattern_footer' => false, 'fable_pattern_long_read' => false] + PATTERN_DEFAULTS);
    expect(patternPartial('phone_header', 'class'))->toBe('')
        ->and(patternPartial('footer', 'class'))->toBe('')
        ->and(patternPartial('long_read', 'class'))->toBe('')
        ->and(patternPartial('rail', 'class'))->toBe('patterned')
        ->and(patternPartial('shell', 'class'))->toBe('patterned');

    fableSettings($this, ['pattern' => 'food', 'fable_pattern_rail' => false, 'fable_pattern_phone_header' => false, 'fable_pattern_footer' => false] + PATTERN_DEFAULTS);
    expect(patternPartial('shell', 'class'))->toBe('');
});

it('prints the style attribute escaped, and the bare value for a caller that escapes it itself', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    fableSettings($this, ['pattern' => 'home'] + PATTERN_DEFAULTS);

    $style = patternPartial('rail', 'style');
    $value = patternPartial('long_read', 'value');

    expect($style)->toStartWith('style="--fable-pattern: url(&quot;data:image/svg+xml,%3Csvg')
        ->and($style)->toEndWith('&quot;)"')
        ->and($style)->not->toContain('<')
        ->and($value)->toStartWith('url("data:image/svg+xml,%3Csvg')
        ->and($value)->toEndWith('")')
        // Nothing is fetched from outside: the only address in the tile is the SVG namespace.
        ->and(str_replace('http://www.w3.org/2000/svg', '', rawurldecode($value)))->not->toContain('http');
});

it('wraps the shared partial for the home page\'s long read, and honours its off switch', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    fableSettings($this, ['pattern' => 'none'] + PATTERN_DEFAULTS);

    $args = ['pattern' => 'sections', 'strength' => 'medium', 'density' => 'regular', 'accent' => '#ffd23f'];
    $on = trim(view('theme::skins.fable.partials.home-pattern', $args)->render());

    expect($on)->toStartWith('url("data:image/svg+xml,%3Csvg')
        ->and($on)->toContain('%23ffd23f')
        // Medium strength: white icons at .16, the accent icon at .9.
        ->and(rawurldecode($on))->toContain("fill-opacity='0.16'")
        ->and(rawurldecode($on))->toContain("fill-opacity='0.9'");

    fableSettings($this, ['pattern' => 'sections', 'fable_pattern_long_read' => false] + PATTERN_DEFAULTS);
    expect(trim(view('theme::skins.fable.partials.home-pattern', $args)->render()))->toBe('');
});

it('draws nothing on a page by default, and puts the tile on the shell and the three areas when a pattern is picked', function (): void {
    $this->publish('A tiled story');
    $this->theme(__DIR__.'/..')->activate();

    $plain = $this->fetch('/a-tiled-story/')->assertOk()->getContent();
    expect(substr_count($plain, '<h1'))->toBe(1)
        ->and($plain)->not->toContain('--fable-pattern')
        ->and($plain)->not->toContain('patterned');

    fableSettings($this, ['pattern' => 'sections'] + PATTERN_DEFAULTS);
    $html = $this->fetch('/a-tiled-story/')->assertOk()->getContent();

    // The tile is on the shell once; the rail, the phone header and the footer wear the class.
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count($html, '--fable-pattern:'))->toBe(1)
        ->and(patternTile($html))->toStartWith('<svg xmlns=')
        ->and(patternWorn($html, 'aside'))->toBeTrue()
        ->and(patternWorn($html, 'header'))->toBeTrue()
        ->and(patternWorn($html, 'footer'))->toBeTrue();

    // One switch off: that area is solid, the others keep the pattern.
    fableSettings($this, ['pattern' => 'sections', 'fable_pattern_rail' => false, 'fable_pattern_footer' => false] + PATTERN_DEFAULTS);
    $some = $this->fetch('/a-tiled-story/')->assertOk()->getContent();
    expect(patternWorn($some, 'aside'))->toBeFalse()
        ->and(patternWorn($some, 'header'))->toBeTrue()
        ->and(patternWorn($some, 'footer'))->toBeFalse();

    // All three off: no tile at all.
    fableSettings($this, ['pattern' => 'sections', 'fable_pattern_rail' => false, 'fable_pattern_phone_header' => false, 'fable_pattern_footer' => false] + PATTERN_DEFAULTS);
    $none = $this->fetch('/a-tiled-story/')->assertOk()->getContent();
    expect($none)->not->toContain('--fable-pattern')
        ->and($none)->not->toContain('patterned');
});

it('builds the tile the settings ask for', function (): void {
    $this->publish('A tiled story');
    $this->theme(__DIR__.'/..')->activate();

    $tile = function (array $settings): string {
        fableSettings($this, $settings + ['pattern' => 'sections'] + PATTERN_DEFAULTS);

        return (string) patternTile($this->fetch('/a-tiled-story/')->assertOk()->getContent());
    };

    // The default look: scattered, outline, regular density, subtle, every icon white (no accent colour).
    $default = $tile([]);
    expect($default)->toContain("width='532'")
        ->and($default)->toContain("fill='#fff' fill-opacity='0.09'")
        ->and($default)->toContain('rotate(-14')
        ->and($default)->not->toContain('<circle');

    // Density and strength.
    expect($tile(['pattern_density' => 'sparse', 'pattern_strength' => 'bold']))->toContain("width='700'")->toContain("fill-opacity='0.26'")
        ->and($tile(['pattern_density' => 'dense']))->toContain("width='392'");

    // Colour: with an accent, white plus one accent icon; all accent; or all white (the accent is then left out).
    expect($tile(['pattern_accent' => '#ffd23f']))->toContain("fill='#ffd23f' fill-opacity='0.8'")->toContain("fill='#fff'")
        ->and($tile(['pattern_accent' => '#ffd23f', 'fable_pattern_colour' => 'accent']))->toContain("fill='#ffd23f' fill-opacity='0.34'")->not->toContain("fill='#fff'")
        ->and($tile(['pattern_accent' => '#ffd23f', 'fable_pattern_colour' => 'white']))->not->toContain('#ffd23f')
        ->and($tile(['fable_pattern_colour' => 'accent']))->not->toContain('#ffd23f');

    // Layout: straight rows (no turn), and straight rows with dots between.
    expect($tile(['fable_pattern_layout' => 'grid']))->not->toContain('rotate(-14')->not->toContain('<circle')
        ->and($tile(['fable_pattern_layout' => 'dots']))->not->toContain('rotate(-14')->toContain('<circle');

    // Style: the solid icons are different shapes (the solid house has no door line, the outline one does).
    expect($tile(['fable_pattern_style' => 'fill']))->toContain('M240,208H224V136l2.34,2.34')->toContain('Zm-88,0H104V160a4,4,0,0,1,4-4h40')
        ->and($default)->not->toContain('Zm-88,0H104V160a4,4,0,0,1,4-4h40');

    // Each icon set draws its own icons: pets has a bone, sections does not.
    fableSettings($this, ['pattern' => 'pets'] + PATTERN_DEFAULTS);
    expect((string) patternTile($this->fetch('/a-tiled-story/')->assertOk()->getContent()))->toContain('M231.67,60.89')
        ->and($default)->not->toContain('M231.67,60.89');
});
