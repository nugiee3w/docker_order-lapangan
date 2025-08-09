<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LapanganController;
use App\Http\Controllers\Api\JadwalLapanganController;
use App\Http\Controllers\Api\AuthController;

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public routes (no authentication required)
Route::get('/lapangan', [LapanganController::class, 'index']);
Route::get('/lapangan/{id}', [LapanganController::class, 'show']);
Route::get('/jadwal-lapangan', [JadwalLapanganController::class, 'index']);
Route::get('/jadwal-lapangan/{id}', [JadwalLapanganController::class, 'show']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Admin and Staff only routes
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::post('/lapangan', [LapanganController::class, 'store']);
        Route::put('/lapangan/{id}', [LapanganController::class, 'update']);
        Route::delete('/lapangan/{id}', [LapanganController::class, 'destroy']);
        
        Route::post('/jadwal-lapangan', [JadwalLapanganController::class, 'store']);
        Route::put('/jadwal-lapangan/{id}', [JadwalLapanganController::class, 'update']);
        Route::delete('/jadwal-lapangan/{id}', [JadwalLapanganController::class, 'destroy']);
    });
});
