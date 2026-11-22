<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

// routes/web.php
Route::get('/', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::middleware(['auth','role:client'])->group(function () {
    Route::resource('projects', ProjectController::class)->except(['index','show']);
});

require __DIR__.'/auth.php';
