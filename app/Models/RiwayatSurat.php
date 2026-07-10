<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatSurat extends Model
{
    protected $table = 'riwayatsurat';

    protected $fillable = [
        'nomor_surat',
        'perihal',
        'tanggal',
        'penandatangan_id',
        'tujuan_surat_id',
        'klasifikasi_surat_id',
        'user_id',
        'status',
        'uploaded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penandatangan()
    {
        return $this->belongsTo(Penandatangan::class);
    }

    public function tujuanSurat()
    {
        return $this->belongsTo(TujuanSurat::class);
    }

    public function klasifikasiSurat()
    {
        return $this->belongsTo(KlasifikasiSurat::class);
    }

    public function detailSurat()
    {
        return $this->hasOne(DetailSurat::class, 'riwayatsurat_id');
    }
}
