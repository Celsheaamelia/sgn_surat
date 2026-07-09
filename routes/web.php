<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RiwayatSuratController;


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

// Route::middleware('auth')->group(function () {

//     Route::get('/dashboard', function () {
//         $suratList = bacaSurat();
//         return view('dashboard', compact('suratList'));
//     })->name('dashboard');

    // Tampilkan form tambah surat
    Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/surat/tambah', [RiwayatSuratController::class,'create'])
        ->name('tambahsurat');

    Route::post('/surat/tambah', [RiwayatSuratController::class,'store'])
        ->name('surat.store');

    Route::get('/riwayat-surat', [RiwayatSuratController::class,'index'])
        ->name('riwayatsurat');

    Route::post('/logout', [LoginController::class,'logout'])
        ->name('logout');
});

//     Route::get('/riwayat-surat', function () {
//         return view('riwayatsurat');
//     })->name('riwayatsurat');

//     Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// });
