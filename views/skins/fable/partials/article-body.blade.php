{{--
    The body core built, in a .prose block, and the rail's "In this story" list made from it.

    Contents: core writes the body's h2 headings with no id, so this gives each h2 one (from its words, kept unique;
    an id core already wrote is kept) and sets the rail's list (the `toc` section of the layout) from them. The list
    shows when $contents is on and the body has at least $contentsMin such headings. fable.js marks the entry the
    reader has reached, from [data-toc-link].

    In-article ad: when the body is long enough the first in-article ad (the ad-in-article region) sits inside it, after
    $after top-level blocks, so the body is cut between two blocks (read from the HTML's tags, never inside one); a
    shorter body, or one the scan cannot read, gets the ad after it instead.

    Inline related card: when the article has one to offer ($inline, a related entry: ['item' => ListingItem, ...]), it
    sits inside the body after its second top-level block (the drawing's place), when the body has at least three;
    a shorter body, or one the scan cannot read, gets the card after it. When both the card and the ad cut the body,
    the card comes first.

    Takes $html (the body), $lede (whether the first paragraph is set as the lede), $ads (whether to draw the ad),
    $after, $min (the fewest top-level blocks that earn a cut), $contents and $contentsMin (the template reads show_contents,
    contents_min_headings, article_lede and the ad switches and passes them; this partial reads no setting itself),
    and optionally $inline (the related entry for the card, or null).

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $toc = [];
    if (($contents ?? true) && str_contains($html, '<h2')) {
        $taken = ['main', 'top', 'comments', 'comments-title', 'respond', 'newsletter', 'related-title', 'children-title'];
        if (preg_match_all('/\bid=(["\'])(.+?)\1/i', $html, $known)) {
            $taken = array_merge($taken, $known[2]);
        }
        $html = preg_replace_callback('/<h2\b([^>]*)>(.*?)<\/h2>/is', static function (array $heading) use (&$toc, &$taken): string {
            $label = trim(html_entity_decode(strip_tags($heading[2]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($label === '') {
                return $heading[0];
            }
            if (preg_match('/\bid=(["\'])(.+?)\1/i', $heading[1], $own)) {
                $toc[] = ['id' => html_entity_decode($own[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'label' => $label];

                return $heading[0];
            }
            $base = \Illuminate\Support\Str::slug($label) ?: 'section';
            $id = $base;
            for ($n = 2; in_array($id, $taken, true); $n++) {
                $id = $base.'-'.$n;
            }
            $taken[] = $id;
            $toc[] = ['id' => $id, 'label' => $label];

            return '<h2'.$heading[1].' id="'.e($id).'">'.$heading[2].'</h2>';
        }, $html) ?? $html;
    }

    $ends = [];
    $depth = 0;
    $ok = true;
    $void = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'source', 'track', 'wbr'];
    $inlineEntry = $inline ?? null;
    if ((($ads ?? true) || $inlineEntry !== null) && preg_match_all('/<(\/?)([a-zA-Z][a-zA-Z0-9-]*)\b[^>]*?(\/?)>/s', $html, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        foreach ($tags as $tag) {
            $name = strtolower($tag[2][0]);
            $end = $tag[0][1] + strlen($tag[0][0]);
            if ($tag[1][0] === '/') {
                $depth--;
                if ($depth < 0) {
                    $ok = false;
                    break;
                }
                if ($depth === 0) {
                    $ends[] = $end;
                }
            } elseif ($tag[3][0] === '/' || in_array($name, $void, true)) {
                if ($depth === 0) {
                    $ends[] = $end;
                }
            } else {
                $depth++;
            }
        }
    }
    // Where the body is cut, in order, and what goes at each cut ('inline' = the related card, 'ad' = the ad).
    $marks = [];
    $atEnd = [];
    $readable = $ok && $depth === 0;
    if ($inlineEntry !== null) {
        if ($readable && count($ends) >= 3) {
            $marks[] = [$ends[1], 'inline'];
        } else {
            $atEnd[] = 'inline';
        }
    }
    if ($ads ?? true) {
        $adAt = $readable && count($ends) >= max(3, (int) ($min ?? 8)) ? ($ends[min((int) ($after ?? 4), count($ends) - 2) - 1] ?? null) : null;
        if ($adAt !== null) {
            $marks[] = [$adAt, 'ad'];
        } else {
            $atEnd[] = 'ad';
        }
    }
    usort($marks, static fn (array $one, array $two): int => [$one[0], $one[1] === 'inline' ? 0 : 1] <=> [$two[0], $two[1] === 'inline' ? 0 : 1]);

    // The body in pieces, each with what follows it.
    $pieces = [];
    $from = 0;
    foreach ($marks as [$at, $kind]) {
        if ($pieces !== [] && $at === $from) {
            // Two things at one cut: the second follows the first, with no empty block between.
            $pieces[count($pieces) - 1]['then'][] = $kind;

            continue;
        }
        $pieces[] = ['html' => substr($html, $from, $at - $from), 'then' => [$kind]];
        $from = $at;
    }
    $pieces[] = ['html' => substr($html, $from), 'then' => $atEnd];
    $proseClass = 'prose'.(($lede ?? true) ? ' prose-lede' : '');
@endphp
@foreach ($pieces as $piece)
    <div class="{{ $loop->first ? $proseClass : 'prose prose-cont' }}">{!! $piece['html'] !!}</div>
    @foreach ($piece['then'] as $kind)
        @if ($kind === 'inline')
            @include('theme::skins.fable.partials.article-inline-related', ['entry' => $inlineEntry])
        @else
            @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-article', 'class' => 'my-10'])
        @endif
    @endforeach
@endforeach
@if (count($toc) >= max(1, (int) ($contentsMin ?? 2)))
    {{-- The rail is drawn from the layout once the page is, so it can be filled from here. --}}
    @section('toc')
        <nav class="px-6 mt-7" aria-label="{{ __('In this story') }}">
            <p class="rail-label mb-2.5">{{ __('In this story') }}</p>
            @foreach ($toc as $entry)
                <a href="#{{ $entry['id'] }}" class="toc-link" data-toc-link aria-current="false">{{ $entry['label'] }}</a>
            @endforeach
        </nav>
    @endsection
@endif
