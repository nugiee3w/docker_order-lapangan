<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangans = [];
        
        try {
            // Coba ambil data dari lapangan service via Docker network name
            $response = Http::timeout(10)->get('http://lapangan-service:80/api/lapangan');
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                    // Cek apakah data terpaginasi
                    if (isset($data['data']['data'])) {
                        $lapangans = $data['data']['data']; // Paginated data
                    } else {
                        $lapangans = $data['data']; // Direct array data
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch lapangan data', ['error' => $e->getMessage()]);
            
            // Jika gagal dengan Docker network, coba localhost sebagai fallback
            try {
                $response = Http::timeout(10)->get('http://localhost:8001/api/lapangan');
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                        // Cek apakah data terpaginasi
                        if (isset($data['data']['data'])) {
                            $lapangans = $data['data']['data']; // Paginated data
                        } else {
                            $lapangans = $data['data']; // Direct array data
                        }
                    }
                }
            } catch (\Exception $e2) {
                Log::error('Failed to fetch lapangan data from localhost too', ['error' => $e2->getMessage()]);
            }
        }
        
        // Data dummy sebagai fallback jika API gagal
        $dummyLapangans = [
            [
                'id' => 999,
                'nama' => 'Lapangan Dummy A',
                'jenis' => 'futsal',
                'deskripsi' => 'Data dummy - API tidak tersedia',
                'harga_per_jam' => '150000',
                'lokasi' => 'Gedung Dummy',
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Toilet', 'Parkir', 'Kantin'],
                'image' => 'https://source.unsplash.com/800x600/?soccer-field&fit=crop&auto=format&q=80&sig=999'
            ]
        ];
        
        // Jika gagal mengambil data dari service, gunakan data dummy
        if (empty($lapangans)) {
            $lapangans = $dummyLapangans;
        }
        
        // Pastikan $lapangans adalah array
        if (!is_array($lapangans)) {
            $lapangans = $dummyLapangans;
        }
        
        // Convert harga_per_jam ke integer jika berupa string dan tambahkan gambar Unsplash
        foreach ($lapangans as &$lapangan) {
            if (isset($lapangan['harga_per_jam']) && is_string($lapangan['harga_per_jam'])) {
                $lapangan['harga_per_jam'] = (int) $lapangan['harga_per_jam'];
            }
            
            // Tambahkan gambar Unsplash jika belum ada
            if (!isset($lapangan['image']) || empty($lapangan['image'])) {
                $lapangan['image'] = $this->getUnsplashImageByJenis($lapangan['jenis'] ?? 'futsal', $lapangan['id'] ?? rand(1, 1000));
            }
        }
        
        // Ambil data pesanan untuk setiap lapangan
        foreach ($lapangans as $key => &$lapangan) {
            if (is_array($lapangan) && isset($lapangan['id'])) {
                $orders = Order::where('lapangan_id', $lapangan['id'])
                    ->orderBy('tanggal_booking', 'desc')
                    ->take(5)
                    ->get();
                
                $lapangan['recent_orders'] = $orders;
                $lapangan['total_orders'] = Order::where('lapangan_id', $lapangan['id'])->count();
            } else {
                // Hapus elemen yang tidak valid
                unset($lapangans[$key]);
            }
        }
        
        return view('lapangan.index', compact('lapangans'));
    }
    
    public function show($id)
    {
        try {
            $lapangan = null;
            
            // Coba ambil detail lapangan dari lapangan service via Docker network
            try {
                $response = Http::timeout(10)->get("http://lapangan-service:80/api/lapangan/{$id}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                        $lapangan = $data['data'];
                    }
                }
            } catch (\Exception $e) {
                // Fallback ke localhost jika gagal
                try {
                    $response = Http::timeout(10)->get("http://localhost:8001/api/lapangan/{$id}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                            $lapangan = $data['data'];
                        }
                    }
                } catch (\Exception $e2) {
                    Log::error('Failed to fetch lapangan detail', ['id' => $id, 'docker_error' => $e->getMessage(), 'localhost_error' => $e2->getMessage()]);
                }
            }
            
            if ($lapangan) {
                // Tambahkan gambar Unsplash jika belum ada
                if (!isset($lapangan['image']) || empty($lapangan['image'])) {
                    $lapangan['image'] = $this->getUnsplashImageByJenis($lapangan['jenis'] ?? 'futsal', $lapangan['id'] ?? rand(1, 1000));
                }
                
                // Ambil semua pesanan untuk lapangan ini
                $orders = Order::where('lapangan_id', $id)
                    ->orderBy('tanggal_booking', 'desc')
                    ->paginate(10);
                
                return view('lapangan.show', compact('lapangan', 'orders'));
            }
            
            return redirect()->route('lapangan.index')->with('error', 'Lapangan tidak ditemukan');
            
        } catch (\Exception $e) {
            Log::error('LapanganController show error', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('lapangan.index')->with('error', 'Gagal mengambil detail lapangan: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate Unsplash image URL based on lapangan type
     */
    private function getUnsplashImageByJenis($jenis, $seed = null)
    {
        $seed = $seed ?? rand(1, 1000);
        
        $searchTerms = [
            'futsal' => 'soccer-field',
            'badminton' => 'badminton-court',
            'basket' => 'basketball-court',
            'voli' => 'volleyball-court',
            'tennis' => 'tennis-court'
        ];
        
        $searchTerm = $searchTerms[$jenis] ?? 'sports-field';
        
        // Menggunakan Unsplash Source API dengan parameter yang lebih spesifik
        return "https://source.unsplash.com/800x600/?{$searchTerm}&fit=crop&auto=format&q=80&sig={$seed}";
    }
}
