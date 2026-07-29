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
 *
 * Kenapa tab (<w:tab/>) ditangani khusus?
 *   Tab di Word BUKAN karakter '\t' di dalam <w:t>, tapi elemen XML
 *   terpisah <w:tab/> yang berdiri sendiri di antara run. Kalau cuma
 *   menggabung <w:t> tanpa memperhitungkan <w:tab/>, tab-nya hilang dari
 *   teks gabungan dan alignment kolom (mis. "Nama :  Budi") jadi berantakan
 *   begitu paragraf ditulis ulang. Di sini <w:tab/> direpresentasikan
 *   sebagai karakter "\t" waktu digabung, dan saat ditulis ulang, paragraf
 *   dibongkar total lalu dibangun ulang: teks dipecah per "\t", diselingi
 *   elemen <w:tab/> ASLI lagi (bukan karakter tab biasa) supaya Word tetap
 *   mengenalinya sebagai tab kolom.
 */
class DocxTemplateService
{
    private const NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

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

        $paragraphs = $dom->getElementsByTagNameNS(self::NS, 'p');

        // Kumpulkan dulu ke array biasa - paragraphs akan dimodifikasi
        // (run dihapus & ditambah lagi) selagi di-loop, NodeList live query
        // bisa kacau kalau di-iterate langsung sambil diubah.
        foreach (iterator_to_array($paragraphs) as $paragraph) {
            $this->mergeAndReplaceInParagraph($paragraph, $data);
        }

        return $dom->saveXML();
    }

    private function mergeAndReplaceInParagraph($paragraph, array $data): void
    {
        $runs = $paragraph->getElementsByTagNameNS(self::NS, 'r');

        if ($runs->length === 0) {
            return;
        }

        $runList = iterator_to_array($runs);

        // Gabungkan teks paragraf, TERMASUK tab (<w:tab/> -> "\t") dan baris
        // baru (<w:br/> -> "\n"), supaya alignment tidak hilang waktu digabung.
        $fullText = '';
        foreach ($runList as $run) {
            foreach ($run->childNodes as $child) {
                if ($child->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }
                if ($child->localName === 't') {
                    $fullText .= $child->textContent;
                } elseif ($child->localName === 'tab') {
                    $fullText .= "\t";
                } elseif ($child->localName === 'br' || $child->localName === 'cr') {
                    $fullText .= "\n";
                }
            }
        }

        if (strpos($fullText, '{{') === false) {
            return; // tidak ada placeholder di paragraf ini, biarkan apa adanya
        }

        $replaced = preg_replace_callback('/\{\{\s*([A-Z0-9_]+)\s*\}\}/', function ($m) use ($data) {
            $key = $m[1];
            return array_key_exists($key, $data) ? (string) $data[$key] : $m[0];
        }, $fullText);

        // Ambil format (rPr) dari run pertama yang punya rPr, supaya font/bold
        // dsb tetap konsisten di run-run baru. Highlight (kuning, dsb) SENGAJA
        // dibuang dari format yang dipakai ulang - itu cuma penanda "isi di sini"
        // di draft, bukan bagian dari dokumen final.
        $templateRPr = null;
        foreach ($runList as $run) {
            foreach ($run->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === 'rPr') {
                    $templateRPr = $child->cloneNode(true);
                    break 2;
                }
            }
        }
        if ($templateRPr !== null) {
            foreach (iterator_to_array($templateRPr->getElementsByTagNameNS(self::NS, 'highlight')) as $hl) {
                $hl->parentNode->removeChild($hl);
            }
        }

        $dom = $paragraph->ownerDocument;

        // Hapus semua run lama di paragraf ini
        foreach ($runList as $run) {
            $run->parentNode->removeChild($run);
        }

        // Bangun ulang: pecah per baris ("\n") lalu per kolom ("\t"), selingi
        // run teks baru dengan elemen <w:tab/> / <w:br/> ASLI Word.
        $lines = explode("\n", $replaced);

        foreach ($lines as $lineIndex => $line) {
            if ($lineIndex > 0) {
                $brRun = $dom->createElementNS(self::NS, 'w:r');
                if ($templateRPr) {
                    $brRun->appendChild($templateRPr->cloneNode(true));
                }
                $brRun->appendChild($dom->createElementNS(self::NS, 'w:br'));
                $paragraph->appendChild($brRun);
            }

            $parts = explode("\t", $line);
            $lastIndex = count($parts) - 1;

            foreach ($parts as $partIndex => $part) {
                if ($part !== '') {
                    $run = $dom->createElementNS(self::NS, 'w:r');
                    if ($templateRPr) {
                        $run->appendChild($templateRPr->cloneNode(true));
                    }
                    $t = $dom->createElementNS(self::NS, 'w:t');
                    $t->appendChild($dom->createTextNode($part));
                    $t->setAttribute('xml:space', 'preserve');
                    $run->appendChild($t);
                    $paragraph->appendChild($run);
                }

                if ($partIndex < $lastIndex) {
                    $tabRun = $dom->createElementNS(self::NS, 'w:r');
                    if ($templateRPr) {
                        $tabRun->appendChild($templateRPr->cloneNode(true));
                    }
                    $tabRun->appendChild($dom->createElementNS(self::NS, 'w:tab'));
                    $paragraph->appendChild($tabRun);
                }
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

        $paragraphs = $dom->getElementsByTagNameNS(self::NS, 'p');

        $out = [];
        foreach ($paragraphs as $paragraph) {
            $textNodes = $paragraph->getElementsByTagNameNS(self::NS, 't');
            $line = '';
            foreach ($textNodes as $node) {
                $line .= $node->textContent;
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }
}
