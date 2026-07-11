<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatSurat;
use App\Models\Penandatangan;
use App\Models\TujuanSurat;
use App\Models\KlasifikasiSurat;
use App\Models\DetailSurat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RiwayatSuratController extends Controller
{
    public function index()
    {
        $suratList = RiwayatSurat::with([
            'penandatangan',
            'tujuanSurat',
            'klasifikasiSurat',
            'detailSurat'
        ])->latest()->get();

        $klasifikasiList = KlasifikasiSurat::orderBy('kode')->get();

        return view('riwayatsurat', compact(
            'suratList',
            'klasifikasiList'
        ));
        }

    public function show(RiwayatSurat $riwayatSurat)
    {
        $riwayatSurat->load(['penandatangan', 'tujuanSurat', 'klasifikasiSurat', 'user']);

        return view('surat.detail', [
            'surat' => $riwayatSurat,
        ]);
    }

    public function create()
    {
        $penandatanganList = Penandatangan::orderBy('jabatan')->get();
        $tujuanList = TujuanSurat::orderBy('nama_tujuan')->get();
        $klasifikasiList = KlasifikasiSurat::orderBy('jenis_surat')->get();

        // $today = now()->toDateString();
        $tanggal = now()->toDateString();
        $nextSequence = RiwayatSurat::whereDate('tanggal', $tanggal)->count() + 1;

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

        $jumlahHariIni = RiwayatSurat::whereDate('tanggal', $request->tanggal)->count();
        $urut = str_pad($jumlahHariIni + 1, 3, '0', STR_PAD_LEFT);

        // Ambil data berdasarkan ID yang dipilih di form
        $penandatangan = Penandatangan::find($request->signatory);
        $tujuan = TujuanSurat::find($request->kode_tujuan);
        $klasifikasi = KlasifikasiSurat::find($request->klasifikasi);

        // Susun nomor surat menggunakan KODE, bukan ID
        $nomor = $urut . '/' .
                $klasifikasi->kode . '/' .
                $penandatangan->kode . '/' .
                $tujuan->kode . '/' .
                date('Y', strtotime($request->tanggal));

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

public function showUpload($id)
{
    return redirect()->back();
}

public function storeUpload(Request $request, $id)
{
    return response()->json(['message' => 'Not implemented'], 501);
}

public function deleteUpload($id)
{
    return response()->json(['message' => 'Not implemented'], 501);
}

public function getNextSequence(Request $request)
{
    $tanggal = $request->input('tanggal', now()->toDateString());
    $count = RiwayatSurat::whereDate('tanggal', $tanggal)->count();
    $next = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    return response()->json(['nextSequence' => $next]);
}
}
