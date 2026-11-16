<?php

declare(strict_types=1);

/**
 * Fable's static page: a published page draws with the single-page template, one h1, the breadcrumb and the body
 * block, and leaves out what a page does not have (the reading bar, the byline and share row, the ads, tags and
 * author card).
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

it('draws a page with the page template', function (): void {
    $this->publish('About', 'page');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/about/')->assertOk()->getContent();

    expect($html)->toContain('data-template="single-page"')
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count($html, '<main'))->toBe(1)
        ->and($html)->toContain('<h1 class="hed hed-page">About</h1>')
        ->and($html)->toContain('aria-label="Breadcrumb"')
        ->and($html)->toContain('class="prose')
        ->and($html)->toContain('Last updated');

    // What a page leaves out: the reading bar, the share row, the ads and the story-only blocks.
    expect($html)->not->toContain('data-progress')
        ->and($html)->not->toContain('copyLink')
        ->and($html)->not->toContain('class="ad-slot')
        ->and($html)->not->toContain('Read next')
        ->and($html)->not->toContain('Filed under')
        ->and($html)->not->toContain('Written by')
        ->and($html)->not->toContain('In this section');

    // Views carry no inline script of their own: those left are core's, by their markers.
    preg_match_all('/<script\b([^>]*)>/i', $html, $scripts);
    $foreign = array_filter($scripts[1], static fn (string $attributes): bool => preg_match('/src=|ld\+json|speculationrules|data-teme=/', $attributes) !== 1);

    expect($foreign)->toBe([]);
});
