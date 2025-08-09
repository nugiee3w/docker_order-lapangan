<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\LapanganService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $lapanganService;

    public function __construct(LapanganService $lapanganService)
    {
        $this->lapanganService = $lapanganService;
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     operationId="getOrdersList",
     *     tags={"Orders"},
     *     summary="Dapatkan daftar pemesanan",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query();
        if ($request->has('customer_email')) {
            $query->byCustomer($request->customer_email);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('tanggal_booking')) {
            $query->byTanggalBooking($request->tanggal_booking);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Data order berhasil diambil',
            'data' => $orders
        ]);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     operationId="storeOrder",
     *     tags={"Orders"},
     *     summary="Buat pemesanan baru",
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lapangan_id' => 'required|integer',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $lapanganData = $this->lapanganService->getLapangan($validated['lapangan_id']);
            if (!$lapanganData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan'
                ], 404);
            }

            $lapangan = $lapanganData['data'];

            $existingOrder = Order::where('lapangan_id', $validated['lapangan_id'])
                ->where(function ($query) use ($validated) {
                    $query->where('tanggal_booking', $validated['tanggal_booking'])
                          ->orWhere('booking_date', $validated['tanggal_booking']);
                })
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        $q->where(function ($subQ) use ($validated) {
                            $subQ->where('jam_mulai', '<=', $validated['jam_mulai'])
                                 ->where('jam_selesai', '>', $validated['jam_mulai']);
                        })->orWhere(function ($subQ) use ($validated) {
                            $subQ->where('start_time', '<=', $validated['jam_mulai'])
                                 ->where('end_time', '>', $validated['jam_mulai']);
                        });
                    })->orWhere(function ($q) use ($validated) {
                        $q->where(function ($subQ) use ($validated) {
                            $subQ->where('jam_mulai', '<', $validated['jam_selesai'])
                                 ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        })->orWhere(function ($subQ) use ($validated) {
                            $subQ->where('start_time', '<', $validated['jam_selesai'])
                                 ->where('end_time', '>=', $validated['jam_selesai']);
                        });
                    })->orWhere(function ($q) use ($validated) {
                        $q->where(function ($subQ) use ($validated) {
                            $subQ->where('jam_mulai', '>=', $validated['jam_mulai'])
                                 ->where('jam_selesai', '<=', $validated['jam_selesai']);
                        })->orWhere(function ($subQ) use ($validated) {
                            $subQ->where('start_time', '>=', $validated['jam_mulai'])
                                 ->where('end_time', '<=', $validated['jam_selesai']);
                        });
                    });
                })
                ->whereNotIn('status', ['cancelled'])
                ->first();

            if ($existingOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal sudah dipesan'
                ], 422);
            }

            $jamMulai = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_mulai']);
            $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_selesai']);
            $durasiJam = $jamSelesai->diffInHours($jamMulai);
            
            $hargaPerJam = $lapangan['harga_per_jam'];
            $jamMulaiHour = $jamMulai->hour;
            if ($jamMulaiHour >= 18 && $jamMulaiHour < 21) {
                $hargaPerJam = $hargaPerJam * 1.2;
            }
            
            $totalHarga = $hargaPerJam * $durasiJam;

            $order = Order::create([
                'lapangan_id' => $validated['lapangan_id'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'phone' => $validated['customer_phone'],
                'booking_date' => $validated['tanggal_booking'],
                'start_time' => $validated['jam_mulai'],
                'end_time' => $validated['jam_selesai'],
                'tanggal_booking' => $validated['tanggal_booking'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'notes' => $validated['notes'] ?? null,
                'total_harga' => $totalHarga,
                'total_price' => $totalHarga,
                'lapangan_info' => [
                    'nama' => $lapangan['nama'],
                    'jenis' => $lapangan['jenis'],
                    'lokasi' => $lapangan['lokasi']
                ]
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     operationId="showOrder",
     *     tags={"Orders"},
     *     summary="Detail pemesanan",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order detail", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data order berhasil diambil',
            'data' => $order
        ]);
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}",
     *     operationId="updateOrder",
     *     tags={"Orders"},
     *     summary="Update pemesanan",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'notes' => 'nullable|string'
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diupdate',
            'data' => $order
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/orders/{id}",
     *     operationId="deleteOrder",
     *     tags={"Orders"},
     *     summary="Hapus/Batalkan pemesanan",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak dapat dibatalkan'
            ], 422);
        }

        try {
            $order->updateStatus('cancelled');

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/orders/{id}/status",
     *     operationId="updateOrderStatus",
     *     tags={"Orders"},
     *     summary="Update status pemesanan",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Status updated", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'paid', 'cancelled', 'completed'])]
        ]);

        $order->updateStatus($validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Status order berhasil diupdate',
            'data' => $order
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/orders/{id}/payment-status",
     *     operationId="updateOrderPaymentStatus",
     *     tags={"Orders"},
     *     summary="Update status pembayaran",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Payment status updated", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function updatePaymentStatus(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(['unpaid', 'paid', 'refunded'])]
        ]);

        $order->updatePaymentStatus($validated['payment_status']);

        if ($validated['payment_status'] === 'paid' && $order->status === 'confirmed') {
            $order->updateStatus('paid');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diupdate',
            'data' => $order
        ]);
    }

    public function getAvailableTimeSlots(Request $request, string $lapanganId): JsonResponse
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        try {
            $slotsData = $this->lapanganService->getAvailableTimeSlots($lapanganId, $validated['tanggal']);
            
            if (!$slotsData['success']) {
                return response()->json($slotsData, 404);
            }

            $lapangan = $slotsData['data']['lapangan'];
            $allSlots = $slotsData['data']['slots'];

            $existingOrders = Order::where('lapangan_id', $lapanganId)
                ->where('tanggal_booking', $validated['tanggal'])
                ->whereNotIn('status', ['cancelled'])
                ->get(['jam_mulai', 'jam_selesai']);

            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $isAvailable = true;
                
                foreach ($existingOrders as $order) {
                    if (
                        ($slot['jam_mulai'] >= $order->jam_mulai && $slot['jam_mulai'] < $order->jam_selesai) ||
                        ($slot['jam_selesai'] > $order->jam_mulai && $slot['jam_selesai'] <= $order->jam_selesai) ||
                        ($slot['jam_mulai'] <= $order->jam_mulai && $slot['jam_selesai'] >= $order->jam_selesai)
                    ) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                if ($isAvailable) {
                    $availableSlots[] = $slot;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Slot waktu tersedia berhasil diambil',
                'data' => [
                    'lapangan' => $lapangan,
                    'tanggal' => $validated['tanggal'],
                    'available_slots' => $availableSlots
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil slot waktu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyAll(Request $request): JsonResponse
    {
        try {
            $count = Order::count();
            
            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada order untuk dihapus',
                'deleted_count' => 0
            ]);
        }

        Order::truncate();            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus semua order ($count order dihapus)",
                'deleted_count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_ids' => 'sometimes|array',
            'order_ids.*' => 'integer',
            'status' => 'sometimes|array',
            'status.*' => 'in:pending,confirmed,paid,cancelled,completed',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'force_delete' => 'sometimes|boolean'
        ]);

        try {
            $query = Order::query();
            $conditions = [];

            if (isset($validated['order_ids'])) {
                $query->whereIn('id', $validated['order_ids']);
                $conditions[] = 'IDs: ' . implode(', ', $validated['order_ids']);
            }

            if (isset($validated['status'])) {
                $query->whereIn('status', $validated['status']);
                $conditions[] = 'Status: ' . implode(', ', $validated['status']);
            }

            if (isset($validated['date_from'])) {
                $query->whereDate('tanggal_booking', '>=', $validated['date_from']);
                $conditions[] = 'Dari tanggal: ' . $validated['date_from'];
            }

            if (isset($validated['date_to'])) {
                $query->whereDate('tanggal_booking', '<=', $validated['date_to']);
                $conditions[] = 'Sampai tanggal: ' . $validated['date_to'];
            }

            if (empty($conditions) && !($validated['force_delete'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Untuk menghapus semua order, gunakan parameter force_delete=true atau gunakan endpoint DELETE /api/orders'
                ], 422);
            }

            $count = $query->count();
            
            if ($count === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada order yang sesuai kriteria untuk dihapus',
                    'deleted_count' => 0,
                    'conditions' => $conditions
                ]);
            }

            $deletedCount = $query->delete();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus $deletedCount order",
                'deleted_count' => $deletedCount,
                'conditions' => $conditions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrdersCount(): JsonResponse
    {
        try {
            $totalOrders = Order::count();
            $totalPaidOrders = Order::where('payment_status', 'paid')->count();
            $totalPendingOrders = Order::where('payment_status', 'pending')->count();
            $totalConfirmedOrders = Order::where('status', 'confirmed')->count();

            $totalHours = Order::selectRaw('
                SUM(
                    TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai)) / 3600
                ) as total_hours
            ')->value('total_hours') ?? 0;

            return response()->json([
                'success' => true,
                'message' => 'Data statistik order berhasil diambil',
                'data' => [
                    'total' => $totalOrders,
                    'paid' => $totalPaidOrders,
                    'pending' => $totalPendingOrders,
                    'confirmed' => $totalConfirmedOrders,
                    'total_hours' => round($totalHours, 1)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOrderStatsByLapangan($lapanganId): JsonResponse
    {
        try {
            $confirmedOrders = Order::where('lapangan_id', $lapanganId)
                ->where('status', 'confirmed')
                ->get();

            $totalPemesan = $confirmedOrders->count();
            
            $totalJamDipesan = 0;
            foreach ($confirmedOrders as $order) {
                try {
                    $jamMulaiStr = trim($order->jam_mulai);
                    $jamSelesaiStr = trim($order->jam_selesai);
                    
                    $mulaiParts = explode(':', $jamMulaiStr);
                    $selesaiParts = explode(':', $jamSelesaiStr);
                    
                    $jamMulai = (int)$mulaiParts[0];
                    $menitMulai = (int)$mulaiParts[1];
                    
                    $jamSelesai = (int)$selesaiParts[0];
                    $menitSelesai = (int)$selesaiParts[1];
                    
                    $mulaiMinutes = ($jamMulai * 60) + $menitMulai;
                    $selesaiMinutes = ($jamSelesai * 60) + $menitSelesai;
                    
                    $diffMinutes = $selesaiMinutes - $mulaiMinutes;
                    
                    if ($diffMinutes < 0) {
                        $diffMinutes += (24 * 60);
                    }
                    
                    $hours = $diffMinutes / 60;
                    $totalJamDipesan += $hours;
                    
                } catch (\Exception $timeException) {
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data statistik lapangan berhasil diambil',
                'data' => [
                    'total_pemesan' => $totalPemesan,
                    'total_jam_dipesan' => $totalJamDipesan
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik lapangan: ' . $e->getMessage()
            ], 500);
        }
    }
}
