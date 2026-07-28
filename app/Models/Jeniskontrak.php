<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKontrak extends Model
{
    protected $table = 'jenis_kontraks';

    protected $fillable = [
        'kode',
        'kode_nomor',       // segmen nomor surat: KTR / PJJ
        'nama_jenis',
        'nama_singkat',     // contoh: LMG-DMG, DMG
        'masa_berlaku_bulan',
        'masa_giling',      // true = kontrak dibuat selama masa giling (pakai kode PJJ)
        'template_file',
    ];

    protected $casts = [
        'masa_giling' => 'boolean',
    ];

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }
}
