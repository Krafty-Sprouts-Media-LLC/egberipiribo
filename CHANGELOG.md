# Changelog

All notable changes to this theme are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and versioning follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 22/11/2026

First release: the shared foundation and one skin, **Fable**, ported from the Egberipiribo WordPress theme (Sage, Blade, Tailwind CSS 4, Vite, Alpine.js).

### Added

- Fable's pages: front page (`latest`), article (`single`), page (`single-page`), archive, author, search, `notfound`, `error`, `index`, and core's comment pages (`comments`, `respond`, `protected`), as a skin folder (`views/skins/fable`) that the root templates include, so a later update can add skins.
- The rail with the site's sections, small-screen header and menu, search panel, footer with a newsletter band, social links, back to top, reading progress and an in-article contents list.
- 128 theme settings (Settings › Design): the skin choice and shared switches, every block of the front page, article, page, archive, author, search, 404 and error templates, the share row (ten networks, placement, style), the icon pattern (type, strength, density, accent, colour, layout, style and where it applies), the ads master switch and the newsletter band. Every default reproduces the drawings.
- Six advertisement regions named for their place, each filled only with the Custom HTML card and drawn only when filled; regions for the newsletter, after the article, and the front page's "also today", "the long read", "from the sections" and "most read".
- Plugins can place content between the items of the front page's grid, archives, author pages and search results (`x-teme::between-items`).
- Fonts (Bricolage Grotesque, Literata) self-hosted from Fontsource; 24 inline SVG icons; Alpine's CSP build; no outside host.
- Translations: de, es, fr, hi, it, ja, pt_BR.
- The site's accent from Settings › Design recolours the rail, kickers, links and buttons.

### Notes

- Requires Teme `>=0.8.0-alpha.1 <2.0`.
- Not yet: the Strata and Threads skins, a dark scheme, and the settings still listed as proposals in `OPTIONS.md`.
