<?php

declare(strict_types=1);

/**
 * The settings of the shared chrome: the skin (which skin draws the pages), the ads master switch, the footer's
 * newsletter band (two switches and the buttons that point to it), the search panel and the "/" shortcut. Every default
 * draws what the page drew before the settings existed.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** The chrome settings this file proves and the default each one draws. */
const CHROME_DEFAULTS = [
    'skin' => 'fable',
    'show_ads' => true,
    'show_newsletter' => true,
    'fable_footer_newsletter' => true,
    'fable_search_panel' => true,
    'fable_search_shortcut' => true,
];

/** The article page's markup after the settings are set (an article draws the ads, the rail and the footer). */
function chromeArticle(object $test, array $settings): string
{
    fableSettings($test, $settings);

    return $test->fetch('/a-chrome-story/')->assertOk()->getContent();
}

it('declares every chrome setting in the manifest with the default the template uses', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);
    $declared = [];
    foreach ($manifest['settings'] as $setting) {
        $declared[$setting['key']] = $setting['default'] ?? null;
    }

    foreach (CHROME_DEFAULTS as $key => $default) {
        expect($declared)->toHaveKey($key)
            ->and($declared[$key])->toBe($default, "default of {$key}");
    }
});

it('reads the skin once in every dispatcher, and draws the Fable shell by default', function (): void {
    foreach (glob(__DIR__.'/../views/templates/*.blade.php') as $dispatcher) {
        expect((string) file_get_contents($dispatcher))->toContain("\$themeSettings['skin'] ?? 'fable'");
    }

    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/a-chrome-story/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('data-skin="fable"');
});

it('draws the ad slots, the newsletter band and the search panel by default', function (): void {
    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/a-chrome-story/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('class="ad-slot')
        ->and($html)->toContain('Advertisement')
        ->and($html)->toContain('id="newsletter"')
        ->and($html)->toContain('Never miss a story.')
        ->and(substr_count($html, 'class="border-t border-white/15"'))->toBe(2)
        // The rail's button and the menu's button, both pointing at the band.
        ->and(substr_count($html, 'href="#newsletter"'))->toBe(2)
        ->and($html)->toContain('role="dialog"')
        ->and($html)->toContain('@click.prevent="openSearch"')
        ->and($html)->toContain('<kbd aria-hidden="true">/</kbd>')
        ->and($html)->toContain('@keydown.window="onSlash"');
});

it('draws no ad slot at all while the master switch is off', function (): void {
    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    $html = chromeArticle($this, ['show_ads' => false]);

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('class="ad-slot')
        ->and($html)->not->toContain('ad-label')
        ->and($html)->not->toContain('class="ad-body"');
});

it('removes the newsletter band and the buttons that point to it when either switch is off', function (): void {
    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    foreach ([['show_newsletter' => false], ['fable_footer_newsletter' => false]] as $settings) {
        $html = chromeArticle($this, $settings + ['show_newsletter' => true, 'fable_footer_newsletter' => true]);

        expect(substr_count($html, '<h1'))->toBe(1)
            ->and($html)->not->toContain('id="newsletter"')
            ->and($html)->not->toContain('href="#newsletter"')
            ->and($html)->not->toContain('Never miss a story.')
            ->and($html)->not->toContain('Get our newsletter')
            // The rest of the footer stays, and its first row no longer carries a separator over nothing: only the
            // copyright row keeps its own.
            ->and($html)->toContain('All rights reserved.')
            ->and(substr_count($html, 'class="border-t border-white/15"'))->toBe(1);
    }
});

it('turns the search buttons into plain links when the panel is off, and drops the key with it', function (): void {
    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    $html = chromeArticle($this, ['fable_search_panel' => false]);

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('role="dialog"')
        ->and($html)->not->toContain('openSearch')
        ->and($html)->not->toContain('onSlash')
        ->and($html)->not->toContain('<kbd')
        // The buttons are still there, as links to the search page.
        ->and($html)->toContain('?s=')
        ->and($html)->toContain('class="rail-search"');
});

it('drops the "/" hint and the key but keeps the panel when only the shortcut is off', function (): void {
    $this->publish('A chrome story');
    $this->theme(__DIR__.'/..')->activate();

    $html = chromeArticle($this, ['fable_search_panel' => true, 'fable_search_shortcut' => false]);

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('role="dialog"')
        ->and($html)->toContain('@click.prevent="openSearch"')
        ->and($html)->not->toContain('<kbd')
        ->and($html)->not->toContain('onSlash')
        // Escape still closes whatever is open.
        ->and($html)->toContain('@keydown.escape.window="closeAll"');
});
