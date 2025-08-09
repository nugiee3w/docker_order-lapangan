<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LapanganService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.lapangan.url', env('LAPANGAN_SERVICE_URL', 'http://lapangan-service'));
        $this->token = config('services.lapangan.token', env('LAPANGAN_SERVICE_TOKEN'));
    }

    /**
     * Get HTTP client with authentication
     */
    protected function getHttpClient()
    {
        $client = Http::timeout(30);
        
        if ($this->token) {
            $client = $client->withToken($this->token);
        }
        
        return $client;
    }

    /**
     * Get lapangan data by ID
     */
    public function getLapangan($id)
    {
        try {
            $response = $this->getHttpClient()->get("{$this->baseUrl}/api/lapangan/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to get lapangan data", [
                'id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil data lapangan'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when getting lapangan data", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Get jadwal lapangan data by ID
     */
    public function getJadwalLapangan($id)
    {
        try {
            $response = $this->getHttpClient()->get("{$this->baseUrl}/api/jadwal-lapangan/{$id}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to get jadwal lapangan data", [
                'id' => $id,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil data jadwal lapangan'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when getting jadwal lapangan data", [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Book schedule by ID
     */
    public function bookSchedule($jadwalLapanganId)
    {
        try {
            $response = $this->getHttpClient()->post("{$this->baseUrl}/api/jadwal-lapangan/{$jadwalLapanganId}/book");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to book schedule", [
                'jadwal_lapangan_id' => $jadwalLapanganId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memesan jadwal'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when booking schedule", [
                'jadwal_lapangan_id' => $jadwalLapanganId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Release schedule by ID
     */
    public function releaseSchedule($jadwalLapanganId)
    {
        try {
            $response = $this->getHttpClient()->post("{$this->baseUrl}/api/jadwal-lapangan/{$jadwalLapanganId}/release");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to release schedule", [
                'jadwal_lapangan_id' => $jadwalLapanganId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membebaskan jadwal'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when releasing schedule", [
                'jadwal_lapangan_id' => $jadwalLapanganId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Get available schedule for lapangan
     */
    public function getAvailableSchedule($lapanganId, $tanggal)
    {
        try {
            $response = $this->getHttpClient()->get("{$this->baseUrl}/api/lapangan/{$lapanganId}/available-schedule", [
                'tanggal' => $tanggal
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to get available schedule", [
                'lapangan_id' => $lapanganId,
                'tanggal' => $tanggal,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil jadwal tersedia'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when getting available schedule", [
                'lapangan_id' => $lapanganId,
                'tanggal' => $tanggal,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Get all lapangan with filters
     */
    public function getAllLapangan($filters = [])
    {
        try {
            $response = $this->getHttpClient()->get("{$this->baseUrl}/api/lapangan", $filters);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Failed to get all lapangan", [
                'filters' => $filters,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil data lapangan'
            ];

        } catch (\Exception $e) {
            Log::error("Exception when getting all lapangan", [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke layanan lapangan'
            ];
        }
    }

    /**
     * Generate available time slots for a lapangan on a specific date
     */
    public function getAvailableTimeSlots($lapanganId, $tanggal)
    {
        try {
            // Get lapangan info first
            $lapanganData = $this->getLapangan($lapanganId);
            if (!$lapanganData['success']) {
                return $lapanganData;
            }

            $lapangan = $lapanganData['data'];
            
            // Generate standard time slots based on lapangan type
            $slots = [];
            $startHour = 8; // 08:00
            $endHour = 22;  // 22:00
            
            // Determine slot duration based on lapangan type
            $slotDuration = ($lapangan['jenis'] === 'tenis_meja') ? 1 : 2;
            
            for ($hour = $startHour; $hour < $endHour; $hour += $slotDuration) {
                $jamMulai = sprintf('%02d:00', $hour);
                $jamSelesai = sprintf('%02d:00', $hour + $slotDuration);
                
                // Calculate price with prime time multiplier
                $hargaPerJam = $lapangan['harga_per_jam'];
                if ($hour >= 18 && $hour < 21) {
                    $hargaPerJam = $hargaPerJam * 1.2;
                }
                $totalHarga = $hargaPerJam * $slotDuration;
                
                $slots[] = [
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'harga' => $totalHarga,
                    'is_prime_time' => ($hour >= 18 && $hour < 21)
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'lapangan' => $lapangan,
                    'tanggal' => $tanggal,
                    'slots' => $slots
                ]
            ];

        } catch (\Exception $e) {
            Log::error("Exception when generating time slots", [
                'lapangan_id' => $lapanganId,
                'tanggal' => $tanggal,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menghasilkan slot waktu'
            ];
        }
    }
}
