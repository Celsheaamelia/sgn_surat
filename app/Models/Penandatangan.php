<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penandatangan extends Model
{
    protected $table = 'penandatangan';

    protected $fillable = [
        'kode',
        'jabatan',
    ];

    public function riwayatSurat()
    {
        return $this->hasMany(RiwayatSurat::class);
    }
}
