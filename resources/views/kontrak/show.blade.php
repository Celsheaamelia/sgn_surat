@extends('layouts.app')

@section('content')

@include('partials.ledger-styles')

<div class="ledger-page">
    <div class="container-fluid py-1 py-md-2">

        @if (session('success'))
            <div class="alert ledger-alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert ledger-alert-danger">{{ session('error') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="ledger-title mb-1">Detail Kontrak</h2>
                <p class="ledger-subtitle mb-0 ledger-nomor">{{ $kontrak->nomor_kontrak }}</p>
            </div>
            <a href="{{ route('kontrak.index') }}" class="btn ledger-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card ledger-card">
                    <div class="card-header ledger-card-header">
                        <h3 class="ledger-table-title mb-0">Informasi Kontrak</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4 ledger-subtitle">Nama</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->nama }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">NIK Karyawan</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->nik }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">No. KTP</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->no_ktp ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Tempat/Tanggal Lahir</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->tempat_tanggal_lahir ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Jenis Kelamin</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->jenis_kelamin ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Agama</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->agama ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Status Perkawinan</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->status_perkawinan ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Alamat</dt>
                            <dd class="col-sm-8">{{ $kontrak->karyawan->alamat ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Jabatan</dt>
                            <dd class="col-sm-8">{{ $kontrak->jabatan_kontrak ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Bagian / Departemen</dt>
                            <dd class="col-sm-8">{{ $kontrak->bagian_kontrak ?: '-' }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Rincian Pekerjaan</dt>
                            <dd class="col-sm-8">
                                @if ($kontrak->rincian_pekerjaan_1 || $kontrak->rincian_pekerjaan_2 || $kontrak->rincian_pekerjaan_3)
                                    <ol class="mb-0 ps-3">
                                        @if ($kontrak->rincian_pekerjaan_1)
                                            <li>{{ $kontrak->rincian_pekerjaan_1 }}</li>
                                        @endif
                                        @if ($kontrak->rincian_pekerjaan_2)
                                            <li>{{ $kontrak->rincian_pekerjaan_2 }}</li>
                                        @endif
                                        @if ($kontrak->rincian_pekerjaan_3)
                                            <li>{{ $kontrak->rincian_pekerjaan_3 }}</li>
                                        @endif
                                    </ol>
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-sm-4 ledger-subtitle">Jenis Kontrak</dt>
                            <dd class="col-sm-8">{{ $kontrak->jenisKontrak->nama_jenis }}</dd>

                            <dt class="col-sm-4 ledger-subtitle">Penandatangan</dt>
                            <dd class="col-sm-8">{{ $kontrak->penandatangan->jabatan }} ({{ $kontrak->penandatangan->kode }})</dd>

                            <dt class="col-sm-4 ledger-subtitle">Periode</dt>
                            <dd class="col-sm-8">
                                {{ $kontrak->tanggal_mulai->format('d F Y') }}
                                &ndash;
                                @if ($kontrak->tanggal_selesai)
                                    {{ $kontrak->tanggal_selesai->format('d F Y') }}
                                @elseif ($kontrak->jenisKontrak->masa_giling)
                                    Sampai dengan berakhirnya Masa Giling
                                @else
                                    Tidak ditentukan (tetap)
                                @endif
                            </dd>

                            <dt class="col-sm-4 ledger-subtitle">Tanggal Dibuat</dt>
                            <dd class="col-sm-8">{{ $kontrak->created_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card ledger-status">
                    <div class="card-body d-flex flex-column gap-3">
                        <h3 class="ledger-status-title mb-0">Dokumen</h3>

                        @if ($kontrak->generated_file_path)
                            <a href="{{ route('kontrak.download', $kontrak) }}" class="btn ledger-btn-brass w-100">
                                <i class="bi bi-file-earmark-word me-1"></i> Download Dokumen Word
                            </a>
                        @else
                            <p class="ledger-help mb-0">Dokumen belum berhasil digenerate.</p>
                        @endif

                        <form method="POST" action="{{ route('kontrak.regenerate', $kontrak) }}">
                            @csrf
                            <button type="submit" class="btn ledger-btn-ghost w-100">
                                <i class="bi bi-arrow-repeat me-1"></i> Generate Ulang Dokumen
                            </button>
                        </form>

                        <a href="{{ route('kontrak.upload.form', $kontrak) }}" class="btn ledger-btn-ghost w-100">
                            <i class="bi bi-upload me-1"></i> Kelola File Bertanda Tangan
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
