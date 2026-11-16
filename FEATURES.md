# FEATURES

Inventory of the WordPress theme **Egberipiribo** (Sage 11 / Acorn, Blade, Tailwind 4, Vite, Alpine; version 2.3.0; skins Strata and Threads) and what each feature becomes in this Teme theme. Source read: `README.md`, `AGENTS.md`, `changelog.md`, `docs/superpowers/*`, `app/*.php`, `app/Providers`, `app/View/Composers`, `functions.php`, `theme.json`, `style.css`, `vite.config.js`, `package.json`, `resources/views` (root, layouts, components, partials, sections, forms, `skins/strata`, `skins/threads`), `resources/js`, `resources/css` (structure), and the demo folders `skins/` and `One WP theme/`. Not read: `vendor/`, `node_modules/`, `public/`, `.legacy/`. Teme facts checked against `teme-dev/docs/reference/{theme,theme-manifest,cards,settings,fields}.md`, the handbook (`themes/regions.md`, `themes/languages.md`, `concepts/manifest.md`, `rules/plugins-and-themes.md`, `start/first-theme.md`) and the default theme's manifest.

**The four statuses.** Each row carries exactly one. **same in Teme** means the feature works as it did (same Blade, Tailwind or Alpine idea, only the file moves). **different in Teme** means the feature is kept but Teme does it another way; the last column says what replaces it. **later** means it is not in this release: the Strata and Threads skins, the other demo skins, and anything deferred to a later update; for a template the note says what Teme data it needs and whether it looks supportable. **can't yet** means Teme has no socket or theme feature for it; the last column names which one is missing (a Strata or Threads behaviour that no later release can build until Teme adds that thing is also marked can't yet, so the next release can plan around it). In sections B and C every skin template has a **later** row (with a "supportable" verdict); behaviours inside a template are **later** when buildable, **can't yet** when blocked.

Teme short forms used below: `$page` is the page's view model (LatestView, ArticleView, ListingView, NotFoundView, ErrorView, CommentsPageView, RespondView, ProtectedView); `$site`, `$navigation`, `$themeSettings`, `$isFront`, `$language` are the other view data; slots are the manifest's `navigations`; regions are the manifest's `regions` (cards: callout, button, image, gallery, embed, snippet, audio, video, site-code = Custom HTML, featured-posts); settings are `teme-theme.json` `settings` (types select, boolean, text, color; fieldsets site, latest, content, archive). "This theme" means the Teme port in this folder (already has slots `primary`, `footer`, `network`, `social`, and regions `after-content`, `newsletter`, `home-*`, `ad-*`).

---

## A. The foundation (shared by every skin)

### A1 Bootstrap, packaging and asset pipeline

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Acorn / Sage boot (`Application::configure()->withProviders()->boot()`) and the empty `ThemeServiceProvider` | `functions.php`, `app/Providers/ThemeServiceProvider.php` | **different in Teme** | No PHP boot. Teme reads `teme-theme.json`, then draws Blade views; manifest `composers` stays `[]`. |
| Loads `app/setup.php`, `app/filters.php` (which pulls in `strata-setup.php`, `threads-setup.php`) and the update checker | `functions.php`, `app/setup.php` (last lines) | **different in Teme** | No hook files. Each hook, filter and Customizer row below says what core or the manifest does instead. |
| Theme header (name, version 2.3.0, author Krafty Sprouts Media, MIT, Requires PHP 8.3, WP 6.6, text domain) | `style.css` | **different in Teme** | Manifest keys `code`, `name`, `version`, `description`, `author`, `author_url`, `license`, `requires.teme` (`>=0.8.0-alpha.1 <1.0`). |
| Block-theme `theme.json` (content size 48rem, colour/duotone/gradient palettes off, spacing units, font-size controls off) and the `theme_file_path` filter that swaps in the Vite-generated copy | `theme.json`, `app/setup.php` | **different in Teme** | No block theme. Manifest `layout` {content, wide, full} (this theme: 720 / 1248 / full) and `tokens`; nothing is generated. |
| Screenshot (225-byte placeholder) | `screenshot.png` | **same in Teme** | Manifest `screenshot`; this theme ships a real one. |
| Vite 8 + `laravel-vite-plugin` + `@roots/vite-plugin` (`wordpressPlugin`, `wordpressThemeJson`), `base` `/wp-content/themes/egberipiribo/public/build/`, aliases `@scripts @styles @fonts @images`, one `input` per skin CSS/JS | `vite.config.js` | **different in Teme** | Vite + `laravel-vite-plugin` + `@tailwindcss/vite`, no `@roots` plugin; `base` `/themes/egberipiribo/build/`; build committed to `public/build`; manifest `assets.manifest` and `assets.entries` name the entries. |
| Alpine.js 3.14 started in `app.js` (`window.Alpine = Alpine; Alpine.start()`) | `resources/js/app.js`, `package.json` | **different in Teme** | `@alpinejs/csp` build (the page policy allows no `unsafe-eval`); attributes name properties and methods only; logic lives in `assets/js/theme.js` and `assets/js/skins/*.js`. |
| Skin JS registered on `alpine:init` (`Alpine.store`) before `app.js` runs | `resources/js/skins/*.js`, `layouts/app.blade.php` | **same in Teme** | Same pattern with the CSP build (`Alpine.data`/`Alpine.store` in the bundle). No inline scripts. |
| Tailwind CSS 4 with `@import "tailwindcss"`, narrowed `@source` per skin | `resources/css/app.css`, `skins/*.css` | **same in Teme** | Tailwind 4, imported into core's `teme.theme` cascade layers; one entry stylesheet. |
| Shared `app.css` plus one CSS file per skin, loaded only for the active skin (`active_skin()`) | `resources/css/skins/{strata,threads}.css`, `layouts/app.blade.php` | **different in Teme** | One bundle, `assets/css/theme.css`, imports `skins/<skin>.css`; skins scoped by `[data-skin]` on the layout wrapper (see FINDINGS 6). A site downloads every skin's CSS unless a later update splits the build. |
| Post-content baseline (list markers, heading sizes, block spacing, tables, quotes) scoped to `.entry-content` and `.editor-styles-wrapper` | `resources/css/content-base.css` | **different in Teme** | Core draws `$page->bodyHtml`; the theme styles that wrapper in its own prose CSS (Fable ports its own). |
| Self-hosted fonts: Fraunces, Manrope (Strata); Libre Baskerville, Space Grotesk (Threads) with `@font-face` registry | `resources/fonts/*`, `resources/css/fonts.css` | **different in Teme** | Manifest `fonts` (file paths under `public/`, at most one preloaded) plus hand-written `@font-face`; Fable ships Bricolage Grotesque and Literata. Strata and Threads fonts return with those skins. |
| Block-editor styles: `editor.css`, `skins/*-editor.css`, `editor.js`, `block_editor_settings_all` and `admin_head` Vite injection (widgets screen excluded) | `app/setup.php`, `resources/css/editor.css`, `resources/css/skins/*-editor.css`, `resources/js/editor.js` | **can't yet** | The editor is core's Tiptap; the manifest has no editor-styles socket, so a theme cannot preview its typography in the editor. |
| `{{ }}` escape discipline; `{!! !!}` only for already-safe HTML | all views, `AGENTS.md` §3 | **same in Teme** | Blade escaping; the handbook asks the same (`rules/plugins-and-themes.md`). |
| Blade templating, `@include`, `@extends`, `@yield`, `@php` | all views | **same in Teme** | Templates stay Blade. |
| Theme update checker: `yahnis-elsts/plugin-update-checker`, constants `EGBERIPIRIBO_THEME_UPDATE_GITHUB_REPO`, `_BRANCH`, `_TOKEN`, `_USE_RELEASE_ASSETS`, `_RELEASE_ASSET_REGEX` | `app/theme-update-checker.php`, `README.md` | **different in Teme** | Not needed: Teme's installer updates themes (Composer package `krafty-sprouts/egberipiribo`). |
| GitHub release workflow builds `egberipiribo-X.Y.Z.zip` with `vendor/` and `public/build` on `v*` tags | `.github/workflows/release.yml` | **different in Teme** | Not needed; the package carries committed `public/build`. |
| Composer runtime deps `roots/acorn`, `plugin-update-checker`; dev `laravel/pint`; suggests `sage-directives` | `composer.json` | **different in Teme** | No PHP runtime dependencies in a Teme theme. |
| Translations: `__()` with text domains `egberipiribo` and `sage`; `npm run translate*` to `resources/lang/sage.pot` | views, `app/*.php`, `package.json` | **different in Teme** | `__()` with the English sentence as key; translations in the theme's `lang/<locale>.json`. One domain, no `.pot` step. |
| Inline `<script>` carrying data to JS (`window.strataData` via `@js`) | `skins/strata/header.blade.php` | **can't yet** | `teme:theme:check` refuses inline scripts; there is no client-side JSON data socket for a theme. |

**Totals (A1):** same in Teme 5 · different in Teme 13 · later 0 · can't yet 2 (20 rows)

### A2 Templates (WordPress template hierarchy)

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Front page: lead, columns, also-in (Threads) or ticker, hero, grid (Strata); blog index | `front-page.blade.php` to `skins/{skin}/home`, `index.blade.php` | **different in Teme** | `templates/latest.blade.php` (kind `latest`, `$page` LatestView): `lead` (newest pinned, else newest), `featured` (up to 3 more pinned), `feed` (ListingView with pagination); `$isFront` is true on the home page and its numbered pages. Settings › Addresses chooses latest posts or a page. |
| Index fallback: page header, "no results" alert + `get_search_form()`, Loop over `partials.content-{type}`, posts navigation, sidebar | `index.blade.php` | **different in Teme** | `templates/index.blade.php`, the floor for every kind but `error`; no Loop, the template draws `$page`. |
| Single post | `single.blade.php` dispatcher to `skins/{skin}/single` | **different in Teme** | `templates/single.blade.php` (also `single-{type}`, `single-{type}-{template}`); `$page` ArticleView. |
| Static page | `page.blade.php` to `skins/{skin}/page` | **different in Teme** | `templates/single-page.blade.php`. |
| Page template "Custom Template" (page header + content, no skin) | `template-custom.blade.php` | **different in Teme** | Manifest `templates.page` gains a slug (the default theme has `full-width`) with `single-page-<slug>.blade.php`; editors pick it in the `template` field. |
| Page template "Subscribe Page" (skin dispatch to `skins/{skin}/subscribe`) | `template-subscribe.blade.php` | **different in Teme** | Manifest `templates.page.subscribe` + `single-page-subscribe.blade.php`. Only the layout carries over; see the newsletter rows for the form. |
| Archives: category, tag, date | `archive.blade.php` | **different in Teme** | `templates/archive.blade.php` (type), `taxonomy-category`, `taxonomy-category-{term}`, `date`; all ListingView and all fall back to `archive`. |
| Author archive | `author.blade.php` | **different in Teme** | `templates/author.blade.php` (falls to `archive`); `$page->subject` has `title`, `description`, `image`. |
| Search results | `search.blade.php` | **different in Teme** | `templates/search.blade.php`; `$page->search` (query, results with snippets, total). |
| 404 | `404.blade.php` | **different in Teme** | `templates/notfound.blade.php` (NotFoundView `path`) plus `error.blade.php` / `error-404` for HTTP errors. |
| Page kinds WordPress never had: later comment pages, reply page, password page, 429 | none | **different in Teme** | New templates `comments`, `respond`, `protected`, `error-429`; if the theme omits one, core's own page draws it. |
| Post page ("Latest Posts" title or `page_for_posts` title in the page header) | `app/View/Composers/Post.php` | **different in Teme** | Settings › Addresses `posts_page_id`; `latest` kind; heading from `$site` or the page. |
| Custom post types, taxonomies, `partials.content-{post_type}` fallback | `index.blade.php` | **different in Teme** | The theme registers none (as before). Types come from the site; a file named `single-{type}` / `archive-{type}` draws one, and `single`/`archive` draw the rest. A theme cannot declare types. |
| Microformats classes (`h-entry`, `p-name`, `e-content`, `dt-published`, `p-author h-card`) | `partials/content-single`, `entry-meta` | **same in Teme** | Plain markup in the theme's own views; core also writes JSON-LD. |
| Thin dispatchers: root template includes `skins.{active_skin()}.{page}` (AGENTS.md rule 3) | `single/archive/search/404/author/page.blade.php` | **same in Teme** | `templates/*.blade.php` are one-line `@include('theme::skins.'.($themeSettings['skin'] ?? 'fable').'.<page>')` (DECISIONS 1). |

**Totals (A2):** same in Teme 2 · different in Teme 13 · later 0 · can't yet 0 (15 rows)

### A3 Layout, head and body shell

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| `<html @language_attributes>`, charset, viewport | `layouts/app.blade.php` | **different in Teme** | `<x-teme::document>` writes `lang`, `dir`, colour scheme and the head; a theme never writes `lang` or `dir`. Use logical CSS properties. |
| `do_action('get_header')`, `wp_head()` | `layouts/app.blade.php` | **different in Teme** | Core writes title, description, canonical, robots, hreflang, social cards, JSON-LD, feed and sitemap links, tokens and assets inside the document component. |
| `wp_body_open()`, `do_action('get_footer')`, `wp_footer()` | `layouts/app.blade.php` | **different in Teme** | Settings › Site code: head, start of body, end of body, written by core; hashes and origins go into the page policy. |
| `@vite([app.css, skins/{skin}.css, skins/{skin}.js, app.js])` | `layouts/app.blade.php` | **different in Teme** | Manifest `assets.entries`; the document component writes the tags. |
| Skip link (`sr-only focus:not-sr-only`) to `#main` | `layouts/app.blade.php` | **different in Teme** | Core's shell writes the skip link and its style. |
| One `<main id="main" class="main">` around `@yield('content')`; skins must not nest another `<main>` | `layouts/app.blade.php`, `AGENTS.md` §5 | **same in Teme** | `layouts/base.blade.php`, `<main id="main">` around `@yield('main')`; one `h1` per page. |
| Optional `<aside class="sidebar">` from `@yield('sidebar')` | `layouts/app.blade.php`, `index.blade.php` | **different in Teme** | No sidebar section; the template places a region. |
| `body_class()` plus the active skin key (`strata`, `threads`) so CSS can scope itself | `app/setup.php` | **different in Teme** | `<body>` is core's; the layout wrapper carries `data-skin="<skin>"`. |
| Threads state on `<body>`: `x-data` with `mobileMenuOpen`, `searchOpen`, drawer touch X/Y, `@keydown.escape.window`, `:class` scroll lock | `layouts/app.blade.php` | **different in Teme** | Core owns `<body>`; state moves to an Alpine component on a theme wrapper, written as methods in the skin script (no inline expressions in the CSP build). |
| Skin colours and fonts as `:root` custom properties in `<style id="egberipiribo-skin-vars">` (`--color-primary/accent/link/text/muted/background`, `--font-display/body`, plus `body` background, colour, font) | `app/setup.php` (`wp_head`) | **different in Teme** | Manifest `tokens` (only base, contrast, accent, on-accent, muted, rule, font-body, font-heading, font-mono, step-*, space-*), written by core with `tokens_dark`; other colours derived in CSS with `color-mix()`. Per-skin named tokens: see A6. |
| Skin-specific header and footer included from the layout | `layouts/app.blade.php` | **same in Teme** | `layouts/base.blade.php` includes the skin's `site-header` / `site-footer` partials. |

**Totals (A3):** same in Teme 2 · different in Teme 9 · later 0 · can't yet 0 (11 rows)

### A4 Shared components and partials

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Ad slot: placement `leaderboard`, `rectangle`, `in_article`; enabled flag; ad code; placeholder box "Advertisement . {label}" when enabled with no code; `print_embed_html()` | `components/ad-slot.blade.php` | **different in Teme** | Regions `ad-*`, each holding a `site-code` (Custom HTML) card; an empty region draws nothing, so no placeholder box. This theme has six ad regions (leaderboard, in-feed, rectangle, in-article x2, half page). |
| Alert box (`success/caution/warning`) | `components/alert.blade.php` | **different in Teme** | A plain theme partial; used for "no results" and "comments closed" (core's comment view words the closed state). |
| Article card (thumb, title, byline, excerpt with "Continued" link) | `components/article-card.blade.php` | **different in Teme** | `partials/card.blade.php` over `ListingItem` (title, url, standfirst, kicker, authors, publishedText, image). |
| Author byline "By {author} . date" | `components/author-byline.blade.php` | **different in Teme** | `$item->authors` (name, url) and `publishedText`; `published` for `<time datetime>`. |
| Breadcrumbs (Home / archive title / post title) | `components/breadcrumbs.blade.php` | **different in Teme** | Manifest `breadcrumbs: true`; `$page->breadcrumbs` is a list of Crumb (name, url) built by core. |
| Pagination (`get_the_posts_pagination`) | `components/pagination.blade.php` | **different in Teme** | `$page->pagination`: `previous`, `next`, `current`, `total`, `pages()` (up to seven links with gaps). |
| Search form (GET `s` to home) | `forms/search.blade.php` | **same in Teme** | Plain GET form posting `s` to the site address. |
| Page header: per-context title ("Latest Posts", archive title, "Search Results for %s", "Not Found") | `partials/page-header.blade.php`, `Composers/Post.php` | **different in Teme** | `$page->subject->title`, `$page->search->query`; the theme words the rest through `__()`. |
| Loop partials: `content`, `content-search`, `content-single`, `content-page` | `partials/*.blade.php` | **different in Teme** | Per-kind templates over view models; no `the_post()` / Loop. |
| Entry meta (`<time class="dt-published">`, author link `p-author h-card`) | `partials/entry-meta.blade.php` | **different in Teme** | `$page->published`, `publishedText`, `authors`. |
| Multi-page posts: `wp_link_pages()` "Pages:" nav (also on Threads page) | `Composers/Post.php`, `partials/content-page`, `skins/threads/page` | **can't yet** | ArticleView has no in-article page-break data. |
| Primary sidebar `dynamic_sidebar('sidebar-primary')` | `sections/sidebar.blade.php` | **different in Teme** | A region placed by the template. |

**Totals (A4):** same in Teme 1 · different in Teme 10 · later 0 · can't yet 1 (12 rows)

### A5 View composers, helpers and filters (`app/`)

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Composer `App` (every view): `siteName()` | `View/Composers/App.php` | **different in Teme** | `$site->name`; `$site->tagline`, `$site->url`, `$site->year`. |
| Composer `Post`: `title()`, `pagination()` | `View/Composers/Post.php` | **different in Teme** | `$page->title`; pagination of a post's pages is **can't yet** (see A4). |
| Composer `Comments`: `title`, `responses`, `previous`, `next`, `paginated`, `closed` | `View/Composers/Comments.php` | **different in Teme** | `$page->comments` (CommentsView): `count`, `open`, `items`, `pagination`, `form`. |
| Composers `Strata`, `Threads` (queries: 24 latest, related, category counts, per-category columns, random 404 picks, author counts) | `View/Composers/Strata.php`, `Threads.php` | **different in Teme** | Manifest `composers` may only ask for `Teme\Contracts` interfaces; a theme cannot query posts, so these become view-model fields and regions. Row by row in B and C. |
| `theme_option($key, $default)` over `get_theme_mod` | `app/setup.php` | **different in Teme** | `$themeSettings['key'] ?? default`, read once at the top of a template. |
| `skins()` / `active_skin()` (fallback `strata`) | `app/setup.php`, `Skins.php` | **different in Teme** | `$themeSettings['skin'] ?? 'fable'`; see A6. |
| `skin_menu_locations()` (menu locations per skin) | `app/setup.php` | **different in Teme** | Manifest `navigations` is theme-wide; every skin's slots show in the admin once declared. |
| `yoast_breadcrumbs_active()` (use Yoast's trail and BreadcrumbList schema) | `app/setup.php` | **different in Teme** | No Yoast; core's trail via `$page->breadcrumbs` and its JSON-LD graph. |
| `post_display_date()`: "Updated {date}" when modified more than 7 days after publish | `app/setup.php` | **different in Teme** | `$page->updatedText` (set only when the revision is at least a day newer) else `publishedText`; the threshold is core's (1 day), not 7. |
| `reading_time()` ("N min", 200 words per minute) on cards, rows and posts | `app/filters.php` | **different in Teme** | `$page->readingMinutes` on an article (238 wpm). On listing items it is **can't yet** (ListingItem has no reading time). |
| `excerpt_to_first_period()` (card excerpt stops at first full stop or N words) | `app/filters.php` | **different in Teme** | `standfirst` is only an excerpt someone wrote and is never generated; the template may cut at the first full stop. Posts with no written excerpt show none. |
| `excerpt_more` filter: " ... Continued" link; plain " ..." in feeds | `app/filters.php` | **different in Teme** | Not needed; no generated excerpts, core builds feeds. |
| `decode_entities` on `the_title`, `get_the_excerpt`, nav item titles, `term_name` (avoid double-encoding) | `app/filters.php` | **different in Teme** | Not needed; Teme hands plain text and Blade escapes once. |
| Embed sanitising: `allowed_html_for_embeds()`, `sanitize_embed_html()`, `print_embed_html()` (skips `do_shortcode` when a script is present) | `app/filters.php` | **different in Teme** | The `site-code` card: administrators write the HTML in Settings › Site code; core handles hashes and origins. |
| `egberipiribo_nav_menu_group_by_parent()` | `app/filters.php` | **different in Teme** | `$navigation[slot]` is already a tree (`children`). |
| `egberipiribo_footer_nav_column()` (heading = the menu's name, items, tree, aria label) | `app/filters.php` | **different in Teme** | One slot per column; heading is the slot's label or a `heading` item in the menu. The assigned menu's own name is not in `$navigation`. |
| `egberipiribo_footer_copyright_text()` tokens `[y] [year] [site] {{year}} {{site}}` | `app/filters.php` | **different in Teme** | `$site->year`, `$site->name`; a `text` theme setting for the line. A `text` setting is escaped, so links in it are **can't yet** (see A6). |
| `egberipiribo_social_icon_html_for_menu_classes()` (menu item class `egberi-social-{slug}` and `egberi-social-icon-only` pick an icon) | `app/filters.php` | **different in Teme** | Navigation items have no CSS classes; the theme picks the icon from the item's URL host and draws inline SVG (`views/icons`). |
| Social platform registry: 9 platforms, labels, SVG paths, `social_links()` | `app/setup.php` | **different in Teme** | `social` navigation slot plus theme icon views; the registry is a Blade partial. |
| Author profile helpers: `egberipiribo_author_social_networks/links/profile_fields/social_profiles` | `app/threads-setup.php` | **can't yet** | `Person` has only name, url, bio; no per-person fields or socials (ACF-like fields exist for content items only). |
| `sanitize_checkbox`, `sanitize_home_column_category_term_id`, `sanitize_home_also_in_initial`, `sanitize_home_also_in_per_load` | `app/setup.php`, `threads-setup.php` | **different in Teme** | Teme validates declared settings by type; number and term settings **can't yet** (A6). |
| Footer copyright token default ("(c) {{year}} {{site}}. Published by Krafty Sprouts Media, LLC...") | `app/filters.php` | **different in Teme** | Fixed default markup in the footer partial. |

**Totals (A5):** same in Teme 0 · different in Teme 21 · later 0 · can't yet 1 (22 rows)

### A6 Customizer, theme options and skin switching

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| "Active Design" select `egberipiribo_skin` (default `strata`; choices from `array_keys(Skins.php)`; sanitize_key) | `app/setup.php` | **different in Teme** | A `select` theme setting `skin` (fieldset `site`), declared when a second skin lands; this release has none and fixes Fable (DECISIONS 4). Dispatchers already read `$themeSettings['skin']`. |
| Skin registry `Skins.php`: label, six colours (primary, accent, link, text, muted, background), two fonts | `app/Skins.php` | **different in Teme** | Skin CSS under `[data-skin]`; the manifest carries only the default skin's tokens. |
| Per-skin token sets (named colours, a second accent, per-skin fonts as tokens) | `app/Skins.php` | **can't yet** | Tokens are a fixed set; a skin cannot ship its own named tokens or switch tokens from a setting (FINDINGS 1 and 6). |
| Site-wide accent override (WordPress had none; AGENTS.md forbids per-site colours) | `AGENTS.md` §2 | **different in Teme** | New: Settings › Design `design.accent` recolours everything that reads `var(--teme-accent)`. |
| Skin-gated Customizer sections (`active_callback`) | `app/*-setup.php` | **different in Teme** | `visible_when` on a setting may name earlier settings, so skin-specific settings can show only when `skin` equals that skin. |
| Social Links section: nine URL settings `egberipiribo_social_{facebook,instagram,x,threads,youtube,tiktok,flipboard,linkedin,pinterest}` | `app/setup.php` | **different in Teme** | Navigation slot `social` (links in a menu); no URL setting type. |
| Newsletter section: `egberipiribo_newsletter_enabled` (checkbox, off), `egberipiribo_newsletter_embed` (textarea) | `app/setup.php` | **different in Teme** | Region `newsletter` holding a `site-code` card; empty region hides the band, so "enabled" is "has a card". |
| Advertising section: `egberipiribo_ad_{leaderboard,rectangle,in_article}_{enabled,code}` (6 settings) | `app/setup.php` | **different in Teme** | Regions `ad-*` with `site-code` cards. |
| Number settings: single sidebar width (240 to 420), also-in first batch (0 to 50), per load (1 to 20) | `threads-setup.php` | **can't yet** | No `number` setting type; only select, boolean, text, color. Fixed defaults or a `select` of a few choices. |
| Category picker settings: Threads column 1, 2, 3 category ids | `threads-setup.php` | **can't yet** | No term setting type; a `featured-posts` card in a region chooses a category instead (`termId`). |
| Per-category checkboxes (Strata rail sections), generated from live categories | `strata-setup.php` | **can't yet** | A theme cannot declare settings from site data; the menu tree curates sections instead. |
| URL setting (subscribe URL) and repeaters (five publication label+URL pairs) | `threads-setup.php` | **can't yet** | No URL, repeater or image setting; a navigation slot carries lists of links. |
| Text and textarea settings (taglines, manifesto, titles, labels) | `*-setup.php` | **different in Teme** | `text` settings (single line); multi-line text is one line or a callout. |
| HTML-allowed text (`wp_kses_post` on copyright lines) | `*-setup.php` | **can't yet** | `text` settings are escaped; a link inside needs a region card or fixed markup. |
| Boolean toggles (checkbox, `sanitize_checkbox`) | `*-setup.php` | **same in Teme** | `boolean` setting with a default. |
| Settings storage in `theme_mod`, read with defaults | `app/setup.php` | **different in Teme** | Stored as `theme.egberipiribo.*` settings, handed to every template as `$themeSettings`. |
| Customizer live preview / selective refresh | `app/setup.php` | **can't yet** | No live preview of theme settings is documented for themes. |
| Customizer section order and priorities | `app/setup.php` | **different in Teme** | Settings show in declaration order inside the fieldsets site, latest, content, archive. |

**Totals (A6):** same in Teme 1 · different in Teme 10 · later 0 · can't yet 7 (18 rows)

### A7 Menus, sidebars and widgets, images, content blocks

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Nav menu locations registered per skin (`register_nav_menus(skin_menu_locations())`): Strata 2, Threads 7 | `app/setup.php` | **different in Teme** | Manifest `navigations` (this theme: `primary`, `footer`, `network`, `social`); Settings assigns a menu to each slot; `<x-teme::navigation slot label>` draws one. |
| Menu tree with any depth, parents and children via `wp_get_nav_menu_items` | `skins/threads/header`, `partials/*nav*` | **different in Teme** | `$navigation[slot]` items: label, url, current, opensNewTab, heading, children. |
| Menu item CSS classes (`egberi-social-*`) | `app/filters.php` | **can't yet** | Navigation items carry no classes; icons come from the URL host instead (see A5). |
| Menu item opens in a new tab (link target) | menu admin | **same in Teme** | `opensNewTab` on each item (a `heading` item type is new). |
| Sidebars `sidebar-primary` (rendered only by `index.blade.php`) and `sidebar-footer` (registered, rendered nowhere) | `app/setup.php` | **different in Teme** | Regions; `sidebar-footer` was dead code. |
| Sidebars used by Threads but never registered in this version: `sidebar-also-in`, `sidebar-single`, `sidebar-archive`, `sidebar-page`, `sidebar-author` (always inactive, so single falls back to a rectangle ad) | `skins/threads/*.blade.php` | **later** | Return with Threads as regions (`home-sidebar`, `single-sidebar`, ...); noted so the port does not invent widgets the site never had. |
| Widget: recent posts | sidebars | **different in Teme** | `featured-posts` card (latest of a type, optionally one category or tag). |
| Widgets: categories list, tag cloud, archives, popular posts | sidebars | **can't yet** | No taxonomy lists or term counts for templates beyond the navigation tree; no view counts. |
| Widgets: text / custom HTML / image / gallery / video / audio | sidebars | **different in Teme** | Region cards `callout`, `site-code`, `image`, `gallery`, `video`, `audio`, `embed`, `button`, `snippet` (cards allowed per region in the manifest). |
| Widget: search | sidebars | **same in Teme** | Plain GET form partial. |
| Image sizes: theme registers none; uses `thumbnail`, `medium`, `medium_large`, `large`; avatar sizes 32 to 200 | `app/setup.php`, views | **different in Teme** | Manifest `images` (context to sizes value) and `<x-teme::image :id context>`; this theme has cover, hero, content, wide, card, thumb, square, tile, avatar. |
| Featured image (`has_post_thumbnail`, `get_the_post_thumbnail`) | single, cards | **different in Teme** | `$page->lead` / `leadIsFeatured`; `$item->image` (ImageRef). |
| Shortcodes: none registered; `do_shortcode()` runs on embed HTML | `app/filters.php` | **different in Teme** | No shortcodes; old content comes through the importer (`imported` card); embeds are the `embed` card. |
| Blocks and patterns: none registered, `core-block-patterns` removed, no `patterns/` folder; `.wp-block-list` and core block CSS loaded in full | `app/setup.php`, `content-base.css` | **same in Teme** | No patterns; content is Tiptap JSON with cards, styled by core's card CSS (`cards.replaces` lets a theme replace a card's stylesheet). |
| Block templates (FSE) removed; HTML5 markup, `responsive-embeds`, selective-refresh support flags | `app/setup.php` | **different in Teme** | Not needed. |
| Post formats, page attributes, `add_editor_style` | none | **same in Teme** | Not used. |
| Sticky posts (`ignore_sticky_posts` in Threads queries) | `Composers/Threads.php` | **different in Teme** | Editors pin with the Pin field; core puts pinned posts in `lead` and `featured`. |
| De-duplicating the lead, columns and feed on the front page | `Composers/Threads.php` | **different in Teme** | LatestView never repeats a post between `lead`, `featured` and `feed`; `featured-posts` cards in regions are separate and can repeat (see C). |

**Totals (A7):** same in Teme 4 · different in Teme 11 · later 1 · can't yet 2 (18 rows)

### A8 SEO, head output, language, feeds

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Document title (`add_theme_support('title-tag')`) | `app/setup.php` | **different in Teme** | Core writes it from Settings › Search and social patterns; manifest `title_separator` (this theme: `·`). |
| Meta description, canonical, robots, noindex, snippet controls | Yoast / WP | **different in Teme** | Built-in fields `meta_description`, `canonical`, `noindex`, `nosnippet`, `max_snippet`, `noarchive`; read in the editor, written by core. |
| Open Graph and social cards | Yoast / WP | **different in Teme** | Fields `social_title`, `social_description`, `social_image`; core writes the cards. |
| JSON-LD (Yoast graph, breadcrumbs) | Yoast | **different in Teme** | Core writes the page's JSON-LD graph (publisher, article type fields). |
| Sitemap, robots.txt, feeds (RSS), `llms.txt`, hreflang | WP / Yoast | **different in Teme** | Core: `seo.feed_items`, `seo.feed_content`, robots rules, sitemap, hreflang. |
| RSS link in the Strata rail (`get_bloginfo('rss2_url')`) | `skins/strata/social-icons` | **can't yet** | `$site` has no feed address; a theme can only link a fixed path core serves. |
| Site name and tagline (`get_bloginfo`) | header, footer | **different in Teme** | `$site->name`, `$site->tagline`. |
| Current date line ("Monday, 16 November 2026": `wp_date` / `now()`) | Strata home, Threads masthead | **different in Teme** | Not in view data; pages may be stored and shared, so a request-time date goes stale. Draw a fixed word or leave it out (Fable's drawing has a date). |
| Language and RTL | `language_attributes()` | **different in Teme** | `$language`, `$direction`, `$alternates`, `<x-teme::languages>` and menu language switcher; use logical CSS. |
| Translation of theme words | `__()` | **different in Teme** | `lang/<locale>.json`, English as key. |
| Yoast breadcrumbs adoption (own line fallback; Customizer visibility switch) | Strata single | **different in Teme** | Manifest `breadcrumbs`, `$page->breadcrumbs`; the visibility switch is a `boolean` setting. |
| Speculation / prefetch | none | **different in Teme** | Manifest `speculation` (core writes rules; a theme may only opt out). |

**Totals (A8):** same in Teme 0 · different in Teme 11 · later 0 · can't yet 1 (12 rows)

### A9 Comments, search, pagination, author data

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| Comment list `wp_list_comments` (ol, short pings) | `partials/comments`, `Composers/Comments` | **different in Teme** | `$page->comments->items` (CommentView with `replies`, `byAuthor`, `bodyHtml`, `dateText`, `iso`); ping kinds `pingback`, `trackback`. |
| Comment form `comment_form()` with custom fields (name, email, website, cookies consent), intro text, cookie checkbox toggle, `.thread-comment-form` CSS | `skins/threads/comments` | **different in Teme** | `<x-teme::comment-form :form="$page->comments->form">`; core words and fields it (website optional by `discussion.ask_website`); the theme styles it. Intro-text and cookie-checkbox settings are core's. |
| Comment avatars (`get_avatar_url`) | `skins/strata/comments` | **different in Teme** | `CommentView` carries `initials` and `hue` (0 to 7), no image. |
| Comment time "x ago" (`human_time_diff`) | `skins/strata/comments` | **different in Teme** | `dateText` and `iso`; relative times would be computed at request time, which pages stored and shared make unreliable. |
| Comment pagination (older / newer) | `Composers/Comments` | **different in Teme** | `$page->comments->pagination`; later pages are `comments` kind pages. |
| Comments closed notice; password-protected posts | `partials/comments` | **different in Teme** | `comments->open`; protected posts draw `protected` kind with `<x-teme::password-form>`. |
| Threaded replies (Strata: one level by hand) | `skins/strata/comments` | **different in Teme** | Core threads to `discussion.thread_depth`; `replies` nest; the theme only draws them. |
| Search form field, results page, "found N" | `skins/*/search` | **different in Teme** | `$page->search`: `query`, `results` (item + `snippetHtml`), `total` (capped), `empty`. |
| Search live overlay filtering 24 stories client-side (Strata) | `skins/strata/header`, `strata.js` | **can't yet** | No client-side JSON data or search endpoint for a theme; a plain GET form posts to `/?s=`. |
| Pagination: numbered (`paginate_links`, `add_args` for `s`), Newer / Older links | `skins/threads/search`, `archive`, `author` | **different in Teme** | `$page->pagination` (`pages()`, `previous`, `next`). |
| Listing total count ("N stories") | `$wp_query->found_posts` | **can't yet** | ListingView has `pagination->total` (pages) but no item total; only a search has `total`. |
| Per-item tags in a listing row (#tag chips) | `skins/threads/archive` | **can't yet** | ListingItem has `kicker` only; tags are on ArticleView, not on listing items. |
| Author avatar (`get_avatar`, `get_avatar_url`) | single, author, Strata/Threads | **can't yet** | `Person` has no image; initials in CSS are the only stand-in. |
| Author post count (`count_user_posts`) | `Composers/Threads.php` | **can't yet** | No item count on a listing. |
| Author profile fields (role, location, topics, X, Instagram, YouTube, Threads, Flipboard, Facebook, LinkedIn, Pinterest URLs) and their user-profile screens | `app/threads-setup.php` | **can't yet** | No per-person fields; `Person` is name, url, bio. |
| Author bio and name | single, author | **same in Teme** | `Person` name and `bio`. |

**Totals (A9):** same in Teme 1 · different in Teme 9 · later 0 · can't yet 6 (16 rows)

### A10 Server features and admin hooks

| Feature | Where in the WordPress theme | Status | Teme: how it is done or what is missing |
|---|---|---|---|
| REST route `egberipiribo/v1/home-also-in` (per_page 1 to 20, `story_start`, `story_start_mobile`, exclude up to 200 ids; returns HTML fragment, `has_more`, next numbers) | `app/HomeAlsoInRest.php` | **can't yet** | A theme cannot register routes or an endpoint, and has no query or client-side JSON socket. |
| Load-more JS (`fetch` the route, `insertAdjacentHTML`, collect exclude ids, loading label) | `resources/js/skins/threads.js` | **can't yet** | Same: no theme endpoint. A paged `feed` with Newer / Older links replaces it. |
| User profile admin screens (`show_user_profile`, `edit_user_profile`, save hooks, `Author Details` table) | `app/threads-setup.php` | **can't yet** | No per-person field registry for a theme. |
| Random posts (`orderby rand`) for the 404 picks | `Composers/Threads.php` | **can't yet** | A theme cannot query posts; a region with a `featured-posts` card (latest) is the stand-in. |
| WordPress queries in templates (`get_posts`, `WP_Query`, `get_categories`) | composers, search modal, 404 | **can't yet** | A theme gets `lead`, `featured`, `feed`, listings, related, and `featured-posts` cards only; no way to query posts or terms. |
| Category list by count (search modal top 8) | `skins/threads/search-modal` | **can't yet** | No term counts or taxonomy lists beyond the navigation tree; a `navigations` slot replaces it. |

**Totals (A10):** same in Teme 0 · different in Teme 0 · later 0 · can't yet 6 (6 rows)

**Section A totals:** same in Teme 16 · different in Teme 107 · later 1 · can't yet 26 (150 rows)

---

## B. Skin STRATA (fixed dark rail, filterable canvas)

Fraunces (display) + Manrope (body); palette panel `#101013`, accent `#7c98f5`, ink `#17171a`, muted `#7a746e`, rule `#e9e7e4`; body class `strata`; menu locations `primary_navigation`, `footer_navigation`. Source: `resources/views/skins/strata/*`, `app/View/Composers/Strata.php`, `app/strata-setup.php`, `resources/css/skins/strata*.css`, `resources/js/skins/strata.js`. Demo: `skins/strata/*.html`. Every template is **later**; the note gives what Teme must supply and whether it looks supportable.

### B1 Templates

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| Header: 260px fixed dark rail (brand, tagline, search button, Sections nav, social icons), mobile sticky bar, search overlay, back-to-top | `skins/strata/header.blade.php` | **later** | Needs `$site`, slots `primary` and `social`, a text setting. Supportable except the live Sections filter, the live search overlay and the inline data script (rows B2). |
| Footer: newsletter band, brand + about, two menu columns, copyright | `skins/strata/footer.blade.php` | **later** | Needs region `newsletter`, slots `primary` and `footer`, two text settings. Supportable (copyright links limited, see B3). |
| Home: ticker, edition header, leaderboard ad, hero cluster, "Long Read" band, filterable grid, rectangle ad | `skins/strata/home.blade.php` | **later** | Needs `$page` LatestView (`lead`, `featured`, `feed`), regions. Partly supportable: hero and secondary from `lead` and `featured`/`feed`; the grid filter by category and the story counts are not (rows B2). |
| Single: progress bar, breadcrumbs, title, standfirst, author row, share buttons, image, body, in-article ad, "Read also", tags, author box, comments, "Keep Exploring" | `skins/strata/single.blade.php` | **later** | Needs ArticleView (`kicker`, `authors`, `readingMinutes`, `updatedText`, `lead`, `bodyHtml`, `tags`, `related`, `comments`, `breadcrumbs`). Supportable; author avatar and socials are blocked (B2). |
| Archive: kicker, title, story count, leaderboard ad, 4-column card grid, pagination | `skins/strata/archive.blade.php` | **later** | Needs ListingView. Supportable except the story count and the cards' read time (B2). |
| Search: kicker, query heading, inline form, result count, 3-column grid with category dot, empty state, pagination | `skins/strata/search.blade.php` | **later** | Needs `$page->search`. Supportable (count from `total`, dot from `kicker`; read time blocked). |
| 404: large 404, copy, search form, back link, "Or keep reading" four latest posts | `skins/strata/404.blade.php` | **later** | Needs `notfound` template plus a region with a `featured-posts` card (latest, count 4). Supportable. |
| Page: breadcrumb (site / title), h1, prose | `skins/strata/page.blade.php` | **later** | Needs ArticleView of a page. Supportable. |
| Author: avatar, "Author" kicker, name, bio, story count, 3-column grid, pagination | `skins/strata/author.blade.php` | **later** | Needs ListingView author, `Person` bio. Supportable without avatar and count (B2). |
| Comments: count, one-level threads, author badge, relative time, custom form | `skins/strata/comments.blade.php` | **later** | Needs `$page->comments` and `<x-teme::comment-form>`. Supportable; avatar is initials, time is `dateText`. |
| Social icons partial (configured platforms plus RSS) | `skins/strata/social-icons.blade.php` | **later** | Needs slot `social`; RSS needs a feed address (B2). Supportable. |
| Subscribe page fallback (title + content) | `skins/strata/subscribe.blade.php` | **later** | A page template `subscribe`. Supportable. |

**Totals (B1):** same in Teme 0 · different in Teme 0 · later 12 · can't yet 0 (12 rows)

### B2 Behaviours and data

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| Rail "Sections": category buttons with live post counts and per-category colour dots; clicking filters the home grid in place (Alpine store `strata`: `active`, `filtered`, `title`, `count`) | `header.blade.php`, `home.blade.php`, `strata.js`, `Composers/Strata.php` | **can't yet** | Needs term counts, client-side JSON data and a no-reload filter. Stand-in: rail links from the `primary` menu to category pages (Fable's approach); counts dropped. |
| Category colour dots (ten fixed colours by list order, grey fallback) | `Composers/Strata.php` | **later** | Supportable in CSS by item position in the `primary` slot, or a fixed colour per kicker position. |
| Grid of 24 latest stories, hero (1), secondary (3), feature (5th), ticker (6) taken from one 24-post query | `Composers/Strata.php` | **can't yet** | A theme cannot query posts or choose how many; it gets `lead`, `featured` and one page of `feed` (`per_page` default 10, `reading.posts_per_page`). Hero and ticker from those are supportable. |
| Top-stories ticker (CSS marquee, doubled list) | `home.blade.php`, `strata.css` | **later** | Supportable from `lead` + `featured` + first `feed` items; no JS. |
| Cover story excerpt and secondary excerpts (generated from content) | `Composers/Strata.php` | **later** | Only a written `standfirst` exists; posts without one show none. |
| "Long Read" feature band (the fifth story) | `home.blade.php` | **later** | A region `home-long-read` with a `featured-posts` card (as this theme does for Fable). Supportable. |
| Read time on cards, hero, archive, search | `Composers/Strata.php`, archive/search | **can't yet** | ListingItem has no reading time (ArticleView only). |
| Story counts ("N stories" in the edition header, archive, author; "N results") | `home/archive/author.blade.php` | **can't yet** | No item total on a listing; a search has `total` (supportable for search only). |
| Edition date line (`wp_date('l, j F Y')`) | `home.blade.php` | **can't yet** | Not in view data and pages are stored and shared; leave out. |
| Search overlay: query typed live, results from the 24 loaded stories | `header.blade.php`, `strata.js` | **can't yet** | Needs client-side JSON data or a search endpoint for a theme; a GET form to `/?s=` is the stand-in. |
| Inline `<script>window.strataData = @js(...)</script>` | `header.blade.php` | **can't yet** | Inline scripts are refused; same JSON socket missing. |
| Mobile header with collapsible menu | `header.blade.php` | **later** | Supportable in Alpine CSP (state in `assets/js/skins/strata.js`). |
| Back-to-top button shown after 24px scroll | `header.blade.php` | **later** | Supportable (scroll handler as a method, not an inline expression). |
| Reading progress bar (3px, scroll-driven) | `single.blade.php` | **later** | Supportable (method in the skin script; the inline `progress` IIFE must move). |
| Copy-link button (`navigator.clipboard`, "Copied!") | `single.blade.php` | **later** | Supportable as a script method; outside hosts none. |
| Share links: X intent, Facebook sharer, mailto | `single.blade.php` | **later** | Links open outside hosts but are plain `href`s, so no `csp` entry. Supportable from `$page->url` and `title`. |
| Breadcrumbs: Yoast trail or own line (site / category / title) with visibility switch | `single.blade.php` | **later** | `$page->breadcrumbs` and manifest `breadcrumbs`. Supportable. |
| Author line: avatar, name, "N min read . {display date}" with "Updated" after 7 days | `single.blade.php` | **later** | `authors`, `readingMinutes`, `updatedText`. Supportable; avatar **can't yet** (see A9). |
| "Read also" callout linking the first related story | `single.blade.php` | **later** | `$page->related[0]` (up to three, core chooses; Strata picked same-category-first, four). Supportable. |
| "Keep Exploring": four related cards (same category first, then newest) | `Composers/Strata.php` | **later** | `$page->related` gives up to 3 with a `reason`; counts differ. Supportable with three. |
| Tags "Filed under" chips | `single.blade.php` | **later** | `$page->tags`. Supportable. |
| Author box: avatar, "Written by", name, bio, social icon links, "More by" | `single.blade.php` | **later** | `Person` name, url, bio; avatar and socials **can't yet** (A9). |
| Comment form fields styled by `comment_form` args (name, email, comment, "Not published.") | `comments.blade.php` | **later** | Core's form; supportable styling only. |
| In-article ad slot after the body | `single.blade.php` | **later** | Region `ad-in-article`. Supportable (ads inside paragraphs are not). |
| Zoom-on-hover images, card hover lift, `.balance` headings, `::selection`, rail scrollbar | `strata.css` | **later** | CSS only. Supportable. |
| `.prose-strata` article body styling | `strata.css`, `strata-editor.css` | **later** | Styles on core's `bodyHtml` wrapper. Supportable; editor copy **can't yet** (A1). |
| Layout offset `lg:ml-[260px]` for every page and footer | all templates | **later** | CSS. Supportable. |
| Strata palette and fonts (Fraunces, Manrope) as a skin | `Skins.php`, `fonts.css` | **later** | Needs per-skin tokens; accent `#7c98f5` differs from Fable's brand colour (A6, can't yet; CSS under `[data-skin]` instead). |

**Totals (B2):** same in Teme 0 · different in Teme 0 · later 21 · can't yet 7 (28 rows)

### B3 Strata options (Customizer)

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| `egberipiribo_strata_rail_tagline` (text; empty uses site tagline, then "Layers worth exploring") | `strata-setup.php`, `header.blade.php` | **later** | `text` setting; `$site->tagline`. Supportable. |
| `egberipiribo_strata_breadcrumbs_enabled` (boolean, on) | `strata-setup.php` | **later** | `boolean` setting (fieldset content). Supportable. |
| `egberipiribo_strata_share_enabled` (boolean, on) | `strata-setup.php` | **later** | `boolean` setting. Supportable. |
| `egberipiribo_strata_tags_enabled` (boolean, on) | `strata-setup.php` | **later** | `boolean` setting. Supportable. |
| `egberipiribo_strata_author_box_enabled` (boolean, on) | `strata-setup.php` | **later** | `boolean` setting. Supportable. |
| `egberipiribo_strata_related_enabled` (boolean, on) | `strata-setup.php` | **later** | `boolean` setting. Supportable. |
| `egberipiribo_strata_rail_{term_id}` (one checkbox per category, on) | `strata-setup.php`, `Composers/Strata.php` | **can't yet** | Settings cannot be generated from site data; curate with the `primary` menu. |
| `egberipiribo_strata_footer_about` (textarea; empty uses tagline) | `strata-setup.php`, `footer.blade.php` | **later** | `text` setting. Supportable (one line). |
| `egberipiribo_strata_footer_copyright` (HTML text with `[year]`, `[site]`) | `strata-setup.php`, `footer.blade.php` | **later** | `text` setting plus `$site->year` and `name`; HTML links **can't yet** (A6). |
| Footer columns named after the assigned menus' names | `footer.blade.php` | **later** | One slot per column with fixed heading or a heading item (A5). Supportable. |
| Rail and footer social icons from the nine platforms plus RSS | `social-icons.blade.php` | **later** | Slot `social`; icon by URL host. Supportable; RSS address **can't yet** (A8). |
| Newsletter band in footer (heading, text, embed; hidden unless enabled) | `footer.blade.php` | **later** | Region `newsletter`, `site-code` card; no theme-posted form. Supportable as a pasted embed. |
| Ads: leaderboard (home, archive), rectangle (home), in-article (single) | views | **later** | Regions `ad-leaderboard`, `ad-rectangle`, `ad-in-article`. Supportable. |

**Totals (B3):** same in Teme 0 · different in Teme 0 · later 12 · can't yet 1 (13 rows)

**Section B totals:** same in Teme 0 · different in Teme 0 · later 45 · can't yet 8 (53 rows)

---

## C. Skin THREADS (centred masthead, numbered issue)

Libre Baskerville (display) + Space Grotesk (body); palette fore `#1a1a1a`, accent `#e84545`, text `#374151`, mid `#6b6b6b`, light `#f0edea`; body class `threads`; seven menu locations. Source: `resources/views/skins/threads/*` (+ `partials/`), `app/View/Composers/Threads.php`, `app/threads-setup.php`, `app/HomeAlsoInRest.php`, `resources/css/skins/threads*.css`, `resources/js/skins/threads.js`. Demo: `skins/threads/thread-*.html` (23 files, including mobile-nav 1 to 5, search drawer and modal, subscribe 1 to 3). Continuity skin: parity with the pre-rebuild live theme.

### C1 Templates

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| Header: leaderboard ad, mobile sticky bar and drawer, desktop masthead (date, search, subscribe, wordmark, tagline, primary nav with dropdowns) | `skins/threads/header.blade.php` + `partials/header-primary-nav-*` | **later** | Needs slots `primary`, `mobile-pages`, `mobile-social`, `drawer-footer`, region `ad-leaderboard`, three settings. Supportable (dropdown depth through `children`). |
| Search modal (full-screen overlay, input, top eight categories by post count) | `skins/threads/search-modal.blade.php` | **later** | Needs a GET form and a slot of topic links. Supportable; the by-count category list **can't yet** (rows C2). |
| Footer: publications strip, wordmark + tagline, three menu columns, copyright | `skins/threads/footer.blade.php`, `partials/footer-menu-items` | **later** | Needs slots `network`, `footer-sections`, `footer-about`, `footer-connect`, settings. Supportable. |
| Home: lead + image + rectangle ad, three category columns, belief band, "Also in This Issue" with load more and sidebar, newsletter band | `skins/threads/home.blade.php` | **later** | Needs LatestView and regions. Partly supportable: columns via three `featured-posts` regions, load more not (rows C2). |
| Home rows partial (also used by REST) | `partials/home-also-in-rows.blade.php` | **later** | Needs `feed` items. Supportable as a list; the REST fragment use **can't yet** (A10). |
| Single: tag chip, h1, standfirst, author row, 16:9 image, body, tags, author bio box with links, comments, sidebar | `skins/threads/single.blade.php` | **later** | Needs ArticleView. Supportable; author links and role blocked (A9). |
| Archive: kicker, title, description, count, featured first story, ruled rows with tags, sidebar, Newer/Older | `skins/threads/archive.blade.php` | **later** | Needs ListingView, `subject->description`. Supportable except count, read time and per-row tags (A9). |
| Search: wash header with italic query, found count, "search again" form, ruled result rows, numbered pagination, sidebar | `skins/threads/search.blade.php`, `partials/content-search.blade.php` | **later** | Needs `$page->search`. Supportable (snippets instead of 40-word excerpts). |
| 404: "Error 404" copy, search form, "From the archive" three random picks | `skins/threads/404.blade.php` | **later** | Needs `notfound` template + region with `featured-posts`. Supportable without randomness. |
| Page: centred title, prose, optional sidebar, multipage nav | `skins/threads/page.blade.php` | **later** | Needs ArticleView of a page. Supportable; multipage nav **can't yet** (A4). |
| Author: role label, avatar, name, bio, link chips, profile box (count, topics, location), ruled article rows, Newer/Older, sidebar | `skins/threads/author.blade.php` | **later** | Needs ListingView author. Name and bio supportable; the rest **can't yet** (A9). |
| Comments: title "N responses to ...", list, pager, closed alert, form with intro and cookie checkbox | `skins/threads/comments.blade.php` | **later** | Needs `$page->comments`, `<x-teme::comment-form>`. Supportable; intro text and cookie checkbox are core's. |
| Subscribe page template ("A Letter from the Editor", body, "Join the Sunday Edition" band with embed or placeholder form) | `skins/threads/subscribe.blade.php`, `template-subscribe.blade.php` | **later** | A page template plus region `newsletter`. Supportable as pasted embed; the placeholder POST form (`action="#"`) **can't yet** (no form socket). |

**Totals (C1):** same in Teme 0 · different in Teme 0 · later 13 · can't yet 0 (13 rows)

### C2 Behaviours and data

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| Mobile drawer: overlay, slide-in, swipe left to close (touch handlers), Escape, body scroll lock, close on link tap | `header.blade.php`, `layouts/app.blade.php` | **later** | Supportable as an Alpine CSP component with methods (the touch maths moves into JS). |
| Search modal open from mobile and desktop buttons, focus the input via `$refs`, close with Escape | `header.blade.php`, `search-modal.blade.php` | **later** | Supportable (CSP methods). |
| Top eight categories by post count in the search modal (`get_categories orderby count`) | `search-modal.blade.php` | **can't yet** | Needs term counts and taxonomy lists; stand-in is an editor-built slot of topic links. |
| Desktop primary nav with hover and focus-within dropdowns, any depth, `menubar` roles | `header-primary-nav-desktop*.blade.php` | **later** | Supportable from `$navigation` `children`. |
| Mobile nav with indented nested items | `header-primary-nav-mobile.blade.php` | **later** | Supportable. |
| Drawer footer menu "About the publication"; falls back to the subscribe link | `header.blade.php` | **later** | Slot `drawer-footer`. Supportable. |
| Date line `now()->format('l, d F Y')` | `header.blade.php` | **can't yet** | Not in view data; request-time dates go stale on stored pages. |
| Subscribe button: enabled, label ("Subscribe"), URL ("#") | `Composers/Threads.php`, `header.blade.php` | **later** | `boolean` + `text` settings; a URL is a `text` setting (no URL type). Supportable as plain `href`. |
| Publications strip: title, new-tab flag, five label/URL pairs, `/` separators, hover underline | `footer.blade.php`, `Composers/Threads.php` | **later** | Slot `network` (items carry `opensNewTab`); title as text setting. Supportable. |
| Footer columns Sections, About, Connect with headings from menu names; social icons by menu class, icon-only | `footer.blade.php`, `footer-menu-items.blade.php` | **later** | Three slots; heading from slot label; icons from URL host (A5). Supportable. |
| Footer wordmark tagline (default "Issue 142 . Published weekly since 2019.") | `footer.blade.php` | **later** | `text` setting. Supportable. |
| Footer copyright with HTML link and tokens | `footer.blade.php` | **later** | `text` setting + `$site->year`; link **can't yet** (A6). |
| Lead story: big "1" numeral, category tag, read time, h1, first-sentence excerpt, author avatar + name + date, image, rectangle ad (desktop only) | `home.blade.php`, `Composers/Threads.php` | **later** | `$page->lead`. Supportable; read time and avatar blocked (A9); excerpt only if written. |
| Three-column feed, each column 4 stories from a chosen category (else a mixed, deduped column with default labels) | `home.blade.php`, `Composers/Threads.php` | **later** | Three regions `home-col-1` to `3`, each a `featured-posts` card with a category and count 4. Supportable; no site-wide dedupe (next row). |
| "Avoid duplicate posts" across lead, columns and also-in | `Composers/Threads.php`, `egberipiribo_home_dedupe_posts` | **can't yet** | Region cards cannot exclude posts shown elsewhere; no theme-side query or exclude list. |
| Story numbering continues across the columns and also-in (02 to 13, then 14+), with a separate mobile counter | `home.blade.php`, `home-also-in-rows.blade.php` | **later** | CSS counters across sibling lists. Supportable. |
| Belief band: manifesto kicker + text, toggles, replaced by the in-article ad when that ad is on | `home.blade.php`, `Composers/Threads.php` | **later** | Settings text x2, boolean x2; region `ad-in-article`. Supportable. |
| "Also in This Issue": heading, initial batch (10, 0 to 50), numbered rows with thumbnails, load-more button (AJAX, per-load 3, label, loading state) | `home.blade.php`, `HomeAlsoInRest.php`, `threads.js` | **can't yet** | Needs a theme endpoint plus client-side JSON data and a number setting. Stand-in: the paged `$page->feed` with Newer / Older links. |
| Also-in right sidebar (toggle, width 240 to 420, sticky last widget) | `home.blade.php`, `threads.css` | **later** | Region `home-sidebar`, CSS `position: sticky`; width **can't yet** (no number setting), use a fixed width. |
| Newsletter band: title, text, embed (needs both the home toggle and the Newsletter toggle) | `home.blade.php`, `Composers/Threads.php` | **later** | Region `newsletter` plus two text settings. Supportable as pasted embed. |
| Single header: category link, "N min read", excerpt, author row ("Updated" logic) | `single.blade.php` | **later** | `kicker`, `readingMinutes`, `standfirst`, `updatedText`. Supportable. |
| Single stats row toggle `egberipiribo_single_stats_enabled` | `threads-setup.php`, `Composers/Threads.php` | **later** | Read by the composer but no view draws it (dead option); do not port, or port as a `boolean` if a stats row is designed. |
| Single sidebar: toggle, width, widgets or a rectangle ad when empty | `single.blade.php` | **later** | Region `single-sidebar`, region `ad-rectangle`; width **can't yet**. Supportable with a fixed width. |
| Tag chips (toggle) and author bio box (toggle) with role and link chips (Website plus eight networks) | `single.blade.php` | **later** | `$page->tags`; bio from `Person`; role and links **can't yet** (A9). |
| Archive kicker by type (Category / Tag / Section archive), description, featured first row with 450px image, "Latest in this archive" | `archive.blade.php` | **later** | `subject->kind`, `taxonomy`, `description`, first of `items`. Supportable. |
| Archive row: category link, date, read time, up to two #tags, title, excerpt, 3:2 image | `archive.blade.php` | **later** | `kicker`, `publishedText`, `standfirst`, `image`; read time and tags **can't yet** (A9). |
| Archive sidebar (widgets) | `archive.blade.php` | **later** | Region `archive-sidebar`. Supportable. |
| Newer / Older links | `archive.blade.php`, `author.blade.php` | **later** | `pagination->previous` and `next`. Supportable. |
| Search header: italic query, "Found N articles", search-again form | `search.blade.php` | **later** | `search->query`, `total`. Supportable. |
| Search table header (Published / Article / Topic) and row layout; topic tag = first category | `search.blade.php`, `content-search.blade.php` | **later** | `results[].item` with `kicker` and `publishedText`, `snippetHtml`. Supportable. |
| Search pagination with `s` kept in links | `search.blade.php` | **later** | `pagination->pages()`. Supportable. |
| 404 picks heading "From the archive" plus three random posts (first in accent) | `Composers/Threads.php`, `404.blade.php` | **can't yet** | No random query for a theme; a `featured-posts` region (latest 3) replaces it. |
| Author header: role label, grayscale avatar, bio, website + social chips | `author.blade.php` | **can't yet** | `Person` lacks role, avatar and links (A9). |
| Author profile box: article count, topics, location | `author.blade.php`, `Composers/Threads.php` | **can't yet** | No per-person fields; no listing total. |
| Page sidebar (widgets) | `page.blade.php` | **later** | Region `page-sidebar`. Supportable. |
| Comment form: intro text setting, "save info" toggle, fields marked `*` | `comments.blade.php` | **later** | Core's form; the two settings are not themeable. |
| Responses title "N responses to {title}" | `Composers/Comments.php` | **later** | From `comments->count` and `$page->title` through `__()`. Supportable. |
| `egberipiribo-sticky-last-widget-wrap` (last widget in a sidebar sticks) | `threads.css` | **later** | CSS on the region wrapper. Supportable. |
| `.hero-num`, `.story-row`, `.story-num`, `.tag`, `.col-head`, `.archive-v4-row`, `.author-v4-row`, `.search-result-row`, `.thread-comment-form`, `.footer-network-strip`, `.newsletter-embed` | `threads.css` | **later** | CSS only. Supportable. |
| Threads palette and fonts (Libre Baskerville, Space Grotesk), Tailwind aliases `fore`, `mid`, `light`, `accent` | `Skins.php`, `threads.css`, `fonts.css` | **later** | Needs per-skin tokens (can't yet, A6); CSS under `[data-skin]` instead. |

**Totals (C2):** same in Teme 0 · different in Teme 0 · later 33 · can't yet 7 (40 rows)

### C3 Threads options (Customizer, legacy `theme_mod` keys kept for continuity)

| Feature | Where in the WordPress theme | Status | Teme: what it would need, and is it supportable |
|---|---|---|---|
| `egberipiribo_header_subscribe_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean` setting (fieldset site). Supportable. |
| `egberipiribo_header_subscribe_label` (text, "Subscribe") | `threads-setup.php` | **later** | `text` setting. Supportable. |
| `egberipiribo_header_subscribe_url` (url, "#") | `threads-setup.php` | **later** | `text` setting (no URL type). Supportable. |
| `egberipiribo_home_quote_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean`. Supportable. |
| `egberipiribo_home_quote_kicker` (text, "Thread believes") | `threads-setup.php` | **later** | `text`. Supportable. |
| `egberipiribo_home_quote_text` (textarea) | `threads-setup.php` | **later** | `text` (one line). Supportable. |
| `egberipiribo_home_belief_manifesto_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean`. Supportable. |
| `egberipiribo_home_newsletter_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean`, or "region has a card". Supportable. |
| `egberipiribo_home_newsletter_title` (text), `egberipiribo_home_newsletter_text` (textarea) | `threads-setup.php` | **later** | Two `text` settings. Supportable. |
| `egberipiribo_home_issue_section_heading` (text, "Also in This Issue") | `threads-setup.php` | **later** | `text`. Supportable. |
| `egberipiribo_home_also_in_initial` (number 0 to 50, default 10) | `threads-setup.php` | **can't yet** | No number setting; `reading.posts_per_page` / manifest `per_page` is the only count. |
| `egberipiribo_home_also_in_load_more_enabled`, `_per_load` (1 to 20), `_load_more_label` | `threads-setup.php` | **can't yet** | Load more needs a theme endpoint (A10); per-load is a number. |
| `egberipiribo_also_in_sidebar_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean`. Supportable. |
| `egberipiribo_home_column_1_category`, `_2_`, `_3_` (select of categories) | `threads-setup.php` | **can't yet** | No term setting; the category lives on each region's `featured-posts` card. |
| `egberipiribo_home_dedupe_posts` (boolean, on) | `threads-setup.php` | **can't yet** | No dedupe across regions (C2). |
| `egberipiribo_single_stats_enabled` (boolean, on; unused by views) | `threads-setup.php` | **later** | Dead option; skip unless a stats row is designed. |
| `egberipiribo_single_sidebar_enabled` (boolean, on) | `threads-setup.php` | **later** | `boolean`. Supportable. |
| `egberipiribo_single_sidebar_width` (number 240 to 420, default 300; also used by archive, search, page, author, home) | `threads-setup.php` | **can't yet** | No number setting; fixed 300px. |
| `egberipiribo_single_tags_enabled`, `egberipiribo_single_author_bio_enabled` (booleans, on) | `threads-setup.php` | **later** | `boolean` x2. Supportable. |
| `egberipiribo_comments_intro_text` (textarea) | `threads-setup.php` | **can't yet** | Core words the comment form; the theme cannot change its text. |
| `egberipiribo_comments_show_cookies` (boolean, on) | `threads-setup.php` | **can't yet** | The "save my details" checkbox is core's, not the theme's. |
| `egberipiribo_footer_network_enabled` (boolean), `_network_title` (text), `_network_new_tab` (boolean) | `threads-setup.php` | **later** | Slot `network` (new tab per item) plus a `text` setting; "enabled" is "slot has items". Supportable. |
| `egberipiribo_footer_network_item_1..5_label` and `_url` (ten settings; defaults PawPrint, The Garden Thread, At Table, The Travel Letter, Well, all "#") | `threads-setup.php` | **can't yet** | No repeater or URL type; a navigation slot carries the links (different, but loses the shipped defaults). |
| `egberipiribo_footer_copyright` (HTML text with tokens) | `threads-setup.php` | **can't yet** | HTML link in a `text` setting (A6). |
| `egberipiribo_footer_tagline` (text) | `threads-setup.php` | **later** | `text`. Supportable. |
| Menu locations: `primary_navigation`, `mobile_drawer_pages_navigation`, `mobile_drawer_social_navigation`, `drawer_footer_navigation`, `footer_sections`, `footer_about`, `footer_connect` | `app/setup.php` | **later** | Seven manifest slots; all appear in the admin regardless of skin (A5). Supportable. |
| Author profile user meta keys (`egberipiribo_author_role`, `_location`, `_topics`, `_x_url`, `_instagram_url`, `_youtube_url`, `_threads_url`, `_flipboard_url`, `_facebook_url`, `_linkedin_url`, `_pinterest_url`) | `threads-setup.php` | **can't yet** | No per-person fields; WordPress user meta does not import. |

**Totals (C3):** same in Teme 0 · different in Teme 0 · later 17 · can't yet 10 (27 rows)

**Section C totals:** same in Teme 0 · different in Teme 0 · later 63 · can't yet 17 (80 rows)

---

## D. Later: the other skins and design sources

All **later**; none has Blade. Demos are standalone HTML (Tailwind and Alpine from a CDN, fonts from Google); a port follows the same route as Fable: drawings first, then Blade with Teme data.

### D1 Demo skins (`skins/`)

| Candidate | What it is | Status | Teme: what it would need |
|---|---|---|---|
| strata (`skins/strata/*.html`) | Seven-page demo of the built Strata skin (refined v2 home, article, archive, author, page, search, 404). | **later** | Section B. |
| threads (`skins/threads/*.html`) | 23 drawings of the built Threads skin (home, article, archive v1 to v3, author v1 and v3, page, search and drawer and modal, 404 v1 and v2, subscribe 1 to 3, mobile nav 1 to 5). Variants are archive only. | **later** | Section C. |
| astra | Ivory canvas, olive accents, pale nav rail, Newsreader + DM Sans; seven pages, topic filters, load more, saved stories (`stories.js`, `astra.js`). | **later** | Needs client-side filter and saved-stories storage (can't yet), otherwise like Fable. |
| bloom | Home, garden and lifestyle; DM Serif Display + DM Sans, sage and terracotta, pills; eight pages incl. subscribe. | **later** | Standard listings, nav, regions; newsletter by region. Looks supportable. |
| concept9 ("Stanley") | 50/50 split screen: sticky featured panel left, scrolling feed right; Outfit + DM Sans; live date; search overlay; eight pages. | **later** | Needs `lead` for the sticky panel and `feed`; live date and overlay search limited (can't yet). Looks supportable. |
| opus ("Strata Opus") | Full-width ticker, aligned rail, Archivo + Geist, single accent in light and dark; eight pages. | **later** | Needs `tokens_dark` (supported) and a ticker from `featured`/`feed`. Looks supportable. |
| outline | Architectural grid, Chivo + Space Mono, wireframe borders, grayscale-to-colour reveal; two pages only (home, article). | **later** | Needs archive, search, author, page, 404 drawn first. Looks supportable. |
| zenith | Friendly lifestyle, Outfit + Nunito, badge categories, soft shadows, top ad banner; eight pages incl. subscribe. | **later** | Needs `ad-*` regions and category badges from `kicker`. Looks supportable. |
| fable v2, v3, reskin (`skins/fable/{v2,v3,reskin}`) | Earlier and alternative Fable drawings (v2 adds subscribe). | **later** | Not ported; the shipped Fable is the nine `fable-*.html` files. |

**Totals (D1):** same in Teme 0 · different in Teme 0 · later 9 · can't yet 0 (9 rows)

### D2 `One WP theme/` (design source material, never loaded by WordPress)

| Candidate | What it is | Status | Teme: what it would need |
|---|---|---|---|
| `PICKS-ANALYSIS.md`, `SAGE-THEME-ARCHITECTURE.md` | Analysis of 26 homepage concepts (groups A to I) and the early multi-pattern, `config/brand.php` architecture the approved spec replaced. | **later** | Reference only. |
| `Picks/` (11 files: anewtheme, baseline, broadstreet, chroma, nexus, prism, the-nnegi-forge pairs) and `Picks/final/` (15: baseline, canvas v2, chroma, cleanreader2, homepage-v16-v2, observer, pulse, velocity, "Krafty Publisher") | Curated skin candidates; unique structures: fixed left panel (Strata), split screen (concept9), category streams (canvas), list-driven (broadstreet), bento (chroma), architectural grid (outline). | **later** | Same data needs as the demo skins; prism (horizontal scroll panels) is judged impractical for SEO. |
| `Candidates/` (23 files: kiosk, ledger, signal, spread, stack, canvas, chroma, classic-publisher, curated, folio, meridian, observer, palate, storyteller variants) | Earlier concept demos, not curated. | **later** | Pick-and-draw first. |
| `idb/` (WordPress plugin "IDB / Inquiral Database" 7.0.0: TMDB and IGDB import, movies, TV, games, cast tables, ratings; includes `kamaba-theme`) | A data plugin with its own post types and taxonomies. | **later** | Belongs in a Teme plugin (content writer, jobs, import capabilities), not a theme. Custom post types for a theme to declare: can't yet. |
| `kamaba/` (WordPress theme for IDB: movie, TV, game archives, watch providers, Tailwind and Alpine) | A PHP theme (not Sage) for the IDB data. | **later** | Needs the IDB content types from a plugin and the ability for a theme to render custom type fields (supported through `$page->field()` once types exist). |

**Totals (D2):** same in Teme 0 · different in Teme 0 · later 5 · can't yet 0 (5 rows)

**Section D totals:** same in Teme 0 · different in Teme 0 · later 14 · can't yet 0 (14 rows)

---

## Fable (the skin this release ships)

(filled in by the lead from the page reports)

---

## Grand totals

| Section | same in Teme | different in Teme | later | can't yet | rows |
|---|---|---|---|---|---|
| A | 16 | 107 | 1 | 26 | 150 |
| B | 0 | 0 | 45 | 8 | 53 |
| C | 0 | 0 | 63 | 17 | 80 |
| D | 0 | 0 | 14 | 0 | 14 |
| E (Fable) | 17 | 25 | 1 | 3 | 46 |
| **All** | 33 | 132 | 124 | 54 | 343 |
