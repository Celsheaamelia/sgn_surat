<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PatrolSchedule;
use App\Models\PatrolScan;
use App\Models\PatrolSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan otomatis: Harian / Mingguan / Bulanan.
     * Berisi grafik kepatuhan patroli (jadwal vs realisasi), statistik kejadian, dan rekap shift.
     */
    public function index(Request $request)
    {
        $jenis  = in_array($request->jenis, ['harian', 'mingguan', 'bulanan']) ? $request->jenis : 'mingguan';
        $anchor = $request->tanggal ? Carbon::parse($request->tanggal) : now();

        [$mulai, $selesai] = match ($jenis) {
            'harian'  => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
            'bulanan' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default   => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
        };

        $sesiPeriode = PatrolSession::with(['user', 'scans'])
            ->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->orderBy('tanggal')
            ->get();

        $scanPeriode = PatrolScan::whereHas('session', function ($q) use ($mulai, $selesai) {
            $q->whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()]);
        })->get();

        $jadwalPeriode = PatrolSchedule::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])->get();

        // ===== Grafik kepatuhan harian: % petugas terjadwal yang benar-benar mulai shift =====
        $labelHarian   = [];
        $dataKepatuhan = [];
        $cursor        = $mulai->copy();

        while ($cursor->lte($selesai)) {
            $tgl = $cursor->toDateString();
            $labelHarian[] = $cursor->translatedFormat('d M');

            $dijadwalkan = $jadwalPeriode->where('tanggal', $tgl)->pluck('user_id')->unique();
            $realisasi   = $sesiPeriode->where('tanggal', $tgl)->pluck('user_id')->unique();

            $dataKepatuhan[] = $dijadwalkan->isEmpty()
                ? null // tidak ada jadwal hari itu, tidak dihitung ke grafik
                : round($dijadwalkan->intersect($realisasi)->count() / $dijadwalkan->count() * 100);

            $cursor->addDay();
        }

        $kepatuhanValid = array_filter($dataKepatuhan, fn ($v) => $v !== null);
        $rataKepatuhan  = count($kepatuhanValid) > 0 ? round(array_sum($kepatuhanValid) / count($kepatuhanValid)) : null;

        // ===== Statistik kejadian =====
        $totalAman   = $scanPeriode->where('status', 'aman')->count();
        $totalTemuan = $scanPeriode->where('status', 'temuan')->count();
        $totalBahaya = $scanPeriode->where('status', 'bahaya')->count();

        // ===== Rekap shift =====
        $rekapShift = $sesiPeriode->map(function (PatrolSession $s) {
            return [
                'tanggal'    => Carbon::parse($s->tanggal)->translatedFormat('d M Y'),
                'petugas'    => $s->user->username ?? '-',
                'mulai'      => optional($s->mulai_at)->format('H:i'),
                'selesai'    => optional($s->selesai_at)->format('H:i') ?: '-',
                'status'     => $s->status,
                'terlambat'  => $s->terlambat,
                'checkpoint' => $s->scans->count() . '/' . $s->total_checkpoint,
                'temuan'     => $s->scans->whereIn('status', ['temuan', 'bahaya'])->count(),
            ];
        });

        return view('patroli.laporan.index', [
            'jenis'         => $jenis,
            'anchor'        => $anchor,
            'mulai'         => $mulai,
            'selesai'       => $selesai,
            'totalShift'    => $sesiPeriode->count(),
            'totalAman'     => $totalAman,
            'totalTemuan'   => $totalTemuan,
            'totalBahaya'   => $totalBahaya,
            'rataKepatuhan' => $rataKepatuhan,
            'labelHarian'   => $labelHarian,
            'dataKepatuhan' => $dataKepatuhan,
            'rekapShift'    => $rekapShift,
        ]);
    }
}
