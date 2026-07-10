<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSurat extends Model
{
    protected $table = 'detail_surat';

    protected $fillable = [
        'riwayatsurat_id',
        'file_path',
        'file_name',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function surat()
    {
        return $this->belongsTo(RiwayatSurat::class, 'riwayatsurat_id');
    }
}
