@extends('layouts.app')

@section('content')
@include('patroli._styles')

<div class="patroli-page">
    <div class="container-fluid py-1 py-md-2">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <div class="patroli-eyebrow mb-1">Patroli Digital</div>
                <h2 class="patroli-title mb-1" style="font-size: 1.6rem;">Monitoring Patroli</h2>
                <p class="patroli-subtitle mb-0">
                    {{ now()->translatedFormat('l, d F Y') }}
                    &middot; diperbarui <span id="updatedAt">{{ $updatedAt }}</span>
                </p>
            </div>
            <a href="{{ route('patroli.monitoring.riwayat') }}" class="btn patroli-btn-ghost">
                <i class="bi bi-clock-history me-1"></i> Riwayat Semua Shift
            </a>
        </div>

        @if (session('success'))
            <div class="patroli-alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- ===== Kartu ringkasan ===== --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="patroli-card h-100">
                    <div class="card-body patroli-stat">
                        <div class="patroli-stat-icon blue"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <div class="patroli-stat-value" id="statPetugasAktif">{{ $petugasAktif }}</div>
                            <div class="patroli-stat-label">Petugas Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="patroli-card h-100">
                    <div class="card-body patroli-stat">
                        <div class="patroli-stat-icon slate"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="patroli-stat-value">{{ $totalCheckpointAktif }}</div>
                            <div class="patroli-stat-label">Titik Checkpoint</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="patroli-card h-100">
                    <div class="card-body patroli-stat">
                        <div class="patroli-stat-icon amber"><i class="bi bi-exclamation-triangle"></i></div>
                        <div>
                            <div class="patroli-stat-value" id="statTotalTemuan">{{ $totalTemuanHariIni }}</div>
                            <div class="patroli-stat-label">Temuan Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="patroli-card h-100">
                    <div class="card-body patroli-stat">
                        <div class="patroli-stat-icon green"><i class="bi bi-list-check"></i></div>
                        <div>
                            <div class="patroli-stat-value">{{ $sesiHariIni->count() }}</div>
                            <div class="patroli-stat-label">Shift Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- ===== Daftar shift hari ini ===== --}}
            <div class="col-lg-7">
                <h6 class="patroli-title mb-3" style="font-size: 1.05rem;">Shift Hari Ini</h6>
                <div id="sesiList" class="vstack gap-2">
                    @forelse ($sesiHariIni as $s)
                        @php $pct = $s->total_checkpoint > 0 ? round(($s->scans->count() / $s->total_checkpoint) * 100) : 0; @endphp
                        <div class="patroli-card patroli-card-link">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $s->user->username ?? '-' }}
                                            @if ($s->terlambat)
                                                <span class="patroli-pill terlambat ms-1">Terlambat</span>
                                            @endif
                                        </div>
                                        <div class="patroli-subtitle">
                                            {{ optional($s->mulai_at)->format('H:i') }}
                                            &ndash;
                                            {{ optional($s->selesai_at)->format('H:i') ?? 'berjalan' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if ($s->status === 'berjalan')
                                            <span class="patroli-pill berjalan">Berjalan</span>
                                        @else
                                            <span class="patroli-pill selesai">Selesai</span>
                                        @endif
                                        <div class="patroli-subtitle mt-1">{{ $s->scans->count() }}/{{ $s->total_checkpoint }} titik</div>
                                    </div>
                                </div>
                                <div class="patroli-progress mt-3">
                                    <div class="bar" style="width: {{ $pct }}%"></div>
                                </div>
                                <a href="{{ route('patroli.monitoring.show', $s->id) }}" class="stretched-link"></a>
                            </div>
                        </div>
                    @empty
                        <div class="patroli-empty">
                            <i class="bi bi-calendar-x d-block mb-2"></i>
                            Belum ada shift patroli hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ===== Temuan terbaru ===== --}}
            <div class="col-lg-5">
                <h6 class="patroli-title mb-3" style="font-size: 1.05rem;">Temuan Terbaru</h6>
                <div id="temuanList" class="vstack gap-2">
                    @forelse ($temuanTerbaru as $t)
                        <div class="patroli-card patroli-card-link" style="border-left: 4px solid {{ $t->status === 'bahaya' ? '#ef4444' : '#f59e0b' }};">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <span class="patroli-pill {{ $t->status === 'bahaya' ? 'bahaya' : 'temuan' }}">
                                        <i class="bi bi-exclamation-triangle"></i> {{ ucfirst($t->status) }}
                                    </span>
                                    @if ($t->ditangani_at)
                                        <span class="patroli-pill ditangani">Ditangani</span>
                                    @endif
                                </div>
                                <div class="fw-semibold mt-2">{{ $t->checkpoint->nama_titik ?? '-' }}</div>
                                <div class="patroli-subtitle">
                                    {{ $t->session->user->username ?? '-' }} &middot; {{ $t->scanned_at?->format('H:i') }}
                                </div>
                                @if ($t->catatan)
                                    <div class="small mt-1">{{ \Illuminate\Support\Str::limit($t->catatan, 90) }}</div>
                                @endif
                                <a href="{{ route('patroli.monitoring.show', $t->patrol_session_id) }}" class="stretched-link"></a>
                            </div>
                        </div>
                    @empty
                        <div class="patroli-empty">
                            <i class="bi bi-emoji-smile d-block mb-2"></i>
                            Belum ada temuan hari ini. 👍
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Polling ringan setiap 15 detik supaya dashboard terasa real-time
    async function refreshMonitoring() {
        try {
            const res = await fetch(@json(route('patroli.monitoring.data')), {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            document.getElementById('updatedAt').textContent = data.updatedAt;
            document.getElementById('statPetugasAktif').textContent = data.petugasAktif;
            document.getElementById('statTotalTemuan').textContent = data.totalTemuanHariIni;
        } catch (e) {
            // diam saja kalau gagal, coba lagi di siklus berikutnya
        }
    }
    // Angka ringkasan di-update tiap 15 detik, daftar shift & temuan disegarkan penuh tiap 60 detik
    setInterval(refreshMonitoring, 15000);
    setInterval(() => window.location.reload(), 60000);
</script>
@endsection
