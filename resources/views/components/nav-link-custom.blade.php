@props(['active', 'icon', 'title'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 rounded-xl transition bg-green-500 text-slate-900 font-bold shadow-lg shadow-green-900/20'
            : 'flex items-center gap-3 px-3 py-2.5 rounded-xl transition hover:bg-slate-800 text-slate-300 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <i class="{{ $icon }} w-5 text-center text-lg"></i>
    <span class="text-sm tracking-wide">{{ $title }}</span>
</a>