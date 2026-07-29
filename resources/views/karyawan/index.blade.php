@extends('layouts.app')

@section('content')

@include('partials.ledger-styles')

<div class="ledger-page">
    <div class="container-fluid py-1 py-md-2">

        @if (session('success'))
            <div class="alert ledger-alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="ledger-title mb-1">Data Karyawan</h2>
                <p class="ledger-subtitle mb-0">Database identitas ini dipakai untuk isi otomatis dokumen kontrak.</p>
            </div>
            <a href="{{ route('karyawan.create') }}" class="btn ledger-btn-brass">
                <i class="bi bi-person-plus me-1"></i> Tambah Karyawan
            </a>
        </div>

        <div class="ledger-toolbar mb-3 position-relative">
            <input type="text" id="karyawanLiveSearch" value="{{ request('search') }}" class="form-control"
                   placeholder="Ketik nama / NIK / No. KTP..." autocomplete="off">
            <div class="spinner-border spinner-border-sm text-secondary position-absolute d-none"
                 id="karyawanSearchSpinner" style="right: 0.9rem; top: 0.65rem;" role="status"></div>
        </div>

        <div id="karyawanTableWrap">
            @include('karyawan._table')
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('karyawanLiveSearch');
    const wrap = document.getElementById('karyawanTableWrap');
    const spinner = document.getElementById('karyawanSearchSpinner');
    const baseUrl = "{{ route('karyawan.index') }}";
    let debounceTimer = null;
    let currentRequest = null;

    async function runSearch(q, pushUrl = true) {
        spinner.classList.remove('d-none');

        const url = q ? `${baseUrl}?search=${encodeURIComponent(q)}` : baseUrl;

        if (pushUrl) {
            window.history.replaceState({}, '', url);
        }

        if (currentRequest) {
            currentRequest.abort();
        }
        const controller = new AbortController();
        currentRequest = controller;

        try {
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller.signal,
            });
            const html = await res.text();
            wrap.innerHTML = html;
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error(err);
            }
        } finally {
            spinner.classList.add('d-none');
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => runSearch(this.value.trim()), 300);
    });

    // Klik link paginasi di dalam tabel tetap jalan normal (reload halaman biasa).
});
</script>
@endpush

@endsection
