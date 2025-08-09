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
     * @OA\Get(
     *     path="/lapangan",
     *     operationId="getLapanganList",
     *     tags={"Lapangan"},
     *     summary="Dapatkan daftar lapangan",
     *     description="Mengambil daftar semua lapangan dengan opsi filter",
     *     @OA\Parameter(
     *         name="jenis",
     *         in="query",
     *         description="Filter berdasarkan jenis lapangan",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"futsal", "badminton", "basket", "voli", "tennis"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter berdasarkan status lapangan",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"tersedia", "maintenance", "tidak_tersedia"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="tersedia",
     *         in="query",
     *         description="Filter hanya lapangan yang tersedia",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman untuk pagination",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar lapangan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data lapangan berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Lapangan")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * 
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
     * @OA\Post(
     *     path="/lapangan",
     *     operationId="storeLapangan",
     *     tags={"Lapangan"},
     *     summary="Buat lapangan baru",
     *     description="Membuat data lapangan baru dengan validasi lengkap",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"nama", "jenis", "harga_per_jam", "kapasitas"},
     *                 @OA\Property(property="nama", type="string", example="Lapangan Futsal A", description="Nama lapangan"),
     *                 @OA\Property(property="jenis", type="string", enum={"futsal", "badminton", "basket", "voli", "tennis"}, example="futsal", description="Jenis lapangan"),
     *                 @OA\Property(property="deskripsi", type="string", example="Lapangan futsal dengan rumput sintetis berkualitas tinggi", description="Deskripsi lapangan"),
     *                 @OA\Property(property="harga_per_jam", type="number", format="float", example=150000, description="Harga sewa per jam"),
     *                 @OA\Property(property="kapasitas", type="integer", example=14, description="Kapasitas maksimal pemain"),
     *                 @OA\Property(property="fasilitas", type="string", example="AC, Sound System, Kamar Ganti", description="Fasilitas yang tersedia"),
     *                 @OA\Property(property="status", type="string", enum={"tersedia", "maintenance", "tidak_tersedia"}, example="tersedia", description="Status lapangan"),
     *                 @OA\Property(property="gambar", type="string", format="binary", description="File gambar lapangan (JPEG, PNG, JPG max 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lapangan berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lapangan berhasil dibuat"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lapangan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * 
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
     * @OA\Get(
     *     path="/lapangan/{id}",
     *     operationId="getLapanganById",
     *     tags={"Lapangan"},
     *     summary="Dapatkan detail lapangan",
     *     description="Mengambil data detail lapangan berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID lapangan",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail lapangan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data lapangan berhasil diambil"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lapangan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lapangan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lapangan tidak ditemukan")
     *         )
     *     )
     * )
     * 
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
     * @OA\Put(
     *     path="/lapangan/{id}",
     *     operationId="updateLapangan",
     *     tags={"Lapangan"},
     *     summary="Update lapangan",
     *     description="Memperbarui data lapangan berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID lapangan",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nama", type="string", example="Lapangan Futsal A Updated", description="Nama lapangan"),
     *                 @OA\Property(property="jenis", type="string", enum={"futsal", "badminton", "basket", "voli", "tennis"}, example="futsal", description="Jenis lapangan"),
     *                 @OA\Property(property="deskripsi", type="string", example="Lapangan futsal dengan fasilitas terbaru", description="Deskripsi lapangan"),
     *                 @OA\Property(property="harga_per_jam", type="number", format="float", example=175000, description="Harga sewa per jam"),
     *                 @OA\Property(property="kapasitas", type="integer", example=16, description="Kapasitas maksimal pemain"),
     *                 @OA\Property(property="fasilitas", type="string", example="AC, Sound System, Kamar Ganti, WiFi", description="Fasilitas yang tersedia"),
     *                 @OA\Property(property="status", type="string", enum={"tersedia", "maintenance", "tidak_tersedia"}, example="tersedia", description="Status lapangan"),
     *                 @OA\Property(property="gambar", type="string", format="binary", description="File gambar lapangan baru (JPEG, PNG, JPG max 2MB)"),
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Method override untuk form data")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lapangan berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lapangan berhasil diperbarui"),
     *             @OA\Property(property="data", ref="#/components/schemas/Lapangan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lapangan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lapangan tidak ditemukan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * 
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
     * @OA\Delete(
     *     path="/lapangan/{id}",
     *     operationId="deleteLapangan",
     *     tags={"Lapangan"},
     *     summary="Hapus lapangan",
     *     description="Menghapus data lapangan berdasarkan ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID lapangan",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lapangan berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Lapangan berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Lapangan tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lapangan tidak ditemukan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Lapangan tidak dapat dihapus karena memiliki booking aktif",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lapangan tidak dapat dihapus karena memiliki booking aktif")
     *         )
     *     )
     * )
     * 
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
