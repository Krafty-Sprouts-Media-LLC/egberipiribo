{{--
    The ONE nav: "All stories", then each section of the primary menu with a caret that opens its topics. The
    section's own link always goes to its page. The section the reader is in (or in one of whose topics they are)
    starts open. Takes $mobile, true inside the small-screen menu, where the touch targets are larger.

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $sections = $navigation['primary'] ?? [];
    $mobile = $mobile ?? false;
@endphp
<a href="{{ $site->url }}" class="rail-link" @if ($isFront) aria-current="page" @endif>
    {{ __('All stories') }}
</a>
@foreach ($sections as $section)
    @php
        $topics = $section['children'] ?? [];
        $inside = $section['current'] || collect($topics)->contains(fn ($topic) => $topic['current']);
        $sectionId = 'rail-'.($mobile ? 'm-' : 'd-').$loop->index;
    @endphp
    <div x-data="railSection" data-open="{{ $inside ? 'true' : 'false' }}">
        <div class="flex items-center gap-1">
            @if ($section['url'] !== null)
                <a href="{{ $section['url'] }}" class="rail-link flex-1{{ $section['current'] ? ' on' : '' }}" @if ($section['current']) aria-current="page" @endif @if ($section['opensNewTab']) target="_blank" rel="noopener" @endif>{{ $section['label'] }}</a>
            @else
                <span class="rail-link flex-1">{{ $section['label'] }}</span>
            @endif
            @if ($topics !== [])
                <button type="button" class="rail-caret {{ $mobile ? 'w-11 h-11' : '' }}" @click="toggle" :aria-expanded="open" aria-controls="{{ $sectionId }}" aria-label="{{ __('Topics in :section', ['section' => $section['label']]) }}">
                    <span class="transition-transform" :class="caretClass">@include('theme::icons.caret-down', ['class' => 'text-sm'])</span>
                </button>
            @endif
        </div>
        @if ($topics !== [])
            <ul id="{{ $sectionId }}" x-show="open" x-cloak class="rail-sub">
                @foreach ($topics as $topic)
                    <li><a href="{{ $topic['url'] ?? '#' }}" @class(['font-bold text-white' => $topic['current'], 'py-2' => $mobile]) @if ($topic['current']) aria-current="page" @endif>{{ $topic['label'] }}</a></li>
                @endforeach
            </ul>
        @endif
    </div>
@endforeach
