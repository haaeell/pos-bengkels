<?php

use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\riwayatTransaksiController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    Route::get('/services', [PosController::class, 'services'])->name('services.index');
    Route::post('/services/store', [PosController::class, 'servicesStore'])->name('services.store');
    Route::resource('products', ProductController::class);
    Route::get('/transaksi-harian', [riwayatTransaksiController::class, 'dailyTransactions'])->name('transaksi.harian');
    Route::get('/transaksi-bulanan', [riwayatTransaksiController::class, 'monthlyTransactions'])->name('transaksi.bulanan');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('/sales', [riwayatTransaksiController::class, 'sales'])->name('penjualan');

    Route::resource('suppliers', SupplierController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
});

Auth::routes();
