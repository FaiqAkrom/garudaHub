<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::middleware(['auth','role:client'])->group(function () {
    Route::resource('projects', ProjectController::class)->except(['index','show']);
});
Route::middleware(['auth','role:developer'])->group(function () {
    Route::post('/projects/{project}/bids', [BidController::class, 'store'])->name('bids.store');
    Route::put('/bids/{bid}', [BidController::class, 'update'])->name('bids.update');
    Route::delete('/bids/{bid}', [BidController::class, 'destroy'])->name('bids.destroy');
});
Route::post('/bids/{bid}/accept', [BidController::class, 'accept'])
    ->middleware(['auth','role:client'])->name('bids.accept');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

require __DIR__.'/auth.php';
