# AGENTS.md: Egberipiribo for Teme

Read this before changing the theme. It is short on purpose. Parent workshop rules: `../../AGENTS.md`.

## What this package is

A Teme theme (`krafty-sprouts/egberipiribo`, code `egberipiribo`, by Krafty Sprouts Media, LLC) ported from the
WordPress theme of the same name. It ships the shared foundation and one skin, **Fable**. The other skins (Strata,
Threads and the design demos) come in later updates; `FEATURES.md` lists them as "later".

## Package facts

| | |
|---|---|
| Manifest | `teme-theme.json` |
| Code | `egberipiribo` (the folder name must match) |
| Version | keep in sync with `CHANGELOG.md` |
| Requires | Teme `>=0.8.0-alpha.1 <2.0` (accepts 1.3.2, the version balafama runs) |
| Remote | https://github.com/Krafty-Sprouts-Media-LLC/egberipiribo.git |
| Lives at | `C:\Users\kings\Herd\balafama\workshop\themes\egberipiribo` (moved there from `teme-plugins/teme-themes/`) |

The WordPress version of this theme is a separate repository, `egberipiribo-wp`. Never mix the two.

## Where things are

| Path | What |
|---|---|
| `teme-theme.json` | The manifest: tokens, fonts, image contexts, menus, regions, assets, 128 settings. |
| `views/templates/*.blade.php` | Teme's template names. Each is one line: it includes the active skin's page. |
| `views/skins/fable/` | Every Fable page (`home`, `article`, `page`, `archive`, `author`, `search`, `notfound`, `error`, `index`, `comments`, `respond`, `protected`), `layout.blade.php` and `partials/`. |
| `views/cards/featured-posts.blade.php` | How core's Featured posts card is drawn. |
| `views/icons/` | One SVG view per icon, written by `npm run icons`. |
| `assets/css/theme.css` | The one stylesheet entry; Tailwind inside core's `teme.theme` layer. |
| `assets/css/skins/` | `fable.css` (shared), `fable-home.css`, `fable-article.css`, `fable-listing.css`, `fable-chrome.css`. |
| `assets/js/` | `theme.js` and `skins/fable.js`: Alpine's **CSP build** components. |
| `public/` | The committed build (`public/build`) and the self-hosted fonts (`public/fonts`). |
| `docs/options.json`, `docs/settings-labels/` | The settings' data and labels. `OPTIONS.md` and the manifest's `settings` are generated from them. |
| `tests/` | Pest tests, run from the Teme site. |
| `scripts/` | Build, settings, options and audit scripts. |
| `seed/` | **teme-dev only** demo content script; see the rule below. |
| `FEATURES.md`, `OPTIONS.md`, `DECISIONS.md`, `FINDINGS.md`, `REPORT.md`, `CHANGELOG.md` | The port's record. |

## Rules that are easy to break

- **Teme data only.** Templates read `$page`, `$site`, `$navigation`, `$themeSettings`, `$isFront` and the
  components in Teme's `docs/reference/theme.md`. Never Teme's own `App\` classes.
- **One `<h1>` per page**; run `php artisan teme:theme:check egberipiribo`.
- **No inline script in a view.** Alpine is the CSP build: an `x-*` attribute names a property or a method, nothing
  more (no `!`, ternaries, object literals or assignments). Put the logic in `assets/js/skins/fable.js`.
- **Every printed word goes through `__()`** with the English sentence as its key.
- **One place per choice.** A choice an administrator may one day make is read once at the top of its template as
  `$themeSettings['key'] ?? <default>`. Declare it in the manifest (through `docs/options.json`) and nothing else
  changes. `node scripts/check-settings.mjs` proves every declared setting is read and every fallback equals the
  manifest default.
- **Ads are regions.** Never ad code, an ad script or a placeholder ad in the theme.
- **Every file has a header** with `@package egberipiribo` and `@since`.
- **Fonts and icons are self-hosted.** Nothing loads from a font or icon host.
- **Version every change that is not pure docs:** bump `teme-theme.json` `version` and add a `CHANGELOG.md` heading
  dated `dd/MM/yyyy`.
- **Known exceptions to "never `App\`" (workshop rule 3), both recorded as Teme gaps:** `tests/Support/settings.php`
  (the public testing kit cannot set a theme setting: FINDINGS 25) and `seed/seed.php` (no public way to create demo
  menus, regions and posts: FINDINGS 13). Do not add a third. Never run `seed/seed.php` on balafama.

## Build, check, test

```bash
npm install
npm run build          # public/build and public/fonts
npm run icons          # after changing scripts/build-icons.mjs
node scripts/build-settings.mjs   # after changing docs/options.json or docs/settings-labels
node scripts/build-options.mjs    # rewrites OPTIONS.md
node scripts/check-settings.mjs
```

From the Teme site root (balafama), with its PostgreSQL test database running:

```bash
php artisan teme:theme:check egberipiribo
php artisan test workshop/themes/egberipiribo/tests --compact
```

The theme must be in the site's `TEME_THEME_PATHS` (`workshop/themes/egberipiribo`); ask the person to add it, do not edit
the site's `.env`.

Accessibility and overflow, in a real browser, against a site that is drawing the theme:

```bash
node scripts/audit.mjs http://balafama.test home=/ article=/some-story/
```

Link the theme's `public` folder into the site once with `php artisan teme:themes:link` (in the Teme site's folder).

## Handbook

Teme's developer handbook: `docs/guides/developers/` in the Teme site, starting at `start/first-theme.md`.
