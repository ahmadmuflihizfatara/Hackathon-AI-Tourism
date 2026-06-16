<x-layouts.app>
<div class="min-h-screen flex flex-col items-center justify-center px-4 text-center">
    <div class="w-24 h-24 bg-terracotta/10 rounded-3xl flex items-center justify-center mb-6">
        <span class="material-icons-round text-terracotta text-5xl">error_outline</span>
    </div>
    <h1 class="font-heading font-bold text-6xl text-terracotta mb-3">500</h1>
    <h2 class="font-heading font-semibold text-2xl text-stone-700 mb-3">Terjadi Kesalahan Server</h2>
    <p class="text-stone-400 max-w-sm mb-8 leading-relaxed">
        Maaf, server sedang mengalami gangguan. Coba lagi dalam beberapa saat.
    </p>
    <a href="{{ route('landing') }}" class="btn-primary">
        <span class="material-icons-round">home</span>
        Kembali ke Beranda
    </a>
</div>
</x-layouts.app>