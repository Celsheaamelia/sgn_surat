<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    return view('welcome');
});


$hardcodedUsers = [
    'admin@example.com' => 'password123',
    'user@example.com'  => 'user123',
];

// Tampilkan halaman login
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/dashboard', function () {

    if (!session('logged_in')) {
        return redirect('/login');
    }

    return view('dashboard');

})->name('dashboard');

// Proses login
Route::post('/login', function (Request $request) use ($hardcodedUsers) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    $email    = $credentials['email'];
    $password = $credentials['password'];

    if (isset($hardcodedUsers[$email]) && $hardcodedUsers[$email] === $password) {
        $request->session()->put('logged_in', true);
        $request->session()->put('user_email', $email);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password yang Anda masukkan salah.',
    ])->onlyInput('email');
});

// Halaman setelah login berhasil
Route::get('/dashboard', function () {

    if (!session('logged_in')) {
        return redirect('/login');
    }

    $suratList = bacaSurat();

    return view('dashboard', compact('suratList'));

})->name('dashboard');

// Logout
Route::post('/logout', function (Request $request) {
    $request->session()->forget(['logged_in', 'user_email']);
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ==================================================
// FITUR: TAMBAH NOMOR SURAT (tanpa database, pakai JSON file)
// ==================================================

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

// Tampilkan form tambah surat
Route::get('/surat/tambah', function () {
    if (!session('logged_in')) {
        return redirect('/login');
    }

    $suratList     = bacaSurat();
    $nextSequence  = count($suratList) + 1;

    return view('tambahsurat', compact('suratList', 'nextSequence'));
})->name('tambahsurat');

// Proses simpan nomor surat baru
Route::post('/surat/tambah', function (Request $request) {
    if (!session('logged_in')) {
        return redirect('/login');
    }

    $validated = $request->validate([
        'perihal'     => ['required', 'string', 'max:255'],
        'departemen'  => ['required', 'string'],
        'signatory'   => ['required', 'string'],
        'tanggal'     => ['required', 'date'],
        'kode_tujuan' => ['required', 'string'],
    ]);

    $suratList    = bacaSurat();
    $nextSequence = count($suratList) + 1;
    $urut         = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

    $tanggal = \Carbon\Carbon::parse($validated['tanggal']);

    $nomorSurat = sprintf(
        '%s/%s/%s/%s/%s/%s',
        $urut,
        $validated['departemen'],
        $validated['signatory'],
        $tanggal->format('Y'),
        $tanggal->format('m'),
        $tanggal->format('d')
    );

    $suratList[] = [
        'nomor'       => $nomorSurat,
        'perihal'     => $validated['perihal'],
        'departemen'  => $validated['departemen'],
        'signatory'   => $validated['signatory'],
        'kode_tujuan' => $validated['kode_tujuan'],
        'tanggal'     => $tanggal->format('d-m-Y'),
        'dibuat_oleh' => session('user_email'),
        'dibuat_pada' => now()->toDateTimeString(),
    ];

    simpanSurat($suratList);

    return redirect()->route('tambahsurat')->with('success', "Nomor surat berhasil dibuat: {$nomorSurat}");
})->name('surat.store');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

