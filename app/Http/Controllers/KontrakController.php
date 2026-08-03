<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use App\Models\Karyawan;
use App\Models\JenisKontrak;
use App\Models\Penandatangan;
use App\Models\Template;
use App\Services\DocxTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Support\IndonesianDate;

class KontrakController extends Controller
{
    use NomorUrut;

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

            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $jenisList = JenisKontrak::orderBy('nama_jenis')->get();

        if ($request->ajax()) {
            return view('kontrak.partials.results', compact('kontrakList'))->render();
        }

        return view('kontrak.index', compact('kontrakList', 'jenisList'));
    }

    public function create()
    {
        $jenisList = JenisKontrak::orderBy('nama_jenis')->get();

        $tanggal = now()->toDateString();

        $nextSequence = $this->nextAvailableSequence($tanggal);

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

        if (!$jenisKontrak->masa_giling && !$request->tanggal_selesai) {
            return back()->withInput()->with('error',
                'Tanggal Selesai wajib diisi untuk jenis kontrak ' . ($jenisKontrak->nama_singkat ?: $jenisKontrak->nama_jenis) . '.'
            );
        }

        $nomorInt = (int) $request->nomor_urut;

        $grouped = $this->groupedUsedNumbersForDate($request->tanggal);

        if (in_array($nomorInt, $grouped['terpakai'])) {
            return back()->withInput()->with('error',
                'Nomor #' . str_pad($nomorInt, 3, '0', STR_PAD_LEFT) . ' sudah dipakai untuk kontrak/surat lain.'
            );
        }

        if (in_array($nomorInt, $grouped['direservasi'])) {
            return back()->withInput()->with('error',
                'Nomor #' . str_pad($nomorInt, 3, '0', STR_PAD_LEFT) . ' sedang direservasi. Pilih nomor lain.'
            );
        }

        $urut = str_pad($nomorInt, 3, '0', STR_PAD_LEFT);

        $nomorKontrak = $this->buildNomorKontrak($jenisKontrak, $request->tanggal, $urut);
        $tanggalSelesai = $jenisKontrak->masa_giling ? null : $request->tanggal_selesai;

        // Template default aktif untuk jenis kontrak ini DIBEKUKAN ke kontrak
        // yang baru dibuat (disimpan sebagai template_id). Kalau nanti
        // default-nya diganti (upload template baru & jadikan default),
        // kontrak yang sudah ada ini TIDAK ikut berubah - hanya kontrak yang
        // dibuat SETELAH pergantian yang otomatis pakai template baru.
        $templateDefault = Template::where('jenis_kontrak_id', $jenisKontrak->id)
            ->where('is_default', true)
            ->first();

        $kontrak = Kontrak::create([
            'nomor_kontrak'        => $nomorKontrak,
            'tanggal'              => $request->tanggal,
            'karyawan_id'          => $karyawan->id,
            'jenis_kontrak_id'     => $jenisKontrak->id,
            'template_id'          => $templateDefault?->id,
            'penandatangan_id'     => $penandatangan?->id,
            'tanggal_mulai'        => $request->tanggal_mulai,
            'tanggal_selesai'      => $tanggalSelesai,
            'jabatan_kontrak'      => $request->jabatan_kontrak,
            'bagian_kontrak'       => $request->bagian_kontrak,
            'rincian_pekerjaan_1'  => $request->rincian_pekerjaan_1,
            'rincian_pekerjaan_2'  => $request->rincian_pekerjaan_2,
            'rincian_pekerjaan_3'  => $request->rincian_pekerjaan_3,
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

    private function buildNomorKontrak(JenisKontrak $jenisKontrak, string $tanggal, string $urut): string
    {
        $prefix = config('kontrak.prefix', 'SG26-PERSE');
        $kodeNomor = $jenisKontrak->kode_nomor ?: $jenisKontrak->kode;

        return $prefix . '-' . $kodeNomor . '/' . date('Ymd', strtotime($tanggal)) . '.' . $urut;
    }

    private function generateDocument(Kontrak $kontrak, DocxTemplateService $docxService): string
    {
        $kontrak->load(['karyawan', 'jenisKontrak', 'penandatangan', 'template']);
        $karyawan = $kontrak->karyawan;
        $jenis    = $kontrak->jenisKontrak;
        $ttd      = $kontrak->penandatangan;

        // Urutan prioritas template: template yang sudah dipilih eksplisit
        // buat kontrak ini (lewat tombol "Ganti Template") > template
        // bawaan jenis kontrak > default_template.docx.
        $templatePath = $kontrak->template
            ? Storage::path($kontrak->template->file_path)
            : ($jenis->template_file
                ? Storage::path($jenis->template_file)
                : Storage::path('templates/kontrak/default_template.docx'));

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
            'TANGGAL_SK_PENANDATANGAN'  => $ttd?->tanggal_sk
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

        $kontrak->loadMissing('template');
        if ($kontrak->template && !$kontrak->template->published_at) {
            return back()->with('error', 'Template yang dipakai kontrak ini belum dipublish. Publish dulu template-nya di halaman Kelola Template.');
        }

        $filename = str_replace(['/', '\\'], '-', $kontrak->nomor_kontrak) . '.docx';

        return Storage::download($kontrak->generated_file_path, $filename);
    }

    public function preview(Kontrak $kontrak)
    {
        if (!$kontrak->generated_file_path || !Storage::exists($kontrak->generated_file_path)) {
            return back()->with('error', 'Dokumen belum digenerate, tidak bisa dipreview.');
        }

        $kontrak->load(['karyawan', 'jenisKontrak', 'penandatangan', 'template']);

        return view('kontrak.preview', [
            'kontrak' => $kontrak,
            'docxUrl' => route('kontrak.preview.file', $kontrak),
        ]);
    }

    /**
     * Kirim raw bytes file .docx (dipanggil via fetch() dari JS di halaman
     * preview, lalu di-render langsung di browser - bukan didownload).
     */
    public function previewFile(Kontrak $kontrak)
    {
        if (!$kontrak->generated_file_path || !Storage::exists($kontrak->generated_file_path)) {
            abort(404);
        }

        return response(Storage::get($kontrak->generated_file_path), 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Tandai kontrak sebagai sudah dipublish. Setelah ini baru boleh didownload.
     */


    /**
     * Ganti template dokumen untuk kontrak ini, lalu generate ulang.
     * Publish status di-reset ke belum-publish karena isi dokumen berubah -
     * user wajib preview & publish ulang sebelum bisa download lagi.
     */
    public function switchTemplate(Request $request, Kontrak $kontrak, DocxTemplateService $docxService)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        $kontrak->update([
            'template_id'  => $request->template_id,
        ]);

        try {
            $generatedPath = $this->generateDocument($kontrak, $docxService);
            $kontrak->update(['generated_file_path' => $generatedPath]);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal generate ulang dengan template baru: ' . $e->getMessage());
        }

        return redirect()->route('kontrak.show', $kontrak)
            ->with('success', 'Template berhasil diganti. Silakan preview lagi sebelum publish.');
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
        // jenis_kontrak_id sengaja tidak lagi divalidasi wajib di sini -
        // nomor urut sekarang satu rangkaian gabungan (surat + semua jenis
        // kontrak), jadi tidak butuh info jenis kontrak buat dihitung.
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        return response()->json([
            'sequence' => str_pad($this->nextAvailableSequence($request->tanggal), 3, '0', STR_PAD_LEFT),
        ]);
    }

    public function cekStatusNomor(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        return response()->json($this->groupedUsedNumbersForDate($request->tanggal));
    }
}
