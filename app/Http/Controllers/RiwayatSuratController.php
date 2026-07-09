<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatSurat;
use App\Models\Penandatangan;
use App\Models\TujuanSurat;
use App\Models\KlasifikasiSurat;
use Illuminate\Support\Facades\Auth;

class RiwayatSuratController extends Controller
{
    public function index()
    {
        $suratList = RiwayatSurat::with([
            'penandatangan',
            'tujuanSurat',
            'klasifikasiSurat',
            'user'
        ])
        ->latest()
        ->get();

        $klasifikasiList = KlasifikasiSurat::orderBy('jenis_surat')->get();

        return view('riwayatsurat', compact(
            'suratList',
            'klasifikasiList'
        ));
    }

    public function create()
    {
        $penandatanganList = Penandatangan::orderBy('jabatan')->get();
        $tujuanList = TujuanSurat::orderBy('nama_tujuan')->get();
        $klasifikasiList = KlasifikasiSurat::orderBy('jenis_surat')->get();

        $nextSequence = RiwayatSurat::count() + 1;

        // Ambil semua riwayat surat
        $suratList = RiwayatSurat::latest()->get();

        return view('tambahsurat', compact(
            'penandatanganList',
            'tujuanList',
            'klasifikasiList',
            'nextSequence',
            'suratList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'perihal' => 'required',
            'signatory' => 'required',
            'kode_tujuan' => 'required',
            'klasifikasi' => 'required',
            'tanggal' => 'required|date',
        ]);

        $urut = str_pad(RiwayatSurat::count() + 1, 3, '0', STR_PAD_LEFT);

        $nomor = $urut . '/' .
                 $request->signatory . '/' .
                 $request->kode_tujuan . '/' .
                 $request->klasifikasi . '/' .
                 date('Y');

        RiwayatSurat::create([
            'nomor_surat' => $nomor,
            'perihal' => $request->perihal,
            'tanggal' => $request->tanggal,
            'penandatangan_id' => $request->signatory,
            'tujuan_surat_id' => $request->kode_tujuan,
            'klasifikasi_surat_id' => $request->klasifikasi,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('riwayatsurat')
            ->with('success', 'Surat berhasil dibuat.');
    }
}
