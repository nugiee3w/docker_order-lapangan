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
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query();

        // Filter berdasarkan customer email
        if ($request->has('customer_email')) {
            $query->byCustomer($request->customer_email);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter berdasarkan tanggal booking
        if ($request->has('tanggal_booking')) {
            $query->byTanggalBooking($request->tanggal_booking);
        }

        // Filter berdasarkan payment status
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
     * Store a newly created resource in storage.
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

            // Verify lapangan exists via lapangan service
            $lapanganData = $this->lapanganService->getLapangan($validated['lapangan_id']);
            if (!$lapanganData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lapangan tidak ditemukan'
                ], 404);
            }

            $lapangan = $lapanganData['data'];

            // Check if there's already a booking for this lapangan at the same time
            $existingOrder = Order::where('lapangan_id', $validated['lapangan_id'])
                ->where(function ($query) use ($validated) {
                    $query->where('tanggal_booking', $validated['tanggal_booking'])
                          ->orWhere('booking_date', $validated['tanggal_booking']);
                })
                ->where(function ($query) use ($validated) {
                    $query->where(function ($q) use ($validated) {
                        // Case 1: New booking starts during existing booking
                        $q->where(function ($subQ) use ($validated) {
                            $subQ->where('jam_mulai', '<=', $validated['jam_mulai'])
                                 ->where('jam_selesai', '>', $validated['jam_mulai']);
                        })->orWhere(function ($subQ) use ($validated) {
                            $subQ->where('start_time', '<=', $validated['jam_mulai'])
                                 ->where('end_time', '>', $validated['jam_mulai']);
                        });
                    })->orWhere(function ($q) use ($validated) {
                        // Case 2: New booking ends during existing booking
                        $q->where(function ($subQ) use ($validated) {
                            $subQ->where('jam_mulai', '<', $validated['jam_selesai'])
                                 ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        })->orWhere(function ($subQ) use ($validated) {
                            $subQ->where('start_time', '<', $validated['jam_selesai'])
                                 ->where('end_time', '>=', $validated['jam_selesai']);
                        });
                    })->orWhere(function ($q) use ($validated) {
                        // Case 3: Existing booking is completely within new booking
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

            // Calculate total price based on lapangan rate and duration
            $jamMulai = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_mulai']);
            $jamSelesai = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_selesai']);
            $durasiJam = $jamSelesai->diffInHours($jamMulai);
            
            // Apply prime time multiplier (18:00 - 21:00) - 20% extra
            $hargaPerJam = $lapangan['harga_per_jam'];
            $jamMulaiHour = $jamMulai->hour;
            if ($jamMulaiHour >= 18 && $jamMulaiHour < 21) {
                $hargaPerJam = $hargaPerJam * 1.2;
            }
            
            $totalHarga = $hargaPerJam * $durasiJam;

            // Create order - menggunakan field database yang sebenarnya
            $order = Order::create([
                'lapangan_id' => $validated['lapangan_id'],
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'phone' => $validated['customer_phone'], // untuk kompatibilitas dengan tabel
                'booking_date' => $validated['tanggal_booking'], // mapping ke field database
                'start_time' => $validated['jam_mulai'],          // mapping ke field database
                'end_time' => $validated['jam_selesai'],          // mapping ke field database
                'tanggal_booking' => $validated['tanggal_booking'], // tetap ada untuk kompatibilitas
                'jam_mulai' => $validated['jam_mulai'],             // tetap ada untuk kompatibilitas
                'jam_selesai' => $validated['jam_selesai'],         // tetap ada untuk kompatibilitas
                'notes' => $validated['notes'] ?? null,
                'total_harga' => $totalHarga,
                'total_price' => $totalHarga, // untuk kompatibilitas dengan tabel
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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

        // Only allow cancellation if order is pending or confirmed
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak dapat dibatalkan'
            ], 422);
        }

        try {
            // Update order status to cancelled
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
     * Update order status
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
     * Update payment status
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

        // Auto update order status if payment is completed
        if ($validated['payment_status'] === 'paid' && $order->status === 'confirmed') {
            $order->updateStatus('paid');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diupdate',
            'data' => $order
        ]);
    }

    /**
     * Get available time slots for a lapangan on a specific date
     */
    public function getAvailableTimeSlots(Request $request, string $lapanganId): JsonResponse
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        try {
            // Get available time slots from lapangan service
            $slotsData = $this->lapanganService->getAvailableTimeSlots($lapanganId, $validated['tanggal']);
            
            if (!$slotsData['success']) {
                return response()->json($slotsData, 404);
            }

            $lapangan = $slotsData['data']['lapangan'];
            $allSlots = $slotsData['data']['slots'];

            // Get existing orders for this lapangan on this date
            $existingOrders = Order::where('lapangan_id', $lapanganId)
                ->where('tanggal_booking', $validated['tanggal'])
                ->whereNotIn('status', ['cancelled'])
                ->get(['jam_mulai', 'jam_selesai']);

            // Filter out unavailable slots
            $availableSlots = [];
            foreach ($allSlots as $slot) {
                $isAvailable = true;
                
                foreach ($existingOrders as $order) {
                    // Check if slot conflicts with existing order
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

    /**
     * Delete all orders (Admin only)
     */
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

            // Delete all orders
            Order::truncate();

            return response()->json([
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

    /**
     * Bulk delete orders by IDs or conditions (Admin only)
     */
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

            // Filter by specific order IDs
            if (isset($validated['order_ids'])) {
                $query->whereIn('id', $validated['order_ids']);
                $conditions[] = 'IDs: ' . implode(', ', $validated['order_ids']);
            }

            // Filter by status
            if (isset($validated['status'])) {
                $query->whereIn('status', $validated['status']);
                $conditions[] = 'Status: ' . implode(', ', $validated['status']);
            }

            // Filter by date range
            if (isset($validated['date_from'])) {
                $query->whereDate('tanggal_booking', '>=', $validated['date_from']);
                $conditions[] = 'Dari tanggal: ' . $validated['date_from'];
            }

            if (isset($validated['date_to'])) {
                $query->whereDate('tanggal_booking', '<=', $validated['date_to']);
                $conditions[] = 'Sampai tanggal: ' . $validated['date_to'];
            }

            // If no specific conditions, require force_delete flag
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

            // Delete the orders
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

    /**
     * Get total orders count for dashboard statistics
     */
    public function getOrdersCount(): JsonResponse
    {
        try {
            $totalOrders = Order::count();
            $totalPaidOrders = Order::where('payment_status', 'paid')->count();
            $totalPendingOrders = Order::where('payment_status', 'pending')->count();
            $totalConfirmedOrders = Order::where('status', 'confirmed')->count();

            // Calculate total hours booked
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

    /**
     * Get order statistics for specific lapangan
     */
    public function getOrderStatsByLapangan($lapanganId): JsonResponse
    {
        try {
            // Get confirmed orders for this lapangan
            $confirmedOrders = Order::where('lapangan_id', $lapanganId)
                ->where('status', 'confirmed')
                ->get();

            $totalPemesan = $confirmedOrders->count();
            
            // Calculate total hours for confirmed orders
            $totalJamDipesan = 0;
            foreach ($confirmedOrders as $order) {
                try {
                    // Extract time components using simple string manipulation
                    $jamMulaiStr = trim($order->jam_mulai);
                    $jamSelesaiStr = trim($order->jam_selesai);
                    
                    // Parse the time string (e.g., "06:00:00" or "08:00:00")
                    $mulaiParts = explode(':', $jamMulaiStr);
                    $selesaiParts = explode(':', $jamSelesaiStr);
                    
                    $jamMulai = (int)$mulaiParts[0];
                    $menitMulai = (int)$mulaiParts[1];
                    
                    $jamSelesai = (int)$selesaiParts[0];
                    $menitSelesai = (int)$selesaiParts[1];
                    
                    // Convert to minutes from midnight
                    $mulaiMinutes = ($jamMulai * 60) + $menitMulai;
                    $selesaiMinutes = ($jamSelesai * 60) + $menitSelesai;
                    
                    // Calculate difference
                    $diffMinutes = $selesaiMinutes - $mulaiMinutes;
                    
                    // If negative, assume next day (24-hour wrap)
                    if ($diffMinutes < 0) {
                        $diffMinutes += (24 * 60);
                    }
                    
                    // Convert to hours
                    $hours = $diffMinutes / 60;
                    $totalJamDipesan += $hours;
                    
                } catch (\Exception $timeException) {
                    // Skip this order if time parsing fails
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
