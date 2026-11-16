<?php

declare(strict_types=1);

/**
 * The Fable listing pages: the archive (a date, here), the author page, the search page, the not-found page, and the
 * two plain templates (error, index). Each page reachable with the testing kit is fetched and checked for the
 * template that drew it, its status, its one h1, its main landmark, no inline script, and its own facts: the
 * lead story and the grid with its layout buttons, the profile with the initials, the search form prefilled and
 * the count, the empty states, the 404's search form.
 *
 * Not reachable with the kit, so not tested through a request: a category's own page (the kit makes no terms), a
 * second page of a listing (the shared pagination partial fails on a case-insensitive file system, see the
 * report), a result's snippet (the kit's posts have no body), the section chips (the kit sets no menu), and the
 * error and index templates, which no address reaches; those two are compiled and parsed instead.
 *
 * @package egberipiribo
 * @since   0.1.0
 */
uses(Teme\Testing\TemeTestCase::class);

/** The page's one <main>, from its opening tag to its closing tag. */
function listingsMain(string $html): string
{
    $start = (int) strpos($html, '<main');

    return substr($html, $start, (int) strpos($html, '</main>') - $start);
}

/** The facts every listing page shares: its template, one h1, one main, and no inline script. */
function listingsBasics(string $html, string $template): void
{
    expect($html)->toContain('data-template="'.$template.'"')
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and(substr_count($html, '<main'))->toBe(1)
        ->and(preg_match('/<script(?![^>]*\bsrc=)(?![^>]*application\/ld\+json)[^>]*>/', listingsMain($html)))->toBe(0);
}

it('draws a date archive: the lead story, then the grid with its layout buttons', function (): void {
    $year = (int) date('Y');
    $this->person('administrator');
    foreach (['First wolf story', 'Second wolf story', 'Third wolf story'] as $title) {
        $this->publish($title);
    }
    $this->theme(__DIR__.'/..')->activate();

    $response = $this->fetch('/'.$year.'/')->assertOk();
    $html = $response->getContent();
    listingsBasics($html, 'archive');

    expect($html)->toContain('<h1 class="hed hed-page mt-2">'.$year.'</h1>')
        ->and($html)->toContain('Archive')
        ->and($html)->toContain('aria-label="Latest in '.$year.'"')
        ->and($html)->toContain('<h2 class="hed hed-xl mt-2.5"><a href="/third-wolf-story/">Third wolf story</a></h2>')
        ->and($html)->toContain('All stories in '.$year.'')
        ->and($html)->toContain('x-data="layoutToggle"')
        ->and($html)->toContain(':class="listClass"')
        ->and(substr_count($html, ':class="rowClass"'))->toBe(2)
        ->and($html)->toContain('Grid layout')
        ->and($html)->toContain('List layout')
        ->and($html)->toContain('aria-label="Breadcrumb"')
        ->and($html)->toContain('Second wolf story')
        ->and($html)->not->toContain('Newest');

    // The lead story is not drawn twice: the grid holds the other two only.
    expect(substr_count($html, 'Third wolf story</a>'))->toBe(1);
});

it('draws the author page with initials for a writer without a portrait, and an empty page for one without stories', function (): void {
    $admin = $this->person('administrator');
    $this->publish('A story by the editor');
    $this->theme(__DIR__.'/..')->activate();

    preg_match('#href="(/author/[^"]+)" rel="author"#', $this->fetch('/')->getContent(), $found);
    expect($found)->not->toBeEmpty();

    $html = $this->fetch($found[1])->assertOk()->getContent();
    listingsBasics($html, 'author');
    expect($html)->toContain('<h1 class="hed hed-page mt-2">Person 2</h1>')
        ->and($html)->toContain('>P2</div>')
        ->and($html)->toContain('Writer')
        ->and($html)->toContain('Latest from Person 2')
        ->and($html)->toContain('<h3 class="hed card-title"><a href="/a-story-by-the-editor/">A story by the editor</a></h3>')
        ->and($html)->toContain('listing-rows');

    $empty = $this->fetch('/author/'.Illuminate\Support\Str::slug($admin->name).'/')->assertOk()->getContent();
    listingsBasics($empty, 'author');
    expect($empty)->toContain('No stories yet')
        ->and($empty)->toContain($admin->name.' has not published anything here yet.')
        ->and($empty)->not->toContain('listing-rows');
});

it('draws the search page: results with the form prefilled and the count, then the empty states', function (): void {
    $this->publish('Wolves of the high Arctic');
    $this->publish('Something else entirely');
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/?s=wolves')->assertOk()->getContent();
    listingsBasics($html, 'search');
    expect($html)->toContain('role="search"')
        ->and($html)->toContain('name="s"')
        ->and($html)->toContain('value="wolves"')
        ->and($html)->toContain('1 result')
        ->and($html)->toContain('<h2 class="hed card-title"><a href="/wolves-of-the-high-arctic/">Wolves of the high Arctic</a></h2>')
        ->and($html)->toContain('listing-rows')
        ->and($html)->not->toContain('Something else entirely')
        ->and($html)->not->toContain('No stories match');

    $none = $this->fetch('/?s=zzzqqq')->assertOk()->getContent();
    listingsBasics($none, 'search');
    expect($none)->toContain('No stories match that search')
        ->and($none)->toContain('Check the spelling')
        ->and($none)->toContain('value="zzzqqq"')
        ->and($none)->not->toContain('listing-rows');

    $blank = $this->fetch('/?s=')->assertOk()->getContent();
    listingsBasics($blank, 'search');
    expect($blank)->toContain('Type a word to start searching')
        ->and($blank)->toContain('value=""')
        ->and($blank)->not->toContain('listing-rows');

    // What the reader typed is escaped wherever it is printed.
    $hostile = $this->fetch('/?s='.urlencode('<b>x</b>'))->assertOk()->getContent();
    expect($hostile)->not->toContain('<b>x</b>')
        ->and($hostile)->toContain('value="&lt;b&gt;x&lt;/b&gt;"');
});

it('draws an archive with nothing in it as an empty state with a search form', function (): void {
    $year = (int) date('Y');
    $this->publish('A story in '.$year);
    $this->theme(__DIR__.'/..')->activate();

    $html = $this->fetch('/'.($year - 1).'/')->assertOk()->getContent();
    listingsBasics($html, 'archive');
    expect($html)->toContain('<h1 class="hed hed-page mt-2">'.($year - 1).'</h1>')
        ->and($html)->toContain('Nothing here yet')
        ->and($html)->toContain('role="search"')
        ->and($html)->not->toContain('aria-label="Latest in '.($year - 1).'"')
        ->and($html)->not->toContain('layoutToggle');
});

it('draws the not-found page with its status, a search form and the way home', function (): void {
    $this->publish('A first story');
    $this->theme(__DIR__.'/..')->activate();

    $response = $this->fetch('/no-such-page-here/')->assertNotFound();
    $html = $response->getContent();
    listingsBasics($html, 'notfound');
    expect($html)->toContain('That page has moved, or it was never here')
        ->and($html)->toContain('Page not found')
        ->and($html)->toContain('role="search"')
        ->and($html)->toContain('name="s"')
        ->and($html)->toContain('Go to the home page')
        ->and($html)->toContain('aria-hidden="true">404</p>');
});

it('compiles the error and index templates, which no address reaches, to valid PHP that reads little from the page', function (): void {
    $this->theme(__DIR__.'/..')->activate();

    foreach (['error', 'index'] as $name) {
        $source = file_get_contents(__DIR__.'/../views/skins/fable/'.$name.'.blade.php');
        $php = app('blade.compiler')->compileString($source);

        expect(fn () => token_get_all($php, TOKEN_PARSE))->not->toThrow(ParseError::class);
        expect($source)->toContain("@section('template', '".$name."')")
            ->and(substr_count($source, '<h1'))->toBe(1)
            ->and($source)->not->toContain('<script');
    }

    // The index template reads nothing from $page.
    expect(file_get_contents(__DIR__.'/../views/skins/fable/index.blade.php'))->not->toContain('$page->');
});
