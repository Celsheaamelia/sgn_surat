<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenandatanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penandatangan')->insert([
            [
                'kode' => 'SG26',
                'jabatan' => 'General Manager',
            ],
            [
                'kode' => 'SG26F',
                'jabatan' => 'Manager',
            ],
            [
                'kode' => 'ASMAN',
                'jabatan' => 'Asistan Manager',
            ],
        ]);
    }
}
