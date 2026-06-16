@props([
    'title'   => '',
    'content' => '',
    'icon'    => 'lightbulb',
    'color'   => 'amber',
])

@php
$colorMap = [
    'amber'      => ['bg' => 'bg-amber-50',      'text' => 'text-amber-500'],
    'emerald'    => ['bg' => 'bg-emerald/10',    'text' => 'text-emerald'],
    'terracotta' => ['bg' => 'bg-terracotta/10', 'text' => 'text-terracotta'],
];
$style = $colorMap[$color] ?? $colorMap['amber'];
@endphp

<div class="bg-white rounded-xl px-5 py-4 border border-stone-100 flex items-start gap-3 hover:border-stone-200 transition-colors">
    <div class="w-9 h-9 {{ $style['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
        <span class="material-icons-round {{ $style['text'] }} text-base">{{ $icon }}</span>
    </div>
    <div>
        @if($title)
            <p class="text-sm font-semibold text-stone-700 mb-1">{{ $title }}</p>
        @endif
        <p class="text-sm text-stone-500 leading-relaxed">{{ $content }}</p>
    </div>
</div>