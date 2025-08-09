<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JadwalLapangan;
use App\Models\Lapangan;
use Carbon\Carbon;

class JadwalLapanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lapangans = Lapangan::all();
        
        // Generate jadwal untuk 7 hari ke depan
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);

        foreach ($lapangans as $lapangan) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Generate jadwal untuk setiap hari
                $this->generateDailySchedule($lapangan, $currentDate);
                $currentDate->addDay();
            }
        }
    }

    private function generateDailySchedule(Lapangan $lapangan, Carbon $date)
    {
        // Jam operasional: 08:00 - 22:00
        $startHour = 8;
        $endHour = 22;
        
        // Durasi per slot (dalam jam)
        $slotDuration = $lapangan->jenis === 'tenis_meja' ? 1 : 2;
        
        for ($hour = $startHour; $hour < $endHour; $hour += $slotDuration) {
            $jamMulai = sprintf('%02d:00', $hour);
            $jamSelesai = sprintf('%02d:00', $hour + $slotDuration);
            
            // Harga bisa berbeda berdasarkan waktu
            $harga = $this->calculatePrice($lapangan, $hour);
            
            JadwalLapangan::create([
                'lapangan_id' => $lapangan->id,
                'tanggal' => $date->format('Y-m-d'),
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'status' => 'tersedia',
                'harga' => $harga
            ]);
        }
    }

    private function calculatePrice(Lapangan $lapangan, int $hour): float
    {
        $basePrice = $lapangan->harga_per_jam;
        
        // Prime time (18:00 - 21:00) harga lebih mahal 20%
        if ($hour >= 18 && $hour < 21) {
            return $basePrice * 1.2;
        }
        
        // Weekend bisa dibuat lebih mahal (implementasi bisa ditambah nanti)
        
        return $basePrice;
    }
}
