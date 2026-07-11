@extends('layouts.app')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

:root {
  /* Color tokens — cool slate, distinct from tambahsurat's warm ledger green */
  --slate-ink:      #1e2a35;
  --slate-ink-soft: #526271;
  --slate-paper:    #f5f7f9;
  --slate-panel:    #eef1f5;
  --slate-line:     #dde3e9;
  --slate-accent:   #3a5a78;   /* steel blue — this page's primary accent */
  --slate-accent-dark: #2a4258;
  --slate-accent-tint: #e6edf3;
  --slate-danger:   #b3432f;

  /* Department tab colors — each dept gets a distinct filing-label hue */
  --dept-hrd: #a9812f;
  --dept-fin: #3f6b4a;
  --dept-ops: #3a5a78;
  --dept-it:  #6a4c93;
  --dept-mkt: #b3432f;

  --font-display: 'Fraunces', Georgia, serif;
  --font-body: 'Inter', -apple-system, sans-serif;
  --font-mono: 'IBM Plex Mono', ui-monospace, monospace;
}

/* ==========================================================================
   Page base
   ========================================================================== */

.archive-page {
  background: var(--slate-paper);
  font-family: var(--font-body);
  color: var(--slate-ink);
  min-height: 100vh;
}

/* Breadcrumb */
.archive-breadcrumb {
  background: transparent;
  padding: 0;
  color: var(--slate-ink-soft);
  font-family: var(--font-mono);
  font-size: 0.72rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}
.archive-breadcrumb .breadcrumb-item a {
  color: var(--slate-ink-soft);
  text-decoration: none;
}
.archive-breadcrumb .breadcrumb-item.active {
  color: var(--slate-accent-dark);
  font-weight: 600;
}

/* Alert */
.archive-alert-success {
  background: #eef5ef;
  border: 1px solid #cfe2d4;
  color: #3f6b4a;
  border-radius: 0.75rem;
  font-size: 0.9rem;
}

/* ==========================================================================
   Header card + toolbar
   ========================================================================== */

.archive-card {
  background: #ffffff;
  border: 1px solid var(--slate-line);
  border-radius: 0.9rem;
  box-shadow: 0 1px 2px rgba(30,42,53,0.05), 0 1px 10px rgba(30,42,53,0.04);
}

.archive-title {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 1.55rem;
  color: var(--slate-ink);
  letter-spacing: -0.01em;
}

.archive-subtitle {
  color: var(--slate-ink-soft);
  font-size: 0.85rem;
}

.counter-badge {
  background: var(--slate-accent-tint);
  color: var(--slate-accent-dark);
  font-family: var(--font-mono);
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  padding: 0.4rem 0.85rem;
  border-radius: 999px;
  white-space: nowrap;
  display: inline-block;
}

#searchInput,
#filterDept,
#sortOrder {
  font-family: var(--font-body);
  color: var(--slate-ink);
  background-color: var(--slate-paper);
  border-color: var(--slate-line);
  border-radius: 0.55rem;
}

.input-group-text {
  background-color: var(--slate-paper);
  border-color: var(--slate-line);
  color: var(--slate-ink-soft);
  border-radius: 0.55rem 0 0 0.55rem;
}

.input-group #searchInput {
  border-radius: 0 0.55rem 0.55rem 0;
}

#searchInput:focus,
#filterDept:focus,
#sortOrder:focus {
  border-color: var(--slate-accent);
  box-shadow: 0 0 0 3px rgba(58,90,120,0.16);
}

#filterDept,
#sortOrder {
  cursor: pointer;
}

/* ==========================================================================
   Archive list — index-card rows with a folder-tab strip
   ========================================================================== */

#archiveCard {
  overflow: hidden;
}

.archive-row {
  display: flex;
  align-items: stretch;
  border-top: 1px solid var(--slate-line);
  transition: background 0.15s ease;
}

.archive-row:first-child {
  border-top: none;
}

.archive-row:hover {
  background: var(--slate-panel);
}

.archive-tab {
  width: 6px;
  flex-shrink: 0;
  background: var(--slate-ink-soft);
}

.archive-tab[data-dept="HRD"] { background: var(--dept-hrd); }
.archive-tab[data-dept="FIN"] { background: var(--dept-fin); }
.archive-tab[data-dept="OPS"] { background: var(--dept-ops); }
.archive-tab[data-dept="IT"]  { background: var(--dept-it); }
.archive-tab[data-dept="MKT"] { background: var(--dept-mkt); }

.archive-body {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.1rem 1.75rem;
  flex-wrap: wrap;
}

.archive-main {
  min-width: 0;
}

.archive-nomor {
  margin: 0 0 0.2rem 0;
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 0.86rem;
  letter-spacing: 0.02em;
  color: var(--slate-accent-dark);
}

.archive-perihal {
  margin: 0;
  font-size: 0.94rem;
  color: var(--slate-ink);
}

.archive-meta {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  flex-shrink: 0;
}

.dept-chip {
  font-family: var(--font-mono);
  font-size: 0.68rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  padding: 0.28rem 0.6rem;
  border-radius: 999px;
  background: var(--slate-accent-tint);
  color: var(--slate-accent-dark);
}

.dept-chip[data-dept="HRD"] { background: color-mix(in srgb, var(--dept-hrd) 16%, white); color: var(--dept-hrd); }
.dept-chip[data-dept="FIN"] { background: color-mix(in srgb, var(--dept-fin) 16%, white); color: var(--dept-fin); }
.dept-chip[data-dept="OPS"] { background: color-mix(in srgb, var(--dept-ops) 16%, white); color: var(--dept-ops); }
.dept-chip[data-dept="IT"]  { background: color-mix(in srgb, var(--dept-it) 16%, white); color: var(--dept-it); }
.dept-chip[data-dept="MKT"] { background: color-mix(in srgb, var(--dept-mkt) 16%, white); color: var(--dept-mkt); }

.archive-date {
  font-family: var(--font-mono);
  font-size: 0.78rem;
  color: var(--slate-ink-soft);
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

/* ==========================================================================
   Empty states
   ========================================================================== */

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 3.5rem 2rem;
  color: var(--slate-ink-soft);
}

.empty-state i {
  font-size: 2rem;
  color: var(--slate-line);
  margin-bottom: 1rem;
}

.empty-state p {
  margin: 0 0 1rem 0;
  font-size: 0.92rem;
}

.empty-cta {
  font-family: var(--font-mono);
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--slate-accent-dark);
  text-decoration: none;
  border-bottom: 1px dashed var(--slate-accent-dark);
  padding-bottom: 2px;
}

.empty-cta:hover {
  color: var(--slate-accent);
  border-color: var(--slate-accent);
}

/* ==========================================================================
   Responsive
   ========================================================================== */

@media (max-width: 640px) {
  .archive-body {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.6rem;
  }

  .archive-meta {
    width: 100%;
    justify-content: space-between;
  }
}

@media (prefers-reduced-motion: reduce) {
  * {
    transition: none !important;
  }
}
</style>

<div class="archive-page" id="riwayatPage">
    <div class="container-fluid py-4 py-md-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb archive-breadcrumb mb-4">
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
            <div class="alert archive-alert-success d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- Header + Toolbar --}}
        <div class="card archive-card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h2 class="archive-title mb-1">Riwayat Surat</h2>
                        <p class="archive-subtitle mb-0">Arsip seluruh nomor surat yang pernah diterbitkan.</p>
                    </div>
                    <div id="totalCounter">
                        <span class="counter-badge">
                            <span id="totalCount">{{ count($suratList ?? []) }}</span> surat tercatat
                        </span>
                    </div>
                </div>

                {{-- Toolbar: search + filter --}}
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
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
                        <select id="filterDept" class="form-select">
                            <option value="">Semua Departemen</option>
                            <option value="HRD">HRD</option>
                            <option value="FIN">Finance</option>
                            <option value="OPS">Operasional</option>
                            <option value="IT">IT</option>
                            <option value="MKT">Marketing</option>
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
        <div class="card archive-card" id="archiveCard">
            @if (count($suratList ?? []) > 0)
                <div id="archiveList">
                <div id="archiveList">
                    @foreach ($suratList->reverse() as $surat)
                        <div class="archive-row" data-perihal="{{ strtolower($surat->perihal) }}" data-nomor="{{ strtolower($surat->nomor_surat) }}" data-dept="{{ $surat->departemen ?? '' }}" data-tanggal="{{ $surat->tanggal }}">
                            <div class="archive-tab" data-dept="{{ $surat->departemen ?? '' }}"></div>
                            <div class="archive-body">
                                <div class="archive-main">
                                    <p class="archive-nomor">{{ $surat->nomor_surat }}</p>
                                    <p class="archive-perihal">{{ $surat->perihal }}</p>
                                </div>
                                <div class="archive-meta">
                                    @if (!empty($surat->departemen))
                                        <span class="dept-chip" data-dept="{{ $surat->departemen }}">{{ $surat->departemen }}</span>
                                    @endif
                                    <span class="archive-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $surat->tanggal }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>

                <div id="emptySearchState" class="d-none">
                    <div class="empty-state">
                        <i class="fa-solid fa-folder-open"></i>
                        <p>Tidak ada surat yang cocok dengan pencarian.</p>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-box-archive"></i>
                    <p>Belum ada surat yang tercatat.</p>
                    <a href="{{ route('tambahsurat') }}" class="empty-cta">Buat surat pertama</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const filterDept = document.getElementById('filterDept');
    const sortOrder = document.getElementById('sortOrder');
    const archiveList = document.getElementById('archiveList');
    const emptySearchState = document.getElementById('emptySearchState');
    const totalCount = document.getElementById('totalCount');

    function applyFilters() {
        if (!archiveList) return;

        const query = searchInput.value.trim().toLowerCase();
        const dept = filterDept.value;
        const rows = Array.from(archiveList.querySelectorAll('.archive-row'));

        let visibleCount = 0;

        rows.forEach(row => {
            const matchQuery = !query ||
                row.dataset.nomor.includes(query) ||
                row.dataset.perihal.includes(query);
            const matchDept = !dept || row.dataset.dept === dept;
            const visible = matchQuery && matchDept;

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

        const rows = Array.from(archiveList.querySelectorAll('.archive-row'));
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
        filterDept.addEventListener('change', applyFilters);
        sortOrder.addEventListener('change', () => {
            applySort();
            applyFilters();
        });
    }
</script>

@endsection