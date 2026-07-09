<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiSurat extends Model
{
    protected $table = 'klasifikasi_surat';

    protected $fillable = [
        'kode',
        'jenis_surat',
    ];

    public function riwayatSurat()
    {
        return $this->hasMany(RiwayatSurat::class);
    }
}
