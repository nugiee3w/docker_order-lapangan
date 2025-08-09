<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public routes for checking available time slots
Route::get('/lapangan/{id}/available-slots', [OrderController::class, 'getAvailableTimeSlots']);

// Public route for order statistics (for dashboard)
Route::get('/orders/count', [OrderController::class, 'getOrdersCount']);

// Public route for order statistics by lapangan (for lapangan service)
Route::get('/orders/stats/lapangan/{id}', [OrderController::class, 'getOrderStatsByLapangan']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Customer, Staff, and Admin can view orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // Customers can create orders
    Route::post('/orders', [OrderController::class, 'store']);
    
    // Staff and Admin can update/delete orders
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
        
        // Bulk operations - Admin only
        Route::middleware(['role:admin'])->group(function () {
            Route::delete('/orders', [OrderController::class, 'destroyAll']);
            Route::post('/orders/bulk-delete', [OrderController::class, 'bulkDestroy']);
        });
    });
});
