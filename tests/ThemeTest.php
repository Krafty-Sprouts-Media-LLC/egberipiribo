<?php

declare(strict_types=1);

/**
 * The theme as a whole: its manifest passes Teme's own check, and activating it
 * makes it draw the public site.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

it('passes teme:theme:check with no problem', function (): void {
    expect($this->theme(__DIR__.'/..')->check())->toBe([]);
});

it('draws the home page with the Fable shell', function (): void {
    $this->publish('A first story');
    $this->theme(__DIR__.'/..')->activate();

    $this->fetch('/')
        ->assertOk()
        ->assertSee('class="wordmark', false);
});
