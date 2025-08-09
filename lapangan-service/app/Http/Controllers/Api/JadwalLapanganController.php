<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalLapangan;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class JadwalLapanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = JadwalLapangan::with('lapangan');

        // Filter berdasarkan lapangan_id
        if ($request->has('lapangan_id')) {
            $query->where('lapangan_id', $request->lapangan_id);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal')) {
            $query->byTanggal($request->tanggal);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter jadwal tersedia
        if ($request->boolean('tersedia')) {
            $query->tersedia();
        }

        $jadwalLapangan = $query->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal lapangan berhasil diambil',
            'data' => $jadwalLapangan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'harga' => 'required|numeric|min:0',
            'status' => ['nullable', Rule::in(['tersedia', 'dipesan', 'sedang_digunakan', 'selesai'])]
        ]);

        // Check if schedule already exists
        $existingSchedule = JadwalLapangan::where('lapangan_id', $validated['lapangan_id'])
            ->where('tanggal', $validated['tanggal'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhere(function ($subQuery) use ($validated) {
                        $subQuery->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                    });
            })
            ->exists();

        if ($existingSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal bertabrakan dengan jadwal yang sudah ada'
            ], 422);
        }

        $jadwalLapangan = JadwalLapangan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal lapangan berhasil dibuat',
            'data' => $jadwalLapangan->load('lapangan')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $jadwalLapangan = JadwalLapangan::with('lapangan')->find($id);

        if (!$jadwalLapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal lapangan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data jadwal lapangan berhasil diambil',
            'data' => $jadwalLapangan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $jadwalLapangan = JadwalLapangan::find($id);

        if (!$jadwalLapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal lapangan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'tanggal' => 'sometimes|date|after_or_equal:today',
            'jam_mulai' => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
            'harga' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(['tersedia', 'dipesan', 'sedang_digunakan', 'selesai'])]
        ]);

        $jadwalLapangan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal lapangan berhasil diupdate',
            'data' => $jadwalLapangan->load('lapangan')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $jadwalLapangan = JadwalLapangan::find($id);

        if (!$jadwalLapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal lapangan tidak ditemukan'
            ], 404);
        }

        // Check if jadwal is already booked
        if ($jadwalLapangan->status === 'dipesan' || $jadwalLapangan->status === 'sedang_digunakan') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus jadwal yang sudah dipesan'
            ], 422);
        }

        $jadwalLapangan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal lapangan berhasil dihapus'
        ]);
    }

    /**
     * Book a schedule (change status to dipesan)
     */
    public function bookSchedule(string $id): JsonResponse
    {
        $jadwalLapangan = JadwalLapangan::find($id);

        if (!$jadwalLapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal lapangan tidak ditemukan'
            ], 404);
        }

        if ($jadwalLapangan->status !== 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak tersedia untuk dipesan'
            ], 422);
        }

        $jadwalLapangan->bookSchedule();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dipesan',
            'data' => $jadwalLapangan->load('lapangan')
        ]);
    }

    /**
     * Release a schedule (change status to tersedia)
     */
    public function releaseSchedule(string $id): JsonResponse
    {
        $jadwalLapangan = JadwalLapangan::find($id);

        if (!$jadwalLapangan) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal lapangan tidak ditemukan'
            ], 404);
        }

        $jadwalLapangan->releaseSchedule();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibebaskan',
            'data' => $jadwalLapangan->load('lapangan')
        ]);
    }
}
