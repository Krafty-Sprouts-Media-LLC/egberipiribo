<?php

declare(strict_types=1);

/**
 * The Fable home page's theme settings (every "Home:" setting): each is declared in the manifest with the default
 * the template uses, the default page draws every block, and each switch removes its block and the block's wrapper
 * from the page while the page keeps its one h1.
 *
 * Not reachable through the testing kit, which makes bare posts only (no picture, section, author or editor-filled
 * region): the effect of the kicker and picture switches on real content (fable_home_cover_kicker,
 * fable_home_cover_standfirst, fable_home_also_today_kicker, fable_home_also_today_thumb), and what the editor's
 * region lists look like with the rank numbers off. For those the page's wrapper classes are asserted instead.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** Every Home setting this file covers, with its default (all switches on). */
function homeSettingKeys(): array
{
    return [
        'fable_home_title_visible', 'show_story_count', 'fable_home_top_stories', 'fable_home_top_stories_rank',
        'fable_home_cover', 'fable_home_cover_kicker', 'fable_home_cover_standfirst', 'fable_home_cover_byline',
        'fable_home_also_today', 'fable_home_also_today_kicker', 'fable_home_also_today_thumb',
        'fable_home_ad_leaderboard', 'fable_home_ad_in_feed', 'fable_home_ad_rectangle', 'fable_home_long_read',
        'fable_home_grid', 'show_layout_toggle', 'fable_home_sections', 'fable_home_most_read',
        'fable_home_most_read_rank',
    ];
}

/** All switches on, with the given ones changed. */
function homeSettings(array $changes): array
{
    return $changes + array_fill_keys(homeSettingKeys(), true);
}

it('declares every Home setting in the manifest, on by default', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);
    $declared = [];
    foreach ($manifest['settings'] as $setting) {
        $declared[$setting['key']] = $setting;
    }

    foreach (homeSettingKeys() as $key) {
        expect($declared)->toHaveKey($key)
            ->and($declared[$key]['type'])->toBe('boolean')
            ->and($declared[$key]['default'])->toBeTrue();
    }
});

it('draws every home block by default, with one h1 and the title in view', function (): void {
    for ($n = 1; $n <= 9; $n++) {
        $this->publish('Home settings story '.$n);
    }
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('hed hed-page sr-only')
        ->and($html)->toContain('stories on this page')
        ->and($html)->toContain('Top stories')
        ->and($html)->toContain('class="rank text-[1.375rem]"')
        ->and($html)->toContain('aria-label="Cover story"')
        ->and($html)->toContain('meta mt-3.5')
        ->and($html)->toContain('aria-label="Also today"')
        ->and($html)->toContain('region-also-today"')
        ->and($html)->not->toContain('no-kicker')
        ->and($html)->not->toContain('no-thumb')
        ->and($html)->toContain('region-long-read')
        ->and($html)->toContain('id="grid-title"')
        ->and($html)->toContain('aria-label="Layout"')
        ->and($html)->toContain('region-sections')
        ->and($html)->toContain('region-most-read"')
        ->and($html)->toContain('class="home-most"')
        ->and(substr_count($html, 'class="ad-slot'))->toBe(3);
});

it('lets each switch take its block out of the page, wrapper and all', function (): void {
    for ($n = 1; $n <= 9; $n++) {
        $this->publish('Home switch story '.$n);
    }
    $this->theme(__DIR__.'/..')->activate();

    // The default page: a cover, 3 in "Also today", so 5 cards in the grid.
    $default = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($default, 'class="story card"'))->toBe(5);

    // Title hidden (kept for assistive technology), no count, no rank, no cover byline, no long read, no layout
    // buttons, no leaderboard.
    fableSettings($this, homeSettings([
        'fable_home_title_visible' => false, 'show_story_count' => false, 'fable_home_top_stories_rank' => false,
        'fable_home_cover_byline' => false, 'fable_home_long_read' => false, 'show_layout_toggle' => false,
        'fable_home_ad_leaderboard' => false,
    ]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('hed hed-page sr-only')
        ->and($html)->toContain('<header class="pt-8">')
        ->and($html)->not->toContain('stories on this page')
        ->and($html)->toContain('Top stories')
        ->and($html)->not->toContain('class="rank text')
        ->and($html)->toContain('aria-label="Cover story"')
        ->and($html)->not->toContain('meta mt-3.5')
        ->and($html)->not->toContain('region-long-read')
        ->and($html)->not->toContain('aria-label="Layout"')
        ->and($html)->toContain('id="grid-title"')
        ->and(substr_count($html, 'class="ad-slot'))->toBe(2);

    // The title kept in view but the count off: the header still holds the title.
    fableSettings($this, homeSettings(['show_story_count' => false]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('<header class="pt-8">')
        ->and($html)->not->toContain('stories on this page');

    // No strip, no cover, no "Also today", no rectangle ad: the three stories "Also today" would have taken go to
    // the grid (8 cards), and the Most read frame stays for its list.
    fableSettings($this, homeSettings([
        'fable_home_top_stories' => false, 'fable_home_cover' => false, 'fable_home_also_today' => false,
        'fable_home_ad_rectangle' => false,
    ]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('Top stories')
        ->and($html)->not->toContain('aria-label="Cover story"')
        ->and($html)->not->toContain('aria-label="Also today"')
        ->and($html)->not->toContain('region-also-today')
        ->and(substr_count($html, 'class="story card"'))->toBe(8)
        ->and($html)->toContain('region-most-read')
        ->and($html)->toContain('class="home-most"')
        ->and(substr_count($html, 'class="ad-slot'))->toBe(2);

    // No grid (and so no in-feed ad), no sections, no Most read: only the rectangle ad is left in its frame.
    fableSettings($this, homeSettings([
        'fable_home_grid' => false, 'fable_home_sections' => false, 'fable_home_most_read' => false,
    ]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('id="grid-title"')
        ->and($html)->not->toContain('x-data="layoutToggle"')
        ->and($html)->not->toContain('class="story card"')
        ->and($html)->not->toContain('region-sections')
        ->and($html)->not->toContain('region-most-read')
        ->and($html)->toContain('home-most home-most-solo')
        ->and(substr_count($html, 'class="ad-slot'))->toBe(2);

    // The in-feed ad off on its own, and the last frame gone when Most read and the rectangle ad are both off.
    fableSettings($this, homeSettings([
        'fable_home_ad_in_feed' => false, 'fable_home_most_read' => false, 'fable_home_ad_rectangle' => false,
    ]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('id="grid-title"')
        ->and($html)->not->toContain('home-most')
        ->and(substr_count($html, 'class="ad-slot'))->toBe(1);
});

it('marks the editor lists when their parts are switched off, and ignores a sub-option under an off parent', function (): void {
    for ($n = 1; $n <= 9; $n++) {
        $this->publish('Home wrapper story '.$n);
    }
    $this->theme(__DIR__.'/..')->activate();

    fableSettings($this, homeSettings([
        'fable_home_also_today_kicker' => false, 'fable_home_also_today_thumb' => false,
        'fable_home_most_read_rank' => false,
    ]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('region-also-today no-kicker no-thumb')
        ->and($html)->toContain('region-most-read no-rank');

    // A parent off: its sub-options are ignored, whatever they say (here they are on, and nothing of them shows).
    fableSettings($this, homeSettings(['fable_home_also_today' => false, 'fable_home_most_read' => false, 'fable_home_top_stories' => false]));
    $html = $this->fetch('/')->assertOk()->getContent();
    expect($html)->not->toContain('region-also-today')
        ->and($html)->not->toContain('region-most-read')
        ->and($html)->not->toContain('class="rank text');
});

it('keeps the page whole with one story and the cover off', function (): void {
    $this->publish('Lone home story');
    $this->theme(__DIR__.'/..')->activate();

    fableSettings($this, homeSettings(['fable_home_cover' => false]));
    $html = $this->fetch('/')->assertOk()->assertSee('data-template="latest"', false)->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('aria-label="Cover story"')
        ->and($html)->toContain('Top stories')
        ->and($html)->toContain('1 story on this page');
});
