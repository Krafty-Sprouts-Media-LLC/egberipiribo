# DECISIONS

Every decision taken while porting Egberipiribo to Teme, and why. The brief said not to stop and ask, so each
choice that could have gone another way is written here. Newest at the bottom of each section.

## Scope and structure

1. **One skin, Fable, in a skin-ready tree.** Templates Teme chooses (`views/templates/*.blade.php`) are one-line
   dispatchers: `@include('theme::skins.'.($themeSettings['skin'] ?? 'fable').'.<page>')`. The skin is read in that
   one place, and every Fable page lives in `views/skins/fable/`. A later update adds a skin by adding a folder
   `views/skins/<name>/` with the same page names, a stylesheet import in `assets/css/theme.css`, and (if wanted) a
   `skin` select setting. No Fable template names Fable's own folder except its own `@extends`.
2. **Fable's styles are the drawings' styles, once.** The nine drawings carry the same `<style>` block (a diff
   shows only a `.patterned` addition in the two pattern samples). It is ported once into
   `assets/css/skins/fable.css`; page-only CSS lives in `fable-home.css`, `fable-article.css`, `fable-listing.css`.
3. **Package and code.** Composer package `krafty-sprouts/egberipiribo`, theme code `egberipiribo`, manifest
   `author` "Krafty Sprouts Media, LLC", MIT licence. `requires.teme` is `>=0.8.0-alpha.1 <1.0`, the range the
   handbook says to state (the contracts reference offers 0.9.0; the dev site reports 0.8.0-alpha.1).
4. **No theme settings in this release.** The brief asks for the drawings' choices as fixed defaults. Every
   choice is read once at the top of its template as `$themeSettings['key'] ?? <drawing's default>` so a later
   update can declare `key` in `teme-theme.json` and change no markup (see OPTIONS.md for the proposed keys).

## Tooling

5. **Vite 7 + `@tailwindcss/vite` + `laravel-vite-plugin`, own `package.json`.** The default theme is built by a
   config in Teme's own repository (`vite.theme.config.ts`, `vite-plus`); a theme in its own folder cannot use it,
   so the theme carries its own. Output goes to `public/build`, committed, as the default theme does, with
   `base: '/themes/egberipiribo/build/'` and the manifest's `assets.manifest` pointing at it.
6. **Tailwind lives inside core's cascade layer.** The stylesheet declares `teme.theme.{theme,base,components,
   utilities}` and imports Tailwind's parts into those sub-layers, so everything sits in core's `teme.theme`
   layer, after `teme.core` and `teme.plugins`, and the unlayered tokens core writes keep winning.
7. **Fonts: Fontsource packages, copied by the Vite build.** `@fontsource-variable/bricolage-grotesque` and
   `@fontsource-variable/literata` supply the variable `woff2` files. A small Vite plugin (`selfHostedFonts` in
   `vite.config.js`) copies the six files the theme uses into `public/fonts` on every build, where the manifest's
   `fonts` key and `assets/css/fonts.css` point, as the default theme does (a font path Vite could resolve would be
   copied under a hashed name and then load twice). Only the Latin and Latin Extended subsets are shipped;
   Cyrillic, Greek and Vietnamese are left out to keep the page light (add them from the same packages if a site
   needs them). Bricolage Grotesque uses the `wdth` build (weight and width axes) because the drawings set
   `font-stretch`; Literata uses the `wght` build, without its optical-size axis, which halves the file (52 kB
   instead of 110 kB) at a small cost in fine optical tuning. Nothing is loaded from Google or any other host.
8. **Icons: inline SVG from `@phosphor-icons/core`, not the Phosphor icon font.** The drawings load
   `@phosphor-icons/web` (a CSS file and font, about 4 formats, every icon). Teme asks for outside resources to be
   bundled or declared in `csp`; an icon font would add weight and a font file for 19 glyphs. `scripts/build-icons.mjs`
   writes one Blade view per icon used (`views/icons/*.blade.php`); the views are committed.
9. **Alpine's CSP build (`@alpinejs/csp`).** Teme sets a page policy, and Alpine's standard build compiles
   attribute strings with `new Function`, which needs `unsafe-eval`. The CSP build reads no string as code, so
   every x-* attribute names a property or a method and the logic is in `assets/js/skins/fable.js`. Pages work with
   the script off: the menu, sections and search are links and a plain form first.
10. **Nothing runs in views.** No inline script anywhere (`teme:theme:check` refuses one).

## Design tokens and the accent

11. **The brand colour is the accent.** Fable's "rail" colour is `#2135d8`; it is the manifest's `accent`, so the
    accent set under Settings, Design recolours the rail, kickers, links, buttons and page numbers. Utilities
    `bg-rail`, `text-rail` read `var(--teme-accent)`.
12. **Tokens beyond the allowed set are derived in CSS.** The manifest accepts only a fixed list of tokens (see
    FINDINGS.md). Fable's body-text colour, surface tint and radius are derived in `fable.css` from
    `--teme-contrast`/`--teme-base` with `color-mix()`; `font-heading` carries Bricolage Grotesque (headlines and
    interface) and `font-body` carries Literata (the text of a story).
13. **No `tokens_dark`.** Fable's drawings have no dark scheme, so none is declared. Teme lightens the accent only
    for a dark scheme.

## Data Teme does not give (and what replaces it)

14. **The rail's section counts, "Most read", "Trending" and "Quick reads" sort are not built.** Teme gives a
    template no counts, no view data and no sort. Sections and their topics come from the `primary` menu tree
    (an editor builds the menu); "Top stories" is the lead plus pinned posts; "Most read" and "From the sections"
    are regions an editor fills with `featured-posts` cards (a category, or chosen stories).
15. **Live search results in the panel become a plain search form.** Teme has no JSON endpoint for a theme; the
    panel posts a GET to `/?s=`, and the search page lists results with core's snippets.
16. **Newsletter.** A form that posts to a mailing service is a feature, not looks (handbook rule: a feature that
    outlives the theme belongs in a plugin). The footer band keeps the drawing's heading and text and holds the
    `newsletter` region, which an editor fills with a Custom HTML card (a service's embed form); with the region empty
    the band hides itself (CSS `:has`).
17. **Advertisements** are regions (`ad-*`), each filled with a Custom HTML card, under the drawings' "Advertisement"
    label; an empty slot hides itself, so no page shows a label over nothing.
18. **Pictures come from Teme media.** The drawings' Unsplash images are placeholders; every image is
    `<x-teme::image>` with a context from the manifest's `images` key.

## Working together

19. **The shared dev site, in turns.** `teme-dev` is used by this session, the temecms.com theme session and the
    Securetor port session. We agreed (by message) a lock file, `teme-plugins/.teme-dev.lock`, taken before any change
    to shared state (`.env`, migrations, plugin enable/disable, the active theme) and deleted straight after. Tests
    use their own database and need no lock; my own page agents also take `.theme-tests.lock` around test runs.
20. **Workers.** `schedule:work`, `queue:work` and `artisan serve` were already running for `teme-dev`, so I did not
    start duplicates.
21. **File headers.** Every file carries a header with `@package egberipiribo` and `@since 0.1.0`
    (Blade `{{-- --}}` blocks, PHP docblocks, CSS and JS comments). JSON files cannot carry comments; their
    meaning is in this folder's docs.

## Advertising

22. **Every ad place in the drawings is a declared region, named for its place.** The drawings have six ad places:
    the header banner (leaderboard, 728 × 90, under the top of home, archive and article pages), the in-feed banner
    (970 × 250, between rows of stories on home and archive), the most-read side ad (336 × 280), two in-article ads
    (300 × 250) and the article side column (half page, 300 × 600). They are the regions `ad-leaderboard`,
    `ad-in-feed`, `ad-rectangle`, `ad-in-article`, `ad-in-article-2` and `ad-half-page`, labelled in the editor by
    place ("Header banner…", "In-feed banner…"). Each allows only the Custom HTML (`site-code`) card, which is how an
    administrator places an ad network's code; the theme contains no ad code, no network script and no placeholder ad.
    AdPlacr places ads inside article text through its own insertion socket, so it needs no region.
23. **An empty ad region draws nothing at all.** The slot (label and all) is `display: none` while its region holds
    nothing (CSS `:has()` on `.ad-slot`), so no empty box shows and no space is reserved. A slot an editor fills
    reserves its height only by the ad's own markup, which is the editor's to size: the drawings' reserved heights
    existed to hide layout shift from a placeholder, and a placeholder is not allowed.

## Verification and tooling decisions

24. **Page agents.** The page templates were built by three parallel agents (home, article and comment pages, listings and
    error pages) from one written brief, each owning separate files, with the lead owning the shared chrome, manifest, build
    and commits. A fourth wrote the first draft of FEATURES.md and a fifth the seven translation files. The lead reviewed
    each report, fixed the shared defects they found (the `Pagination` key clash, the archive's column count) and ran the
    whole suite.
25. **The demo seed reaches into Teme.** `seed/seed.php` is the one place the work calls Teme's own `App` Operations: an
    outside developer has no public way to make menus, regions, media and posts in a site (FINDINGS #13). It is a dev tool,
    outside the theme's templates, tests and manifest, and it is the same approach the temecms theme's seed uses. A test that
    first did the same to give a post headings was rewritten to use only the public kit.
26. **Two ways to audit.** `scripts/audit.mjs` runs axe-core, an overflow check and an h1 count in a real browser at 1280 and
    375 pixels. It runs against the dev site when this theme is active there, and against `docs/snapshots` (pages the test kit
    rendered, written by `tests/SnapshotTest.php` when `FABLE_SNAPSHOT=1`, served by `scripts/serve-snapshots.mjs`) when the
    shared site is busy with another theme. Snapshots are layout-only: no pictures, menus or categories.
27. **Translations.** `lang/{de,es,fr,hi,it,ja,pt_BR}.json`, the languages the default theme ships, for the 123 sentences in
    the views; words shared with the default theme reuse its wording.
28. **Taking turns on the dev site.** The temecms session's messages could be read only when its long turns ended, so the lock
    file and its work in progress were the effective signals. Switching the active theme waits until its files have been quiet
    for six minutes and the lock is free.

## The options proposal (second version)

29. **OPTIONS.md is generated, per skin.** The owner found the first version (about 55 settings, Fable only) too thin. It is now
    `docs/options.json` (the data) and `scripts/build-options.mjs` (writes `OPTIONS.md`, validates every row against Teme's key,
    type, fieldset, select-default and `visible_when` rules, and computes the totals). `skin` is the first setting; every skin's
    keys carry its prefix and `visible_when: {"skin": …}`; Fable's blocks each have a switch; Strata and Threads are listed from the
    WordPress theme's own options so the per-skin structure is proved. Defaults reproduce the drawings; the 46 keys the templates
    already read are marked "read now" (checked against the views by script); the rest need one read line each.
30. **No ad code or embed in a setting.** The WordPress theme kept ad code and a newsletter embed in text options. Those rows are
    dropped (the brief: ad plugins and the Custom HTML card fill the ad regions, never the theme); only on/off switches remain.
    The shared `show_ads` is the one master switch for every skin.

## The first settings (built)

31. **What was built.** 128 of OPTIONS.md's rows: `skin` (only `fable`), the 8 shared rows that were not waiting, every on/off switch of
    the home page, article, page, comments, archive, author, search, 404 and error templates, the share row (master, ten networks,
    placement, style, label), the whole pattern set (type, strength, density, accent, colour, layout, style and the six "apply to"
    switches) and the shared chrome switches (ads master, newsletter band, search panel and its "/" shortcut). They are declared in
    `teme-theme.json` by `scripts/build-settings.mjs` from the rows of `docs/options.json` marked `"build": true`; labels come from
    `docs/settings-labels/*.json` ("Area: what it controls", at most 60 characters, so the long Settings, Design list sorts by prefix).
    Everything else stays a proposal, as do the home-order selects and the rows marked "waits".
32. **One read per setting, checked by script.** Each template reads its keys once at the top (`$themeSettings['key'] ?? <default>`) and lists
    them in a "Settings this template reads" docblock. `scripts/check-settings.mjs` fails if a declared setting is read nowhere or a
    template's fallback differs from the manifest default, so a template and the manifest cannot disagree about what "unchanged" means.
33. **Defaults reproduce the drawings; where that moved the page, it is written here.** Pinterest share is off by default (the drawing has
    Facebook, X, email and copy only; the Pinterest button was an earlier addition of the port). The "updated" rule defaults to one day
    (Teme's own; WordPress's seven days stays the Strata and Threads default), and the inline "Related" card, which the drawing has but the
    port had left out, is off by default so no page changes until someone switches it on. Every other default renders as before.
34. **Pattern.** One partial (`partials/pattern`) builds the tile once and the page shell carries it as a custom property; the rail, phone header,
    footer and long read only add the `patterned` class. The "section headers" and "404" areas, which the two samples do not show, draw a
    brand-colour strip carrying the tile (`pattern-band`), above the archive, author and 404 headers and the front page's "from the
    sections" slot (hidden while that region is empty). The pattern is off by default. The tile is set in an inline `style` attribute,
    which a strict page policy could block; the pattern then stays solid (fails safe).
35. **Switch interplay**: a block's sub-options count only while its switch is on; with the front page's grid off, its pagination goes too;
    with "Also today" off its stories return to the grid; turning the search panel off makes the search buttons plain links and drops the
    "/" shortcut; the newsletter band needs both of its switches, and takes the "Get our newsletter" buttons with it.
36. **Tests reach into Teme once.** Proving that a setting changes a page needs a way to set a theme setting, which the public kit does not
    have (FINDINGS 25). `tests/Support/settings.php` calls the two Operations the admin uses to save and publish design settings; it is the
    only place the tests name Teme's own classes, and the only thing to replace when the kit gains a method.

## Teme 1.0

37. **The Teme range now covers 1.x.** The manifest said `>=0.8.0-alpha.1 <1.0`, which refuses Teme 1.0.0 (released 17 November 2026):
    `teme:theme:check` with `TEME_VERSION=1.0.0` failed with "The theme requires Teme >=0.8.0-alpha.1 <1.0, but this is Teme 1.0.0". It
    passed on the dev site only because that site's `.env` says 0.8.0-alpha.1. The range is now `>=0.8.0-alpha.1 <2.0`: the lower bound
    stays so the shared dev site still loads the theme, and the upper bound is the next major. Verified on the Teme 1.0.0 checkout: theme check
    passes under both versions, the whole suite passes with `TEME_VERSION=1.0.0` (60 tests), and a browser audit of nine rendered pages passes.
38. **Between items.** Teme 1.0 adds `<x-teme::between-items>` so plugins can place content between a list's items, and `teme:theme:check`
    warns when lists don't call it. The front page's grid, an archive, an author's stories and the search results now call it after each item
    through `partials/between`; the slot spans a whole grid row and hides itself while nothing is placed there, so a site with no such plugin
    looks as before. The check is a text scan of the root templates, so the four dispatchers that include the skin's lists carry a sentence
    saying so (FINDINGS 26).

## Moving to the workshop

39. **The folder moved to balafama's workshop** (`C:\Users\kings\Herd\balafama\workshop\themes\egberipiribo`, beside zenith) on
    22 November 2026, with its git history, `node_modules` and ignored files. The remote is now
    `Krafty-Sprouts-Media-LLC/egberipiribo` (the WordPress version was renamed `egberipiribo-wp`). To match the workshop's
    conventions the package gained `CLAUDE.md`, `README.md` and `CHANGELOG.md` (0.1.0, dated dd/MM/yyyy) and `AGENTS.md` now names the
    workshop rules, the remote and the commands to run from balafama. The manifest's `$schema` points at balafama's schema, and
    `teme-dev`'s `.env` entry and `public/themes` link were repointed at the new folder so the old dev site still loads it. The range
    `>=0.8.0-alpha.1 <2.0` already accepts balafama's 1.3.2, so no version bump was needed. balafama's `.env` was not touched: the person adds
    `workshop/themes/egberipiribo` to its `TEME_THEME_PATHS`. Two known exceptions to the workshop's "never `App\\`" rule remain, both Teme
    gaps (FINDINGS 13 and 25): `seed/seed.php` (teme-dev only) and `tests/Support/settings.php`.
