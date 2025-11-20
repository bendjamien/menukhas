<?php

use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon; 

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StokLogController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanPendapatanController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    
    $today = Carbon::today('Asia/Jakarta');
    $jumlahPelanggan = Pelanggan::count();
    $jumlahProduk = Produk::count();
    
    $transaksiHariIni = Transaksi::whereDate('tanggal', $today)
                                 ->where('status', 'selesai') 
                                 ->get();
    
    $totalPendapatanHariIni = $transaksiHariIni->sum('total');
    $jumlahTransaksiHariIni = $transaksiHariIni->count();

    return view('dashboard', [
        'jumlahPelanggan' => $jumlahPelanggan,
        'jumlahProduk' => $jumlahProduk,
        'totalPendapatanHariIni' => $totalPendapatanHariIni,
        'jumlahTransaksiHariIni' => $jumlahTransaksiHariIni,
    ]);

})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    
    // ===========================================
    // RUTE SEMUA USERNYA
    // ===========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('pelanggan', PelangganController::class);

    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

    Route::post('pos/check-voucher', [PosController::class, 'checkVoucher'])->name('pos.check_voucher');



    // ===========================================
    // RUTE KASIRNYA 
    // ===========================================
    
    Route::get('pos/{transaksi?}', [PosController::class, 'index'])->name('pos.index');
    
    Route::get('pos-new-draft', [PosController::class, 'buatDraftBaru'])->name('pos.new_draft');
    
    Route::post('pos/add-item', [PosController::class, 'addItem'])->name('pos.add_item');
    
    Route::post('pos/update-item', [PosController::class, 'updateItem'])->name('pos.update_item');
    
    Route::post('pos/remove-item', [PosController::class, 'removeItem'])->name('pos.remove_item');
    
    Route::post('pos/cancel-draft', [PosController::class, 'cancelDraft'])->name('pos.cancel_draft');

    Route::post('pos/save-customer', [PosController::class, 'saveCustomerToDraft'])->name('pos.save_customer');

    Route::get('pos/checkout/{transaksi}', [PosController::class, 'showCheckoutForm'])->name('pos.checkout.show');
    
    Route::post('pos/checkout/{transaksi}', [PosController::class, 'storeCheckout'])->name('pos.checkout.store');
    

    
    // ===========================================
    // RUTE ADMINNYA
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin'])->group(function () {
        
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class); 

        Route::get('stok-log', [StokLogController::class, 'index'])->name('stok_log.index');
        Route::get('stok-log/create', [StokLogController::class, 'create'])->name('stok_log.create');
        Route::post('stok-log', [StokLogController::class, 'store'])->name('stok_log.store');

        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');

        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

        Route::get('laporan/pendapatan', [LaporanPendapatanController::class, 'index'])->name('laporan.pendapatan');

        Route::post('vouchers', [App\Http\Controllers\VoucherController::class, 'store'])->name('vouchers.store');
        Route::delete('vouchers/{id}', [App\Http\Controllers\VoucherController::class, 'destroy'])->name('vouchers.destroy');
        Route::patch('vouchers/{id}/toggle', [App\Http\Controllers\VoucherController::class, 'toggleStatus'])->name('vouchers.toggle');

        Route::resource('users', UserController::class);
          Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    }); 
    
});

require __DIR__.'/auth.php';