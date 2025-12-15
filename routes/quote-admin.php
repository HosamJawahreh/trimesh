<?php

use App\Http\Controllers\Admin\PricingRuleController;
use App\Http\Controllers\Admin\QuoteManagementController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix"=> "admin", "as" => "admin.", "middleware" => ['auth', 'checkAdmin']], function () {
    
    // Pricing Rules Management
    Route::prefix('3d-pricing')->name('pricing.')->group(function () {
        Route::get('/', [PricingRuleController::class, 'index'])->name('index');
    });
    
    // Quote Management
    Route::prefix('3d-quotes')->name('quotes.')->group(function () {
        Route::get('/', [QuoteManagementController::class, 'index'])->name('index');
        Route::get('/{id}', [QuoteManagementController::class, 'show'])->name('show');
        Route::post('/{id}/status', [QuoteManagementController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [QuoteManagementController::class, 'destroy'])->name('destroy');
    });
    
});
