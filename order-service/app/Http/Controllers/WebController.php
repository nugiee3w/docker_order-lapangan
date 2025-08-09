<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\LapanganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebController extends Controller
{
    protected $lapanganService;

    public function __construct(LapanganService $lapanganService)
    {
        $this->lapanganService = $lapanganService;
    }

    public function dashboard()
    {
        $totalOrders = Order::count();
        $ordersToday = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $confirmedOrders = Order::where('status', 'confirmed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        // Revenue statistics
        $totalRevenue = Order::where('status', 'confirmed')->sum('total_harga');
        $revenueToday = Order::where('status', 'confirmed')
                            ->whereDate('created_at', today())
                            ->sum('total_harga');
        
        // Recent orders with lapangan info - sorted by upcoming booking dates first
        $recentOrders = Order::where('tanggal_booking', '>=', Carbon::now()->toDateString())
                            ->orderBy('tanggal_booking', 'asc')
                            ->orderBy('jam_mulai', 'asc')
                            ->take(10)
                            ->get();
        
        // If we don't have enough upcoming orders, add recent past orders
        if ($recentOrders->count() < 10) {
            $additionalOrders = Order::where('tanggal_booking', '<', Carbon::now()->toDateString())
                                   ->orderBy('tanggal_booking', 'desc')
                                   ->orderBy('jam_mulai', 'desc')
                                   ->take(10 - $recentOrders->count())
                                   ->get();
            $recentOrders = $recentOrders->merge($additionalOrders);
        }

        // Get lapangan info for each recent order
        foreach ($recentOrders as $order) {
            if ($order->lapangan_id) {
                $lapanganData = $this->lapanganService->getLapangan($order->lapangan_id);
                if (isset($lapanganData['data'])) {
                    $lapangan = $lapanganData['data'];
                    $order->lapangan_info = [
                        'nama' => $lapangan['nama'] ?? 'N/A',
                        'jenis' => ucfirst($lapangan['jenis'] ?? 'N/A'),
                        'lokasi' => $lapangan['lokasi'] ?? 'N/A'
                    ];
                } else {
                    $order->lapangan_info = [
                        'nama' => 'Data tidak ditemukan',
                        'jenis' => 'N/A',
                        'lokasi' => 'N/A'
                    ];
                }
            } else {
                $order->lapangan_info = [
                    'nama' => 'ID lapangan kosong',
                    'jenis' => 'N/A',
                    'lokasi' => 'N/A'
                ];
            }
        }

        // Statistics array for view
        $stats = [
            'total_orders' => $totalOrders,
            'orders_today' => $ordersToday,
            'pending_orders' => $pendingOrders,
            'confirmed_orders' => $confirmedOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_revenue' => $totalRevenue,
            'revenue_today' => $revenueToday
        ];

        return view('dashboard', compact('stats', 'recentOrders'));
    }

    public function orders(Request $request)
    {
        $query = Order::query();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%");
            });
        }
        
        // Sort by upcoming bookings first, then by booking date and time
        $orders = $query->orderByRaw("
            CASE 
                WHEN tanggal_booking >= CURDATE() THEN 0 
                ELSE 1 
            END,
            tanggal_booking ASC,
            jam_mulai ASC
        ")->paginate(15);
        
        // Get lapangan info for each order
        foreach ($orders as $order) {
            if ($order->lapangan_id) {
                $lapanganData = $this->lapanganService->getLapangan($order->lapangan_id);
                if (isset($lapanganData['data'])) {
                    $lapangan = $lapanganData['data'];
                    $order->lapangan_info = [
                        'nama' => $lapangan['nama'] ?? 'N/A',
                        'jenis' => ucfirst($lapangan['jenis'] ?? 'N/A'),
                        'lokasi' => $lapangan['lokasi'] ?? 'N/A',
                        'harga_per_jam' => (float)($lapangan['harga_per_jam'] ?? 0),
                        'fasilitas' => is_string($lapangan['fasilitas'] ?? '') 
                            ? json_decode($lapangan['fasilitas'], true) 
                            : ($lapangan['fasilitas'] ?? [])
                    ];
                } else {
                    $order->lapangan_info = [
                        'nama' => 'Data tidak ditemukan',
                        'jenis' => 'N/A',
                        'lokasi' => 'N/A',
                        'harga_per_jam' => 0,
                        'fasilitas' => []
                    ];
                }
            } else {
                $order->lapangan_info = [
                    'nama' => 'ID lapangan kosong',
                    'jenis' => 'N/A',
                    'lokasi' => 'N/A',
                    'harga_per_jam' => 0,
                    'fasilitas' => []
                ];
            }
        }
        
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        
        // Debug log
        Log::info("Showing order ID: {$id}, Lapangan ID: {$order->lapangan_id}");
        
        // Get lapangan info
        if ($order->lapangan_id) {
            $lapanganData = $this->lapanganService->getLapangan($order->lapangan_id);
            if (isset($lapanganData['data'])) {
                $lapangan = $lapanganData['data'];
                $order->lapangan_info = [
                    'nama' => $lapangan['nama'] ?? 'N/A',
                    'jenis' => ucfirst($lapangan['jenis'] ?? 'N/A'),
                    'lokasi' => $lapangan['lokasi'] ?? 'N/A',
                    'harga_per_jam' => (float)($lapangan['harga_per_jam'] ?? 0),
                    'fasilitas' => is_string($lapangan['fasilitas'] ?? '') 
                        ? json_decode($lapangan['fasilitas'], true) 
                        : ($lapangan['fasilitas'] ?? []),
                    'status' => ucfirst($lapangan['status'] ?? 'N/A')
                ];
            } else {
                $order->lapangan_info = [
                    'nama' => 'Data lapangan tidak ditemukan',
                    'jenis' => 'N/A',
                    'lokasi' => 'N/A',
                    'harga_per_jam' => 0,
                    'fasilitas' => []
                ];
            }
        } else {
            $order->lapangan_info = [
                'nama' => 'ID lapangan tidak tersedia',
                'jenis' => 'N/A',
                'lokasi' => 'N/A',
                'harga_per_jam' => 0,
                'fasilitas' => []
            ];
        }
        
        return view('orders.show', compact('order'));
    }
    
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        
        // Get lapangan list from lapangan service
        $lapanganResponse = $this->lapanganService->getAllLapangan();
        
        if (isset($lapanganResponse['data'])) {
            if (isset($lapanganResponse['data']['data'])) {
                // Paginated data
                $lapangan_list = $lapanganResponse['data']['data'];
            } else {
                // Direct array data
                $lapangan_list = $lapanganResponse['data'];
            }
        } else {
            $lapangan_list = [];
            Log::warning('Failed to get lapangan list from service', ['response' => $lapanganResponse]);
        }
        
        return view('orders.edit', compact('order', 'lapangan_list'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'lapangan_id' => 'required|integer',
            'tanggal_booking' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
            'notes' => 'nullable|string'
        ]);

        // Validasi konflik jadwal hanya jika ada perubahan jadwal, lapangan, atau tanggal
        $scheduleChanged = $order->lapangan_id != $validated['lapangan_id'] ||
                          $order->tanggal_booking != $validated['tanggal_booking'] ||
                          $order->jam_mulai != $validated['jam_mulai'] ||
                          $order->jam_selesai != $validated['jam_selesai'];

        if ($scheduleChanged) {
            $conflictingOrder = $this->checkScheduleConflictForUpdate(
                $validated['lapangan_id'],
                $validated['tanggal_booking'],
                $validated['jam_mulai'],
                $validated['jam_selesai'],
                $order->id // Exclude current order from conflict check
            );

            if ($conflictingOrder) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'jam_mulai' => 'Jadwal lapangan sudah dibooking oleh ' . $conflictingOrder->customer_name . 
                                      ' pada tanggal ' . $conflictingOrder->tanggal_booking->format('d/m/Y') . 
                                      ' jam ' . $conflictingOrder->jam_mulai . ' - ' . $conflictingOrder->jam_selesai . 
                                      '. Silakan pilih waktu lain.'
                    ]);
            }
        }

        // Map fields untuk kompatibilitas dengan tabel database
        $updateData = $validated;
        $updateData['booking_date'] = $validated['tanggal_booking']; // mapping ke field database
        $updateData['start_time'] = $validated['jam_mulai'];         // mapping ke field database
        $updateData['end_time'] = $validated['jam_selesai'];         // mapping ke field database
        $updateData['phone'] = $validated['customer_phone'];         // untuk kompatibilitas dengan tabel
        $updateData['total_price'] = $validated['total_harga'];      // untuk kompatibilitas dengan tabel

        $order->update($updateData);

        return redirect()->route('orders.show', $order->id)
                        ->with('success', 'Order berhasil diupdate!');
    }

    public function destroy($id)
    {
        Log::info("Delete request received for order ID: {$id}");
        
        try {
            $order = Order::findOrFail($id);
            $orderNumber = $order->order_number;
            
            Log::info("Order found: {$orderNumber}, Status: {$order->status}, Payment: {$order->payment_status}");
            
            // Check if order can be deleted (business logic)
            if ($order->status == 'confirmed' && $order->payment_status == 'paid') {
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

    public function create(Request $request)
    {
        // Get lapangan list from lapangan service
        $lapanganResponse = $this->lapanganService->getAllLapangan();
        
        if (isset($lapanganResponse['data'])) {
            if (isset($lapanganResponse['data']['data'])) {
                // Paginated data
                $lapangan_list = $lapanganResponse['data']['data'];
            } else {
                // Direct array data
                $lapangan_list = $lapanganResponse['data'];
            }
        } else {
            $lapangan_list = [];
            Log::warning('Failed to get lapangan list from service', ['response' => $lapanganResponse]);
        }
        
        // Get selected lapangan ID from query parameter
        $selectedLapanganId = $request->get('lapangan_id');
        
        return view('orders.create', compact('lapangan_list', 'selectedLapanganId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'lapangan_id' => 'required|integer',
            'tanggal_booking' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
            'notes' => 'nullable|string'
        ]);

        // Validasi konflik jadwal booking
        $conflictingOrder = $this->checkScheduleConflict(
            $validated['lapangan_id'],
            $validated['tanggal_booking'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($conflictingOrder) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'jam_mulai' => 'Jadwal lapangan sudah dibooking oleh ' . $conflictingOrder->customer_name . 
                                  ' pada tanggal ' . $conflictingOrder->tanggal_booking->format('d/m/Y') . 
                                  ' jam ' . $conflictingOrder->jam_mulai . ' - ' . $conflictingOrder->jam_selesai . 
                                  '. Silakan pilih waktu lain.'
                ]);
        }

        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        $validated['order_number'] = $orderNumber;
        
        // Set default value for jadwal_lapangan_id if not provided
        $validated['jadwal_lapangan_id'] = 1; // Default dummy value

        // Map fields untuk kompatibilitas dengan tabel database
        $orderData = $validated;
        $orderData['booking_date'] = $validated['tanggal_booking']; // mapping ke field database
        $orderData['start_time'] = $validated['jam_mulai'];         // mapping ke field database
        $orderData['end_time'] = $validated['jam_selesai'];         // mapping ke field database
        $orderData['phone'] = $validated['customer_phone'];         // untuk kompatibilitas dengan tabel
        $orderData['total_price'] = $validated['total_harga'];      // untuk kompatibilitas dengan tabel

        $order = Order::create($orderData);

        return redirect()->route('orders.show', $order->id)
                        ->with('success', 'Order berhasil dibuat!');
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info("UpdateStatus request received for order ID: {$id}", $request->all());
        
        try {
            $order = Order::findOrFail($id);
            Log::info("Order found: {$order->order_number}, Current Status: {$order->status}, Current Payment: {$order->payment_status}");
            
            // Validate only the fields that are being sent
            $rules = [];
            $updateData = [];
            
            if ($request->has('status')) {
                $rules['status'] = 'required|in:pending,confirmed,cancelled,completed';
                $updateData['status'] = $request->status;
            }
            
            if ($request->has('payment_status')) {
                $rules['payment_status'] = 'required|in:unpaid,paid,refunded';
                $updateData['payment_status'] = $request->payment_status;
            }
            
            if ($request->has('notes')) {
                $rules['notes'] = 'nullable|string|max:500';
                $updateData['notes'] = $request->notes;
            }
            
            // Validate the request
            $validated = $request->validate($rules);
            
            // Update only the provided fields
            $order->update($updateData);
            
            Log::info("Order {$order->order_number} updated successfully", $updateData);
            
            // Create success message based on what was updated
            $message = 'Status order berhasil diupdate!';
            if (isset($updateData['status']) && isset($updateData['payment_status'])) {
                $message = "Status order berhasil diubah menjadi {$updateData['status']} dan payment status menjadi {$updateData['payment_status']}!";
            } elseif (isset($updateData['status'])) {
                $message = "Status order berhasil diubah menjadi {$updateData['status']}!";
            } elseif (isset($updateData['payment_status'])) {
                $message = "Payment status berhasil diubah menjadi {$updateData['payment_status']}!";
            }

            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate status order. Silakan coba lagi.');
        }
    }

    /**
     * Check for schedule conflicts when booking a lapangan
     */
    private function checkScheduleConflict($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai)
    {
        // Cari order yang sudah ada dengan lapangan dan tanggal yang sama
        // dan statusnya bukan cancelled
        $conflictingOrder = Order::where('lapangan_id', $lapanganId)
            ->where('tanggal_booking', $tanggalBooking)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                // Cek overlap waktu:
                // 1. Jam mulai baru di antara jam booking yang sudah ada
                $query->where(function ($q) use ($jamMulai) {
                    $q->where('jam_mulai', '<=', $jamMulai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                // 2. Jam selesai baru di antara jam booking yang sudah ada  
                ->orWhere(function ($q) use ($jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>=', $jamSelesai);
                })
                // 3. Booking baru mencakup seluruh waktu booking yang sudah ada
                ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '>=', $jamMulai)
                      ->where('jam_selesai', '<=', $jamSelesai);
                })
                // 4. Booking yang sudah ada mencakup seluruh waktu booking baru
                ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<=', $jamMulai)
                      ->where('jam_selesai', '>=', $jamSelesai);
                });
            })
            ->first();

        return $conflictingOrder;
    }

    /**
     * Check for schedule conflicts when updating an existing booking
     */
    private function checkScheduleConflictForUpdate($lapanganId, $tanggalBooking, $jamMulai, $jamSelesai, $excludeOrderId)
    {
        // Sama seperti checkScheduleConflict tapi mengecualikan order yang sedang diupdate
        $conflictingOrder = Order::where('lapangan_id', $lapanganId)
            ->where('tanggal_booking', $tanggalBooking)
            ->where('id', '!=', $excludeOrderId) // Exclude current order
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                // Cek overlap waktu yang sama seperti sebelumnya
                $query->where(function ($q) use ($jamMulai) {
                    $q->where('jam_mulai', '<=', $jamMulai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                ->orWhere(function ($q) use ($jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>=', $jamSelesai);
                })
                ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '>=', $jamMulai)
                      ->where('jam_selesai', '<=', $jamSelesai);
                })
                ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<=', $jamMulai)
                      ->where('jam_selesai', '>=', $jamSelesai);
                });
            })
            ->first();

        return $conflictingOrder;
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

            // Jam operasional lapangan (bisa dikonfigurasi per lapangan)
            $operatingHours = [
                '06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', 
                '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'
            ];

            // Get semua booking yang sudah ada di tanggal tersebut
            $existingBookings = Order::where('lapangan_id', $lapanganId)
                ->whereDate('tanggal_booking', $tanggalBooking)  // Use whereDate for proper date comparison
                ->whereNotIn('status', ['cancelled'])
                ->select('jam_mulai', 'jam_selesai', 'customer_name', 'order_number')
                ->get();

            // Debug: log the raw booking data
            Log::info('Raw booking data:', [
                'lapangan_id' => $lapanganId,
                'tanggal_booking' => $tanggalBooking,
                'bookings' => $existingBookings->toArray()
            ]);

            $availableSlots = [];
            $bookedSlots = [];

            // Process existing bookings to show actual booked time slots
            foreach ($existingBookings as $booking) {
                // Ensure we have valid time data
                if (empty($booking->jam_mulai) || empty($booking->jam_selesai)) {
                    continue;
                }
                
                // Use model accessors for consistent time formatting
                $bookingStart = $booking->jam_mulai_formatted;
                $bookingEnd = $booking->jam_selesai_formatted;
                
                // Debug logging
                Log::info('Processing booking for debug:', [
                    'order_number' => $booking->order_number,
                    'customer_name' => $booking->customer_name,
                    'jam_mulai_raw' => $booking->jam_mulai,
                    'jam_selesai_raw' => $booking->jam_selesai,
                    'jam_mulai_formatted' => $bookingStart,
                    'jam_selesai_formatted' => $bookingEnd
                ]);
                
                // Validate time format and ensure it's not the same time
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
                    // Log invalid time format for debugging
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
            
            // Generate available slots by filtering out booked times
            foreach ($operatingHours as $hour) {
                $nextHour = date('H:i', strtotime($hour . ' +1 hour'));
                
                // Check if this hour slot conflicts with any booking
                $isBooked = false;
                foreach ($existingBookings as $booking) {
                    // Ensure we have valid time data
                    if (empty($booking->jam_mulai) || empty($booking->jam_selesai)) {
                        continue;
                    }
                    
                    // Use model accessors for consistent time formatting
                    $bookingStart = $booking->jam_mulai_formatted;
                    $bookingEnd = $booking->jam_selesai_formatted;
                    
                    // Validate time format before checking conflict
                    if (!empty($bookingStart) && !empty($bookingEnd) && $bookingStart !== $bookingEnd) {
                        if ($this->isTimeSlotConflict($hour, $nextHour, $bookingStart, $bookingEnd)) {
                            $isBooked = true;
                            break;
                        }
                    }
                }

                if (!$isBooked) {
                    $availableSlots[] = [
                        'time' => $hour . ' - ' . $nextHour,
                        'start' => $hour,
                        'end' => $nextHour,
                        'available' => true
                    ];
                }
            }

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
                    'operating_hours_count' => count($operatingHours)
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

    /**
     * Helper method to check if two time slots conflict
     */
    private function isTimeSlotConflict($startA, $endA, $startB, $endB)
    {
        try {
            // Convert time strings to comparable format
            $startA = $this->timeToMinutes($startA);
            $endA = $this->timeToMinutes($endA);
            $startB = $this->timeToMinutes($startB);
            $endB = $this->timeToMinutes($endB);
            
            // Check for overlap: (startA < endB) && (endA > startB)
            return ($startA < $endB) && ($endA > $startB);
        } catch (\Exception $e) {
            Log::error('Error in isTimeSlotConflict: ' . $e->getMessage(), [
                'startA' => $startA,
                'endA' => $endA,
                'startB' => $startB,
                'endB' => $endB
            ]);
            return false; // Default to no conflict if there's an error
        }
    }
    
    /**
     * Helper method to convert time to minutes for easier comparison
     */
    private function timeToMinutes($time)
    {
        // Ensure we have a valid time string
        if (empty($time) || !is_string($time)) {
            return 0;
        }
        
        $parts = explode(':', $time);
        
        // Check if we have at least hour and minute parts
        if (count($parts) < 2) {
            return 0;
        }
        
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);
        
        return $hours * 60 + $minutes;
    }
}
