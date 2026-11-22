# Egberipiribo

**Version:** 0.1.0

A multi-skin publishing theme for [Teme CMS](https://github.com/temecms/teme), by Krafty Sprouts Media, LLC. It ships the shared foundation and one skin, **Fable**: a colour rail with the site's sections, a front page that leads deeper, and stories set for long reading. It is the Teme port of the Egberipiribo WordPress theme.

## Requirements

- Teme `>=0.8.0-alpha.1 <2.0`
- PHP 8.4+

## What it draws

| Template | Draws |
|---|---|
| `latest` | The front page and the posts page: top stories, cover story, also today, the long read, a story grid, sections, most read |
| `single` | An article: breadcrumb, headline, byline, share row, hero, body with a contents list, tags, author card, related stories, comments |
| `single-page` | A page |
| `archive` | A section, topic, tag or date, with a lead story and a grid or list |
| `author` | A writer and their stories |
| `search` | Results with the matched passage |
| `notfound`, `error` | The 404 and other errors |
| `comments`, `respond`, `protected` | Core's comment pages and password form, in Fable's look |

**Menus:** `primary` (the rail's sections), `footer`, `network`, `social`.
**Regions:** after the article, the newsletter, four front-page blocks, and six advertisement places (header banner, in-feed, most-read side, two in-article, article side column). An advertisement region draws only when something fills it; the theme holds no ad code.

## Settings

128 settings under Settings › Design cover every block of every page, the share row, the icon pattern, ads and the newsletter band. Every default is the design's own. The full list, with what each does, is in [OPTIONS.md](OPTIONS.md); more are proposed there for later.

## Install on a Teme site

**From the admin:** Site › Themes › **Add theme**, the **Git address** tab: `https://github.com/Krafty-Sprouts-Media-LLC/egberipiribo.git`, package `krafty-sprouts/egberipiribo`. Then activate it under Site › Themes, assign menus to the four slots and fill the regions you want.

**From a folder (development):** point the site at this folder (comma-separate if you have several themes):

```dotenv
TEME_THEME_PATHS=workshop/themes/egberipiribo
```

From the Teme site root:

```bash
php artisan teme:theme:check egberipiribo
php artisan teme:themes:link
```

Then activate it under Site › Themes.

## Build

The build output (`public/build`) and the fonts (`public/fonts`) are committed. To rebuild after changing styles or scripts:

```bash
npm install
npm run build
```

`npm run icons` rewrites the inline icon views from `scripts/build-icons.mjs`.

## Tests

From the Teme site root, with its PostgreSQL test database running:

```bash
php artisan test workshop/themes/egberipiribo/tests --compact
```

`scripts/audit.mjs` runs accessibility (axe-core), sideways-scroll and one-`h1` checks in a real browser against a site that is drawing the theme.

## The port's record

[FEATURES.md](FEATURES.md) (what became what), [OPTIONS.md](OPTIONS.md) (settings), [DECISIONS.md](DECISIONS.md), [FINDINGS.md](FINDINGS.md) (gaps found in Teme), [REPORT.md](REPORT.md), [CHANGELOG.md](CHANGELOG.md).

## Licence

MIT. The fonts are under the SIL Open Font License 1.1 (`public/fonts/OFL-*.txt`); the icons are Phosphor (MIT).
