# AGENTS.md: Egberipiribo for Teme

Read this before changing the theme. It is short on purpose.

## What this package is

A Teme theme (`krafty-sprouts/egberipiribo`, code `egberipiribo`, by Krafty Sprouts Media, LLC) ported from the
WordPress theme of the same name. It ships the shared foundation and one skin, **Fable**. The other skins (Strata,
Threads and the design demos) come in later updates; `FEATURES.md` lists them as "later".

## Where things are

| Path | What |
|---|---|
| `teme-theme.json` | The manifest: tokens, fonts, image contexts, menus, regions, assets. |
| `views/templates/*.blade.php` | Teme's template names. Each is one line: it includes the active skin's page. |
| `views/skins/fable/` | Every Fable page (`home`, `article`, `page`, `archive`, `author`, `search`, `notfound`, `error`, `index`, `comments`, `respond`, `protected`), `layout.blade.php` and `partials/`. |
| `views/cards/featured-posts.blade.php` | How core's Featured posts card is drawn. |
| `views/icons/` | One SVG view per icon, written by `npm run icons`. |
| `assets/css/theme.css` | The one stylesheet entry; Tailwind inside core's `teme.theme` layer. |
| `assets/css/skins/` | `fable.css` (shared), `fable-home.css`, `fable-article.css`, `fable-listing.css`. |
| `assets/js/` | `theme.js` and `skins/fable.js`: Alpine's **CSP build** components. |
| `public/` | The committed build (`public/build`) and the self-hosted fonts (`public/fonts`). |
| `tests/` | Pest tests, run from the Teme checkout. |
| `FEATURES.md`, `OPTIONS.md`, `DECISIONS.md`, `FINDINGS.md`, `REPORT.md` | The port's record. |

## Rules that are easy to break

- **Teme data only.** Templates read `$page`, `$site`, `$navigation`, `$themeSettings`, `$isFront` and the
  components in Teme's `docs/reference/theme.md`. Never Teme's own `App\` classes.
- **One `<h1>` per page**; run `php artisan teme:theme:check egberipiribo`.
- **No inline script in a view.** Alpine is the CSP build: an `x-*` attribute names a property or a method, nothing
  more (no `!`, ternaries, object literals or assignments). Put the logic in `assets/js/skins/fable.js`.
- **Every printed word goes through `__()`** with the English sentence as its key.
- **One place per choice.** A choice an administrator may one day make is read once at the top of its template as
  `$themeSettings['key'] ?? <default>`. Add the setting to the manifest and nothing else changes.
- **Ads are regions.** Never ad code, an ad script or a placeholder ad in the theme.
- **Every file has a header** with `@package egberipiribo` and `@since`.
- **Fonts and icons are self-hosted.** Nothing loads from a font or icon host.

## Build, check, test

```bash
npm install
npm run build          # public/build and public/fonts
npm run icons          # after changing scripts/build-icons.mjs
```

From the Teme checkout (needs its PostgreSQL test database):

```bash
php artisan teme:theme:check egberipiribo
php artisan test ../teme-themes/egberipiribo/tests --compact
```

Accessibility and overflow, in a real browser, against a site that is drawing the theme:

```bash
node scripts/audit.mjs http://127.0.0.1:8099 home=/ article=/some-story/
```

Link the theme's `public` folder into the site once with `php artisan teme:themes:link` (in the Teme checkout).

## Handbook

Teme's developer handbook: `docs/guides/developers/` in the Teme checkout, starting at `start/first-theme.md`.
