{{--
    A person's picture drawn as their initials in a circle (Teme gives an author no picture). Takes $name and $class
    (the size and type size, such as "w-[3.25rem] h-[3.25rem] text-lg").

    @package egberipiribo
    @since   0.1.0
--}}
@php
    $words = array_values(array_filter(preg_split('/\s+/u', trim($name ?? '')) ?: [], static fn ($word) => $word !== ''));
    $letters = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $letters .= mb_strtoupper(mb_substr($word, 0, 1));
    }
@endphp
<span class="grid place-items-center shrink-0 rounded-full bg-rail text-white font-display font-extrabold {{ $class ?? 'w-11 h-11' }}" aria-hidden="true">{{ $letters }}</span>
