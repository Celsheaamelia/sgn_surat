@extends('layouts.app')

@section('content')

{{--
    Choices.js — bikin <select> jadi searchable, dipakai buat Klasifikasi & Kode Tujuan.
    Di-push ke stack di layout (bukan ditaruh langsung di tengah body) supaya
    tidak merusak posisi sidebar/navbar.
--}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endpush

<style>
    /* ==========================================================================
       Tambah Surat — Registry Ledger Theme (Bootstrap version)
       Scoped to Bootstrap + custom .ledger-* classes used di bawah.
       ========================================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    :root {
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

    .ledger-page {
        background: var(--ledger);
        font-family: var(--font-body);
        color: var(--ink);
        min-height: 100vh;
        position: relative;
        isolation: isolate;
    }

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

    .ledger-alert-success {
        background: var(--success-bg);
        border: 1px solid #cfe2d4;
        color: var(--success);
        border-radius: 0.75rem;
        font-size: 0.9rem;
    }
    .ledger-alert-danger {
        background: var(--danger-bg);
        border: 1px solid #f2d3cc;
        color: var(--danger);
        border-radius: 0.75rem;
        font-size: 0.9rem;
    }

    .ledger-card,
    .ledger-stamp,
    .ledger-status {
        border-radius: 0.9rem;
        border: 1px solid var(--line);
        box-shadow: 0 1px 2px rgba(28,43,35,0.05), 0 1px 10px rgba(28,43,35,0.04);
    }

    .ledger-card {
        background: var(--paper);
    }

    .ledger-card-header {
        background: transparent;
        border-bottom: 1px solid var(--line);
        padding: 1.5rem 1.75rem 1.1rem;
    }

    .ledger-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.55rem;
        color: var(--ink);
        letter-spacing: -0.01em;
        margin-bottom: 0.25rem;
    }

    .ledger-subtitle {
        color: var(--ink-soft);
        font-size: 0.85rem;
    }

    .ledger-card .card-body {
        padding: 1.75rem;
    }

    .ledger-form label {
        color: var(--ink);
        font-weight: 600;
        font-size: 0.82rem;
        letter-spacing: 0.01em;
    }

    .ledger-required {
        color: var(--brass-dark);
    }

    .ledger-form .form-control,
    .ledger-form .form-select {
        border-radius: 0.55rem;
        border: 1px solid var(--line);
        padding: 0.65rem 0.95rem;
        font-size: 0.92rem;
        font-family: var(--font-body);
        color: var(--ink);
        background-color: var(--paper);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .ledger-form .form-control:focus,
    .ledger-form .form-select:focus {
        outline: none;
        border-color: var(--brass);
        box-shadow: 0 0 0 3px rgba(169,129,47,0.16);
    }

    .ledger-form .form-control:focus-visible,
    .ledger-form .form-select:focus-visible,
    .ledger-btn-brass:focus-visible,
    .ledger-btn-ghost:focus-visible {
        outline: 2px solid var(--brass-dark);
        outline-offset: 2px;
    }

    .ledger-form .form-control:disabled {
        background-color: var(--ledger);
        color: var(--ink-soft);
        border-color: var(--ledger-line);
        font-family: var(--font-mono);
        letter-spacing: 0.08em;
    }

    .ledger-form .form-control::placeholder {
        color: #a3ada2;
    }

    /* ==========================================================================
       Choices.js override — biar nyatu sama tema ledger DAN dikunci lebarnya
       ========================================================================== */

    .ledger-form .choices {
        margin-bottom: 0;
        font-size: 0.92rem;
        font-family: var(--font-body);
        width: 100% !important;
        max-width: 100%;
    }

    .ledger-form .choices__inner {
        background-color: var(--paper) !important;
        border: 1px solid var(--line) !important;
        border-radius: 0.55rem !important;
        padding: 0.5rem 0.75rem !important;
        min-height: 48px;
        width: 100%;
        box-sizing: border-box;
    }

    .ledger-form .choices.is-focused .choices__inner,
    .ledger-form .choices.is-open .choices__inner {
        border-color: var(--brass) !important;
        box-shadow: 0 0 0 3px rgba(169,129,47,0.16);
    }

    .ledger-form .choices__list--single .choices__item {
        color: var(--ink);
    }

    .ledger-form .choices__list--dropdown,
    .ledger-form .choices__list[aria-expanded] {
        border: 1px solid var(--line) !important;
        border-radius: 0.55rem !important;
        box-shadow: 0 8px 24px rgba(28,43,35,0.12);
        overflow: hidden;
        width: 100%;
        z-index: 20;
    }

    .ledger-form .choices__list--dropdown .choices__input {
        border-bottom: 1px solid var(--line) !important;
        background: var(--paper);
        color: var(--ink);
        font-family: var(--font-body);
    }

    .ledger-form .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: var(--brass-tint) !important;
        color: var(--brass-dark);
    }

    .ledger-form .choices__list--dropdown .choices__item {
        font-size: 0.9rem;
        padding: 0.6rem 0.85rem;
    }

    .ledger-form .choices__placeholder {
        opacity: 1;
        color: #a3ada2;
    }

    .ledger-help {
        color: #8a9587;
        font-size: 0.75rem;
        margin-top: 0.35rem;
    }

    .ledger-btn-ghost {
        background: transparent;
        color: var(--ink-soft);
        font-weight: 500;
        border: none;
        padding: 0.6rem 1rem;
        transition: color 0.15s ease;
    }
    .ledger-btn-ghost:hover {
        color: var(--ink);
    }

    .ledger-btn-brass {
        background: linear-gradient(180deg, #b8903f, var(--brass-dark));
        box-shadow: 0 4px 14px rgba(138,106,36,0.35);
        border: none;
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.01em;
        padding: 0.65rem 1.4rem;
        border-radius: 0.55rem;
    }
    .ledger-btn-brass:hover {
        background: linear-gradient(180deg, #c39a4c, #7a5c1f);
        box-shadow: 0 6px 18px rgba(138,106,36,0.42);
        color: #fff;
    }

    .ledger-stamp {
        background: linear-gradient(160deg, #24382e 0%, var(--ink) 70%);
        border: 1px solid rgba(255,255,255,0.06);
        position: relative;
        overflow: hidden;
        color: var(--brass-tint);
    }

    .ledger-stamp::before {
        content: "TERDAFTAR";
        position: absolute;
        top: 14px;
        right: -34px;
        font-family: var(--font-mono);
        font-size: 0.62rem;
        letter-spacing: 0.28em;
        color: rgba(244,236,216,0.14);
        transform: rotate(8deg);
        pointer-events: none;
    }

    .ledger-stamp-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.05rem;
        color: var(--brass-tint);
    }

    .ledger-stamp .fa-eye {
        color: var(--brass);
    }

    .ledger-stamp-box {
        background: rgba(169,129,47,0.08);
        border: 1.5px dashed rgba(244,236,216,0.35);
        border-radius: 0.85rem;
        position: relative;
        padding: 1.1rem 1rem;
    }

    .ledger-stamp-label {
        color: rgba(244,236,216,0.65);
        font-family: var(--font-mono);
        font-size: 0.65rem;
        letter-spacing: 0.22em;
        text-transform: uppercase;
    }

    #previewNumber {
        display: inline-block;
        color: var(--brass-tint);
        font-family: var(--font-mono);
        font-weight: 600;
        font-size: 1.05rem;
        letter-spacing: 0.04em;
        word-break: break-all;
    }

    .ledger-stamp-key {
        color: rgba(244,236,216,0.55);
        font-size: 0.83rem;
    }
    .ledger-stamp-value {
        color: var(--brass-tint);
        font-family: var(--font-mono);
        font-weight: 600;
        font-size: 0.86rem;
        letter-spacing: 0.02em;
    }

    .ledger-status {
        background: var(--paper);
    }
    .ledger-status-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1rem;
        color: var(--ink);
    }
    .ledger-status-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: var(--brass);
        box-shadow: 0 0 0 3px rgba(169,129,47,0.18);
        display: inline-block;
        flex-shrink: 0;
    }
    .ledger-status-line {
        color: var(--ink-soft);
        font-family: var(--font-mono);
        font-size: 0.82rem;
    }

    .ledger-table-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--ink);
    }

    .ledger-table thead {
        background: var(--ledger);
    }
    .ledger-table thead th {
        color: var(--ink-soft);
        font-family: var(--font-mono);
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 600;
        border-bottom: none;
        padding: 0.9rem 1.75rem;
    }

    .ledger-table tbody tr {
        border-top: 1px solid var(--ledger-line);
    }
    .ledger-table tbody tr:hover {
        background: var(--brass-tint);
    }
    .ledger-table tbody tr td {
        padding: 0.8rem 1.75rem;
        vertical-align: middle;
    }
    .ledger-table .ledger-nomor {
        color: var(--brass-dark);
        font-family: var(--font-mono);
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .ledger-table .ledger-perihal {
        color: var(--ink);
    }
    .ledger-table .ledger-tanggal {
        color: var(--ink-soft);
        font-family: var(--font-mono);
        font-size: 0.82rem;
    }

    @media (max-width: 1024px) {
        #previewNumber {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 640px) {
        #previewNumber {
            display: block;
            width: 100%;
        }
        .ledger-stamp::before {
            display: none;
        }
        .ledger-table thead th,
        .ledger-table tbody tr td {
            padding: 0.75rem 1rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }
</style>

<div class="ledger-page">
    <div class="container-fluid py-4 py-md-5">

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="alert ledger-alert-success d-flex align-items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- Alert error --}}
        @if ($errors->any())
            <div class="alert ledger-alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="row g-4">

            {{-- FORM --}}
            <div class="col-lg-8">
                <div class="card ledger-card h-100">

                    <div class="card-header ledger-card-header">
                        <h2 class="ledger-title">Buat Nomor Surat</h2>
                   </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('surat.store') }}" class="ledger-form" id="suratForm">
                            @csrf

                            {{-- Judul / Perihal Surat --}}
                            <div class="mb-3">
                                <label for="perihal" class="form-label">
                                    Perihal Surat <span class="ledger-required">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="perihal"
                                    id="perihal"
                                    required
                                    placeholder="e.g. Annual Budget Approval Request"
                                    value="{{ old('perihal') }}"
                                    class="form-control">
                            </div>

                            <div class="row g-3 mb-3">
                                {{-- Penandatangan --}}
                                <div class="col-md-6">
                                    <label for="signatory" class="form-label">
                                        Penandatangan <span class="ledger-required">*</span>
                                    </label>
                                    {{-- NOTE: value pakai id (bukan kode) — pastikan controller
                                         surat.store juga baca 'signatory' sebagai id, bukan kode. --}}
                                    <select name="signatory" id="signatory" required class="form-select">
                                        <option value="">Pilih Penandatangan</option>
                                        @foreach ($penandatanganList as $penandatangan)
                                            <option value="{{ $penandatangan->id }}"
                                                    data-kode="{{ $penandatangan->kode }}"
                                                    @selected(old('signatory') == $penandatangan->id)>
                                                {{ $penandatangan->jabatan }} ({{ $penandatangan->kode }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Kode Tujuan (searchable) --}}
                                <div class="col-md-6">
                                    <label for="kode_tujuan" class="form-label">
                                        Kode Tujuan <span class="ledger-required">*</span>
                                    </label>
                                    <select name="kode_tujuan" id="kode_tujuan" required class="form-select">
                                        <option value="">Pilih Kode Tujuan</option>
                                        @foreach ($tujuanList as $tujuan)
                                            <option value="{{ $tujuan->id }}"
                                                    data-kode="{{ $tujuan->kode }}"
                                                    @selected(old('kode_tujuan') == $tujuan->id)>
                                                {{ $tujuan->kode }} — {{ $tujuan->nama_tujuan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                {{-- Klasifikasi Surat (searchable) --}}
                                <div class="col-md-6">
                                    <label for="klasifikasi" class="form-label">
                                        Klasifikasi Surat <span class="ledger-required">*</span>
                                    </label>
                                    <select name="klasifikasi" id="klasifikasi" required class="form-select">
                                        <option value="">Pilih Klasifikasi Surat</option>
                                        @foreach ($klasifikasiList as $klasifikasi)
                                            <option value="{{ $klasifikasi->id }}"
                                                    data-kode="{{ $klasifikasi->kode }}"
                                                    @selected(old('klasifikasi') == $klasifikasi->id)>
                                                {{ $klasifikasi->kode }} — {{ $klasifikasi->jenis_surat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Tanggal Surat --}}
                                <div class="col-md-6">
                                    <label for="tanggal" class="form-label">
                                        Tanggal Surat <span class="ledger-required">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        name="tanggal"
                                        id="tanggal"
                                        required
                                        value="{{ old('tanggal', date('Y-m-d')) }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- Nomor urut (readonly, otomatis dari server) — 4 digit --}}
                            <div class="mb-4">
                                <label class="form-label">Nomor Urut</label>
                                <input
                                    type="text"
                                    id="nomorUrut"
                                    value="{{ str_pad($nextSequence,3,'0',STR_PAD_LEFT) }}"
                                    disabled
                                    class="form-control">
                                <div class="ledger-help">Nomor urut ini otomatis, berdasarkan surat terakhir yang dibuat.</div>
                            </div>

                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <button type="reset" class="btn ledger-btn-ghost">
                                    Reset
                                </button>
                                <button type="submit" class="btn ledger-btn-brass">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                    Simpan Surat
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            {{-- PREVIEW --}}
            <div class="col-lg-4">

                <div class="card ledger-stamp mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <i class="fa-solid fa-eye"></i>
                            <h3 class="ledger-stamp-title mb-0">Live Preview</h3>
                        </div>

                        <div class="ledger-stamp-box mb-4">
                            <p class="ledger-stamp-label mb-1">Generated Number</p>
                            {{-- Format: SIGNATORY-TUJUAN-KLASIFIKASI/YYYYMMDD.SEQ (e.g. SG26-BD05-SKP/20260710.001) --}}
                            <p class="mb-0" id="previewNumber">
                                ---&#8209;---&#8209;---/--------.{{ str_pad($nextSequence, 3, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="ledger-stamp-key">Penandatangan</span>
                            <span class="ledger-stamp-value" id="previewSign">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="ledger-stamp-key">Tujuan</span>
                            <span class="ledger-stamp-value" id="previewTujuan">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="ledger-stamp-key">Klasifikasi</span>
                            <span class="ledger-stamp-value" id="previewKlasifikasi">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="ledger-stamp-key">Tanggal</span>
                            <span class="ledger-stamp-value" id="previewTanggal">-</span>
                        </div>
                    </div>
                </div>

                <div class="card ledger-status">
                    <div class="card-body">
                        <h3 class="ledger-status-title mb-3">Status Sistem</h3>
                        <div class="d-flex align-items-center gap-2 ledger-status-line">
                            <span class="ledger-status-dot"></span>
                            Siap generate nomor #{{ str_pad($nextSequence, 3, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>

       {{-- Daftar surat yang sudah dibuat --}}
        @if (count($suratList ?? []) > 0)
            <div class="card ledger-card mt-4">
                <div class="card-header ledger-card-header">
                    <h3 class="ledger-table-title mb-0">Daftar Nomor Surat</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table ledger-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nomor Surat</th>
                                <th>Perihal</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suratList->reverse() as $surat)
                                <tr>
                                    <td class="ledger-nomor">{{ $surat->nomor_surat }}</td>
                                    <td class="ledger-perihal">{{ $surat->perihal }}</td>
                                    <td class="ledger-tanggal">{{ $surat->tanggal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    async function loadSequence() {
        if (!tanggalEl.value) return;
        const response = await fetch(
            `/surat/next-sequence?tanggal=${tanggalEl.value}`
        );
        const data = await response.json();
        seqText = data.sequence;
        // update input Nomor Urut
        document.getElementById('nomorUrut').value = seqText;
        // update preview
        updatePreview();
    }

    // Bikin dropdown Kode Tujuan & Klasifikasi jadi bisa dicari
    const tujuanChoices = new Choices('#kode_tujuan', {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari kode tujuan...',
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Pilih Kode Tujuan',
    });

    const klasifikasiChoices = new Choices('#klasifikasi', {
        searchEnabled: true,
        searchPlaceholderValue: 'Cari klasifikasi...',
        itemSelectText: '',
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Pilih Klasifikasi Surat',
    });

    const signEl = document.getElementById('signatory');
    const tujuanEl = document.getElementById('kode_tujuan');
    const klasifikasiEl = document.getElementById('klasifikasi');
    const tanggalEl = document.getElementById('tanggal');
    let seqText = "001";

    // "2026-07-10" -> "20260710"
    function formatTanggal(isoDate) {
        return isoDate ? isoDate.replaceAll('-', '') : '--------';
    }

    // Ambil kode singkat dari atribut data-kode option yang dipilih (fallback ke '-')
    function selectedKode(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        return opt && opt.dataset.kode ? opt.dataset.kode : '-';
    }

    // Format final: SIGNATORY-TUJUAN-KLASIFIKASI/YYYYMMDD.SEQ
    // Contoh: SG26-BD05-SKP/20260710.0001
    function updatePreview() {
        const sign = selectedKode(signEl);
        const tujuan = selectedKode(tujuanEl);
        const klasifikasi = selectedKode(klasifikasiEl);
        const tanggal = formatTanggal(tanggalEl.value);

        document.getElementById('previewNumber').textContent =
            `${sign}-${tujuan}-${klasifikasi}/${tanggal}.${seqText}`;
        document.getElementById('previewSign').textContent = sign;
        document.getElementById('previewTujuan').textContent = tujuan;
        document.getElementById('previewKlasifikasi').textContent = klasifikasi;
        document.getElementById('previewTanggal').textContent = tanggalEl.value || '-';
    }

    signEl.addEventListener('change', updatePreview);
    tujuanEl.addEventListener('change', updatePreview);
    klasifikasiEl.addEventListener('change', updatePreview);
    tanggalEl.addEventListener('change', loadSequence);
    loadSequence();
</script>
@endpush

@endsection