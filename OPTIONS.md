# OPTIONS: the theme settings Fable should offer

For the owner to review. The Fable drawings have no list of options, so this is what an administrator would want to change, read
from the drawings and from the choices each template already makes. **None of this is built.** Fable ships with each default below
fixed; every key without a † is already read in one place at the top of its template as `$themeSettings['key'] ?? <default>`, so accepting an
option means adding the entry to `teme-theme.json` and nothing else (no template changes). A key marked † is a new proposal that no
template reads yet: it needs one `$themeSettings[...] ?? default` line at the top of the template named in **Where**.

How to read the tables:

- **Type** is what Teme can store today: `select`, `boolean`, `text` or `color`. Teme has no number type, so a count is a `select`
  of the few sensible values (FINDINGS #7).
- **Fieldset** is one of the four Teme offers (`site`, `latest`, `content`, `archive`).
- **Default** is what Fable does now, taken from the drawing.
- **Where** names the template that reads the key.
- Settings marked **(visible when)** should only show while their parent is on (`visible_when`).
- Nothing here changes the brand colour: Fable's rail colour is the site's accent, set under Settings, Design, which already works.

## Site (fieldset `site`)

| Key | Type | Default | Where | Why an administrator wants it |
|---|---|---|---|---|
| `skin` | select: `fable` (later `strata`, `threads`) | `fable` | every dispatcher in `views/templates` | Chooses the skin once more skins ship. Needs the token wall solved first (FINDINGS #6). |
| `show_ads` † | boolean | on | ad slots (`partials/ad`) | One switch that hides every ad region without emptying them, for a clean page while testing or for a sponsor-free week. |
| `show_rail_tagline` † | boolean | on | `partials/rail` | The tagline under the wordmark is long on some sites. |
| `search_placeholder` | text | "Search stories, sections and writers" | search panel, search and 404 forms | Sites that are not stories-and-writers want their own words. |
| `footer_newsletter_heading` | text | "Never miss a story." | `partials/footer` | The sign-up band's headline. |
| `footer_newsletter_text` | text | "The best stories of the week, picked by an editor and sent once. You can leave in one click." | `partials/footer` | The sign-up band's line. The form itself is the `newsletter` region. |
| `footer_blurb` | text | the site's tagline | `partials/footer` | A longer "about" line than the tagline. |
| `back_to_top` † | boolean | on | `layout` | Some editors dislike a floating button. |
| `pattern` | select: `none`, `sections`, `pets`, `home`, `food` | `none` | `partials/home-pattern`, solid brand areas | The pattern samples' repeated icon tile over solid areas. Off by default because the seven main drawings are solid. |
| `pattern_strength` | select: `subtle`, `medium`, `bold` **(visible when `pattern` is not none)** | `subtle` | same | How visible the tile is. |
| `pattern_density` | select: `sparse`, `regular`, `dense` **(visible when `pattern` is not none)** | `regular` | same | How tightly it repeats. |
| `pattern_accent` | color **(visible when `pattern` is not none)** | empty | same | One icon in seven takes this colour (the second colour of pattern sample 2). |

Today the pattern is applied to the front page's long read only; making it site-wide (rail, small-screen header, footer, as the
samples do) means moving `home-pattern` to a shared partial, then reading `pattern` in the rail, header and footer.

## Latest posts (fieldset `latest`): the front page and the posts page

| Key | Type | Default | Where | Why |
|---|---|---|---|---|
| `front_title` | text | "Today on :site" | `home` | The front page's one `h1`. |
| `posts_title` | text | "Latest stories" | `home` | The title of a posts page that has none of its own. |
| `grid_title` | text | "More to explore" | `home` | The heading over the story grid. |
| `top_stories_count` | select: 0, 3, 4, 5 | 4 | `home` (the strip above the page) | 0 removes the "Top stories" strip. |
| `also_today_count` | select: 0, 3, 4 | 3 | `home` | How many newest stories fill "Also today" when none are pinned. |
| `show_story_count` | boolean | on | `home` | "N stories on this page" under the title. |
| `show_layout_toggle` | boolean | on | `home` | The reader's grid/list buttons. |
| `feed_layout` † | select: `grid`, `list` | `grid` | `home`, `archive` (via `layoutToggle`) | The layout a reader sees first; their own choice is kept on their device. New proposal: today it is fixed to grid. |
| `feed_ad_after` | select: 4, 8, 12 | 4 | `home` | Where the in-feed ad sits in the grid. |

## Content (fieldset `content`): articles, pages, comments pages

| Key | Type | Default | Where | Why |
|---|---|---|---|---|
| `show_reading_progress` | boolean | on | `article` | The thin bar at the top. |
| `show_share` | boolean | on | `article` | Share links and copy-link, top and bottom. |
| `show_reading_time` | boolean | on | `article` | "N min read" in the byline. |
| `show_contents` | boolean | on | `article`, `page` | The rail's "In this story" list. |
| `contents_min_headings` | select: 2, 3, 4 **(visible when `show_contents`)** | 2 | `article`, `page` | The fewest `h2` headings that earn a list. |
| `article_lede` | boolean | on | `article` | Sets the first paragraph larger, as the drawing's lede. |
| `show_read_next` | boolean | on | `article` | The "Read next" strip (the first related story). |
| `show_tags` | boolean | on | `article` | Tag chips. |
| `show_author_card` | boolean | on | `article` | The author card with bio. |
| `show_related` | boolean | on | `article` | The "Keep reading" row. |
| `related_heading` | text **(visible when `show_related`)** | "Keep reading" | `article` | The row's heading. |
| `show_comments` | boolean | on | `article` | Draws the thread when core gives one. |
| `ad_in_article_after` | select: 3, 4, 6, 8 | 4 | `article` | After how many blocks the in-article ad goes. |
| `ad_in_article_min` | select: 6, 8, 10 | 8 | `article` | The fewest blocks a body needs before it is cut for an ad. |
| `page_lede` | boolean | on | `page` | The first paragraph larger. |
| `show_updated` | boolean | on | `page` | "Last updated". |
| `show_children` | boolean | on | `page` | The "In this section" list of child pages. |
| `children_heading` | text **(visible when `show_children`)** | "In this section" | `page` | The list's heading. |
| `show_page_comments` | boolean | on | `page` | Comments under pages, when core allows them. |
| `comments_page_title` | text | "Comments on “:title”" | `comments` | The `h1` of a later page of a thread. |

## Archive (fieldset `archive`): sections, topics, authors, search, 404

| Key | Type | Default | Where | Why |
|---|---|---|---|---|
| `archive_lead_story` | boolean | on | `archive` | The first story on page 1 set large above the grid. |
| `archive_layout_toggle` | boolean | on | `archive` | The reader's grid/list buttons. |
| `archive_topic_chips` | boolean | on | `archive` | A section's child topics as chips under its title. |
| `archive_header_image` | boolean | on | `archive` | The subject's image beside the title. |
| `archive_ads` | boolean | on | `archive` | The leaderboard and in-feed slots on archives (a finer switch than `show_ads`). |
| `author_show_portrait` | boolean | on | `author` | The author's portrait, or initials. |
| `search_show_sections` | boolean | on | `search` | Section chips in the empty states. |
| `notfound_show_sections` | boolean | on | `notfound` | Section chips on the 404. |

## What cannot be a setting yet

These would be useful but need a Teme feature first (FINDINGS has the detail):

- **A number** for items per page, columns and counts (#7): the selects above stand in.
- **A logo** (image) and **a list** (social links, "our network"): menus carry the lists; no image setting exists.
- **Per-skin tokens** (#6): a `skin` setting can switch templates but not the colours and fonts that belong to a skin.
- **A category or story picker** for "Also today", "The long read" and "From the sections": regions with Featured posts cards
  carry these, edited in the admin, not under Design.

## Suggested grouping

Under **Settings, Design** (the four fieldsets): Site gets the skin, footer words and the pattern; Latest posts gets the front page's
titles, counts and layout; Content gets everything an article or page shows; Archive gets what sections, authors, search and the
404 show. About 55 keys in all; none is required, every one has a default.
