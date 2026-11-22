<?php

declare(strict_types=1);

/**
 * Teme 1.0's `x-teme::between-items`: the lists the skin draws (the front page's grid, an archive, the search
 * results) call it after each item through `partials/between`, so a plugin can place content between items. With no
 * plugin the slot is an empty element the skin's CSS hides, and the page is otherwise unchanged.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

it('gives each list a between-items slot after every item', function (): void {
    foreach (range(1, 10) as $n) {
        $this->publish("Story number {$n}");
    }
    $this->theme(__DIR__.'/..')->activate();

    foreach (['home' => '/', 'archive' => '/'.date('Y').'/', 'search' => '/?s=story'] as $name => $path) {
        $html = $this->fetch($path)->getContent();
        // One slot per item drawn (the grid's, the archive's, the results'), each empty with no plugin placing anything.
        expect(substr_count($html, 'class="between-slot"'))->toBeGreaterThanOrEqual(1, "{$name} has no between-items slot");
        expect($html)->not->toMatch('/class="between-slot">\s*<[a-z]/i');
    }
});

it('declares a Teme range that covers 1.x', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true);

    expect($manifest['requires']['teme'])->toBe('>=0.8.0-alpha.1 <2.0');
});
