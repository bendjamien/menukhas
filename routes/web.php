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
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// ===========================================
// RUTE PUBLIC
// ===========================================
Route::get('/', function () {
    return view('welcome');
});

// Midtrans Callback (Sebaiknya di luar auth karena diakses oleh Server Midtrans)
Route::post('/midtrans-callback', [PosController::class, 'midtransCallback']);

// ===========================================
// RUTE AUTH (Login Required)
// ===========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. DASHBOARD (Semua User Login)
    // Nanti di Sidebar kita sembunyikan linknya untuk Owner jika mau
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
    })->name('dashboard');

    // 2. PROFILE & CHAT (Semua User)
    Route::post('/chat-ai', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 3. LAPORAN TRANSAKSI (Semua User Boleh Lihat Riwayat)
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');


    // ===========================================
    // GROUP: ADMIN & KASIR (Akses POS/Input Transaksi)
    // Owner TIDAK BOLEH akses ini
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,kasir'])->group(function () {
        Route::get('pos/{transaksi?}', [PosController::class, 'index'])->name('pos.index');
        Route::get('pos-new-draft', [PosController::class, 'buatDraftBaru'])->name('pos.new_draft');
        Route::post('pos/add-item', [PosController::class, 'addItem'])->name('pos.add_item');
        Route::post('pos/update-item', [PosController::class, 'updateItem'])->name('pos.update_item');
        Route::post('pos/remove-item', [PosController::class, 'removeItem'])->name('pos.remove_item');
        Route::post('pos/cancel-draft', [PosController::class, 'cancelDraft'])->name('pos.cancel_draft');
        Route::post('pos/save-customer', [PosController::class, 'saveCustomerToDraft'])->name('pos.save_customer');
        Route::get('pos/checkout/{transaksi}', [PosController::class, 'showCheckoutForm'])->name('pos.checkout.show');
        Route::post('pos/checkout/{transaksi}', [PosController::class, 'storeCheckout'])->name('pos.checkout.store');
        Route::get('pos/payment-success/{transaksi}', [PosController::class, 'handlePaymentSuccess'])->name('pos.payment_success');
        Route::get('pos/cancel-pending/{transaksi}', [PosController::class, 'cancelPendingTransaction'])->name('pos.cancel_pending');
        Route::post('pos/check-voucher', [PosController::class, 'checkVoucher'])->name('pos.check_voucher');
    });


    // ===========================================
    // GROUP: ADMIN & OWNER (Akses Laporan Lengkap & Data Pelanggan)
    // Kasir TIDAK BOLEH akses ini (kecuali Pelanggan, biasanya kasir perlu create, 
    // tapi di sini kita taruh Resource Pelanggan agar Owner bisa lihat datanya)
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,owner,kasir'])->group(function () {
        // Kita izinkan kasir akses pelanggan juga untuk tambah data
        Route::resource('pelanggan', PelangganController::class);
    });

    // KHUSUS ADMIN & OWNER (Laporan Keuangan & Stok)
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,owner'])->group(function () {
        Route::get('stok-log', [StokLogController::class, 'index'])->name('stok_log.index');
        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('laporan/pendapatan', [LaporanPendapatanController::class, 'index'])->name('laporan.pendapatan');
    });


    // ===========================================
    // GROUP: STRICT ADMIN (Hanya Admin)
    // Manajemen Master Data, User, Setting
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin'])->group(function () {
        
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class); 

        // Admin boleh input manual stok log
        Route::get('stok-log/create', [StokLogController::class, 'create'])->name('stok_log.create');
        Route::post('stok-log', [StokLogController::class, 'store'])->name('stok_log.store');

        // Setting & User
        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Voucher
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::delete('vouchers/{id}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
        Route::patch('vouchers/{id}/toggle', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle');
    }); 

});

require __DIR__.'/auth.php';