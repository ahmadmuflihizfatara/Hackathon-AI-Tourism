@props([
    'category' => 'lainnya',
    'label'    => '',
    'amount'   => 0,
    'per'      => '',
    'note'     => '',
])

@php
$iconMap = [
    'akomodasi' => ['icon' => 'hotel',             'bg' => 'bg-blue-50',    'text' => 'text-blue-500'],
    'makan'     => ['icon' => 'restaurant',         'bg' => 'bg-orange-50',  'text' => 'text-orange-500'],
    'transport' => ['icon' => 'directions_car',     'bg' => 'bg-purple-50',  'text' => 'text-purple-500'],
    'tiket'     => ['icon' => 'confirmation_number','bg' => 'bg-emerald/10', 'text' => 'text-emerald'],
    'lainnya'   => ['icon' => 'more_horiz',         'bg' => 'bg-stone-100',  'text' => 'text-stone-400'],
];
$style = $iconMap[strtolower($category)] ?? $iconMap['lainnya'];
@endphp

<div class="bg-white rounded-xl px-5 py-4 border border-stone-100 flex items-center justify-between hover:border-stone-200 transition-colors">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 {{ $style['bg'] }} rounded-xl flex items-center justify-center">
            <span class="material-icons-round {{ $style['text'] }} text-base">{{ $style['icon'] }}</span>
        </div>
        <div>
            <p class="text-sm font-medium text-stone-700">{{ $label }}</p>
            @if($note)
                <p class="text-xs text-stone-400">{{ $note }}</p>
            @endif
        </div>
    </div>
    <div class="text-right">
        <p class="text-sm font-semibold text-stone-800">Rp {{ number_format($amount, 0, ',', '.') }}</p>
        @if($per)
            <p class="text-xs text-stone-400">{{ $per }}</p>
        @endif
    </div>
</div>