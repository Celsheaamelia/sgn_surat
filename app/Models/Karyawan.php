<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';

    protected $fillable = [
        'nik',
        'no_ktp',
        'nama',
        'tempat_tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'alamat',
    ];

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }
}
