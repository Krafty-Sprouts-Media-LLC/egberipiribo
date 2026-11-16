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
