<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplianceController;
use App\Http\Controllers\SolarCalculationController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Solar Report
Route::post('/solar-calculations', [SolarCalculationController::class, 'store']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard'); 

    Route::prefix('appliances')->name('appliances.')->group(function () {
        Route::get('/', [ApplianceController::class, 'index'])->name('index');
        Route::get('/create', [ApplianceController::class, 'create'])->name('create');
        Route::post('/', [ApplianceController::class, 'store'])->name('store');

        Route::get('/{appliance}/edit', [ApplianceController::class, 'edit'])->name('edit');
        Route::put('/{appliance}', [ApplianceController::class, 'update'])->name('update');

        Route::delete('/{appliance}', [ApplianceController::class, 'destroy'])->name('destroy');
    });

    // Solar Reports
    Route::get('/solar/reports', [SolarCalculationController::class, 'index']);
    Route::get('/solar/{report_id}/premium_report', [SolarCalculationController::class, 'show'])->name('report.premium');
});

require __DIR__.'/settings.php';
