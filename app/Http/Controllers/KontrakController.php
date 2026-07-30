<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\JenisKontrak;
use App\Models\Penandatangan;
use App\Services\DocxTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Support\IndonesianDate;

class KontrakController extends Controller
{
    use NomorKontrakUrut;

    public function index(Request $request)
    {
        $kontrakList = Kontrak::with(['karyawan', 'jenisKontrak', 'penandatangan'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('nomor_kontrak', 'like', "%{$request->search}%")
                  ->orWhereHas('karyawan', function ($qq) use ($request) {
                      $qq->where('nama', 'like', "%{$request->search}%")
                         ->orWhere('nik', 'like', "%{$request->search}%");
                  });
            })
            ->when($request->jenis, function ($q) use ($request) {
                $q->where('jenis_kontrak_id', $request->jenis);
            })
            // Kontrak paling baru tampil paling atas. Urut berdasarkan tanggal kontrak
            // dulu, lalu id sebagai tie-breaker supaya kontrak dengan tanggal yang sama
            // tetap konsisten menampilkan yang terakhir dibuat di paling atas.
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $jenisList = JenisKontrak::orderBy('nama_jenis')->get();

        return view('kontrak.index', compact('kontrakList', 'jenisList'));
    }

    public function create()
    {
        $jenisList = JenisKontrak::orderBy('nama_jenis')->get();

        $tanggal = now()->toDateString();
        $defaultJenis = $jenisList->first();
        $nextSequence = $defaultJenis
            ? $this->nextAvailableContractSequence($tanggal, $defaultJenis->kode_nomor ?: $defaultJenis->kode)
            : 1;

        $penandatangan = $this->resolveDefaultPenandatangan();

        return view('kontrak.create', compact('jenisList', 'nextSequence', 'penandatangan'));
    }

    public function store(Request $request, DocxTemplateService $docxService)
    {
        $request->validate([
            'karyawan_id'         => 'required|exists:karyawans,id',
            'jenis_kontrak_id'    => 'required|exists:jenis_kontraks,id',
            'tanggal'             => 'required|date',
            'nomor_urut'          => 'required|integer|min:1',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'nullable|date|after_or_equal:tanggal_mulai',
            'jabatan_kontrak'     => 'nullable|string|max:150',
            'bagian_kontrak'      => 'nullable|string|max:150',
            'rincian_pekerjaan_1' => 'nullable|string|max:255',
            'rincian_pekerjaan_2' => 'nullable|string|max:255',
            'rincian_pekerjaan_3' => 'nullable|string|max:255',
            'catatan'             => 'nullable|string',
        ]);

        $karyawan      = Karyawan::findOrFail($request->karyawan_id);
        $jenisKontrak  = JenisKontrak::findOrFail($request->jenis_kontrak_id);
        $penandatangan = $this->resolveDefaultPenandatangan();

        // Tanggal Selesai wajib diisi manual HANYA untuk jenis kontrak yang
        // bukan masa giling (KTR / 12 bulan). Untuk PJJ/masa giling, selesainya
        // otomatis "sampai berakhirnya Masa Giling" - tidak boleh diisi tanggal
        // tetap secara manual di form.
        if (!$jenisKontrak->masa_giling && !$request->tanggal_selesai) {
            return back()->withInput()->with('error',
                'Tanggal Selesai wajib diisi untuk jenis kontrak ' . ($jenisKontrak->nama_singkat ?: $jenisKontrak->nama_jenis) . '.'
            );
        }

        $kodeNomor = $jenisKontrak->kode_nomor ?: $jenisKontrak->kode;
        $nomorInt  = (int) $request->nomor_urut;
        $grouped   = $this->groupedUsedContractNumbersForDate($request->tanggal, $kodeNomor);

        if (in_array($nomorInt, $grouped['terpakai'])) {
            return back()->withInput()->with('error',
                'Nomor #' . str_pad($nomorInt, 3, '0', STR_PAD_LEFT) . ' sudah dipakai untuk kontrak lain.'
            );
        }

        if (in_array($nomorInt, $grouped['direservasi'])) {
            return back()->withInput()->with('error',
                'Nomor #' . str_pad($nomorInt, 3, '0', STR_PAD_LEFT) . ' sedang direservasi. Pilih nomor lain.'
            );
        }

        $urut = str_pad($nomorInt, 3, '0', STR_PAD_LEFT);

        $nomorKontrak = $this->buildNomorKontrak($jenisKontrak, $request->tanggal, $urut);

        // Masa giling (PJJ) -> tanggal_selesai memang sengaja dikosongkan di
        // database (artinya "sampai ditetapkan berakhirnya Masa Giling").
        // Kontrak biasa (KTR) -> tanggal_selesai wajib dari input form.
        $tanggalSelesai = $jenisKontrak->masa_giling ? null : $request->tanggal_selesai;

        $kontrak = Kontrak::create([
            'nomor_kontrak'        => $nomorKontrak,
            'tanggal'              => $request->tanggal,
            'karyawan_id'          => $karyawan->id,
            'jenis_kontrak_id'     => $jenisKontrak->id,
            'penandatangan_id'     => $penandatangan?->id,
            'tanggal_mulai'        => $request->tanggal_mulai,
            'tanggal_selesai'      => $tanggalSelesai,
            'jabatan_kontrak'      => $request->jabatan_kontrak,
            'bagian_kontrak'       => $request->bagian_kontrak,
            'rincian_pekerjaan_1'  => $request->rincian_pekerjaan_1,
            'rincian_pekerjaan_2'  => $request->rincian_pekerjaan_2,
            'rincian_pekerjaan_3'  => $request->rincian_pekerjaan_3,
            // Gaji pokok sudah standar per jenis kontrak (lihat draft), jadi
            // diambil otomatis dari jenis_kontraks.gaji_pokok_default -
            // tidak lagi diinput manual lewat form.
            'gaji_pokok'           => $jenisKontrak->gaji_pokok_default,
            'catatan'              => $request->catatan,
            'status'               => 'Draft',
            'user_id'              => Auth::id(),
        ]);

        try {
            $generatedPath = $this->generateDocument($kontrak, $docxService);
            $kontrak->update(['generated_file_path' => $generatedPath]);
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('kontrak.index')
                ->with('success', 'Kontrak tersimpan, tapi dokumen Word gagal digenerate otomatis: ' . $e->getMessage());
        }

        return redirect()->route('kontrak.index')
            ->with('success', 'Kontrak berhasil dibuat.')
            ->with('created_nomor', $nomorKontrak);
    }

    /**
     * Semua kontrak SG26 ditandatangani orang yang sama (General Manager),
     * jadi tidak perlu dipilih manual tiap kali - otomatis ambil urutan
     * jabatan tertinggi (General Manager > Manager > Asisten Manager).
     * Kalau suatu saat memang butuh milih manual lagi, tinggal kembalikan
     * dropdown penandatangan_id di form dan skip method ini.
     */
    private function resolveDefaultPenandatangan(): ?Penandatangan
    {
        return Penandatangan::orderByRaw("
            CASE
                WHEN jabatan = 'General Manager' THEN 1
                WHEN jabatan = 'Manager' THEN 2
                WHEN jabatan = 'Asisten Manager' THEN 3
                ELSE 4
            END
        ")->first();
    }

    /**
     * Format: {prefix dari config}-{kode_nomor jenis kontrak}/{Ymd}.{urut}
     * Contoh: SG26-PERSE-PJJ/20260508.001
     */
    private function buildNomorKontrak(JenisKontrak $jenisKontrak, string $tanggal, string $urut): string
    {
        $prefix = config('kontrak.prefix', 'SG26-PERSE');
        $kodeNomor = $jenisKontrak->kode_nomor ?: $jenisKontrak->kode;

        return $prefix . '-' . $kodeNomor . '/' . date('Ymd', strtotime($tanggal)) . '.' . $urut;
    }

    /**
     * Isi template docx dengan data kontrak + karyawan, simpan ke storage.
     */
    private function generateDocument(Kontrak $kontrak, DocxTemplateService $docxService): string
    {
        $kontrak->load(['karyawan', 'jenisKontrak', 'penandatangan']);
        $karyawan = $kontrak->karyawan;
        $jenis    = $kontrak->jenisKontrak;
        $ttd      = $kontrak->penandatangan;

        $templatePath = $jenis->template_file
            ? Storage::path($jenis->template_file)
            : Storage::path('templates/kontrak/default_template.docx');

        if (!file_exists($templatePath)) {
            $templatePath = Storage::path('templates/kontrak/default_template.docx');
        }

        $tempatTanggalLahir = $karyawan->tempat_tanggal_lahir ?: '-';

        $tanggalKontrak = Carbon::parse($kontrak->tanggal);
        $tanggalMulai   = Carbon::parse($kontrak->tanggal_mulai);
        $gajiPokok      = (int) ($kontrak->gaji_pokok ?? $jenis->gaji_pokok_default ?? 0);

        $data = [
            'NOMOR_KONTRAK'             => $kontrak->nomor_kontrak,
            'JENIS_KONTRAK'             => $jenis->nama_jenis,
            'TANGGAL_KONTRAK'           => $tanggalKontrak->translatedFormat('d F Y'),

            'PADA_HARI_INI'             => IndonesianDate::namaHari($tanggalKontrak),
            'TANGGAL_KONTRAK_TERBILANG' => IndonesianDate::tanggalTerbilang($tanggalKontrak),
            'BULAN_KONTRAK'             => IndonesianDate::namaBulan($tanggalKontrak),
            'TANGGAL_KONTRAK_SINGKAT'   => $tanggalKontrak->format('d-m-Y'),

            'NAMA_PENANDATANGAN'        => $ttd->nama ?? $ttd->jabatan ?? '-',
            'JABATAN_PENANDATANGAN'     => $ttd->jabatan ?? '-',
            'NO_SK_PENANDATANGAN'       => $ttd->no_sk ?? '-',
            'TANGGAL_SK_PENANDATANGAN'  => $ttd->tanggal_sk
                ? Carbon::parse($ttd->tanggal_sk)->format('d-m-Y')
                : '-',

            'NAMA_KARYAWAN'             => $karyawan->nama,
            'NIK_KARYAWAN'              => $karyawan->nik,
            'NO_KTP_KARYAWAN'           => $karyawan->no_ktp ?? '-',
            'TEMPAT_TANGGAL_LAHIR'      => $tempatTanggalLahir,
            'JENIS_KELAMIN'             => $karyawan->jenis_kelamin ?? '-',
            'AGAMA'                     => $karyawan->agama ?? '-',
            'STATUS_PERKAWINAN'         => $karyawan->status_perkawinan ?? '-',
            'ALAMAT_KARYAWAN'           => $karyawan->alamat ?? '-',
            'JABATAN_KARYAWAN'          => $kontrak->jabatan_kontrak ?: '-',
            'DEPARTEMEN'                => $kontrak->bagian_kontrak ?: '-',
            'RINCIAN_PEKERJAAN_1'       => $kontrak->rincian_pekerjaan_1 ?: '-',
            'RINCIAN_PEKERJAAN_2'       => $kontrak->rincian_pekerjaan_2 ?: '-',
            'RINCIAN_PEKERJAAN_3'       => $kontrak->rincian_pekerjaan_3 ?: '-',

            'TANGGAL_MULAI'             => $tanggalMulai->translatedFormat('d F Y'),
            'TANGGAL_SELESAI'           => $kontrak->tanggal_selesai
                ? Carbon::parse($kontrak->tanggal_selesai)->translatedFormat('d F Y')
                : ($jenis->masa_giling ? 'Berakhirnya Masa Giling' : 'Tidak Ditentukan (Tetap)'),

            'GAJI_POKOK'                => number_format($gajiPokok, 0, ',', '.'),
            'GAJI_POKOK_TERBILANG'      => $gajiPokok > 0
                ? IndonesianDate::rupiahTerbilang($gajiPokok)
                : '-',
            'UPAH_LEMBUR_SEJAM'         => $gajiPokok > 0
                ? number_format((int) round($gajiPokok / 173), 0, ',', '.')
                : '-',

            'CATATAN'                   => $kontrak->catatan ?: '-',
        ];

        $outputRelative = 'kontrak/generated/' . str_replace(['/', '\\'], '-', $kontrak->nomor_kontrak) . '.docx';
        $outputPath = Storage::path($outputRelative);

        $docxService->generate($templatePath, $data, $outputPath);

        return $outputRelative;
    }

    public function show(Kontrak $kontrak)
    {
        $kontrak->load(['karyawan', 'jenisKontrak', 'penandatangan', 'user']);

        return view('kontrak.show', compact('kontrak'));
    }

    public function download(Kontrak $kontrak)
    {
        if (!$kontrak->generated_file_path || !Storage::exists($kontrak->generated_file_path)) {
            return back()->with('error', 'File dokumen kontrak belum tersedia.');
        }

        $filename = str_replace(['/', '\\'], '-', $kontrak->nomor_kontrak) . '.docx';

        return Storage::download($kontrak->generated_file_path, $filename);
    }

    public function regenerate(Kontrak $kontrak, DocxTemplateService $docxService)
    {
        try {
            $generatedPath = $this->generateDocument($kontrak, $docxService);
            $kontrak->update(['generated_file_path' => $generatedPath]);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal generate ulang dokumen: ' . $e->getMessage());
        }

        return back()->with('success', 'Dokumen kontrak berhasil digenerate ulang.');
    }

    public function uploadSignedForm(Kontrak $kontrak)
    {
        $kontrak->load(['karyawan', 'jenisKontrak', 'penandatangan']);

        return view('kontrak.upload', compact('kontrak'));
    }

    public function uploadSigned(Request $request, Kontrak $kontrak)
    {
        $request->validate([
            'file_kontrak' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $filePath = $request->file('file_kontrak')->store('kontrak-signed', 'public');

        $kontrak->update([
            'signed_file_path'    => $filePath,
            'signed_file_name'    => $request->file('file_kontrak')->getClientOriginalName(),
            'signed_uploaded_at'  => now(),
            'status'              => 'Aktif',
        ]);

        return redirect()->route('kontrak.upload.form', $kontrak->id)
            ->with('success', 'Kontrak yang sudah ditandatangani berhasil diupload.');
    }

    public function deleteSigned(Kontrak $kontrak)
    {
        if ($kontrak->signed_file_path && Storage::disk('public')->exists($kontrak->signed_file_path)) {
            Storage::disk('public')->delete($kontrak->signed_file_path);
        }

        $kontrak->update([
            'signed_file_path'   => null,
            'signed_file_name'   => null,
            'signed_uploaded_at' => null,
            'status'             => 'Draft',
        ]);

        return redirect()->route('kontrak.upload.form', $kontrak->id)
            ->with('success', 'File tanda tangan berhasil dihapus.');
    }

    public function destroy(Kontrak $kontrak)
    {
        if ($kontrak->generated_file_path && Storage::exists($kontrak->generated_file_path)) {
            Storage::delete($kontrak->generated_file_path);
        }
        if ($kontrak->signed_file_path && Storage::disk('public')->exists($kontrak->signed_file_path)) {
            Storage::disk('public')->delete($kontrak->signed_file_path);
        }

        $kontrak->delete();

        return redirect()->route('kontrak.index')->with('success', 'Kontrak berhasil dihapus.');
    }

    public function getNextSequence(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'jenis_kontrak_id' => 'required|exists:jenis_kontraks,id',
        ]);

        $jenis = JenisKontrak::findOrFail($request->jenis_kontrak_id);
        $kodeNomor = $jenis->kode_nomor ?: $jenis->kode;

        return response()->json([
            'sequence' => str_pad($this->nextAvailableContractSequence($request->tanggal, $kodeNomor), 3, '0', STR_PAD_LEFT),
        ]);
    }

    public function cekStatusNomor(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'jenis_kontrak_id' => 'required|exists:jenis_kontraks,id',
        ]);

        $jenis = JenisKontrak::findOrFail($request->jenis_kontrak_id);
        $kodeNomor = $jenis->kode_nomor ?: $jenis->kode;

        return response()->json($this->groupedUsedContractNumbersForDate($request->tanggal, $kodeNomor));
    }
}