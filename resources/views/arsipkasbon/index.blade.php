@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    :root {
        --ink: #1c2b23; --ink-soft: #3d4f45; --ink-faint: #7c8a80; --paper: #fbfcf9;
        --brass: #a9812f; --brass-dark: #8a6a24; --brass-bg: #fbf6ea; --line: #dfe6da;
        --success: #3f6b4a; --success-bg: #eef5ef;
        --font-display: 'Fraunces', Georgia, serif;
        --font-body: 'Inter', -apple-system, sans-serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, monospace;
    }

    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.75rem; }
    .page-title { font-family: var(--font-display); font-weight: 700; font-size: 1.75rem; color: var(--ink); }
    .page-subtitle { color: var(--ink-soft); font-size: 0.92rem; margin-top: 0.2rem; }

    .ledger-btn-brass {
        background: linear-gradient(180deg, #b8903f, var(--brass-dark));
        box-shadow: 0 4px 14px rgba(138,106,36,0.35);
        border: none; color: #fff; font-weight: 600;
        padding: 0.7rem 1.4rem; border-radius: 0.65rem; text-decoration: none;
        display: inline-flex; align-items: center; font-size: 0.92rem;
        transition: transform 0.15s ease;
    }
    .ledger-btn-brass:hover { color: #fff; transform: translateY(-1px); }

    .ledger-card { background: var(--paper); border: 1px solid var(--line); border-radius: 1rem; box-shadow: 0 1px 2px rgba(28,43,35,0.05), 0 1px 12px rgba(28,43,35,0.04); }

    /* ---- Search hero ---- */
    .search-hero { padding: 1.5rem 1.75rem; }
    .search-hero-label { font-family: var(--font-mono); font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-faint); margin-bottom: 0.6rem; }
    .search-hero-box { position: relative; }
    .search-hero-box input {
        width: 100%; padding: 0.95rem 1.1rem 0.95rem 3.1rem; border: 1.5px solid var(--line);
        border-radius: 0.8rem; font-size: 1rem; color: var(--ink); background: #fff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .search-hero-box input:focus {
        outline: none; border-color: var(--brass); box-shadow: 0 0 0 4px rgba(169,129,47,0.12);
    }
    .search-hero-box .search-icon {
        position: absolute; left: 1.1rem; top: 50%; transform: translateY(-50%);
        color: var(--ink-faint); font-size: 1rem; pointer-events: none;
    }
    .search-hero-box .clear-btn {
        position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%);
        border: none; background: var(--line); color: var(--ink-soft);
        width: 30px; height: 30px; border-radius: 50%; display: grid; place-items: center;
        text-decoration: none; font-size: 0.85rem;
    }
    .search-hint { font-size: 0.78rem; color: var(--ink-faint); margin-top: 0.55rem; }
    .search-hint strong { color: var(--ink-soft); }

    /* ---- Stats strip ---- */
    .stats-strip { display: flex; gap: 1rem; padding: 0 1.75rem 1.5rem; flex-wrap: wrap; }
    .stat-chip {
        background: #fff; border: 1px solid var(--line); border-radius: 0.7rem;
        padding: 0.7rem 1.1rem; display: flex; align-items: center; gap: 0.6rem;
        font-size: 0.85rem; color: var(--ink-soft);
    }
    .stat-chip strong { color: var(--ink); font-family: var(--font-mono); font-size: 0.95rem; }
    .stat-chip i { color: var(--brass-dark); }

    /* ---- Table ---- */
    table.kasbon-table { margin: 0; }
    table.kasbon-table thead th {
        font-family: var(--font-mono); font-size: 0.68rem; letter-spacing: 0.06em;
        text-transform: uppercase; color: var(--ink-faint); border-bottom: 1px solid var(--line);
        padding: 0.9rem 1rem; background: #f7f8f5;
    }
    table.kasbon-table tbody td { vertical-align: middle; font-size: 0.9rem; padding: 0.95rem 1rem; border-bottom: 1px solid var(--line); }
    table.kasbon-table tbody tr:last-child td { border-bottom: none; }
    table.kasbon-table tbody tr { transition: background 0.12s ease; }
    table.kasbon-table tbody tr:hover { background: #f9f7f0; }

    .vendor-cell { display: flex; align-items: center; gap: 0.7rem; }
    .vendor-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        display: grid; place-items: center; font-family: var(--font-display); font-weight: 700;
        background: linear-gradient(135deg, #d9c68f, var(--brass-dark)); color: #fff; font-size: 0.85rem;
    }
    .vendor-name { font-weight: 600; color: var(--ink); }
    .vendor-doc { font-family: var(--font-mono); font-size: 0.76rem; color: var(--ink-faint); }

    .amount-val { font-family: var(--font-mono); font-weight: 700; color: var(--ink); }
    .akun-badge {
        font-family: var(--font-mono); font-size: 0.72rem; font-weight: 600;
        background: var(--brass-bg); color: var(--brass-dark); border: 1px solid #ecdfb8;
        padding: 0.25rem 0.55rem; border-radius: 6px; margin-right: 0.25rem; display: inline-block;
    }
    .akun-more { font-size: 0.76rem; color: var(--ink-faint); }

    .action-cell { display: flex; justify-content: flex-end; }
    .action-btn {
        width: 36px; height: 36px; border-radius: 0.55rem; display: inline-flex;
        align-items: center; justify-content: center; line-height: 1;
        border: 1px solid var(--line); color: var(--ink-soft); background: #fff; text-decoration: none;
        transition: all 0.15s ease; font-size: 0.95rem;
    }
    .action-btn:hover { background: var(--brass-dark); border-color: var(--brass-dark); color: #fff; }

    .empty-state { text-align: center; padding: 3.5rem 1.5rem; color: var(--ink-soft); }
    .empty-state i { font-size: 2rem; color: var(--line); margin-bottom: 0.8rem; display: block; }
    .empty-state strong { color: var(--ink); }

    /* ---- Cari No Akun (utility, dipindah jadi accordion ringkas) ---- */
    .akun-utility summary {
        cursor: pointer; list-style: none; display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.5rem; font-weight: 600; color: var(--ink); font-size: 0.9rem;
    }
    .akun-utility summary::-webkit-details-marker { display: none; }
    .akun-utility summary .chev { transition: transform 0.15s ease; color: var(--ink-faint); }
    .akun-utility[open] summary .chev { transform: rotate(180deg); }
    .akun-utility-body { padding: 0 1.5rem 1.25rem; }
    .akun-result-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.6rem 0; border-bottom: 1px solid var(--line);
        font-family: var(--font-mono); font-size: 0.82rem;
    }
    .akun-result-row:last-child { border-bottom: none; }
    .akun-code { font-weight: 700; color: var(--brass-dark); }
    .akun-desc { color: var(--ink); font-family: var(--font-body); text-align: right; }
</style>

<div class="container-fluid">

    <div class="page-header">
        <div>
            <div class="page-title">Riwayat Arsip SPP</div>
            <div class="page-subtitle">Semua Surat Permintaan Pembayaran (SPP) yang sudah diarsipkan.</div>
        </div>
        <a href="{{ route('arsipkasbon.create') }}" class="ledger-btn-brass">
            <i class="bi bi-camera-fill me-2"></i> Upload Surat Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    {{-- Search hero + daftar --}}
    <div class="ledger-card mb-4">
        <form method="GET" action="{{ route('arsipkasbon.index') }}" class="search-hero">
            <div class="search-hero-label">Cari Arsip</div>
            <div class="search-hero-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama vendor, no dokumen, no akun, tanggal, cek/giro, deskripsi, terbilang...">
                @if(request()->filled('q'))
                    <a href="{{ route('arsipkasbon.index') }}" class="clear-btn" title="Reset pencarian">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
            <div class="search-hint">
                Pencarian mencakup <strong>semua informasi surat</strong> — header (vendor, dokumen, tanggal, cek/giro, cost object, terbilang) maupun rincian akun di dalamnya.
            </div>
        </form>

        <div class="stats-strip">
            <div class="stat-chip"><i class="bi bi-folder2-open"></i> <strong>{{ $arsipList->total() }}</strong> surat terarsip</div>
            @if(request()->filled('q'))
                <div class="stat-chip"><i class="bi bi-funnel-fill"></i> menampilkan hasil untuk "<strong>{{ request('q') }}</strong>"</div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table kasbon-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Vendor &amp; Dokumen</th>
                        <th>Jumlah</th>
                        <th>Rincian Akun</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arsipList as $kasbon)
                        <tr>
                            <td style="white-space:nowrap;">{{ optional($kasbon->tanggal_transaksi)->format('d M Y') ?? '-' }}</td>
                            <td>
                                <div class="vendor-cell">
                                    <div class="vendor-avatar">{{ strtoupper(substr($kasbon->nama_vendor ?? '?', 0, 1)) }}</div>
                                    <div>
                                        <div class="vendor-name">{{ $kasbon->nama_vendor ?? 'Vendor tidak diketahui' }}</div>
                                        <div class="vendor-doc">{{ $kasbon->document_no ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="amount-val">{{ $kasbon->jumlah_total ? 'Rp ' . number_format($kasbon->jumlah_total, 0, ',', '.') : '-' }}</span>
                            </td>
                            <td>
                                @forelse($kasbon->items->take(2) as $item)
                                    <span class="akun-badge">{{ $item->no_akun }}</span>
                                @empty
                                    <span class="ledger-subtitle">-</span>
                                @endforelse
                                @if($kasbon->items->count() > 2)
                                    <span class="akun-more">+{{ $kasbon->items->count() - 2 }} lagi</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="action-cell">
                                    <a href="{{ route('arsipkasbon.show', $kasbon) }}" class="action-btn" title="Lihat detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    @if(request()->filled('q'))
                                        <i class="bi bi-search"></i>
                                        Tidak ada surat yang cocok dengan <strong>"{{ request('q') }}"</strong>.
                                        <div class="mt-1" style="font-size:0.85rem;">Coba kata kunci lain, atau <a href="{{ route('arsipkasbon.index') }}">reset pencarian</a>.</div>
                                    @else
                                        <i class="bi bi-inbox"></i>
                                        Belum ada SPP yang diarsipkan.
                                        <div class="mt-1" style="font-size:0.85rem;">Mulai dengan <a href="{{ route('arsipkasbon.create') }}">Unggah Surat Baru</a>.</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($arsipList->hasPages())
            <div class="card-body">
                {{ $arsipList->links() }}
            </div>
        @endif
    </div>

</div>

@endsection