<?php

namespace App\Http\Controllers;

use App\Models\ArsipKasbon;
use App\Models\ArsipKasbonItem;
use App\Models\MasterAkun;
use App\Services\KasbonOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArsipKasbonController extends Controller
{
    /**
     * Daftar arsip kasbon.
     */
    public function index(Request $request)
    {
        $query = ArsipKasbon::with('items')->latest();

        if ($request->filled('q')) {
            $keyword = trim($request->q);

            $query->where(function ($sub) use ($keyword) {
                // Cari di semua field header surat
                $sub->where('document_no', 'like', "%{$keyword}%")
                    ->orWhere('nama_vendor', 'like', "%{$keyword}%")
                    ->orWhere('kode_vendor', 'like', "%{$keyword}%")
                    ->orWhere('park_oleh', 'like', "%{$keyword}%")
                    ->orWhere('cek_giro_trx', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi_cost_object', 'like', "%{$keyword}%")
                    ->orWhere('terbilang', 'like', "%{$keyword}%")
                    ->orWhere('file_scan_name', 'like', "%{$keyword}%")
                    // Cocokkan juga kalau keyword adalah angka nominal (jumlah_total)
                    ->orWhere('jumlah_total', 'like', "%{$keyword}%")
                    // Cari juga di tanggal yang sudah diformat, misal "16 Juli 2026" atau "2026-07-16"
                    ->orWhereRaw("DATE_FORMAT(tanggal_transaksi, '%d-%m-%Y') LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("DATE_FORMAT(tanggal_transaksi, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"])
                    // Cari di rincian akun (baris item)
                    ->orWhereHas('items', function ($item) use ($keyword) {
                        $item->where('no_akun', 'like', "%{$keyword}%")
                            ->orWhere('pk', 'like', "%{$keyword}%")
                            ->orWhere('cost_object', 'like', "%{$keyword}%")
                            ->orWhere('item_text', 'like', "%{$keyword}%");
                    });
            });
        }

        $arsipList = $query->paginate(10)->withQueryString();

        return view('arsipkasbon.index', compact('arsipList'));
    }

    public function create()
    {
        return view('arsipkasbon.create');
    }

    /**
     * Endpoint AJAX dipanggil setelah admin pilih file di form upload.
     * Menjalankan OCR dan mengembalikan field-field tebakan sebagai JSON,
     * TIDAK menyimpan apapun ke database.
     */
    public function scan(Request $request, KasbonOcrService $ocr)
    {
        $request->validate([
            'file_scan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:15360',
        ]);

        // Simpan sementara supaya bisa diproses OCR
        $tempPath = $request->file('file_scan')->store('kasbon-temp', 'local');
        $fullPath = Storage::disk('local')->path($tempPath);

        $result = $ocr->scan($fullPath);

        // Lengkapi deskripsi tiap item dari master_akun kalau kodenya sudah dikenal
        foreach ($result['items'] as &$item) {
            $master = MasterAkun::where('no_akun', $item['no_akun'])->first();
            $item['deskripsi_akun'] = $master?->deskripsi;
        }

        // Cek apakah document_no hasil bacaan OCR sudah pernah diarsipkan sebelumnya.
        $duplicate = null;
        $documentNo = $result['header']['document_no'] ?? null;
        if (!empty($documentNo)) {
            $existing = ArsipKasbon::where('document_no', $documentNo)->first();
            if ($existing) {
                $duplicate = [
                    'document_no' => $documentNo,
                    'message'     => "Surat Permintaan Pembayaran dengan No Dokumen \"{$documentNo}\" sudah pernah diunggah sebelumnya.",
                    'existing_id' => $existing->id,
                ];
            }
        }

        return response()->json([
            'temp_path' => $tempPath,
            'header'    => $result['header'],
            'items'     => $result['items'],
            'duplicate' => $duplicate,
        ]);
    }

    /**
     * AJAX: dipanggil setiap admin selesai ketik/koreksi Document No secara manual
     * di form verifikasi, untuk cek duplikat secara real-time.
     */
    public function checkDocumentNo(Request $request)
    {
        $documentNo = trim((string) $request->query('document_no', ''));

        if ($documentNo === '') {
            return response()->json(['duplicate' => false]);
        }

        $existing = ArsipKasbon::where('document_no', $documentNo)->first();

        if (!$existing) {
            return response()->json(['duplicate' => false]);
        }

        return response()->json([
            'duplicate'   => true,
            'message'     => "Surat Permintaan Pembayaran dengan No Dokumen \"{$documentNo}\" sudah pernah diunggah sebelumnya.",
            'existing_id' => $existing->id,
        ]);
    }

    /**
     * Simpan final setelah admin verifikasi/koreksi hasil scan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi'      => 'nullable|date',
            'document_no'            => 'nullable|string|max:50|unique:arsip_kasbon,document_no',
            'park_oleh'               => 'nullable|string|max:100',
            'nama_vendor'             => 'nullable|string|max:150',
            'kode_vendor'             => 'nullable|string|max:50',
            'cek_giro_trx'            => 'nullable|string|max:100',
            'deskripsi_cost_object'   => 'nullable|string|max:150',
            'jumlah_total'            => 'nullable|numeric',
            'terbilang'               => 'nullable|string',
            'temp_path'               => 'nullable|string',
            'file_scan'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:15360',

            'items'                   => 'required|array|min:1',
            'items.*.no_akun'         => 'required|string|max:30',
            'items.*.pk'              => 'nullable|string|max:20',
            'items.*.cost_object'     => 'nullable|string|max:50',
            'items.*.item_text'       => 'nullable|string',
            'items.*.jumlah_rupiah'   => 'nullable|numeric',
            'items.*.deskripsi_akun'  => 'nullable|string',
        ], [
            'document_no.unique' => 'Surat Permintaan Pembayaran dengan No Dokumen ":input" sudah pernah diunggah sebelumnya.',
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Pindahkan file dari lokasi sementara (hasil scan) ke penyimpanan final,
            // atau pakai file baru kalau admin upload ulang di step ini.
            $finalPath = null;
            $finalName = null;

            if ($request->hasFile('file_scan')) {
                $finalPath = $request->file('file_scan')->store('kasbon', 'public');
                $finalName = $request->file('file_scan')->getClientOriginalName();
            } elseif ($request->filled('temp_path') && Storage::disk('local')->exists($request->temp_path)) {
                $finalName = basename($request->temp_path);
                $finalPath = 'kasbon/' . uniqid() . '_' . $finalName;
                Storage::disk('public')->put(
                    $finalPath,
                    Storage::disk('local')->get($request->temp_path)
                );
                Storage::disk('local')->delete($request->temp_path);
            }

            $kasbon = ArsipKasbon::create([
                'tanggal_transaksi'     => $validated['tanggal_transaksi'] ?? null,
                'document_no'           => $validated['document_no'] ?? null,
                'park_oleh'             => $validated['park_oleh'] ?? null,
                'nama_vendor'           => $validated['nama_vendor'] ?? null,
                'kode_vendor'           => $validated['kode_vendor'] ?? null,
                'cek_giro_trx'          => $validated['cek_giro_trx'] ?? null,
                'deskripsi_cost_object' => $validated['deskripsi_cost_object'] ?? null,
                'jumlah_total'          => $validated['jumlah_total'] ?? null,
                'terbilang'             => $validated['terbilang'] ?? null,
                'file_scan'             => $finalPath,
                'file_scan_name'        => $finalName,
                'status'                => 'arsip',
                'user_id'               => Auth::id(),
            ]);

            foreach ($validated['items'] as $item) {
                $kasbon->items()->create([
                    'no_akun'       => $item['no_akun'],
                    'pk'            => $item['pk'] ?? null,
                    'cost_object'   => $item['cost_object'] ?? null,
                    'item_text'     => $item['item_text'] ?? null,
                    'jumlah_rupiah' => $item['jumlah_rupiah'] ?? 0,
                ]);

                // Perkaya "kamus" No Akun -> Deskripsi otomatis, supaya surat
                // berikutnya dengan akun yang sama langsung ke-autofill.
                if (!empty($item['no_akun']) && !empty($item['deskripsi_akun'])) {
                    MasterAkun::updateOrCreate(
                        ['no_akun' => $item['no_akun']],
                        ['deskripsi' => $item['deskripsi_akun']]
                    );
                }
            }
        });

        return redirect()
            ->route('arsipkasbon.index')
            ->with('success', 'SPP berhasil diarsipkan.');
    }

    public function show(ArsipKasbon $arsipKasbon)
    {
        $arsipKasbon->load('items');
        return view('arsipkasbon.show', ['kasbon' => $arsipKasbon]);
    }

    public function destroy(ArsipKasbon $arsipKasbon)
    {
        if ($arsipKasbon->file_scan && Storage::disk('public')->exists($arsipKasbon->file_scan)) {
            Storage::disk('public')->delete($arsipKasbon->file_scan);
        }
        $arsipKasbon->delete();

        return redirect()
            ->route('arsipkasbon.index')
            ->with('success', 'Arsip SPP berhasil dihapus.');
    }

    /**
     * AJAX: dipanggil tiap admin selesai ketik/scan No Akun di baris item,
     * untuk auto-fill deskripsi dari master_akun.
     */
    public function lookupAkun(string $noAkun)
    {
        $akun = MasterAkun::where('no_akun', $noAkun)->first();

        return response()->json([
            'found'     => (bool) $akun,
            'deskripsi' => $akun->deskripsi ?? null,
        ]);
    }
}