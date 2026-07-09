<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TujuanSurat extends Model
{
    protected $table = 'tujuan_surats';

    protected $fillable = [
        'kode',
        'nama_tujuan',
    ];

    public function riwayatSurat()
    {
        return $this->hasMany(RiwayatSurat::class);
    }
}
