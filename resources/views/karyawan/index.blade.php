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
                <p class="ledger-subtitle mb-0">Database ini dipakai untuk isi otomatis dokumen kontrak.</p>
            </div>
            <a href="{{ route('karyawan.create') }}" class="btn ledger-btn-brass">
                <i class="bi bi-person-plus me-1"></i> Tambah Karyawan
            </a>
        </div>

        <form method="GET" class="ledger-toolbar row g-2 mb-3">
            <div class="col-md-8">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                       placeholder="Cari nama / NIK / jabatan...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn ledger-btn-brass flex-fill">Cari</button>
                <a href="{{ route('karyawan.index') }}" class="btn ledger-btn-ghost">Reset</a>
            </div>
        </form>

        <div class="card ledger-card">
            <div class="card-body p-0">
                <table class="table ledger-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawanList as $k)
                            <tr>
                                <td class="ledger-tanggal">{{ $k->nik }}</td>
                                <td class="ledger-perihal">{{ $k->nama }}</td>
                                <td class="ledger-tujuan">{{ $k->jabatan ?? '-' }}</td>
                                <td class="ledger-tujuan">{{ $k->departemen ?? '-' }}</td>
                                <td>
                                    <span class="ledger-status-pill {{ $k->status_karyawan === 'Aktif' ? 'is-active' : 'is-done' }}">
                                        {{ $k->status_karyawan }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('karyawan.edit', $k) }}" class="btn ledger-btn-detail" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('karyawan.destroy', $k) }}"
                                              onsubmit="return confirm('Hapus data {{ $k->nama }}? Kontrak yang sudah dibuat untuk karyawan ini juga akan terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn ledger-btn-detail text-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 ledger-subtitle">Belum ada data karyawan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($karyawanList->hasPages())
                <div class="card-body">
                    {{ $karyawanList->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
