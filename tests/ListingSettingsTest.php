<?php

declare(strict_types=1);

/**
 * The settings of the Fable listing pages: the archive (reached here through a date archive), the author page, the
 * search page, the not-found page and the error page. Every key is declared in the manifest with the default its
 * template reads; with the defaults each page is as ListingsTest.php describes; and turning a switch off removes its
 * block, and any wrapper it leaves empty, from the page.
 *
 * Not testable through the kit (it makes bare posts and no terms, menu, picture, description or body): the archive's
 * description, header picture, topic chips and All chip; the lead story's standfirst; the author's bio; the search
 * snippet and the section chips of the search and not-found pages; and the error page, which no address reaches (its
 * source is checked instead). The search panel and its shortcut belong to the shared partials, not to this file.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** Every key this file's templates read, with the file that reads it. */
function listingSettingsKeys(): array
{
    return [
        'fable_archive_breadcrumb' => 'archive',
        'fable_archive_kind_label' => 'archive',
        'fable_archive_description' => 'archive',
        'archive_header_image' => 'archive',
        'archive_topic_chips' => 'archive',
        'fable_archive_chip_all' => 'archive',
        'archive_ads' => 'archive',
        'fable_archive_ad_leaderboard' => 'archive',
        'fable_archive_ad_in_feed' => 'archive',
        'archive_lead_story' => 'archive',
        'fable_archive_lead_standfirst' => 'archive',
        'fable_archive_lead_byline' => 'archive',
        'archive_layout_toggle' => 'archive',
        'fable_author_breadcrumb' => 'author',
        'author_show_portrait' => 'author',
        'fable_author_kicker' => 'author',
        'fable_author_bio' => 'author',
        'fable_author_stories' => 'author',
        'fable_search_form' => 'search',
        'fable_search_count' => 'search',
        'fable_search_snippets' => 'search',
        'search_show_sections' => 'search',
        'fable_search_ad_leaderboard' => 'search',
        'fable_search_ad_in_feed' => 'search',
        'fable_404_big_number' => 'notfound',
        'fable_404_search_form' => 'notfound',
        'fable_404_home_button' => 'notfound',
        'notfound_show_sections' => 'notfound',
        'fable_error_big_number' => 'error',
        'fable_error_home_button' => 'error',
        'fable_error_search_form' => 'error',
        'fable_error_sections' => 'error',
    ];
}

/** The manifest's settings by key. */
function listingSettingsManifest(): array
{
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);

    return collect($manifest['settings'])->keyBy('key')->all();
}

/** Publishes the theme's settings: every key of this file at its manifest default, then the overrides. */
function listingSet(object $test, array $overrides): void
{
    $values = collect(listingSettingsManifest())->only(array_keys(listingSettingsKeys()))->map(fn (array $row) => $row['default'])->all();
    fableSettings($test, array_merge($values, $overrides));
}

/** The page's one <main>, from its opening tag to its closing tag. */
function listingSettingsMain(string $html): string
{
    $start = (int) strpos($html, '<main');

    return substr($html, $start, (int) strpos($html, '</main>') - $start);
}

/** How many advertisement slots the page's main draws. */
function listingSettingsAds(string $html): int
{
    return substr_count(listingSettingsMain($html), 'class="ad-slot');
}

it('declares every key in the manifest with the default its template reads', function (): void {
    $manifest = listingSettingsManifest();

    foreach (listingSettingsKeys() as $key => $template) {
        expect($manifest)->toHaveKey($key);
        expect($manifest[$key]['type'])->toBe('boolean');

        $source = (string) file_get_contents(__DIR__.'/../views/skins/fable/'.$template.'.blade.php');
        preg_match_all('/\$themeSettings\[\''.preg_quote($key, '/').'\'\]\s*\?\?\s*(true|false)/', $source, $reads);

        // Read once, at the top of its template, with the manifest's default.
        expect($reads[1])->toHaveCount(1, $key.' is read once in '.$template);
        expect($reads[1][0] === 'true')->toBe($manifest[$key]['default'], $key.' default');
    }

    // The search ads are off by default: the drawing has none on the search page.
    expect($manifest['fable_search_ad_leaderboard']['default'])->toBeFalse()
        ->and($manifest['fable_search_ad_in_feed']['default'])->toBeFalse();
});

it('has a label and a description for every key, each label prefixed and short', function (): void {
    $labels = json_decode((string) file_get_contents(__DIR__.'/../docs/settings-labels/listing.json'), true, 512, JSON_THROW_ON_ERROR);

    expect(array_keys($labels))->toEqualCanonicalizing(array_keys(listingSettingsKeys()));
    foreach ($labels as $key => $row) {
        expect(mb_strlen($row['label']))->toBeLessThanOrEqual(60)
            ->and($row['label'])->toContain(': ')
            ->and($row['description'])->not->toBe('');
    }
});

it('switches the archive blocks on and off', function (): void {
    $year = (int) date('Y');
    $this->person('administrator');
    foreach (['First wolf story', 'Second wolf story', 'Third wolf story'] as $title) {
        $this->publish($title);
    }
    $this->theme(__DIR__.'/..')->activate();

    // The defaults: everything the drawing shows.
    $html = $this->fetch('/'.$year.'/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('aria-label="Breadcrumb"')
        ->and($html)->toContain('<p class="kicker">Archive</p>')
        ->and($html)->toContain('<h1 class="hed hed-page mt-2">'.$year.'</h1>')
        ->and(listingSettingsAds($html))->toBe(2)
        ->and($html)->toContain('aria-label="Latest in '.$year.'"')
        ->and($html)->toContain('<p class="meta mt-3.5">')
        ->and($html)->toContain('Grid layout')
        ->and(substr_count($html, ':class="rowClass"'))->toBe(2);

    // Header blocks off: no breadcrumb, no kind label, and no orphan margin on the title.
    listingSet($this, ['fable_archive_breadcrumb' => false, 'fable_archive_kind_label' => false]);
    $html = $this->fetch('/'.$year.'/')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('aria-label="Breadcrumb"')
        ->and($html)->not->toContain('<p class="kicker">Archive</p>')
        ->and($html)->toContain('<h1 class="hed hed-page">'.$year.'</h1>')
        ->and($html)->toContain('<div class="grid gap-x-10')
        ->and($html)->toContain('aria-label="Latest in '.$year.'"');

    // One ad spot off at a time, then both through the parent switch.
    listingSet($this, ['fable_archive_ad_leaderboard' => false]);
    expect(listingSettingsAds($this->fetch('/'.$year.'/')->assertOk()->getContent()))->toBe(1);
    listingSet($this, ['fable_archive_ad_in_feed' => false]);
    expect(listingSettingsAds($this->fetch('/'.$year.'/')->assertOk()->getContent()))->toBe(1);
    listingSet($this, ['archive_ads' => false]);
    expect(listingSettingsAds($this->fetch('/'.$year.'/')->assertOk()->getContent()))->toBe(0);
    // The parent off wins over children left on; the children off do the same with the parent on.
    listingSet($this, ['archive_ads' => true, 'fable_archive_ad_leaderboard' => false, 'fable_archive_ad_in_feed' => false]);
    expect(listingSettingsAds($this->fetch('/'.$year.'/')->assertOk()->getContent()))->toBe(0);

    // The lead story's byline off leaves the headline; the layout buttons off leave the grid.
    listingSet($this, ['fable_archive_lead_byline' => false, 'archive_layout_toggle' => false]);
    $html = $this->fetch('/'.$year.'/')->assertOk()->getContent();
    expect($html)->toContain('<h2 class="hed hed-xl mt-2.5"><a href="/third-wolf-story/">Third wolf story</a></h2>')
        ->and($html)->not->toContain('<p class="meta mt-3.5">')
        ->and($html)->not->toContain('Grid layout')
        ->and($html)->not->toContain('List layout')
        ->and($html)->toContain('x-data="layoutToggle"')
        ->and($html)->toContain('All stories in '.$year);

    // The lead story off: no large story, and all three stories are in the grid.
    listingSet($this, ['archive_lead_story' => false]);
    $html = $this->fetch('/'.$year.'/')->assertOk()->getContent();
    expect($html)->not->toContain('aria-label="Latest in '.$year.'"')
        ->and($html)->not->toContain('hed-xl')
        ->and(substr_count($html, ':class="rowClass"'))->toBe(3)
        ->and($html)->toContain('Third wolf story')
        ->and(substr_count($html, '<h1'))->toBe(1);

    // With the lead off its children are ignored, even when left on.
    listingSet($this, ['archive_lead_story' => false, 'fable_archive_lead_standfirst' => true, 'fable_archive_lead_byline' => true]);
    expect($this->fetch('/'.$year.'/')->assertOk()->getContent())->not->toContain('hed-xl');
});

it('switches the author page blocks on and off', function (): void {
    $admin = $this->person('administrator');
    $this->publish('A story by the editor');
    $this->theme(__DIR__.'/..')->activate();

    preg_match('#href="(/author/[^"]+)" rel="author"#', $this->fetch('/')->getContent(), $found);
    expect($found)->not->toBeEmpty();
    $url = $found[1];
    $emptyUrl = '/author/'.Illuminate\Support\Str::slug($admin->name).'/';

    $html = $this->fetch($url)->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->toContain('aria-label="Breadcrumb"')
        ->and($html)->toContain('>P2</div>')
        ->and($html)->toContain('<p class="kicker">Writer</p>')
        ->and($html)->toContain('<h1 class="hed hed-page mt-2">Person 2</h1>')
        ->and($html)->toContain('Latest from Person 2')
        ->and($html)->toContain('listing-rows');

    listingSet($this, ['fable_author_breadcrumb' => false, 'author_show_portrait' => false, 'fable_author_kicker' => false]);
    $html = $this->fetch($url)->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('aria-label="Breadcrumb"')
        ->and($html)->not->toContain('>P2</div>')
        ->and($html)->not->toContain('md:grid-cols-[10rem_minmax(0,1fr)]')
        ->and($html)->not->toContain('<p class="kicker">Writer</p>')
        ->and($html)->toContain('<h1 class="hed hed-page">Person 2</h1>')
        ->and($html)->toContain('Latest from Person 2');

    // The stories off: no rows, no heading, and the empty note goes with them.
    listingSet($this, ['fable_author_stories' => false]);
    $html = $this->fetch($url)->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('Latest from Person 2')
        ->and($html)->not->toContain('listing-rows')
        ->and($html)->toContain('<h1 class="hed hed-page mt-2">Person 2</h1>');
    $empty = $this->fetch($emptyUrl)->assertOk()->getContent();
    expect($empty)->not->toContain('No stories yet')
        ->and(substr_count($empty, '<h1'))->toBe(1);

    // Back on, the empty note returns.
    listingSet($this, []);
    expect($this->fetch($emptyUrl)->assertOk()->getContent())->toContain('No stories yet');
});

it('switches the search page blocks on and off, and keeps the search ads off by default', function (): void {
    $this->publish('Wolves of the high Arctic');
    $this->publish('Something else entirely');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count(listingSettingsMain($html), 'role="search"'))->toBe(1)
        ->and($html)->toContain('1 result')
        ->and($html)->toContain('listing-rows')
        ->and(listingSettingsAds($html))->toBe(0);

    listingSet($this, ['fable_search_form' => false, 'fable_search_count' => false]);
    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and(listingSettingsMain($html))->not->toContain('role="search"')
        ->and($html)->not->toContain('1 result')
        ->and($html)->toContain('Wolves of the high Arctic')
        ->and($html)->toContain('listing-rows');

    // The snippet switch reads without breaking the page (the kit's posts have no body to match).
    listingSet($this, ['fable_search_snippets' => false, 'search_show_sections' => false]);
    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    expect($html)->toContain('Wolves of the high Arctic')
        ->and($this->fetch('/?s=zzzqqq')->assertOk()->getContent())->toContain('No stories match that search');

    // The ads appear only when switched on, one spot at a time.
    listingSet($this, ['fable_search_ad_leaderboard' => true]);
    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    expect(listingSettingsAds($html))->toBe(1)
        ->and(strpos(listingSettingsMain($html), 'class="ad-slot'))->toBeLessThan(strpos(listingSettingsMain($html), 'listing-rows'));

    listingSet($this, ['fable_search_ad_in_feed' => true]);
    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    expect(listingSettingsAds($html))->toBe(1)
        ->and(strpos(listingSettingsMain($html), 'class="ad-slot'))->toBeGreaterThan(strpos(listingSettingsMain($html), 'listing-rows'));

    listingSet($this, ['fable_search_ad_leaderboard' => true, 'fable_search_ad_in_feed' => true]);
    expect(listingSettingsAds($this->fetch('/?s=wolves')->assertOk()->getContent()))->toBe(2);

    // No results: the leaderboard still sits under the header, the in-feed ad has no list to sit in.
    expect(listingSettingsAds($this->fetch('/?s=zzzqqq')->assertOk()->getContent()))->toBe(1);
});

it('switches the not-found page blocks on and off', function (): void {
    $this->publish('A first story');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/no-such-page-here/')->assertNotFound()->getContent();
    $main = listingSettingsMain($html);
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($main)->toContain('aria-hidden="true">404</p>')
        ->and($main)->toContain('role="search"')
        ->and($main)->toContain('Go to the home page')
        ->and($main)->toContain('lg:grid-cols-[minmax(0,1fr)_auto]');

    listingSet($this, ['fable_404_big_number' => false, 'fable_404_search_form' => false]);
    $html = $this->fetch('/no-such-page-here/')->assertNotFound()->getContent();
    $main = listingSettingsMain($html);
    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($main)->not->toContain('aria-hidden="true">404</p>')
        ->and($main)->not->toContain('lg:grid-cols-[minmax(0,1fr)_auto]')
        ->and($main)->not->toContain('role="search"')
        ->and($main)->toContain('Go to the home page');

    // The home button off with no chips (the kit sets no menu): the whole row goes, not an empty wrapper.
    listingSet($this, ['fable_404_home_button' => false]);
    $main = listingSettingsMain($this->fetch('/no-such-page-here/')->assertNotFound()->getContent());
    expect($main)->not->toContain('Go to the home page')
        ->and($main)->toContain('aria-hidden="true">404</p>')
        ->and($main)->toContain('role="search"');
});

it('reads the error page settings and compiles it, since no address reaches that template', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    $source = (string) file_get_contents(__DIR__.'/../views/skins/fable/error.blade.php');

    expect(fn () => token_get_all(app('blade.compiler')->compileString($source), TOKEN_PARSE))->not->toThrow(ParseError::class);
    expect(substr_count($source, '<h1'))->toBe(1)
        ->and($source)->toContain("\$themeSettings['fable_error_big_number'] ?? true")
        ->and($source)->toContain("\$themeSettings['fable_error_home_button'] ?? true")
        ->and($source)->toContain("\$themeSettings['fable_error_search_form'] ?? false")
        ->and($source)->toContain("\$themeSettings['fable_error_sections'] ?? false")
        ->and($source)->toContain('@if ($showBigNumber)')
        ->and($source)->toContain('@if ($showHomeButton)')
        ->and($source)->toContain('@if ($showSearchForm)')
        ->and($source)->toContain('@if ($showSections)')
        ->and($source)->not->toContain('<script');
});
