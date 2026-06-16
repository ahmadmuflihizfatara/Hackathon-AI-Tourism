@props([
    'role'    => 'ai',
    'message' => '',
])

@if($role === 'ai')
<div class="flex items-start gap-3">
    <div class="w-8 h-8 bg-terracotta rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
        <span class="material-icons-round text-white text-base">smart_toy</span>
    </div>
    <div class="bg-stone-50 border border-stone-100 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[280px]">
        <p class="text-sm text-stone-700 leading-relaxed">{!! $message !!}</p>
    </div>
</div>
@else
<div class="flex items-start gap-3 justify-end">
    <div class="bg-terracotta text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-[260px]">
        <p class="text-sm leading-relaxed">{{ $message }}</p>
    </div>
    <div class="w-8 h-8 bg-stone-100 rounded-full flex items-center justify-center flex-shrink-0">
        <span class="material-icons-round text-stone-500 text-base">person</span>
    </div>
</div>
@endif