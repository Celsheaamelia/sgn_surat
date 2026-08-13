<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PatrolSession extends Model
{
    protected $table = 'patrol_sessions';

    protected $fillable = [
        'user_id',
        'tanggal',
        'mulai_at',
        'selesai_at',
        'status',
        'total_checkpoint',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scans()
    {
        return $this->hasMany(PatrolScan::class);
    }

    public function scopeBerjalan(Builder $query): Builder
    {
        return $query->where('status', 'berjalan');
    }

    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal', now()->toDateString());
    }

    public function getJumlahScanAttribute(): int
    {
        return $this->scans()->count();
    }

    public function getJumlahTemuanAttribute(): int
    {
        return $this->scans()->whereIn('status', ['temuan', 'bahaya'])->count();
    }

    public function getProgresPersenAttribute(): int
    {
        if (! $this->total_checkpoint) {
            return 0;
        }

        return (int) round(($this->jumlah_scan / $this->total_checkpoint) * 100);
    }

    /**
     * Sesi dianggap terlambat kalau sudah lewat 4 jam sejak mulai dan belum selesai.
     */
    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'berjalan'
            && $this->mulai_at
            && $this->mulai_at->diffInHours(now()) >= 4;
    }
}
