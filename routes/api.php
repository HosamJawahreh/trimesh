<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreeDFileController;

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

// 3D File Sharing API Routes (no CSRF protection)
Route::prefix('3d-files')->name('3d-files.')->group(function () {
    Route::post('/store', [ThreeDFileController::class, 'store'])->name('store');
    Route::get('/{fileId}', [ThreeDFileController::class, 'show'])->name('show');
    Route::post('/{fileId}/camera', [ThreeDFileController::class, 'updateCamera'])->name('updateCamera');
    Route::post('/cleanup-expired', [ThreeDFileController::class, 'cleanupExpired'])->name('cleanup');
});
