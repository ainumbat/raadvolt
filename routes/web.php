<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplianceController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('appliances')->name('appliances.')->group(function () {
        Route::get('/', [ApplianceController::class, 'index'])->name('index');
        Route::get('/create', [ApplianceController::class, 'create'])->name('create');
        Route::post('/', [ApplianceController::class, 'store'])->name('store');

        Route::get('/{appliance}/edit', [ApplianceController::class, 'edit'])->name('edit');
        Route::put('/{appliance}', [ApplianceController::class, 'update'])->name('update');

        Route::delete('/{appliance}', [ApplianceController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
