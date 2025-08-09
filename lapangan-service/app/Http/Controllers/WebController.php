<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\JadwalLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WebController extends Controller
{
    public function dashboard()
    {
        try {
            $totalLapangan = Lapangan::count();
        } catch (Exception $e) {
            $totalLapangan = 0;
            Log::warning('Cannot count lapangan: ' . $e->getMessage());
        }
        
        // Simplified - skip order service for now
        $totalOrders = 0;
        $totalHours = 0;
        
        return view('dashboard', compact(
            'totalLapangan', 
            'totalOrders',
            'totalHours'
        ));
    }

    private function getOrdersFromService()
    {
        try {
            // Call order service API to get total orders (use port 80 inside Docker network)
            $response = file_get_contents('http://order-service:80/api/orders/count');
            $data = json_decode($response, true);
            
            if ($data && isset($data['success']) && $data['success']) {
                return $data['data'] ?? [];
            }
        } catch (Exception $e) {
            // If order service is not available, fallback to localhost for development
            try {
                $response = file_get_contents('http://localhost:8000/api/orders/count');
                $data = json_decode($response, true);
                
                if ($data && isset($data['success']) && $data['success']) {
                    return $data['data'] ?? [];
                }
            } catch (Exception $fallbackException) {
                Log::warning('Cannot connect to order service: ' . $e->getMessage());
            }
        }
        
        return [];
    }

    public function lapangan()
    {
        $lapangans = Lapangan::with('jadwalLapangans')->orderBy('created_at', 'desc')->get();
        
        // Enrich each lapangan with order statistics from order service
        foreach ($lapangans as $lapangan) {
            $orderStats = $this->getOrderStatsByLapangan($lapangan->id);
            $lapangan->order_stats = $orderStats;
        }
        
        return view('lapangan.index', compact('lapangans'));
    }

    private function getOrderStatsByLapangan($lapanganId)
    {
        try {
            // Try to get order statistics from order service
            $url = "http://order-service:80/api/orders/stats/lapangan/{$lapanganId}";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if ($data && isset($data['success']) && $data['success']) {
                return $data['data'] ?? [];
            }
        } catch (Exception $e) {
            // Fallback to localhost for development
            try {
                $url = "http://localhost:8000/api/orders/stats/lapangan/{$lapanganId}";
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                
                if ($data && isset($data['success']) && $data['success']) {
                    return $data['data'] ?? [];
                }
            } catch (Exception $fallbackException) {
                Log::warning("Cannot get order stats for lapangan {$lapanganId}: " . $e->getMessage());
            }
        }
        
        // Return empty stats if service is unavailable
        return [
            'total_pemesan' => 0,
            'total_jam_dipesan' => 0
        ];
    }

    public function lapanganCreate()
    {
        return view('lapangan.create');
    }

    public function lapanganEdit($id)
    {
        // We'll load the data via AJAX in the view
        return view('lapangan.edit');
    }

    public function jadwal(Request $request)
    {
        $query = JadwalLapangan::with('lapangan');
        
        // Filter by lapangan if specified
        if ($request->filled('lapangan')) {
            $query->where('lapangan_id', $request->lapangan);
        }
        
        if ($request->filled('jenis')) {
            $query->whereHas('lapangan', function($q) use ($request) {
                $q->where('jenis', $request->jenis);
            });
        }
        
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jadwals = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();
        
        // Get unique jenis for filter
        $jenisOptions = Lapangan::distinct()->pluck('jenis');
        
        // Get lapangan for filter (if not already filtered)
        $lapanganOptions = Lapangan::select('id', 'nama', 'jenis')->orderBy('nama')->get();
        
        return view('jadwal.index', compact('jadwals', 'jenisOptions', 'lapanganOptions'));
    }
}
