<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Services\LapanganService;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Test endpoint untuk token integration
Route::get('/test-lapangan-connection', function () {
    $lapanganService = new LapanganService();
    
    // Test get all lapangan dengan token authentication
    $result = $lapanganService->getAllLapangan();
    
    return response()->json([
        'message' => 'Testing Lapangan Service Connection with Token',
        'token_configured' => config('services.lapangan.token') ? 'Yes' : 'No',
        'token_preview' => substr(config('services.lapangan.token'), 0, 10) . '...',
        'result' => $result
    ]);
});

Route::get('/test-lapangan-detail/{id}', function ($id) {
    $lapanganService = new LapanganService();
    
    // Test get specific lapangan dengan token authentication
    $result = $lapanganService->getLapangan($id);
    
    return response()->json([
        'message' => 'Testing Get Lapangan Detail with Token',
        'lapangan_id' => $id,
        'result' => $result
    ]);
});

Route::get('/dashboard', [WebController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/orders', [WebController::class, 'orders'])->name('orders.index');
    Route::get('/orders/create', [WebController::class, 'create'])->name('orders.create');
    Route::post('/orders', [WebController::class, 'store'])->name('orders.store');
    Route::get('/orders/{id}', [WebController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/edit', [WebController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{id}', [WebController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [WebController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{id}/status', [WebController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // API untuk cek ketersediaan jadwal
    Route::get('/api/available-time-slots', [WebController::class, 'getAvailableTimeSlots'])->name('api.available-time-slots');
    
    // Routes untuk lapangan
    Route::get('/lapangan', [\App\Http\Controllers\LapanganController::class, 'index'])->name('lapangan.index');
    Route::get('/lapangan/{id}', [\App\Http\Controllers\LapanganController::class, 'show'])->name('lapangan.show');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Simple debug route
Route::get('/simple-test', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'Simple route working',
        'time' => now()
    ]);
});

// Route untuk testing API connection (bisa dihapus jika sudah tidak diperlukan)
Route::get('/debug-lapangan-count', function() {
    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)->get('http://lapangan-service:80/api/lapangan');
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['success']) && $data['success'] && isset($data['data'])) {
                $lapangans = [];
                // Cek apakah data terpaginasi
                if (isset($data['data']['data'])) {
                    $lapangans = $data['data']['data']; // Paginated data
                } else {
                    $lapangans = $data['data']; // Direct array data
                }
                
                return response()->json([
                    'status' => 'success',
                    'total_lapangan' => count($lapangans),
                    'lapangan_names' => array_column($lapangans, 'nama'),
                    'api_url' => 'http://lapangan-service:80/api/lapangan'
                ]);
            }
        }
        
        return response()->json(['status' => 'failed', 'message' => 'API call failed']);
        
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

// Debug route untuk test lapangan API
Route::get('/test-lapangan-api', [\App\Http\Controllers\DebugController::class, 'testLapanganApi']);

// Test lapangan controller tanpa auth
Route::get('/test-lapangan-controller', function() {
    $controller = new \App\Http\Controllers\LapanganController();
    try {
        return $controller->index();
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

require __DIR__.'/auth.php';
