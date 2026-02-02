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
use App\Http\Controllers\AbsensiController; 
use Illuminate\Support\Facades\Route;

// ===========================================
// RUTE PUBLIC (BISA DIAKSES TANPA LOGIN)
// ===========================================

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/scan-absensi', [AbsensiController::class, 'index'])->name('scan.absensi');
Route::post('/proses-absensi', [AbsensiController::class, 'store'])->name('absensi.store');

// Midtrans Callback (Diakses oleh Server Midtrans)
Route::post('/midtrans-callback', [PosController::class, 'midtransCallback']);


// ===========================================
// RUTE AUTH (HARUS LOGIN DULU)
// ===========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Middleware tambahan: Cek apakah user sudah absen pulang
    Route::middleware(['not.clocked.out'])->group(function () {

    // 1. DASHBOARD
    Route::post('/absensi/clock-out', [AbsensiController::class, 'storeClockOutWeb'])->name('absensi.clock_out');
    Route::get('/dashboard', function () {
        $today = Carbon::today('Asia/Jakarta');
        
        // --- 1. Statistik Utama ---
        $jumlahPelanggan = Pelanggan::count();
        $jumlahProduk = Produk::count();
        
        // Transaksi Hari Ini (Selesai)
        $transaksiHariIni = Transaksi::whereDate('tanggal', $today)
                                     ->where('status', 'selesai') 
                                     ->get();
        $totalPendapatanHariIni = $transaksiHariIni->sum('total');
        $jumlahTransaksiHariIni = $transaksiHariIni->count();

        // --- 2. Peringatan Stok Menipis (Limit 5) ---
        $batasStokMenipis = \App\Models\Setting::where('key', 'stok_minimum')->value('value') ?? 5;
        $stokMenipis = Produk::where('stok', '<=', $batasStokMenipis)->orderBy('stok', 'asc')->limit(5)->get();

        // --- 3. Transaksi Terbaru (Limit 5) ---
        $transaksiTerbaru = Transaksi::with('pelanggan', 'kasir')
                                     ->where('status', 'selesai')
                                     ->latest('tanggal')
                                     ->limit(5)
                                     ->get();

        // --- 4. Grafik Pendapatan 7 Hari Terakhir ---
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now('Asia/Jakarta')->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartValues[] = Transaksi::whereDate('tanggal', $date)
                                      ->where('status', 'selesai')
                                      ->sum('total');
        }

        // --- 5. Produk Terlaris (Top 5) ---
        // Menggunakan join atau relation counting yang efisien
        $produkTerlaris = \Illuminate\Support\Facades\DB::table('transaksi_detail')
            ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.id')
            ->join('transaksi', 'transaksi_detail.transaksi_id', '=', 'transaksi.id')
            ->where('transaksi.status', 'selesai')
            ->select('produk.nama_produk', \Illuminate\Support\Facades\DB::raw('SUM(transaksi_detail.jumlah) as total_sold'))
            ->groupBy('produk.id', 'produk.nama_produk')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Data Absensi User Login Hari Ini
        $absensiHariIni = \App\Models\Absensi::where('user_id', auth()->id())
                                             ->where('tanggal', $today->format('Y-m-d'))
                                             ->first();
        
        $jamPulangSetting = \App\Models\Setting::where('key', 'jam_pulang_kantor')->value('value') ?? '17:00';
        
        // Parse jam pulang safely
        try {
            $jamPulang = \Carbon\Carbon::createFromFormat('H:i', substr($jamPulangSetting, 0, 5), 'Asia/Jakarta');
            // Set date to today so we compare times on the same day
            $jamPulang->setDate($today->year, $today->month, $today->day);
            $isWaktunyaPulang = \Carbon\Carbon::now('Asia/Jakarta')->greaterThanOrEqualTo($jamPulang);
        } catch (\Exception $e) {
            // Fallback if format invalid
            $isWaktunyaPulang = false;
        }

        return view('dashboard', [
            'jumlahPelanggan' => $jumlahPelanggan,
            'jumlahProduk' => $jumlahProduk,
            'totalPendapatanHariIni' => $totalPendapatanHariIni,
            'jumlahTransaksiHariIni' => $jumlahTransaksiHariIni,
            'stokMenipis' => $stokMenipis,
            'transaksiTerbaru' => $transaksiTerbaru,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'produkTerlaris' => $produkTerlaris,
            'absensiHariIni' => $absensiHariIni,
            'isWaktunyaPulang' => $isWaktunyaPulang,
            'jamPulangSetting' => $jamPulangSetting
        ]);
    })->name('dashboard');

    Route::post('/chat-ai', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/request-pin', [ProfileController::class, 'requestPinChange'])->name('profile.request_pin');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('transaksi/{transaksi}/cetak-struk', [TransaksiController::class, 'cetakStruk'])->name('transaksi.cetak_struk');


    // ===========================================
    // GROUP: ADMIN & KASIR (Akses POS/Input Transaksi)
    // ===========================================

    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,kasir'])->group(function () {
        Route::get('pos/{transaksi?}', [PosController::class, 'index'])->name('pos.index');
        Route::get('pos-new-draft', [PosController::class, 'buatDraftBaru'])->name('pos.new_draft');
        Route::get('pos-search-member', [PosController::class, 'searchMember'])->name('pos.search_member'); 
        Route::post('pos/store-member', [PosController::class, 'storeNewMember'])->name('pos.store_member'); // Route Tambah Member via POS
        Route::post('pos/scan', [PosController::class, 'scanBarcode'])->name('pos.scan'); // Route Scan Barcode
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
    // GROUP: ADMIN & OWNER & KASIR (Data Pelanggan)
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,owner,kasir'])->group(function () {
        Route::resource('pelanggan', PelangganController::class);
    });

    // ===========================================
    // GROUP: KHUSUS ADMIN & OWNER (Laporan Keuangan & Stok)
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin,owner'])->group(function () {
        Route::get('stok-log', [StokLogController::class, 'index'])->name('stok_log.index');
        Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        
        Route::get('laporan/pendapatan', [LaporanPendapatanController::class, 'index'])->name('laporan.pendapatan');
        Route::get('laporan/pendapatan/pdf', [LaporanPendapatanController::class, 'exportPdf'])->name('laporan.pendapatan.pdf');
        Route::get('laporan/pendapatan/excel', [LaporanPendapatanController::class, 'exportExcel'])->name('laporan.pendapatan.excel');

        Route::get('laporan/absensi', [App\Http\Controllers\LaporanAbsensiController::class, 'index'])->name('laporan.absensi');
        Route::get('laporan/absensi/{user}', [App\Http\Controllers\LaporanAbsensiController::class, 'show'])->name('laporan.absensi.show');
        Route::get('laporan/absensi/{user}/print', [App\Http\Controllers\LaporanAbsensiController::class, 'print'])->name('laporan.absensi.print');
    });


    // ===========================================
    // GROUP: STRICT ADMIN (Hanya Admin)
    // Manajemen Master Data, User, Setting
    // ===========================================
    Route::middleware([\App\Http\Middleware\CheckRoleMiddleware::class . ':admin'])->group(function () {
        
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class); 

        Route::get('stok-log/create', [StokLogController::class, 'create'])->name('stok_log.create');
        Route::post('stok-log', [StokLogController::class, 'store'])->name('stok_log.store');

        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        
        Route::resource('users', UserController::class);
        Route::get('users/{id}/cetak-kartu', [UserController::class, 'cetakKartu'])->name('users.cetak_kartu');
        Route::patch('users/{user}/approve-pin', [UserController::class, 'approvePin'])->name('users.approve_pin');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/{user}/view-pin', [UserController::class, 'viewPin'])->name('users.view_pin');
        Route::post('users/{user}/reset-pin', [UserController::class, 'resetPin'])->name('users.reset_pin');
        
        Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
        Route::delete('vouchers/{id}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
        Route::patch('vouchers/{id}/toggle', [VoucherController::class, 'toggleStatus'])->name('vouchers.toggle');
    }); 

    }); // End Middleware not.clocked.out

});

require __DIR__.'/auth.php';