@props([
    'time'        => '',
    'place'       => '',
    'category'    => 'default',
    'description' => '',
    'ticket'      => null,
    'isLast'      => false,
])

@php
$icons = [
    'alam'      => 'landscape',
    'pantai'    => 'beach_access',
    'museum'    => 'museum',
    'kuliner'   => 'restaurant',
    'hotel'     => 'hotel',
    'belanja'   => 'shopping_bag',
    'transport' => 'directions_car',
    'budaya'    => 'temple_hindu',
    'default'   => 'place',
];
$icon = $icons[strtolower($category)] ?? $icons['default'];
@endphp

<div class="flex gap-3 py-3 {{ $isLast ? '' : 'border-b border-stone-100' }}">
    <div class="text-center w-14 flex-shrink-0 pt-0.5">
        <p class="text-xs font-semibold text-terracotta">{{ $time }}</p>
        @if(!$isLast)
            <span class="inline-block w-0.5 h-6 bg-stone-200 mx-auto mt-1"></span>
        @endif
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-icons-round text-stone-400 text-base">{{ $icon }}</span>
                <p class="text-sm font-semibold text-stone-800">{{ $place }}</p>
            </div>
            @if($ticket)
                <span class="badge-emerald flex-shrink-0">{{ $ticket }}</span>
            @endif
        </div>
        @if($description)
            <p class="text-xs text-stone-400 mt-1 ml-6 leading-relaxed">{{ $description }}</p>
        @endif
    </div>
</div>