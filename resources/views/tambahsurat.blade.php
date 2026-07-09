@extends('layouts.app')

@section('content')

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
