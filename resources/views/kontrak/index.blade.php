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
                <p class="ledger-subtitle mb-0">Semua kontrak karyawan yang pernah dibuat lewat sistem.</p>
            </div>
            <a href="{{ route('kontrak.create') }}" class="btn ledger-btn-brass">
                <i class="bi bi-file-earmark-plus me-1"></i> Buat Kontrak
            </a>
        </div>

        <form method="GET" class="ledger-toolbar row g-2 mb-3">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                       placeholder="Cari nomor kontrak / nama / NIK karyawan...">
            </div>
            <div class="col-md-4">
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis Kontrak</option>
                    @foreach ($jenisList as $jenis)
                        <option value="{{ $jenis->id }}" @selected(request('jenis') == $jenis->id)>{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn ledger-btn-brass flex-fill">Cari</button>
                <a href="{{ route('kontrak.index') }}" class="btn ledger-btn-ghost">Reset</a>
            </div>
        </form>

        <div class="card ledger-card">
            <div class="card-body p-0">
                <table class="table ledger-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nomor Kontrak</th>
                            <th>Karyawan</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kontrakList as $kontrak)
                            @php
                                $statusClass = match($kontrak->status) {
                                    'Aktif' => 'is-active',
                                    'Selesai' => 'is-done',
                                    'Direservasi' => 'is-reserved',
                                    default => 'is-draft',
                                };
                            @endphp
                            <tr>
                                <td class="ledger-nomor">{{ $kontrak->nomor_kontrak }}</td>
                                <td>
                                    <div class="ledger-perihal">{{ $kontrak->karyawan->nama ?? '-' }}</div>
                                    <div class="ledger-help mb-0">{{ $kontrak->karyawan->nik ?? '-' }}</div>
                                </td>
                                <td class="ledger-tujuan">{{ $kontrak->jenisKontrak->nama_jenis ?? '-' }}</td>
                                <td class="ledger-tanggal">
                                    {{ optional($kontrak->tanggal_mulai)->format('d/m/Y') }}
                                    &ndash;
                                    {{ $kontrak->tanggal_selesai ? $kontrak->tanggal_selesai->format('d/m/Y') : 'Tetap' }}
                                </td>
                                <td><span class="ledger-status-pill {{ $statusClass }}">{{ $kontrak->status }}</span></td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @if ($kontrak->generated_file_path)
                                            <a href="{{ route('kontrak.download', $kontrak) }}" class="btn ledger-btn-detail" title="Download dokumen Word">
                                                <i class="bi bi-file-earmark-word"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('kontrak.upload.form', $kontrak) }}" class="btn ledger-btn-detail" title="Upload / lihat kontrak bertanda tangan">
                                            <i class="bi bi-upload"></i>
                                        </a>
                                        <a href="{{ route('kontrak.show', $kontrak) }}" class="btn ledger-btn-detail" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 ledger-subtitle">Belum ada kontrak yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($kontrakList->hasPages())
                <div class="card-body">
                    {{ $kontrakList->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
