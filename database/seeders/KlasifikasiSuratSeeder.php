<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlasifikasiSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('klasifikasi_surat')->insert([
            [
                'kode' => 'SBI',
                'jenis_surat' => 'Surat Biasa',
            ],
            [
                'kode' => 'SIN',
                'jenis_surat' => 'Surat Internal',
            ],
            [
                'kode' => 'SKP',
                'jenis_surat' => 'Surat Keputusan',
            ],
            [
                'kode' => 'SPK',
                'jenis_surat' => 'Surat Perintah Kerja',
            ],
            [
                'kode' => 'UND',
                'jenis_surat' => 'Surat Undangan',
            ],
            [
                'kode' => 'SPP',
                'jenis_surat' => 'Surat Permintaan Persetujuan',
            ],
            [
                'kode' => 'SKU',
                'jenis_surat' => 'Surat Kuasa',
            ],
            [
                'kode' => 'SKE',
                'jenis_surat' => 'Surat Keterangan',
            ],
            [
                'kode' => 'SED',
                'jenis_surat' => 'Surat Edaran',
            ],
            [
                'kode' => 'SUT',
                'jenis_surat' => 'Surat Tugas',
            ],
            [
                'kode' => 'SRA',
                'jenis_surat' => 'Surat Rahasia',
            ],
            [
                'kode' => 'PJJ',
                'jenis_surat' => 'Surat Perjanjian',
            ],
            [
                'kode' => 'KJS',
                'jenis_surat' => 'Surat Kerjasama',
            ],
            [
                'kode' => 'OPL',
                'jenis_surat' => 'Surat Pengadaan',
            ],
            [
                'kode' => 'SPS',
                'jenis_surat' => 'Surat Pemasaran',
            ],
            [
                'kode' => 'KTR',
                'jenis_surat' => 'Kontrak',
            ],
       ]);
    }
}
