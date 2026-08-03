@extends('layouts.app')

@section('content')

@include('partials.ledger-styles')

<div class="ledger-page">
    <div class="container-fluid py-1 py-md-2">

        @if (session('success'))
            <div class="alert ledger-alert-success d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert ledger-alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="ledger-title mb-1">Riwayat Kontrak</h2>
                {{-- <p class="ledger-subtitle mb-0">Semua kontrak karyawan yang pernah dibuat lewat sistem.</p> --}}
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('kontrak-template.index') }}" class="btn ledger-btn-ghost">
                    <i class="bi bi-file-earmark-text me-1"></i> Kelola Template
                </a>
                <a href="{{ route('kontrak.create') }}" class="btn ledger-btn-brass">
                    <i class="bi bi-file-earmark-plus me-1"></i> Buat Kontrak
                </a>
            </div>
        </div>

        <div class="ledger-toolbar row g-2 mb-3">
            <div class="col-md-7">
                <input type="text" id="searchInput" value="{{ request('search') }}" class="form-control"
                       placeholder="Cari nomor kontrak / nama / NIK karyawan...">
            </div>
            <div class="col-md-5">
                <select id="jenisFilter" class="form-select">
                    <option value="">Semua Jenis Kontrak</option>
                    @foreach ($jenisList as $jenis)
                        <option value="{{ $jenis->id }}" @selected(request('jenis') == $jenis->id)>{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="resultsContainer" style="transition: opacity 0.15s ease;">
            @include('kontrak.partials.results')
        </div>

    </div>
</div>

@push('scripts')
<script>
(function () {
    const searchInput = document.getElementById('searchInput');
    const jenisFilter = document.getElementById('jenisFilter');
    const resultsContainer = document.getElementById('resultsContainer');
    const indexUrl = '{{ route('kontrak.index') }}';

    let debounceTimer = null;

    function currentParams() {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
        if (jenisFilter.value) params.set('jenis', jenisFilter.value);
        return params;
    }

    function attachPaginationHandlers() {
        resultsContainer.querySelectorAll('.pagination a.page-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                fetchResults(url.searchParams.get('page'));
                resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function fetchResults(page) {
        const params = currentParams();
        if (page) params.set('page', page);

        resultsContainer.style.opacity = '0.45';

        fetch(`${indexUrl}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.text())
        .then(html => {
            resultsContainer.innerHTML = html;
            resultsContainer.style.opacity = '1';
            attachPaginationHandlers();

            const qs = params.toString();
            const newUrl = qs ? `${window.location.pathname}?${qs}` : window.location.pathname;
            history.replaceState(null, '', newUrl);
        })
        .catch(() => {
            resultsContainer.style.opacity = '1';
        });
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchResults(), 300);
    });

    jenisFilter.addEventListener('change', () => fetchResults());

    attachPaginationHandlers();
})();
</script>
@endpush

@endsection
