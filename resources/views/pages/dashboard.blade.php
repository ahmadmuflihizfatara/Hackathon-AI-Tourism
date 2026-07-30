<x-layouts.app>
{{-- Leaflet Maps CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<style>
@media print {
    /* Hide navigation, chat sidebar, buttons, and overlays */
    nav, aside, #empty-state, .tab-btn, #btn-export, #loading-overlay, #trip-summary-pill, #chat-form, p.text-center.text-stone-300 {
        display: none !important;
    }
    
    /* Make right panel fit print pages */
    body, html, .h-screen, main, section {
        height: auto !important;
        overflow: visible !important;
        display: block !important;
        background: white !important;
    }
    
    #right-panel, #itinerary-content {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        background: white !important;
        padding: 0 !important;
    }
    
    /* Render all major content tabs sequentially in the print layout */
    #tab-itinerary, #tab-budget, #tab-tips, #tab-aturan, #tab-kendaraan {
        display: block !important;
        page-break-after: always;
        break-after: page;
        padding: 20px 0 !important;
        background: white !important;
    }
    
    #tab-rute {
        display: none !important;
    }

    /* Print color adjustment for primary elements */
    .bg-white {
        background-color: #fff !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .text-stone-800 {
        color: #1A1C1E !important;
    }
    
    .bg-primary {
        background-color: #004777 !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Reliable toggle state for the left/right panels, independent of the
   responsive hidden/md:flex pair used for the initial mobile/desktop state. */
.force-hidden {
    display: none !important;
}
</style>

<div class="flex h-screen overflow-hidden bg-slate-50 font-sans text-neutral-color">

    {{-- ========== LEFT COLUMN: Sidebar ========== --}}
    <aside id="left-sidebar" class="w-[260px] md:w-[280px] bg-white border-r border-stone-200 hidden md:flex flex-col shrink-0 z-20 transition-all duration-300">
        {{-- Logo & App Name --}}
        <div class="px-6 py-5 border-b border-stone-100">
            <a href="{{ route('landing') }}" class="flex flex-col gap-0.5 hover:opacity-80 transition-opacity">
                <h1 class="font-heading font-extrabold text-xl text-primary tracking-tight">Toba <span class="text-secondary">Itinerary</span></h1>
                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">Asisten Itinerary</p>
            </a>
        </div>

        {{-- New Itinerary Button --}}
        <div class="px-5 py-5">
            <button onclick="resetChat()" class="w-full flex items-center justify-center gap-2 bg-primary text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm shadow-primary/20">
                <span class="material-icons-round text-[18px]">add</span>
                Itinerary Baru
            </button>
        </div>

        {{-- Navigation Menu --}}
        <div class="px-3 py-2 space-y-1">
            <a href="{{ route('landing') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50 hover:text-primary rounded-xl transition-colors group">
                <span class="material-icons-round text-stone-400 group-hover:text-primary transition-colors text-[20px]">home</span>
                Home
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50 hover:text-primary rounded-xl transition-colors group">
                <span class="material-icons-round text-stone-400 group-hover:text-primary transition-colors text-[20px]">bookmark_border</span>
                Itinerary Disimpan
            </a>
        </div>

        {{-- History Section --}}
        <div class="flex-1 overflow-y-auto px-3 py-4 mt-2">
            <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest px-4 mb-3">Riwayat Itinerary</p>
            <div id="history-list" class="space-y-1.5">
                <p class="text-xs text-stone-400 px-4 py-2">Belum ada riwayat percakapan.</p>
            </div>
        </div>

        {{-- Settings Button --}}
        <div class="p-4 border-t border-stone-100">
            <button class="flex items-center gap-3 w-full px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50 rounded-xl transition-colors group">
                <span class="material-icons-round text-stone-400 group-hover:text-stone-600 text-[20px]">settings</span>
                Settings
            </button>
        </div>
    </aside>

    {{-- ========== MIDDLE COLUMN: Itinerary Results ========== --}}
    <main class="flex-1 flex flex-col overflow-hidden bg-slate-50 relative z-10">
        
        {{-- Mobile Header (shows only on small screens) --}}
        <nav class="md:hidden flex-shrink-0 bg-white border-b border-stone-200 px-4 py-3 flex items-center justify-between z-10">
            <h1 class="font-heading font-extrabold text-lg text-primary tracking-tight">Toba <span class="text-secondary">Itinerary</span></h1>
            <button onclick="toggleMobileSidebar()" class="text-stone-600"><span class="material-icons-round">menu</span></button>
        </nav>

        {{-- Action Buttons (Export/Reset/Toggles) --}}
        <div class="absolute top-4 left-4 z-20 flex gap-2">
            <button onclick="toggleLeftSidebar()" class="hidden md:flex items-center justify-center bg-white border border-stone-200 text-stone-600 w-9 h-9 rounded-lg hover:border-primary hover:text-primary transition-all shadow-sm">
                <span class="material-icons-round text-[18px]">menu_open</span>
            </button>
        </div>
        <div class="absolute top-4 right-6 z-20 flex gap-2">
            <button id="btn-export" onclick="exportItinerary()" class="hidden items-center gap-1.5 bg-white border border-stone-200 text-stone-600 text-xs font-semibold px-3 py-1.5 rounded-lg hover:border-primary hover:text-primary transition-all shadow-sm">
                <span class="material-icons-round text-[16px]">download</span> Ekspor
            </button>
            <button onclick="toggleRightSidebar()" class="hidden md:flex items-center justify-center bg-white border border-stone-200 text-stone-600 w-9 h-9 rounded-lg hover:border-primary hover:text-primary transition-all shadow-sm">
                <span class="material-icons-round text-[18px]">chat</span>
            </button>
        </div>

        {{-- Empty state (shown before itinerary loads) --}}
        <div id="empty-state" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50">
            <div class="w-24 h-24 bg-white rounded-[2rem] shadow-sm flex items-center justify-center mb-6 border border-stone-100 relative overflow-hidden">
                <div class="absolute inset-0 bg-primary/5"></div>
                <span class="material-icons-round text-primary text-5xl relative z-10">explore</span>
            </div>
            <h3 class="font-heading font-extrabold text-neutral-color text-2xl mb-3">Belum ada Itinerary</h3>
            <p class="text-stone-500 text-sm max-w-sm leading-relaxed">
                Silakan mulai percakapan dengan AI Assistant di sebelah kanan untuk merencanakan liburan impian Anda.
            </p>
        </div>

        {{-- Itinerary content (hidden until generated) --}}
        <div id="itinerary-content" class="hidden flex-1 overflow-y-auto p-4 md:p-8">
            
            {{-- Header Stats --}}
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm shadow-stone-200/50 p-6 mb-6">
                <h2 class="font-heading font-extrabold text-2xl text-neutral-color mb-6 flex items-center gap-2">
                    Ringkasan Itinerary
                </h2>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4 border border-stone-100 flex flex-col justify-center items-center text-center group hover:border-primary/20 transition-colors">
                        <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Lokasi</p>
                        <p id="summary-location" class="font-heading font-bold text-neutral-color text-sm md:text-base w-full truncate"></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-stone-100 flex flex-col justify-center items-center text-center group hover:border-primary/20 transition-colors">
                        <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Biaya</p>
                        <p id="summary-budget" class="font-heading font-bold text-neutral-color text-sm md:text-base w-full truncate"></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-stone-100 flex flex-col justify-center items-center text-center group hover:border-primary/20 transition-colors">
                        <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Waktu Berlibur</p>
                        <p id="summary-duration" class="font-heading font-bold text-neutral-color text-sm md:text-base w-full truncate"></p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-stone-100 flex flex-col justify-center items-center text-center group hover:border-primary/20 transition-colors">
                        <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1.5">Total Tempat</p>
                        <p id="summary-places" class="font-heading font-bold text-neutral-color text-sm md:text-base w-full truncate"></p>
                    </div>
                </div>

                {{-- Hidden elements to keep JS parsing happy --}}
                <div class="hidden">
                    <span id="dest-name"></span>
                    <span id="dest-province"></span>
                    <span id="stat-duration"></span>
                    <span id="stat-budget"></span>
                    <span id="stat-places"></span>
                    <span id="pill-destination"></span>
                    <span id="pill-duration"></span>
                    <span id="pill-budget"></span>
                </div>
            </div>

            {{-- Navigation Tabs --}}
            <div class="flex border-b border-stone-200 mb-8 mx-1">
                <button onclick="switchTab('itinerary')" data-tab="itinerary" class="flex-1 py-3.5 text-sm font-bold text-primary border-b-[3px] border-primary transition-all text-center">
                    Jadwal
                </button>
                <button onclick="switchTab('rute')" data-tab="rute" class="flex-1 py-3.5 text-sm font-medium text-stone-400 border-b-[3px] border-transparent hover:text-stone-600 transition-all text-center">
                    Rute dan Peta
                </button>
                <button onclick="switchTab('kendaraan')" data-tab="kendaraan" class="flex-1 py-3.5 text-sm font-medium text-stone-400 border-b-[3px] border-transparent hover:text-stone-600 transition-all text-center">
                    Kendaraan
                </button>
                
                {{-- Hidden tabs from original codebase to prevent JS errors --}}
                <div class="hidden">
                    <button onclick="switchTab('budget')" data-tab="budget"></button>
                    <button onclick="switchTab('tips')" data-tab="tips"></button>
                    <button onclick="switchTab('aturan')" data-tab="aturan"></button>
                </div>
            </div>

            {{-- Tab Content: Itinerary (Jadwal) --}}
            <div id="tab-itinerary" class="space-y-8">
                <div id="day-cards" class="space-y-6"></div>
                
                {{-- Mini Widgets Grid (moved below schedule) --}}
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 pt-6 border-t border-stone-200">
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons-round text-primary text-[18px]">wb_sunny</span>
                            <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">Cuaca</p>
                        </div>
                        <p id="widget-weather" class="text-sm font-semibold text-neutral-color leading-relaxed"></p>
                    </div>
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons-round text-primary text-[18px]">account_balance_wallet</span>
                            <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">Detail Budget</p>
                        </div>
                        <p id="widget-detail-budget" class="text-sm font-semibold text-neutral-color leading-relaxed"></p>
                    </div>
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons-round text-primary text-[18px]">checklist</span>
                            <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">Checklist</p>
                        </div>
                        <ul id="widget-checklist" class="text-sm font-medium text-stone-600 space-y-1.5 ml-1"></ul>
                    </div>
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons-round text-primary text-[18px]">sticky_note_2</span>
                            <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">Catatan AI</p>
                        </div>
                        <p id="widget-notes" class="text-sm font-medium text-stone-600 leading-relaxed"></p>
                    </div>
                </div>
            </div>

            {{-- Tab Content: Rute --}}
            <div id="tab-rute" class="hidden flex flex-col gap-6">
                <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
                    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-2 flex flex-col h-[500px]">
                        <div id="map-route" class="flex-1 rounded-xl overflow-hidden z-0"></div>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 flex-1 max-h-[500px] overflow-y-auto">
                            <h3 class="font-heading font-extrabold text-neutral-color mb-5">Rute Harian</h3>
                            <div id="route-timeline" class="space-y-4 text-sm text-stone-600"></div>
                        </div>
                        <div id="route-summary" class="hidden"></div>
                    </div>
                </div>
            </div>

            {{-- Tab Content: Kendaraan --}}
            <div id="tab-kendaraan" class="hidden space-y-6">
                {{-- Search & Stats Header --}}
                <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <h3 class="font-heading font-extrabold text-neutral-color text-xl">Transportasi</h3>
                        <div class="relative w-full md:w-72">
                            <input type="text" id="kendaraan-search" placeholder="Cari Kendaraan..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-stone-200 rounded-xl text-sm font-medium text-stone-700 focus:bg-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
                            <span class="material-icons-round text-stone-400 absolute left-3 top-2.5 text-[20px]">search</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="border border-stone-200 rounded-xl p-4 flex items-center justify-between group hover:border-primary/20 transition-colors">
                            <div>
                                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1">Total Kendaraan</p>
                                <p id="vehicle-total" class="font-heading font-extrabold text-3xl text-neutral-color">0</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-slate-50 text-stone-400 flex items-center justify-center">
                                <span class="material-icons-round text-[24px]">directions_car</span>
                            </div>
                        </div>
                        <div class="border border-stone-200 rounded-xl p-4 flex items-center justify-between group hover:border-secondary/30 transition-colors">
                            <div>
                                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1">Tersedia</p>
                                <p id="vehicle-available" class="font-heading font-extrabold text-3xl text-neutral-color">0</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-secondary/10 text-secondary flex items-center justify-center">
                                <span class="material-icons-round text-[24px]">check_circle</span>
                            </div>
                        </div>
                        <div class="border border-stone-200 rounded-xl p-4 flex items-center justify-between group hover:border-amber-500/30 transition-colors">
                            <div>
                                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1">Digunakan</p>
                                <p id="vehicle-in-use" class="font-heading font-extrabold text-3xl text-neutral-color">0</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center">
                                <span class="material-icons-round text-[24px]">timelapse</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- List Kendaraan --}}
                <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="font-heading font-bold text-neutral-color text-base">Daftar Kendaraan</h4>
                        <div class="flex gap-2">
                            <button class="bg-slate-50 hover:bg-slate-100 text-stone-600 border border-stone-200 text-xs font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-1">
                                Status <span class="material-icons-round text-[16px]">arrow_drop_down</span>
                            </button>
                            <button class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-1 shadow-sm">
                                <span class="material-icons-round text-[16px]">add</span> Tambah
                            </button>
                        </div>
                    </div>
                    <div id="kendaraan-content" class="space-y-4">
                        {{-- Injected by JS --}}
                    </div>
                </div>
            </div>
            
            {{-- Hidden containers for other tabs to keep JS happy --}}
            <div id="tab-budget" class="hidden"><div id="budget-content"></div></div>
            <div id="tab-tips" class="hidden"><div id="tips-content"></div></div>
            <div id="tab-aturan" class="hidden"><div id="aturan-content"></div></div>

        </div> {{-- /itinerary-content --}}
    </main>

    {{-- ========== RIGHT COLUMN: Chatbot ========== --}}
    <aside id="right-sidebar" class="w-full md:w-[340px] xl:w-[380px] bg-white border-l border-stone-200 flex flex-col shrink-0 shadow-[-8px_0_30px_rgba(0,0,0,0.03)] z-20 transition-all duration-300">
        {{-- Chat Header --}}
        <div class="relative flex flex-col items-center justify-center px-6 py-5 bg-white border-b border-stone-200">
            <button onclick="toggleRightSidebar()" class="absolute top-4 right-4 text-stone-600 hover:text-stone-900 transition-colors">
                <span class="material-icons-round text-2xl">close</span>
            </button>
            <div class="flex flex-col items-center gap-1.5 w-full">
                <div class="flex items-center justify-center">
                    <span class="material-icons-round text-2xl text-stone-600">smart_display</span>
                </div>
                <h3 class="font-heading font-extrabold text-primary text-[22px] tracking-tight">AI Assistant</h3>
            </div>
        </div>

        {{-- Chat Messages --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-5 space-y-6 scroll-smooth bg-slate-50/30">
            {{-- Initial AI Greeting --}}
            <div class="flex flex-col items-center justify-center text-center mt-2 mb-4 gap-4 border-b border-stone-200 pb-8">
                <p class="text-[13px] text-stone-700 font-medium leading-relaxed px-4 max-w-[240px]">
                    Halo, saya siap membantu<br>Anda merencanakan perjalanan
                </p>
                <div class="bg-[#003355] text-white rounded-xl px-5 py-2.5 flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:bg-[#002244] transition-all w-[200px]">
                    <span class="material-icons-round text-[18px]">light_mode</span>
                    <span class="text-xs font-semibold">Saran hari ini</span>
                    <span class="material-icons-round text-[18px] ml-auto">chevron_right</span>
                </div>
            </div>
        </div>

        {{-- Chat Input Form --}}
        <div class="p-4 bg-slate-50/50">
            <form id="chat-form" class="flex items-end gap-3">
                <div class="relative flex-1 bg-white border border-stone-300 rounded-xl overflow-hidden">
                    <textarea
                        id="chat-input"
                        rows="1"
                        placeholder="Tanyakan Sesuatu"
                        class="w-full bg-transparent pl-4 pr-4 py-3.5 text-[13px] font-medium text-stone-700 placeholder-stone-500 outline-none resize-none leading-relaxed focus:ring-0 max-h-32"
                        style="overflow-y: hidden;"
                    ></textarea>
                </div>
                <button type="submit"
                        id="send-btn"
                        class="w-[46px] h-[46px] flex items-center justify-center bg-white border border-stone-300 text-stone-600 hover:text-primary hover:border-primary rounded-xl transition-all flex-shrink-0 shadow-sm">
                    <span class="material-icons-round text-[20px] ml-1 mb-0.5 transform -rotate-45">send</span>
                </button>
            </form>
        </div>
    </aside>

</div>

{{-- ── Loading overlay ── --}}
<div id="loading-overlay" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-white rounded-3xl p-8 shadow-2xl text-center max-w-xs w-full mx-4 border border-stone-100">
        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-5 animate-pulse">
            <span class="material-icons-round text-primary text-3xl">travel_explore</span>
        </div>
        <p class="font-heading font-extrabold text-neutral-color mb-1.5 text-lg">Menyusun Itinerary</p>
        <p class="text-xs font-medium text-stone-400" id="loading-tip">AI sedang meracik jadwal terbaik untukmu...</p>
    </div>
</div>

<script>
// ============================================================
//  NusantaraAI Dashboard — JavaScript
// ============================================================

const GEMINI_API_URL = '/api/gemini'; // proxied through Laravel
let conversationHistory = [];
let currentItinerary = null;

// ── Riwayat percakapan (persisted di localStorage per-browser) ─
const HISTORY_KEY = 'toba_itinerary_history';
let currentConversationId = null;

function loadHistoryList() {
    try {
        return JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
    } catch (e) {
        return [];
    }
}

function saveHistoryList(list) {
    try {
        localStorage.setItem(HISTORY_KEY, JSON.stringify(list));
    } catch (e) {
        console.error('Gagal menyimpan riwayat:', e);
    }
}

function conversationTitleFrom(text) {
    const clean = (text || '').trim().replace(/\s+/g, ' ');
    return clean.length > 60 ? clean.slice(0, 57) + '...' : (clean || 'Percakapan baru');
}

// Simpan/perbarui satu percakapan tanpa menghapus percakapan lain,
// lalu pindahkan ke urutan teratas dan re-render sidebar.
function upsertConversation(id, updates) {
    const list = loadHistoryList();
    const idx = list.findIndex(c => c.id === id);
    const clean = {};
    Object.keys(updates).forEach(k => {
        if (updates[k] !== undefined) clean[k] = updates[k];
    });

    let entry;
    if (idx === -1) {
        entry = { id, title: 'Percakapan baru', messages: [], itinerary: null, updatedAt: Date.now(), ...clean };
        list.unshift(entry);
    } else {
        entry = { ...list[idx], ...clean, updatedAt: Date.now() };
        list.splice(idx, 1);
        list.unshift(entry);
    }
    saveHistoryList(list);
    renderHistorySidebar();
}

function renderHistorySidebar() {
    const container = document.getElementById('history-list');
    if (!container) return;
    const list = loadHistoryList();

    if (!list.length) {
        container.innerHTML = '<p class="text-xs text-stone-400 px-4 py-2">Belum ada riwayat percakapan.</p>';
        return;
    }

    container.innerHTML = list.map(c => {
        const active = c.id === currentConversationId;
        return `
        <div data-id="${c.id}" onclick="loadConversation('${c.id}')"
             class="${active ? 'bg-secondary/10 border border-secondary/20 hover:bg-secondary/15' : 'hover:bg-stone-50 border border-transparent'} rounded-xl p-3 cursor-pointer group transition-colors">
            <p class="text-xs font-semibold ${active ? 'text-secondary' : 'text-stone-500 font-medium'} leading-snug">${escapeHtml(c.title)}</p>
        </div>`;
    }).join('');
}

// Mulai percakapan baru sepenuhnya di sisi klien (tanpa reload),
// riwayat percakapan sebelumnya tetap tersimpan di localStorage.
function startNewConversation() {
    currentConversationId = 'conv_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
    conversationHistory = [];
    currentItinerary = null;

    // Reset panel chat ke sapaan awal
    chatMessages.innerHTML = `
        <div class="flex flex-col items-center justify-center text-center mt-2 mb-4 gap-4 border-b border-stone-200 pb-8">
            <p class="text-[13px] text-stone-700 font-medium leading-relaxed px-4 max-w-[240px]">
                Halo, saya siap membantu<br>Anda merencanakan perjalanan
            </p>
            <div class="bg-[#003355] text-white rounded-xl px-5 py-2.5 flex items-center justify-center gap-2 cursor-pointer shadow-sm hover:bg-[#002244] transition-all w-[200px]">
                <span class="material-icons-round text-[18px]">light_mode</span>
                <span class="text-xs font-semibold">Saran hari ini</span>
                <span class="material-icons-round text-[18px] ml-auto">chevron_right</span>
            </div>
        </div>`;

    // Reset panel tengah ke empty state
    itineraryContent.classList.add('hidden');
    itineraryContent.classList.remove('flex', 'flex-col');
    emptyState.classList.remove('hidden');
    btnExport.classList.add('hidden');
    btnExport.classList.remove('flex');
    tripPill?.classList.add('hidden');
    tripPill?.classList.remove('flex');

    renderHistorySidebar();
}

// Muat kembali percakapan lama dari riwayat: pesan chat & itinerary (jika ada).
function loadConversation(id) {
    const list = loadHistoryList();
    const convo = list.find(c => c.id === id);
    if (!convo) return;

    currentConversationId = id;
    conversationHistory = convo.messages || [];
    currentItinerary = convo.itinerary || null;

    chatMessages.innerHTML = '';
    conversationHistory.forEach(m => {
        const text = m.parts?.[0]?.text || '';
        if (!text) return;
        if (m.role === 'user') appendUserMessage(text);
        else appendAIMessage(text);
    });

    if (currentItinerary) {
        renderItinerary(currentItinerary);
    } else {
        itineraryContent.classList.add('hidden');
        itineraryContent.classList.remove('flex', 'flex-col');
        emptyState.classList.remove('hidden');
        btnExport.classList.add('hidden');
        btnExport.classList.remove('flex');
        tripPill?.classList.add('hidden');
        tripPill?.classList.remove('flex');
    }

    renderHistorySidebar();
}

// ── DOM refs ─────────────────────────────────────────────────
const chatMessages   = document.getElementById('chat-messages');
const chatForm       = document.getElementById('chat-form');
const chatInput      = document.getElementById('chat-input');
const kendaraanSearch = document.getElementById('kendaraan-search');
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
        handleChatSubmit(chatInput.value.trim());
    }
});

// ── Chat form submit ──────────────────────────────────────────
async function handleChatSubmit(message) {
    if (!message) return;
    if (!currentConversationId) startNewConversation();

    chatInput.value = '';
    chatInput.style.height = 'auto';
    appendUserMessage(message);
    conversationHistory.push({ role: 'user', parts: [{ text: message }] });

    // Pesan pertama di percakapan ini menentukan judul riwayat.
    const isFirstMessage = conversationHistory.length === 1;
    upsertConversation(currentConversationId, {
        title: isFirstMessage ? conversationTitleFrom(message) : undefined,
        messages: conversationHistory,
    });

    appendTypingIndicator();

    try {
        const response = await sendToGemini(conversationHistory);
        removeTypingIndicator();

        if (response.itinerary) {
            renderItinerary(response.itinerary);
            appendAIMessage(response.message || `Itinerary untuk <strong>${response.itinerary.destination}</strong> sudah siap! Cek panel kiri ya. Ada yang ingin diubah?`);
            conversationHistory.push({ role: 'model', parts: [{ text: response.message || '' }] });
        } else {
            appendAIMessage(response.message);
            conversationHistory.push({ role: 'model', parts: [{ text: response.message }] });
        }

        upsertConversation(currentConversationId, {
            messages: conversationHistory,
            itinerary: currentItinerary,
        });
    } catch (err) {
        removeTypingIndicator();
        appendAIMessage(err.serverMessage || 'Maaf, terjadi gangguan koneksi. Silakan coba lagi sebentar.');
        console.error(err);

        upsertConversation(currentConversationId, { messages: conversationHistory });
    }
}

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    handleChatSubmit(chatInput.value.trim());
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

    // Baca body sebagai teks dulu, karena kalau server error 500/419 di Laravel,
    // responsnya bisa berupa halaman HTML (bukan JSON) — res.json() langsung
    // akan melempar error generik yang menyembunyikan pesan aslinya.
    const rawBody = await res.text();
    let data = null;
    try {
        data = rawBody ? JSON.parse(rawBody) : null;
    } catch (parseErr) {
        console.error('Respon /api/gemini bukan JSON valid. Status:', res.status, 'Body:', rawBody.slice(0, 500));
        throw new Error(`Server mengembalikan respon tidak valid (HTTP ${res.status}). Cek console untuk detail.`);
    }

    if (!res.ok) {
        console.error('Gemini API error. Status:', res.status, 'Body:', data);
        const err = new Error(data?.message || `HTTP ${res.status}`);
        err.serverMessage = data?.message;
        throw err;
    }

    return data;
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
    tripPill?.classList.remove('hidden');
    tripPill?.classList.add('flex');

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

    const summaryPanel = document.getElementById('itinerary-summary-panel');
    if (summaryPanel) {
        summaryPanel.classList.remove('hidden');
    }
    document.getElementById('summary-location').textContent = data.destination || '—';
    document.getElementById('summary-budget').textContent   = data.total_budget || 'Rp 0';
    document.getElementById('summary-duration').textContent = `${data.days} Hari`;
    document.getElementById('summary-places').textContent   = `${data.total_places || (data.schedule?.reduce((sum, day) => sum + (day.activities?.length || 0), 0)) || 0} Tempat`;

    document.getElementById('widget-weather').textContent = data.weather_summary || 'Perkiraan cuaca dasar akan muncul setelah itinerary dibuat.';
    document.getElementById('widget-detail-budget').textContent = data.budget_details || 'Estimasi lengkap tersedia di tab Budget.';
    document.getElementById('widget-checklist').innerHTML = (data.checklist || ['Bawa pakaian nyaman', 'Siapkan uang tunai kecil', 'Isi daya ponsel']).map(item => `<li>• ${escapeHtml(item)}</li>`).join('');
    document.getElementById('widget-notes').textContent = data.notes || 'Catatan khusus akan muncul di sini jika tersedia.';

    const dayCards = document.getElementById('day-cards');
    if (dayCards) {
        dayCards.innerHTML = '';
        (data.schedule || []).forEach((day, i) => {
            dayCards.innerHTML += buildDayCard(day, i + 1);
        });
    }

    // Render budget
    renderBudget(data.budget || []);

    // Render tips
    renderTips(data.tips || []);

    // Render aturan tempat wisata
    renderAturan(data.rules || data.aturan || []);

    // Render rekomendasi kendaraan
    setVehicleRecommendations(data.transportation || data.kendaraan || []);

    // ── Fetch Batch Images & Coordinates ──
    const places = [];
    (data.schedule || []).forEach(day => {
        (day.activities || []).forEach(act => {
            const loc = act.location || act.place;
            if (loc) places.push(loc);
        });
    });

    if (places.length > 0) {
        fetch('/api/destinations/images', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ places: [...new Set(places)] }),
        })
        .then(res => res.json())
        .then(resData => {
            const imagesMap = resData.images || {};
            window.placeMetadata = imagesMap; // simpan untuk rendering peta

            // Update gambar & credit di DOM
            for (const [place, info] of Object.entries(imagesMap)) {
                if (info && info.url) {
                    const safePlace = place.replace(/"/g, '\\"');
                    document.querySelectorAll(`img[data-place="${safePlace}"]`).forEach(img => {
                        img.src = info.url;
                    });
                    document.querySelectorAll(`[data-credit-place="${safePlace}"]`).forEach(el => {
                        el.textContent = info.credit || '';
                        el.classList.remove('hidden');
                    });
                }
            }

            // Render ulang peta dengan koordinat dinamis baru
            renderMap(data);
        })
        .catch(err => {
            console.error('Gagal mengambil gambar destinasi dinamis:', err);
            renderMap(data);
        });
    } else {
        renderMap(data);
    }
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

// Palet warna aksen per hari (siklus), dipakai untuk strip warna & label vertikal "Hari N"
const DAY_ACCENT_COLORS = ['#2D6A4F', '#004777', '#D4872E', '#B4691B', '#265C42', '#002B47'];

function buildDayCard(day, dayNum) {
        const dayColor = '#2D6A4F';
    const acts = day.activities || [];

    const tiles = acts.map((act, idx) => {
        // Dukungan dua format:
        // Format BARU: { action, location, ... }  → tampil dua baris
        // Format LAMA: { place, ... }             → fallback satu baris (kompatibel mundur)
        const hasActionLocation = act.action && act.location;
        const displayLocation   = hasActionLocation ? act.location : (act.place || '');
        const displayAction     = hasActionLocation ? act.action   : null;

        const isFirst = idx === 0;

        const cardHtml = `
        <div class="bg-white w-[280px] sm:w-[320px] flex-shrink-0 snap-center rounded-2xl border border-stone-100 overflow-hidden flex hover:shadow-md transition-shadow">
            ${isFirst ? `
            <div class="w-7 flex-shrink-0 flex items-center justify-center" style="background:${dayColor};">
                <span class="text-white text-[10px] font-bold tracking-wide whitespace-nowrap" style="writing-mode: vertical-rl; transform: rotate(180deg);">Hari ${dayNum}</span>
            </div>
            ` : ''}
            <div class="flex-1 min-w-0 flex flex-col">
                <div class="relative h-32 overflow-hidden" style="background:${getIllustrationAccent(act.category)};">
                    <img
                        data-place="${escapeHtml(displayLocation)}"
                        src="${getIllustrationSrc(act.category)}"
                        alt="Ilustrasi ${displayLocation}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    />
                    <div class="absolute top-2 left-2 bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold px-2 py-1 rounded-full flex items-center gap-1">
                        <span class="material-icons-round text-[12px]">${getActivityIcon(act.category)}</span>
                        ${act.time || ''}
                    </div>
                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent px-3 py-2.5 pointer-events-none">
                        ${displayAction
                            ? `<p class="text-white/80 text-[10px] uppercase tracking-wider leading-tight">${displayAction}</p>
                               <p class="text-white text-sm font-semibold truncate drop-shadow line-clamp-1">${displayLocation}</p>`
                            : `<p class="text-white text-sm font-semibold truncate drop-shadow line-clamp-1">${displayLocation}</p>`
                        }
                        <p class="text-[9px] text-white/50 truncate hidden mt-0.5" data-credit-place="${escapeHtml(displayLocation)}"></p>
                    </div>
                </div>
                <div class="p-3.5 flex-1 flex flex-col">
                    ${act.description ? `<p class="text-[11px] text-stone-500 leading-relaxed mb-2.5 line-clamp-2 flex-1">${act.description}</p>` : '<div class="flex-1"></div>'}
                    <div class="mt-auto">
                        ${buildPriceBadge(act)}
                    </div>
                </div>
            </div>
        </div>`;

        const connectorHtml = `<div class="h-1 w-6 sm:w-10 bg-stone-200 flex-shrink-0 self-center rounded-full"></div>`;

        return isFirst ? cardHtml : connectorHtml + cardHtml;
    }).join('');

    const totalMin = acts.reduce((s, a) => s + (parseInt(a.harga_min) || 0), 0);
    const totalMax = acts.reduce((s, a) => s + (parseInt(a.harga_max) || 0), 0);
    const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');

    return `
        <div>
            <div class="flex items-center justify-between mb-4 px-1">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:${dayColor};">
                        <span class="text-white text-xs font-bold font-heading">${dayNum}</span>
                    </div>
                    <div>
                        <p class="text-sm font-extrabold font-heading text-neutral-color">Hari ${dayNum}</p>
                        ${day.title ? `<p class="text-xs text-stone-400">${day.title}</p>` : ''}
                    </div>
                </div>
                <div class="text-right">
                    ${(totalMin > 0 || totalMax > 0) ? `<p class="text-xs font-extrabold text-primary">${fmt(totalMin)} – ${fmt(totalMax)}</p>` : ''}
                    <p class="text-xs text-stone-400">${acts.length} aktivitas</p>
                </div>
            </div>
            <div class="flex items-stretch overflow-x-auto pb-6 pt-2 snap-x snap-mandatory scroll-smooth px-1" style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    /* Hides scrollbar for Chrome, Safari and Opera */
                    .flex.overflow-x-auto::-webkit-scrollbar {
                        display: none;
                    }
                </style>
                ${tiles}
            </div>
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

function buildPriceBadge(act) {
    const min = parseInt(act.harga_min);
    const max = parseInt(act.harga_max);
    const wna = act.wna_price || null;
    const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
 
    // Warna badge berdasarkan kategori
    const colorMap = {
        'kuliner'  : 'bg-orange-50 text-orange-600 border-orange-100',
        'hotel'    : 'bg-blue-50 text-blue-600 border-blue-100',
        'transport': 'bg-purple-50 text-purple-600 border-purple-100',
        'belanja'  : 'bg-amber-50 text-amber-600 border-amber-100',
        'alam'     : 'bg-emerald/10 text-emerald border-emerald/20',
        'pantai'   : 'bg-emerald/10 text-emerald border-emerald/20',
        'museum'   : 'bg-emerald/10 text-emerald border-emerald/20',
        'budaya'   : 'bg-emerald/10 text-emerald border-emerald/20',
    };
    const color = colorMap[act.category?.toLowerCase()] || 'bg-stone-100 text-stone-500 border-stone-200';
 
    if (!isNaN(min) && !isNaN(max)) {
 
        // Kasus: GRATIS (0 - 0)
        if (min === 0 && max === 0) {
            return `<div class="flex flex-col items-end gap-0.5 flex-shrink-0">
                <span class="text-xs bg-emerald/10 text-emerald border border-emerald/20 px-2 py-0.5 rounded-full font-medium">
                    ✓ Gratis
                </span>
                ${wna ? `<span class="text-xs text-stone-400">WNA: ${wna}</span>` : ''}
            </div>`;
        }
 
        // Kasus: harga pasti / fixed (min == max)
        if (min === max) {
            return `<div class="flex flex-col items-end gap-0.5 flex-shrink-0">
                <span class="text-xs ${color} border px-2 py-0.5 rounded-full font-medium">
                    ± ${fmt(min)}
                </span>
                ${wna ? `<span class="text-xs text-stone-400 font-medium">WNA: ${wna}</span>` : ''}
            </div>`;
        }
 
        // Kasus: range harga normal
        return `<div class="flex flex-col items-end gap-0.5 flex-shrink-0">
            <span class="text-xs ${color} border px-2 py-0.5 rounded-full font-medium">
                ± ${fmt(min)} – ${fmt(max)}
            </span>
            ${wna ? `<span class="text-xs text-stone-400 font-medium">WNA: ${wna}</span>` : ''}
        </div>`;
    }
 
    // Fallback: kalau Gemini masih kirim field ticket lama
    if (act.ticket) {
        return `<div class="flex-shrink-0">
            <span class="text-xs bg-emerald/10 text-emerald-dark border border-emerald/20 px-2 py-0.5 rounded-full">
                ${act.ticket}
            </span>
        </div>`;
    }
 
    return '';
}

function renderBudget(items) {
    const container = document.getElementById('budget-content');
    if (!items.length) {
        container.innerHTML = '<p class="text-stone-400 text-sm text-center py-8">Data budget akan tersedia setelah itinerary dibuat.</p>';
        return;
    }
    const total = items.reduce((sum, i) => sum + (i.amount || 0), 0);
    container.innerHTML = `
        <div class="bg-primary text-white rounded-2xl p-5 mb-4">
            <p class="text-primary-light text-xs mb-1">Total Estimasi Budget</p>
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
    if (!rules || rules.length === 0) {
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="material-icons-round text-stone-300 text-3xl">gavel</span>
                </div>
                <p class="text-stone-400 text-sm text-center">Aturan tempat wisata akan tersedia setelah itinerary dibuat.</p>
            </div>`;
        return;
    }

    const intro = `
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-xl px-4 py-3 mb-3 flex items-start gap-2.5 shadow-sm">
            <span class="material-icons-round text-amber-500 text-base mt-0.5 flex-shrink-0">info</span>
            <p class="text-xs text-amber-800 leading-relaxed">
                <strong>Penting:</strong> Aturan berikut didasarkan pada kearifan lokal, regulasi konservasi, dan protokol keselamatan. Selalu tanyakan ke petugas setempat karena kebijakan dapat berubah.
            </p>
        </div>`;

    container.innerHTML = intro + rules.map((item, idx) => {
        const placeName = item.place || item.location || '';
        const rulesList = item.rules || (Array.isArray(item.items) ? item.items : []);
        const hasRules = rulesList && rulesList.length > 0;
        
        return `
        <div class="bg-white rounded-xl border border-stone-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-stone-50 to-white border-b border-stone-100">
                <div class="w-10 h-10 ${getAturanBgColor(item.category)} rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-icons-round text-base ${getAturanIconColor(item.category)}">${getAturanIcon(item.category)}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-stone-800">${placeName || 'Tempat Wisata'}</p>
                    ${item.category ? `<p class="text-xs text-stone-400 capitalize">${item.category}</p>` : ''}
                </div>
                ${hasRules ? `<span class="text-xs font-medium px-2.5 py-1 bg-stone-100 text-stone-600 rounded-full">${rulesList.length} aturan</span>` : ''}
            </div>
            ${hasRules ? `
            <ul class="px-4 py-3 space-y-2.5">
                ${rulesList.map(r => renderAturanLine(r)).join('')}
            </ul>` : `<div class="px-4 py-3"><p class="text-sm text-stone-400 italic">Tidak ada aturan khusus terdaftar untuk lokasi ini.</p></div>`}
            ${item.source_note ? `<p class="px-4 pb-2.5 text-xs text-stone-300 leading-relaxed border-t border-stone-50">📌 ${item.source_note}</p>` : ''}
        </div>`;
    }).join('');
}

function renderAturanLine(r) {
    const text = typeof r === 'string' ? r : (r.text || '');
    const type = typeof r === 'string' ? 'anjuran' : (r.type || 'anjuran');
    
    const badges = {
        wajib:    { label: 'Wajib',    cls: 'bg-blue-50 text-blue-700 border border-blue-100',    icon: 'check_circle' },
        larangan: { label: 'Larangan', cls: 'bg-rose-50 text-rose-700 border border-rose-100',    icon: 'cancel' },
        anjuran:  { label: 'Anjuran',  cls: 'bg-emerald-50 text-emerald-700 border border-emerald-100', icon: 'lightbulb' },
        peringatan: { label: 'Peringatan', cls: 'bg-amber-50 text-amber-700 border border-amber-100', icon: 'warning' }
    }[type] || { label: 'Info', cls: 'bg-stone-100 text-stone-600 border border-stone-200', icon: 'info' };

    return `
        <li class="flex items-start gap-2.5 group">
            <div class="flex-shrink-0 mt-0.5">
                <span class="material-icons-round text-base group-hover:scale-110 transition-transform ${badges.cls.split(' ')[1]}">${badges.icon}</span>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-[11px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full ${badges.cls} inline-block">${badges.label}</span>
                <p class="text-sm text-stone-700 leading-relaxed mt-1">${escapeHtml(text)}</p>
            </div>
        </li>`;
}

function getAturanIcon(category) {
    const icons = {
        'religi': 'temple_hindu', 'religius': 'temple_hindu', 'pantai': 'beach_access',
        'gunung': 'landscape', 'konservasi': 'eco', 'cagar budaya': 'museum',
        'taman nasional': 'park', 'air terjun': 'water_drop', 'desa adat': 'holiday_village',
        'air panas': 'local_fire_department', 'gua': 'public', 'kebun': 'agriculture',
        'default': 'gavel'
    };
    return icons[category?.toLowerCase()] || icons.default;
}

function getAturanBgColor(category) {
    const colors = {
        'religi': 'bg-yellow-100', 'religius': 'bg-yellow-100', 'pantai': 'bg-cyan-100',
        'gunung': 'bg-green-100', 'konservasi': 'bg-emerald-100', 'cagar budaya': 'bg-purple-100',
        'taman nasional': 'bg-teal-100', 'air terjun': 'bg-blue-100', 'desa adat': 'bg-amber-100',
        'default': 'bg-stone-100'
    };
    return colors[category?.toLowerCase()] || colors.default;
}

function getAturanIconColor(category) {
    const colors = {
        'religi': 'text-yellow-600', 'religius': 'text-yellow-600', 'pantai': 'text-cyan-600',
        'gunung': 'text-green-600', 'konservasi': 'text-emerald-600', 'cagar budaya': 'text-purple-600',
        'taman nasional': 'text-teal-600', 'air terjun': 'text-blue-600', 'desa adat': 'text-amber-600',
        'default': 'text-stone-500'
    };
    return colors[category?.toLowerCase()] || colors.default;
}

// ── Render Rekomendasi Kendaraan ──────────────────────────────
// Format item: { route/segment, vehicle_type, reason, road_condition,
//                public_transport: { available, options: [{name, price_min, price_max, note}] } }
let currentVehicleRecommendations = [];

function setVehicleRecommendations(items) {
    currentVehicleRecommendations = Array.isArray(items) ? items : [];
    renderKendaraanList(currentVehicleRecommendations);
}

function renderKendaraanList(items) {
    const displayedItems = Array.isArray(items) ? items : [];
    const availableCount = currentVehicleRecommendations.filter(item => !['dipakai','digunakan','in use','in-use','used'].includes((item.status || '').toLowerCase())).length;
    const inUseCount = currentVehicleRecommendations.length - availableCount;

    const vehicleTotalEl = document.getElementById('vehicle-total');
    const vehicleAvailableEl = document.getElementById('vehicle-available');
    const vehicleInUseEl = document.getElementById('vehicle-in-use');
    if (vehicleTotalEl) vehicleTotalEl.textContent = currentVehicleRecommendations.length;
    if (vehicleAvailableEl) vehicleAvailableEl.textContent = availableCount;
    if (vehicleInUseEl) vehicleInUseEl.textContent = inUseCount;

    const listContainer = document.getElementById('kendaraan-content');
    if (!listContainer) {
        return;
    }

    if (!currentVehicleRecommendations.length) {
        listContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 px-4">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mb-3">
                    <span class="material-icons-round text-stone-300 text-3xl">directions_car</span>
                </div>
                <p class="text-stone-400 text-sm text-center">Rekomendasi kendaraan akan tersedia setelah itinerary dibuat.</p>
            </div>`;
        return;
    }

    const intro = `
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl px-4 py-3 mb-3 flex items-start gap-2.5 shadow-sm">
            <span class="material-icons-round text-emerald-600 text-base mt-0.5 flex-shrink-0">info</span>
            <p class="text-xs text-emerald-900 leading-relaxed">
                <strong>Tips:</strong> Pilih kendaraan berdasarkan kondisi jalan dan durasi perjalanan. Motor lebih fleksibel untuk jalan sempit, mobil lebih nyaman untuk perjalanan jauh.
            </p>
        </div>`;

    listContainer.innerHTML = intro + displayedItems.map((item, idx) => {
        const vehicleBadge = getVehicleBadge(item.vehicle_type || item.type || item.name);
        const pt = item.public_transport || {};
        const hasPT = pt.available !== false && Array.isArray(pt.options) && pt.options.length > 0;
        const roadBadge = getRoadBadge(item.road_condition || item.condition || '');

        return `
        <div class="bg-white rounded-3xl border border-stone-100 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-stone-50 border-b border-stone-100">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="w-10 h-10 ${vehicleBadge.bgColor} rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="material-icons-round ${vehicleBadge.textColor} text-base">${vehicleBadge.icon}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-stone-800 truncate">${escapeHtml(item.name || item.vehicle_type || item.type || item.segment || item.route || 'Kendaraan')}</p>
                        <p class="text-xs text-stone-400">${escapeHtml(vehicleBadge.label)}</p>
                    </div>
                </div>
                ${item.road_condition || item.condition ? `<span class="text-xs font-medium px-2.5 py-1 ${roadBadge.cls} rounded-full whitespace-nowrap flex-shrink-0">${roadBadge.label}</span>` : ''}
            </div>
            <div class="px-4 py-3 space-y-3">
                ${item.reason ? `
                <div class="flex items-start gap-2">
                    <span class="material-icons-round text-stone-400 text-sm mt-0.5 flex-shrink-0">lightbulb</span>
                    <p class="text-sm text-stone-600 leading-relaxed">${escapeHtml(item.reason)}</p>
                </div>` : ''}

                ${hasPT ? `
                <div class="bg-gradient-to-br from-stone-50 to-stone-25 rounded-xl px-3.5 py-2.5 border border-stone-100">
                    <p class="text-xs font-bold text-stone-600 uppercase tracking-wider mb-2.5 flex items-center gap-1">
                        <span class="material-icons-round text-sm text-stone-400">directions_bus</span>
                        Opsi Transportasi Umum / Sewa
                    </p>
                    <div class="space-y-2">
                        ${pt.options.map(o => `
                            <div class="flex items-start justify-between gap-3 bg-white rounded-2xl px-3 py-2 border border-stone-50">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-stone-700 font-medium flex items-center gap-1.5">
                                        <span class="material-icons-round text-stone-400 text-sm">${o.icon || 'directions_bus'}</span>
                                        ${escapeHtml(o.name || 'Transportasi Umum')}
                                    </p>
                                    ${o.duration ? `<p class="text-xs text-stone-400 mt-0.5">⏱ ${escapeHtml(o.duration)}</p>` : ''}
                                    ${o.note ? `<p class="text-xs text-stone-400 mt-0.5">💡 ${escapeHtml(o.note)}</p>` : ''}
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm font-semibold text-stone-800">${formatPriceRange(o.price_min, o.price_max)}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>` : `
                <div class="bg-amber-50 border border-amber-100 rounded-xl px-3.5 py-2.5">
                    <p class="text-xs text-amber-800 flex items-center gap-1.5">
                        <span class="material-icons-round text-amber-600 text-sm">warning</span>
                        Kendaraan umum terbatas, sebaiknya gunakan kendaraan pribadi atau sewa lokal.
                    </p>
                </div>`}
            </div>
        </div>`;
    }).join('');
}

function filterKendaraan(query) {
    if (!currentVehicleRecommendations.length) return;
    const keyword = query?.trim().toLowerCase();
    const filtered = keyword ? currentVehicleRecommendations.filter(item => {
        const text = `${item.name || ''} ${item.vehicle_type || ''} ${item.type || ''} ${item.segment || ''} ${item.route || ''}`.toLowerCase();
        return text.includes(keyword);
    }) : currentVehicleRecommendations;
    renderKendaraanList(filtered);
}


function getVehicleBadge(type) {
    const t = (type || '').toLowerCase();
    if (t.includes('motor')) return { 
        icon: 'two_wheeler', 
        label: 'Motor / Sepeda',  
        bgColor: 'bg-amber-100', 
        textColor: 'text-amber-600' 
    };
    if (t.includes('mobil') || t.includes('pribadi')) return { 
        icon: 'directions_car',  
        label: 'Mobil Pribadi / Sewa', 
        bgColor: 'bg-blue-100',   
        textColor: 'text-blue-600' 
    };
    if (t.includes('umum') || t.includes('bus') || t.includes('angkot')) return { 
        icon: 'directions_bus',   
        label: 'Kendaraan Umum',   
        bgColor: 'bg-emerald-100',
        textColor: 'text-emerald-600' 
    };
    if (t.includes('kapal') || t.includes('perahu') || t.includes('boat')) return { 
        icon: 'directions_boat',  
        label: 'Kapal / Perahu',   
        bgColor: 'bg-cyan-100',   
        textColor: 'text-cyan-600' 
    };
    if (t.includes('jalan') || t.includes('kaki')) return { 
        icon: 'directions_walk',  
        label: 'Jalan Kaki',       
        bgColor: 'bg-stone-100',  
        textColor: 'text-stone-500' 
    };
    return { 
        icon: 'directions_car', 
        label: type || 'Kendaraan', 
        bgColor: 'bg-stone-100', 
        textColor: 'text-stone-500' 
    };
}

function getRoadBadge(condition) {
    const c = (condition || '').toLowerCase();
    if (c.includes('rusak') || c.includes('terbatas') || c.includes('sulit') || c.includes('sempit')) {
        return { label: '⚠ Akses Terbatas', cls: 'bg-rose-50 text-rose-700 border border-rose-100' };
    }
    if (c.includes('sedang') || c.includes('cukup')) {
        return { label: '◐ Akses Sedang', cls: 'bg-amber-50 text-amber-700 border border-amber-100' };
    }
    return { label: '✓ Akses Mudah', cls: 'bg-emerald-50 text-emerald-700 border border-emerald-100' };
}

function formatPriceRange(min, max) {
    if (min == null && max == null) return '—';
    const fmt = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');
    if (min != null && max != null && min !== max) return `${fmt(min)} – ${fmt(max)}`;
    return fmt(min ?? max);
}

function renderMap(data) {
    const existingDisclaimer = document.getElementById('price-disclaimer');
    if (existingDisclaimer) existingDisclaimer.remove();
    
    const disclaimer = document.createElement('div');
    disclaimer.id = 'price-disclaimer';
    disclaimer.className = 'px-1 pt-1 pb-3';
    disclaimer.innerHTML = `
        <p class="text-xs text-stone-400 text-center flex items-center justify-center gap-1">
            <span class="material-icons-round text-xs">info</span>
            Estimasi harga berdasarkan data umum, dapat berbeda di lapangan.
        </p>
    `;
    document.getElementById('tab-itinerary').appendChild(disclaimer);
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
        const meta = window.placeMetadata || {};
        places.forEach((place, idx) => {
            let coords = null;
            
            // 1. Coba ambil koordinat presisi dari metadata dinamis (Database/Wikipedia)
            if (meta[place] && meta[place].lat && meta[place].lng) {
                coords = [parseFloat(meta[place].lat), parseFloat(meta[place].lng)];
            }
            
            // 2. Fallback ke cityCoords manual
            if (!coords) {
                for (const [city, coord] of Object.entries(cityCoords)) {
                    if (place.toLowerCase().includes(city.toLowerCase())) {
                        coords = coord;
                        break;
                    }
                }
            }

            if (coords) {
                const marker = L.marker(coords, {
                    opacity: 0.9
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

    // Render route summary list and timeline
    const routeTimeline = document.getElementById('route-timeline');
    if (routeTimeline) {
        routeTimeline.innerHTML = (data.schedule || []).map((day, dayIndex) => {
            const activities = (day.activities || []).map(act => `<p class="text-sm text-stone-600 leading-relaxed">${escapeHtml(act.time || '')} · <span class="font-medium text-stone-800">${escapeHtml(act.location || act.place || act.title || '')}</span></p>`).join('');
            return `
                <div class="rounded-2xl bg-stone-50 border border-stone-100 p-4">
                    <p class="text-sm font-semibold text-stone-700 mb-2">Hari ${dayIndex + 1}</p>
                    ${activities || '<p class="text-sm text-stone-500">Tidak ada detail kegiatan.</p>'}
                </div>`;
        }).join('');
    }

    routeSummary.innerHTML = `
        <div class="bg-white/90 rounded-2xl border border-stone-100 p-4">
            <p class="text-sm font-semibold text-stone-700 mb-3">Rute Perjalanan</p>
            <ol class="list-decimal list-inside space-y-2 text-sm text-stone-600">
                ${places.map((place, idx) => `<li><span class="font-medium">${escapeHtml(place)}</span></li>`).join('')}
            </ol>
        </div>
    `;
}


function switchTab(name) {
    ['itinerary', 'rute', 'kendaraan'].forEach(tab => {
        const el = document.getElementById(`tab-${tab}`);
        const btn = document.querySelector(`[data-tab="${tab}"]`);
        
        if (el) {
            if (tab === name) {
                el.classList.remove('hidden');
                el.classList.add('flex', 'flex-col');
                if (btn) {
                    btn.className = "flex-1 py-3.5 text-sm font-bold text-primary border-b-[3px] border-primary transition-all text-center";
                }
            } else {
                el.classList.add('hidden');
                el.classList.remove('flex', 'flex-col');
                if (btn) {
                    btn.className = "flex-1 py-3.5 text-sm font-medium text-stone-400 border-b-[3px] border-transparent hover:text-stone-600 transition-all text-center";
                }
            }
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
        <div class="bg-[#003355] text-white rounded-xl px-4 py-3 max-w-[260px] shadow-sm">
            <p class="text-[13px] font-medium leading-relaxed">${escapeHtml(text)}</p>
        </div>
        <div class="w-8 h-8 bg-[#003355] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
            <span class="material-icons-round text-white text-[16px]">person_outline</span>
        </div>`;
    chatMessages.appendChild(div);
    scrollToBottom();
}

function appendAIMessage(html) {
    const div = document.createElement('div');
    div.className = 'flex items-start gap-3';
    div.innerHTML = `
        <div class="w-8 h-8 bg-[#3c3726] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
            <span class="material-icons-round text-white text-[16px]">smart_display</span>
        </div>
        <div class="bg-[#3c3726] text-white shadow-sm rounded-xl px-4 py-3 max-w-[280px]">
            <p class="text-[13px] font-medium leading-relaxed">${html}</p>
        </div>`;
    chatMessages.appendChild(div);
    scrollToBottom();
}

function appendTypingIndicator() {
    const div = document.createElement('div');
    div.id = 'typing-indicator';
    div.className = 'flex items-start gap-3';
    div.innerHTML = `
        <div class="w-8 h-8 bg-[#3c3726] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm mt-1">
            <span class="material-icons-round text-white text-[16px]">smart_display</span>
        </div>
        <div class="bg-[#3c3726] shadow-sm rounded-xl px-4 py-3 flex items-center gap-2">
            <p class="text-[12px] text-stone-200 font-medium mr-1">Sedang Berpikir ....</p>
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
    if (typeof text !== 'string') return '';
    return text.replace(/&/g, '&amp;')
               .replace(/</g, '&lt;')
               .replace(/>/g, '&gt;')
               .replace(/"/g, '&quot;')
               .replace(/'/g, '&#039;');
}

function resetChat() {
    if (conversationHistory.length > 0 && !confirm('Mulai percakapan baru? Percakapan saat ini akan disimpan ke riwayat.')) return;
    startNewConversation();
}

function exportItinerary() {
    window.print();
}

// ── Sidebar / AI Assistant toggles ─────────────────────────────
// Menggunakan class 'force-hidden' (display:none !important) agar klik
// selalu menghasilkan hasil yang konsisten, terlepas dari kombinasi
// class responsive 'hidden'/'md:flex' yang dipakai untuk state awal.
// Karena kolom tengah (main) memakai flex-1, ia otomatis menyesuaikan
// lebar begitu salah satu sidebar disembunyikan/ditampilkan.
function toggleLeftSidebar() {
    document.getElementById('left-sidebar')?.classList.toggle('force-hidden');
}

function toggleRightSidebar() {
    document.getElementById('right-sidebar')?.classList.toggle('force-hidden');
}

function toggleMobileSidebar() {
    const el = document.getElementById('left-sidebar');
    if (!el) return;
    el.classList.toggle('hidden');
    el.classList.toggle('flex');
}

// ── Auto-load from URL query ──────────────────────────────────
if (kendaraanSearch) {
    kendaraanSearch.addEventListener('input', function() {
        filterKendaraan(this.value);
    });
}

// ── Inisialisasi ─────────────────────────────────────────────
// Render riwayat percakapan yang tersimpan, lalu mulai percakapan baru.
renderHistorySidebar();
startNewConversation();

const params = new URLSearchParams(window.location.search);
const q = params.get('q');
if (q) {
    // Jalankan langsung tanpa menunggu event DOMContentLoaded karena script ada di akhir body.
    // Prompt dari landing page otomatis dikirim ke AI Assistant dan muncul di kolom kanan.
    setTimeout(() => {
        handleChatSubmit(q);
        // Bersihkan URL agar tidak submit ulang saat refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }, 100);
}
</script>
</x-layouts.app>