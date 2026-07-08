@extends('layouts.app')

@section('content')

{{--
    Slate Archive theme, versi Bootstrap.
    Pastikan resources/css/asset/riwayatsurat.css (versi Bootstrap) sudah
    di-load di layouts.app, mis:
    <link rel="stylesheet" href="{{ asset('asset/riwayatsurat.css') }}">
--}}

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
                    @foreach (array_reverse($suratList) as $surat)
                        <div class="archive-row" data-perihal="{{ strtolower($surat['perihal']) }}" data-nomor="{{ strtolower($surat['nomor']) }}" data-dept="{{ $surat['departemen'] ?? '' }}" data-tanggal="{{ $surat['tanggal'] }}">
                            <div class="archive-tab" data-dept="{{ $surat['departemen'] ?? '' }}"></div>
                            <div class="archive-body">
                                <div class="archive-main">
                                    <p class="archive-nomor">{{ $surat['nomor'] }}</p>
                                    <p class="archive-perihal">{{ $surat['perihal'] }}</p>
                                </div>
                                <div class="archive-meta">
                                    @if (!empty($surat['departemen']))
                                        <span class="dept-chip" data-dept="{{ $surat['departemen'] }}">{{ $surat['departemen'] }}</span>
                                    @endif
                                    <span class="archive-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $surat['tanggal'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
