<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;

/**
 * Import data karyawan dari database/seeders/data/karyawan_import.csv
 * (hasil gabungan otomatis dari 3 sumber Excel kamu: SOURCE roster + kedua
 * sheet PKWT yang datanya udah lengkap per-karyawan). 914 baris unik
 * berdasarkan NIK Karyawan.
 *
 * Aman dijalankan berkali-kali - pakai updateOrCreate berdasarkan NIP (NIK
 * Karyawan), jadi kalau CSV-nya diupdate lagi nanti tinggal seed ulang.
 */
class KaryawanImportSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/karyawan_import.csv');

        if (!file_exists($path)) {
            $this->command?->warn("File tidak ditemukan: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue; // baris rusak/kosong, skip
            }

            $data = array_combine($header, $row);

            if (empty($data['nik']) || empty($data['nama'])) {
                continue;
            }

            Karyawan::updateOrCreate(
                ['nik' => trim($data['nik'])],
                [
                    'no_ktp'               => $data['no_ktp'] ?: null,
                    'nama'                 => $data['nama'],
                    'jabatan'              => $data['jabatan'] ?: null,
                    'departemen'           => $data['departemen'] ?: null,
                    'rincian_pekerjaan_1'  => $data['rincian_pekerjaan_1'] ?: null,
                    'rincian_pekerjaan_2'  => $data['rincian_pekerjaan_2'] ?: null,
                    'rincian_pekerjaan_3'  => $data['rincian_pekerjaan_3'] ?: null,
                    'status_kepegawaian'   => $data['status_kepegawaian'] ?: null,
                    'nilai_grade'          => $data['nilai_grade'] !== '' ? (int) $data['nilai_grade'] : null,
                    'tempat_tanggal_lahir' => $data['tempat_tanggal_lahir'] ?: null,
                    'jenis_kelamin'        => $data['jenis_kelamin'] ?: null,
                    'agama'                => $data['agama'] ?: null,
                    'status_perkawinan'    => $data['status_perkawinan'] ?: null,
                    'alamat'               => $data['alamat'] ?: null,
                    'status_karyawan'      => $data['status_karyawan'] ?: 'Aktif',
                ]
            );

            $count++;
        }

        fclose($handle);

        $this->command?->info("Karyawan berhasil diimport: {$count} baris.");
    }
}
