<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PertandinganController;

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

    Route::get('/pertandingan', [PertandinganController::class, 'index'])->name('pertandingan.index');
    Route::post('/pertandingan', [PertandinganController::class, 'store'])->name('pertandingan.store');
    Route::get('/pertandingan/{id}/mulai', [PertandinganController::class, 'mulai'])->name('pertandingan.mulai');

    Route::post('/skor/tambah', [SkorController::class, 'tambah'])->name('skor.tambah');
    Route::get('/skor/history/{kelompok_id}', [SkorController::class, 'history'])->name('skor.history');
});

require __DIR__ . '/auth.php';
