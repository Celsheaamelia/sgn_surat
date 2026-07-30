<div class="card ledger-card">
    <div class="card-body p-0">
        <table class="table ledger-table align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:1%;">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>No. KTP</th>
                    <th>Tempat, Tanggal Lahir</th>
                    <th>Agama</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawanList as $k)
                    @php
                        $jenisKontrakTerakhir = $k->latestKontrak->jenisKontrak ?? null;
                    @endphp
                    <tr>
                        <td class="ledger-tanggal">{{ $karyawanList->firstItem() + $loop->index }}</td>
                        <td class="ledger-tanggal">{{ $k->nik }}</td>
                        <td class="ledger-perihal">{{ $k->nama }}</td>
                        <td class="ledger-tanggal">{{ $k->no_ktp ?? '-' }}</td>
                        <td class="ledger-tujuan">{{ $k->tempat_tanggal_lahir ?? '-' }}</td>
                        <td class="ledger-tujuan">{{ $k->agama ?? '-' }}</td>
                        <td>
                            @if ($jenisKontrakTerakhir)
                                <span class="ledger-status-pill {{ $jenisKontrakTerakhir->masa_giling ? 'is-draft' : 'is-active' }}">
                                    {{ $jenisKontrakTerakhir->nama_singkat ?: $jenisKontrakTerakhir->nama_jenis }}
                                </span>
                            @else
                                <span class="ledger-subtitle">-</span>
                            @endif
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
                        <td colspan="8" class="text-center py-4 ledger-subtitle">
                            {{ (request('search') || request('status_kontrak')) ? 'Tidak ada karyawan yang cocok dengan pencarian/filter.' : 'Belum ada data karyawan.' }}
                        </td>
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