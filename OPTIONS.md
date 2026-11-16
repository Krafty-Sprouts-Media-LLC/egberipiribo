# OPTIONS: the theme settings, skin by skin

**A proposal, with a first set built.** The settings marked **built** are declared in `teme-theme.json` and read by the templates; every other row is still a proposal for later updates. Defaults reproduce the drawings, so the site looks unchanged until someone changes a setting.

The Fable drawings and the WordPress theme's built skins give the administrator of a magazine site a long list of choices. This document lists them as proposed Teme theme settings, **per skin and section by section**.

Every setting is read in one place, at the top of the template named under "Read in", as `$themeSettings['key'] ?? <the drawing's default>`. With no settings declared yet the default always applies, so Fable draws exactly as the drawings do. Adopting a setting means adding it to `teme-theme.json` (in the order the tables give) and, for a row marked *new*, one read line at the top of that template. **No markup changes.**

The data behind this file is `docs/options.json`; `node scripts/build-options.mjs` writes this file from it and checks every row against Teme's rules, so the totals below are computed, not typed.

## Totals

| | Settings | **Built** | Proposed, not built | Waiting on a Teme feature |
|---|---|---|---|---|
| Fable | 222 | 119 | 78 | 25 |
| Shared by every skin (with the skin choice) | 10 | 9 | 0 | 1 |
| Later: Strata | 47 | 0 | 36 | 11 |
| Later: Threads | 57 | 0 | 44 | 13 |
| **All** | **336** | **128** | 158 | 50 |

Fable settings per template or partial: `home` 48 · `archive` 18 · `article` 16 · `partials/footer` 14 · `partials/article-share` 14 · `partials/card` 11 · `author` 10 · `layout` 9 · `page` 9 · `partials/rail` 8 · `notfound` 8 · `search` 8 · `partials/article-byline` 7 · `partials/home-pattern` 6 · `partials/article-body` 5 · `partials/mobile-header` 4 · `partials/article-readnext` 4 · `partials/article-related` 4 · `error` 4 · `partials/article-authors` 3 · `partials/nav-sections` 2 · `partials/pagination` 2 · `partials/comment-card` 2 · `partials/ad` 1 · `partials/wordmark` 1 · `partials/search-panel` 1 · `partials/listing-search-form` 1 · `partials/comments-thread` 1 · `comments` 1.

Per fieldset (all skins): site 128 · latest 74 · content 68 · archive 66.

Waiting on a Teme feature (50): list of links (#373) 2 · request-time date 5 · reading time on a listing item 1 · per-skin tokens (#370/#371) 17 · dark tokens designed 3 · load more (next page of items without a page load) 1 · a movable block between the layout's `before` and `main` sections 1 · list (#373) 1 · sort 4 · counts 5 · author role 2 · latest items on an article page 1 · author e-mail and social links 1 · author facts 1 · section counts 1 · term picker (category) 4.

Every row passes the checks in `scripts/build-options.mjs` (key shape and length, types, fieldsets, a select's default is one of its options, a `visible_when` names only earlier keys).

## How to read the tables

- **Key**: 2 to 41 lower-case letters, digits and underscores, never starting `nav_` (Teme's rule). Every skin's keys carry its prefix (`fable_`, `strata_`, `threads_`) because all settings share one flat list.
- **Type**: what Teme stores today: `select`, `boolean`, `text` or `color`. There is no number, image, URL or list type, so a count is a `select` of a few steps.
- **Default**: what the drawing (or, for Strata and Threads, the WordPress theme) does now.
- **Read in**: the template or partial that reads the key (a skin's own file name).
- **Visible when**: Teme's `visible_when`, which names only **earlier** keys. Every setting of a skin carries `skin` = that skin, so an administrator sees only the chosen skin's settings; a sub-option shows only while its block's switch is on.
- **Fieldset**: one of Teme's four (`site`, `latest`, `content`, `archive`). Teme has no sub-groups, so the "Read in" and heading groups here are documentation only.
- **Status**: *read now* (the Fable template already reads exactly this key), *new* (a proposal that needs its one read line), or **waits** on a Teme feature, with its issue where one exists: number, image and list of links **#373**; per-skin tokens (colours, fonts, a second accent) **#370 and #371**. A "waits" row with no number waits on data Teme does not give a template yet (a request-time date, counts, view counts, sort), which is recorded in FINDINGS.md item 20.

A setting that waits is still listed with the form it will take, so the template can already read it.

## The skin setting, first

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `skin` | select: `fable`, `strata`, `threads` | `fable` | `views/templates/* (every dispatcher)` | always | site | Chooses the skin once more than one ships (strata and threads are added to the options by the update that builds them). Today there is one option, so the setting is not shown. Strata and Threads are listed as options for the structure; only fable exists in this release. | **built** |

### How skins and settings fit together

1. **`skin` is the first setting.** Every other setting of a skin is `visible_when: {"skin": "<that skin>"}`. Strata and Threads therefore get their own sets later, without touching Fable's.
2. **Templates already dispatch on it.** Each file in `views/templates` is `@include('theme::skins.'.($themeSettings['skin'] ?? 'fable').'.<page>')`, so adding `skin` to the manifest switches the template set with no template change.
3. **The colours and fonts do not switch yet.** Manifest tokens are static (FINDINGS 1 and 6; #370 and #371), so a second skin cannot bring its own tokens, fonts or second accent. The interim is CSS keyed on `[data-skin]` (the layout already writes `data-skin="fable"` on its wrapper).
4. **Manifest order**: `skin`, then the shared settings, then Fable's, then Strata's, then Threads's. The tables below are in that order; each `visible_when` names only keys that come before it.

**Settings every skin shares** (below) carry no skin condition when every skin uses them, or `skin` = a list when only some do.

## Shared by every skin

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_ads` | boolean | on | `components/ad-slot` | always | site | Master switch that hides every ad region at once without emptying them. Used by Fable, Strata and Threads. WordPress has no master switch, only the three per-placement switches below. | **built** |
| `show_newsletter` | boolean | on | `skins/strata/footer, skins/threads/home, skins/threads/subscribe` | always | site | Turns the newsletter band on or off (Strata footer, Threads home band and Subscribe page). WordPress: egberipiribo_newsletter_enabled, default off; the drawings show the band so it defaults on. | **built** |
| `social_networks` | text | empty | `skins/strata/social-icons, skins/threads/footer` | always | site | The site social accounts (WordPress: nine egberipiribo_social_* URLs for Facebook, Instagram, X, Threads, YouTube, TikTok, Flipboard, LinkedIn, Pinterest; icons render only for the filled ones; RSS is automatic). Used by Strata rail and Threads connect column. *When Teme can: One list of network and URL pairs; the icon registry (nine platforms) stays in the theme.* | **waits**: list of links #373 |
| `show_updated_date` | boolean | on | `skins/strata/single, skins/threads/single` | always | site | Prints "Updated <date>" in place of the publish date once a post was edited well after publishing (WordPress: post_display_date(), always on). Strata and Threads singles. | **built** |
| `updated_after_days` | select: `1`, `7`, `14`, `30` | `1` | `skins/strata/single, skins/threads/single` | `show_updated_date` = on | site | How long after publishing an edit counts as an update. Teme's own rule starts at one day, which Fable follows by default; Strata and Threads' WordPress rule is a fixed seven. | **built** |
| `show_comments` | boolean | on | `skins/strata/comments, skins/threads/comments` | always | content | Shows or hides the comment list and form on posts. Used by Fable, Strata and Threads (WordPress shows them whenever the post allows comments). | **built** |
| `show_reading_time` | boolean | on | `skins/strata/single, skins/threads/single` | always | content | The "N min read" figure (Strata byline and cards, Threads kicker, lead and rows). WordPress always prints it. | **built** |
| `show_tags` | boolean | on | `skins/strata/single, skins/threads/single` | always | content | The tag chips under a post. Strata and Threads (WordPress: egberipiribo_strata_tags_enabled and egberipiribo_single_tags_enabled, both default on). | **built** |
| `show_author_card` | boolean | on | `skins/strata/single, skins/threads/single` | always | content | The author bio box under a post. Strata and Threads (WordPress: egberipiribo_strata_author_box_enabled and egberipiribo_single_author_bio_enabled, both default on). | **built** |

## Fable (the skin this release ships)

Every block of every Fable page has its own switch; every presentational choice has its own select, text or boolean. A block's sub-options appear only while its switch is on.

**The home page's order.** Teme allows a template to place its main blocks in any order a setting names, so a select per position (or one named preset) can reorder the blocks inside `main`. A `select` cannot stop an administrator choosing the same block twice and Teme has no list or drag-and-drop setting, so a preset is the safer form; both are listed. The Top stories strip sits in the layout's `before` section (outside `main`) and the ads sit with their blocks, so they do not move. See the "Home: order" rows.

**Fieldsets.** Fable's settings spread over Teme's four fieldsets only; the crowded `site` fieldset is the cost of having no sub-groups (FINDINGS 24).

### Site-wide: header, rail, footer, share, patterns, dark mode, dates, cards

#### Ads: master switch

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_ad_label` | text | `Advertisement` | `partials/ad` | `skin` = `fable`; `show_ads` = on | site | The small word over every slot (a publisher may need "Sponsored" or "Advertising" by local rules). | new |

#### Newsletter band

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_footer_newsletter` | boolean | on | `partials/footer` | `skin` = `fable` | site | The dark sign-up band above the footer (the drawing has it on every page); off removes the band and the rail and menu buttons that point to it. | **built** |
| `fable_newsletter_where` | select: `every_page`, `home_only` | `every_page` | `partials/footer` | `skin` = `fable`; `fable_footer_newsletter` = on | site | A magazine may want the sign-up band only on the front page; the footer reads $isFront once. | new |
| `footer_newsletter_heading` | text | `Never miss a story.` | `partials/footer` | `skin` = `fable`; `fable_footer_newsletter` = on | site | The band's headline (the drawing: "Seven sections. One email. Sundays."). | read now |
| `footer_newsletter_text` | text | `The best stories of the week, picked by an editor and sent once. You can leave in one click.` | `partials/footer` | `skin` = `fable`; `fable_footer_newsletter` = on | site | The line under the headline; the sign-up form itself is the `newsletter` region. | read now |

#### Header (small screens)

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_header_sticky` | boolean | on | `partials/mobile-header` | `skin` = `fable` | site | The small-screen header stays at the top while reading (the drawing's sticky bar); off lets it scroll away. | new |
| `fable_header_search` | boolean | on | `partials/mobile-header` | `skin` = `fable` | site | The search button in the small-screen header (the drawing has it). | new |
| `fable_phone_menu_newsletter` | boolean | on | `partials/mobile-header` | `skin` = `fable`; `fable_footer_newsletter` = on | site | The newsletter button at the foot of the small-screen menu. | new |
| `fable_header_date_line` | boolean | on | `home` | `skin` = `fable` | site | Today's date above the home page title, as the drawing prints it ("Saturday 14 November"). *When Teme can: The date needs the reader's request time; a cached page cannot know it. Today the line is left out.* | **waits**: request-time date |
| `fable_date_line_format` | select: `weekday_day_month`, `weekday_day_month_year`, `day_month_year` | `weekday_day_month` | `home` | `skin` = `fable`; `fable_header_date_line` = on | site | How the date line reads: the drawing prints weekday, day and month. *When Teme can: Same wall as the date line; only the choice of words is storable.* | **waits**: request-time date |

#### Rail

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_rail_show` | boolean | on | `layout` | `skin` = `fable` | site | The fixed left rail on wide screens; off shows the small-screen header on every width (the "In this story" list lives in the rail and goes with it). | new |
| `fable_rail_tablet` | select: `open`, `collapsed` | `open` | `layout` | `skin` = `fable`; `fable_rail_show` = on | site | On tablets the rail can start collapsed behind the menu button (rail from 1024px, the drawing) or only from 1280px. | new |
| `fable_wordmark_text` | text | empty | `partials/wordmark` | `skin` = `fable` | site | The wordmark text in the rail and header; empty uses the site name (the drawing: "Fable"). | new |
| `fable_rail_tagline` | boolean | on | `partials/rail` | `skin` = `fable`; `fable_rail_show` = on | site | The tagline under the wordmark (the drawing shows it); some sites have a tagline too long for the rail. | new |
| `fable_rail_tagline_text` | text | empty | `partials/rail` | `skin` = `fable`; `fable_rail_show` = on; `fable_rail_tagline` = on | site | A shorter line just for the rail (Strata has a rail tagline setting); empty uses the site tagline. | new |
| `fable_rail_search` | boolean | on | `partials/rail` | `skin` = `fable`; `fable_rail_show` = on | site | The search field in the rail (the drawing's "Search /" button). | new |
| `fable_rail_all_stories` | boolean | on | `partials/nav-sections` | `skin` = `fable` | site | The "All stories" entry at the top of the sections list (rail and small-screen menu). | new |
| `fable_rail_topics_open` | select: `current`, `all`, `none` | `current` | `partials/nav-sections` | `skin` = `fable` | site | Which sections show their topics at first: only the one the reader is in (as built), all of them, or none. | new |
| `fable_rail_newsletter` | boolean | on | `partials/rail` | `skin` = `fable`; `fable_rail_show` = on; `fable_footer_newsletter` = on | site | The white "newsletter" button under the nav in the rail. | new |
| `fable_newsletter_button_label` | text | `Get our newsletter` | `partials/rail` | `skin` = `fable` | site | The words on the rail and menu button (the drawing: "Get the Sunday letter"). | new |
| `fable_rail_social` | boolean | on | `partials/rail` | `skin` = `fable`; `fable_rail_show` = on | site | The social icon row at the foot of the rail (from the `social` menu). | new |

#### Search

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_search_panel` | boolean | on | `partials/search-panel` | `skin` = `fable` | site | The search overlay opened by the search buttons or "/" (the drawing); off makes the buttons plain links to the search page. | **built** |
| `fable_search_shortcut` | boolean | on | `partials/rail` | `skin` = `fable`; `fable_search_panel` = on | site | The "/" key hint in the rail search button and the keyboard shortcut itself. | **built** |
| `search_placeholder` | text | `Search stories, sections and writers` | `partials/listing-search-form` | `skin` = `fable` | site | The words in every empty search field; a site that is not stories-and-writers wants its own. | read now |

#### Cards

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_card_image` | boolean | on | `partials/card` | `skin` = `fable` | site | Pictures on story cards (the drawing always has them); off gives text-only cards. | new |
| `fable_card_image_ratio` | select: `3:2`, `4:3`, `16:10`, `1:1` | `3:2` | `partials/card` | `skin` = `fable`; `fable_card_image` = on | site | The picture shape on cards: 3:2 from 640px (the drawing); 4:3, 16:10 and 1:1 are the shapes the drawings use elsewhere. | new |
| `fable_card_order` | select: `image_first`, `text_first` | `image_first` | `partials/card` | `skin` = `fable`; `fable_card_image` = on | site | Picture above the words (the drawing) or the words first. | new |
| `fable_card_kicker` | boolean | on | `partials/card` | `skin` = `fable` | site | The section name above each card headline. | new |
| `fable_card_standfirst` | boolean | on | `partials/card` | `skin` = `fable` | site | The standfirst under the headline (the drawing shows it in the list layout and in search results only). | new |
| `fable_card_author` | boolean | on | `partials/card` | `skin` = `fable` | site | The author name in the card byline. | new |
| `fable_card_date` | boolean | on | `partials/card` | `skin` = `fable` | site | The date in the card byline. | new |
| `fable_card_headline_size` | select: `compact`, `regular`, `large` | `regular` | `partials/card` | `skin` = `fable` | site | Card headline size: compact is the phone size, regular the drawing's 1.125rem, large the "hed-md" size. | new |
| `fable_card_reading_time` | boolean | on | `partials/card` | `skin` = `fable` | site | Minutes to read in the card byline (the drawing's cover story, long read and Also today print it). *When Teme can: Teme's ListingItem has no readingMinutes (only the article has), so cards cannot show it yet.* | **waits**: reading time on a listing item |

#### Dates and reading time

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_date_style_cards` | select: `relative`, `absolute` | `relative` | `partials/card` | `skin` = `fable` | site | Card dates read "3 days ago" (the drawing's grid) or as a full date (as built today). *When Teme can: Only `relative` waits (it needs the reader's request time); `absolute` is storable and is what the theme draws today.* | **waits**: request-time date |
| `fable_article_date_format` | select: `with_time`, `words` | `with_time` | `partials/article-byline` | `skin` = `fable` | site | The article byline date with the time of day (the drawing: "14 November 2026, 9:12 am") or the date alone. | new |
| `fable_reading_time_wording` | text | `:count min read` | `partials/article-byline` | `skin` = `fable` | site | How the reading time is worded; :count is the minutes ("14 min read" in the drawing). | new |

#### Share

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_share` | boolean | on | `partials/article-share` | `skin` = `fable` | site | The share buttons on articles, at the byline and after the tags. | **built** |
| `fable_share_facebook` | boolean | on | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A Facebook button (in the drawing). | **built** |
| `fable_share_x` | boolean | on | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | An X button (in the drawing). | **built** |
| `fable_share_email` | boolean | on | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A share-by-email button (in the drawing). | **built** |
| `fable_share_copy` | boolean | on | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | The copy-link button (in the drawing). | **built** |
| `fable_share_pinterest` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A Pinterest button; the template draws it today but the drawing has none, so the default is off. | **built** |
| `fable_share_linkedin` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A LinkedIn button for a business or news audience. | **built** |
| `fable_share_whatsapp` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A WhatsApp button, the main way stories travel on phones in many countries. | **built** |
| `fable_share_telegram` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A Telegram button. | **built** |
| `fable_share_reddit` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | A Reddit button. | **built** |
| `fable_share_native` | boolean | off | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | The phone's own share sheet (navigator.share), shown only on devices that offer it. | **built** |
| `fable_share_placement` | select: `top`, `bottom`, `both`, `floating` | `both` | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | Where the buttons sit: at the byline (top), after the tags (bottom), both (the drawing) or a floating rail down the side. | **built** |
| `fable_share_style` | select: `icons`, `labels` | `icons` | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | Round icon buttons only (the drawing) or icons with their network name. | **built** |
| `fable_share_label` | text | `Share this story` | `partials/article-share` | `skin` = `fable`; `show_share` = on | site | The line before the bottom row of buttons. | **built** |

#### Patterns

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `pattern` | select: `none`, `sections`, `pets`, `home`, `food` | `none` | `home` | `skin` = `fable` | site | The icon pattern laid over the brand-colour areas (the pattern samples); off keeps every area solid, as the seven main drawings are. | **built** |
| `pattern_strength` | select: `subtle`, `medium`, `bold` | `subtle` | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | How visible the icons are. | **built** |
| `pattern_density` | select: `sparse`, `regular`, `dense` | `regular` | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | How tightly the icons repeat (the samples: open, even, tight). | **built** |
| `pattern_accent` | color | empty | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | One icon in seven takes this colour (pattern sample 2 uses #ffd23f); empty keeps every icon white. | **built** |
| `fable_pattern_colour` | select: `mixed`, `accent`, `white` | `mixed` | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | Icon colour as in sample 2: white with one accent icon, all accent, or all white. | **built** |
| `fable_pattern_layout` | select: `scatter`, `grid`, `dots` | `scatter` | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | Scattered and turned icons, straight rows, or straight rows with dots between (sample 2). | **built** |
| `fable_pattern_style` | select: `regular`, `fill` | `regular` | `partials/home-pattern` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | Outline or solid icons (the samples' icon style). | **built** |
| `fable_pattern_rail` | boolean | on | `partials/rail` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern on the rail (both samples do). | **built** |
| `fable_pattern_phone_header` | boolean | on | `partials/mobile-header` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern on the small-screen header (both samples do). | **built** |
| `fable_pattern_footer` | boolean | on | `partials/footer` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern on the footer (both samples do). | **built** |
| `fable_pattern_long_read` | boolean | on | `home` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern on the home page's long read (built today whenever `pattern` is on; this adds only the off switch). | **built** |
| `fable_pattern_sections` | boolean | off | `home` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern on section headings; not in the samples, so off. | **built** |
| `fable_pattern_404` | boolean | off | `notfound` | `skin` = `fable`; `pattern` = `sections` or `pets` or `home` or `food` | site | The pattern behind the big 404 number; not in the samples, so off. | **built** |

#### Colour and dark mode

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_accent_use` | select: `everywhere`, `rail_only` | `everywhere` | `layout` | `skin` = `fable` | site | Where the brand colour appears: rail, kickers, links and the 404 number (the drawing) or only the rail and footer with ink elsewhere. | new |
| `fable_accent_second` | color | `#ffd23f` | `layout` | `skin` = `fable` | site | The second colour of pattern sample 2, free for highlights (the pattern already has `pattern_accent`). *When Teme can: A second site colour token for the skin; until then only the pattern accent is storable.* | **waits**: per-skin tokens #370/#371 |
| `fable_dark_mode` | select: `light`, `system`, `dark`, `toggle` | `light` | `layout` | `skin` = `fable` | site | Follow the reader's system, always light, always dark or a reader's toggle; the drawings are light only, so `light` is the default and the others need a dark design first. | new |
| `fable_dark_background` | color | empty | `layout` | `skin` = `fable`; `fable_dark_mode` = `system` or `dark` or `toggle` | site | The page colour in dark mode. *When Teme can: Fable's drawings have no dark tokens; once a dark palette is designed this stores its page colour.* | **waits**: dark tokens designed |
| `fable_dark_text` | color | empty | `layout` | `skin` = `fable`; `fable_dark_mode` = `system` or `dark` or `toggle` | site | The text colour in dark mode. *When Teme can: Same wall: needs the dark palette designed first.* | **waits**: dark tokens designed |
| `fable_dark_rail` | color | empty | `layout` | `skin` = `fable`; `fable_dark_mode` = `system` or `dark` or `toggle` | site | The rail colour in dark mode. *When Teme can: Same wall: needs the dark palette designed first.* | **waits**: dark tokens designed |

#### Page chrome

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_back_to_top` | boolean | on | `layout` | `skin` = `fable` | site | The floating back-to-top button (the drawing has it); some editors dislike it. | new |
| `fable_pagination_links` | select: `numbered`, `prev_next` | `numbered` | `partials/pagination` | `skin` = `fable` | site | Numbered page links (the archive drawing) or only Previous and Next. | new |
| `fable_pagination_style` | select: `pages`, `show_more` | `pages` | `partials/pagination` | `skin` = `fable` | site | Real pages (as built) or the home drawing's "Show more stories" button. *When Teme can: Only `show_more` waits; Teme gives a template one page of items, not a way to ask for the next.* | **waits**: load more (next page of items without a page load) |

#### Footer

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_footer_brand` | boolean | on | `partials/footer` | `skin` = `fable` | site | The first footer column: wordmark and blurb. | new |
| `footer_blurb` | text | empty | `partials/footer` | `skin` = `fable`; `fable_footer_brand` = on | site | The "about" line under the footer wordmark; empty uses the site tagline. | read now |
| `fable_footer_company` | boolean | on | `partials/footer` | `skin` = `fable` | site | The "Company" menu column (the `footer` menu). | new |
| `fable_footer_company_heading` | text | `Company` | `partials/footer` | `skin` = `fable`; `fable_footer_company` = on | site | The Company column heading. | new |
| `fable_footer_network` | boolean | on | `partials/footer` | `skin` = `fable` | site | The "Our network" menu column (the `network` menu). | new |
| `fable_footer_network_heading` | text | `Our network` | `partials/footer` | `skin` = `fable`; `fable_footer_network` = on | site | The network column heading. | new |
| `fable_footer_copyright` | boolean | on | `partials/footer` | `skin` = `fable` | site | The copyright line at the very bottom. | new |
| `fable_footer_copyright_text` | text | `© {{year}} {{site}}. All rights reserved.` | `partials/footer` | `skin` = `fable`; `fable_footer_copyright` = on | site | The line's words; {{year}} and {{site}} fill in (as in the Strata footer setting). | new |
| `fable_footer_maker` | text | empty | `partials/footer` | `skin` = `fable` | site | An optional maker or credit line beside the copyright, such as "Site by ..."; empty draws nothing. | new |


### The front page and the posts page (fieldset latest)

#### Home: block order

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_order` | select: `drawing`, `grid_early`, `sections_early`, `custom` | `drawing` | `home` | `skin` = `fable` | latest | The order of the blocks inside <main>: cover story, Also today, leaderboard, long read, story grid, From the sections, Most read. The Top stories strip (the layout's `before` section) and the title cannot move; "custom" uses the seven slots below. | new |
| `fable_home_slot_1` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `cover` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 1 of 7 for the "custom" order; a select cannot forbid duplicates, so the template draws a block at its first slot and ignores repeats, and a block in no slot is not drawn. | new |
| `fable_home_slot_2` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `also_today` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 2 of 7 for the "custom" order. | new |
| `fable_home_slot_3` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `leaderboard` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 3 of 7 for the "custom" order. | new |
| `fable_home_slot_4` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `long_read` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 4 of 7 for the "custom" order. | new |
| `fable_home_slot_5` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `grid` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 5 of 7 for the "custom" order. | new |
| `fable_home_slot_6` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `sections` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 6 of 7 for the "custom" order. | new |
| `fable_home_slot_7` | select: `cover`, `also_today`, `leaderboard`, `long_read`, `grid`, `sections`, `most_read`, `none` | `most_read` | `home` | `skin` = `fable`; `fable_home_order` = `custom` | latest | Slot 7 of 7 for the "custom" order. | new |
| `fable_home_top_stories_position` | select: `above_page`, `below_title` | `above_page` | `home` | `skin` = `fable` | latest | The Top stories strip can only sit above <main> (the layout's `before` section); it cannot be moved below it. *When Teme can: Teme gives a page a fixed `before` and `main`; a full-width strip drawn in `main` would be a new design. Only `above_page` is possible.* | **waits**: a movable block between the layout's `before` and `main` sections |
| `fable_home_order_list` | text | empty | `home` | `skin` = `fable` | latest | The real answer: an ordered, drag-and-drop list of the blocks, with no duplicates possible. *When Teme can: An ordered list setting of block names; until then the preset and the seven slots above stand in.* | **waits**: list #373 |

#### Home: title

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `front_title` | text | `Today on :site` | `home` | `skin` = `fable` | latest | The home page's one h1; :site is the site's name. | read now |
| `posts_title` | text | `Latest stories` | `home` | `skin` = `fable` | latest | The title of a posts page that has none of its own. | read now |
| `fable_home_title_visible` | boolean | on | `home` | `skin` = `fable` | latest | Show the h1 on the page; off keeps it for assistive technology and search engines but hides it (a page must keep one h1). | **built** |
| `show_story_count` | boolean | on | `home` | `skin` = `fable` | latest | "N stories on this page" beside the title. | **built** |

#### Home: top stories strip

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_top_stories` | boolean | on | `home` | `skin` = `fable` | latest | The ranked "Top stories" strip above the page (the drawing's first band). | **built** |
| `top_stories_count` | select: `3`, `4`, `5`, `6` | `4` | `home` | `skin` = `fable`; `fable_home_top_stories` = on | latest | How many stories the strip lists. | read now |
| `fable_home_top_stories_label` | text | `Top stories` | `home` | `skin` = `fable`; `fable_home_top_stories` = on | latest | The strip's label. | new |
| `fable_home_top_stories_rank` | boolean | on | `home` | `skin` = `fable`; `fable_home_top_stories` = on | latest | The big rank numbers before each headline (1, 2, 3...). | **built** |

#### Home: cover story

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_cover` | boolean | on | `home` | `skin` = `fable` | latest | The cover story (the lead) with its picture and overlapping headline card. | **built** |
| `fable_home_cover_kicker` | boolean | on | `home` | `skin` = `fable`; `fable_home_cover` = on | latest | The section name above the cover headline. | **built** |
| `fable_home_cover_standfirst` | boolean | on | `home` | `skin` = `fable`; `fable_home_cover` = on | latest | The standfirst under the cover headline. | **built** |
| `fable_home_cover_byline` | boolean | on | `home` | `skin` = `fable`; `fable_home_cover` = on | latest | The byline under the cover story. | **built** |
| `fable_home_cover_layout` | select: `overlap`, `stacked` | `overlap` | `home` | `skin` = `fable`; `fable_home_cover` = on | latest | The headline card overlaps the bottom of the picture (the drawing) or sits plainly below it (as the no-picture variant does). | new |
| `fable_home_cover_ratio` | select: `wide`, `standard` | `wide` | `home` | `skin` = `fable`; `fable_home_cover` = on | latest | Picture shape: 21:10 from 640px (the drawing) or 4:3 at every width. | new |

#### Home: Also today

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_also_today` | boolean | on | `home` | `skin` = `fable` | latest | The "Also today" row of small stories under the cover. | **built** |
| `also_today_count` | select: `3`, `6` | `3` | `home` | `skin` = `fable`; `fable_home_also_today` = on | latest | How many of the newest stories fill it when none are pinned (rows of three). | read now |
| `fable_home_also_today_kicker` | boolean | on | `home` | `skin` = `fable`; `fable_home_also_today` = on | latest | The section name above each small headline. | **built** |
| `fable_home_also_today_thumb` | boolean | on | `home` | `skin` = `fable`; `fable_home_also_today` = on | latest | The small square picture beside each headline. | **built** |

#### Home: ad spots

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_ad_leaderboard` | boolean | on | `home` | `skin` = `fable`; `show_ads` = on | latest | The 728 x 90 leaderboard under Also today (region `ad-leaderboard`). | **built** |
| `fable_home_ad_in_feed` | boolean | on | `home` | `skin` = `fable`; `show_ads` = on | latest | The wide advertisement inside the story grid (region `ad-in-feed`). | **built** |
| `feed_ad_after` | select: `4`, `8`, `12` | `4` | `home` | `skin` = `fable`; `fable_home_ad_in_feed` = on; `show_ads` = on | latest | The in-feed ad sits after this many stories. | read now |
| `fable_home_ad_rectangle` | boolean | on | `home` | `skin` = `fable`; `show_ads` = on | latest | The 336 x 280 rectangle beside Most read (region `ad-rectangle`). | **built** |

#### Home: the long read

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_long_read` | boolean | on | `home` | `skin` = `fable` | latest | The brand-colour "long read" block (region `home-long-read`; its story and heading are the editor's). | **built** |

#### Home: story grid

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_grid` | boolean | on | `home` | `skin` = `fable` | latest | The "More to explore" grid of stories with its pages. | **built** |
| `grid_title` | text | `More to explore` | `home` | `skin` = `fable`; `fable_home_grid` = on | latest | The heading above the grid. | read now |
| `show_layout_toggle` | boolean | on | `home` | `skin` = `fable`; `fable_home_grid` = on | latest | The reader's grid / list buttons above the grid. | **built** |
| `fable_home_grid_layout` | select: `grid`, `list` | `grid` | `home` | `skin` = `fable`; `fable_home_grid` = on | latest | The layout a reader sees first (the drawing: grid); their own choice is kept on their device. | new |
| `fable_home_grid_columns` | select: `3`, `4` | `4` | `home` | `skin` = `fable`; `fable_home_grid` = on | latest | Columns of the grid on wide screens (the drawing: 4). | new |
| `fable_home_sort_toggle` | boolean | on | `home` | `skin` = `fable`; `fable_home_grid` = on | latest | The "Newest / Quick reads" switch above the grid (the drawing). *When Teme can: A listing has one order, newest first; Teme offers no sort and no reading time on items.* | **waits**: sort |

#### Home: From the sections

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_sections` | boolean | on | `home` | `skin` = `fable` | latest | The "From the sections" band of up to four section lists (region `home-sections`). | **built** |

#### Home: Most read

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_home_most_read` | boolean | on | `home` | `skin` = `fable` | latest | The "Most read" ranked list beside its advertisement (region `home-most-read`). | **built** |
| `fable_home_most_read_source` | select: `editor_list`, `views` | `editor_list` | `home` | `skin` = `fable`; `fable_home_most_read` = on | latest | An editor's hand-picked list (as built) or the stories read most this week (the drawing). *When Teme can: Teme keeps no per-story view counts; only `editor_list` is storable.* | **waits**: counts |
| `fable_home_most_read_rank` | boolean | on | `home` | `skin` = `fable`; `fable_home_most_read` = on | latest | The ranking numbers beside each story. | **built** |


### Articles, pages and comment pages (fieldset content)

#### Article: top

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_reading_progress` | boolean | on | `article` | `skin` = `fable` | content | The thin reading bar across the top. | **built** |
| `fable_article_breadcrumb` | boolean | on | `article` | `skin` = `fable` | content | The breadcrumb above the headline (Strata has the same switch). | **built** |
| `fable_article_kicker` | boolean | on | `article` | `skin` = `fable` | content | The section name above the headline when there is no breadcrumb. | **built** |
| `fable_article_standfirst` | boolean | on | `article` | `skin` = `fable` | content | The standfirst (deck) under the headline. | **built** |

#### Article: byline

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_byline_avatar` | boolean | on | `partials/article-byline` | `skin` = `fable` | content | The author's initials circle beside the byline. | **built** |
| `fable_byline_authors` | boolean | on | `partials/article-byline` | `skin` = `fable` | content | "By" and the author names. | **built** |
| `fable_byline_role` | boolean | on | `partials/article-byline` | `skin` = `fable`; `fable_byline_authors` = on | content | The author's role after the name ("animals editor" in the drawing). *When Teme can: Teme's Person has a name, url and bio but no role or job title.* | **waits**: author role |
| `fable_byline_date` | boolean | on | `partials/article-byline` | `skin` = `fable` | content | The publication date in the byline. | **built** |
| `fable_article_show_updated` | boolean | on | `partials/article-byline` | `skin` = `fable` | content | The "Updated" date in the byline, when the article was revised at least a day later. | **built** |

#### Article: hero

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_article_hero` | boolean | on | `article` | `skin` = `fable` | content | The large picture under the byline. | **built** |
| `fable_article_hero_caption` | boolean | on | `article` | `skin` = `fable`; `fable_article_hero` = on | content | The caption and credit under the hero. | **built** |
| `fable_article_hero_ratio` | select: `16:10`, `16:9` | `16:10` | `article` | `skin` = `fable`; `fable_article_hero` = on | content | The hero's shape: 16:10 (the drawing's hero) or 16:9 (its in-text figure). | new |
| `fable_article_fields_before` | boolean | on | `article` | `skin` = `fable` | content | The custom fields set to show before the body (the drawing's "Reviewed by" line is such a field). | **built** |

#### Article: body

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_contents` | boolean | on | `partials/article-body` | `skin` = `fable` | content | The "In this story" list in the rail, built from the body's h2 headings (articles and pages). | **built** |
| `contents_min_headings` | select: `2`, `3`, `4` | `2` | `partials/article-body` | `skin` = `fable`; `show_contents` = on | content | The fewest h2 headings that earn the list. | read now |
| `article_lede` | boolean | on | `partials/article-body` | `skin` = `fable` | content | The first paragraph set larger, as the drawing's lede. | **built** |
| `fable_article_inline_related` | boolean | off | `article` | `skin` = `fable` | content | The small "Related" card inside the body, after its second block (it is in the drawing; off by default so the page stays as it is until someone chooses it). | **built** |
| `fable_article_fields_after` | boolean | on | `article` | `skin` = `fable` | content | The custom fields set to show after the body. | **built** |
| `fable_article_after_content` | boolean | on | `article` | `skin` = `fable` | content | The `after-content` region (a callout, button or featured posts) under the body. | **built** |

#### Article: ads

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_article_ad_leaderboard` | boolean | on | `article` | `skin` = `fable`; `show_ads` = on | content | The leaderboard above the article (region `ad-leaderboard`). | **built** |
| `fable_article_ad_in_article` | boolean | on | `article` | `skin` = `fable`; `show_ads` = on | content | The ad inside the body (region `ad-in-article`). | **built** |
| `ad_in_article_after` | select: `3`, `4`, `6`, `8` | `4` | `partials/article-body` | `skin` = `fable`; `show_ads` = on; `fable_article_ad_in_article` = on | content | The in-article ad sits after this many top-level blocks of the body. | read now |
| `ad_in_article_min` | select: `6`, `8`, `10` | `8` | `partials/article-body` | `skin` = `fable`; `show_ads` = on; `fable_article_ad_in_article` = on | content | The fewest blocks a body needs before the ad goes inside it. | read now |
| `fable_article_ad_second` | boolean | on | `article` | `skin` = `fable`; `show_ads` = on | content | The second ad after the body (region `ad-in-article-2`). | **built** |
| `fable_article_ad_half_page` | boolean | on | `article` | `skin` = `fable`; `show_ads` = on | content | The sticky half-page ad beside the article on screens from 1440px (region `ad-half-page`). | **built** |

#### Article: Read next

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_read_next` | boolean | on | `partials/article-readnext` | `skin` = `fable` | content | The "Read next" strip after the body. | **built** |
| `fable_read_next_source` | select: `same_section`, `newest` | `same_section` | `partials/article-readnext` | `skin` = `fable`; `show_read_next` = on | content | A story from the same section (as built) or simply the newest story. *When Teme can: A single template gets `related` only; only `same_section` is storable.* | **waits**: latest items on an article page |
| `fable_read_next_count` | select: `1`, `2`, `3` | `1` | `partials/article-readnext` | `skin` = `fable`; `show_read_next` = on | content | How many stories the strip offers (the drawing: one). | new |
| `fable_read_next_reason` | boolean | on | `partials/article-readnext` | `skin` = `fable`; `show_read_next` = on | content | The small line giving the reason or byline under the "Read next" headline. | **built** |

#### Article: tags

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_tags_label` | text | `Filed under` | `article` | `skin` = `fable`; `show_tags` = on | content | The word before the tags. | new |

#### Article: author card

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_author_card_avatar` | boolean | on | `partials/article-authors` | `skin` = `fable`; `show_author_card` = on | content | The initials circle in the card. | **built** |
| `fable_author_card_bio` | boolean | on | `partials/article-authors` | `skin` = `fable`; `show_author_card` = on | content | The author's bio in the card. | **built** |
| `fable_author_card_link` | boolean | on | `partials/article-authors` | `skin` = `fable`; `show_author_card` = on | content | The "More from ..." link to the author's page. | **built** |

#### Article: related

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `show_related` | boolean | on | `partials/article-related` | `skin` = `fable` | content | The "Keep reading" row of related stories under the article. | **built** |
| `related_heading` | text | `Keep reading` | `partials/article-related` | `skin` = `fable`; `show_related` = on | content | The row's heading. | read now |
| `fable_related_count` | select: `2`, `3`, `4`, `6` | `3` | `partials/article-related` | `skin` = `fable`; `show_related` = on | content | How many stories the row shows (it can only show as many as Teme relates). | new |
| `fable_related_more_link` | boolean | on | `partials/article-related` | `skin` = `fable`; `show_related` = on | content | The "All [section]" link at the right of the heading. | **built** |

#### Comments

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_comments_placement` | select: `below`, `rail` | `below` | `partials/comments-thread` | `skin` = `fable`; `show_comments` = on | content | Below the author card (the drawing) or in the rail; "hidden" is `show_comments` off. The rail is a 17rem column, a poor home for a thread: listed only so the owner can strike it. | new |
| `fable_comments_avatar` | boolean | on | `partials/comment-card` | `skin` = `fable`; `show_comments` = on | content | The initials circle beside each comment. | **built** |
| `fable_comments_author_badge` | boolean | on | `partials/comment-card` | `skin` = `fable`; `show_comments` = on | content | The "Author" badge on a credited author's comment (the drawing). | **built** |
| `comments_page_title` | text | `Comments on “:title”` | `comments` | `skin` = `fable` | content | The h1 of a later page of a thread; :title is the story's title. | read now |

#### Page

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_page_breadcrumb` | boolean | on | `page` | `skin` = `fable` | content | The breadcrumb above a page's title. | **built** |
| `fable_page_standfirst` | boolean | on | `page` | `skin` = `fable` | content | The standfirst under a page's title. | **built** |
| `show_updated` | boolean | on | `page` | `skin` = `fable` | content | The "Last updated" line under the title. | **built** |
| `fable_page_hero` | boolean | on | `page` | `skin` = `fable` | content | The featured picture under the title. | **built** |
| `fable_page_fields` | boolean | on | `page` | `skin` = `fable` | content | The custom fields set to show on a page. | **built** |
| `page_lede` | boolean | on | `page` | `skin` = `fable` | content | The first paragraph set larger. | **built** |
| `show_children` | boolean | on | `page` | `skin` = `fable` | content | The list of pages under this one. | **built** |
| `children_heading` | text | `In this section` | `page` | `skin` = `fable`; `show_children` = on | content | That list's heading. | read now |
| `show_page_comments` | boolean | on | `page` | `skin` = `fable` | content | Comments under pages, when Teme gives them a thread. | **built** |


### Archives, authors, search, 404 and errors (fieldset archive)

#### Archive: header

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_archive_breadcrumb` | boolean | on | `archive` | `skin` = `fable` | archive | The breadcrumb above the section name. | **built** |
| `fable_archive_kind_label` | boolean | on | `archive` | `skin` = `fable` | archive | The small label ("Section", "Topic", "Writer") above the title. | **built** |
| `fable_archive_description` | boolean | on | `archive` | `skin` = `fable` | archive | The section's description under the title. | **built** |
| `archive_header_image` | boolean | on | `archive` | `skin` = `fable` | archive | The section's own picture beside the header, when it has one. | **built** |
| `archive_topic_chips` | boolean | on | `archive` | `skin` = `fable` | archive | The section's topics as chips under the description. | **built** |
| `fable_archive_chip_all` | boolean | on | `archive` | `skin` = `fable`; `archive_topic_chips` = on | archive | The "All" chip that starts the row of topics. | **built** |
| `fable_archive_story_count` | boolean | on | `archive` | `skin` = `fable` | archive | The "32 stories" count beside the title (the drawing). *When Teme can: A listing knows only this page's items, never the total.* | **waits**: counts |

#### Archive: ads

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `archive_ads` | boolean | on | `archive` | `skin` = `fable`; `show_ads` = on | archive | Both advertisement slots on listings (leaderboard and in-feed). | **built** |
| `fable_archive_ad_leaderboard` | boolean | on | `archive` | `skin` = `fable`; `archive_ads` = on; `show_ads` = on | archive | The leaderboard under the header. | **built** |
| `fable_archive_ad_in_feed` | boolean | on | `archive` | `skin` = `fable`; `archive_ads` = on; `show_ads` = on | archive | The wide ad at the end of the grid. | **built** |

#### Archive: lead story

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `archive_lead_story` | boolean | on | `archive` | `skin` = `fable` | archive | The first story of page 1 set large above the grid. | **built** |
| `fable_archive_lead_standfirst` | boolean | on | `archive` | `skin` = `fable`; `archive_lead_story` = on | archive | The standfirst under the lead story's headline. | **built** |
| `fable_archive_lead_byline` | boolean | on | `archive` | `skin` = `fable`; `archive_lead_story` = on | archive | The byline under the lead story. | **built** |

#### Archive: story grid

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_archive_list_heading` | text | `All stories in :title` | `archive` | `skin` = `fable` | archive | The heading over the grid; :title is the section's name. | new |
| `archive_layout_toggle` | boolean | on | `archive` | `skin` = `fable` | archive | The reader's grid / list buttons. | **built** |
| `fable_archive_layout` | select: `grid`, `list` | `grid` | `archive` | `skin` = `fable` | archive | The layout a reader sees first (the drawing: grid). | new |
| `fable_archive_columns` | select: `3`, `4` | `3` | `archive` | `skin` = `fable` | archive | Columns of the grid on wide screens (the drawing: 3). | new |
| `fable_archive_sort_toggle` | boolean | on | `archive` | `skin` = `fable` | archive | The "Newest / Quick reads" switch (the drawing). *When Teme can: A listing has one order, newest first.* | **waits**: sort |

#### Author

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_author_breadcrumb` | boolean | on | `author` | `skin` = `fable` | archive | The breadcrumb ("Our writers / Name") above the profile. | **built** |
| `author_show_portrait` | boolean | on | `author` | `skin` = `fable` | archive | The writer's portrait or initials beside the name. | **built** |
| `fable_author_kicker` | boolean | on | `author` | `skin` = `fable` | archive | The small "Writer" label above the name. | **built** |
| `fable_author_bio` | boolean | on | `author` | `skin` = `fable` | archive | The writer's bio under the name. | **built** |
| `fable_author_role` | boolean | on | `author` | `skin` = `fable` | archive | The role line ("Animals editor") under the name. *When Teme can: Teme's Person has no role or job title.* | **waits**: author role |
| `fable_author_email` | boolean | on | `author` | `skin` = `fable` | archive | The "Email Imani" button. *When Teme can: Teme's listing subject gives no e-mail or links.* | **waits**: author e-mail and social links |
| `fable_author_facts` | boolean | on | `author` | `skin` = `fable` | archive | The facts panel (covers, with us since, training, story count). *When Teme can: Teme gives none of these; they would be custom fields on the writer.* | **waits**: author facts |
| `fable_author_stories` | boolean | on | `author` | `skin` = `fable` | archive | The writer's stories as rows under the profile. | **built** |
| `fable_author_stories_heading` | text | `Latest from :name` | `author` | `skin` = `fable`; `fable_author_stories` = on | archive | The heading over the stories; :name is the writer's name. | new |
| `fable_author_sort_toggle` | boolean | on | `author` | `skin` = `fable`; `fable_author_stories` = on | archive | The "Newest / Quick reads" switch above the stories (the drawing). *When Teme can: A listing has one order, newest first.* | **waits**: sort |

#### Search

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_search_form` | boolean | on | `search` | `skin` = `fable` | archive | The search field under the results heading. | **built** |
| `fable_search_count` | boolean | on | `search` | `skin` = `fable` | archive | The "N results" line. | **built** |
| `fable_search_snippets` | boolean | on | `partials/card` | `skin` = `fable` | archive | The matched passage shown under each result, with the words marked. | **built** |
| `search_show_sections` | boolean | on | `search` | `skin` = `fable` | archive | The section chips in the empty and no-result states. | **built** |
| `fable_search_facets` | boolean | on | `search` | `skin` = `fable` | archive | The "All" and section filter chips above the results (the drawing). *When Teme can: Teme gives no results by section.* | **waits**: counts |
| `fable_search_sort_toggle` | boolean | on | `search` | `skin` = `fable` | archive | The "Newest / Quick reads" switch (the drawing). *When Teme can: A search has one relevance order.* | **waits**: sort |
| `fable_search_most_read` | boolean | on | `search` | `skin` = `fable` | archive | "Most read this week" under the empty states (the drawing). *When Teme can: No view counts, so no popularity list.* | **waits**: counts |
| `fable_search_ad_leaderboard` | boolean | off | `search` | `skin` = `fable`; `show_ads` = on | archive | A leaderboard under the search header; the drawing has no ad here, so off. | **built** |
| `fable_search_ad_in_feed` | boolean | off | `search` | `skin` = `fable`; `show_ads` = on | archive | An ad inside the results list; the drawing has no ad here, so off. | **built** |

#### 404

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_404_big_number` | boolean | on | `notfound` | `skin` = `fable` | archive | The big "404" beside the message. | **built** |
| `fable_404_title` | text | `That page has moved, or it was never here` | `notfound` | `skin` = `fable` | archive | The 404 headline. | new |
| `fable_404_text` | text | `The link may be out of date or mistyped. Search for the story you wanted, or start again from the front page.` | `notfound` | `skin` = `fable` | archive | The line under the headline. | new |
| `fable_404_search_form` | boolean | on | `notfound` | `skin` = `fable` | archive | The search field on the not-found page. | **built** |
| `fable_404_home_button` | boolean | on | `notfound` | `skin` = `fable` | archive | The "Go to the home page" button. | **built** |
| `notfound_show_sections` | boolean | on | `notfound` | `skin` = `fable` | archive | The section chips beside the way home. | **built** |
| `fable_404_most_read` | boolean | on | `notfound` | `skin` = `fable` | archive | "Most read this week" under the message (the drawing). *When Teme can: A not-found template gets no stories and Teme keeps no view counts.* | **waits**: counts |

#### Error pages

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `fable_error_big_number` | boolean | on | `error` | `skin` = `fable` | archive | The big status number (403, 429, 500...) beside the message. | **built** |
| `fable_error_home_button` | boolean | on | `error` | `skin` = `fable` | archive | The "Go to the home page" button. | **built** |
| `fable_error_search_form` | boolean | off | `error` | `skin` = `fable` | archive | A search field on error pages; the current page has none and there is no drawing, so off. | **built** |
| `fable_error_sections` | boolean | off | `error` | `skin` = `fable` | archive | Section chips on error pages; the current page has none and there is no drawing, so off. | **built** |


## Later: Strata

Read from the WordPress theme's own options (app/Skins.php, app/strata-setup.php, the Customizer code and the skin's templates in resources/views/skins/strata). Strata is **not built** in this release; the rows prove the per-skin structure: every key starts `strata_` and is shown only while `skin` = `strata`.

### Strata settings

#### Design tokens

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_color_primary` | color | `#101013` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Rail, buttons and feature band background (Strata "primary"). Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_color_accent` | color | `#7c98f5` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | The highlight colour: ticker, links on hover, progress bar, filter buttons. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_color_link` | color | `#7c98f5` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Link colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_color_text` | color | `#17171a` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Body text colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_color_muted` | color | `#7a746e` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Secondary text colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_color_background` | color | `#ffffff` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Page background. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Strata only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `strata_font_display` | text | `"Fraunces", Georgia, serif` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Headline font (Fraunces). Skin identity. *When Teme can: A font that belongs to Strata only.* | **waits**: per-skin tokens #370/#371 |
| `strata_font_body` | text | `"Manrope", system-ui, sans-serif` | `app/Skins.php (skins/strata)` | `skin` = `strata` | site | Body font (Manrope). Skin identity. *When Teme can: A font that belongs to Strata only.* | **waits**: per-skin tokens #370/#371 |

#### Rail (header)

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_rail_tagline` | text | empty | `skins/strata/header` | `skin` = `strata` | site | The small line under the site name in the dark rail. Empty falls back to the site tagline (WordPress: egberipiribo_strata_rail_tagline). | new |
| `strata_rail_search` | boolean | on | `skins/strata/header` | `skin` = `strata` | site | The "Search stories" button in the rail and the header search overlay it opens. | new |
| `strata_rail_social` | boolean | on | `skins/strata/header` | `skin` = `strata` | site | The social and RSS icon row at the foot of the rail (the footer does not repeat it). | new |
| `strata_rail_counts` | boolean | on | `skins/strata/header` | `skin` = `strata` | site | The live story count beside each rail section and "All Stories". *When Teme can: Per-section story totals are not available from Teme data today.* | **waits**: section counts |
| `strata_rail_sections` | text | empty | `skins/strata/header` | `skin` = `strata` | site | Which categories appear as rail and drawer filter buttons; all shown by default, new ones added automatically (WordPress: one checkbox per category, egberipiribo_strata_rail_<id>). *When Teme can: A checklist of categories; stored as the hidden ones so new categories appear by default.* | **waits**: term picker (category) |
| `strata_back_to_top` | boolean | on | `skins/strata/header` | `skin` = `strata` | site | The round back-to-top button that appears after scrolling. The drawing has it; WordPress has no switch. | new |

#### Home: top stories ticker

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_home_ticker` | boolean | on | `skins/strata/home` | `skin` = `strata` | latest | The scrolling "Top stories" strip above the header zone. | new |
| `strata_home_ticker_label` | text | `Top stories` | `skins/strata/home` | `skin` = `strata`; `strata_home_ticker` = on | latest | The label on the ticker tab. | new |
| `strata_home_ticker_count` | select: `4`, `6`, `8`, `10` | `6` | `skins/strata/home` | `skin` = `strata`; `strata_home_ticker` = on | latest | How many headlines run in the ticker (the template takes the latest six). | new |

#### Home: header zone

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_home_date` | boolean | on | `skins/strata/home` | `skin` = `strata` | latest | The weekday and date line above the page title ("Monday, 16 November 2026"). *When Teme can: Needs today's date at request time; Teme gives a page no clock.* | **waits**: request-time date |
| `strata_home_story_count` | boolean | on | `skins/strata/home` | `skin` = `strata` | latest | The "N stories" count beside the title that follows the active filter. | new |

#### Home: hero cluster

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_home_hero` | boolean | on | `skins/strata/home` | `skin` = `strata` | latest | The big lead story with its side list. Off leaves the feature band and grid. | new |
| `strata_home_hero_side_count` | select: `2`, `3`, `4` | `3` | `skins/strata/home` | `skin` = `strata`; `strata_home_hero` = on | latest | How many smaller stories sit beside the lead (the template shows three). | new |

#### Home: feature band

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_home_feature` | boolean | on | `skins/strata/home` | `skin` = `strata` | latest | The dark "Long Read" band with one story and a button. | new |
| `strata_home_feature_label` | text | `Long Read` | `skins/strata/home` | `skin` = `strata`; `strata_home_feature` = on | latest | The kicker above the feature headline. | new |

#### Home: story grid

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_home_grid_title` | text | `More to Explore` | `skins/strata/home` | `skin` = `strata` | latest | The heading over the filterable grid (it becomes "Latest in <section>" while filtered). | new |
| `strata_home_grid_count` | select: `12`, `24`, `36`, `48` | `24` | `skins/strata/home` | `skin` = `strata` | latest | How many stories the grid and rail filters work from (the template loads the latest 24; the hero, side list, feature and ticker draw from the same list). | new |
| `strata_home_updated_note` | text | `Updated daily` | `skins/strata/home` | `skin` = `strata` | latest | The small note at the right of the grid heading. Empty hides it; a site that does not post daily should not promise it. | new |

#### Article: header

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_breadcrumbs` | boolean | on | `skins/strata/single` | `skin` = `strata` | content | The breadcrumb line above the headline (uses the SEO plugin trail automatically when its breadcrumbs are on). WordPress: egberipiribo_strata_breadcrumbs_enabled. | new |
| `strata_reading_progress` | boolean | on | `skins/strata/single` | `skin` = `strata` | content | The thin accent bar at the top that fills as you read. The drawing has it; WordPress has no switch. | new |

#### Share

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_share` | boolean | on | `skins/strata/single` | `skin` = `strata` | site | The share row in the byline: X, Facebook, email and copy link. WordPress: egberipiribo_strata_share_enabled. | new |

#### Article: footer

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_read_also` | boolean | on | `skins/strata/single` | `skin` = `strata` | content | The blue "Read also" callout between the story and its tags (the first related story). | new |
| `strata_related` | boolean | on | `skins/strata/single` | `skin` = `strata` | content | The "Keep Exploring" row of related stories after the article. WordPress: egberipiribo_strata_related_enabled. | new |
| `strata_related_heading` | text | `Keep Exploring` | `skins/strata/single` | `skin` = `strata`; `strata_related` = on | content | The related row heading. | new |
| `strata_related_count` | select: `4`, `8` | `4` | `skins/strata/single` | `skin` = `strata`; `strata_related` = on | content | How many related stories (the template takes four: same category first, then latest). | new |

#### Page

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_page_breadcrumbs` | boolean | on | `skins/strata/page` | `skin` = `strata` | content | The small breadcrumb above a static page title (About, Contact). | new |

#### Archive

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_archive_story_count` | boolean | on | `skins/strata/archive` | `skin` = `strata` | archive | The "N stories" count at the right of the section title. | new |
| `strata_archive_columns` | select: `3`, `4` | `4` | `skins/strata/archive` | `skin` = `strata` | archive | Cards per row on a wide screen in a category archive (the author and search grids use three). | new |

#### Author

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_author_story_count` | boolean | on | `skins/strata/author` | `skin` = `strata` | archive | The "N stories" count under the author name. | new |

#### Search

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_search_form` | boolean | on | `skins/strata/search` | `skin` = `strata` | archive | The editable search box inside the results header (separate from the rail overlay). | new |
| `strata_search_count` | boolean | on | `skins/strata/search` | `skin` = `strata` | archive | The "N results" line under the search box. | new |
| `strata_search_category` | boolean | on | `skins/strata/search` | `skin` = `strata` | archive | The coloured category label on each search result card. | new |

#### 404

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_404_search` | boolean | on | `skins/strata/404` | `skin` = `strata` | archive | The search field on the not-found page. | new |
| `strata_404_recent` | boolean | on | `skins/strata/404` | `skin` = `strata` | archive | The "Or keep reading" row of recent stories under the 404 message. | new |
| `strata_404_recent_count` | select: `4`, `8` | `4` | `skins/strata/404` | `skin` = `strata`; `strata_404_recent` = on | archive | How many recent stories the 404 page offers (the template takes four). | new |

#### Footer

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `strata_newsletter_heading` | text | `Every layer, once a week.` | `skins/strata/footer` | `skin` = `strata`; `show_newsletter` = on | site | Headline of the footer signup band (hard-coded in the Strata footer today). | new |
| `strata_newsletter_text` | text | `The best stories across every section, chosen by an editor — not an algorithm.` | `skins/strata/footer` | `skin` = `strata`; `show_newsletter` = on | site | The line under the footer signup headline (hard-coded today). | new |
| `strata_footer_about` | text | empty | `skins/strata/footer` | `skin` = `strata` | site | The paragraph under the site name in the footer. Empty shows the site tagline (WordPress: egberipiribo_strata_footer_about). | new |
| `strata_footer_copyright` | text | `© {{year}} {{site}}. All rights reserved.` | `skins/strata/footer` | `skin` = `strata` | site | The bottom bar line; [year] and [site] fill in automatically (WordPress: egberipiribo_strata_footer_copyright, basic HTML allowed). | new |


## Later: Threads

Read from the WordPress theme's own options (app/Skins.php, app/threads-setup.php, the Customizer code and the skin's templates in resources/views/skins/threads). Threads is **not built** in this release; every key starts `threads_` and is shown only while `skin` = `threads`.

### Threads settings

#### Design tokens

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_color_primary` | color | `#1a1a1a` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Ink and rule colour (Threads "fore"). Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_color_accent` | color | `#e84545` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | The red accent: tags, subscribe button, hover. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_color_link` | color | `#e84545` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Link colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_color_text` | color | `#374151` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Body text colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_color_muted` | color | `#6b6b6b` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Secondary text colour. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_color_background` | color | `#ffffff` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Page background. Skin identity in WordPress (never a per-site setting there); in Teme it needs per-skin tokens. *When Teme can: A colour that belongs to Threads only, so changing skin changes it.* | **waits**: per-skin tokens #370/#371 |
| `threads_font_display` | text | `"Libre Baskerville", Georgia, serif` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Headline font (Libre Baskerville). Skin identity. *When Teme can: A font that belongs to Threads only.* | **waits**: per-skin tokens #370/#371 |
| `threads_font_body` | text | `"Space Grotesk", system-ui, sans-serif` | `app/Skins.php (skins/threads)` | `skin` = `threads` | site | Body font (Space Grotesk). Skin identity. *When Teme can: A font that belongs to Threads only.* | **waits**: per-skin tokens #370/#371 |

#### Sidebar

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_sidebar_width` | select: `240`, `300`, `360`, `420` | `300` | `skins/threads/single, archive, author, search, page, home` | `skin` = `threads` | site | Width in pixels of the right-hand widget column on home, article, archive, author, search and page (WordPress: egberipiribo_single_sidebar_width, 240 to 420, default 300). | new |

#### Header

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_header_date` | boolean | on | `skins/threads/header` | `skin` = `threads` | site | The weekday and date line at the left of the desktop masthead. *When Teme can: Needs today's date at request time; Teme gives a page no clock.* | **waits**: request-time date |
| `threads_header_search` | boolean | on | `skins/threads/header` | `skin` = `threads` | site | The "Search archives" link and button that open the full-screen search overlay. | new |
| `threads_header_tagline` | boolean | on | `skins/threads/header` | `skin` = `threads` | site | The small spaced-out tagline under the big wordmark. | new |
| `threads_header_subscribe` | boolean | on | `skins/threads/header` | `skin` = `threads` | site | The red Subscribe button in the masthead and drawer (WordPress: egberipiribo_header_subscribe_enabled). | new |
| `threads_header_subscribe_label` | text | `Subscribe` | `skins/threads/header` | `skin` = `threads`; `threads_header_subscribe` = on | site | The button words (WordPress: egberipiribo_header_subscribe_label). | new |
| `threads_header_subscribe_url` | text | `#` | `skins/threads/header` | `skin` = `threads`; `threads_header_subscribe` = on | site | Where the button goes (WordPress: egberipiribo_header_subscribe_url; the button hides while empty). | new |

#### Search overlay

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_search_sections` | boolean | on | `skins/threads/search-modal` | `skin` = `threads` | site | The list of top sections under the search field in the overlay. | new |

#### Home: lead and columns

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_home_columns` | boolean | on | `skins/threads/home` | `skin` = `threads` | latest | The three-column index under the lead story (desktop only). | new |
| `threads_home_column_posts` | select: `3`, `4`, `5` | `4` | `skins/threads/home` | `skin` = `threads`; `threads_home_columns` = on | latest | Stories per column (the template takes four). | new |
| `threads_home_column_1` | text | empty | `skins/threads/home` | `skin` = `threads`; `threads_home_columns` = on | latest | Which category fills column 1; empty mixes the latest stories under "Home & Garden" (WordPress: egberipiribo_home_column_1_category). *When Teme can: A single category choice; the heading and link come from the chosen category.* | **waits**: term picker (category) |
| `threads_home_column_2` | text | empty | `skins/threads/home` | `skin` = `threads`; `threads_home_columns` = on | latest | Which category fills column 2; empty mixes the latest stories under "Animals & Nature" (WordPress: egberipiribo_home_column_2_category). *When Teme can: A single category choice.* | **waits**: term picker (category) |
| `threads_home_column_3` | text | empty | `skins/threads/home` | `skin` = `threads`; `threads_home_columns` = on | latest | Which category fills column 3; empty mixes the latest stories under "Life & Ideas" (WordPress: egberipiribo_home_column_3_category). *When Teme can: A single category choice.* | **waits**: term picker (category) |
| `threads_home_dedupe` | boolean | on | `skins/threads/home` | `skin` = `threads` | latest | Stops the lead, the three columns and "Also in This Issue" from repeating a story (WordPress: egberipiribo_home_dedupe_posts). | new |

#### Home: belief band

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_home_belief` | boolean | on | `skins/threads/home` | `skin` = `threads` | latest | The ruled band between columns and the issue list (WordPress: egberipiribo_home_quote_enabled). Holds the in-article ad when that is on, otherwise the manifesto. | new |
| `threads_home_belief_manifesto` | boolean | on | `skins/threads/home` | `skin` = `threads`; `threads_home_belief` = on | latest | Shows the manifesto text in the band (WordPress: egberipiribo_home_belief_manifesto_enabled). An active in-article ad takes the band instead. | new |
| `threads_home_belief_kicker` | text | `Thread believes` | `skins/threads/home` | `skin` = `threads`; `threads_home_belief` = on; `threads_home_belief_manifesto` = on | latest | The small label above the manifesto (WordPress: egberipiribo_home_quote_kicker). | new |
| `threads_home_belief_text` | text | `"Every great idea begins as curiosity. We are here for the curious."` | `skins/threads/home` | `skin` = `threads`; `threads_home_belief` = on; `threads_home_belief_manifesto` = on | latest | The big manifesto line (WordPress: egberipiribo_home_quote_text). | new |

#### Home: Also in This Issue

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_home_also_in_heading` | text | `Also in This Issue` | `skins/threads/home` | `skin` = `threads` | latest | Heading of the numbered story list (WordPress: egberipiribo_home_issue_section_heading). | new |
| `threads_home_also_in_count` | select: `0`, `5`, `10`, `15`, `20`, `30`, `50` | `10` | `skins/threads/home` | `skin` = `threads` | latest | How many stories the list shows first; 0 hides the section (WordPress: egberipiribo_home_also_in_initial, 0 to 50, default 10). | new |
| `threads_home_also_in_sidebar` | boolean | on | `skins/threads/partials/home-also-in-rows` | `skin` = `threads`; `threads_home_also_in_count` = `5` or `10` or `15` or `20` or `30` or `50` | latest | The widget column beside the list (WordPress: egberipiribo_also_in_sidebar_enabled). | new |
| `threads_home_load_more` | boolean | on | `skins/threads/partials/home-also-in-rows` | `skin` = `threads`; `threads_home_also_in_count` = `5` or `10` or `15` or `20` or `30` or `50` | latest | The "Load more" button that appends stories without a page load (WordPress: egberipiribo_home_also_in_load_more_enabled, backed by its own REST route). | new |
| `threads_home_load_more_count` | select: `1`, `3`, `5`, `10`, `20` | `3` | `skins/threads/partials/home-also-in-rows` | `skin` = `threads`; `threads_home_load_more` = on | latest | Stories added per click (WordPress: egberipiribo_home_also_in_per_load, 1 to 20, default 3). | new |
| `threads_home_load_more_label` | text | `Load more stories` | `skins/threads/partials/home-also-in-rows` | `skin` = `threads`; `threads_home_load_more` = on | latest | The button words (WordPress: egberipiribo_home_also_in_load_more_label). | new |

#### Home: newsletter band

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_home_newsletter` | boolean | on | `skins/threads/home` | `skin` = `threads`; `show_newsletter` = on | latest | The grey signup band at the foot of the home page (WordPress: egberipiribo_home_newsletter_enabled, which also needs the site newsletter switch). | new |
| `threads_home_newsletter_title` | text | `Thread. In your inbox.` | `skins/threads/home` | `skin` = `threads`; `show_newsletter` = on; `threads_home_newsletter` = on | latest | The band headline (WordPress: egberipiribo_home_newsletter_title). | new |
| `threads_home_newsletter_text` | text | `Every Monday. The full issue. Free, always.` | `skins/threads/home` | `skin` = `threads`; `show_newsletter` = on; `threads_home_newsletter` = on | latest | The line under the band headline (WordPress: egberipiribo_home_newsletter_text). | new |

#### Article

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_single_stats` | boolean | on | `skins/threads/single` | `skin` = `threads` | content | The stats row on a single post (WordPress: egberipiribo_single_stats_enabled; the setting and its composer value exist but the current Threads single template renders no stats row, so decide whether it is built or dropped). | new |
| `threads_single_sidebar` | boolean | on | `skins/threads/single` | `skin` = `threads` | content | The right-hand column on an article, showing widgets or the rectangle ad (WordPress: egberipiribo_single_sidebar_enabled). | new |

#### Comments

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_comments_intro` | text | `Your email address will not be published. Required fields are marked *` | `skins/threads/comments` | `skin` = `threads`; `show_comments` = on | content | The note above the comment form (WordPress: egberipiribo_comments_intro_text). | new |
| `threads_comments_cookies` | boolean | on | `skins/threads/comments` | `skin` = `threads`; `show_comments` = on | content | The "Save my name and email" checkbox on the comment form (WordPress: egberipiribo_comments_show_cookies). | new |

#### Archive

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_archive_featured` | boolean | on | `skins/threads/archive` | `skin` = `threads` | archive | The large "Latest in this archive" story above the ruled list. | new |
| `threads_archive_story_count` | boolean | on | `skins/threads/archive` | `skin` = `threads` | archive | The "N stories" line under the archive title. | new |
| `threads_archive_row_tags` | boolean | on | `skins/threads/archive` | `skin` = `threads` | archive | The first two #tags on each archive row. | new |

#### Author

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_author_profile` | boolean | on | `skins/threads/author` | `skin` = `threads` | archive | The "Profile" box with topics, location and article count above the list. | new |
| `threads_author_links` | boolean | on | `skins/threads/author` | `skin` = `threads` | archive | The website and social link buttons at the right of the author header. | new |

#### Search

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_search_form` | boolean | on | `skins/threads/search` | `skin` = `threads` | archive | The "Search again" box in the results header. | new |

#### 404

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_404_picks` | boolean | on | `skins/threads/404` | `skin` = `threads` | archive | The "From the archive" column of random stories beside the 404 message. | new |
| `threads_404_picks_heading` | text | `From the archive` | `skins/threads/404` | `skin` = `threads`; `threads_404_picks` = on | archive | The picks heading. | new |
| `threads_404_picks_count` | select: `3`, `5` | `3` | `skins/threads/404` | `skin` = `threads`; `threads_404_picks` = on | archive | How many random stories the 404 page offers (the composer takes three). | new |

#### Subscribe page

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_subscribe_kicker` | text | `A Letter from the Editor` | `skins/threads/subscribe` | `skin` = `threads` | content | The red kicker above the Subscribe page title (hard-coded today). | new |
| `threads_subscribe_heading` | text | `Join the Sunday Edition` | `skins/threads/subscribe` | `skin` = `threads` | content | Headline of the signup box on the Subscribe page (hard-coded today). | new |
| `threads_subscribe_text` | text | `Join a quiet corner of the internet. Enter your email address to receive our next essay.` | `skins/threads/subscribe` | `skin` = `threads` | content | The line under that headline (hard-coded today). | new |

#### Footer

| Key | Type | Default | Read in | Visible when | Fieldset | Why | Status |
|---|---|---|---|---|---|---|---|
| `threads_footer_network` | boolean | on | `skins/threads/footer` | `skin` = `threads` | site | The dark "Our publications" strip above the footer (WordPress: egberipiribo_footer_network_enabled). | new |
| `threads_footer_network_title` | text | `Our publications:` | `skins/threads/footer` | `skin` = `threads`; `threads_footer_network` = on | site | The strip label (WordPress: egberipiribo_footer_network_title). | new |
| `threads_footer_network_new_tab` | boolean | off | `skins/threads/footer` | `skin` = `threads`; `threads_footer_network` = on | site | Opens publication links in a new tab (WordPress: egberipiribo_footer_network_new_tab). | new |
| `threads_footer_network_links` | text | empty | `skins/threads/footer` | `skin` = `threads`; `threads_footer_network` = on | site | The up to five sister publications: PawPrint, The Garden Thread, At Table, The Travel Letter, Well (WordPress: five label and URL pairs, egberipiribo_footer_network_item_1_label ... _5_url). *When Teme can: Pairs of label and URL; the strip hides while the list is empty.* | **waits**: list of links #373 |
| `threads_footer_tagline` | text | `Issue 142 · Published weekly since 2019.` | `skins/threads/footer` | `skin` = `threads` | site | The line under the footer wordmark (WordPress: egberipiribo_footer_tagline). | new |
| `threads_footer_copyright` | text | `© {{year}} {{site}}. Published by <a href="https://kraftysprouts.com">Krafty Sprouts Media, LLC</a>. All rights reserved.` | `skins/threads/footer` | `skin` = `threads` | site | The bottom bar line; [year] and [site] fill in automatically, links allowed (WordPress: egberipiribo_footer_copyright). The default names the publisher, so a new site will want to change it. | new |


## What cannot be a setting yet

- **A free number** (items per page, columns, how many of a block): #373; the selects of fixed steps stand in.
- **An image** (a logo, a default picture, a fallback portrait): #373.
- **A list of links** (social networks, "our network", footer columns): #373; menus carry the lists today (`social`, `footer`, `network`).
- **Per-skin tokens** (colours, fonts, a second accent): #370 and #371.
- **A request-time date** (the date line), **section counts**, **view counts** ("most read"), **sorts**: Teme gives a template none of these (FINDINGS 20).
- **Setting groups** and a **category or story picker**: neither exists; regions with Featured posts cards carry the pickers (edited in the admin, not under Design).

## Adopting the proposal

1. The owner marks what to adopt in `docs/options.json` (delete a row, change a default or a label), then `node scripts/build-options.mjs` rewrites this file and re-checks it.
2. For each adopted row: add the entry to `teme-theme.json`; for a *new* row add the one read line to the template named under "Read in". The kit cannot set a theme setting in a test (FINDINGS 20), so each template is tested by its rendered output with the default.
3. Adopt in batches by fieldset; the defaults reproduce the drawings, so any batch can ship alone.
