<x-layouts.app>
{{-- ========== NAVBAR ========== --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-warm-sand/90 backdrop-blur-sm border-b border-primary/10">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="material-icons-round text-primary text-2xl">travel_explore</span>
            <span class="font-heading font-bold text-xl text-stone-800">Toba<span class="text-primary">Itinerary</span></span>
        </div>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-stone-600">
            <a href="#features" class="hover:text-primary transition-colors">Fitur</a>
            <a href="#how-it-works" class="hover:text-primary transition-colors">Cara Kerja</a>
            <a href="#destinations" class="hover:text-primary transition-colors">Destinasi</a>
        </div>
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-1 bg-primary text-white text-sm font-medium px-4 py-2 rounded-full hover:bg-primary-dark transition-colors">
            <span class="material-icons-round text-base">map</span>
            Mulai Merencanakan
        </a>
    </div>
</nav>

{{-- ========== HERO ========== --}}
<section class="min-h-screen flex flex-col items-center justify-center px-4 pt-20 pb-16 relative overflow-hidden">

    {{-- Background decorative elements --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 right-10 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-80 h-80 bg-emerald/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/3 rounded-full blur-3xl"></div>
    </div>

    {{-- Eyebrow label --}}
    <div class="flex items-center gap-2 bg-emerald/10 text-emerald-dark text-xs font-semibold px-4 py-1.5 rounded-full mb-6 border border-emerald/20">
        <span class="material-icons-round text-sm">auto_awesome</span>
        Didukung Teknologi Gemini AI
    </div>

    {{-- Headline --}}
    <h1 class="font-heading font-bold text-4xl md:text-6xl text-center text-stone-800 leading-tight max-w-3xl mb-4">
        Rencanakan Wisata <span class="text-primary">Danau Toba</span> dengan AI
    </h1>
    <p class="text-stone-500 text-center text-lg max-w-xl mb-10">
        Ceritakan impian liburanmu. AI kami akan menyusun itinerary lengkap — sesuai budget, durasi, dan selera wisatamu.
    </p>

    {{-- ===== CHATBOT CARD ===== --}}
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-lg shadow-primary/10 border border-stone-100 overflow-hidden">

        {{-- Chat messages area --}}
        <div id="chat-messages" class="p-6 space-y-4 min-h-[220px] max-h-[320px] overflow-y-auto">
            {{-- AI greeting message --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="material-icons-round text-white text-base">smart_toy</span>
                </div>
                <div class="bg-stone-50 rounded-2xl rounded-tl-sm px-4 py-3 max-w-sm">
                    <p class="text-sm text-stone-700 leading-relaxed">
                        Halo! Saya <strong>Toba Itinerary</strong> 🌴 Mau liburan ke mana? Ceritakan provinsi tujuanmu, budget, dan berapa hari liburanmu!
                    </p>
                </div>
            </div>

            {{-- Dynamic messages will appear here via JS --}}
        </div>

        {{-- Quick suggestion chips --}}
        <div class="px-6 pb-3 flex flex-wrap gap-2" id="suggestion-chips">
            <button onclick="fillSuggestion('Samosir, 3 hari, budget Rp 2 juta')"
                    class="chip flex items-center gap-1 bg-stone-50 border border-stone-200 text-stone-600 text-xs px-3 py-1.5 rounded-full hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                <span class="material-icons-round text-sm">landscape</span> Samosir
            </button>
            <button onclick="fillSuggestion('Danau Toba, 2 hari, wisata alam')"
                    class="chip flex items-center gap-1 bg-stone-50 border border-stone-200 text-stone-600 text-xs px-3 py-1.5 rounded-full hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                <span class="material-icons-round text-sm">water</span> Danau Toba
            </button>
            <button onclick="fillSuggestion('Parapat, 4 hari, wisata keluarga')"
                    class="chip flex items-center gap-1 bg-stone-50 border border-stone-200 text-stone-600 text-xs px-3 py-1.5 rounded-full hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                <span class="material-icons-round text-sm">family_restroom</span> Parapat
            </button>
            <button onclick="fillSuggestion('Balige, 3 hari, wisata budaya')"
                    class="chip flex items-center gap-1 bg-stone-50 border border-stone-200 text-stone-600 text-xs px-3 py-1.5 rounded-full hover:border-primary hover:text-primary hover:bg-primary/5 transition-all">
                <span class="material-icons-round text-sm">museum</span> Balige
            </button>
        </div>

        {{-- Chat input --}}
        <div class="px-4 pb-4">
            <form id="landing-chat-form" class="flex items-center gap-2 bg-stone-50 border border-stone-200 rounded-2xl px-4 py-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/10 transition-all">
                <input
                    type="text"
                    id="landing-input"
                    placeholder="Contoh: Saya mau ke Samosir 3 hari dengan budget Rp 2 juta..."
                    class="flex-1 bg-transparent text-sm text-stone-700 placeholder-stone-400 outline-none py-1"
                    autocomplete="off"
                />
                <button type="submit"
                        class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center hover:bg-primary-dark transition-colors flex-shrink-0">
                    <span class="material-icons-round text-white text-lg">send</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Trust indicators --}}
    <div class="flex flex-wrap items-center justify-center gap-6 mt-8 text-xs text-stone-400">
        <div class="flex items-center gap-1.5">
            <span class="material-icons-round text-sm text-emerald">check_circle</span>
            Gratis digunakan
        </div>
        <div class="flex items-center gap-1.5">
            <span class="material-icons-round text-sm text-emerald">check_circle</span>
            34 Provinsi Indonesia
        </div>
        <div class="flex items-center gap-1.5">
            <span class="material-icons-round text-sm text-emerald">check_circle</span>
            Itinerary detail & peta
        </div>
    </div>
</section>

{{-- ========== FEATURES ========== --}}
<section id="features" class="py-20 px-4 bg-white">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-14">
            <p class="text-primary text-sm font-semibold tracking-wider uppercase mb-2">Mengapa Toba Itinerary?</p>
            <h2 class="font-heading font-bold text-3xl md:text-4xl text-stone-800">Semua yang Kamu Butuhkan</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'psychology', 'color' => 'primary', 'title' => 'AI Cerdas Gemini', 'desc' => 'Didukung Google Gemini AI yang memahami preferensi unik setiap wisatawan Indonesia.'],
                ['icon' => 'account_balance_wallet', 'color' => 'emerald', 'title' => 'Kalkulasi Budget Otomatis', 'desc' => 'Hitung estimasi biaya akomodasi, transportasi, makan, dan tiket masuk secara real-time.'],
                ['icon' => 'map', 'color' => 'amber', 'title' => 'Peta Interaktif', 'desc' => 'Visualisasikan rute perjalananmu di peta dengan urutan kunjungan yang efisien.'],
                ['icon' => 'schedule', 'color' => 'primary', 'title' => 'Jadwal Detail', 'desc' => 'Dapatkan timeline per hari lengkap dengan estimasi waktu dan jarak antar lokasi.'],
                ['icon' => 'photo_library', 'color' => 'emerald', 'title' => 'Info & Foto Wisata', 'desc' => 'Setiap destinasi dilengkapi foto, sejarah, jam buka, dan tips lokal terbaik.'],
                ['icon' => 'download', 'color' => 'amber', 'title' => 'Ekspor Itinerary', 'desc' => 'Simpan rencana perjalananmu sebagai PDF atau bagikan ke teman lewat tautan.'],
            ] as $feature)
            <div class="group p-6 rounded-2xl border border-stone-100 hover:border-{{ $feature['color'] }}/30 hover:shadow-md hover:shadow-{{ $feature['color'] }}/5 transition-all">
                <div class="w-11 h-11 bg-{{ $feature['color'] }}/10 rounded-xl flex items-center justify-center mb-4">
                    <span class="material-icons-round text-{{ $feature['color'] }}">{{ $feature['icon'] }}</span>
                </div>
                <h3 class="font-heading font-semibold text-stone-800 mb-2">{{ $feature['title'] }}</h3>
                <p class="text-sm text-stone-500 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ========== FOOTER ========== --}}
<footer class="bg-stone-800 text-stone-400 py-10 px-4 text-center text-sm">
    <div class="flex items-center justify-center gap-2 mb-3">
        <span class="material-icons-round text-primary">travel_explore</span>
        <span class="font-heading font-bold text-white">Toba Itinerary</span>
    </div>
    <p>© 2025 Toba Itinerary · Dibuat dengan ❤️ untuk pejalan nusantara</p>
</footer>

<script>
// ── Landing page chat logic ──────────────────────────────────────────────────
const form = document.getElementById('landing-chat-form');
const input = document.getElementById('landing-input');
const messagesDiv = document.getElementById('chat-messages');
const chipsDiv = document.getElementById('suggestion-chips');

function fillSuggestion(text) {
    input.value = text;
    input.focus();
}

function appendMessage(text, isUser = true) {
    const div = document.createElement('div');
    div.className = 'flex items-start gap-3' + (isUser ? ' justify-end' : '');

    if (isUser) {
        div.innerHTML = `
            <div class="bg-primary text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-xs">
                <p class="text-sm leading-relaxed">${text}</p>
            </div>
            <div class="w-8 h-8 bg-stone-200 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-icons-round text-stone-500 text-base">person</span>
            </div>`;
    } else {
        div.innerHTML = `
            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-icons-round text-white text-base">smart_toy</span>
            </div>
            <div class="bg-stone-50 rounded-2xl rounded-tl-sm px-4 py-3 max-w-sm">
                <p class="text-sm text-stone-700 leading-relaxed">${text}</p>
            </div>`;
    }

    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function appendTyping() {
    const div = document.createElement('div');
    div.id = 'typing-indicator';
    div.className = 'flex items-start gap-3';
    div.innerHTML = `
        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-white text-base">smart_toy</span>
        </div>
        <div class="bg-stone-50 rounded-2xl rounded-tl-sm px-4 py-3">
            <div class="flex gap-1 items-center h-4">
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
            </div>
        </div>`;
    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    // Hide chips after first message
    chipsDiv.style.display = 'none';

    appendMessage(message, true);
    input.value = '';
    appendTyping();

    // Redirect to dashboard with query params after short delay
    setTimeout(() => {
        const params = new URLSearchParams({ q: message });
        window.location.href = `{{ route('dashboard') }}?${params.toString()}`;
    }, 1000);
});
</script>
</x-layouts.app>
