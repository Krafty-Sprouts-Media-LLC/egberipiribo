{{--
    Fable: the home page (the latest stories), drawn for the front page and its numbered pages, and for a posts page
    that is not the front. Page 1 draws, top to bottom: the top stories strip (above <main>), the title, the cover story
    with its overlapping headline card, "Also today" (three small stories), an advertisement, "The long read", the
    grid "More to explore" with its pages, "From the sections", and "Most read" beside an advertisement. A numbered
    page (2 and on) draws the title and the grid only.

    Takes $page (LatestView: ->lead, ->featured, ->feed with ->items, ->pagination and ->subject), $site and
    $isFront. Four blocks are filled by the site's editors through regions, drawn by the theme's featured-posts card
    (views/cards/featured-posts.blade.php) and shaped by the wrapper class around each (see fable-home.css):
    home-also-today (replaces the three stories drawn from the pinned posts), home-long-read, home-sections (up to
    four lists), home-most-read. The advertisement slots are the regions ad-leaderboard, ad-in-feed and
    ad-rectangle; each slot hides itself while empty.

    Settings this template reads (once, below; the default is the drawing's). A switch that is off draws nothing of
    its block, wrapper included; a sub-option is ignored while its parent switch is off. The wrapper classes
    no-kicker, no-thumb and no-rank tell fable-home.css to leave a part out of an editor's region list:
      front_title          text, "Today on :site"  the title on the home page; :site is the site's name
      posts_title          text, "Latest stories"  the title of a posts page that has no title of its own
      grid_title           text, "More to explore" the heading above the grid of stories
      top_stories_count    number, 4               how many stories the top stories strip lists (0 hides it)
      also_today_count     number, 3               how many of the newest stories "Also today" draws when none are pinned
      show_story_count     on/off, on              "N stories on this page" beside the title
      show_layout_toggle   on/off, on              the grid / list switch above the grid (it needs JavaScript)
      feed_ad_after        number, 4               the grid shows the in-feed advertisement after this many stories
      pattern              none, sections, pets, home or food, none: an icon pattern laid over the long read block
      pattern_strength     subtle, medium or bold, subtle: how visible the pattern's icons are
      pattern_density      sparse, regular or dense, regular: how close together the pattern's icons sit
      pattern_accent       colour #rrggbb or empty, empty: one icon in seven takes this colour, the rest are white
      fable_home_title_visible      on/off, on  the title is seen; off hides it but keeps the page's one h1 for assistive technology
      fable_home_top_stories        on/off, on  the top stories strip above the page
      fable_home_top_stories_rank   on/off, on  the big rank numbers before each headline of the strip
      fable_home_cover              on/off, on  the cover story (its story then shows only in the strip)
      fable_home_cover_kicker       on/off, on  the section name above the cover headline
      fable_home_cover_standfirst   on/off, on  the standfirst under the cover headline
      fable_home_cover_byline       on/off, on  the byline under the cover story
      fable_home_also_today         on/off, on  "Also today" and the editor's region that replaces it. Off: the newest
                                                stories it would have taken return to the grid; pinned ones show only in the strip
      fable_home_also_today_kicker  on/off, on  the section name above each small headline (editor's list too)
      fable_home_also_today_thumb   on/off, on  the small square picture beside each headline (editor's list too)
      fable_home_ad_leaderboard     on/off, on  the ad-leaderboard slot under "Also today" (show_ads is the master, in partials/ad)
      fable_home_ad_in_feed         on/off, on  the ad-in-feed slot inside the grid
      fable_home_ad_rectangle       on/off, on  the ad-rectangle slot beside "Most read"
      fable_home_long_read          on/off, on  the long read block (region home-long-read)
      fable_home_grid               on/off, on  the grid "More to explore" with its layout switch, in-feed slot and pages
      fable_home_sections           on/off, on  "From the sections" (region home-sections)
      fable_home_most_read          on/off, on  "Most read" (region home-most-read)
      fable_home_most_read_rank     on/off, on  the rank numbers beside each "Most read" story

    @package egberipiribo
    @since   0.1.0
--}}
@extends('theme::skins.fable.layout')

@php
    $frontTitle = $themeSettings['front_title'] ?? __('Today on :site', ['site' => $site->name]);
    $postsTitle = $themeSettings['posts_title'] ?? __('Latest stories');
    $gridTitle = $themeSettings['grid_title'] ?? __('More to explore');
    $topStoriesCount = (int) ($themeSettings['top_stories_count'] ?? 4);
    $alsoTodayCount = (int) ($themeSettings['also_today_count'] ?? 3);
    $showStoryCount = (bool) ($themeSettings['show_story_count'] ?? true);
    $showLayoutToggle = (bool) ($themeSettings['show_layout_toggle'] ?? true);
    $feedAdAfter = max(1, (int) ($themeSettings['feed_ad_after'] ?? 4));
    $pattern = $themeSettings['pattern'] ?? 'none';
    $patternStrength = $themeSettings['pattern_strength'] ?? 'subtle';
    $patternDensity = $themeSettings['pattern_density'] ?? 'regular';
    $patternAccent = $themeSettings['pattern_accent'] ?? '';

    // A sub-option counts only while its parent switch is on.
    $titleVisible = (bool) ($themeSettings['fable_home_title_visible'] ?? true);
    $showTopStories = (bool) ($themeSettings['fable_home_top_stories'] ?? true);
    $showTopStoriesRank = $showTopStories && (bool) ($themeSettings['fable_home_top_stories_rank'] ?? true);
    $showCover = (bool) ($themeSettings['fable_home_cover'] ?? true);
    $showCoverKicker = $showCover && (bool) ($themeSettings['fable_home_cover_kicker'] ?? true);
    $showCoverStandfirst = $showCover && (bool) ($themeSettings['fable_home_cover_standfirst'] ?? true);
    $showCoverByline = $showCover && (bool) ($themeSettings['fable_home_cover_byline'] ?? true);
    $showAlsoToday = (bool) ($themeSettings['fable_home_also_today'] ?? true);
    $showAlsoTodayKicker = $showAlsoToday && (bool) ($themeSettings['fable_home_also_today_kicker'] ?? true);
    $showAlsoTodayThumb = $showAlsoToday && (bool) ($themeSettings['fable_home_also_today_thumb'] ?? true);
    $showAdLeaderboard = (bool) ($themeSettings['fable_home_ad_leaderboard'] ?? true);
    $showAdInFeed = (bool) ($themeSettings['fable_home_ad_in_feed'] ?? true);
    $showAdRectangle = (bool) ($themeSettings['fable_home_ad_rectangle'] ?? true);
    $showLongRead = (bool) ($themeSettings['fable_home_long_read'] ?? true);
    $showGrid = (bool) ($themeSettings['fable_home_grid'] ?? true);
    $showSections = (bool) ($themeSettings['fable_home_sections'] ?? true);
    $showMostRead = (bool) ($themeSettings['fable_home_most_read'] ?? true);
    $showMostReadRank = $showMostRead && (bool) ($themeSettings['fable_home_most_read_rank'] ?? true);

    $title = $isFront ? $frontTitle : ($page->feed->subject?->title ?? $postsTitle);

    // The stories on this page. The lead and the pinned stories exist on page 1 only.
    $lead = $page->lead;
    $pinned = array_values($page->featured);
    $feed = array_values($page->feed->items);
    $firstPage = $lead !== null;
    $storyCount = ($firstPage ? 1 : 0) + count($pinned) + count($feed);

    // With the title hidden and no count beside it, the header keeps only the (screen-reader) h1 and a top space.
    $headerShowsSomething = $titleVisible || ($showStoryCount && $storyCount > 0);

    // "Also today": the pinned stories; with none pinned, the newest of the feed, which the grid then leaves out.
    // With "Also today" switched off nothing takes them, so they stay in the grid.
    $alsoToday = [];
    $gridItems = $feed;
    if ($firstPage && $showAlsoToday) {
        if ($pinned !== []) {
            $alsoToday = $pinned;
        } else {
            $alsoToday = array_slice($feed, 0, max(0, $alsoTodayCount));
            $gridItems = array_slice($feed, count($alsoToday));
        }
    }

    // The top stories strip: the lead, the pinned stories, then the newest of the feed.
    $topStories = $firstPage && $showTopStories ? array_slice([$lead, ...$pinned, ...$feed], 0, max(0, $topStoriesCount)) : [];

    // The icon pattern, one tile as a CSS value (empty while the pattern is off).
    $patternTile = trim(view('theme::skins.fable.partials.home-pattern', [
        'pattern' => $pattern,
        'strength' => $patternStrength,
        'density' => $patternDensity,
        'accent' => $patternAccent,
    ])->render());
@endphp

@section('template', 'latest')

@section('before')
    @if ($topStories !== [])
        {{-- Top stories: ranked, no marquee --}}
        <section class="border-b border-rule" aria-labelledby="top-stories-label">
            <div class="wrap flex items-center gap-x-8 overflow-x-auto py-3.5 [scrollbar-width:none]">
                <p id="top-stories-label" class="shrink-0 kicker text-ink">{{ __('Top stories') }}</p>
                <ol class="flex gap-x-8">
                    @foreach ($topStories as $item)
                        <li class="story shrink-0 flex items-baseline gap-2.5 max-w-[19rem]">
                            @if ($showTopStoriesRank)
                                <span class="rank text-[1.375rem]" aria-hidden="true">{{ $loop->iteration }}</span>
                            @endif
                            <span class="hed text-[.9375rem] font-semibold leading-snug [text-wrap:wrap] line-clamp-2"><a href="{{ $item->url }}">{{ $item->title }}</a></span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif
@endsection

@section('main')
    {{-- The title, and how many stories this page holds --}}
    <header class="{{ $headerShowsSomething ? 'pt-8 pb-6 flex flex-wrap items-end justify-between gap-x-8 gap-y-3' : 'pt-8' }}">
        <h1 class="hed hed-page{{ $titleVisible ? '' : ' sr-only' }}">{{ $title }}</h1>
        @if ($showStoryCount && $storyCount > 0)
            <p class="meta pb-1.5">{{ trans_choice(':count story on this page|:count stories on this page', $storyCount, ['count' => $storyCount]) }}</p>
        @endif
    </header>

    @if ($storyCount === 0)
        {{-- Nothing published yet --}}
        <div class="py-14 max-w-[30rem]">
            <p class="hed hed-lg">{{ __('No stories yet') }}</p>
            <p class="deck mt-2.5">{{ __('Our writers are working on it. Check back soon.') }}</p>
        </div>
    @endif

    @if ($firstPage && $showCover)
        {{-- Cover story: the headline card sits over the picture --}}
        <article class="story" aria-label="{{ __('Cover story') }}">
            @if ($lead->image !== null)
                <div class="media aspect-[4/3] sm:aspect-[21/10]">
                    <x-teme::image :id="$lead->image->id" context="cover" :priority="true" :alt="$lead->image->alt" :decorative="$lead->image->decorative" />
                </div>
            @endif
            <div class="relative bg-white {{ $lead->image !== null ? 'pt-5 sm:-mt-24 sm:mr-[12%] lg:mr-[30%] sm:pt-7 sm:pr-9 sm:rounded-tr-[var(--radius-box)]' : 'border-t border-ink pt-6' }}">
                @if ($showCoverKicker && $lead->kicker !== null)
                    <p class="kicker">{{ $lead->kicker->name }}</p>
                @endif
                <h2 class="hed hed-xl mt-2.5"><a href="{{ $lead->url }}">{{ $lead->title }}</a></h2>
                @if ($showCoverStandfirst && $lead->standfirst !== null && $lead->standfirst !== '')
                    <p class="deck text-[1.125rem] mt-3.5 max-w-[38rem]">{{ $lead->standfirst }}</p>
                @endif
                @if ($showCoverByline)
                    <p class="meta mt-3.5">@include('theme::skins.fable.partials.byline', ['authors' => $lead->authors, 'publishedText' => $lead->publishedText, 'published' => $lead->published])</p>
                @endif
            </div>
        </article>
    @endif

    @if ($firstPage && $showAlsoToday)
        {{-- Also today: the pinned stories, or the newest of the feed. An editor's list in the region replaces it. --}}
        @if ($alsoToday !== [])
            <section class="home-also-today grid md:grid-cols-3 gap-x-8 gap-y-6 mt-9 pt-7 border-t border-ink" aria-label="{{ __('Also today') }}">
                @foreach ($alsoToday as $item)
                    @php
                        $thumb = $showAlsoTodayThumb && $item->image !== null;
                    @endphp
                    <article class="story grid {{ $thumb ? 'grid-cols-[minmax(0,1fr)_6rem]' : 'grid-cols-1' }} gap-4 items-start">
                        <div>
                            @if ($showAlsoTodayKicker && $item->kicker !== null)
                                <p class="kicker">{{ $item->kicker->name }}</p>
                            @endif
                            <h2 class="hed hed-sm mt-1.5"><a href="{{ $item->url }}">{{ $item->title }}</a></h2>
                            <p class="meta mt-2">@include('theme::skins.fable.partials.byline', ['authors' => $item->authors, 'publishedText' => $item->publishedText, 'published' => $item->published])</p>
                        </div>
                        @if ($thumb)
                            <div class="media aspect-square"><x-teme::image :id="$item->image->id" context="square" :decorative="true" /></div>
                        @endif
                    </article>
                @endforeach
            </section>
        @endif
        <div class="home-slot region-also-today{{ $showAlsoTodayKicker ? '' : ' no-kicker' }}{{ $showAlsoTodayThumb ? '' : ' no-thumb' }}"><x-teme::region name="home-also-today" /></div>
    @endif

    @if ($firstPage)
        {{-- Advertisement: leaderboard --}}
        @if ($showAdLeaderboard)
            @include('theme::skins.fable.partials.ad', ['region' => 'ad-leaderboard', 'class' => 'mt-10'])
        @endif

        {{-- The long read: the brand colour carries it --}}
        @if ($showLongRead)
            <div class="home-slot region-long-read on-rail{{ $patternTile !== '' ? ' patterned' : '' }}"@if ($patternTile !== '') style="--fable-pattern: {{ $patternTile }}"@endif>
                <x-teme::region name="home-long-read" />
            </div>
        @endif
    @endif

    {{-- The grid: every other story of this page, with the layout switch --}}
    @if ($showGrid && $gridItems !== [])
        <section class="pt-12 pb-4" aria-labelledby="grid-title" x-data="layoutToggle">
            <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 border-t border-ink pt-5">
                <h2 id="grid-title" class="hed hed-lg">{{ $gridTitle }}</h2>
                @if ($showLayoutToggle)
                    <div class="seg hidden sm:inline-flex" role="group" aria-label="{{ __('Layout') }}" x-cloak>
                        <button type="button" :aria-pressed="isGrid" @click="setGrid" aria-label="{{ __('Grid layout') }}">@include('theme::icons.squares-four', ['class' => 'text-base'])</button>
                        <button type="button" :aria-pressed="isList" @click="setList" aria-label="{{ __('List layout') }}">@include('theme::icons.rows', ['class' => 'text-base'])</button>
                    </div>
                @endif
            </div>

            <div class="home-feed mt-7" :class="listClass">
                @foreach ($gridItems as $item)
                    @include('theme::skins.fable.partials.card', ['item' => $item, 'level' => 3, 'bind' => ':class="rowClass"'])
                    @include('theme::skins.fable.partials.between', ['after' => $loop->iteration, 'last' => $loop->last])
                    @if ($showAdInFeed && ($loop->iteration === $feedAdAfter || ($loop->last && $loop->count < $feedAdAfter)))
                        {{-- Advertisement: in the feed, across the row in both layouts --}}
                        @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-feed', 'class' => 'home-feed-ad col-span-full'])
                    @endif
                @endforeach
            </div>

            @include('theme::skins.fable.partials.pagination', ['pagination' => $page->feed->pagination])
        </section>
    @elseif ($showGrid && ($page->feed->pagination->previous !== null || $page->feed->pagination->next !== null))
        @include('theme::skins.fable.partials.pagination', ['pagination' => $page->feed->pagination])
    @endif

    @if ($firstPage)
        {{-- From the sections: up to four lists, each filed under a section by the editor --}}
        @if ($showSections)
            @include('theme::skins.fable.partials.pattern', ['area' => 'sections', 'mode' => 'band'])
            <div class="home-slot region-sections"><x-teme::region name="home-sections" /></div>
        @endif

        {{-- Most read: an editor's ranked list, with the last advertisement beside it. The frame goes when both are off. --}}
        @if ($showMostRead || $showAdRectangle)
            <div class="home-most{{ $showMostRead ? '' : ' home-most-solo' }}">
                @if ($showMostRead)
                    <div class="home-slot region-most-read{{ $showMostReadRank ? '' : ' no-rank' }}"><x-teme::region name="home-most-read" /></div>
                @endif
                @if ($showAdRectangle)
                    @include('theme::skins.fable.partials.ad', ['region' => 'ad-rectangle'])
                @endif
            </div>
        @endif
    @endif
@endsection
