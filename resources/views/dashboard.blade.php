@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap');

    :root {
        --bg-soft: #f6f7fb;
        --ink: #1a1d29;
        --muted: #8a8fa3;
        --card-radius: 20px;
        --shadow-soft: 0 4px 24px rgba(30, 34, 60, 0.06);
        --shadow-hover: 0 10px 30px rgba(30, 34, 60, 0.10);

        /* Font tokens — matched with Riwayat Surat / Manajemen Surat */
        --font-display: 'Fraunces', Georgia, serif;
        --font-body: 'Inter', -apple-system, sans-serif;
        --font-mono: 'IBM Plex Mono', ui-monospace, monospace;
    }

    .content-wrap {
        background: var(--bg-soft);
        padding: 2rem 1.75rem;
        min-height: 100%;
        font-family: var(--font-body);
    }

    .page-head h3 {
        font-family: var(--font-display);
        letter-spacing: -0.02em;
        color: var(--ink);
        font-weight: 700;
    }

    .page-head p {
        font-family: var(--font-body);
        color: var(--muted);
        font-size: 0.95rem;
    }

    /* ---- Stat Cards ---- */
    .stat-card {
        border: none;
        border-radius: var(--card-radius);
        background: #fff;
        box-shadow: var(--shadow-soft);
        transition: transform .25s ease, box-shadow .25s ease;
        overflow: hidden;
        position: relative;
    }

    .stat-card::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity .25s ease;
        background: linear-gradient(135deg, rgba(255,255,255,.5), transparent 60%);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card:hover::before { opacity: 1; }

    .stat-label {
        font-family: var(--font-mono);
        color: var(--muted);
        font-size: .78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .stat-value {
        font-family: var(--font-display);
        color: var(--ink);
        font-weight: 700;
        font-size: 2.1rem;
        letter-spacing: -0.01em;
        margin-top: .35rem;
    }

    .icon-orb {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .icon-orb.blue    { background: linear-gradient(135deg,#4f6bff,#3546c9); color:#fff; }
    .icon-orb.green   { background: linear-gradient(135deg,#22c98e,#12a374); color:#fff; }
    .icon-orb.cyan    { background: linear-gradient(135deg,#3ec8ea,#1e9cc4); color:#fff; }
    .icon-orb.amber   { background: linear-gradient(135deg,#ffb84f,#f2932a); color:#fff; }

    .stat-trend {
        font-family: var(--font-mono);
        font-size: .76rem;
        font-weight: 600;
        margin-top: .4rem;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .stat-trend.up   { color: #12a374; }
    .stat-trend.warn { color: #f2932a; }

    /* ---- Chart Card ---- */
    .panel-card {
        border: none;
        border-radius: var(--card-radius);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .panel-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef0f6;
        padding: 1.25rem 1.5rem;
    }

    .panel-title {
        font-family: var(--font-display);
        font-weight: 600;
        color: var(--ink);
        letter-spacing: -0.01em;
        margin: 0;
    }

    .filter-pill {
        font-family: var(--font-mono);
        border-radius: 999px;
        border: 1px solid #e6e8f0;
        background: #f8f9fd;
        font-size: .8rem;
        font-weight: 600;
        color: var(--ink);
        padding: .45rem 1.1rem;
        width: auto;
        box-shadow: none;
    }

    .filter-pill:focus {
        border-color: #4f6bff;
        box-shadow: 0 0 0 3px rgba(79,107,255,.12);
    }

    .panel-card .card-body { padding: 1.5rem; }

    /* ---- Riwayat list ---- */
    .riwayat-item {
        display: flex;
        align-items: flex-start;
        gap: .9rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f2f7;
        transition: background .15s ease;
    }

    .riwayat-item:hover { background: #f8f9fd; }

    .riwayat-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        margin-top: .45rem;
        flex-shrink: 0;
        background: #4f6bff;
    }

    /* .riwayat-item:nth-child(2) .riwayat-dot { background: #22c98e; }
    .riwayat-item:nth-child(3) .riwayat-dot { background: #3ec8ea; }
    .riwayat-item:nth-child(4) .riwayat-dot { background: #ffb84f; } */

    .riwayat-code {
        font-family: var(--font-mono);
        font-weight: 600;
        color: var(--ink);
        font-size: .86rem;
        letter-spacing: 0.01em;
        display: block;
    }

    .riwayat-date {
        font-family: var(--font-mono);
        color: var(--muted);
        font-size: .74rem;
    }

    .riwayat-footer {
        padding: 1rem 1.5rem;
        text-align: center;
    }

    .riwayat-footer a {
        font-family: var(--font-mono);
        color: #4f6bff;
        font-weight: 700;
        font-size: .85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        transition: gap .2s ease;
    }

    .riwayat-footer a:hover { gap: .55rem; }

    @media (max-width: 767px) {
        .content-wrap { padding: 1.25rem .9rem; }
        .stat-value { font-size: 1.7rem; }
    }
</style>

<div class="content-wrap">

    <!-- Header -->
    <div class="page-head mb-4">
        <h3>Dashboard</h3>
        <p class="mb-0">Ringkasan penggunaan nomor surat</p>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Nomor Surat</div>
                        <div class="stat-value">254</div>
                        <div class="stat-trend up">
                            <i class="bi bi-arrow-up-short"></i> 12% bulan ini
                        </div>
                    </div>
                    <div class="icon-orb blue">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Surat Hari Ini</div>
                        <div class="stat-value">8</div>
                        <div class="stat-trend up">
                            <i class="bi bi-arrow-up-short"></i> Aktif
                        </div>
                    </div>
                    <div class="icon-orb green">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Sudah Upload</div>
                        <div class="stat-value">180</div>
                        <div class="stat-trend up">
                            <i class="bi bi-check2-circle"></i> 70.8%
                        </div>
                    </div>
                    <div class="icon-orb cyan">
                        <i class="bi bi-cloud-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Belum Upload</div>
                        <div class="stat-value">12</div>
                        <div class="stat-trend warn">
                            <i class="bi bi-exclamation-circle"></i> Perlu tindak lanjut
                        </div>
                    </div>
                    <div class="icon-orb amber">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Grafik & Riwayat -->
    <div class="row g-4">

        <!-- Grafik -->
        <div class="col-lg-8">
            <div class="card panel-card h-100">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="panel-title">Grafik Penggunaan Nomor Surat</h5>

                    <select class="form-select filter-pill" id="filterChart">
                        <option value="7">7 Hari</option>
                        <option value="30">1 Bulan</option>
                        <option value="365">1 Tahun</option>
                    </select>
                </div>

                <div class="card-body">
                    <canvas id="suratChart" height="300"></canvas>
                </div>

            </div>
        </div>

        <!-- Riwayat -->
        <div class="col-lg-4">
            <div class="card panel-card h-100">

                <div class="card-header">
                    <h5 class="panel-title">Riwayat Nomor Surat</h5>
                </div>

                <div class="card-body p-0">

                    <div class="riwayat-item">
                        <span class="riwayat-dot"></span>
                        <div>
                            <span class="riwayat-code">001/SP/HRD/VII/2026</span>
                            <span class="riwayat-date">07 Juli 2026</span>
                        </div>
                    </div>

                    <div class="riwayat-item">
                        <span class="riwayat-dot"></span>
                        <div>
                            <span class="riwayat-code">002/SP/KEU/VII/2026</span>
                            <span class="riwayat-date">07 Juli 2026</span>
                        </div>
                    </div>

                    <div class="riwayat-item">
                        <span class="riwayat-dot"></span>
                        <div>
                            <span class="riwayat-code">003/SP/PROD/VII/2026</span>
                            <span class="riwayat-date">06 Juli 2026</span>
                        </div>
                    </div>

                    <div class="riwayat-item">
                        <span class="riwayat-dot"></span>
                        <div>
                            <span class="riwayat-code">004/SP/GA/VII/2026</span>
                            <span class="riwayat-date">05 Juli 2026</span>
                        </div>
                    </div>

                    <div class="riwayat-footer">
                        <a href="#">Lihat Semua <i class="bi bi-arrow-right"></i></a>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const dataChart = {
    7: {
        labels: ['Sen','Sel','Rab','Kam','Jum','Sab','Min'],
        data: [5,8,4,10,7,15,12]
    },
    30: {
        labels: ['Minggu 1','Minggu 2','Minggu 3','Minggu 4'],
        data: [30,45,38,52]
    },
    365: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
        data: [12,20,18,30,25,22,35,28,26,31,29,40]
    }
};

const ctx = document.getElementById('suratChart');

const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(79,107,255,.28)');
gradient.addColorStop(1, 'rgba(79,107,255,0)');

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dataChart[7].labels,
        datasets: [{
            label: 'Nomor Surat',
            data: dataChart[7].data,
            borderColor: '#4f6bff',
            backgroundColor: gradient,
            fill: true,
            tension: 0.45,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#4f6bff',
            pointBorderWidth: 2,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },

        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1a1d29',
                titleFont: { weight: '600', family: "'Inter', sans-serif" },
                bodyFont: { family: "'IBM Plex Mono', monospace" },
                padding: 10,
                cornerRadius: 10,
                displayColors: false
            }
        },

        scales: {
            x: {
                grid: { display: false },
                border: { display: false },
                ticks: { color: '#8a8fa3', font: { size: 12, family: "'IBM Plex Mono', monospace" } }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#8a8fa3', font: { size: 12, family: "'IBM Plex Mono', monospace" } },
                grid: { color: '#eef0f6' },
                border: { display: false }
            }
        }
    }
});

document.getElementById('filterChart').addEventListener('change', function () {
    const value = this.value;
    chart.data.labels = dataChart[value].labels;
    chart.data.datasets[0].data = dataChart[value].data;
    chart.update();
});

</script>

@endsection