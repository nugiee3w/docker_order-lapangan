<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\LapanganWebController;

Route::get('/test', function() { return 'Test OK - Laravel Working!'; });
Route::get('/', [WebController::class, 'dashboard'])->name('dashboard');
Route::get('/lapangan', [WebController::class, 'lapangan'])->name('lapangan.index');
Route::get('/lapangan/create', [WebController::class, 'lapanganCreate'])->name('lapangan.create');
Route::get('/lapangan/{id}/edit', [WebController::class, 'lapanganEdit'])->name('lapangan.edit');
Route::get('/jadwal', [WebController::class, 'jadwal'])->name('jadwal');

// Web-based CRUD routes for lapangan (no authentication required for now)
Route::post('/web/lapangan', [LapanganWebController::class, 'store'])->name('web.lapangan.store');
Route::get('/web/lapangan/{id}', [LapanganWebController::class, 'show'])->name('web.lapangan.show');
Route::put('/web/lapangan/{id}', [LapanganWebController::class, 'update'])->name('web.lapangan.update');
Route::delete('/web/lapangan/{id}', [LapanganWebController::class, 'destroy'])->name('web.lapangan.destroy');

require __DIR__.'/auth.php';
