<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{section}', [DashboardController::class, 'show'])->name('dashboard.section');
    Route::post('/dashboard/{section}', [FeatureController::class, 'store'])->name('dashboard.section.store');
    Route::delete('/dashboard/{section}/{id}', [FeatureController::class, 'destroy'])->name('dashboard.section.destroy');
    Route::patch('/dashboard/permohonan-sktm/{permohonan}/verifikasi', [FeatureController::class, 'verify'])->name('dashboard.permohonan.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
