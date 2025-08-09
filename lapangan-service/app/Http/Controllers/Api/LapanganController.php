<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Lapangan::query();

        // Filter berdasarkan jenis lapangan
        if ($request->has('jenis')) {
            $query->byJenis($request->jenis);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter lapangan yang tersedia
        if ($request->boolean('tersedia')) {
            $query->tersedia();
        }

        $lapangans = $query->with('jadwalLapangan')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data lapangan berhasil diambil',
            'data' => $lapangans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => ['required', Rule::in(['futsal', 'badminton', 'basket', 'tenis_meja'])],
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => ['nullable', Rule::in(['tersedia', 'tidak_tersedia', 'maintenance'])],
            'fasilitas' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        // Process fasilitas from string to array
        if (isset($validated['fasilitas']) && is_string($validated['fasilitas'])) {
            $validated['fasilitas'] = array_map('trim', explode(',', $validated['fasilitas']));
        }

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('lapangan', $imageName, 'public');
            $validated['gambar'] = $imagePath;
        }

        $lapangan = Lapangan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil dibuat',
            'data' => $lapangan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $lapangan = Lapangan::with('jadwalLapangan')->find($id);

        if (!$lapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data lapangan berhasil diambil',
            'data' => $lapangan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $lapangan = Lapangan::find($id);

        if (!$lapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => ['sometimes', Rule::in(['futsal', 'badminton', 'basket', 'tenis_meja'])],
            'deskripsi' => 'nullable|string',
            'harga_per_jam' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(['tersedia', 'tidak_tersedia', 'maintenance'])],
            'fasilitas' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        // Process fasilitas from string to array
        if (isset($validated['fasilitas']) && is_string($validated['fasilitas'])) {
            $validated['fasilitas'] = array_map('trim', explode(',', $validated['fasilitas']));
        }

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($lapangan->gambar && Storage::disk('public')->exists($lapangan->gambar)) {
                Storage::disk('public')->delete($lapangan->gambar);
            }

            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('lapangan', $imageName, 'public');
            $validated['gambar'] = $imagePath;
        }

        $lapangan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil diupdate',
            'data' => $lapangan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $lapangan = Lapangan::find($id);

        if (!$lapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan tidak ditemukan'
            ], 404);
        }

        // Delete associated image if exists
        if ($lapangan->gambar && Storage::disk('public')->exists($lapangan->gambar)) {
            Storage::disk('public')->delete($lapangan->gambar);
        }

        $lapangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lapangan berhasil dihapus'
        ]);
    }

    /**
     * Get available schedule for specific lapangan and date
     */
    public function getAvailableSchedule(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        $lapangan = Lapangan::find($id);

        if (!$lapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Lapangan tidak ditemukan'
            ], 404);
        }

        $availableSchedule = $lapangan->getAvailableSchedule($request->tanggal);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal tersedia berhasil diambil',
            'data' => [
                'lapangan' => $lapangan,
                'tanggal' => $request->tanggal,
                'jadwal_tersedia' => $availableSchedule
            ]
        ]);
    }
}
