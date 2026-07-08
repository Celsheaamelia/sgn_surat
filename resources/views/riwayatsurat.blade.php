@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto p-6 md:p-10 bg-gray-50 min-h-screen" id="riwayatPage">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
        <span>&gt;</span>
        <span>Letter Management</span>
        <span>&gt;</span>
        <span class="text-blue-600 font-medium">Riwayat Surat</span>
    </div>

    {{-- Alert sukses (jika ada aksi hapus dll) --}}
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header + Toolbar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-8 pt-8 pb-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Riwayat Surat</h2>
                    <p class="text-sm text-gray-500 mt-1">Arsip seluruh nomor surat yang pernah diterbitkan.</p>
                </div>
                <div class="flex items-center gap-2 text-sm" id="totalCounter">
                    <span class="counter-badge">
                        <span id="totalCount">{{ count($suratList ?? []) }}</span> surat tercatat
                    </span>
                </div>
            </div>

            {{-- Toolbar: search + filter --}}
            <div class="mt-6 flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Cari nomor surat atau perihal..."
                        class="w-full pl-11 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2"
                    >
                </div>

                <select id="filterDept" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2">
                    <option value="">Semua Departemen</option>
                    <option value="HRD">HRD</option>
                    <option value="FIN">Finance</option>
                    <option value="OPS">Operasional</option>
                    <option value="IT">IT</option>
                    <option value="MKT">Marketing</option>
                </select>

                <select id="sortOrder" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2">
                    <option value="desc">Terbaru dulu</option>
                    <option value="asc">Terlama dulu</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Archive list --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" id="archiveCard">
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

            <div id="emptySearchState" class="hidden">
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

            row.classList.toggle('hidden', !visible);
            if (visible) visibleCount++;
        });

        totalCount.textContent = visibleCount;

        if (emptySearchState) {
            emptySearchState.classList.toggle('hidden', visibleCount !== 0);
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