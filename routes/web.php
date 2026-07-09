<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;


Route::get('/', function () {
    return redirect()->route('login');
});

// Login
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class,'index'])->name('login');
    Route::post('/login', [LoginController::class,'login']);

});

// Helper: baca data surat dari file JSON
function bacaSurat(): array
{
    if (!Storage::exists('surat.json')) {
        return [];
    }
    return json_decode(Storage::get('surat.json'), true) ?? [];
}

// Helper: simpan data surat ke file JSON
function simpanSurat(array $data): void
{
    Storage::put('surat.json', json_encode($data, JSON_PRETTY_PRINT));
}

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $suratList = bacaSurat();
        return view('dashboard', compact('suratList'));
    })->name('dashboard');

    // Tampilkan form tambah surat
    Route::get('/surat/tambah', function () {

        $suratList = bacaSurat();
        $nextSequence = count($suratList) + 1;

        // Data referensi dari database, dipakai untuk isi dropdown di form
        $klasifikasiList   = DB::table('klasifikasi_surat')->orderBy('jenis_surat')->get();
        $penandatanganList = DB::table('penandatangan')->orderBy('jabatan')->get();
        $tujuanList        = DB::table('tujuan_surats')->orderBy('nama_tujuan')->get();

        return view('tambahsurat', compact(
            'suratList',
            'nextSequence',
            'klasifikasiList',
            'penandatanganList',
            'tujuanList'
        ));

    })->name('tambahsurat');

    // Proses simpan nomor surat baru
    Route::post('/surat/tambah', function (Request $request) {

        // isi kode simpan surat

    })->name('surat.store');

    Route::get('/riwayat-surat', function () {
        return view('riwayatsurat');
    })->name('riwayatsurat');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
