<?php

declare(strict_types=1);

/**
 * The Fable article, page and comment settings (every "Article", "Article byline", "Article hero", "Article ads",
 * "Article read next", "Article author card", "Article related", "Comments" and "Page" setting, plus the shared
 * show_updated_date, updated_after_days, show_comments, show_reading_time, show_tags and show_author_card): each is
 * declared in the manifest with the default the templates use and has a label; the default pages draw every block;
 * each switch removes its block, wrapper and separator from the page, which keeps its one h1.
 *
 * The kit makes bare posts only (no picture, section, excerpt, field, body headings, related story or comment), so
 * what a bare post cannot draw is proved by rendering the partial itself with a hand-made value, the way
 * ArticleTest renders the body partial: the byline, the author card, "Read next", the related row's link, the
 * inline related card, the comment card and a removed comment.
 *
 * Not reachable through the kit or a partial render (they live in the article and page templates and need a
 * picture, an excerpt, a section, a field or a page with children): fable_article_kicker,
 * fable_article_standfirst, fable_article_hero, fable_article_hero_caption, fable_article_fields_before,
 * fable_article_fields_after, show_contents (its list needs headings; the partial takes it as a parameter),
 * fable_page_standfirst, fable_page_hero, fable_page_fields, show_children, show_related (it needs related
 * stories) and show_read_next (the same). For these the template compiles and the default page renders.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

require_once __DIR__.'/Support/settings.php';

/** Every key this file covers, with the default the templates use. */
function articleSettingDefaults(): array
{
    return [
        'show_reading_progress' => true, 'fable_article_breadcrumb' => true, 'fable_article_kicker' => true,
        'fable_article_standfirst' => true, 'fable_byline_avatar' => true, 'fable_byline_authors' => true,
        'fable_byline_date' => true, 'fable_article_show_updated' => true, 'fable_article_hero' => true,
        'fable_article_hero_caption' => true, 'fable_article_fields_before' => true, 'show_contents' => true,
        'article_lede' => true, 'fable_article_inline_related' => false, 'fable_article_fields_after' => true,
        'fable_article_after_content' => true, 'fable_article_ad_leaderboard' => true,
        'fable_article_ad_in_article' => true, 'fable_article_ad_second' => true,
        'fable_article_ad_half_page' => true, 'show_read_next' => true, 'fable_read_next_reason' => true,
        'fable_author_card_avatar' => true, 'fable_author_card_bio' => true, 'fable_author_card_link' => true,
        'show_related' => true, 'fable_related_more_link' => true, 'fable_comments_avatar' => true,
        'fable_comments_author_badge' => true, 'fable_page_breadcrumb' => true, 'fable_page_standfirst' => true,
        'show_updated' => true, 'fable_page_hero' => true, 'fable_page_fields' => true, 'page_lede' => true,
        'show_children' => true, 'show_page_comments' => true, 'show_updated_date' => true,
        'updated_after_days' => '1', 'show_comments' => true, 'show_reading_time' => true, 'show_tags' => true,
        'show_author_card' => true,
    ];
}

/** Every switch on, with the given ones changed. */
function articleSettings(array $changes = []): array
{
    return $changes + articleSettingDefaults();
}

/** A person as the byline and the author card read one. */
function articleAuthor(string $name = 'Ada Writer', string $bio = 'Writes about quays.', string $url = '/author/ada/'): object
{
    return (object) ['name' => $name, 'bio' => $bio, 'url' => $url];
}

/** An article's byline data: published ten days before it was updated. */
function articleBylinePage(?DateTimeImmutable $updated, array $authors): object
{
    $published = new DateTimeImmutable('2026-03-01 09:00:00');

    return (object) [
        'authors' => $authors, 'published' => $published, 'publishedText' => 'March 1, 2026',
        'updated' => $updated, 'updatedText' => $updated?->format('F j, Y'), 'readingMinutes' => 5,
    ];
}

/** One comment as the comment card reads it. */
function articleComment(bool $byAuthor = false): object
{
    return (object) [
        'id' => 'c1', 'legacyId' => null, 'pending' => false, 'hue' => 3, 'initials' => 'JD', 'name' => 'Jo Doe',
        'url' => null, 'byAuthor' => $byAuthor, 'permalink' => '#comment-c1', 'iso' => '2026-03-02T10:00:00+00:00',
        'dateText' => 'March 2, 2026', 'isReply' => false, 'parentName' => null, 'bodyHtml' => '<p>Well said.</p>',
        'replyUrl' => null, 'removed' => false, 'kind' => 'comment', 'replies' => [],
    ];
}

it('declares every article, page and comment setting in the manifest with the default the templates use', function (): void {
    $manifest = json_decode((string) file_get_contents(__DIR__.'/../teme-theme.json'), true, 512, JSON_THROW_ON_ERROR);
    $declared = [];
    foreach ($manifest['settings'] as $setting) {
        $declared[$setting['key']] = $setting;
    }

    foreach (articleSettingDefaults() as $key => $default) {
        expect($declared)->toHaveKey($key)
            ->and($declared[$key]['default'])->toBe($default);
    }
});

it('labels every setting with its area, a colon and at most 60 characters', function (): void {
    $labels = json_decode((string) file_get_contents(__DIR__.'/../docs/settings-labels/article.json'), true, 512, JSON_THROW_ON_ERROR);

    expect(array_keys($labels))->toEqualCanonicalizing(array_keys(articleSettingDefaults()));

    foreach ($labels as $key => $entry) {
        expect(mb_strlen($entry['label']))->toBeLessThanOrEqual(60, $key)
            ->and($entry['label'])->toContain(': ')
            ->and($entry['description'])->not->toBe('');
    }

    expect(array_map('strval', array_keys($labels['updated_after_days']['optionLabels'])))->toBe(['1', '7', '14', '30']);
});

it('draws every article block by default and takes each switched-off block out, wrapper and all', function (): void {
    $this->publish('Switchable story');
    $this->theme(__DIR__.'/..')->activate();

    $default = $this->fetch('/switchable-story/')->assertOk()->getContent();
    $slots = substr_count($default, 'class="ad-slot');

    expect(substr_count($default, '<h1'))->toBe(1)
        ->and($default)->toContain('data-progress')
        ->and($default)->toContain('aria-label="Breadcrumb"')
        ->and($default)->toContain('w-[3.25rem] h-[3.25rem]')
        ->and($default)->toContain('rel="author"')
        ->and($default)->toContain('>Published ')
        ->and($default)->toContain('min read')
        ->and($default)->toContain('prose prose-lede')
        ->and($default)->toContain('class="article-after mt-10"')
        ->and($default)->toContain('class="ad-side"')
        ->and($default)->toContain('Written by')
        ->and($default)->toContain('w-24 h-24')
        ->and($default)->toContain('More from')
        ->and($default)->toContain('data-teme-thread')
        ->and($slots)->toBeGreaterThanOrEqual(4);

    // Bar, breadcrumb, initials, date, reading time, lede, leaderboard, after-content and the half-page column off.
    fableSettings($this, articleSettings([
        'show_reading_progress' => false, 'fable_article_breadcrumb' => false, 'fable_byline_avatar' => false,
        'fable_byline_date' => false, 'show_reading_time' => false, 'article_lede' => false,
        'fable_article_ad_leaderboard' => false, 'fable_article_after_content' => false,
        'fable_article_ad_half_page' => false,
    ]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('data-progress')
        ->and($html)->not->toContain('aria-label="Breadcrumb"')
        ->and($html)->not->toContain('<header class="mt-5">')
        ->and($html)->not->toContain('w-[3.25rem] h-[3.25rem]')
        ->and($html)->toContain('rel="author"')
        ->and($html)->not->toContain('>Published ')
        ->and($html)->not->toContain('min read')
        ->and($html)->toContain('class="prose"')
        ->and($html)->not->toContain('prose-lede')
        ->and($html)->not->toContain('article-after')
        ->and($html)->not->toContain('class="ad-side"')
        ->and(substr_count($html, 'class="ad-slot'))->toBe($slots - 2);

    // The in-article and second ads off, the names off, the share row off: the byline row has nothing left, so it goes.
    fableSettings($this, articleSettings([
        'fable_article_ad_in_article' => false, 'fable_article_ad_second' => false, 'fable_byline_authors' => false,
        'fable_byline_avatar' => false, 'fable_byline_date' => false, 'show_reading_time' => false,
        'show_share' => false,
    ]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('rel="author"')
        ->and($html)->not->toContain('border-y border-rule py-4')
        ->and(substr_count($html, 'class="ad-slot'))->toBe($slots - 2);

    // With the share row on, the byline keeps its row for the share buttons alone.
    fableSettings($this, articleSettings([
        'fable_byline_authors' => false, 'fable_byline_avatar' => false, 'fable_byline_date' => false,
        'show_reading_time' => false, 'show_share' => true,
    ]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();

    expect($html)->toContain('border-y border-rule py-4')
        ->and($html)->toContain('x-data="copyLink"')
        ->and($html)->not->toContain('rel="author"');

    // The author card: no initials; then no link (a bare editor has no bio, so no card is left); then the card switch.
    fableSettings($this, articleSettings(['fable_author_card_avatar' => false]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();
    expect($html)->toContain('Written by')->and($html)->not->toContain('w-24 h-24')->and($html)->toContain('More from');

    fableSettings($this, articleSettings(['fable_author_card_link' => false, 'fable_author_card_bio' => false]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();
    expect($html)->not->toContain('Written by')->and($html)->not->toContain('More from');

    fableSettings($this, articleSettings(['show_author_card' => false, 'show_comments' => false]));
    $html = $this->fetch('/switchable-story/')->assertOk()->getContent();
    expect($html)->not->toContain('Written by')
        ->and($html)->not->toContain('data-teme-thread')
        ->and($html)->not->toContain('id="comments"');
});

it('draws a page with its blocks and takes each switched-off block out', function (): void {
    $this->publish('About us', 'page');
    $this->theme(__DIR__.'/..')->activate();

    $default = $this->fetch('/about-us/')->assertOk()->getContent();

    expect(substr_count($default, '<h1'))->toBe(1)
        ->and($default)->toContain('aria-label="Breadcrumb"')
        ->and($default)->toContain('Last updated')
        ->and($default)->toContain('prose prose-lede');

    fableSettings($this, articleSettings([
        'fable_page_breadcrumb' => false, 'show_updated' => false, 'page_lede' => false, 'show_page_comments' => false,
    ]));
    $html = $this->fetch('/about-us/')->assertOk()->getContent();

    expect(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('aria-label="Breadcrumb"')
        ->and($html)->not->toContain('Last updated')
        ->and($html)->toContain('class="prose"')
        ->and($html)->not->toContain('prose-lede')
        ->and($html)->not->toContain('data-teme-thread');

    // The site-wide switch for updated dates also takes the line away, with the page's own switch on.
    fableSettings($this, articleSettings(['show_updated_date' => false]));
    $html = $this->fetch('/about-us/')->assertOk()->getContent();

    expect($html)->not->toContain('Last updated')
        ->and($html)->toContain('aria-label="Breadcrumb"');
});

it('draws the byline, author card, Read next, related link and comment under each setting state', function (): void {
    $this->theme(__DIR__.'/..')->activate();

    // The partials get the site's published settings from core, so each state is published, then every partial drawn.
    $ada = articleAuthor();
    $page = articleBylinePage(new DateTimeImmutable('2026-03-11 09:00:00'), [$ada]);
    $recent = articleBylinePage(new DateTimeImmutable('2026-03-04 09:00:00'), [$ada]);
    $byline = fn (object $of, bool $time = true): string => view('theme::skins.fable.partials.article-byline', ['page' => $of, 'showReadingTime' => $time, 'showShare' => false])->render();
    $card = fn (array $authors): string => view('theme::skins.fable.partials.article-authors', ['authors' => $authors])->render();
    $entry = ['item' => (object) ['image' => null, 'url' => '/next/', 'title' => 'A next story'], 'reason' => 'Same section'];
    $readNext = fn (): string => view('theme::skins.fable.partials.article-readnext', ['entry' => $entry])->render();
    $section = (object) ['url' => '/section/', 'name' => 'Harbours'];
    $related = fn (): string => view('theme::skins.fable.partials.article-related', ['entries' => [], 'heading' => 'Keep reading', 'category' => $section])->render();
    $comment = fn (bool $byAuthor): string => view('theme::skins.fable.partials.comment-card', ['comment' => articleComment($byAuthor), 'withReply' => false])->render();
    $removed = articleComment();
    $removed->removed = true;
    $tombstone = fn (): string => view('theme::skins.fable.partials.comment-item', ['comment' => $removed])->render();

    // State 0, the defaults: everything drawn; under the one-day rule (Teme's own, the default) an edit ten days or three days on is an update.
    fableSettings($this, articleSettings());
    $html = $byline($page);
    expect($html)->toContain('w-[3.25rem] h-[3.25rem]')
        ->and($html)->toContain('>Ada Writer</a>')
        ->and($html)->toContain('Published March 1, 2026</time> · 5 min read · <time')
        ->and($html)->toContain('Updated March 11, 2026')
        ->and($byline($recent))->toContain('Updated March');
    // A seven-day rule ignores the three-day edit and keeps the ten-day one.
    fableSettings($this, articleSettings(['updated_after_days' => '7']));
    expect($byline($recent))->not->toContain('Updated March')->and($byline($page))->toContain('Updated March 11, 2026');
    fableSettings($this, articleSettings());
    $html = $card([$ada]);
    expect($html)->toContain('w-24 h-24')->and($html)->toContain('sm:grid-cols-[6rem_minmax(0,1fr)]')
        ->and($html)->toContain('Writes about quays.')->and($html)->toContain('More from Ada Writer')
        ->and($card([articleAuthor('No Page', '', '')]))->not->toContain('Written by');
    expect($readNext())->toContain('Read next')->and($readNext())->toContain('Same section')
        ->and($related())->toContain('All Harbours')
        ->and($comment(true))->toContain('class="c-avatar')->and($comment(true))->toContain('>JD</span>')->and($comment(true))->toContain('>Author</span>')
        ->and($comment(false))->not->toContain('>Author</span>')
        ->and($tombstone())->toContain('c-avatar is-empty')->and($tombstone())->toContain('This comment was removed.');

    // State 1: no byline initials, no site-wide Updated; no card initials and no bio; no reason, no "All" link; no comment initials.
    fableSettings($this, articleSettings([
        'fable_byline_avatar' => false, 'show_updated_date' => false, 'fable_author_card_avatar' => false,
        'fable_author_card_bio' => false, 'fable_read_next_reason' => false, 'fable_related_more_link' => false,
        'fable_comments_avatar' => false,
    ]));
    $html = $byline($page);
    expect($html)->not->toContain('w-[3.25rem]')->and($html)->toContain('>Ada Writer</a>')->and($html)->not->toContain('Updated March');
    $html = $card([$ada]);
    expect($html)->not->toContain('w-24 h-24')->and($html)->not->toContain('sm:grid-cols')->and($html)->not->toContain('Writes about quays.')
        ->and($html)->toContain('Written by')->and($html)->toContain('More from Ada Writer');
    expect($readNext())->toContain('A next story')->and($readNext())->not->toContain('Same section')
        ->and($related())->toContain('Keep reading')->and($related())->not->toContain('All Harbours')
        ->and($comment(true))->not->toContain('c-avatar')->and($comment(true))->toContain('>Author</span>')->and($comment(true))->toContain('Jo Doe')
        ->and($tombstone())->not->toContain('c-avatar')->and($tombstone())->toContain('This comment was removed.');

    // State 2: no names, no byline Updated; no card link (the bio stays); no Author badge.
    fableSettings($this, articleSettings([
        'fable_byline_authors' => false, 'fable_article_show_updated' => false, 'fable_author_card_link' => false,
        'fable_comments_author_badge' => false,
    ]));
    $html = $byline($page);
    expect($html)->not->toContain('Ada Writer')->and($html)->toContain('w-[3.25rem]')
        ->and($html)->toContain('Published March 1, 2026</time> · 5 min read</p>')->and($html)->not->toContain('Updated March');
    $html = $card([$ada]);
    expect($html)->toContain('Writes about quays.')->and($html)->not->toContain('More from');
    expect($comment(true))->toContain('c-avatar')->and($comment(true))->not->toContain('>Author</span>');

    // State 3: no date, so the reading time leads and the update follows; an author with neither bio nor link has no card.
    fableSettings($this, articleSettings(['fable_byline_date' => false, 'fable_author_card_bio' => false, 'fable_author_card_link' => false]));
    $html = $byline($page);
    expect($html)->not->toContain('Published')->and($html)->toContain('5 min read · <time datetime="2026-03-11')
        ->and($card([$ada]))->not->toContain('Written by');

    // State 4: a two-week rule ignores a ten-day-old edit.
    fableSettings($this, articleSettings(['updated_after_days' => '14']));
    expect($byline($page))->not->toContain('Updated March')->and($byline($page))->toContain('Published March 1, 2026');

    // State 5: nothing left in the byline (the article passes the reading-time switch in; the share row is off here): no bordered row at all.
    fableSettings($this, articleSettings([
        'fable_byline_avatar' => false, 'fable_byline_authors' => false, 'fable_byline_date' => false,
        'show_reading_time' => false, 'fable_article_show_updated' => false,
    ]));
    expect(trim($byline($page, false)))->toBe('');
});

it('puts the inline related card after the body\'s second block, before the in-article ad, and drops it when off', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    $entry = ['item' => (object) ['image' => null, 'url' => '/quay/', 'title' => 'The quay story'], 'reason' => ''];
    $draw = fn (string $html, bool $ads, ?array $inline): string => view('theme::skins.fable.partials.article-body', [
        'html' => $html, 'lede' => true, 'ads' => $ads, 'after' => 4, 'min' => 8, 'contents' => false, 'contentsMin' => 2,
        'inline' => $inline,
    ])->render();
    $blocks = static fn (int $n): string => implode('', array_map(static fn (int $i): string => '<p>Block '.$i.'.</p>', range(1, $n)));

    // Five blocks: the card sits between the second and the third, with no ad (too short for a cut, ads off here).
    $html = $draw($blocks(5), false, $entry);
    expect($html)->toContain('<div class="prose prose-lede"><p>Block 1.</p><p>Block 2.</p></div>')
        ->and($html)->toContain('class="story inline-related"')
        ->and($html)->toContain('>The quay story</a>')
        ->and($html)->toContain('<div class="prose prose-cont"><p>Block 3.</p>');

    // Without a card (the switch is off, so the article passes none): no card and the body is whole.
    $html = $draw($blocks(5), false, null);
    expect($html)->not->toContain('inline-related')->and($html)->toContain('<div class="prose prose-lede"><p>Block 1.</p>');

    // Eight blocks and the ad: the card first, then the ad after block four.
    $html = $draw($blocks(8), true, $entry);
    expect(strpos($html, 'inline-related'))->toBeLessThan(strpos($html, 'ad-slot'))
        ->and(strpos($html, 'Block 3.'))->toBeGreaterThan(strpos($html, 'inline-related'))
        ->and(substr_count($html, 'class="prose prose-cont"'))->toBe(2);

    // A body with fewer than three blocks gets the card after it; the story is not lost.
    $html = $draw($blocks(2), false, $entry);
    expect($html)->toContain('<div class="prose prose-lede"><p>Block 1.</p><p>Block 2.</p></div>')
        ->and(strpos($html, 'inline-related'))->toBeGreaterThan(strpos($html, 'Block 2.'));
});

it('compiles the article, page, comments, respond and protected templates', function (): void {
    foreach (['article', 'page', 'comments', 'respond', 'protected', 'partials.article-inline-related', 'partials.article-byline', 'partials.article-authors', 'partials.article-readnext', 'partials.article-related', 'partials.article-body'] as $view) {
        $file = __DIR__.'/../views/skins/fable/'.str_replace('.', '/', $view).'.blade.php';
        $compiled = Illuminate\Support\Facades\Blade::compileString((string) file_get_contents($file));

        expect(fn () => token_get_all($compiled, TOKEN_PARSE))->not->toThrow(ParseError::class);
    }
});
