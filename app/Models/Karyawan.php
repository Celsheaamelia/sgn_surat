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
        'jabatan',
        'departemen',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'alamat',
        'no_hp',
        'email',
        'tanggal_mulai_kerja',
        'status_karyawan',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'tanggal_mulai_kerja' => 'date',
    ];

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class);
    }
}
