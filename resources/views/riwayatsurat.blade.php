@extends('layouts.app')

@section('content')

<style>
    /* ==========================================================================
       Riwayat Surat — Registry Ledger Theme (matches Tambah Surat)
       Same token set / typography / card language as tambahsurat.blade.php.
       ========================================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    :root {
        /* Color tokens — identical to Tambah Surat */
        --ink:        #1c2b23;
        --ink-soft:   #3d4f45;
        --ledger:     #eef3ea;
        --ledger-line:#cdd9c8;
        --paper:      #fbfcf9;
        --brass:      #a9812f;
        --brass-dark: #8a6a24;
        --brass-tint: #f4ecd8;
        --line:       #dfe6da;
        --danger:     #b3432f;
        --danger-bg:  #fdf1ee;
        --success:    #3f6b4a;
        --success-bg: #eef5ef;

        --font-display: 'Fraunces', Georgia, serif;
        --font-body: 'Inter', -apple-system, sans-serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, monospace;
    }

    /* ==========================================================================
       Page base — same ledger paper canvas as Tambah Surat
       ========================================================================== */

    .ledger-page {
        background: var(--ledger);
        font-family: var(--font-body);
        color: var(--ink);
        min-height: 100vh;
    }

    /* Breadcrumb */
    .ledger-breadcrumb {
        background: transparent;
        padding: 0;
        color: var(--ink-soft);
        font-family: var(--font-mono);
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .ledger-breadcrumb .breadcrumb-item a {
        color: var(--ink-soft);
        text-decoration: none;
    }
    .ledger-breadcrumb .breadcrumb-item.active {
        color: var(--brass-dark);
        font-weight: 600;
    }

    /* Alert */
    .ledger-alert-success {
        background: var(--success-bg);
        border: 1px solid #cfe2d4;
        color: var(--success);
        border-radius: 0.75rem;
        font-size: 0.9rem;
    }

    /* ==========================================================================
       Cards — same surface, radius, shadow as Tambah Surat
       ========================================================================== */

    .ledger-card {
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 0.9rem;
        box-shadow: 0 1px 2px rgba(28,43,35,0.05), 0 1px 10px rgba(28,43,35,0.04);
    }

    .ledger-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.55rem;
        color: var(--ink);
        letter-spacing: -0.01em;
    }

    .ledger-subtitle {
        color: var(--ink-soft);
        font-size: 0.85rem;
    }

    /* Counter badge — brass stamp pill, echoes the registrar's stamp on Tambah Surat */
    .ledger-badge {
        background: var(--brass-tint);
        color: var(--brass-dark);
        font-family: var(--font-mono);
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-block;
        border: 1px solid rgba(169,129,47,0.25);
    }

    /* ==========================================================================
       Toolbar — same input styling language as Tambah Surat's form fields
       ========================================================================== */

    #searchInput,
    #filterKlasifikasi,
    #sortOrder {
        font-family: var(--font-body);
        color: var(--ink);
        background-color: var(--paper);
        border: 1px solid var(--line);
        border-radius: 0.55rem;
        padding: 0.65rem 0.95rem;
        font-size: 0.92rem;
    }

    .ledger-input-icon {
        background-color: var(--ledger);
        border: 1px solid var(--line);
        border-right: none;
        color: var(--ink-soft);
        border-radius: 0.55rem 0 0 0.55rem;
    }

    .input-group #searchInput {
        border-radius: 0 0.55rem 0.55rem 0;
    }

    #searchInput:focus,
    #filterKlasifikasi:focus,
    #sortOrder:focus {
        outline: none;
        border-color: var(--brass);
        box-shadow: 0 0 0 3px rgba(169,129,47,0.16);
    }

    #searchInput:focus-visible,
    #filterKlasifikasi:focus-visible,
    #sortOrder:focus-visible {
        outline: 2px solid var(--brass-dark);
        outline-offset: 2px;
    }

    #filterKlasifikasi,
    #sortOrder {
        cursor: pointer;
    }

    /* ==========================================================================
       Archive list — index-card rows, ledger-toned
       ========================================================================== */

    #archiveCard {
        overflow: hidden;
    }

    .ledger-row {
        display: flex;
        align-items: stretch;
        border-top: 1px solid var(--ledger-line);
        transition: background 0.15s ease;
    }

    .ledger-row:first-child {
        border-top: none;
    }

    .ledger-row:hover {
        background: var(--brass-tint);
    }

    .ledger-tab-strip {
        width: 6px;
        flex-shrink: 0;
        background: var(--brass);
    }

    .ledger-row-body {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.75rem;
        flex-wrap: wrap;
    }

    .ledger-row-main {
        min-width: 0;
    }

    .ledger-row-nomor {
        margin: 0 0 0.3rem 0;
        font-family: var(--font-mono);
        font-weight: 600;
        font-size: 0.86rem;
        letter-spacing: 0.02em;
        color: var(--brass-dark);
        word-break: break-all;
    }

    .ledger-row-perihal {
        margin: 0 0 0.4rem 0;
        font-size: 0.94rem;
        color: var(--ink);
    }

    .ledger-row-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .ledger-row-meta {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        flex-shrink: 0;
    }

    /* Chips — one per related entity: klasifikasi, penandatangan, tujuan */
    .ledger-chip {
        font-family: var(--font-mono);
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        white-space: nowrap;
    }

    .ledger-chip-klasifikasi {
        background: var(--brass-tint);
        color: var(--brass-dark);
    }

    .ledger-chip-signatory {
        background: var(--success-bg);
        color: var(--success);
    }

    .ledger-chip-tujuan {
        background: var(--ledger);
        color: var(--ink-soft);
        border: 1px solid var(--ledger-line);
    }

    .ledger-row-date {
        font-family: var(--font-mono);
        font-size: 0.78rem;
        color: var(--ink-soft);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    /* ==========================================================================
       Empty states — same brass/dashed accent as Tambah Surat's stamp box
       ========================================================================== */

    .ledger-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 3.5rem 2rem;
        color: var(--ink-soft);
    }

    .ledger-empty i {
        font-size: 2rem;
        color: var(--ledger-line);
        margin-bottom: 1rem;
    }

    .ledger-empty p {
        margin: 0 0 1rem 0;
        font-size: 0.92rem;
    }

    .ledger-cta {
        font-family: var(--font-mono);
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--brass-dark);
        text-decoration: none;
        border-bottom: 1px dashed var(--brass-dark);
        padding-bottom: 2px;
    }

    .ledger-cta:hover {
        color: var(--brass);
        border-color: var(--brass);
    }

    /* ==========================================================================
       Responsive
       ========================================================================== */

    @media (max-width: 640px) {
        .ledger-row-body {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .ledger-row-meta {
            width: 100%;
            justify-content: flex-start;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }
</style>

<div class="ledger-page" id="riwayatPage">
    <div class="container-fluid py-4 py-md-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb ledger-breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ Route::has('surat.index') ? route('surat.index') : '#' }}">Letter Management</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Surat</li>
            </ol>
        </nav>

        {{-- Alert sukses (jika ada aksi hapus dll) --}}
        @if (session('success'))
            <div class="alert ledger-alert-success d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- Header + Toolbar --}}
        <div class="card ledger-card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h2 class="ledger-title mb-1">Riwayat Surat</h2>
                        <p class="ledger-subtitle mb-0">Arsip seluruh nomor surat yang pernah diterbitkan.</p>
                    </div>
                    <div id="totalCounter">
                        <span class="ledger-badge">
                            <span id="totalCount">{{ count($suratList ?? []) }}</span> surat tercatat
                        </span>
                    </div>
                </div>

                {{-- Toolbar: search + filter klasifikasi (sesuai tabel klasifikasi_surat) + sort --}}
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text ledger-input-icon">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input
                                type="text"
                                id="searchInput"
                                placeholder="Cari nomor surat atau perihal..."
                                class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select id="filterKlasifikasi" class="form-select">
                            <option value="">Semua Klasifikasi</option>
                            @foreach ($klasifikasiList ?? [] as $klasifikasi)
                                <option value="{{ $klasifikasi->kode }}">{{ $klasifikasi->kode }} — {{ $klasifikasi->jenis_surat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select id="sortOrder" class="form-select">
                            <option value="desc">Terbaru dulu</option>
                            <option value="asc">Terlama dulu</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Archive list --}}
        <div class="card ledger-card" id="archiveCard">
            @if (count($suratList ?? []) > 0)
                <div id="archiveList">
                    @foreach ($suratList as $surat)
                        <div class="ledger-row"
                             data-perihal="{{ strtolower($surat->perihal) }}"
                             data-nomor="{{ strtolower($surat->nomor_surat) }}"
                             data-klasifikasi="{{ $surat->klasifikasiSurat->kode ?? '' }}"
                             data-tanggal="{{ $surat->tanggal }}">
                            <div class="ledger-tab-strip"></div>
                            <div class="ledger-row-body">
                                <div class="ledger-row-main">
                                    <p class="ledger-row-nomor">{{ $surat->nomor_surat }}</p>
                                    <p class="ledger-row-perihal">{{ $surat->perihal }}</p>
                                    <div class="ledger-row-tags">
                                        @if (!empty($surat->klasifikasiSurat))
                                            <span class="ledger-chip ledger-chip-klasifikasi">{{ $surat->klasifikasiSurat->kode }}</span>
                                        @endif
                                        @if (!empty($surat->penandatangan))
                                            <span class="ledger-chip ledger-chip-signatory">{{ $surat->penandatangan->kode }}</span>
                                        @endif
                                        @if (!empty($surat->tujuanSurat))
                                            <span class="ledger-chip ledger-chip-tujuan">{{ $surat->tujuanSurat->kode }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ledger-row-meta">
                                    <span class="ledger-row-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $surat->tanggal }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="emptySearchState" class="d-none">
                    <div class="ledger-empty">
                        <i class="fa-solid fa-folder-open"></i>
                        <p>Tidak ada surat yang cocok dengan pencarian.</p>
                    </div>
                </div>
            @else
                <div class="ledger-empty">
                    <i class="fa-solid fa-box-archive"></i>
                    <p>Belum ada surat yang tercatat.</p>
                    <a href="{{ route('tambahsurat') }}" class="ledger-cta">Buat surat pertama</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const filterKlasifikasi = document.getElementById('filterKlasifikasi');
    const sortOrder = document.getElementById('sortOrder');
    const archiveList = document.getElementById('archiveList');
    const emptySearchState = document.getElementById('emptySearchState');
    const totalCount = document.getElementById('totalCount');

    function applyFilters() {
        if (!archiveList) return;

        const query = searchInput.value.trim().toLowerCase();
        const klasifikasi = filterKlasifikasi.value;
        const rows = Array.from(archiveList.querySelectorAll('.ledger-row'));

        let visibleCount = 0;

        rows.forEach(row => {
            const matchQuery = !query ||
                row.dataset.nomor.includes(query) ||
                row.dataset.perihal.includes(query);
            const matchKlasifikasi = !klasifikasi || row.dataset.klasifikasi === klasifikasi;
            const visible = matchQuery && matchKlasifikasi;

            row.classList.toggle('d-none', !visible);
            if (visible) visibleCount++;
        });

        totalCount.textContent = visibleCount;

        if (emptySearchState) {
            emptySearchState.classList.toggle('d-none', visibleCount !== 0);
        }
    }

    function applySort() {
        if (!archiveList) return;

        const rows = Array.from(archiveList.querySelectorAll('.ledger-row'));
        const direction = sortOrder.value;

        rows.sort((a, b) => {
            const dateA = new Date(a.dataset.tanggal);
            const dateB = new Date(b.dataset.tanggal);
            return direction === 'asc' ? dateA - dateB : dateB - dateA;
        });

        rows.forEach(row => archiveList.appendChild(row));
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
        filterKlasifikasi.addEventListener('change', applyFilters);
        sortOrder.addEventListener('change', () => {
            applySort();
            applyFilters();
        });
    }
</script>

@endsection
