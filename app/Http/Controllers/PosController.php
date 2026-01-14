<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\StokLog;
use App\Models\Pembayaran;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class PosController extends Controller
{
    public function index(Request $request, Transaksi $transaksi = null)
    {
        $user = Auth::user();
        $kasirId = $user->id;
        $activeDraft = null;

        if ($transaksi && $transaksi->exists) {
            if ($user->role == 'admin') {
                if ($transaksi->status == 'draft') $activeDraft = $transaksi;
            } else {
                if ($transaksi->status == 'draft' && $transaksi->kasir_id == $kasirId) $activeDraft = $transaksi;
            }
            if (!$activeDraft) return redirect()->route('pos.index')->with('toast_danger', 'Draft tidak valid.');
        } else {
            $activeDraft = Transaksi::where('kasir_id', $kasirId)
                ->where('status', 'draft')
                ->latest('tanggal')
                ->first();
        }

        if (!$activeDraft) $activeDraft = $this->createEmptyDraft($kasirId);

        $activeDraft->load(['details.produk', 'pelanggan', 'kasir']);

        $pendingDraftsQuery = Transaksi::with(['details', 'pelanggan', 'kasir'])
            ->where('status', 'draft')
            ->where('id', '!=', $activeDraft->id);
        
        if ($user->role == 'kasir') {
            $pendingDraftsQuery->where('kasir_id', $kasirId);
        }
        $pendingDrafts = $pendingDraftsQuery->latest('tanggal')->get();

        $search = $request->query('search');
        $produksQuery = Produk::with('kategori')->where('stok', '>', 0); 
        
        if ($search) {
            $produksQuery->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_barcode', 'like', "%{$search}%");
            });
        }
        $produks = $produksQuery->orderBy('nama_produk', 'asc')->limit(50)->get();
        
        $pelanggans = Pelanggan::orderBy('nama', 'asc')->get();

        return view('pos.index', compact('produks', 'pelanggans', 'activeDraft', 'pendingDrafts', 'search'));
    }

    private function createEmptyDraft($kasirId)
    {
        return Transaksi::create([
            'kasir_id' => $kasirId,
            'status' => 'draft',
            'tanggal' => Carbon::now('Asia/Jakarta'),
            'total' => 0, 'diskon' => 0, 'pajak' => 0,
        ]);
    }

    private function recalculateTransactionTotal(Transaksi $transaksi)
    {
        $subtotal = $transaksi->details()->sum(DB::raw('harga_satuan * jumlah'));
        $transaksi->total = $subtotal; 
        $transaksi->save();
    }

    public function buatDraftBaru()
    {
        $newDraft = $this->createEmptyDraft(Auth::id());
        return redirect()->route('pos.index', ['transaksi' => $newDraft->id])
            ->with('toast_success', 'Draft baru dibuat.');
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'transaksi_id' => 'required|exists:transaksi,id',
        ]);

        $produk = Produk::find($validated['produk_id']);
        $transaksi = Transaksi::find($validated['transaksi_id']);

        if ($produk->stok <= 0) {
            return back()->with('toast_danger', 'Stok habis!');
        }

        $item = $transaksi->details()->where('produk_id', $produk->id)->first();
        $jumlahDiKeranjang = $item ? $item->jumlah : 0;

        if (($jumlahDiKeranjang + 1) > $produk->stok) {
            return back()->with('toast_danger', 'Stok tidak cukup. Sisa: ' . $produk->stok);
        }

        if ($item) {
            $item->increment('jumlah');
            $item->update(['subtotal' => $item->jumlah * $item->harga_satuan]);
        } else {
            $transaksi->details()->create([
                'produk_id' => $produk->id,
                'jumlah' => 1,
                'harga_satuan' => $produk->harga_jual,
                'subtotal' => $produk->harga_jual,
            ]);
        }

        $this->recalculateTransactionTotal($transaksi);
        return redirect()->route('pos.index', ['transaksi' => $transaksi->id]);
    }

    public function updateItem(Request $request)
    {
        $validated = $request->validate([
            'transaksi_detail_id' => 'required|exists:transaksi_detail,id',
            'qty' => 'required|integer|min:1',
        ]);

        $item = TransaksiDetail::find($validated['transaksi_detail_id']);
        
        if ($validated['qty'] > $item->produk->stok) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Melebihi stok! Sisa: ' . $item->produk->stok
                ], 422);
            }
            return back()->with('toast_danger', 'Melebihi stok! Sisa: ' . $item->produk->stok);
        }

        $item->update([
            'jumlah' => $validated['qty'],
            'subtotal' => $validated['qty'] * $item->harga_satuan
        ]);

        $this->recalculateTransactionTotal($item->transaksi);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'item_subtotal' => number_format($item->subtotal, 0, ',', '.'),
                'item_subtotal_raw' => $item->subtotal,
                'transaksi_total' => number_format($item->transaksi->total, 0, ',', '.'),
                'transaksi_total_raw' => $item->transaksi->total,
                'message' => 'Jumlah berhasil diupdate'
            ]);
        }

        return back();
    }

    public function removeItem(Request $request)
    {
        $item = TransaksiDetail::findOrFail($request->transaksi_detail_id);
        $transaksi = $item->transaksi;
        $item->delete();
        $this->recalculateTransactionTotal($transaksi);
        return back();
    }

    public function saveCustomerToDraft(Request $request)
    {
        $transaksi = Transaksi::findOrFail($request->transaksi_id);
        $pelangganId = null;

        if ($request->filled('nama_pelanggan_baru')) {
            $namaBaru = $request->nama_pelanggan_baru;
            $noHpBaru = $request->no_hp_baru ?? '-'; // Ambil No HP dari input baru
            
            $existing = Pelanggan::where('nama', $namaBaru)->first();
            
            if ($existing) {
                $pelangganId = $existing->id;
            } else {
                $newPelanggan = Pelanggan::create([
                    'nama' => $namaBaru,
                    'no_hp' => $noHpBaru,      
                    'alamat' => '-',     
                    'email' => null,     
                    'member_level' => 'Regular',
                    'poin' => 0
                ]);
                $pelangganId = $newPelanggan->id;
            }
        } 
        elseif ($request->filled('pelanggan_id')) {
            $pelangganId = $request->pelanggan_id;
        }

        $transaksi->update(['pelanggan_id' => $pelangganId]);
        
        $namaPelanggan = $transaksi->pelanggan->nama ?? 'Pelanggan Umum';

        return back()->with('toast_success', "Pelanggan diatur ke: $namaPelanggan");
    }

    public function cancelDraft(Request $request)
    {
        $transaksi = Transaksi::findOrFail($request->transaksi_id);
        if ($transaksi->status == 'draft') {
            $transaksi->details()->delete(); 
            $transaksi->delete();
        }
        return redirect()->route('pos.index')->with('toast_danger', 'Draft dihapus.');
    }

    public function searchMember(Request $request)
    {
        $no_hp = $request->no_hp;
        $member = Pelanggan::where('no_hp', $no_hp)->first();

        if ($member) {
            return response()->json([
                'valid' => true,
                'member' => $member,
                'message' => 'Member ditemukan!'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Member tidak ditemukan.'
        ]);
    }

    public function storeNewMember(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20|unique:pelanggan,no_hp',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $member = Pelanggan::create([
            'nama' => $request->nama,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat ?? '-',
            'email' => $request->email,
            'poin' => 0,
            'member_level' => 'Member'
        ]);

        // Jika ada transaksi_id, otomatis set pelanggan ke transaksi tersebut
        if ($request->has('transaksi_id')) {
            $transaksi = Transaksi::find($request->transaksi_id);
            if ($transaksi && $transaksi->status == 'draft') {
                $transaksi->update(['pelanggan_id' => $member->id]);
            }
        }

        return back()->with('toast_success', 'Member baru berhasil didaftarkan & dipilih!');
    }

    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'transaksi_id' => 'required|exists:transaksi,id'
        ]);

        $barcode = $request->barcode;
        $transaksiId = $request->transaksi_id;

        // Cari Produk by Barcode
        $produk = Produk::where('kode_barcode', $barcode)->first();

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan!'
            ], 404);
        }

        if ($produk->stok <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => "Stok {$produk->nama_produk} habis!"
            ], 400);
        }

        $transaksi = Transaksi::find($transaksiId);
        $item = $transaksi->details()->where('produk_id', $produk->id)->first();
        
        $currentQty = $item ? $item->jumlah : 0;

        if (($currentQty + 1) > $produk->stok) {
            return response()->json([
                'status' => 'error',
                'message' => "Stok tidak cukup. Sisa: {$produk->stok}"
            ], 400);
        }

        // Add or Update Item
        if ($item) {
            $item->increment('jumlah');
            $item->update(['subtotal' => $item->jumlah * $item->harga_satuan]);
        } else {
            $transaksi->details()->create([
                'produk_id' => $produk->id,
                'jumlah' => 1,
                'harga_satuan' => $produk->harga_jual,
                'subtotal' => $produk->harga_jual,
            ]);
        }

        $this->recalculateTransactionTotal($transaksi);

        return response()->json([
            'status' => 'success',
            'message' => "{$produk->nama_produk} berhasil ditambahkan!",
            'cart_total' => number_format($transaksi->total, 0, ',', '.')
        ]);
    }

    public function showCheckoutForm(Transaksi $transaksi)
    {
        if ($transaksi->status != 'draft') return redirect()->route('pos.index');
        
        $subtotal = $transaksi->details->sum('subtotal');
        if ($subtotal <= 0) return back()->with('toast_danger', 'Keranjang kosong!');

        $taxRate = Cache::rememberForever('ppn_tax_rate', fn() => Setting::where('key','ppn_tax_rate')->value('value') ?? 0.11);

        return view('pos.checkout', [
            'transaksi' => $transaksi,
            'cart' => $transaksi->details,
            'pelanggan' => $transaksi->pelanggan,
            'subtotal' => $subtotal,
            'taxRate' => (float)$taxRate
        ]);
    }

    public function checkVoucher(Request $request)
    {
        $code = $request->voucher_code;
        $subtotal = $request->subtotal;
        
        $voucher = Voucher::where('kode', $code)->where('is_active', true)->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Voucher tidak valid']);
        }
        
        $disc = ($voucher->tipe == 'nominal') ? $voucher->nilai : ($subtotal * $voucher->nilai / 100);
        if ($disc > $subtotal) $disc = $subtotal;

        return response()->json([
            'valid' => true, 
            'discount_amount' => $disc, 
            'message' => 'Voucher diterapkan!'
        ]);
    }

    public function storeCheckout(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'metode_bayar' => 'required|string', 
            'nominal_bayar' => 'required|numeric|min:0',
            'voucher_code'  => 'nullable|string|exists:vouchers,kode',
            'poin_tukar'    => 'nullable|integer|min:0', 
            'pelanggan_id'  => 'nullable|exists:pelanggan,id', // Validasi Pelanggan ID
        ]);

        // Update Pelanggan jika ada perubahan di checkout (dari fitur Cek Member)
        if ($request->filled('pelanggan_id')) {
            $transaksi->update(['pelanggan_id' => $request->pelanggan_id]);
            $transaksi->load('pelanggan'); // Refresh relasi
        }

        $subtotal = $transaksi->details->sum('subtotal');
        if ($subtotal == 0) return back()->with('toast_danger', 'Keranjang kosong!');

        // 1. Hitung Diskon Voucher
        $diskonVoucher = 0;
        if ($request->voucher_code) {
            $voucher = Voucher::where('kode', $request->voucher_code)->first();
            if ($voucher && $voucher->is_active) {
                $diskonVoucher = ($voucher->tipe == 'nominal') ? $voucher->nilai : ($subtotal * $voucher->nilai / 100);
            }
        }

        // 2. Hitung Diskon Poin
        $diskonPoin = 0;
        $poinDigunakan = 0;
        $pelanggan = $transaksi->pelanggan;

        if ($pelanggan && $request->poin_tukar > 0) {
            $nilaiPerPoin = Setting::where('key', 'loyalty_nilai_rupiah_per_poin')->value('value') ?? 0;
            $maxPoin = $pelanggan->poin;
            
            // Pastikan tidak pakai poin lebih dari yang dimiliki
            $poinDigunakan = min($request->poin_tukar, $maxPoin);
            $diskonPoin = $poinDigunakan * $nilaiPerPoin;
        }

        // Total Diskon (Gabungan)
        $totalDiskon = $diskonVoucher + $diskonPoin;
        if ($totalDiskon > $subtotal) {
            $totalDiskon = $subtotal; // Cap diskon max sebesar subtotal
            // Opsional: Recalculate poin used jika kena cap, tapi biarkan dulu untuk simplifikasi
        }

        $taxRate = Cache::rememberForever('ppn_tax_rate', fn() => 0.11);
        $totalSetelahDiskon = $subtotal - $totalDiskon;
        $pajak = $totalSetelahDiskon * $taxRate;
        $grandTotal = round($totalSetelahDiskon + $pajak);

        if ($request->metode_bayar == 'Tunai' && $request->nominal_bayar < $grandTotal) {
            return back()->withErrors(['nominal_bayar' => 'Uang tunai kurang!']);
        }

        try {
            DB::beginTransaction();

            $isTunai = ($request->metode_bayar == 'Tunai');
            $statusAwal = $isTunai ? 'selesai' : 'pending'; 
            
            $orderIdMidtrans = 'POS-' . $transaksi->id . '-' . time();

            // 3. Hitung Poin yang DIDAPAT (Earned)
            $poinDidapat = 0;
            if ($pelanggan) {
                $minTrxPoin = Setting::where('key', 'loyalty_min_transaksi')->value('value') ?? 50000;
                $nominalPerPoin = Setting::where('key', 'loyalty_nominal_per_poin')->value('value') ?? 10000;

                if ($grandTotal >= $minTrxPoin && $nominalPerPoin > 0) {
                    $poinDidapat = floor($grandTotal / $nominalPerPoin);
                }
            }

            $transaksi->update([
                'tanggal' => Carbon::now('Asia/Jakarta'),
                'kasir_id' => Auth::id(),
                'total' => $grandTotal,
                'diskon' => $totalDiskon,
                'pajak' => $pajak,
                'metode_bayar' => $request->metode_bayar, 
                'nominal_bayar' => $request->nominal_bayar, 
                'kembalian' => $isTunai ? ($request->nominal_bayar - $grandTotal) : 0,
                'status' => $statusAwal,
                'catatan' => $orderIdMidtrans,
                // Update kolom poin baru
                'poin_earned' => $poinDidapat,
                'poin_used' => $poinDigunakan
            ]);

            // Update Stok & Poin Pelanggan
            foreach ($transaksi->details as $item) {
                if ($item->produk->stok < $item->jumlah) {
                    throw new \Exception("Stok {$item->produk->nama_produk} tidak cukup!");
                }
                
                $item->produk->decrement('stok', $item->jumlah);
                
                StokLog::create([
                    'produk_id' => $item->produk_id,
                    'tanggal' => Carbon::now(),
                    'tipe' => 'keluar',
                    'jumlah' => $item->jumlah,
                    'sumber' => 'Penjualan',
                    'keterangan' => 'Trx #' . $transaksi->id,
                    'user_id' => Auth::id()
                ]);
            }

            // Update Poin Pelanggan (Hanya jika status selesai/tunai)
            if ($isTunai && $pelanggan) {
                if ($poinDigunakan > 0) {
                    $pelanggan->poin -= $poinDigunakan;
                }
                if ($poinDidapat > 0) {
                    $pelanggan->poin += $poinDidapat;
                }
                $pelanggan->recalculateLevel(); // Hitung ulang level (Silver/Gold)
            }

            if ($isTunai) {
                Pembayaran::create([
                    'transaksi_id' => $transaksi->id,
                    'metode' => 'Tunai',
                    'jumlah' => $grandTotal,
                    'referensi' => 'Cashier'
                ]);
                DB::commit();
                return redirect()->route('transaksi.show', $transaksi)->with('toast_success', 'Transaksi Selesai!');
            }

            // --- PERBAIKAN: MENGGUNAKAN ENV() AGAR TIDAK HARDCODE ---
            Config::$serverKey = env('MIDTRANS_SERVER_KEY'); 
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderIdMidtrans,
                    'gross_amount' => (int)$grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $transaksi->pelanggan ? $transaksi->pelanggan->nama : 'Guest',
                    'email' => $transaksi->pelanggan->email ?? 'customer@pos.com', 
                ],
                'item_details' => [
                    [
                        'id' => 'TRX-' . $transaksi->id,
                        'price' => (int)$grandTotal,
                        'quantity' => 1,
                        'name' => 'Pembayaran Kasir POS'
                    ]
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            
            DB::commit();

            return view('pos.midtrans-pay', compact('snapToken', 'transaksi'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('toast_danger', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    public function midtransCallback(Request $request)
    {
        // --- PERBAIKAN: MENGGUNAKAN ENV() ---
        $serverKey = env('MIDTRANS_SERVER_KEY'); 
        
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $parts = explode('-', $request->order_id);
            $transaksiId = $parts[1] ?? null;

            if (!$transaksiId) return response()->json(['message' => 'Invalid Order ID'], 400);

            $transaksi = Transaksi::find($transaksiId);
            if (!$transaksi) return response()->json(['message' => 'Not Found'], 404);

            $status = $request->transaction_status;
            $type = $request->payment_type;
            
            if ($status == 'capture' || $status == 'settlement') {
                if ($transaksi->status != 'selesai') {
                    $transaksi->update([
                        'status' => 'selesai',
                        'metode_bayar' => $type 
                    ]);
                    
                    Pembayaran::create([
                        'transaksi_id' => $transaksi->id,
                        'metode' => $type,
                        'jumlah' => $request->gross_amount,
                        'referensi' => $request->transaction_id
                    ]);
                }
            } else if ($status == 'cancel' || $status == 'expire' || $status == 'deny') {
                
                if ($transaksi->status != 'batal') {
                    $transaksi->update(['status' => 'batal']);
                    foreach ($transaksi->details as $item) {
                        $item->produk->increment('stok', $item->jumlah);
                        
                        StokLog::create([
                            'produk_id' => $item->produk_id,
                            'tanggal' => Carbon::now(),
                            'tipe' => 'masuk',
                            'jumlah' => $item->jumlah,
                            'sumber' => 'Pembatalan',
                            'keterangan' => 'Batal System Trx #' . $transaksi->id,
                            'user_id' => 0 
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function handlePaymentSuccess(Transaksi $transaksi)
    {
        if ($transaksi->status == 'pending') {
            $transaksi->update(['status' => 'selesai']);
            
            Pembayaran::create([
                'transaksi_id' => $transaksi->id,
                'metode' => 'Midtrans (Redirect)',
                'jumlah' => $transaksi->total,
                'referensi' => 'Local-Success'
            ]);
        }

        return redirect()->route('pos.index')->with('toast_success', 'Pembayaran Berhasil!');
    }

    public function cancelPendingTransaction(Transaksi $transaksi)
    {
        if ($transaksi->status == 'pending') {
            
            try {
                DB::beginTransaction();

                foreach ($transaksi->details as $item) {
                    $item->produk->increment('stok', $item->jumlah);

                    StokLog::create([
                        'produk_id' => $item->produk_id,
                        'tanggal' => Carbon::now(),
                        'tipe' => 'masuk',
                        'jumlah' => $item->jumlah,
                        'sumber' => 'Pembatalan',
                        'keterangan' => 'Batal Manual Kasir Trx #' . $transaksi->id,
                        'user_id' => Auth::id()
                    ]);
                }

                $transaksi->update(['status' => 'batal']);

                DB::commit();
                return redirect()->route('pos.index')->with('toast_danger', 'Transaksi berhasil dibatalkan!');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('pos.index')->with('toast_danger', 'Gagal membatalkan: ' . $e->getMessage());
            }
        }

        return redirect()->route('pos.index');
    }
}