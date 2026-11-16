<?php

declare(strict_types=1);

/**
 * The pattern's "section headers" and "404" areas: with a pattern chosen and the area's switch on, the front page's
 * "from the sections" slot, an archive's header and the 404 draw the decorative brand-colour strip that carries the
 * tile; with the pattern off, or the area's switch off, none does.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
require_once __DIR__.'/Support/settings.php';

uses(Teme\Testing\TemeTestCase::class);

it('draws the pattern strip only where the pattern and the area switch are on', function (): void {
    $this->publish('A story for the strip');
    $this->theme(__DIR__.'/..')->activate();
    $year = date('Y');

    $pages = ['home' => '/', 'archive' => '/'.$year.'/', 'notfound' => '/no-such-page-here/'];
    foreach ($pages as $name => $path) {
        expect($this->fetch($path)->getContent())->not->toContain('pattern-band'); // $name, by default
    }

    // A pattern chosen, but both areas off (their defaults): still no strip.
    fableSettings($this, ['pattern' => 'pets', 'fable_pattern_sections' => false, 'fable_pattern_404' => false]);
    foreach ($pages as $name => $path) {
        expect($this->fetch($path)->getContent())->not->toContain('class="pattern-band');
    }

    fableSettings($this, ['pattern' => 'pets', 'fable_pattern_sections' => true, 'fable_pattern_404' => true]);
    foreach ($pages as $name => $path) {
        $html = $this->fetch($path)->getContent();
        expect($html)->toContain('<div class="pattern-band patterned" aria-hidden="true"></div>')
            ->and(substr_count($html, '<h1'))->toBe(1);
    }

    // The 404 strip follows its own switch, not the section headers'.
    fableSettings($this, ['pattern' => 'pets', 'fable_pattern_sections' => true, 'fable_pattern_404' => false]);
    expect($this->fetch('/no-such-page-here/')->getContent())->not->toContain('class="pattern-band');
    expect($this->fetch('/'.$year.'/')->getContent())->toContain('class="pattern-band');
});
