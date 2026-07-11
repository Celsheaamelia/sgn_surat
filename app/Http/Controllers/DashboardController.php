<?php

namespace App\Http\Controllers;

use App\Models\RiwayatSurat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSurat  = RiwayatSurat::count();
        $suratHariIni = RiwayatSurat::whereDate('tanggal', Carbon::today())->count();
        $sudahUpload = RiwayatSurat::where('status', 'Terupload')->count();
        $belumUpload = RiwayatSurat::where('status', 'Belum Terupload')->count();

        $persenUpload = $totalSurat > 0
            ? round(($sudahUpload / $totalSurat) * 100, 1)
            : 0;

        // Tren: total surat bulan ini dibanding bulan lalu
        $totalBulanIni = RiwayatSurat::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $bulanLalu = now()->subMonth();
        $totalBulanLalu = RiwayatSurat::whereMonth('tanggal', $bulanLalu->month)
            ->whereYear('tanggal', $bulanLalu->year)
            ->count();

        $trendPersen = $totalBulanLalu > 0
            ? round((($totalBulanIni - $totalBulanLalu) / $totalBulanLalu) * 100)
            : ($totalBulanIni > 0 ? 100 : 0);

        // 4 surat terbaru buat panel "Riwayat Nomor Surat"
        $riwayatTerbaru = RiwayatSurat::latest()->take(4)->get();

        // Data awal buat grafik: default 7 hari terakhir.
        // Rentang custom lainnya diambil lewat AJAX ke endpoint chartRange().
        $defaultEnd = Carbon::today();
        $defaultStart = Carbon::today()->subDays(6);
        $chartData = $this->hitungRentang($defaultStart, $defaultEnd);

        return view('dashboard', compact(
            'totalSurat',
            'suratHariIni',
            'sudahUpload',
            'belumUpload',
            'persenUpload',
            'trendPersen',
            'riwayatTerbaru',
            'chartData',
            'defaultStart',
            'defaultEnd'
        ));
    }

    /**
     * Endpoint AJAX: hitung jumlah surat per hari untuk rentang tanggal
     * yang dipilih admin lewat date picker di dashboard.
     */
    public function chartRange(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->startOfDay();

        if ($start->diffInDays($end) > 366) {
            return response()->json([
                'message' => 'Rentang tanggal maksimal 1 tahun.',
            ], 422);
        }

        return response()->json($this->hitungRentang($start, $end));
    }

    /**
     * Hitung jumlah surat per hari, dari $start sampai $end (inklusif).
     * Dipakai baik untuk data awal (index) maupun endpoint AJAX (chartRange).
     */
    private function hitungRentang(Carbon $start, Carbon $end): array
    {
        $labels = [];
        $data = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            $labels[] = $current->translatedFormat('d M');
            $data[] = RiwayatSurat::whereDate('tanggal', $current->toDateString())->count();
            $current->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
