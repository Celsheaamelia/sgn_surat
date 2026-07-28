<?php

namespace App\Services;

use ZipArchive;
use DOMDocument;
use RuntimeException;

/**
 * Service sederhana untuk mengisi template Word (.docx) secara otomatis
 * tanpa dependency eksternal (cukup ext-zip & ext-dom bawaan PHP).
 *
 * Cara pakai template:
 *   Tulis placeholder di file Word dengan format {{NAMA_PLACEHOLDER}},
 *   contoh: {{NAMA_KARYAWAN}}, {{NOMOR_KONTRAK}}, {{TANGGAL_MULAI}}.
 *
 * Kenapa robust terhadap placeholder yang "kepotong"?
 *   Microsoft Word sering memecah satu kalimat jadi beberapa <w:r> (run)
 *   secara internal, walau tampilannya di layar terlihat menyatu. Kalau
 *   placeholder kena potong di tengah, replace text biasa akan gagal.
 *   Service ini menggabungkan semua run dalam satu paragraf jadi satu teks
 *   dulu sebelum mencari & mengganti placeholder, baru ditulis ulang.
 */
class DocxTemplateService
{
    /**
     * @param  string $templatePath  Path absolut ke file .docx template
     * @param  array<string,string> $data  key => value, key TANPA kurung kurawal
     *                                     contoh: ['NAMA_KARYAWAN' => 'Budi', ...]
     * @param  string $outputPath    Path absolut tujuan file hasil generate
     * @return string                Path file yang berhasil dibuat
     */
    public function generate(string $templatePath, array $data, string $outputPath): string
    {
        if (!file_exists($templatePath)) {
            throw new RuntimeException("Template tidak ditemukan: {$templatePath}");
        }

        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        if (!copy($templatePath, $outputPath)) {
            throw new RuntimeException("Gagal menyalin template ke: {$outputPath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($outputPath) !== true) {
            throw new RuntimeException("Gagal membuka file docx: {$outputPath}");
        }

        // Bagian dalam docx yang mungkin memuat teks/placeholder
        $targets = ['word/document.xml', 'word/header1.xml', 'word/header2.xml',
                    'word/header3.xml', 'word/footer1.xml', 'word/footer2.xml', 'word/footer3.xml'];

        foreach ($targets as $target) {
            $xml = $zip->getFromName($target);
            if ($xml === false) {
                continue;
            }

            $xml = $this->replaceInXml($xml, $data);
            $zip->addFromString($target, $xml);
        }

        $zip->close();

        return $outputPath;
    }

    /**
     * Ambil semua placeholder {{...}} yang ada di dalam template, dipakai
     * untuk validasi "field apa saja yang tersedia di template ini".
     *
     * @return string[]
     */
    public function extractPlaceholders(string $templatePath): array
    {
        if (!file_exists($templatePath)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($templatePath) !== true) {
            return [];
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return [];
        }

        $plainText = $this->flattenParagraphText($xml);

        preg_match_all('/\{\{\s*([A-Z0-9_]+)\s*\}\}/', $plainText, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * Gabungkan run per paragraf lalu replace placeholder pada XML docx.
     */
    private function replaceInXml(string $xml, array $data): string
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;

        $prevErrors = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml);
        libxml_use_internal_errors($prevErrors);

        if (!$loaded) {
            // Kalau gagal parse XML, biarkan file apa adanya (jangan sampai corrupt)
            return $xml;
        }

        $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $paragraphs = $dom->getElementsByTagNameNS($ns, 'p');

        foreach ($paragraphs as $paragraph) {
            $this->mergeAndReplaceInParagraph($paragraph, $ns, $data);
        }

        return $dom->saveXML();
    }

    private function mergeAndReplaceInParagraph($paragraph, string $ns, array $data): void
    {
        $textNodes = $paragraph->getElementsByTagNameNS($ns, 't');

        if ($textNodes->length === 0) {
            return;
        }

        $fullText = '';
        foreach ($textNodes as $node) {
            $fullText .= $node->textContent;
        }

        if (strpos($fullText, '{{') === false) {
            return; // tidak ada placeholder di paragraf ini, skip
        }

        $replaced = preg_replace_callback('/\{\{\s*([A-Z0-9_]+)\s*\}\}/', function ($m) use ($data) {
            $key = $m[1];
            return array_key_exists($key, $data) ? (string) $data[$key] : $m[0];
        }, $fullText);

        // Taruh semua teks hasil replace ke node pertama, kosongkan sisanya
        $first = true;
        foreach ($textNodes as $node) {
            if ($first) {
                $node->textContent = $replaced;
                // Jaga spasi di awal/akhir supaya tidak hilang saat dirender Word
                $node->setAttribute('xml:space', 'preserve');
                $first = false;
            } else {
                $node->textContent = '';
            }
        }
    }

    private function flattenParagraphText(string $xml): string
    {
        $dom = new DOMDocument();
        $prevErrors = libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml);
        libxml_use_internal_errors($prevErrors);

        if (!$loaded) {
            return '';
        }

        $ns = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $paragraphs = $dom->getElementsByTagNameNS($ns, 'p');

        $out = [];
        foreach ($paragraphs as $paragraph) {
            $textNodes = $paragraph->getElementsByTagNameNS($ns, 't');
            $line = '';
            foreach ($textNodes as $node) {
                $line .= $node->textContent;
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }
}
