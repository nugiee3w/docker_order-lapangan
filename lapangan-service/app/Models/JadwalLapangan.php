<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalLapangan extends Model
{
    protected $fillable = [
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'harga'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'harga' => 'decimal:2'
    ];

    // Relationship dengan lapangan
    public function lapangan(): BelongsTo
    {
        return $this->belongsTo(Lapangan::class);
    }

    // Scope untuk jadwal yang tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    // Scope untuk filter berdasarkan range waktu
    public function scopeByTimeRange($query, $jam_mulai, $jam_selesai)
    {
        return $query->where('jam_mulai', '>=', $jam_mulai)
            ->where('jam_selesai', '<=', $jam_selesai);
    }

    // Method untuk mengubah status menjadi dipesan
    public function bookSchedule()
    {
        $this->update(['status' => 'dipesan']);
    }

    // Method untuk mengubah status menjadi tersedia
    public function releaseSchedule()
    {
        $this->update(['status' => 'tersedia']);
    }
}
