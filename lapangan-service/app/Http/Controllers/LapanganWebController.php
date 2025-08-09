<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LapanganWebController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Futsal,Badminton,Basket,Tenis,Voli',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,maintenance,tidak_tersedia',
            'fasilitas' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        // Process fasilitas - convert string to array properly
        if (isset($validated['fasilitas']) && !empty($validated['fasilitas'])) {
            $validated['fasilitas'] = array_map('trim', explode(',', $validated['fasilitas']));
        } else {
            $validated['fasilitas'] = [];
        }

        // Set default lokasi if not provided
        $validated['lokasi'] = $validated['lokasi'] ?? 'Tidak ditentukan';

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lapangan', $filename, 'public');
            $validated['gambar'] = $path;
        }

        $lapangan = Lapangan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil ditambahkan!',
            'data' => $lapangan
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $lapangan = Lapangan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:Futsal,Badminton,Basket,Tenis,Voli',
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,maintenance,tidak_tersedia',
            'fasilitas' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        // Process fasilitas - convert string to array properly
        if (isset($validated['fasilitas']) && !empty($validated['fasilitas'])) {
            $validated['fasilitas'] = array_map('trim', explode(',', $validated['fasilitas']));
        } else {
            $validated['fasilitas'] = [];
        }

        // Set default lokasi if not provided
        $validated['lokasi'] = $validated['lokasi'] ?? $lapangan->lokasi ?? 'Tidak ditentukan';

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($lapangan->gambar && Storage::disk('public')->exists($lapangan->gambar)) {
                Storage::disk('public')->delete($lapangan->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('lapangan', $filename, 'public');
            $validated['gambar'] = $path;
        } else {
            // Remove gambar from validated data if no new file is uploaded
            unset($validated['gambar']);
        }

        $lapangan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil diupdate!',
            'data' => $lapangan->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $lapangan = Lapangan::findOrFail($id);

        // Delete associated image
        if ($lapangan->gambar && Storage::disk('public')->exists($lapangan->gambar)) {
            Storage::disk('public')->delete($lapangan->gambar);
        }

        $lapangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil dihapus!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $lapangan = Lapangan::with('jadwalLapangans')->findOrFail($id);
        
        // Get order statistics from order service
        $orderStats = $this->getOrderStatsByLapangan($id);
        $lapangan->order_stats = $orderStats;

        return response()->json([
            'success' => true,
            'message' => 'Detail lapangan berhasil diambil',
            'data' => $lapangan
        ]);
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
        } catch (\Exception $e) {
            // Fallback to localhost for development
            try {
                $url = "http://localhost:8000/api/orders/stats/lapangan/{$lapanganId}";
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                
                if ($data && isset($data['success']) && $data['success']) {
                    return $data['data'] ?? [];
                }
            } catch (\Exception $fallbackException) {
                Log::warning("Cannot get order stats for lapangan {$lapanganId}: " . $e->getMessage());
            }
        }
        
        // Return empty stats if service is unavailable
        return [
            'total_pemesan' => 0,
            'total_jam_dipesan' => 0
        ];
    }
}
