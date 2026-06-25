<x-layouts.app>
{{-- Leaflet Maps CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<div class="flex flex-col h-screen overflow-hidden">

    {{-- ========== TOP NAV ========== --}}
    <nav class="flex-shrink-0 bg-white border-b border-stone-100 px-4 py-2.5 flex items-center justify-between z-10">
        <a href="{{ route('landing') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="material-icons-round text-terracotta">travel_explore</span>
            <span class="font-heading font-bold text-lg text-stone-800">Nusantara<span class="text-terracotta">AI</span></span>
        </a>

        {{-- Trip summary pill --}}
        <div id="trip-summary-pill" class="hidden items-center gap-3 bg-stone-50 border border-stone-200 rounded-full px-4 py-1.5 text-sm">
            <span class="flex items-center gap-1 text-stone-600">
                <span class="material-icons-round text-base text-terracotta">location_on</span>
                <span id="pill-destination">—</span>
            </span>
            <span class="text-stone-300">|</span>
            <span class="flex items-center gap-1 text-stone-600">
                <span class="material-icons-round text-base text-terracotta">schedule</span>
                <span id="pill-duration">—</span>
            </span>
            <span class="text-stone-300">|</span>
            <span class="flex items-center gap-1 text-stone-600">
                <span class="material-icons-round text-base text-emerald">payments</span>
                <span id="pill-budget">—</span>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button id="btn-export"
                    onclick="exportItinerary()"
                    class="hidden items-center gap-1 border border-stone-200 text-stone-600 text-sm px-3 py-1.5 rounded-lg hover:border-terracotta hover:text-terracotta transition-all">
                <span class="material-icons-round text-base">download</span>
                Ekspor PDF
            </button>
            <button onclick="resetChat()"
                    class="flex items-center gap-1 text-stone-400 text-sm px-3 py-1.5 rounded-lg hover:text-stone-700 hover:bg-stone-50 transition-all">
                <span class="material-icons-round text-base">refresh</span>
                Mulai Ulang
            </button>
        </div>
    </nav>

    {{-- ========== TWO-COLUMN MAIN ========== --}}
    <main class="flex-1 flex overflow-hidden">

        {{-- ── LEFT COLUMN: Chatbot ──────────────────────────────────── --}}
        <aside class="w-full md:w-[420px] flex-shrink-0 flex flex-col border-r border-stone-100 bg-white">

            {{-- Column header --}}
            <div class="flex items-center gap-2 px-5 py-3 border-b border-stone-100">
                <div class="w-7 h-7 bg-terracotta rounded-lg flex items-center justify-center">
                    <span class="material-icons-round text-white text-sm">smart_toy</span>
                </div>
                <span class="font-heading font-semibold text-stone-700 text-sm">Asisten AI</span>
                <span id="ai-status" class="ml-auto flex items-center gap-1 text-xs text-emerald">
                    <span class="w-1.5 h-1.5 bg-emerald rounded-full animate-pulse"></span>
                    Online
                </span>
            </div>

            {{-- Messages --}}
            <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-4 scroll-smooth">
                {{-- Initial AI greeting --}}
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-terracotta rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="material-icons-round text-white text-base">smart_toy</span>
                    </div>
                    <div class="bg-stone-50 border border-stone-100 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[280px]">
                        <p class="text-sm text-stone-700 leading-relaxed">
                            Halo! Saya siap merencanakan wisata impianmu 🌴<br><br>
                            Ceritakan tujuanmu — misalnya: <em>"Mau ke Lombok 5 hari dengan budget Rp 4 juta, suka wisata alam dan pantai"</em>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Chat Input ── --}}
            <div class="p-4 border-t border-stone-100 bg-white">
                <form id="chat-form" class="flex items-end gap-2">
                    <div class="flex-1 bg-stone-50 border border-stone-200 rounded-2xl px-4 py-2.5 focus-within:border-terracotta focus-within:ring-2 focus-within:ring-terracotta/10 transition-all">
                        <textarea
                            id="chat-input"
                            rows="1"
                            placeholder="Ketik pesan atau pertanyaan..."
                            class="w-full bg-transparent text-sm text-stone-700 placeholder-stone-400 outline-none resize-none leading-relaxed max-h-32"
                            style="overflow-y: hidden;"
                        ></textarea>
                    </div>
                    <button type="submit"
                            id="send-btn"
                            class="w-10 h-10 bg-terracotta rounded-xl flex items-center justify-center hover:bg-terracotta-dark transition-colors flex-shrink-0 disabled:opacity-50">
                        <span class="material-icons-round text-white">send</span>
                    </button>
                </form>
                <p class="text-center text-xs text-stone-300 mt-2">Didukung Google Gemini AI</p>
            </div>
        </aside>

        {{-- ── RIGHT COLUMN: Itinerary Results ─────────────────────── --}}
        <section id="right-panel" class="hidden md:flex flex-col flex-1 bg-warm-sand overflow-hidden">

            {{-- Empty state (shown before itinerary loads) --}}
            <div id="empty-state" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-20 h-20 bg-white rounded-3xl shadow-sm flex items-center justify-center mb-5 border border-stone-100">
                    <span class="material-icons-round text-terracotta text-4xl">explore</span>
                </div>
                <h3 class="font-heading font-semibold text-stone-700 text-xl mb-2">Itinerary Akan Tampil di Sini</h3>
                <p class="text-stone-400 text-sm max-w-xs leading-relaxed">
                    Mulai chat dengan AI di sebelah kiri untuk mendapatkan rencana perjalanan personalmu.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-3 w-full max-w-sm">
                    @foreach(['Wisata Alam', 'Budaya & Sejarah', 'Kuliner', 'Pantai & Laut', 'Kota', 'Petualangan'] as $cat)
                    <div class="bg-white rounded-xl p-3 text-center border border-stone-100">
                        <span class="text-xs text-stone-500">{{ $cat }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Itinerary content (hidden until generated) --}}
            <div id="itinerary-content" class="hidden flex-1 overflow-y-auto">

                {{-- Destination Header --}}
                <div id="destination-header" class="relative bg-terracotta px-6 pt-8 pb-6 overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-48 h-48 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    </div>
                    <div class="relative">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-terracotta-light text-xs font-semibold uppercase tracking-widest mb-1">Destinasi Wisata</p>
                                <h2 id="dest-name" class="font-heading font-bold text-white text-3xl"></h2>
                                <p id="dest-province" class="text-white/70 text-sm mt-1"></p>
                            </div>
                            <span class="material-icons-round text-white/30 text-6xl">travel_explore</span>
                        </div>
                        {{-- Stats row --}}
                        <div class="flex gap-4 mt-4">
                            <div class="bg-white/15 rounded-xl px-4 py-2.5 text-center flex-1">
                                <p class="text-white/70 text-xs mb-1">Durasi</p>
                                <p id="stat-duration" class="text-white font-heading font-bold text-lg"></p>
                            </div>
                            <div class="bg-white/15 rounded-xl px-4 py-2.5 text-center flex-1">
                                <p class="text-white/70 text-xs mb-1">Est. Budget</p>
                                <p id="stat-budget" class="text-white font-heading font-bold text-lg"></p>
                            </div>
                            <div class="bg-white/15 rounded-xl px-4 py-2.5 text-center flex-1">
                                <p class="text-white/70 text-xs mb-1">Lokasi</p>
                                <p id="stat-places" class="text-white font-heading font-bold text-lg"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabs: Itinerary / Budget / Tips / Rute / Aturan / Kendaraan --}}
                <div class="bg-white border-b border-stone-100 px-4 overflow-x-auto flex-shrink-0">
                    <div class="flex gap-0 min-w-max">
                        @foreach(['itinerary' => 'Jadwal', 'budget' => 'Budget', 'tips' => 'Tips', 'rute' => 'Rute', 'aturan' => 'Aturan', 'kendaraan' => 'Kendaraan'] as $tab => $label)
                        <button onclick="switchTab('{{ $tab }}')"
                                data-tab="{{ $tab }}"
                                class="tab-btn flex items-center gap-1.5 px-4 py-3 text-sm font-medium border-b-2 transition-all whitespace-nowrap
                                       {{ $tab === 'itinerary' ? 'border-terracotta text-terracotta' : 'border-transparent text-stone-400 hover:text-stone-600' }}">
                            <span class="material-icons-round text-base">
                                {{ $tab === 'itinerary' ? 'calendar_today' : ($tab === 'budget' ? 'payments' : ($tab === 'tips' ? 'lightbulb' : ($tab === 'rute' ? 'route' : ($tab === 'aturan' ? 'gavel' : 'directions_car')))) }}
                            </span>
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tab: Itinerary --}}
                <div id="tab-itinerary" class="p-5 space-y-4 flex-shrink-0">
                    {{-- Day cards injected by JS --}}
                </div>

                {{-- Tab: Budget --}}
                <div id="tab-budget" class="hidden p-5 flex-shrink-0">
                    <div id="budget-content" class="space-y-3">
                        {{-- Budget items injected by JS --}}
                    </div>
                </div>

                {{-- Tab: Tips --}}
                <div id="tab-tips" class="hidden p-5 flex-shrink-0">
                    <div id="tips-content" class="space-y-3">
                        {{-- Tips injected by JS --}}
                    </div>
                </div>

                {{-- Tab: Rute --}}
                <div id="tab-rute" class="hidden p-5 space-y-4 flex-shrink-0">
                    <div id="map-route" class="w-full h-[360px] rounded-2xl border border-stone-100"></div>
                    <div id="route-summary" class="space-y-3"></div>
                </div>

                {{-- Tab: Aturan Tempat Wisata --}}
                <div id="tab-aturan" class="hidden p-5 space-y-3 flex-shrink-0">
                    <div id="aturan-content" class="space-y-3">
                        {{-- Aturan tempat wisata diinjeksi oleh JS --}}
                    </div>
                </div>

                {{-- Tab: Rekomendasi Kendaraan --}}
                <div id="tab-kendaraan" class="hidden p-5 space-y-3 flex-shrink-0">
                    <div id="kendaraan-content" class="space-y-3">
                        {{-- Rekomendasi kendaraan diinjeksi oleh JS --}}
                    </div>
                </div>

            </div> {{-- /itinerary-content --}}
        </section>

    </main>
</div>

{{-- ── Loading overlay ── --}}
<div id="loading-overlay" class="hidden fixed inset-0 bg-warm-sand/80 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 shadow-lg text-center max-w-xs w-full mx-4">
        <div class="w-16 h-16 bg-terracotta/10 rounded-2xl flex items-center justify-center mx-auto mb-4 animate-pulse">
            <span class="material-icons-round text-terracotta text-3xl">travel_explore</span>
        </div>
        <p class="font-heading font-semibold text-stone-700 mb-1">Menyusun Itinerary...</p>
        <p class="text-xs text-stone-400" id="loading-tip">AI sedang menganalisis destinasimu</p>
    </div>
</div>

<script>
// ============================================================
//  NusantaraAI Dashboard — JavaScript
// ============================================================

const GEMINI_API_URL = '/api/gemini'; // proxied through Laravel
let conversationHistory = [];
let currentItinerary = null;

// ── DOM refs ─────────────────────────────────────────────────
const chatMessages   = document.getElementById('chat-messages');
const chatForm       = document.getElementById('chat-form');
const chatInput      = document.getElementById('chat-input');
const loadingOverlay = document.getElementById('loading-overlay');
const emptyState     = document.getElementById('empty-state');
const itineraryContent = document.getElementById('itinerary-content');
const btnExport      = document.getElementById('btn-export');
const tripPill       = document.getElementById('trip-summary-pill');

// ── Auto-resize textarea ──────────────────────────────────────
chatInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 128) + 'px';
});

// Enter to send (Shift+Enter = new line)
chatInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
    }
});

// ── Chat form submit ──────────────────────────────────────────
chatForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message) return;

    chatInput.value = '';
    chatInput.style.height = 'auto';
    appendUserMessage(message);
    conversationHistory.push({ role: 'user', parts: [{ text: message }] });

    appendTypingIndicator();

    try {
        const response = await sendToGemini(conversationHistory);
        removeTypingIndicator();

        if (response.itinerary) {
            renderItinerary(response.itinerary);
            appendAIMessage(response.message || `Itinerary untuk <strong>${response.itinerary.destination}</strong> sudah siap! Cek panel kanan ya. Ada yang ingin diubah?`);
            conversationHistory.push({ role: 'model', parts: [{ text: response.message || '' }] });
        } else {
            appendAIMessage(response.message);
            conversationHistory.push({ role: 'model', parts: [{ text: response.message }] });
        }
    } catch (err) {
        removeTypingIndicator();
        appendAIMessage('Maaf, terjadi gangguan koneksi. Silakan coba lagi sebentar.');
        console.error(err);
    }
});

// ── API call to Laravel backend ───────────────────────────────
async function sendToGemini(history) {
    const res = await fetch(GEMINI_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ history }),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

// ── Render itinerary to right panel ──────────────────────────
function renderItinerary(data) {
    currentItinerary = data;

    // Show itinerary panel, hide empty state
    emptyState.classList.add('hidden');
    itineraryContent.classList.remove('hidden');
    itineraryContent.classList.add('flex', 'flex-col');
    btnExport.classList.remove('hidden');
    btnExport.classList.add('flex');
    tripPill.classList.remove('hidden');
    tripPill.classList.add('flex');

    // Update header
    document.getElementById('dest-name').textContent      = data.destination;
    document.getElementById('dest-province').textContent  = data.province || '';
    document.getElementById('stat-duration').textContent  = `${data.days} Hari`;
    document.getElementById('stat-budget').textContent    = data.total_budget || '—';
    document.getElementById('stat-places').textContent    = `${data.total_places || '—'} Lokasi`;

    // Update pill
    document.getElementById('pill-destination').textContent = data.destination;
    document.getElementById('pill-duration').textContent    = `${data.days} Hari`;
    document.getElementById('pill-budget').textContent      = data.total_budget || '';

    // Render day cards
    const itineraryTab = document.getElementById('tab-itinerary');
    itineraryTab.innerHTML = '';
    (data.schedule || []).forEach((day, i) => {
        itineraryTab.innerHTML += buildDayCard(day, i + 1);
    });

    // Render budget
    renderBudget(data.budget || []);

    // Render tips
    renderTips(data.tips || []);

    // Render aturan tempat wisata
    renderAturan(data.rules || data.aturan || []);

    // Render rekomendasi kendaraan
    renderKendaraan(data.transportation || data.kendaraan || []);

    //Render route
    renderMap(data);
}

// ── SVG Illustration System ───────────────────────────────────
// Menggantikan Unsplash/Pexels/Wikipedia dengan ilustrasi SVG lokal
// berdasarkan kategori aktivitas. Konsisten, cepat, dan tidak bergantung API eksternal.

/**
 * Kembalikan path SVG ilustrasi berdasarkan kategori aktivitas.
 * File SVG disimpan di: public/images/activity-illustrations/{category}.svg
 */
function getIllustrationSrc(category) {
    const validCategories = [
        'transport', 'hotel', 'kuliner', 'pantai', 'alam', 'budaya', 'museum', 'belanja'
    ];
    const cat = (category || 'default').toLowerCase();
    const key = validCategories.includes(cat) ? cat : 'default';
    return `/images/activity-illustrations/${key}.jpg`;
}

/**
 * Warna aksen latar ilustrasi per kategori (untuk fallback background)
 */
function getIllustrationAccent(category) {
    const accents = {
        'transport' : '#BFDBFE', // biru langit
        'hotel'     : '#FEF3C7', // kuning hangat
        'kuliner'   : '#FFEDD5', // oranye muda
        'pantai'    : '#E0F2FE', // biru laut
        'alam'      : '#DCFCE7', // hijau alam
        'budaya'    : '#FEF3C7', // kuning budaya
        'museum'    : '#EFF6FF', // biru museum
        'belanja'   : '#F0FDF4', // hijau pasar
        'default'   : '#DBEAFE', // biru default
    };
    const cat = (category || 'default').toLowerCase();
    return accents[cat] || accents['default'];
}

function buildDayCard(day, dayNum) {
    const activities = (day.activities || []).map((act, idx) => {
        const isLast = idx === (day.activities.length - 1);

        // Dukungan dua format:
        // Format BARU: { action, location, ... }  → tampil dua baris
        // Format LAMA: { place, ... }             → fallback satu baris (kompatibel mundur)
        const hasActionLocation = act.action && act.location;
        const displayLocation   = hasActionLocation ? act.location : (act.place || '');
        const displayAction     = hasActionLocation ? act.action   : null;

        return `
        <div class="py-3 ${isLast ? '' : 'border-b border-stone-100'}">
            <div class="flex gap-3">
                <div class="text-center w-14 flex-shrink-0 pt-0.5">
                    <p class="text-xs font-semibold text-terracotta">${act.time || ''}</p>
                    ${!isLast ? `<span class="inline-block w-0.5 h-5 bg-stone-200 mx-auto mt-1"></span>` : ''}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-start gap-2 min-w-0">
                            <span class="material-icons-round text-stone-400 text-base mt-0.5 flex-shrink-0">${getActivityIcon(act.category)}</span>
                            <div class="min-w-0">
                                ${displayAction
                                    ? `<p class="text-xs font-medium text-terracotta leading-tight">${displayAction}</p>
                                       <p class="text-sm font-semibold text-stone-800 leading-snug truncate">${displayLocation}</p>`
                                    : `<p class="text-sm font-semibold text-stone-800 leading-snug truncate">${displayLocation}</p>`
                                }
                            </div>
                        </div>
                        ${act.ticket ? `<span class="text-xs bg-emerald/10 text-emerald-dark px-2 py-0.5 rounded-full flex-shrink-0 whitespace-nowrap">${act.ticket}</span>` : ''}
                    </div>
                    ${act.description ? `<p class="text-xs text-stone-400 mb-2 ml-6 leading-relaxed">${act.description}</p>` : ''}
                    <div class="ml-6 rounded-xl overflow-hidden border border-stone-100 relative" style="height:200px;background:${getIllustrationAccent(act.category)};">
                        <img
                            src="${getIllustrationSrc(act.category)}"
                            alt="Ilustrasi ${displayLocation}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/50 via-black/15 to-transparent px-3 py-2.5 pointer-events-none">
                            ${displayAction
                                ? `<p class="text-white/80 text-xs leading-tight">${displayAction}</p>
                                   <p class="text-white text-sm font-semibold truncate drop-shadow">${displayLocation}</p>`
                                : `<p class="text-white text-sm font-semibold truncate drop-shadow">${displayLocation}</p>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    return `
        <div class="bg-white rounded-2xl border border-stone-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 bg-stone-50 border-b border-stone-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-terracotta rounded-lg flex items-center justify-center">
                        <span class="text-white text-xs font-bold font-heading">${dayNum}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold font-heading text-stone-700">Hari ${dayNum}</p>
                        ${day.title ? `<p class="text-xs text-stone-400">${day.title}</p>` : ''}
                    </div>
                </div>
                <span class="text-xs text-stone-400">${day.activities?.length || 0} aktivitas</span>
            </div>
            <div class="px-5">${activities}</div>
        </div>
    `;
}

function getActivityIcon(category) {
    const icons = {
        'alam': 'landscape', 'pantai': 'beach_access', 'museum': 'museum',
        'kuliner': 'restaurant', 'hotel': 'hotel', 'belanja': 'shopping_bag',
        'transport': 'directions_car', 'budaya': 'temple_hindu', 'default': 'place'
    };
    return icons[category?.toLowerCase()] || icons.default;
}

function renderBudget(items) {
    const container = document.getElementById('budget-content');
    if (!items.length) {
        container.innerHTML = '<p class="text-stone-400 text-sm text-center py-8">Data budget akan tersedia setelah itinerary dibuat.</p>';
        return;
    }
    const total = items.reduce((sum, i) => sum + (i.amount || 0), 0);
    container.innerHTML = `
        <div class="bg-terracotta text-white rounded-2xl p-5 mb-4">
            <p class="text-terracotta-light text-xs mb-1">Total Estimasi Budget</p>
            <p class="font-heading font-bold text-3xl">Rp ${total.toLocaleString('id-ID')}</p>
        </div>
        ${items.map(item => `
            <div class="bg-white rounded-xl px-5 py-4 border border-stone-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-stone-50 rounded-lg flex items-center justify-center">
                        <span class="material-icons-round text-stone-400 text-base">${getBudgetIcon(item.category)}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-stone-700">${item.label}</p>
                        ${item.note ? `<p class="text-xs text-stone-400">${item.note}</p>` : ''}
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-stone-800">Rp ${(item.amount || 0).toLocaleString('id-ID')}</p>
                    <p class="text-xs text-stone-400">${item.per || ''}</p>
                </div>
            </div>
        `).join('')}
    `;
}

function getBudgetIcon(cat) {
    const m = { akomodasi: 'hotel', makan: 'restaurant', transport: 'directions_car', tiket: 'confirmation_number', lainnya: 'more_horiz' };
    return m[cat?.toLowerCase()] || 'payments';
}

function renderTips(tips) {
    const container = document.getElementById('tips-content');
    if (!tips.length) {
        container.innerHTML = '<p class="text-stone-400 text-sm text-center py-8">Tips akan tersedia setelah itinerary dibuat.</p>';
        return;
    }
    container.innerHTML = tips.map((tip, i) => `
        <div class="bg-white rounded-xl px-5 py-4 border border-stone-100 flex items-start gap-3">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="material-icons-round text-amber-500 text-base">lightbulb</span>
            </div>
            <div>
                ${tip.title ? `<p class="text-sm font-semibold text-stone-700 mb-1">${tip.title}</p>` : ''}
                <p class="text-sm text-stone-500 leading-relaxed">${tip.content || tip}</p>
            </div>
        </div>
    `).join('');
}

// ── Render Aturan Tempat Wisata ───────────────────────────────
// Format item: { place, category, rules: [string,...], severity: 'wajib'|'larangan'|'anjuran', source_note }
function renderAturan(rules) {
    const container = document.getElementById('aturan-content');
    if (!rules.length) {
        container.innerHTML = '<p class="text-stone-400 text-sm text-center py-8">Aturan tempat wisata akan tersedia setelah itinerary dibuat.</p>';
        return;
    }

    const intro = `
        <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-2 flex items-start gap-2.5">
            <span class="material-icons-round text-amber-500 text-base mt-0.5">info</span>
            <p class="text-xs text-amber-700 leading-relaxed">
                Aturan berikut disusun berdasarkan kebijakan umum yang berlaku saat ini (kearifan lokal, regulasi kawasan konservasi/cagar budaya, dan protokol keselamatan). Selalu cek papan informasi atau petugas di lokasi karena kebijakan bisa berubah sewaktu-waktu.
            </p>
        </div>`;

    container.innerHTML = intro + rules.map(item => {
        const placeName = item.place || item.location || '';
        const rulesList = item.rules || (Array.isArray(item.items) ? item.items : []);
        return `
        <div class="bg-white rounded-xl border border-stone-100 overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-3 bg-stone-50 border-b border-stone-100">
                <div class="w-8 h-8 bg-terracotta/10 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="material-icons-round text-terracotta text-base">${getAturanIcon(item.category)}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-stone-700 truncate">${placeName}</p>
                    ${item.category ? `<p class="text-xs text-stone-400">${item.category}</p>` : ''}
                </div>
            </div>
            <ul class="px-5 py-3 space-y-2.5">
                ${rulesList.map(r => renderAturanLine(r)).join('')}
            </ul>
            ${item.source_note ? `<p class="px-5 pb-3 text-xs text-stone-300 leading-relaxed">${item.source_note}</p>` : ''}
        </div>`;
    }).join('');
}

function renderAturanLine(r) {
    // r bisa string sederhana, atau object { text, type: 'wajib'|'larangan'|'anjuran' }
    const text = typeof r === 'string' ? r : (r.text || '');
    const type = typeof r === 'string' ? 'anjuran' : (r.type || 'anjuran');
    const badge = {
        wajib:    { label: 'Wajib',    cls: 'bg-blue-50 text-blue-600',    icon: 'check_circle' },
        larangan: { label: 'Larangan', cls: 'bg-rose-50 text-rose-600',    icon: 'block' },
        anjuran:  { label: 'Anjuran',  cls: 'bg-emerald/10 text-emerald-dark', icon: 'tips_and_updates' },
    }[type] || { label: 'Info', cls: 'bg-stone-100 text-stone-500', icon: 'info' };

    return `
        <li class="flex items-start gap-2.5">
            <span class="material-icons-round text-sm mt-0.5 flex-shrink-0 ${badge.cls.split(' ')[1]}">${badge.icon}</span>
            <div class="min-w-0">
                <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded ${badge.cls} mr-1.5">${badge.label}</span>
                <span class="text-sm text-stone-600 leading-relaxed">${text}</span>
            </div>
        </li>`;
}

function getAturanIcon(category) {
    const icons = {
        'religi': 'temple_hindu', 'religius': 'temple_hindu', 'pantai': 'beach_access',
        'gunung': 'landscape', 'konservasi': 'eco', 'cagar budaya': 'museum',
        'taman nasional': 'park', 'air terjun': 'water_drop', 'desa adat': 'holiday_village',
        'default': 'gavel'
    };
    return icons[category?.toLowerCase()] || icons.default;
}

// ── Render Rekomendasi Kendaraan ──────────────────────────────
// Format item: { route/segment, vehicle_type, reason, road_condition,
//                public_transport: { available, options: [{name, price_min, price_max, note}] } }
function renderKendaraan(items) {
    const container = document.getElementById('kendaraan-content');
    if (!items.length) {
        container.innerHTML = '<p class="text-stone-400 text-sm text-center py-8">Rekomendasi kendaraan akan tersedia setelah itinerary dibuat.</p>';
        return;
    }

    const intro = `
        <div class="bg-emerald/10 border border-emerald/20 rounded-xl px-4 py-3 mb-2 flex items-start gap-2.5">
            <span class="material-icons-round text-emerald-dark text-base mt-0.5">directions_car</span>
            <p class="text-xs text-emerald-dark leading-relaxed">
                Rekomendasi disesuaikan dengan kondisi akses jalan di tiap lokasi. Beberapa daerah (jalur pegunungan, desa terpencil, gang sempit di kawasan wisata padat) lebih cocok diakses dengan motor atau kendaraan umum lokal dibanding mobil pribadi.
            </p>
        </div>`;

    container.innerHTML = intro + items.map(item => {
        const vehicleBadge = getVehicleBadge(item.vehicle_type);
        const pt = item.public_transport || {};
        const hasPT = pt.available !== false && (pt.options || []).length > 0;

        return `
        <div class="bg-white rounded-xl border border-stone-100 overflow-hidden">
            <div class="flex items-center justify-between gap-2 px-5 py-3 bg-stone-50 border-b border-stone-100">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 ${vehicleBadge.bg} rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="material-icons-round ${vehicleBadge.text} text-base">${vehicleBadge.icon}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-stone-700 truncate">${item.segment || item.route || ''}</p>
                        <p class="text-xs text-stone-400">${vehicleBadge.label}</p>
                    </div>
                </div>
                ${item.road_condition ? `<span class="text-[10px] font-medium px-2 py-1 rounded-full ${getRoadBadge(item.road_condition).cls} whitespace-nowrap flex-shrink-0">${getRoadBadge(item.road_condition).label}</span>` : ''}
            </div>
            <div class="px-5 py-3 space-y-3">
                ${item.reason ? `<p class="text-sm text-stone-600 leading-relaxed">${item.reason}</p>` : ''}

                ${hasPT ? `
                <div class="bg-stone-50 rounded-lg px-4 py-3">
                    <p class="text-xs font-semibold text-stone-500 uppercase tracking-wide mb-2">Opsi Kendaraan Umum / Sewa</p>
                    <div class="space-y-2">
                        ${pt.options.map(o => `
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm text-stone-600 flex items-center gap-1.5">
                                    <span class="material-icons-round text-stone-400 text-sm">${o.icon || 'directions_bus'}</span>
                                    ${o.name}
                                </span>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-stone-700">${formatPriceRange(o.price_min, o.price_max)}</p>
                                    ${o.note ? `<p class="text-xs text-stone-400">${o.note}</p>` : ''}
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>` : (item.vehicle_type?.toLowerCase().includes('pribadi') ? '' : `
                <p class="text-xs text-stone-400 italic">Kendaraan umum belum tersedia langsung ke lokasi ini — disarankan sewa kendaraan pribadi atau jasa ojek lokal.</p>
                `)}
            </div>
        </div>`;
    }).join('');
}

function getVehicleBadge(type) {
    const t = (type || '').toLowerCase();
    if (t.includes('motor')) return { icon: 'two_wheeler', label: 'Motor', bg: 'bg-amber-50', text: 'text-amber-500' };
    if (t.includes('mobil') || t.includes('pribadi')) return { icon: 'directions_car', label: 'Mobil Pribadi / Sewa', bg: 'bg-blue-50', text: 'text-blue-600' };
    if (t.includes('umum') || t.includes('bus') || t.includes('angkot')) return { icon: 'directions_bus', label: 'Kendaraan Umum', bg: 'bg-emerald/10', text: 'text-emerald-dark' };
    if (t.includes('kapal') || t.includes('perahu') || t.includes('boat')) return { icon: 'directions_boat', label: 'Kapal / Perahu', bg: 'bg-cyan-50', text: 'text-cyan-600' };
    if (t.includes('jalan') || t.includes('kaki')) return { icon: 'directions_walk', label: 'Jalan Kaki', bg: 'bg-stone-100', text: 'text-stone-500' };
    return { icon: 'directions_car', label: type || 'Kendaraan', bg: 'bg-stone-100', text: 'text-stone-500' };
}

function getRoadBadge(condition) {
    const c = (condition || '').toLowerCase();
    if (c.includes('rusak') || c.includes('terbatas') || c.includes('sulit') || c.includes('sempit')) {
        return { label: 'Akses Terbatas', cls: 'bg-rose-50 text-rose-600' };
    }
    if (c.includes('sedang') || c.includes('cukup')) {
        return { label: 'Akses Sedang', cls: 'bg-amber-50 text-amber-600' };
    }
    return { label: 'Akses Mudah', cls: 'bg-emerald/10 text-emerald-dark' };
}

function formatPriceRange(min, max) {
    if (min == null && max == null) return '—';
    const fmt = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
    if (min != null && max != null && min !== max) return `${fmt(min)} – ${fmt(max)}`;
    return fmt(min ?? max);
}

function renderMap(data) {
    const mapRoute = document.getElementById('map-route');
    const routeSummary = document.getElementById('route-summary');
    if (!mapRoute || !routeSummary) return;

    const places = (data.schedule || []).flatMap(day => (day.activities || []).map(act => act.location || act.place || '')).filter(Boolean);

    // Initialize Leaflet map centered on Indonesia (default)
    setTimeout(() => {
        // Remove existing map if any
        if (window.routeMap) {
            window.routeMap.remove();
        }

        const mapElement = document.getElementById('map-route');
        if (!mapElement) return;

        // Create map centered on Indonesia
        window.routeMap = L.map('map-route').setView([-2.5489, 113.9213], 5);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(window.routeMap);

        // Sample coordinates for major Indonesian cities (simplified)
        const cityCoords = {
            'Bali': [-8.6705, 115.2126],
            'Lombok': [-8.6500, 116.3164],
            'Jakarta': [-6.2088, 106.8456],
            'Bandung': [-6.9175, 107.6062],
            'Yogyakarta': [-7.7956, 110.3695],
            'Surabaya': [-7.2575, 112.7521],
            'Medan': [3.1952, 98.6722],
            'Seminyak': [-8.6906, 115.1694],
            'Ubud': [-8.5069, 115.2625],
            'Denpasar': [-8.6726, 115.2126],
            'Tanah Lot': [-8.6274, 115.1640],
            'Pantai Seminyak': [-8.6906, 115.1694],
            'Pantai Kuta': [-8.7245, 115.1720],
        };

        // Get coordinates for places (fallback to Indonesia center if not found)
        const markers = [];
        places.forEach((place, idx) => {
            // Try to find matching city/location
            let coords = null;
            for (const [city, coord] of Object.entries(cityCoords)) {
                if (place.toLowerCase().includes(city.toLowerCase())) {
                    coords = coord;
                    break;
                }
            }

            if (coords) {
                const marker = L.marker(coords, {
                    opacity: 0.8
                }).addTo(window.routeMap)
                    .bindPopup(`<strong>${idx + 1}. ${place}</strong>`)
                    .openPopup();
                markers.push(marker);
            }
        });

        // Fit map to show all markers
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            window.routeMap.fitBounds(group.getBounds().pad(0.1));
        }

        // Add polyline connecting markers
        if (markers.length > 1) {
            const latlngs = markers.map(m => m.getLatLng());
            L.polyline(latlngs, {color: '#c2410c', weight: 2, opacity: 0.7}).addTo(window.routeMap);
        }
    }, 100);

    // Render route summary list
    routeSummary.innerHTML = `
        <div class="bg-white/90 rounded-2xl border border-stone-100 p-4">
            <p class="text-sm font-semibold text-stone-700 mb-3">Rute Perjalanan</p>
            <ol class="list-decimal list-inside space-y-2 text-sm text-stone-600">
                ${places.map((place, idx) => `<li><span class="font-medium">${escapeHtml(place)}</span></li>`).join('')}
            </ol>
        </div>
    `;
}

// ── Tab switching ─────────────────────────────────────────────
function switchTab(name) {
    ['itinerary', 'budget', 'tips', 'rute', 'aturan', 'kendaraan'].forEach(tab => {
        const el = document.getElementById(`tab-${tab}`);
        const btn = document.querySelector(`[data-tab="${tab}"]`);
        if (tab === name) {
            el.classList.remove('hidden');
            btn.classList.add('border-terracotta', 'text-terracotta');
            btn.classList.remove('border-transparent', 'text-stone-400');
        } else {
            el.classList.add('hidden');
            btn.classList.remove('border-terracotta', 'text-terracotta');
            btn.classList.add('border-transparent', 'text-stone-400');
        }
    });

     if (name === 'rute' && window.routeMap) {
        setTimeout(() => window.routeMap.invalidateSize(), 100);
    }
}

// ── Message helpers ───────────────────────────────────────────
function appendUserMessage(text) {
    const div = document.createElement('div');
    div.className = 'flex items-start gap-3 justify-end';
    div.innerHTML = `
        <div class="bg-terracotta text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-[260px]">
            <p class="text-sm leading-relaxed">${escapeHtml(text)}</p>
        </div>
        <div class="w-8 h-8 bg-stone-100 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-stone-500 text-base">person</span>
        </div>`;
    chatMessages.appendChild(div);
    scrollToBottom();
}

function appendAIMessage(html) {
    const div = document.createElement('div');
    div.className = 'flex items-start gap-3';
    div.innerHTML = `
        <div class="w-8 h-8 bg-terracotta rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-white text-base">smart_toy</span>
        </div>
        <div class="bg-stone-50 border border-stone-100 rounded-2xl rounded-tl-sm px-4 py-3 max-w-[280px]">
            <p class="text-sm text-stone-700 leading-relaxed">${html}</p>
        </div>`;
    chatMessages.appendChild(div);
    scrollToBottom();
}

function appendTypingIndicator() {
    const div = document.createElement('div');
    div.id = 'typing-indicator';
    div.className = 'flex items-start gap-3';
    div.innerHTML = `
        <div class="w-8 h-8 bg-terracotta rounded-full flex items-center justify-center flex-shrink-0">
            <span class="material-icons-round text-white text-base">smart_toy</span>
        </div>
        <div class="bg-stone-50 border border-stone-100 rounded-2xl rounded-tl-sm px-4 py-3">
            <div class="flex gap-1 items-center h-5">
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                <span class="w-2 h-2 bg-stone-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
            </div>
        </div>`;
    chatMessages.appendChild(div);
    scrollToBottom();
}

function removeTypingIndicator() {
    document.getElementById('typing-indicator')?.remove();
}

function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escapeHtml(text) {
    return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function resetChat() {
    if (!confirm('Mulai percakapan baru? Itinerary saat ini akan dihapus.')) return;
    window.location.href = '{{ route("dashboard") }}';
}

function exportItinerary() {
    alert('Fitur ekspor PDF akan segera hadir!');
}

// ── Auto-load from URL query ──────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const q = params.get('q');
    if (q) {
        setTimeout(() => {
            chatInput.value = q;
            chatForm.dispatchEvent(new Event('submit'));
        }, 600);
    }
});
</script>
</x-layouts.app>
