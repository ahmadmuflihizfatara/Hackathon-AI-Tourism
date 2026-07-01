<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NusantaraAI — Smart Itinerary Planner</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-stone-800 antialiased">

    {{-- NAV --}}
    <nav class="border-b border-amber-100">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8 2 5 5.2 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.8-3-7-7-7z"/>
                        <circle cx="12" cy="9" r="2.3"/>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-stone-900">Nusantara<span class="text-amber-600">AI</span></span>
            </div>
            <span class="text-xs text-stone-400 hidden sm:block">Smart Itinerary Planner</span>
        </div>
    </nav>

    {{-- HERO / INPUT DESTINASI --}}
    <section class="max-w-3xl mx-auto px-6 pt-12 pb-8 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold text-stone-900 leading-tight">
            Rencana perjalanan cerdas,
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-orange-500">disusun otomatis</span>
        </h1>
        <p class="mt-3 text-stone-500 text-sm sm:text-base max-w-xl mx-auto">
            Masukkan destinasi tujuanmu — AI akan menyusun aturan lokal dan rekomendasi kendaraan yang paling sesuai untuk rutemu.
        </p>

        <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
            <input
                id="input-destinasi"
                type="text"
                value="Nusa Penida, Bali"
                placeholder="Contoh: Nusa Penida, Bali"
                class="flex-1 sm:w-80 rounded-lg border border-amber-200 px-4 py-2.5 text-sm text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
            />
            <button
                id="btn-generate"
                class="rounded-lg bg-gradient-to-r from-amber-600 to-orange-500 text-white text-sm font-medium px-5 py-2.5 hover:opacity-90 active:scale-[0.98] transition"
            >
                Buat Rencana
            </button>
        </div>
    </section>

    {{-- DASHBOARD CARD --}}
    <section class="max-w-3xl mx-auto px-6 pb-16">
        <div class="rounded-2xl border border-amber-100 shadow-sm shadow-amber-100/50 overflow-hidden bg-white">

            {{-- Header --}}
            <div class="bg-amber-50/60 px-6 py-4 border-b border-amber-100 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs text-amber-600 font-semibold uppercase tracking-wide">Rencana untuk</p>
                    <h2 id="output-destinasi" class="text-lg font-semibold text-stone-900 truncate">—</h2>
                </div>
                <div id="loading-indicator" class="hidden items-center gap-2 text-xs text-amber-600 flex-shrink-0">
                    <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-90" d="M21 12a9 9 0 00-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                    <span>AI menyusun rencana...</span>
                </div>
            </div>

            {{-- Tab nav --}}
            <div class="px-6 pt-4">
                <div class="inline-flex bg-amber-50 rounded-xl p-1 gap-1">
                    <button type="button" data-tab="aturan" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition">
                        Aturan &amp; Norma
                    </button>
                    <button type="button" data-tab="kendaraan" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition">
                        Kendaraan
                    </button>
                </div>
            </div>

            {{-- PANEL: ATURAN --}}
            <div id="panel-aturan" class="tab-panel px-6 py-6 space-y-6">

                {{-- Regulasi tertulis --}}
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 4h9l3 3v13a1 1 0 01-1 1H6a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                                <path d="M9 10h6M9 13h6M9 16h4"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-stone-900 text-sm">Regulasi Tertulis</h3>
                        <span class="ml-auto text-[11px] font-medium px-2 py-0.5 rounded-full bg-red-50 text-red-600">Wajib</span>
                    </div>
                    <ul id="list-regulasi" class="space-y-2 text-sm text-stone-600 pl-1"></ul>
                </div>

                {{-- Keselamatan --}}
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l7 3v5c0 5-3.2 8.2-7 9.5C8.2 19.2 5 16 5 11V6l7-3z"/>
                                <path d="M9.2 12l1.9 1.9L15 10"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-stone-900 text-sm">Keselamatan</h3>
                        <span class="ml-auto text-[11px] font-medium px-2 py-0.5 rounded-full bg-orange-50 text-orange-600">Waspada</span>
                    </div>
                    <ul id="list-keselamatan" class="space-y-2 text-sm text-stone-600 pl-1"></ul>
                </div>

                {{-- Norma adat --}}
                <div>
                    <div class="flex items-center gap-2 mb-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20s-7-4.35-9.3-8.5C1.3 8.3 2.5 5 5.7 4.2c2.1-.5 4 .6 6.3 2.8 2.3-2.2 4.2-3.3 6.3-2.8 3.2.8 4.4 4.1 3 7.3C19 15.65 12 20 12 20z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-stone-900 text-sm">Norma Adat &amp; Tidak Tertulis</h3>
                        <span class="ml-auto text-[11px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">Anjuran</span>
                    </div>
                    <ul id="list-adat" class="space-y-2 text-sm text-stone-600 pl-1"></ul>
                </div>

                {{-- Catatan AI --}}
                <div class="flex gap-3 bg-orange-50/70 border border-orange-100 rounded-xl p-4">
                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 00-6 6c0 2.4 1.4 3.9 2.4 4.9.4.4.6.9.6 1.4V16h6v-.7c0-.5.2-1 .6-1.4C16.6 12.9 18 11.4 18 9a6 6 0 00-6-6z"/>
                        <path d="M9.5 19h5M10.5 21.5h3"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide mb-0.5">Catatan AI</p>
                        <p id="ai-note-text" class="text-sm text-stone-600"></p>
                    </div>
                </div>
            </div>

            {{-- PANEL: KENDARAAN --}}
            <div id="panel-kendaraan" class="tab-panel hidden px-6 py-6">
                <div id="list-kendaraan" class="space-y-4"></div>
            </div>

        </div>
    </section>

    <footer class="text-center text-xs text-stone-400 pb-8">
        Dibuat untuk hackathon — NusantaraAI
    </footer>

    <script>
    let itinerary = {};

    const iconMotor = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="5" cy="17" r="2.6"/><circle cx="18" cy="17" r="2.6"/>
        <path d="M7.5 17h6l3-6h-3.5L11.5 8H7.5l-1.2 3.5"/>
        <path d="M13.5 11h3.3l1.2 3"/>
    </svg>`;
    const iconMobil = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 16l1.4-4.8A2 2 0 017.3 9.7h9.4a2 2 0 011.9 1.5L20 16"/>
        <path d="M4 16h16v1.6a1 1 0 01-1 1h-1a1 1 0 01-1-1V17H7v.6a1 1 0 01-1 1H5a1 1 0 01-1-1V16z"/>
        <circle cx="7.5" cy="16.6" r="1.4"/><circle cx="16.5" cy="16.6" r="1.4"/>
    </svg>`;
    const iconVan = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="7" width="14" height="9.5" rx="1.4"/>
        <path d="M17 10.5h2.6L21 13v3.5h-4"/>
        <path d="M3 11h14"/>
        <circle cx="7" cy="17.8" r="1.4"/><circle cx="17" cy="17.8" r="1.4"/>
    </svg>`;
    const iconDefault = `<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2C8 2 5 5.2 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.8-3-7-7-7z"/><circle cx="12" cy="9" r="2.3"/>
    </svg>`;

    function pickIcon(nama) {
        const n = nama.toLowerCase();
        if (n.includes('motor')) return iconMotor;
        if (n.includes('van') || n.includes('bus')) return iconVan;
        if (n.includes('mobil') || n.includes('jeep') || n.includes('car')) return iconMobil;
        return iconDefault;
    }

    function skorDots(skor) {
        let dots = '';
        for (let i = 1; i <= 5; i++) {
            dots += `<span class="w-2 h-2 rounded-full ${i <= skor ? 'bg-amber-500' : 'bg-amber-100'}"></span>`;
        }
        return `<div class="flex items-center gap-1">${dots}</div>`;
    }

    function renderAturan(data) {
        const bullet = (text) => `
            <li class="flex gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 flex-shrink-0"></span>
                <span>${text}</span>
            </li>`;

        document.getElementById('list-regulasi').innerHTML = data.aturan.regulasi.map(bullet).join('');
        document.getElementById('list-keselamatan').innerHTML = data.aturan.keselamatan.map(bullet).join('');
        document.getElementById('list-adat').innerHTML = data.aturan.adat.map(bullet).join('');
        document.getElementById('ai-note-text').textContent = data.aturan.catatan_ai || '';
    }

    function renderKendaraan(data) {
        const cards = data.kendaraan.rekomendasi.map(k => `
            <div class="flex gap-4 border border-amber-100 rounded-xl p-4 hover:border-amber-200 transition">
                <div class="w-11 h-11 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                    ${pickIcon(k.nama)}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="font-semibold text-stone-900 text-sm">${k.nama}</h4>
                        ${skorDots(k.skor)}
                    </div>
                    <p class="text-sm text-stone-600 mt-1">${k.alasan}</p>
                    <div class="flex flex-wrap gap-1.5 mt-2.5">
                        ${k.cocok_untuk.map(tag => `<span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">${tag}</span>`).join('')}
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('list-kendaraan').innerHTML = cards;
    }

    function renderAll(data) {
        document.getElementById('output-destinasi').textContent = data.destinasi;
        renderAturan(data);
        renderKendaraan(data);
    }

    const tabBtns = document.querySelectorAll('.tab-btn');
    const panels = { aturan: document.getElementById('panel-aturan'), kendaraan: document.getElementById('panel-kendaraan') };

    function setActiveTab(tab) {
        tabBtns.forEach(btn => {
            const isActive = btn.dataset.tab === tab;
            btn.classList.toggle('bg-white', isActive);
            btn.classList.toggle('text-amber-700', isActive);
            btn.classList.toggle('shadow-sm', isActive);
            btn.classList.toggle('text-stone-500', !isActive);
        });
        Object.entries(panels).forEach(([key, el]) => el.classList.toggle('hidden', key !== tab));
    }

    tabBtns.forEach(btn => btn.addEventListener('click', () => setActiveTab(btn.dataset.tab)));

    async function generateItinerary(destinasi) {
        const loading = document.getElementById('loading-indicator');
        const btn = document.getElementById('btn-generate');
        loading.classList.remove('hidden');
        loading.classList.add('flex');
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');

        try {
            const res = await fetch('/api/itinerary/generate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ destinasi })
            });
            const data = await res.json();
            renderAll(data);
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal membuat rencana. Cek konsol untuk detail error.');
        } finally {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-not-allowed');
        }
    }

    document.getElementById('btn-generate').addEventListener('click', () => {
        const destinasi = document.getElementById('input-destinasi').value.trim();
        if (destinasi) generateItinerary(destinasi);
    });

    setActiveTab('aturan');
    </script>
</body>
</html>
