<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreeDFileController;
use App\Http\Controllers\Api\MeshRepairController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\RepairLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Quote API Routes
Route::prefix('quotes')->name('quotes.')->group(function () {
    Route::get('/', [QuoteController::class, 'index'])->name('index');
    Route::post('/store', [QuoteController::class, 'store'])->name('store');
    Route::get('/{id}', [QuoteController::class, 'show'])->name('show');
    Route::put('/{id}', [QuoteController::class, 'update'])->name('update');
    Route::delete('/{id}', [QuoteController::class, 'destroy'])->name('destroy');
});

// 3D File Sharing API Routes (no CSRF protection)
Route::prefix('3d-files')->name('3d-files.')->group(function () {
    Route::post('/store', [ThreeDFileController::class, 'store'])->name('store');
    Route::get('/{fileId}', [ThreeDFileController::class, 'show'])->name('show');
    Route::post('/{fileId}/camera', [ThreeDFileController::class, 'updateCamera'])->name('updateCamera');
    Route::post('/cleanup-expired', [ThreeDFileController::class, 'cleanupExpired'])->name('cleanup');
});

// Mesh Repair API Routes
Route::prefix('mesh')->name('mesh.')->group(function () {
    Route::get('/status', [MeshRepairController::class, 'status'])->name('status');
    Route::get('/stats', [MeshRepairController::class, 'stats'])->name('stats');
    Route::post('/analyze', [MeshRepairController::class, 'analyze'])->name('analyze');
    Route::post('/repair', [MeshRepairController::class, 'repair'])->name('repair');
    Route::post('/repair-download', [MeshRepairController::class, 'repairAndDownload'])->name('repair-download');
    Route::get('/history/{fileId}', [MeshRepairController::class, 'history'])->name('history');
});

// Repair Logs API Routes (for admin dashboard)
Route::prefix('repair-logs')->name('repair-logs.')->group(function () {
    Route::post('/', [RepairLogController::class, 'store'])->name('store');
    Route::get('/', [RepairLogController::class, 'index'])->name('index');
    Route::get('/{id}', [RepairLogController::class, 'show'])->name('show');
});
