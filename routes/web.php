<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;

// Lindungi seluruh aplikasi dengan auth, kecuali route auth.*
Route::middleware('auth')->group(function () {
    Route::get('/', [KasirController::class,'index'])->name('pos.index');
    Route::post('/pos/order', [KasirController::class,'store'])->name('pos.store');
    Route::get('/pos/nota/{id}', [KasirController::class,'nota'])->name('pos.nota');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('barang', BarangController::class)->except(['show']);
    Route::resource('transaksi', TransaksiController::class)->only(['index','create','store','destroy']);
    Route::resource('vendors', VendorController::class)->except(['show']);
    // Kategori (file-based, no DB table)
    Route::get('/kategori', [KategoriController::class,'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class,'store'])->name('kategori.store');
    Route::put('/kategori/{kode}', [KategoriController::class,'update'])->name('kategori.update');
    Route::delete('/kategori/{kode}', [KategoriController::class,'destroy'])->name('kategori.destroy');

    // Stok management
    Route::get('/stok', [StokController::class,'index'])->name('stok.index');
    Route::get('/stok/tambah', [StokController::class,'create'])->name('stok.create');
    Route::post('/stok', [StokController::class,'store'])->name('stok.store');
    Route::get('/stok/{barang}', [StokController::class,'show'])->name('stok.show');

    // Profit summary
    Route::get('/profit', [ProfitController::class,'index'])->name('profit.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
