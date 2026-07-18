<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class KasbonOcrService
{
    public function __construct(protected GeminiService $gemini)
    {
    }

    /**
     * Baca 1 file gambar/scan SPP pakai Gemini vision (structured output),
     * lalu balikin field-field header + baris item siap pakai buat isi
     * form verifikasi di halaman "Scan Surat Baru".
     *
     * Return array:
     * [
     *   'raw_text' => string,          // JSON mentah dari Gemini, buat debug
     *   'header'   => [...],           // field header yang berhasil ketebak
     *   'items'    => [ [...], ... ],  // baris item yang berhasil ketebak
     * ]
     */
    public function scan(string $imagePath): array
    {
        try {
            $parsed = $this->gemini->generateStructuredFromImage(
                $imagePath,
                $this->buildPrompt(),
                $this->buildSchema()
            );
        } catch (\Throwable $e) {
            Log::warning('Gemini OCR gagal: ' . $e->getMessage());
            return $this->emptyResult();
        }

        return [
            'raw_text' => json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'header'   => $this->normalizeHeader($parsed['header'] ?? []),
            'items'    => $this->normalizeItems($parsed['items'] ?? []),
        ];
    }

    /**
     * Instruksi ke Gemini soal cara membaca form SPP. Struktur JSON-nya sendiri
     * sudah dipaksa lewat responseSchema, jadi prompt ini fokus ke ATURAN
     * pembacaan datanya saja, bukan format output.
     */
    private function buildPrompt(): string
    {
        return <<<PROMPT
Kamu membaca gambar formulir "Surat Permintaan Pembayaran" (SPP) / kasbon berbahasa Indonesia.

Panduan membaca tiap field:
- tanggal_transaksi: format asli biasanya dd.mm.yyyy atau dd/mm/yyyy -- konversi ke YYYY-MM-DD.
- document_no: nomor dokumen SPP.
- park_oleh: nama petugas yang membuat/park dokumen. Kalau tidak ada, isi "Administrator".
- nama_vendor, kode_vendor: identitas vendor/penerima pembayaran.
- cek_giro_trx: nomor Cek/Giro/Transaksi bank.
- deskripsi_cost_object: uraian peruntukan dana / cost object.
- jumlah_total: total nilai uang, angka murni tanpa titik/koma pemisah ribuan.
- terbilang: kalimat pembilang nominal total (contoh: "Satu Juta Rupiah").
- items: baris-baris tabel rincian akun di bagian bawah form. Tiap baris:
    - no_akun: kode akun (biasanya 6-8 digit angka)
    - pk: kode PK (biasanya 1-3 digit angka), kosongkan jika tidak ada
    - cost_object: cost object baris tersebut jika ada
    - item_text: deskripsi/uraian item
    - jumlah_rupiah: nilai uang baris tersebut, angka murni

Kalau sebuah field benar-benar tidak terbaca atau tidak ada di gambar, kosongkan string-nya
(jangan mengarang nilai). Kalau tidak ada baris item sama sekali, kembalikan array items kosong.
PROMPT;
    }

    /**
     * JSON schema (subset OpenAPI) yang dikirim ke Gemini lewat generationConfig.responseSchema.
     * Ini yang MEMAKSA Gemini balikin struktur JSON persis seperti ini -- teknik yang sama
     * dipakai prototipe "auto scan", diterapkan lewat REST API karena Laravel tidak pakai SDK JS.
     */
    private function buildSchema(): array
    {
        $stringField = ['type' => 'string'];

        return [
            'type' => 'object',
            'properties' => [
                'header' => [
                    'type' => 'object',
                    'properties' => [
                        'tanggal_transaksi'      => $stringField,
                        'document_no'            => $stringField,
                        'park_oleh'               => $stringField,
                        'nama_vendor'             => $stringField,
                        'kode_vendor'             => $stringField,
                        'cek_giro_trx'            => $stringField,
                        'deskripsi_cost_object'   => $stringField,
                        'jumlah_total'            => ['type' => 'number'],
                        'terbilang'               => $stringField,
                    ],
                    'required' => [
                        'tanggal_transaksi', 'document_no', 'park_oleh', 'nama_vendor',
                        'kode_vendor', 'cek_giro_trx', 'deskripsi_cost_object',
                        'jumlah_total', 'terbilang',
                    ],
                ],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'no_akun'       => $stringField,
                            'pk'            => $stringField,
                            'cost_object'   => $stringField,
                            'item_text'     => $stringField,
                            'jumlah_rupiah' => ['type' => 'number'],
                        ],
                        'required' => ['no_akun', 'pk', 'cost_object', 'item_text', 'jumlah_rupiah'],
                    ],
                ],
            ],
            'required' => ['header', 'items'],
        ];
    }

    private function normalizeHeader(array $h): array
    {
        $fields = [
            'tanggal_transaksi', 'document_no', 'park_oleh', 'nama_vendor',
            'kode_vendor', 'cek_giro_trx', 'deskripsi_cost_object',
            'jumlah_total', 'terbilang',
        ];

        $result = [];
        foreach ($fields as $f) {
            $val = $h[$f] ?? null;
            $result[$f] = ($val === '' || $val === null) ? null : $val;
        }

        if (!empty($result['jumlah_total'])) {
            $result['jumlah_total'] = $this->toNumber($result['jumlah_total']);
        }

        if (!empty($result['tanggal_transaksi']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $result['tanggal_transaksi'])) {
            $result['tanggal_transaksi'] = null;
        }

        return $result;
    }

    private function normalizeItems(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $noAkun = trim((string) ($item['no_akun'] ?? ''));
            if ($noAkun === '') {
                continue; // baris kosong, abaikan
            }

            $result[] = [
                'no_akun'       => $noAkun,
                'pk'            => $this->nullIfEmpty($item['pk'] ?? null),
                'cost_object'   => $this->nullIfEmpty($item['cost_object'] ?? null),
                'item_text'     => $this->nullIfEmpty($item['item_text'] ?? null),
                'jumlah_rupiah' => isset($item['jumlah_rupiah']) ? $this->toNumber($item['jumlah_rupiah']) : null,
            ];
        }
        return $result;
    }

    private function nullIfEmpty(mixed $val): ?string
    {
        $val = is_string($val) ? trim($val) : $val;
        return ($val === '' || $val === null) ? null : $val;
    }

    private function toNumber(mixed $val): ?float
    {
        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }
        $clean = preg_replace('/[^\d.]/', '', (string) $val);
        return is_numeric($clean) ? (float) $clean : null;
    }

    private function emptyResult(): array
    {
        return [
            'raw_text' => '',
            'header'   => array_fill_keys([
                'tanggal_transaksi', 'document_no', 'park_oleh', 'nama_vendor',
                'kode_vendor', 'cek_giro_trx', 'deskripsi_cost_object',
                'jumlah_total', 'terbilang',
            ], null),
            'items' => [],
        ];
    }
}