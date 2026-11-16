<?php

declare(strict_types=1);

/**
 * Fable's article page and its comment pages: what a published post draws (the skin's template, one h1, the
 * breadcrumb, the reading bar, the body in .prose, the share row, the ad slots, which hide while their region is
 * empty, and the comment thread with core's hooks), the same for a post whose body has headings (the rail's
 * contents list and the ad inside the body), and that the comments, respond and protected templates compile.
 *
 * The kit's publish() makes a post with an empty body, and the public kit has no way to give one headings, so the
 * body partial is rendered on its own with a body written in the test; a post with no body, no image, author or
 * category covers the empty branches.
 *
 * @package egberipiribo
 * @since   0.1.0
 */

use App\Domain\Contents\Operations\CreateDraft;
use App\Domain\Contents\Operations\Input\CreateDraftInput;
use App\Domain\Contents\Operations\Input\PublishRevisionInput;
use App\Domain\Contents\Operations\PublishRevision;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Teme\Testing\Acting;

uses(Teme\Testing\TemeTestCase::class);

/** The scripts a page may carry that the view did not write: core's own, by their markers. */
function articleForeignScripts(string $html): array
{
    preg_match_all('/<script\b([^>]*)>/i', $html, $found);

    return array_values(array_filter($found[1], static fn (string $attributes): bool => ! str_contains($attributes, 'src=')
        && ! str_contains($attributes, 'application/ld+json')
        && ! str_contains($attributes, 'speculationrules')
        && ! str_contains($attributes, 'data-teme=')));
}

it('draws a post with the article template', function (): void {
    $this->publish('A first story');
    $this->theme(__DIR__.'/..')->activate();

    $response = $this->fetch('/a-first-story/')->assertOk();
    $html = $response->getContent();

    // The skin's article template drew it, with exactly one h1 and the one <main>.
    expect($html)->toContain('data-template="single"')
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count($html, '<main'))->toBe(1)
        ->and($html)->toContain('<h1 class="hed hed-page">A first story</h1>');

    // The breadcrumb, the reading bar, the body block, the share row and the byline's date.
    expect($html)->toContain('aria-label="Breadcrumb"')
        ->and($html)->toContain('data-progress')
        ->and($html)->toContain('class="prose')
        ->and($html)->toContain('sharer.php?u=http%3A%2F%2Flocalhost%2Fa-first-story%2F')
        ->and($html)->toContain('x-data="copyLink"')
        ->and($html)->toContain('<time datetime=');

    // No lead image, no tags and no related stories: those blocks leave themselves out. The kit's editor is the author.
    expect($html)->not->toContain('Filed under')
        ->and($html)->not->toContain('Read next')
        ->and($html)->not->toContain('Keep reading')
        ->and($html)->not->toContain('In this story')
        ->and($html)->toContain('rel="author"')
        ->and($html)->toContain('Written by');

    // The ad slots are in the page, empty, and hide themselves; the after-content region draws nothing.
    expect(substr_count($html, 'class="ad-body"></div>'))->toBeGreaterThanOrEqual(4)
        ->and($html)->toContain('class="article-after mt-10"></div>');

    // Comments are open on a bare post: the thread holds core's hooks and core's form.
    expect($html)->toContain('data-teme-thread')
        ->and($html)->toContain('data-teme-respond')
        ->and($html)->toContain('data-teme-form');

    // Views carry no inline script of their own.
    expect(articleForeignScripts($html))->toBe([]);
});

it('draws the contents list and the in-article ad for a body with headings', function (): void {
    $this->theme(__DIR__.'/..')->activate();
    $body = '<p>First paragraph.</p><h2>The quay</h2><p>Two.</p><p>Three.</p><p>Four.</p><h3>A detail</h3>'
        .'<blockquote><p>A quoted line.</p></blockquote><h2>The quay</h2><p>Five.</p><h2>Comments &amp; more</h2><p>Six.</p>';

    $html = view('theme::skins.fable.partials.article-body', ['html' => $body, 'lede' => true, 'ads' => true, 'after' => 4, 'min' => 8, 'contents' => true, 'contentsMin' => 2])->render();

    // Core writes no id on an h2, so the view gives each one, kept unique.
    expect($html)->toContain('<h2 id="the-quay">The quay</h2>')
        ->and($html)->toContain('<h2 id="the-quay-2">The quay</h2>')
        ->and($html)->toContain('<h2 id="comments-more">Comments &amp; more</h2>');

    // The body is cut between two blocks for the first in-article ad, and each half is a .prose block.
    expect($html)->toContain('<div class="prose prose-lede"><p>First paragraph.</p><h2 id="the-quay">The quay</h2><p>Two.</p><p>Three.</p></div>')
        ->and($html)->toContain('<div class="prose prose-cont"><p>Four.</p>');
});

it('compiles the comments, respond and protected templates and the thread partials', function (): void {
    // These pages are reached only through a thread with comments, a reply link or a protected item, which the kit
    // cannot make, so each view's compiled code is checked for syntax instead.
    foreach (['comments', 'respond', 'protected', 'partials.comments-thread', 'partials.comment-item', 'partials.comment-card'] as $view) {
        $file = __DIR__.'/../views/skins/fable/'.str_replace('.', '/', $view).'.blade.php';
        $compiled = Blade::compileString((string) file_get_contents($file));

        expect(fn () => token_get_all($compiled, TOKEN_PARSE))->not->toThrow(ParseError::class);
    }
});
