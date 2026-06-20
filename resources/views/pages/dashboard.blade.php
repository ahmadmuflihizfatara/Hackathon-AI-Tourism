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
                <div id="destination-header" class="relative bg-terracotta px-6 pt-8 pb-6 overflow-hidden flex-shrink-0">
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
                {{-- Tabs: Itinerary / Budget / Tips / Rute / Aturan / Kendaraan --}}
                <div class="bg-white border-b border-stone-100 px-4 overflow-x-auto flex-shrink-0">
                    <div class="flex gap-0 min-w-max">
                        @foreach(['itinerary' => 'Jadwal', 'budget' => 'Budget', 'tips' => 'Tips', 'rute' => 'Rute', 'aturan' => 'Aturan', 'kendaraan' => 'Kendaraan'] as $tab => $label)
                        <button onclick="switchTab('{{ $tab }}')"
                                data-tab="{{ $tab }}"
                                class="tab-btn flex items-center gap-1.5 px-4 py-3 text-sm font-medium border-b-2 transition-all
                                       {{ $tab === 'itinerary' ? 'border-terracotta text-terracotta' : 'border-transparent text-stone-400 hover:text-stone-600' }}">
                            <span class="material-icons-round text-base">
                                @switch($tab)
                                    @case('itinerary') calendar_today @break
                                    @case('budget') payments @break
                                    @case('aturan') gavel @break
                                    @case('kendaraan') directions_bus @break
                                    @case('tips') lightbulb @break
                                    @case('rute') route @break
                                @endswitch
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

                {{-- Tab: Aturan Wisata --}}
                <div id="tab-aturan" class="hidden p-5 space-y-4">
                    <div id="aturan-header" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="material-icons-round text-blue-600">gavel</span>
                            </div>
                            <div>
                                <p class="font-heading font-semibold text-stone-700">Aturan & Regulasi Wisata</p>
                                <p class="text-xs text-stone-400">Tetap patuhi agar liburan aman dan berkesan</p>
                            </div>
                        </div>
                    </div>
                    <div id="aturan-content" class="space-y-3">
                        {{-- Aturan items injected by JS --}}
                    </div>
                </div>

                {{-- Tab: Kendaraan --}}
                <div id="tab-kendaraan" class="hidden p-5 space-y-4">
                    <div id="kendaraan-header" class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-5 border border-emerald-100">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <span class="material-icons-round text-emerald-600">directions_bus</span>
                            </div>
                            <div>
                                <p class="font-heading font-semibold text-stone-700">Rekomendasi Kendaraan</p>
                                <p class="text-xs text-stone-400">Pilih transportasi terbaik untuk perjalananmu</p>
                            </div>
                        </div>
                    </div>
                    <div id="kendaraan-content" class="space-y-3">
                        {{-- Kendaraan items injected by JS --}}
                    </div>
                </div>

                {{-- Tab: Rute ← TAMBAHKAN BLOK INI --}}
                <div id="tab-rute" class="hidden p-5 space-y-4 flex-shrink-0">
                    <div id="map-route" class="w-full h-[360px] rounded-2xl border border-stone-100"></div>
                    <div id="route-summary" class="space-y-3"></div>
                </div>

            </div> {{-- /itinerary-content --}}
        </section>

    </main>
</div>

{{-- ── Image Lightbox ── --}}
<div id="img-lightbox" class="hidden fixed inset-0 bg-black/85 backdrop-blur-md z-50 flex items-center justify-center cursor-zoom-out transition-opacity duration-300" onclick="closeLightbox()">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors z-10">
        <span class="material-icons-round text-white text-xl">close</span>
    </button>
    <div class="absolute bottom-4 left-0 right-0 text-center z-10">
        <p id="lightbox-place" class="text-white font-heading font-semibold text-lg drop-shadow-lg"></p>
        <p id="lightbox-caption" class="text-white/60 text-xs mt-1"></p>
    </div>
    <img id="lightbox-img" src="" alt="" class="max-w-[92vw] max-h-[85vh] object-contain rounded-2xl shadow-2xl transition-transform duration-300" onclick="event.stopPropagation()" />
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

    // Lazy-load destination images after DOM is painted
    requestAnimationFrame(() => loadAllDestinationImages(data.schedule));

    // Render budget
    renderBudget(data.budget || []);

    // Render tips
    renderTips(data.tips || []);

    // Render aturan wisata
    renderAturan(data.rules || [], data.destination);

    // Render kendaraan
    renderKendaraan(data.kendaraan || [], data.destination);

    //Render route
    renderMap(data);
}

// ── HD Image fetcher (multi-source) ──────────────────────────

// ── DB-First Image fetcher ────────────────────────────────────────────────────
//
// Alur baru:
//   1. Batch-fetch semua nama tempat ke /api/destinations/images  (1 request)
//   2. Jika DB punya gambar → pakai langsung (akurat, cepat)
//   3. Jika tidak ada di DB  → fallback ke Wikipedia API (existing logic)
//   4. Terakhir              → picsum placeholder

const _imgCache   = {};   // thumb/display URL
const _hdImgCache = {};   // full-res URL untuk lightbox

// ── Batch-load images right after itinerary is rendered ──────────────────────
async function loadAllDestinationImages(scheduleData) {
    // Helper: ambil nama lokasi dari format baru (location) atau lama (place)
    const getPlaceName = (act) => act.location || act.place || '';
    // Kumpulkan semua nama tempat unik
    const allPlaces = [];
    (scheduleData || []).forEach(day => {
        (day.activities || []).forEach(act => {
            const placeName = act.location || act.place;
            if (placeName && !allPlaces.includes(placeName)) {
                allPlaces.push(placeName);
            }
        });
    });

    if (allPlaces.length === 0) return;

    try {
        // Satu request ke Laravel → dapat semua gambar DB sekaligus
        const res = await fetch('/api/destinations/images', {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ places: allPlaces }),
        });

        if (res.ok) {
            const data = await res.json();
            // Pre-populate cache dengan data DB
            Object.entries(data.images || {}).forEach(([place, info]) => {
                if (info.url) {
                    _imgCache[place]   = info.url;
                    _hdImgCache[place] = info.url;
                }
            });
        }
    } catch (e) {
        // Tidak kritis — lanjut ke Wikipedia fallback
        console.warn('[NusantaraAI] Batch image prefetch gagal:', e);
    }

    // Sekarang pasang gambar ke setiap <img> element
    (scheduleData || []).forEach((day, dayIdx) => {
        (day.activities || []).forEach((act, actIdx) => {
            const id    = `act-img-${dayIdx + 1}-${actIdx}`;
            const imgEl = document.getElementById(id);
            const skEl  = document.getElementById(`${id}-sk`);
            const wrapEl= document.getElementById(`${id}-wrapper`);
            if (!imgEl || !wrapEl) return;

            imgEl.addEventListener('load',  () => {
                imgEl.classList.remove('opacity-0');
                imgEl.classList.add('opacity-100');
                skEl?.classList.add('hidden');
            });
            imgEl.addEventListener('error', () => {
                wrapEl.classList.add('hidden');
            });

            _loadDestinationImage(act.location || act.place, imgEl, skEl, wrapEl);
        });
    });
}

// ── Per-image loader (DB cache → Wikipedia → Picsum) ─────────────────────────
async function _loadDestinationImage(placeName, imgEl, skeletonEl, wrapperEl) {
    const fallbackSrc = `https://picsum.photos/seed/${encodeURIComponent(placeName)}/1280/720`;

    // Sudah ada di cache (dari DB atau sebelumnya)?
    if (_imgCache[placeName] !== undefined) {
        imgEl.src = _imgCache[placeName] || fallbackSrc;
        return;
    }

    // Belum ada di DB cache → coba Wikipedia
    const wikiDomain = '{{ env("WIKIMEDIA_API_DOMAIN", "wikipedia.org") }}';

    const tryWikiPageImage = async (lang) => {
        const q   = encodeURIComponent(placeName);
        const url = `https://${lang}.${wikiDomain}/w/api.php?action=query&titles=${q}&prop=pageimages&format=json&pithumbsize=2000&origin=*`;
        const res = await fetch(url);
        const j   = await res.json();
        const pg  = Object.values(j.query?.pages || {})[0];
        return pg?.thumbnail?.source || null;
    };

    const tryWikimediaCommons = async () => {
        const q   = encodeURIComponent(placeName + ' landmark');
        const url = `https://commons.wikimedia.org/w/api.php?action=query&generator=search&gsrnamespace=6&gsrsearch=${q}&prop=imageinfo&iiprop=url|extmetadata&iiurlwidth=2000&format=json&origin=*`;
        try {
            const res   = await fetch(url);
            const j     = await res.json();
            const pages = Object.values(j.query?.pages || {});
            for (const pg of pages) {
                const thumbUrl = pg?.imageinfo?.[0]?.thumburl;
                const origUrl  = pg?.imageinfo?.[0]?.url;
                if (thumbUrl) return { thumb: thumbUrl, original: origUrl };
            }
        } catch { /* ignore */ }
        return null;
    };

    try {
        const wikiSrc = (await tryWikiPageImage('id')) || (await tryWikiPageImage('en'));
        if (wikiSrc) {
            _imgCache[placeName]   = wikiSrc;
            _hdImgCache[placeName] = wikiSrc.replace(/\/thumb\//, '/').replace(/\/\d+px-[^/]+$/, '');
            imgEl.src = wikiSrc;
            return;
        }

        const commonsResult = await tryWikimediaCommons();
        if (commonsResult) {
            _imgCache[placeName]   = commonsResult.thumb;
            _hdImgCache[placeName] = commonsResult.original || commonsResult.thumb;
            imgEl.src = commonsResult.thumb;
            return;
        }

        _imgCache[placeName] = fallbackSrc;
        imgEl.src = fallbackSrc;
    } catch {
        _imgCache[placeName] = null;
        imgEl.src = fallbackSrc;
    }
}



// ── Lightbox ─────────────────────────────────────────────────
function openLightbox(placeName) {
    const lb = document.getElementById('img-lightbox');
    const img = document.getElementById('lightbox-img');
    const place = document.getElementById('lightbox-place');
    const caption = document.getElementById('lightbox-caption');

    place.textContent = placeName;

    // Use HD cache if available, otherwise use the current image src
    const hdSrc = _hdImgCache[placeName];
    const imgEl = document.querySelector(`img[alt="${CSS.escape(placeName)}"]`);

    if (hdSrc) {
        img.src = hdSrc;
        caption.textContent = 'Sumber: Wikimedia Commons';
    } else if (imgEl) {
        img.src = imgEl.src;
        caption.textContent = '';
    }

    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lb = document.getElementById('img-lightbox');
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close lightbox on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
});

function buildDayCard(day, dayNum) {
    const activities = (day.activities || []).map((act, idx) => {
        const isLast = idx === (day.activities.length - 1);
        const actId  = `act-img-${dayNum}-${idx}`;

        // Dukungan dua format:
        // Format BARU: { action, location, ... }  → tampil dua baris
        // Format LAMA: { place, ... }             → fallback satu baris (kompatibel mundur)
        const hasActionLocation = act.action && act.location;
        const displayLocation   = hasActionLocation ? act.location : (act.place || '');
        const displayAction     = hasActionLocation ? act.action   : null;
        const escapedPlace      = (act.location || act.place || '').replace(/'/g, "\\'");

        return `
        <div class="py-3 ${isLast ? '' : 'border-b border-stone-100'}">
            <div class="flex gap-3">
                {{-- Kolom kiri: jam + garis vertikal --}}
                <div class="text-center w-14 flex-shrink-0 pt-0.5">
                    <p class="text-xs font-semibold text-terracotta">${act.time || ''}</p>
                    ${!isLast ? `<span class="inline-block w-0.5 h-5 bg-stone-200 mx-auto mt-1"></span>` : ''}
                </div>

                {{-- Kolom kanan: konten aktivitas --}}
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
                    <div id="${actId}-wrapper" class="ml-6 rounded-xl overflow-hidden border border-stone-100 bg-stone-50 relative cursor-pointer group" style="height:220px;" onclick="openLightbox('${escapedPlace}')">
                        <img id="${actId}" alt="${act.location || act.place}"
                             class="w-full h-full object-cover transition-all duration-500 opacity-0 group-hover:scale-105" loading="lazy" />
                        <div id="${actId}-sk" class="absolute inset-0 flex flex-col items-center justify-center gap-1.5">
                            <div class="w-9 h-9 bg-stone-200 rounded-xl animate-pulse flex items-center justify-center">
                                <span class="material-icons-round text-stone-300 text-lg">photo_camera</span>
                            </div>
                            <p class="text-xs text-stone-300">Memuat foto HD…</p>
                        </div>
                        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/65 via-black/20 to-transparent px-3 py-2.5 pointer-events-none">
                            ${displayAction
                                ? `<p class="text-white/70 text-xs leading-tight">${displayAction}</p>
                                   <p class="text-white text-sm font-medium truncate drop-shadow">${displayLocation}</p>`
                                : `<p class="text-white text-sm font-medium truncate drop-shadow">${displayLocation}</p>`
                            }
                        </div>
                        <div class="absolute top-2 right-2 bg-black/40 backdrop-blur-sm rounded-lg px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            <span class="text-white text-xs flex items-center gap-1">
                                <span class="material-icons-round text-xs">zoom_in</span>
                                HD
                            </span>
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
    ['itinerary', 'budget', 'aturan', 'kendaraan', 'tips', 'rute'].forEach(tab => {
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

     if (name === 'rute' && leafletMap) {
        setTimeout(() => leafletMap.invalidateSize(), 150);
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

// ── Render Aturan Wisata ────────────────────────────────────
function renderAturan(rules, destination) {
    const container = document.getElementById('aturan-content');
    if (!container) return;

    // Jika AI sudah mengirimkan aturan dari backend, gunakan itu.
    // Jika belum ada, gunakan default aturan modern yang relevan.
    let aturanList = rules;
    if (!aturanList.length) {
        aturanList = getDefaultAturanWisata(destination);
    }

    container.innerHTML = aturanList.map((rule, i) => {
        const severityColor = getSeverityColor(rule.severity || 'info');
        const severityBg    = getSeverityBg(rule.severity || 'info');
        const severityIcon  = getSeverityIcon(rule.severity || 'info');
        const severityLabel = getSeverityLabel(rule.severity || 'info');

        return `
        <div class="bg-white rounded-xl border border-stone-100 overflow-hidden">
            <div class="flex items-start gap-3 px-5 py-4">
                <div class="w-9 h-9 ${severityBg} rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-icons-round ${severityColor} text-lg">${severityIcon}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="text-sm font-semibold text-stone-700">${rule.title || 'Aturan ' + (i+1)}</p>
                        <span class="text-xs ${severityBg} ${severityColor} px-2 py-0.5 rounded-full font-medium">${severityLabel}</span>
                    </div>
                    <p class="text-sm text-stone-500 leading-relaxed">${rule.content || rule.description || ''}</p>
                    ${rule.penalty ? `<div class="mt-2 flex items-center gap-1.5 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                        <span class="material-icons-round text-red-500 text-sm">warning</span>
                        <p class="text-xs text-red-600 font-medium"><strong>Sanksi:</strong> ${rule.penalty}</p>
                    </div>` : ''}
                    ${rule.source ? `<p class="text-xs text-stone-300 mt-2 italic">Sumber: ${rule.source}</p>` : ''}
                </div>
            </div>
        </div>`;
    }).join('');
}

function getDefaultAturanWisata(destination) {
    const dest = (destination || '').toLowerCase();
    const aturan = [
        {
            title: 'Larangan Membawa Plastik Sekali Pakai',
            content: 'Sebagian besar destinasi wisata di Indonesia sudah melarang penggunaan plastik sekali pakai (tas plastik, sedotan, styrofoam). Bawa botol minum refill dan tas kain sendiri. Beberapa daerah seperti Bali bahkan menerapkan larangan ini secara hukum (Peraturan Gubernur Bali No. 97/2018).',
            severity: 'warning',
            penalty: 'Denda hingga Rp 1.000.000 di beberapa daerah',
            source: 'Peraturan Pemerintah Daerah masing-masing wilayah'
        },
        {
            title: 'Pembelian Tiket Online / QR Code',
            content: 'Mayoritas destinasi wisata populer kini mewajibkan pembelian tiket secara online melalui aplikasi resmi atau website. Reservasi di tempat (walk-in) sering kali dikenakan harga lebih mahal atau bahkan tidak tersedia di jam sibuk. Pastikan booking H-1 untuk weekend dan libur nasional.',
            severity: 'info',
            source: 'Kebijakan manajemen destinasi wisata (2024-2025)'
        },
        {
            title: 'Jaga Kelestarian & Jangan Merusak Lingkungan',
            content: 'Dilarang memetik tanaman, merusak terumbu karang, mengganggu satwa liar, atau meninggalkan sampah. Aktivitas seperti vandalisme pada candi/relief dikenai UU No. 5/2017 tentang Cagar Budaya dengan ancaman pidana. Patuhi prinsip "Leave No Trace" (tidak meninggalkan jejak).',
            severity: 'danger',
            penalty: 'Pidana penjara maksimal 10 tahun dan/atau denda hingga Rp 5 miliar (UU Cagar Budaya)',
            source: 'UU No. 5 Tahun 2017 tentang Cagar Budaya & UU No. 32 Tahun 2009 tentang PPLH'
        },
        {
            title: 'Patuhi Jam Operasional & Kuota Pengunjung',
            content: 'Banyak destinasi menerapkan jam buka-tutup yang ketat dan sistem kuota harian untuk menjaga kelestarian. Beberapa tempat seperti Komodo Island bahkan membatasi jumlah pengunjung per tahun. Selalu cek jadwal terbaru sebelum berangkat.',
            severity: 'warning',
            penalty: 'Tiket hangus jika melewati batas waktu masuk; akses ditolak jika kuota penuh',
            source: 'Kebijakan BTNKI & manajemen destinasi'
        },
        {
            title: 'Gunakan Jasa Pemandu Lokal Resmi',
            content: 'Untuk area tertentu seperti taman nasional, gunung berapi, kawasan adat, dan situs warisan budaya, diwajibkan menggunakan jasa pemandu lokal resmi yang bersertifikat. Ini bukan hanya untuk keselamatan, tapi juga mendukung ekonomi lokal dan menjaga otentisitas informasi.',
            severity: 'info',
            source: 'Kementerian Pariwisata & Ekonomi Kreatif RI'
        },
        {
            title: 'Hormati Adat & Budaya Lokal',
            content: 'Kenali dan hormati norma adat setempat: berpakaian sopan di tempat ibadah/area suci, minta izin sebelum memotret orang/upacara adat, ikuti aturan khusus kawasan (seperti larangan masuk pada hari tertentu di Desa Penglipuran Bali). Ketidaktahuan bukan alasan yang diterima.',
            severity: 'info',
            source: 'Peraturan adat setempat & UU No. 11 Tahun 2010 tentang Cagar Budaya'
        },
        {
            title: 'Asuransi Perjalanan & Keselamatan',
            content: 'Sangat disarankan memiliki asuransi perjalanan yang mencakup evakuasi medis, terutama untuk aktivitas outdoor (diving, hiking, rafting). Pastikan peralatan selam, pendakian, atau wahana air memenuhi standar keselamatan (SNI). Gunakan life jacket saat naik kapal/boat tanpa kecuali.',
            severity: 'warning',
            source: 'Standar Keselamatan Kemenhub & Asosiasi Wisata'
        },
        {
            title: 'Pelaporan & Pengaduan Wisata',
            content: 'Jika mengalami penipuan, overcharging, atau pelayanan buruk, laporkan melalui hotline 151 (Kemenparekraf) atau aplikasi SiapBerkelana. Sampaikan juga melalui platform review agar wisatawan lain terinformasi. Simpan bukti pembayaran dan tiket digital.',
            severity: 'info',
            source: 'Kementerian Pariwisata & Ekonomi Kreatif RI'
        }
    ];

    // Tambahkan aturan spesifik berdasarkan destinasi
    if (dest.includes('bali')) {
        aturan.push({
            title: 'Aturan Khusus Bali: Larangan Sewa Motor Tanpa SIM Internasional',
            content: 'WNA wajib memiliki SIM Internasional (IDP) atau SIM lokal untuk menyewa motor. Tanpa dokumen ini, polisi berhak menghentikan dan mengeluarkan tilang. Beberapa rental juga meminta paspor sebagai jaminan.',
            severity: 'warning',
            penalty: 'Denda tilang Rp 250.000 - Rp 1.000.000',
            source: 'UU No. 22 Tahun 2009 tentang Lalu Lintas & Polda Bali'
        });
    }
    if (dest.includes('komodo') || dest.includes('labuan bajo') || dest.includes('ntt')) {
        aturan.push({
            title: 'Kawasan Taman Nasional Komodo: Biaya Konservasi WNA Tinggi',
            content: 'Mulai 2024, tarif masuk TN Komodo untuk WNA sekitar USD 200-250 (weekday) dan USD 270-300 (weekend) per orang. WNI dikenai tarif lebih rendah. Kuota pengunjung dibatasi maksimal 200.000 orang/tahun. Wajib didampingi ranger resmi.',
            severity: 'warning',
            penalty: 'Akses ditolak tanpa pembayaran & pemandu ranger',
            source: 'BTNKI & PP No. 28 Tahun 2024'
        });
    }
    if (dest.includes('raja ampat') || dest.includes('papua') || dest.includes('papua barat')) {
        aturan.push({
            title: 'Raja Ampat: Wajib Bayar Reforestation Fee & Pakai Ranger',
            content: 'Setiap wisatawan WNA wajib membayar biaya reforestasi sekitar USD 70-100 (WNI lebih rendah). Kartu ini berlaku 1 tahun. Diperlukan pemandu ranger lokal untuk snorkeling/diving di titik tertentu untuk mencegah kerusakan terumbu karang.',
            severity: 'warning',
            penalty: 'Tidak bisa masuk kawasan tanpa kartu reforestasi',
            source: 'Pemerintah Kabupaten Raja Ampat & Yayasan Konservasi'
        });
    }
    if (dest.includes('gunung') || dest.includes('merapi') || dest.includes('rinjani') || dest.includes('semeru') || dest.includes('bromo') || dest.includes('kerinci')) {
        aturan.push({
            title: 'Pendakian Gunung: Wajib Daftar Online & Bawa Perlengkapan Standar',
            content: 'Semua pendakian gunung berapi aktif di Indonesia wajib didaftarkan melalui sistem online resmi (pos jaga/BMKG). Bawa perlengkapan wajib: tenda, sleeping bag, jas hujan, senter/headlamp, dan P3K. Cek status aktivitas vulkanik terlebih dahulu. Pendakian tanpa pendaftaran resmi dilarang keras.',
            severity: 'danger',
            penalty: 'Ditolak di pos pendakian; tanggung jawab penuh jika terjadi kecelakaan tanpa pendaftaran',
            source: 'PNG, BMKG, & SOP Pendakian Gunung'
        });
    }
    if (dest.includes('dieng') || dest.includes('wonosobo')) {
        aturan.push({
            title: 'Kawasan Dieng: Batasan Pengunjung & Kendaraan',
            content: 'Pada musim libur dan weekend, kawasan Dieng menerapkan sistem one-way untuk kendaraan roda empat dan kadang menerapkan ganjil-genap. Disarankan datang weekday untuk menghindari kemacetan parah. Gunakan transportasi lokal dari Wonosobo jika memungkinkan.',
            severity: 'info',
            source: 'Pemkab Wonosobo & Pengelola Kawasan Dieng'
        });
    }

    return aturan;
}

function getSeverityColor(severity) {
    return { info: 'text-blue-600', warning: 'text-amber-600', danger: 'text-red-600' }[severity] || 'text-stone-600';
}
function getSeverityBg(severity) {
    return { info: 'bg-blue-50', warning: 'bg-amber-50', danger: 'bg-red-50' }[severity] || 'bg-stone-50';
}
function getSeverityIcon(severity) {
    return { info: 'info', warning: 'warning', danger: 'error' }[severity] || 'info';
}
function getSeverityLabel(severity) {
    return { info: 'Informasi', warning: 'Peringatan', danger: 'Wajib Taat' }[severity] || 'Info';
}

// ── Render Kendaraan ──────────────────────────────────────────
function renderKendaraan(kendaraanList, destination) {
    const container = document.getElementById('kendaraan-content');
    if (!container) return;

    let kendaraanData = kendaraanList;
    if (!kendaraanData.length) {
        kendaraanData = getDefaultKendaraan(destination);
    }

    container.innerHTML = kendaraanData.map((item, i) => {
        const vehicleIcon = getVehicleIcon(item.type || 'mobil');
        const vehicleLabel = getVehicleLabel(item.type || 'mobil');
        const accessLevel = item.road_access || 'baik';
        const accessColor = getAccessColor(accessLevel);
        const accessBg    = getAccessBg(accessLevel);
        const accessLabel = getAccessLabel(accessLevel);
        const accessIcon  = getAccessIcon(accessLevel);

        return `
        <div class="bg-white rounded-xl border border-stone-100 overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-icons-round text-emerald-600 text-xl">${vehicleIcon}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <p class="text-sm font-semibold text-stone-700">${item.title || item.location || 'Rute ' + (i+1)}</p>
                            <span class="text-xs bg-stone-100 text-stone-500 px-2 py-0.5 rounded-full font-medium">${vehicleLabel}</span>
                        </div>
                        ${item.description ? `<p class="text-xs text-stone-400 mb-3 leading-relaxed">${item.description}</p>` : ''}

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-stone-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-stone-400 mb-0.5">Jenis Kendaraan</p>
                                <p class="text-sm font-medium text-stone-700">${item.rekomendasi || item.vehicle_name || vehicleLabel}</p>
                            </div>
                            <div class="bg-stone-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-stone-400 mb-0.5">Estimasi Waktu Tempuh</p>
                                <p class="text-sm font-medium text-stone-700">${item.eta || item.duration || '-'}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-stone-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-stone-400 mb-0.5">Estimasi Biaya</p>
                                <p class="text-sm font-semibold text-emerald-600">${item.cost || item.price || '-'}</p>
                            </div>
                            <div class="bg-stone-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-stone-400 mb-0.5">Jadwal Operasi</p>
                                <p class="text-sm font-medium text-stone-700">${item.schedule || item.operational_hour || '-'}</p>
                            </div>
                        </div>

                        {{-- Road access indicator --}}
                        <div class="${accessBg} border ${accessColor.replace('text-', 'border-').replace('/600', '/200')} rounded-lg px-3 py-2.5 flex items-start gap-2">
                            <span class="material-icons-round ${accessColor} text-base flex-shrink-0 mt-0.5">${accessIcon}</span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-semibold ${accessColor}">Akses Jalan: ${accessLabel}</p>
                                </div>
                                <p class="text-xs ${accessColor} opacity-80 leading-relaxed mt-0.5">${item.road_note || getRoadNote(accessLevel)}</p>
                            </div>
                        </div>

                        ${item.tips ? `<div class="mt-2.5 flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            <span class="material-icons-round text-amber-500 text-sm mt-0.5">tips_and_updates</span>
                            <p class="text-xs text-amber-700 leading-relaxed"><strong>Tips:</strong> ${item.tips}</p>
                        </div>` : ''}
                        ${item.alternatif ? `<div class="mt-2 flex items-start gap-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                            <span class="material-icons-round text-blue-500 text-sm mt-0.5">alt_route</span>
                            <p class="text-xs text-blue-700 leading-relaxed"><strong>Alternatif:</strong> ${item.alternatif}</p>
                        </div>` : ''}
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    // Tambahkan ringkasan kendaraan di bawah
    container.innerHTML += `
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
        <div class="flex items-center gap-3 mb-3">
            <span class="material-icons-round text-2xl">emoji_objects</span>
            <p class="font-heading font-semibold">Tips Transportasi Umum</p>
        </div>
        <ul class="space-y-2 text-sm leading-relaxed text-white/90">
            <li class="flex items-start gap-2">
                <span class="material-icons-round text-base mt-0.5 text-white/70">check_circle</span>
                <span>Gunakan aplikasi <strong>Grab/Gojek</strong> untuk transportasi kota dan antar kota dekat di Pulau Jawa, Bali, dan Sumatera.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="material-icons-round text-base mt-0.5 text-white/70">check_circle</span>
                <span>Untuk rute pulau (Lombok-Raja Ampat), gunakan <strong>kapal ferry/Speedboat</strong> via aplikasi seperti <strong>IndoFerry</strong> atau beli langsung di pelabuhan.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="material-icons-round text-base mt-0.5 text-white/70">check_circle</span>
                <span>Sewa <strong>motor</strong> paling fleksibel di Bali/Lombok (Rp 50.000-Rp 100.000/hari) tapi pastikan ada <strong>SIM</strong>.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="material-icons-round text-base mt-0.5 text-white/70">check_circle</span>
                <span>Untuk ke daerah pegunungan/jalan sempit, sewa <strong>jip/offroad</strong> (Rp 500.000-Rp 1.500.000/hari termasuk supir).</span>
            </li>
        </ul>
    </div>`;
}

function getDefaultKendaraan(destination) {
    const dest = (destination || '').toLowerCase();

    if (dest.includes('bali')) {
        return [
            {
                title: 'Bandara Ngurah Rai → Ubud / Gianyar',
                description: 'Dari bandara menuju pusat budaya Ubud, bisa via jalan raya utama atau jalur bypass.',
                rekomendasi: 'Grab / Taxi Blue Bird / Sewa Motor',
                type: 'mobil',
                eta: '1 - 1,5 jam',
                cost: 'Grab: Rp 80.000 - 150.000 | Taxi: Rp 250.000 - 350.000 | Motor: Rp 70.000/hari (sewa)',
                schedule: 'Grab/Taxi 24 jam | Sewa motor 08.00-20.00',
                road_access: 'baik',
                road_note: 'Jalan raya aspal mulus, lebar, mendukung semua kendaraan.',
                tips: 'Pagi hari (06.00-09.00) macet dari arah Denpasar ke Ubud. Hindari jam tersebut.',
                alternatif: 'Sewa motor lebih fleksibel untuk explore Ubud dan sekitarnya.'
            },
            {
                title: 'Ubud → Tegallalang Rice Terrace',
                description: 'Menuju terasering sawah ikonik, jalan cukup sempit di area persawahan.',
                rekomendasi: 'Sewa Motor / Jalan Kaki',
                type: 'motor',
                eta: '15 - 25 menit',
                cost: 'Motor: Rp 70.000/hari (sudah termasuk sehari penuh)',
                schedule: 'Area terbuka 24 jam, paling baik 06.00-10.00',
                road_access: 'sempit',
                road_note: 'Jalan sempit, banyak tikungan dan tanjakan. Mobil besar sulit berpapasan.',
                tips: 'Parkir motor tersedia di area terasering. Hindari saat hujan karena jalan licin.'
            },
            {
                title: 'Ubud → Tanah Lot / Uluwatu / Seminyak',
                description: 'Rute wisata pantai selatan Bali, jalur bypass mandiri.',
                rekomendasi: 'Mobil / Grab',
                type: 'mobil',
                eta: '1,5 - 2 jam ke Uluwatu',
                cost: 'Grab: Rp 150.000 - 250.000 | Sewa mobil + driver: Rp 400.000 - 600.000/hari',
                schedule: 'Jalan utama 24 jam, destinasi biasanya 09.00-18.00',
                road_access: 'baik',
                road_note: 'Jalan utama lebar dan mulus, cocok untuk mobil dan bus wisata.',
                tips: 'Ke Uluwatu untuk sunset Kecak Dance, berangkat pukul 16.00 dari Ubud.',
                alternatif: 'Sewa mobil + supir (Rp 500.000/hari) lebih efisien jika rombongan 3-5 orang.'
            },
            {
                title: 'Sanur → Nusa Penida (Ferry)',
                description: 'Penyeberangan laut ke Pulau Nusa Penida yang terkenal dengan Kelingking Beach dan Manta Point.',
                rekomendasi: 'Speedboat / Fast Boat',
                type: 'kapal',
                eta: '30 - 45 menit (laut)',
                cost: 'Speedboat PP: Rp 200.000 - 350.000 | Fast Boat premium: Rp 400.000 - 600.000',
                schedule: 'Pelayaran mulai 07.00, terakhir 16.00 (tergantung cuaca)',
                road_access: 'laut',
                road_note: 'Akses via laut wajib. Cek kondisi cuaca sebelum berangkat.',
                tips: 'Booking H-1. Jika seasick, minum obat 30 menit sebelum berangkat. Di Nusa Penida, sewa motor wajib (jalan sangat sempit).',
                alternatif: 'Paket tour Nusa Penida 1 hari (Rp 500.000 - 800.000/orang) sudah termasuk speedboat PP dan mobil di Nusa Penida.'
            },
            {
                title: 'Area Nusa Penida (Dalam Pulau)',
                description: 'Di dalam Nusa Penida, infrastruktur jalan sangat terbatas.',
                rekomendasi: 'Sewa Motor + Driver Lokal',
                type: 'motor',
                eta: '20 - 45 menit antar spot',
                cost: 'Sewa motor: Rp 75.000 - 100.000/hari | Driver + motor: Rp 200.000 - 300.000/hari',
                schedule: 'Area wisata 06.00-18.00',
                road_access: 'sangat_sempit',
                road_note: 'Jalan sangat sempit, tidak beraspal di banyak tempat, tanjakan curam, dan berdebu. Mobil besar TIDAK bisa melintas.',
                tips: 'Wajib pakai helm dan hati-hati. Jalan licin saat hujan. Driver lokal sangat direkomendasikan karena familiar dengan jalan.',
                alternatif: 'Jika tidak bisa naik motor, sewa jip lokal (Rp 500.000/hari) untuk akses ke spot utama.'
            }
        ];
    }

    if (dest.includes('lombok')) {
        return [
            {
                title: 'Bandara Lombok (BIL) → Mataram / Senggigi',
                description: 'Dari bandara menuju pusat kota dan area wisata pantai Senggigi.',
                rekomendasi: 'Taksi Bandara / Grab / Sewa Motor',
                type: 'mobil',
                eta: '45 menit - 1 jam',
                cost: 'Taksi resmi: Rp 100.000 - 150.000 | Grab: Rp 80.000 - 120.000 | Motor sewa: Rp 80.000/hari',
                schedule: '24 jam (taksi) | Grab terbatas jam malam',
                road_access: 'baik',
                road_note: 'Jalan raya utama mulus dan lebar.',
                tips: 'Taksi resmi bandara di dalam terminal lebih aman dari taksi gelap.'
            },
            {
                title: 'Senggigi → Pelabuhan Bangsal (ke Gili Trawangan)',
                description: 'Rute menuju pelabuhan penyeberangan ke Kepulauan Gili.',
                rekomendasi: 'Mobil / Taksi / Kendaraan Umum',
                type: 'mobil',
                eta: '1,5 - 2 jam',
                cost: 'Taksi: Rp 200.000 - 300.000 | Angkot/pujasera: Rp 25.000 - 50.000 | Sewa motor: Rp 80.000/hari',
                schedule: '08.00-17.00 (kendaraan umum) | Taksi 24 jam',
                road_access: 'sedang',
                road_note: 'Jalan cukup baik, ada beberapa jalan sempit di Pusuk Pass.',
                tips: 'Pusuk Pass (bukit) berbahaya saat hujan. Berangkat pagi untuk menghindari cuaca buruk.'
            },
            {
                title: 'Bangsal → Gili Trawangan / Gili Air / Gili Meno',
                description: 'Penyeberangan laut ke tiga Gili. Tidak ada kendaraan bermotor di Gili!',
                rekomendasi: 'Public Boat / Fast Boat',
                type: 'kapal',
                eta: '20 - 30 menit (Public Boat) | 10-15 menit (Fast Boat)',
                cost: 'Public Boat PP: Rp 30.000 - 50.000 | Fast Boat PP: Rp 80.000 - 150.000',
                schedule: 'Public Boat mulai 08.00, terakhir sekitar 16.00 | Fast Boat lebih sering',
                road_access: 'laut',
                road_note: 'Akses laut wajib. Tidak ada kendaraan bermotor di Kepulauan Gili.',
                tips: 'Di Gili Trawangan hanya ada cidomo (kereta kuda) dan sepeda. Gili Air lebih sepi, cocok untuk snorkeling.',
                alternatif: 'Paket speedboat charter dari Senggigi (Rp 1.000.000 - 1.500.000 PP) untuk rombongan.'
            },
            {
                title: 'Senggigi → Gunung Rinjani (Senaru/Sembalun)',
                description: 'Menuju basecamp pendakian Gunung Rinjani, jalan menuju Senaru berkelok dan menanjak.',
                rekomendasi: 'Sewa Mobil + Driver / Jip',
                type: 'mobil',
                eta: '2 - 3 jam',
                cost: 'Sewa mobil + driver: Rp 400.000 - 600.000/hari | Jip: Rp 600.000 - 900.000/hari',
                schedule: 'Berangkat paling lambat 06.00 untuk trekking, jalan 24 jam',
                road_access: 'sedang',
                road_note: 'Jalan berkelok-kelok dan menanjak. Saat hujan berpotensi longsor kecil. Mobil city car kurang nyaman, disarankan SUV/Jip.',
                tips: 'Wajib naik mobil dengan supir berpengalaman. Pastikan rem dan ban kendaraan dalam kondisi baik.',
                alternatif: 'Paket trekking Rinjani 2D1N/3D2N (Rp 2.500.000 - 4.000.000/orang) sudah termasuk transportasi, porter, makan, dan tenda.'
            }
        ];
    }

    if (dest.includes('yogyakarta') || dest.includes('jogja') || dest.includes('yogya')) {
        return [
            {
                title: 'Stasiun Tugu / Bandara → Malioboro / Pusat Kota',
                description: 'Dari stasiun atau bandara menuju kawasan wisata utama Jogja.',
                rekomendasi: 'Trans Jogja / Grab / Taksi',
                type: 'bus',
                eta: '15 - 30 menit',
                cost: 'Trans Jogja: Rp 3.600 | Grab: Rp 20.000 - 40.000 | Taksi: Rp 40.000 - 60.000',
                schedule: 'Trans Jogja 05.30-21.30 | Grab/Taksi 24 jam',
                road_access: 'baik',
                road_note: 'Jalan kota lebar dan beraspal baik. Trans Jogja memiliki shelter di seluruh rute wisata utama.',
                tips: 'Beli Kartu Trans Jogja (Rp 20.000) untuk sekali naik Rp 3.600. Sangat ekonomis untuk keliling kota.'
            },
            {
                title: 'Kota Jogja → Candi Borobudur',
                description: 'Rute ke Candi Borobudur di Magelang, jalur lintas kota.',
                rekomendasi: 'Bus Wisata / Sewa Motor / Grab',
                type: 'bus',
                eta: '1,5 - 2 jam',
                cost: 'Bus wisata Jogja-Borobudur: Rp 50.000 - 80.000 PP | Motor sewa: Rp 80.000/hari | Grab: Rp 80.000 - 130.000',
                schedule: 'Bus wisata mulai 06.30 | Borobudur buka 06.00-17.00',
                road_access: 'baik',
                road_note: 'Jalan utama Magelang lebar dan mulus, mendukung semua kendaraan.',
                tips: 'Datang pagi (06.00-07.00) untuk sunrise dan menghindari pengunjung ramai. Booking tiket online wajib (sejak 2024).',
                alternatif: 'Paket tour Borobudur sunrise (Rp 200.000 - 350.000/orang) sudah termasuk transportasi dan pemandu.'
            },
            {
                title: 'Kota Jogja → Candi Prambanan / Ratu Boko',
                description: 'Rute ke kompleks candi Hindu terbesar di Indonesia.',
                rekomendasi: 'Trans Jogja + Jalan Kaki / Grab / Motor',
                type: 'bus',
                eta: '45 menit - 1 jam',
                cost: 'Trans Jogja: Rp 3.600 | Grab: Rp 40.000 - 70.000 | Motor: Rp 80.000/hari',
                schedule: 'Trans Jogja 05.30-21.30 | Candi buka 06.00-17.00',
                road_access: 'baik',
                road_note: 'Jalan raya Solo-Jogja lebar dan baik.',
                tips: 'Untuk Prambanan, kombinasikan dengan Sendang Tirto Martani. Paket Ramayana Ballet malam hari (Rp 200.000 - 500.000).'
            },
            {
                title: 'Kota Jogja → Pantai Gunungkidul (Siung, Krakal, Pok Tunggal)',
                description: 'Rute ke pantai-pantai tersembunyi di selatan Gunungkidul, jalur menanjak.',
                rekomendasi: 'Sewa Motor / Mobil / Jip',
                type: 'motor',
                eta: '2 - 2,5 jam',
                cost: 'Motor: Rp 80.000/hari | Mobil + driver: Rp 400.000/hari | Jip: Rp 500.000/hari',
                schedule: 'Jalan utama 24 jam, pantai buka 05.00-18.00',
                road_access: 'sedang',
                road_note: 'Jalan menanjak dan berkelok di perbukitan Gunungkidul. Jalan ke Pantai Siung sempit di beberapa titik.',
                tips: 'Gunungkidul identik dengan jalan berkelok. Pastikan motor/mobil rem-nya bagus. Bawa BBM cadangan karena SPBU jarang.',
                alternatif: 'Paket offroad Gunungkidul (Rp 500.000 - 800.000/kendaraan) untuk pengalaman seru ke pantai tersembunyi.'
            },
            {
                title: 'Kota Jogja → Tebing Breksi / HeHa Sky',
                description: 'Rute ke destinasi instagramable di perbukitan Patuk.',
                rekomendasi: 'Motor / Grab / Mobil',
                type: 'motor',
                eta: '45 menit - 1 jam',
                cost: 'Grab: Rp 50.000 - 80.000 | Motor: Rp 80.000/hari',
                schedule: 'Destinasi 08.00-20.00 (HeHa Sky) | Breksi 06.00-18.00',
                road_access: 'sedang',
                road_note: 'Jalan menuju Breksi cukup sempit di beberapa titik, mobil besar hati-hati berpapasan.',
                tips: 'Sunset di Breksi sangat indah (17.00-18.00). Parkir mobil terbatas, lebih baik pakai motor.'
            }
        ];
    }

    // Default fallback untuk destinasi lain
    return [
        {
            title: `Kota / Bandara → ${destination || 'Pusat Wisata'}`,
            description: 'Rute utama dari titik kedatangan ke area wisata utama.',
            rekomendasi: 'Grab / Taksi / Kendaraan Umum',
            type: 'mobil',
            eta: 'Tergantung jarak',
            cost: 'Grab/Taksi: Rp 50.000 - 300.000 | Kendaraan umum: Rp 15.000 - 50.000',
            schedule: 'Grab/Taksi 24 jam | Kendaraan umum biasanya 05.30-20.00',
            road_access: 'baik',
            road_note: 'Jalan utama umumnya beraspal dan mendukung mobil. Sesuaikan dengan kondisi lokal.',
            tips: 'Selalu cek tarif Grab/Gojek sebelum naik. Bandingkan dengan taksi konvensional.'
        },
        {
            title: 'Area Wisata → Antar Tempat Wisata',
            description: 'Mobilitas antar destinasi wisata dalam satu area.',
            rekomendasi: 'Sewa Motor (paling fleksibel) / Grab',
            type: 'motor',
            eta: '15 - 60 menit (tergantung jarak)',
            cost: 'Sewa motor: Rp 70.000 - 120.000/hari | Grab: Rp 20.000 - 150.000/trip',
            schedule: 'Rental motor 08.00-20.00 | Grab 24 jam',
            road_access: 'sedang',
            road_note: 'Beberapa area wisata memiliki jalan sempit atau berbatu. Motor lebih fleksibel.',
            tips: 'Sewa motor harian paling ekonomis jika kunjungi 3+ tempat sehari. Pastikan bawa SIM dan helm.',
            alternatif: 'Jika tidak bisa naik motor, rental mobil + driver Rp 350.000 - 600.000/hari.'
        },
        {
            title: 'Rute Menuju Area Pegunungan / Pedesaan',
            description: 'Untuk area wisata alam di dataran tinggi atau pedesaan terpencil.',
            rekomendasi: 'Jip / Mobil SUV / Motor Trail',
            type: 'jip',
            eta: '1 - 3 jam (tergantung medan)',
            cost: 'Sewa jip + driver: Rp 500.000 - 1.500.000/hari | Motor trail: Rp 150.000 - 300.000/hari',
            schedule: 'Fleksibel, berangkat pagi disarankan',
            road_access: 'sangat_sempit',
            road_note: 'Jalan tanah, berbatu, atau tidak beraspal. Mobil sedan/city car TIDAK bisa melintas. Hanya SUV, jip, motor trail, atau jalan kaki.',
            tips: 'Wajib bawa perlengkapan darurat: air minum, obat-obatan, senter, dan charger HP. Sinyal HP mungkin tidak ada.',
            alternatif: 'Gunakan jasa tour lokal yang sudah terbiasa dengan medan. Lebih aman dan efisien.'
        }
    ];
}

function getVehicleIcon(type) {
    const icons = {
        mobil: 'directions_car', motor: 'two_wheeler', bus: 'directions_bus',
        kapal: 'directions_boat', jip: 'airport_shuttle', kereta: 'train',
        pesawat: 'flight', jalan_kaki: 'hiking', default: 'commute'
    };
    return icons[type?.toLowerCase()] || icons.default;
}
function getVehicleLabel(type) {
    const labels = {
        mobil: 'Mobil / Mobil Pribadi', motor: 'Motor / Scooter', bus: 'Bus / Kendaraan Umum',
        kapal: 'Kapal / Speedboat', jip: 'Jip / SUV Offroad', kereta: 'Kereta Api',
        pesawat: 'Pesawat', jalan_kaki: 'Jalan Kaki / Trekking', default: 'Kendaraan'
    };
    return labels[type?.toLowerCase()] || labels.default;
}
function getAccessColor(access) {
    return { baik: 'text-emerald-600', sedang: 'text-amber-600', sempit: 'text-orange-600', sangat_sempit: 'text-red-600', laut: 'text-blue-600', default: 'text-stone-600' }[access] || 'text-stone-600';
}
function getAccessBg(access) {
    return { baik: 'bg-emerald-50', sedang: 'bg-amber-50', sempit: 'bg-orange-50', sangat_sempit: 'bg-red-50', laut: 'bg-blue-50', default: 'bg-stone-50' }[access] || 'bg-stone-50';
}
function getAccessIcon(access) {
    return { baik: 'check_circle', sedang: 'warning', sempit: 'report', sangat_sempit: 'dangerous', laut: 'waves', default: 'info' }[access] || 'info';
}
function getAccessLabel(access) {
    return { baik: 'Baik - Semua Kendaraan', sedang: 'Cukup - Hati-hati', sempit: 'Sempit - Mobil Kecil/Motor', sangat_sempit: 'Sangat Sempit - Motor/Jip Saja', laut: 'Laut - Perahu/Kapal', default: 'Perlu Dicek' }[access] || 'Perlu Dicek';
}
function getRoadNote(access) {
    return {
        baik: 'Jalan lebar, beraspal, dan mendukung semua jenis kendaraan termasuk bus wisata.',
        sedang: 'Jalan cukup baik tapi ada beberapa titik sempit, berkelok, atau menanjak. Perlu kehati-hatian.',
        sempit: 'Jalan sempit di beberapa titik. Mobil besar (minibus/bus) sulit melintas. Motor atau mobil kecil lebih direkomendasikan.',
        sangat_sempit: 'Jalan sangat sempit, tanah, atau berbatu. Mobil sedan/city car dan bus TIDAK bisa melintas. Hanya motor, jip, SUV, atau jalan kaki.',
        laut: 'Akses hanya bisa via laut. Wajib menggunakan kapal/speedboat. Cek cuaca sebelum berangkat.',
        default: 'Kondisi jalan bervariasi. Disarankan menggunakan kendaraan dengan ground clearance tinggi.'
    }[access] || '';
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
