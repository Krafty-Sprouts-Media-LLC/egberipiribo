{{--
    The body core built, in a .prose block, and the rail's "In this story" list made from it.

    Contents: core writes the body's h2 headings with no id, so this gives each h2 one (from its words, kept unique;
    an id core already wrote is kept) and sets the rail's list (the `toc` section of the layout) from them. The list
    shows when $contents is on and the body has at least $contentsMin such headings. fable.js marks the entry the
    reader has reached, from [data-toc-link].

    In-article ad: when the body is long enough the first in-article ad (the ad-in-article region) sits inside it, after
    $after top-level blocks, so the body is cut between two blocks (read from the HTML's tags, never inside one); a
    shorter body, or one the scan cannot read, gets the ad after it instead.

    Takes $html (the body), $lede (whether the first paragraph is set as the lede), $ads (whether to draw the ad),
    $after, $min (the fewest top-level blocks that earn a cut), $contents and $contentsMin.

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
    if (($ads ?? true) && preg_match_all('/<(\/?)([a-zA-Z][a-zA-Z0-9-]*)\b[^>]*?(\/?)>/s', $html, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
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
    $cut = null;
    if ($ok && $depth === 0 && count($ends) >= max(3, (int) ($min ?? 8))) {
        $cut = $ends[min((int) ($after ?? 4), count($ends) - 2) - 1] ?? null;
    }
    $proseClass = 'prose'.(($lede ?? true) ? ' prose-lede' : '');
@endphp
@if ($cut !== null)
    <div class="{{ $proseClass }}">{!! substr($html, 0, $cut) !!}</div>
    @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-article', 'class' => 'my-10'])
    <div class="prose prose-cont">{!! substr($html, $cut) !!}</div>
@else
    <div class="{{ $proseClass }}">{!! $html !!}</div>
    @if ($ads ?? true)
        @include('theme::skins.fable.partials.ad', ['region' => 'ad-in-article', 'class' => 'my-10'])
    @endif
@endif
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
