<?php

declare(strict_types=1);

/**
 * Writes each Fable page's rendered HTML to docs/snapshots, for the browser audit (scripts/serve-snapshots.mjs and
 * scripts/audit.mjs) when the shared dev site cannot be switched to this theme. It runs only when FABLE_SNAPSHOT=1,
 * so the ordinary suite skips it. The kit makes bare posts only, so the snapshots have no pictures, menus or
 * categories: they show layout, type and accessibility, not the filled-in design.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
require_once __DIR__.'/Support/settings.php';

uses(Teme\Testing\TemeTestCase::class);

it('writes page snapshots for the browser audit', function (): void {
    if (getenv('FABLE_SNAPSHOT') !== '1') {
        $this->markTestSkipped('Set FABLE_SNAPSHOT=1 to write the snapshots.');
    }

    $titles = [
        'What Is Your Cat Thinking? The Inner Life Cats Have Always Kept From Us',
        'One Weekend, One Room: The Repainting Rule Decorators Swear By',
        'How to Grow a Cutting Garden That Flowers in Every Season',
        'The Pantry Principle: How to Cook Dinner From What You Already Have',
        'Natural Flea Prevention for Dogs: The Methods Vets Say Actually Work',
        'How to Hang a Gallery Wall That Does Not Look Like an Accident',
        'The Hidden Villages of the Dolomites: A Six-Day Walking Route Without a Car',
        'How to Start a Ferment Shelf: Five Beginner Jars for a Living Pantry',
        'Smart Thermostats, Two Years On: What Saved Us Money and What Did Not',
        '12 Garden Weeds Worth Keeping, and the Ones to Pull on Sight',
        'Why You Should Eat Lunch Outside, Even in the Middle of Winter',
        'Slow Trains Through the Highlands: Four Scenic Routes Worth the Fare',
        'The Nine Apps That Still Earn a Place on Our Home Screens',
        'A 20-Minute Bedtime Ritual That Actually Sticks',
    ];
    foreach ($titles as $title) {
        $this->publish($title);
    }
    $this->publish('About us', 'page');
    $this->theme(__DIR__.'/..')->activate();

    $pages = [
        'home' => '/',
        'article' => '/'.Illuminate\Support\Str::slug($titles[0]).'/',
        'page' => '/about-us/',
        'archive' => '/'.date('Y').'/',
        'search' => '/?s=story',
        'notfound' => '/no-such-page-here/',
    ];
    $dir = __DIR__.'/../docs/snapshots';
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    foreach ($pages as $name => $path) {
        $html = $this->fetch($path)->getContent();
        file_put_contents($dir.'/'.$name.'.html', $html);
        expect($html)->toContain('data-template=');
    }

    // Variants with settings changed, for the audit and the screenshots of what a setting changes.
    $variants = [
        'v-pattern' => [
            ['pattern' => 'pets', 'pattern_strength' => 'medium', 'fable_pattern_sections' => true, 'fable_pattern_404' => true],
            ['home' => '/', 'archive' => '/'.date('Y').'/', 'notfound' => '/no-such-page-here/'],
        ],
        'v-share' => [
            ['fable_share_placement' => 'floating', 'fable_share_style' => 'labels', 'fable_share_linkedin' => true, 'fable_share_whatsapp' => true, 'fable_share_native' => true],
            ['article' => $pages['article']],
        ],
        'v-share-both' => [
            ['fable_share_placement' => 'both', 'fable_share_style' => 'labels', 'fable_share_reddit' => true, 'fable_share_telegram' => true],
            ['article' => $pages['article']],
        ],
        'v-minimal' => [
            ['fable_home_top_stories' => false, 'fable_home_cover' => false, 'fable_home_also_today' => false, 'show_layout_toggle' => false, 'show_story_count' => false, 'fable_home_title_visible' => false, 'show_newsletter' => false],
            ['home' => '/'],
        ],
        'v-article-lean' => [
            ['show_reading_progress' => false, 'fable_article_breadcrumb' => false, 'fable_byline_avatar' => false, 'show_share' => false, 'show_author_card' => false, 'show_comments' => false, 'show_ads' => false, 'fable_article_inline_related' => true],
            ['article' => $pages['article']],
        ],
        'v-listing-lean' => [
            ['archive_lead_story' => false, 'archive_layout_toggle' => false, 'fable_archive_kind_label' => false, 'fable_search_form' => false, 'fable_search_count' => false, 'fable_404_big_number' => false],
            ['archive' => '/'.date('Y').'/', 'search' => $pages['search'], 'notfound' => '/no-such-page-here/'],
        ],
    ];
    foreach ($variants as $prefix => [$settings, $targets]) {
        fableSettings($this, $settings);
        foreach ($targets as $name => $path) {
            $html = $this->fetch($path)->getContent();
            file_put_contents($dir.'/'.$prefix.'-'.$name.'.html', $html);
            expect($html)->toContain('data-template=')->and(substr_count($html, '<h1'))->toBe(1);
        }
        // Back to the defaults before the next variant.
        $defaults = array_column(json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true)['settings'], 'default', 'key');
        fableSettings($this, array_intersect_key($defaults, $settings));
    }
});
