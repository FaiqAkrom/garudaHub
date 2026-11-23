<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\DashboardController;

Route::get('/', [ProjectController::class, 'index'])->name('projects.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::resource('projects', ProjectController::class)->except(['index', 'show']);
});

Route::get('/projects/{project}', [ProjectController::class, 'show'])
    ->name('projects.show')
    ->whereNumber('project');

Route::middleware(['auth', 'role:developer'])->group(function () {
    Route::post('/projects/{project}/bids', [BidController::class, 'store'])
        ->name('bids.store')
        ->whereNumber('project');
    Route::put('/bids/{bid}', [BidController::class, 'update'])
        ->name('bids.update')
        ->whereNumber('bid');
    Route::delete('/bids/{bid}', [BidController::class, 'destroy'])
        ->name('bids.destroy')
        ->whereNumber('bid');
});

Route::post('/bids/{bid}/accept', [BidController::class, 'accept'])
    ->middleware(['auth', 'role:client'])
    ->name('bids.accept')
    ->whereNumber('bid');

require __DIR__.'/auth.php';
