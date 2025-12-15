<?php

use App\Http\Controllers\Api\QuoteController;
use Illuminate\Support\Facades\Route;

// Quote API Routes
Route::prefix('quote')->group(function () {
    Route::post('/', [QuoteController::class, 'store']);
    Route::post('/upload', [QuoteController::class, 'uploadFile']);
    Route::post('/analyze', [QuoteController::class, 'analyzeFile']);
    Route::post('/submit', [QuoteController::class, 'submit']);
    Route::delete('/file/{fileId}', [QuoteController::class, 'deleteFile']);
    Route::get('/materials', [QuoteController::class, 'getMaterials']);
});
