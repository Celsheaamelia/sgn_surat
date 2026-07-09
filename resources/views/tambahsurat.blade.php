@extends('layouts.app')

@section('content')

<style>
    /* ==========================================================================
       Tambah Surat — Registry Ledger Theme (Bootstrap version)
       Scoped to Bootstrap + custom .ledger-* classes used di bawah.

       Concept: halaman ini menerbitkan nomor surat resmi berurutan —
       jadi tampilannya meminjam gaya buku ledger pemerintahan.
       Pale green-tinted ledger paper, hairline rule guides, brass
       registrar's stamp untuk nomor yang di-generate, dan monospace
       serial-number untuk apa pun yang berperan sebagai "nomor".
       ========================================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    :root {
        /* Color tokens */
        --ink:        #1c2b23;   /* near-black, green-cast */
        --ink-soft:   #3d4f45;
        --ledger:     #eef3ea;   /* pale green-tinted ledger paper */
        --ledger-line:#cdd9c8;   /* faint rule lines on ledger paper */
        --paper:      #fbfcf9;   /* card surface, slightly warmer than white */
        --brass:      #a9812f;   /* registrar's stamp / primary accent */
        --brass-dark: #8a6a24;
        --brass-tint: #f4ecd8;
        --line:       #dfe6da;
        --danger:     #b3432f;
        --danger-bg:  #fdf1ee;
        --success:    #3f6b4a;
        --success-bg: #eef5ef;

        /* Type */
        --font-display: 'Fraunces', Georgia, serif;
        --font-body: 'Inter', -apple-system, sans-serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, monospace;
    }

    /* ==========================================================================
       Page base — ledger paper canvas
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

    /* Alerts */
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

    /* ==========================================================================
       Cards
       ========================================================================== */

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

    /* ==========================================================================
       Form fields
       ========================================================================== */

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

    /* Nomor urut helper text */
    .ledger-help {
        color: #8a9587;
        font-size: 0.75rem;
        margin-top: 0.35rem;
    }

    /* ==========================================================================
       Buttons
       ========================================================================== */

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

    /* ==========================================================================
       Live Preview — the registrar's stamp
       ========================================================================== */

    .ledger-stamp {
        background: linear-gradient(160deg, #24382e 0%, var(--ink) 70%);
        border: 1px solid rgba(255,255,255,0.06);
        position: relative;
        overflow: hidden;
        color: var(--brass-tint);
    }

    /* faint watermark, like a registry office's ghost stamp in the corner */
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

    /* The stamped number itself: dashed ring, like a perforated seal */
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

    /* Stat rows below the stamp */
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

    /* Status Sistem card */
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

    /* ==========================================================================
       Daftar Nomor Surat — the ledger table
       ========================================================================== */

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

    /* ==========================================================================
       Responsive
       ========================================================================== */

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

    /* Respect reduced-motion preferences */
    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
        }
    }
</style>

<div class="ledger-page">
    <div class="container-fluid py-4 py-md-5">

        {{-- Breadcrumb --}}
        {{-- <nav aria-label="breadcrumb">
            <ol class="breadcrumb ledger-breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house me-1"></i>Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ Route::has('surat.index') ? route('surat.index') : '#' }}">Letter Management</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Surat</li>
            </ol>
        </nav> --}}

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
                        <p class="ledger-subtitle mb-0">Isi data di bawah untuk generate nomor surat baru.</p>
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
                                {{-- Departemen --}}
                                <div class="col-md-6">
                                    <label for="departemen" class="form-label">
                                        Departemen <span class="ledger-required">*</span>
                                    </label>
                                    <select name="departemen" id="departemen" required class="form-select">
                                        <option value="">Pilih Departemen</option>
                                        <option value="HRD">HRD</option>
                                        <option value="FIN">Finance</option>
                                        <option value="OPS">Operasional</option>
                                        <option value="IT">IT</option>
                                        <option value="MKT">Marketing</option>
                                    </select>
                                </div>

                                {{-- Penandatangan --}}
                                <div class="col-md-6">
                                    <label for="signatory" class="form-label">
                                        Penandatangan <span class="ledger-required">*</span>
                                    </label>
                                    <select name="signatory" id="signatory" required class="form-select">
                                        <option value="">Pilih Penandatangan</option>
                                        <option value="GM">General Manager (GM)</option>
                                        <option value="DIR">Direktur (DIR)</option>
                                        <option value="MGR">Manager (MGR)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
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

                                {{-- Kode Tujuan --}}
                                <div class="col-md-6">
                                    <label for="kode_tujuan" class="form-label">
                                        Kode Tujuan <span class="ledger-required">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="kode_tujuan"
                                        id="kode_tujuan"
                                        required
                                        placeholder="e.g. EXT, INT, BOD"
                                        value="{{ old('kode_tujuan') }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- Nomor urut (readonly, otomatis dari server) --}}
                            <div class="mb-4">
                                <label class="form-label">Nomor Urut</label>
                                <input
                                    type="text"
                                    value="{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}"
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
                            <p class="mb-0" id="previewNumber">
                                {{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}/---/---/{{ date('Y') }}/{{ date('m') }}/{{ date('d') }}
                            </p>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="ledger-stamp-key">Departemen</span>
                            <span class="ledger-stamp-value" id="previewDept">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="ledger-stamp-key">Penandatangan</span>
                            <span class="ledger-stamp-value" id="previewSign">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="ledger-stamp-key">Tahun</span>
                            <span class="ledger-stamp-value">{{ date('Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="card ledger-status">
                    <div class="card-body">
                        <h3 class="ledger-status-title mb-3">Status Sistem</h3>
                        <div class="d-flex align-items-center gap-2 ledger-status-line">
                            <span class="ledger-status-dot"></span>
                            Siap generate nomor #{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}
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
                            @foreach (array_reverse($suratList) as $surat)
                                <tr>
                                    <td class="ledger-nomor">{{ $surat['nomor'] }}</td>
                                    <td class="ledger-perihal">{{ $surat['perihal'] }}</td>
                                    <td class="ledger-tanggal">{{ $surat['tanggal'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    const deptEl = document.getElementById('departemen');
    const signEl = document.getElementById('signatory');
    const seqText = "{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}";
    const year = "{{ date('Y') }}";
    const month = "{{ date('m') }}";
    const day = "{{ date('d') }}";

    function updatePreview() {
        const dept = deptEl.value || '---';
        const sign = signEl.value || '---';
        document.getElementById('previewNumber').textContent =
            `${seqText}/${dept}/${sign}/${year}/${month}/${day}`;
        document.getElementById('previewDept').textContent = dept === '---' ? '-' : dept;
        document.getElementById('previewSign').textContent = sign === '---' ? '-' : sign;
    }

    deptEl.addEventListener('change', updatePreview);
    signEl.addEventListener('change', updatePreview);
</script>

@endsection