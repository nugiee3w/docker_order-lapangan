<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JadwalLapangan;

class Lapangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'deskripsi',
        'harga_per_jam',
        'status',
        'fasilitas',
        'lokasi',
        'gambar'
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'harga_per_jam' => 'decimal:2'
    ];

    // Relationship dengan jadwal lapangan
    public function jadwalLapangan(): HasMany
    {
        return $this->hasMany(JadwalLapangan::class);
    }

    // Alias untuk plural (untuk konsistensi dengan view)
    public function jadwalLapangans(): HasMany
    {
        return $this->jadwalLapangan();
    }

    // Scope untuk filter berdasarkan jenis lapangan
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    // Scope untuk lapangan yang tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    // Method untuk mendapatkan jadwal yang tersedia pada tanggal tertentu
    public function getAvailableSchedule($tanggal)
    {
        return $this->jadwalLapangan()
            ->where('tanggal', $tanggal)
            ->where('status', 'tersedia')
            ->orderBy('jam_mulai')
            ->get();
    }
}
