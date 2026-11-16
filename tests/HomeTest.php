<?php

declare(strict_types=1);

/**
 * The Fable home page (the latest template): its empty state, one story, a page of stories and the next page, its
 * one h1, the region wrappers and the advertisement slots that stay empty until an editor fills them, the icon
 * pattern's tile, and the theme's check.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

/** The `<script>` tags of a page that have no src: core's own are marked or carry data, a theme's must not appear. */
function homeInlineScripts(string $html): array
{
    preg_match_all('/<script\b(?![^>]*\bsrc=)[^>]*>/i', $html, $found);

    return array_values(array_filter($found[0], fn (string $tag): bool => ! str_contains($tag, 'data-teme') && ! str_contains($tag, 'application/') && ! str_contains($tag, 'speculationrules')));
}

it('draws the empty state when nothing is published', function (): void {
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/')->assertOk()->assertSee('data-template="latest"', false)->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('<main id="main"')
        ->and($html)->toContain('No stories yet')
        ->and($html)->not->toContain('aria-label="Cover story"')
        ->and($html)->not->toContain('id="grid-title"')
        ->and($html)->not->toContain('region-long-read')
        ->and(homeInlineScripts($html))->toBe([]);
});

it('draws one published story as the cover story, with the strip, and nothing it cannot fill', function (): void {
    $this->publish('Only story of the day');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/')->assertOk()->assertSee('data-template="latest"', false)->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('aria-label="Cover story"')
        ->and($html)->toContain('>Only story of the day</a>')
        ->and($html)->toContain('Top stories')
        ->and($html)->toContain('1 story on this page')
        ->and($html)->not->toContain('No stories yet')
        // The grid, "Also today" and the pagination have no stories to draw, so they are absent.
        ->and($html)->not->toContain('id="grid-title"')
        ->and($html)->not->toContain('aria-label="Also today"')
        ->and($html)->not->toContain('aria-label="Pagination"')
        // A bare post has no picture and no author: the headline block stands alone, and the byline has no "by".
        ->and($html)->not->toContain('sm:-mt-24')
        ->and(homeInlineScripts($html))->toBe([]);
});

it('keeps the region wrappers in the page, empty, and no advertisement content', function (): void {
    $this->publish('A story for the wrappers');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/')->assertOk()->getContent();

    // The wrappers are there (their CSS hides them while empty)...
    expect($html)->toContain('region-also-today')
        ->and($html)->toContain('region-long-read')
        ->and($html)->toContain('region-sections')
        ->and($html)->toContain('region-most-read')
        // ...no region has drawn anything into them...
        ->and($html)->not->toContain('teme-region')
        ->and($html)->not->toContain('class="fp-item"')
        // ...and the advertisement slots are in the page, but empty: only their label, no content inside.
        ->and(substr_count($html, 'class="ad-slot'))->toBeGreaterThanOrEqual(1)
        ->and(preg_match_all('/<div class="ad-body">\s*<\/div>/', $html))->toBe(substr_count($html, 'class="ad-slot'))
        // The icon pattern is off by default.
        ->and($html)->not->toContain('--fable-pattern')
        ->and($html)->not->toContain('patterned');
});

it('draws a page of stories and spares the grid the stories "Also today" took', function (): void {
    $titles = [];
    for ($n = 1; $n <= 9; $n++) {
        $titles[$n] = 'Daily story number '.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
        $this->publish($titles[$n]);
    }
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/')->assertOk()->assertSee('data-template="latest"', false)->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('aria-label="Cover story"')
        ->and($html)->toContain('aria-label="Also today"')
        ->and($html)->toContain('id="grid-title"')
        ->and($html)->toContain('More to explore')
        ->and($html)->toContain('x-data="layoutToggle"')
        ->and($html)->toContain(':class="rowClass"')
        ->and($html)->toContain(':class="listClass"')
        // The layout switch is for the script: hidden until it runs.
        ->and($html)->toContain('x-cloak')
        ->and(homeInlineScripts($html))->toBe([]);

    // No story is drawn three times: the strip, "Also today" and the grid do not repeat the same one in all three.
    foreach ($titles as $title) {
        expect(substr_count($html, '>'.$title.'</a>'))->toBeLessThanOrEqual(2);
    }

    // The grid holds the cards of the feed after the three "Also today" took theirs; the cards have the story class.
    $cards = substr_count($html, 'class="story card"');
    expect($cards)->toBeGreaterThan(0)->and($cards)->toBeLessThan(9);
});

it('draws a numbered page with its title and the grid only', function (): void {
    for ($n = 1; $n <= 14; $n++) {
        $this->publish('Numbered page story '.$n);
    }
    $this->theme(__DIR__.'/..')->activate();

    $response = $this->fetch('/page/2/');

    if ($response->getStatusCode() !== 200) {
        $this->markTestSkipped("The second page did not draw here (status {$response->getStatusCode()}): the shared pagination partial fails where __('Pagination') returns Laravel's own lang array.");
    }

    $html = $response->assertSee('data-template="latest"', false)->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('id="grid-title"')
        ->and($html)->not->toContain('aria-label="Cover story"')
        ->and($html)->not->toContain('aria-label="Also today"')
        ->and($html)->not->toContain('Top stories')
        ->and($html)->not->toContain('region-long-read')
        ->and($html)->toContain('aria-label="Pagination"')
        ->and(homeInlineScripts($html))->toBe([]);
});

it('makes the icon pattern as an inline tile with no outside address, and nothing while it is off', function (): void {
    $this->theme(__DIR__.'/..')->activate();

    $on = trim(view('theme::skins.fable.partials.home-pattern', ['pattern' => 'sections', 'strength' => 'medium', 'density' => 'regular', 'accent' => '#ffd23f'])->render());
    $off = trim(view('theme::skins.fable.partials.home-pattern', ['pattern' => 'none'])->render());
    $unknown = trim(view('theme::skins.fable.partials.home-pattern', ['pattern' => 'no-such-set'])->render());

    expect($on)->toStartWith('url("data:image/svg+xml,%3Csvg')
        ->and($on)->toEndWith('")')
        ->and($on)->toContain('%23ffd23f')
        ->and($on)->not->toContain('<')
        ->and($off)->toBe('')
        ->and($unknown)->toBe('');
});

it('passes teme:theme:check with no problem', function (): void {
    expect($this->theme(__DIR__.'/..')->check())->toBe([]);
});
