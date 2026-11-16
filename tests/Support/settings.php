<?php

declare(strict_types=1);

use App\Domain\Design\Operations\Input\PublishDesignInput;
use App\Domain\Design\Operations\Input\SaveDesignDraftInput;
use App\Domain\Design\Operations\PublishDesign;
use App\Domain\Design\Operations\SaveDesignDraft;
use Teme\Testing\Acting;

/**
 * Test support: sets this theme's settings the way an administrator does under Settings, Design (save, then
 * publish), so a test can prove that a setting changes the page.
 *
 * This is the ONE place the theme's tests reach into Teme's own `App` namespace. The public testing kit
 * (`teme/testing`) can enable and set up a plugin's settings but has no way to set a theme's (FINDINGS item 25), so the
 * two Operations the admin uses are called here. When the kit gains a method for it, replace the body of this
 * function and nothing else changes.
 *
 * Use it from a test that has called `$this->theme(...)->activate()`:
 *
 *     fableSettings($this, ['show_story_count' => false]);
 *
 * Values are the theme's own keys (see `teme-theme.json`); the theme's group is `theme.egberipiribo`. Pass `design`
 * values (the site's accent) as a second argument.
 *
 * @package egberipiribo
 * @since   0.1.0
 *
 * @param  object  $test  the Pest test case (`$this`), for the kit's `person()`
 * @param  array<string, mixed>  $values  theme settings by key
 * @param  array<string, mixed>  $design  Settings, Design values such as ['accent' => '#0b5394']
 */
function fableSettings(object $test, array $values, array $design = []): void
{
    $admin = $test->person('administrator');
    $groups = ['theme.egberipiribo' => $values] + ($design === [] ? [] : ['design' => $design]);

    (new Acting)->as($admin, fn () => app(SaveDesignDraft::class)->execute(new SaveDesignDraftInput($groups, (string) $admin->id)), 'Saving theme settings');
    (new Acting)->as($admin, fn () => app(PublishDesign::class)->execute(new PublishDesignInput((string) $admin->id)), 'Publishing theme settings');
}
