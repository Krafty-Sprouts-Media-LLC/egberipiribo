<?php

declare(strict_types=1);

/**
 * The Share settings on an article: the master switch, one switch for each network (and the phone's own share sheet
 * and the copy button), where the rows sit (top, bottom, both, floating), icons or labels, and the line before the
 * bottom row. Every default draws what the page drew before the settings existed (the drawing's buttons: Facebook, X,
 * email and copy link, at the byline and after the tags).
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** The share settings this file proves and the default each one draws. */
const SHARE_DEFAULTS = [
    'show_share' => true,
    'fable_share_facebook' => true,
    'fable_share_x' => true,
    'fable_share_email' => true,
    'fable_share_copy' => true,
    'fable_share_pinterest' => false,
    'fable_share_linkedin' => false,
    'fable_share_whatsapp' => false,
    'fable_share_telegram' => false,
    'fable_share_reddit' => false,
    'fable_share_native' => false,
    'fable_share_placement' => 'both',
    'fable_share_style' => 'icons',
    'fable_share_label' => 'Share this story',
];

/** The article page's markup after the settings are set. */
function shareArticle(object $test, array $settings, string $path = '/a-shared-story/'): string
{
    fableSettings($test, $settings);

    return $test->fetch($path)->assertOk()->getContent();
}

it('declares every share setting in the manifest with the default the template uses', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);
    $declared = [];
    foreach ($manifest['settings'] as $setting) {
        $declared[$setting['key']] = $setting['default'] ?? null;
    }

    foreach (SHARE_DEFAULTS as $key => $default) {
        expect($declared)->toHaveKey($key)
            ->and($declared[$key])->toBe($default, "default of {$key}");
    }
});

it('draws the drawing\'s buttons at the byline and after the tags by default', function (): void {
    $this->publish('A shared story');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/a-shared-story/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        // Two rows: the byline and after the tags; each has Facebook, X, email and the copy button.
        ->and(substr_count($html, 'class="share-row'))->toBe(2)
        ->and(substr_count($html, 'sharer.php?u='))->toBe(2)
        ->and(substr_count($html, 'x.com/intent/post'))->toBe(2)
        ->and(substr_count($html, 'mailto:?subject='))->toBe(2)
        ->and(substr_count($html, 'x-data="copyLink"'))->toBe(2)
        ->and($html)->toContain('Share this story')
        // What the drawing does not have stays out.
        ->and($html)->not->toContain('pinterest.com')
        ->and($html)->not->toContain('linkedin.com')
        ->and($html)->not->toContain('api.whatsapp.com')
        ->and($html)->not->toContain('t.me/share')
        ->and($html)->not->toContain('reddit.com/submit')
        ->and($html)->not->toContain('x-data="shareNative"')
        ->and($html)->not->toContain('share-float"')
        // Round icon buttons only: no network name printed beside the icon.
        ->and($html)->not->toContain('<span>Facebook</span>');
});

it('draws each network that is on, and none that is off, and the phone\'s share sheet', function (): void {
    $this->publish('A shared story');
    $this->theme(__DIR__.'/..')->activate();

    $html = shareArticle($this, [
        'fable_share_facebook' => false,
        'fable_share_x' => false,
        'fable_share_email' => false,
        'fable_share_copy' => false,
        'fable_share_pinterest' => true,
        'fable_share_linkedin' => true,
        'fable_share_whatsapp' => true,
        'fable_share_telegram' => true,
        'fable_share_reddit' => true,
        'fable_share_native' => true,
    ]);

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('https://www.pinterest.com/pin/create/button/?url=')
        ->and($html)->toContain('https://www.linkedin.com/sharing/share-offsite/?url=')
        ->and($html)->toContain('https://api.whatsapp.com/send?text=')
        ->and($html)->toContain('https://t.me/share/url?url=')
        ->and($html)->toContain('https://www.reddit.com/submit?url=')
        ->and($html)->toContain('aria-label="Share on LinkedIn"')
        // The phone's share sheet: the component's name, its method and its property, all hidden until the script runs.
        ->and($html)->toContain('x-data="shareNative"')
        ->and($html)->toContain('x-show="supported"')
        ->and($html)->toContain('@click="share"')
        // Switched off: Facebook, X, email and the copy button.
        ->and($html)->not->toContain('sharer.php')
        ->and($html)->not->toContain('x.com/intent/post')
        ->and($html)->not->toContain('mailto:?subject=')
        ->and($html)->not->toContain('x-data="copyLink"');
});

it('puts the rows where the placement says', function (): void {
    $this->publish('A shared story');
    $this->theme(__DIR__.'/..')->activate();

    $top = shareArticle($this, ['fable_share_placement' => 'top']);
    expect(substr_count($top, 'class="share-row'))->toBe(1)
        ->and($top)->not->toContain('Share this story');

    $bottom = shareArticle($this, ['fable_share_placement' => 'bottom', 'fable_share_label' => 'Pass it on']);
    expect(substr_count($bottom, 'class="share-row'))->toBe(1)
        ->and($bottom)->toContain('Pass it on')
        ->and($bottom)->not->toContain('Share this story');

    $both = shareArticle($this, ['fable_share_placement' => 'both', 'fable_share_label' => '']);
    expect(substr_count($both, 'class="share-row'))->toBe(2)
        ->and($both)->not->toContain('Share this story');

    // Floating: the column beside the article, round icons only, and the row after the tags that a narrow screen keeps.
    $floating = shareArticle($this, ['fable_share_placement' => 'floating', 'fable_share_style' => 'labels']);
    expect(substr_count($floating, 'class="share-float"'))->toBe(1)
        ->and(substr_count($floating, 'share-float-rail'))->toBe(1)
        ->and(substr_count($floating, 'share-float-fallback'))->toBe(1)
        ->and(substr_count($floating, 'class="share-row'))->toBe(1)
        ->and(substr_count($floating, 'sharer.php?u='))->toBe(2);
});

it('draws the network names beside the icons in the labels style', function (): void {
    $this->publish('A shared story');
    $this->theme(__DIR__.'/..')->activate();

    $html = shareArticle($this, ['fable_share_style' => 'labels']);

    expect($html)->toContain('<span>Facebook</span>')
        ->and($html)->toContain('<span>X</span>')
        ->and($html)->toContain('<span>Email</span>');
});

it('draws no share row at all when the master switch is off', function (): void {
    $this->publish('A shared story');
    $this->theme(__DIR__.'/..')->activate();

    $html = shareArticle($this, ['show_share' => false, 'fable_share_linkedin' => true]);

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('share-row')
        ->and($html)->not->toContain('sharer.php')
        ->and($html)->not->toContain('linkedin.com')
        ->and($html)->not->toContain('x-data="copyLink"');
});
