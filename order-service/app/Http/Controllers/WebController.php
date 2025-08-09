<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\LapanganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebController extends Controller
{
    private const DEFAULT_PAGINATION_SIZE = 15;
    private const DASHBOARD_RECENT_ORDERS_LIMIT = 10;
    private const ORDER_STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];
    private const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];
    
    private const OPERATING_HOURS = [
        '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', 
        '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'
    ];

    private LapanganService $lapanganService;

    public function __construct(LapanganService $lapanganService)
    {
        $this->lapanganService = $lapanganService;
    }

    public function dashboard()
    {
        $stats = $this->getDashboardStatistics();
        $recentOrders = $this->getRecentOrdersWithLapanganInfo();

        return view('dashboard', compact('stats', 'recentOrders'));
    }

    private function getDashboardStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('status', 'confirmed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'confirmed')->sum('total_harga'),
            'revenue_today' => Order::where('status', 'confirmed')
                                   ->whereDate('created_at', today())
                                   ->sum('total_harga')
        ];
    }

    private function getRecentOrdersWithLapanganInfo()
    {
        $recentOrders = $this->getRecentOrders();
        
        foreach ($recentOrders as $order) {
            $order->lapangan_info = $this->getLapanganInfoForOrder($order);
        }

        return $recentOrders;
    }

    private function getRecentOrders()
    {
        $upcomingOrders = Order::where('tanggal_booking', '>=', Carbon::now()->toDateString())
                               ->orderBy('tanggal_booking', 'asc')
                               ->orderBy('jam_mulai', 'asc')
                               ->take(self::DASHBOARD_RECENT_ORDERS_LIMIT)
                               ->get();

        if ($upcomingOrders->count() < self::DASHBOARD_RECENT_ORDERS_LIMIT) {
            $pastOrders = Order::where('tanggal_booking', '<', Carbon::now()->toDateString())
                               ->orderBy('tanggal_booking', 'desc')
                               ->orderBy('jam_mulai', 'desc')
                               ->take(self::DASHBOARD_RECENT_ORDERS_LIMIT - $upcomingOrders->count())
                               ->get();
            
            return $upcomingOrders->merge($pastOrders);
        }

        return $upcomingOrders;
    }

    public function orders(Request $request)
    {
        $query = $this->buildOrdersQuery($request);
        $orders = $this->paginateOrdersWithSorting($query);
        
        $this->attachLapanganInfoToOrders($orders);
        
        return view('orders.index', compact('orders'));
    }

    private function buildOrdersQuery(Request $request)
    {
        $query = Order::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $this->addSearchFilters($query, $request->search);
        }

        return $query;
    }

    private function addSearchFilters($query, string $search): void
    {
        $query->where(function($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('order_number', 'like', "%{$search}%");
        });
    }

    private function paginateOrdersWithSorting($query)
    {
        return $query->orderByRaw("
            CASE 
                WHEN tanggal_booking >= CURDATE() THEN 0 
                ELSE 1 
            END,
            tanggal_booking ASC,
            jam_mulai ASC
        ")->paginate(self::DEFAULT_PAGINATION_SIZE);
    }

    private function attachLapanganInfoToOrders($orders): void
    {
        foreach ($orders as $order) {
            $order->lapangan_info = $this->getLapanganInfoForOrder($order);
        }
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        
        Log::info("Showing order ID: {$id}, Lapangan ID: {$order->lapangan_id}");
        
        $order->lapangan_info = $this->getLapanganInfoForOrder($order);
        
        return view('orders.show', compact('order'));
    }

    private function getLapanganInfoForOrder(Order $order): array
    {
        if (!$order->lapangan_id) {
            return $this->getDefaultLapanganInfo('ID lapangan tidak tersedia');
        }

        $lapanganData = $this->lapanganService->getLapangan($order->lapangan_id);
        
        if (!isset($lapanganData['data'])) {
            return $this->getDefaultLapanganInfo('Data lapangan tidak ditemukan');
        }

        return $this->formatLapanganInfo($lapanganData['data']);
    }

    private function getDefaultLapanganInfo(string $namaOverride = 'N/A'): array
    {
        return [
            'nama' => $namaOverride,
            'jenis' => 'N/A',
            'lokasi' => 'N/A',
            'harga_per_jam' => 0,
            'fasilitas' => [],
            'status' => 'N/A'
        ];
    }

    private function formatLapanganInfo(array $lapangan): array
    {
        $fasilitas = $lapangan['fasilitas'] ?? '';
        if (is_string($fasilitas)) {
            $fasilitas = json_decode($fasilitas, true) ?? [];
        }

        return [
            'nama' => $lapangan['nama'] ?? 'N/A',
            'jenis' => ucfirst($lapangan['jenis'] ?? 'N/A'),
            'lokasi' => $lapangan['lokasi'] ?? 'N/A',
            'harga_per_jam' => (float)($lapangan['harga_per_jam'] ?? 0),
            'fasilitas' => $fasilitas,
            'status' => ucfirst($lapangan['status'] ?? 'N/A')
        ];
    }
    
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $lapangan_list = $this->getLapanganList();
        
        return view('orders.edit', compact('order', 'lapangan_list'));
    }

    public function create(Request $request)
    {
        $lapangan_list = $this->getLapanganList();
        $selectedLapanganId = $request->get('lapangan_id');
        
        return view('orders.create', compact('lapangan_list', 'selectedLapanganId'));
    }

    private function getLapanganList(): array
    {
        $lapanganResponse = $this->lapanganService->getAllLapangan();
        
        if (!isset($lapanganResponse['data'])) {
            Log::warning('Failed to get lapangan list from service', ['response' => $lapanganResponse]);
            return [];
        }

        // Handle both paginated and direct array responses
        return isset($lapanganResponse['data']['data']) 
            ? $lapanganResponse['data']['data'] 
            : $lapanganResponse['data'];
    }

    public function store(Request $request)
    {
        $validated = $this->validateOrderRequest($request);
        
        $conflictingOrder = $this->checkScheduleConflict(
            $validated['lapangan_id'],
            $validated['tanggal_booking'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($conflictingOrder) {
            return $this->redirectWithScheduleConflictError($conflictingOrder);
        }

        $orderData = $this->prepareOrderData($validated);
        $order = Order::create($orderData);

        return redirect()->route('orders.show', $order->id)
                        ->with('success', 'Order berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $this->validateOrderRequest($request);

        if ($this->hasScheduleChanged($order, $validated)) {
            $conflictingOrder = $this->checkScheduleConflictForUpdate(
                $validated['lapangan_id'],
                $validated['tanggal_booking'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $order->id
            );

            if ($conflictingOrder) {
                return $this->redirectWithScheduleConflictError($conflictingOrder);
            }
        }

        $updateData = $this->prepareOrderData($validated);
        $order->update($updateData);

        return redirect()->route('orders.show', $order->id)
                        ->with('success', 'Order berhasil diupdate!');
    }

    private function validateOrderRequest(Request $request): array
    {
        return $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'lapangan_id' => 'required|integer',
            'tanggal_booking' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', self::ORDER_STATUSES),
            'payment_status' => 'required|in:' . implode(',', self::PAYMENT_STATUSES),
            'notes' => 'nullable|string'
        ]);
    }

    private function hasScheduleChanged(Order $order, array $validated): bool
    {
        return $order->lapangan_id != $validated['lapangan_id'] ||
               $order->tanggal_booking != $validated['tanggal_booking'] ||
               $order->jam_mulai != $validated['jam_mulai'] ||
               $order->jam_selesai != $validated['jam_selesai'];
    }

    private function prepareOrderData(array $validated): array
    {
        $orderData = $validated;
        
        // Generate order number for new orders
        if (!isset($orderData['order_number'])) {
            $orderData['order_number'] = $this->generateOrderNumber();
        }
        
        // Set default value for jadwal_lapangan_id if not provided
        $orderData['jadwal_lapangan_id'] = $orderData['jadwal_lapangan_id'] ?? 1;
        
        // Map fields for database compatibility
        $orderData['booking_date'] = $validated['tanggal_booking'];
        $orderData['start_time'] = $validated['jam_mulai'];
        $orderData['end_time'] = $validated['jam_selesai'];
        $orderData['phone'] = $validated['customer_phone'];
        $orderData['total_price'] = $validated['total_harga'];

        return $orderData;
    }

    private function generateOrderNumber(): string
    {
        $todayOrderCount = Order::whereDate('created_at', today())->count();
        return 'ORD-' . date('Ymd') . '-' . str_pad($todayOrderCount + 1, 3, '0', STR_PAD_LEFT);
    }

    private function redirectWithScheduleConflictError($conflictingOrder)
    {
        $errorMessage = sprintf(
            'Jadwal lapangan sudah dibooking oleh %s pada tanggal %s jam %s - %s. Silakan pilih waktu lain.',
            $conflictingOrder->customer_name,
            $conflictingOrder->tanggal_booking->format('d/m/Y'),
            $conflictingOrder->jam_mulai,
            $conflictingOrder->jam_selesai
        );

        return redirect()->back()
                        ->withInput()
                        ->withErrors(['jam_mulai' => $errorMessage]);
    }

    public function destroy($id)
    {
        Log::info("Delete request received for order ID: {$id}");
        
        try {
            $order = Order::findOrFail($id);
            $orderNumber = $order->order_number;
            
            Log::info("Order found: {$orderNumber}, Status: {$order->status}, Payment: {$order->payment_status}");
            
            if ($this->cannotDeleteOrder($order)) {
                Log::warning("Delete blocked - Order {$orderNumber} is confirmed and paid");
                return redirect()->route('orders.index')
                    ->with('error', "Order {$orderNumber} tidak dapat dihapus karena sudah dikonfirmasi dan dibayar. Silakan batalkan order terlebih dahulu.");
            }
            
            $order->delete();
            Log::info("Order {$orderNumber} successfully deleted");

            return redirect()->route('orders.index')
                ->with('success', "Order {$orderNumber} berhasil dihapus!");
                
        } catch (\Exception $e) {
            Log::error('Error deleting order: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('orders.index')
                ->with('error', 'Terjadi kesalahan saat menghapus order. Silakan coba lagi.');
        }
    }

    private function cannotDeleteOrder(Order $order): bool
    {
        return $order->status === 'confirmed' && $order->payment_status === 'paid';
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info("UpdateStatus request received for order ID: {$id}", $request->all());
        
        try {
            $order = Order::findOrFail($id);
            Log::info("Order found: {$order->order_number}, Current Status: {$order->status}, Current Payment: {$order->payment_status}");
            
            $validationRules = $this->buildStatusUpdateValidationRules($request);
            $updateData = $this->buildStatusUpdateData($request);
            
            $validated = $request->validate($validationRules);
            $order->update($updateData);
            
            Log::info("Order {$order->order_number} updated successfully", $updateData);
            
            $message = $this->generateStatusUpdateMessage($updateData);
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate status order. Silakan coba lagi.');
        }
    }

    private function buildStatusUpdateValidationRules(Request $request): array
    {
        $rules = [];
        
        if ($request->has('status')) {
            $rules['status'] = 'required|in:' . implode(',', self::ORDER_STATUSES);
        }
        
        if ($request->has('payment_status')) {
            $rules['payment_status'] = 'required|in:' . implode(',', self::PAYMENT_STATUSES);
        }
        
        if ($request->has('notes')) {
            $rules['notes'] = 'nullable|string|max:500';
        }
        
        return $rules;
    }

    private function buildStatusUpdateData(Request $request): array
    {
        $updateData = [];
        
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }
        
        if ($request->has('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
        }
        
        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }
        
        return $updateData;
    }

    private function generateStatusUpdateMessage(array $updateData): string
    {
        if (isset($updateData['status']) && isset($updateData['payment_status'])) {
            return "Status order berhasil diubah menjadi {$updateData['status']} dan payment status menjadi {$updateData['payment_status']}!";
        } elseif (isset($updateData['status'])) {
            return "Status order berhasil diubah menjadi {$updateData['status']}!";
        } elseif (isset($updateData['payment_status'])) {
            return "Payment status berhasil diubah menjadi {$updateData['payment_status']}!";
        }
        
        return 'Status order berhasil diupdate!';
    }

    /**
     * Check for schedule conflicts when booking a lapangan
     */
    private function checkScheduleConflict($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai)
    {
        return $this->findConflictingOrder($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai);
    }

    /**
     * Check for schedule conflicts when updating an existing booking
     */
    private function checkScheduleConflictForUpdate($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai, $excludeOrderId)
    {
        return $this->findConflictingOrder($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai, $excludeOrderId);
    }

    /**
     * Find conflicting orders for schedule validation
     */
    private function findConflictingOrder($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai, $excludeOrderId = null)
    {
        $query = Order::where('lapangan_id', $lapanganId)
                     ->where('tanggal_booking', $tanggalBooking)
                     ->whereNotIn('status', ['cancelled']);

        if ($excludeOrderId) {
            $query->where('id', '!=', $excludeOrderId);
        }

        return $query->where(function ($timeQuery) use ($jamMulai, $jamSelesai) {
            $this->addTimeConflictConditions($timeQuery, $jamMulai, $jamSelesai);
        })->first();
    }

    /**
     * Add time conflict conditions to query
     */
    private function addTimeConflictConditions($query, $jamMulai, $jamSelesai): void
    {
        $query->where(function ($q) use ($jamMulai) {
                // New start time overlaps with existing booking
                $q->where('jam_mulai', '<=', $jamMulai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->orWhere(function ($q) use ($jamSelesai) {
                // New end time overlaps with existing booking  
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>=', $jamSelesai);
            })
            ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                // New booking encompasses existing booking
                $q->where('jam_mulai', '>=', $jamMulai)
                  ->where('jam_selesai', '<=', $jamSelesai);
            })
            ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                // Existing booking encompasses new booking
                $q->where('jam_mulai', '<=', $jamMulai)
                  ->where('jam_selesai', '>=', $jamSelesai);
            });
    }

    /**
     * Check if two time slots conflict
     */
    private function isTimeSlotConflict($startA, $endA, $startB, $endB): bool
    {
        try {
            $startA = $this->timeToMinutes($startA);
            $endA = $this->timeToMinutes($endA);
            $startB = $this->timeToMinutes($startB);
            $endB = $this->timeToMinutes($endB);
            
            return ($startA < $endB) && ($endA > $startB);
        } catch (\Exception $e) {
            Log::error('Error in isTimeSlotConflict: ' . $e->getMessage(), [
                'startA' => $startA, 'endA' => $endA, 'startB' => $startB, 'endB' => $endB
            ]);
            return false;
        }
    }
    
    /**
     * Convert time to minutes for easier comparison
     */
    private function timeToMinutes($time): int
    {
        if (empty($time) || !is_string($time)) {
            return 0;
        }
        
        $parts = explode(':', $time);
        
        if (count($parts) < 2) {
            return 0;
        }
        
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        
        return $hours * 60 + $minutes;
    }

    /**
     * Get available time slots for a specific lapangan and date
     */
    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $lapanganId = $request->get('lapangan_id');
            $tanggalBooking = $request->get('tanggal_booking');

            if (!$lapanganId || !$tanggalBooking) {
                return response()->json(['error' => 'lapangan_id and tanggal_booking are required'], 400);
            }

            $existingBookings = $this->getExistingBookings($lapanganId, $tanggalBooking);
            $availableSlots = $this->calculateAvailableSlots($existingBookings);
            $bookedSlots = $this->formatBookedSlots($existingBookings);

            return response()->json([
                'success' => true,
                'lapangan_id' => $lapanganId,
                'tanggal_booking' => $tanggalBooking,
                'available_slots' => $availableSlots,
                'booked_slots' => $bookedSlots,
                'total_available' => count($availableSlots),
                'total_booked' => count($bookedSlots),
                'debug_info' => [
                    'existing_bookings_count' => $existingBookings->count(),
                    'operating_hours_count' => count(self::OPERATING_HOURS)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getAvailableTimeSlots: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    private function getExistingBookings($lapanganId, $tanggalBooking)
    {
        return Order::where('lapangan_id', $lapanganId)
                   ->whereDate('tanggal_booking', $tanggalBooking)
                   ->whereNotIn('status', ['cancelled'])
                   ->select('jam_mulai', 'jam_selesai', 'customer_name', 'order_number')
                   ->get();
    }

    private function calculateAvailableSlots($existingBookings): array
    {
        $availableSlots = [];
        
        foreach (self::OPERATING_HOURS as $hour) {
            $nextHour = date('H:i', strtotime($hour . ' +1 hour'));
            
            if (!$this->isHourSlotBooked($hour, $nextHour, $existingBookings)) {
                $availableSlots[] = [
                    'time' => $hour . ' - ' . $nextHour,
                    'start' => $hour,
                    'end' => $nextHour,
                    'available' => true
                ];
            }
        }

        return $availableSlots;
    }

    private function isHourSlotBooked($hour, $nextHour, $existingBookings): bool
    {
        foreach ($existingBookings as $booking) {
            if (empty($booking->jam_mulai) || empty($booking->jam_selesai)) {
                continue;
            }
            
            $bookingStart = $this->formatTimeForComparison($booking->jam_mulai);
            $bookingEnd = $this->formatTimeForComparison($booking->jam_selesai);
            
            if (!empty($bookingStart) && !empty($bookingEnd) && $bookingStart !== $bookingEnd) {
                if ($this->isTimeSlotConflict($hour, $nextHour, $bookingStart, $bookingEnd)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    private function formatBookedSlots($existingBookings): array
    {
        $bookedSlots = [];
        
        foreach ($existingBookings as $booking) {
            if (empty($booking->jam_mulai) || empty($booking->jam_selesai)) {
                continue;
            }
            
            $bookingStart = $this->formatTimeForComparison($booking->jam_mulai);
            $bookingEnd = $this->formatTimeForComparison($booking->jam_selesai);
            
            Log::info('Processing booking for debug:', [
                'order_number' => $booking->order_number,
                'customer_name' => $booking->customer_name,
                'jam_mulai_raw' => $booking->jam_mulai,
                'jam_selesai_raw' => $booking->jam_selesai,
                'jam_mulai_formatted' => $bookingStart,
                'jam_selesai_formatted' => $bookingEnd
            ]);
            
            if (!empty($bookingStart) && !empty($bookingEnd) && $bookingStart !== $bookingEnd) {
                $bookedSlots[] = [
                    'time' => $bookingStart . ' - ' . $bookingEnd,
                    'start' => $bookingStart,
                    'end' => $bookingEnd,
                    'booked_by' => $booking->customer_name ?? 'Unknown',
                    'order_number' => $booking->order_number ?? '',
                    'available' => false
                ];
            } else {
                Log::warning('Invalid time format in booking:', [
                    'order_number' => $booking->order_number,
                    'customer_name' => $booking->customer_name,
                    'jam_mulai_raw' => $booking->jam_mulai,
                    'jam_selesai_raw' => $booking->jam_selesai,
                    'jam_mulai_formatted' => $bookingStart,
                    'jam_selesai_formatted' => $bookingEnd
                ]);
            }
        }
        
        return $bookedSlots;
    }

    /**
     * Format time for consistent comparison
     */
    private function formatTimeForComparison($time): string
    {
        if (empty($time)) {
            return '';
        }
        
        // If it's already in H:i format, return as is
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        
        // If it's a time object or datetime string, format it
        try {
            $dateTime = new \DateTime($time);
            return $dateTime->format('H:i');
        } catch (\Exception $e) {
            Log::warning('Failed to format time: ' . $time, ['error' => $e->getMessage()]);
            return '';
        }
    }
}
