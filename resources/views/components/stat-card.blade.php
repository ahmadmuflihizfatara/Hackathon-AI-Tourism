@props([
    'icon'  => 'info',
    'label' => '',
    'value' => '',
    'color' => 'terracotta',
])

<div class="bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5 text-center flex-1">
    <div class="flex items-center justify-center gap-1 mb-0.5">
        <span class="material-icons-round text-white/60 text-sm">{{ $icon }}</span>
        <p class="text-white/70 text-xs">{{ $label }}</p>
    </div>
    <p class="text-white font-heading font-bold text-base leading-tight">{{ $value }}</p>
</div>