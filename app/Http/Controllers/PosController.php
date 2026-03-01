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

    public function splitBill(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'items' => 'required|array|min:1',
            'items.*.detail_id' => 'required|exists:transaksi_detail,id',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $oldTrx = Transaksi::findOrFail($request->transaksi_id);
            
            // 1. Buat Transaksi Baru (Draft)
            $newTrx = Transaksi::create([
                'kasir_id' => Auth::id(),
                'pelanggan_id' => $oldTrx->pelanggan_id,
                'tanggal' => now(),
                'status' => 'draft',
                'total' => 0
            ]);

            foreach ($request->items as $itemData) {
                $detail = TransaksiDetail::findOrFail($itemData['detail_id']);
                $qtyToMove = $itemData['qty'];

                if ($qtyToMove >= $detail->jumlah) {
                    // Pindahkan seluruhnya
                    $detail->update(['transaksi_id' => $newTrx->id]);
                } else {
                    // Pindahkan sebagian saja (split quantity)
                    // Kurangi qty di yang lama
                    $detail->decrement('jumlah', $qtyToMove);
                    $detail->update(['subtotal' => $detail->jumlah * $detail->harga_satuan]);

                    // Buat detail baru di yang baru
                    TransaksiDetail::create([
                        'transaksi_id' => $newTrx->id,
                        'produk_id' => $detail->produk_id,
                        'jumlah' => $qtyToMove,
                        'harga_satuan' => $detail->harga_satuan,
                        'subtotal' => $qtyToMove * $detail->harga_satuan
                    ]);
                }
            }

            // 2. Hitung ulang total keduanya
            $this->recalculateTransactionTotal($oldTrx);
            $this->recalculateTransactionTotal($newTrx);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tagihan berhasil dipisah ke Draft #' . $newTrx->id,
                'new_trx_id' => $newTrx->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
            'qty' => 'nullable|integer|min:1',
        ]);

        $qtyToAdd = $request->input('qty', 1);
        $produk = Produk::find($validated['produk_id']);
        $transaksi = Transaksi::find($validated['transaksi_id']);

        $item = $transaksi->details()->where('produk_id', $produk->id)->first();
        $jumlahDiKeranjang = $item ? $item->jumlah : 0;

        if (($jumlahDiKeranjang + $qtyToAdd) > $produk->stok) {
            $msg = 'Stok tidak cukup. Sisa: ' . ($produk->stok - $jumlahDiKeranjang);
            if ($request->expectsJson()) return response()->json(['status' => 'error', 'message' => $msg], 422);
            return back()->with('toast_danger', $msg);
        }

        if ($item) {
            $item->increment('jumlah', $qtyToAdd);
            $item->update(['subtotal' => $item->jumlah * $item->harga_satuan]);
        } else {
            $transaksi->details()->create([
                'produk_id' => $produk->id,
                'jumlah' => $qtyToAdd,
                'harga_satuan' => $produk->harga_jual,
                'subtotal' => $produk->harga_jual * $qtyToAdd,
            ]);
        }

        $this->recalculateTransactionTotal($transaksi);
        
        if ($request->expectsJson()) {
            return $this->getCartResponse($transaksi, "{$produk->nama_produk} ditambahkan!");
        }

        return redirect()->route('pos.index', ['transaksi' => $transaksi->id])
            ->with('toast_success', "<b>{$produk->nama_produk}</b> ditambahkan!");
    }

    public function updateItem(Request $request)
    {
        $validated = $request->validate([
            'transaksi_detail_id' => 'required|exists:transaksi_detail,id',
            'qty' => 'required|integer|min:1',
        ]);

        $item = TransaksiDetail::with('produk')->find($validated['transaksi_detail_id']);
        $transaksi = $item->transaksi;

        if ($validated['qty'] > ($item->produk->stok + $item->jumlah)) {
            $msg = 'Melebihi stok! Sisa: ' . ($item->produk->stok + $item->jumlah);
            if ($request->expectsJson()) return response()->json(['status' => 'error', 'message' => $msg], 422);
            return back()->with('toast_danger', $msg);
        }

        $item->update([
            'jumlah' => $validated['qty'],
            'subtotal' => $validated['qty'] * $item->harga_satuan
        ]);

        $this->recalculateTransactionTotal($transaksi);

        if ($request->expectsJson()) {
            return $this->getCartResponse($transaksi, 'Jumlah diperbarui.');
        }

        return back();
    }
    public function removeItem(Request $request)
    {
        $item = TransaksiDetail::findOrFail($request->transaksi_detail_id);
        $transaksi = $item->transaksi;
        $item->delete();
        $this->recalculateTransactionTotal($transaksi);

        if ($request->expectsJson()) {
            return $this->getCartResponse($transaksi, 'Item dihapus.');
        }

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
        $kode_member = $request->kode_member;

        $query = Pelanggan::query();

        if ($kode_member) {
            $query->where('kode_member', $kode_member);
        } else {
            $query->where('no_hp', $no_hp);
        }

        $member = $query->first();

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
        $transaksi = Transaksi::find($transaksiId);

        // 1. Cek apakah ini KODE MEMBER (Diawali MBR)
        if (str_starts_with($barcode, 'MBR')) {
            $pelanggan = Pelanggan::where('kode_member', $barcode)->first();
            if ($pelanggan) {
                $transaksi->update(['pelanggan_id' => $pelanggan->id]);
                return response()->json([
                    'status' => 'success',
                    'message' => "Member: {$pelanggan->nama} berhasil dipilih!",
                    'data' => [
                        'total_format' => number_format($transaksi->total, 0, ',', '.'),
                        'cart_count' => $transaksi->details->sum('jumlah'),
                        'pelanggan' => $pelanggan->nama,
                        'details' => $transaksi->details->map(function($item) {
                            return [
                                'id' => $item->id,
                                'nama_produk' => $item->produk->nama_produk,
                                'kategori' => $item->produk->kategori->nama ?? '-',
                                'stok_asli' => $item->produk->stok + $item->jumlah,
                                'harga_satuan' => number_format($item->harga_satuan, 0, ',', '.'),
                                'jumlah' => $item->jumlah,
                                'subtotal' => number_format($item->subtotal, 0, ',', '.')
                            ];
                        })
                    ]
                ]);
            }
        }

        // 2. Jika bukan member, maka anggap PRODUK
        $produk = Produk::where('kode_barcode', $barcode)->first();

        if (!$produk) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan!'], 404);
        }

        if ($produk->stok <= 0) {
            return response()->json(['status' => 'error', 'message' => "Stok {$produk->nama_produk} habis!"], 400);
        }

        $transaksi = Transaksi::find($transaksiId);
        $item = $transaksi->details()->where('produk_id', $produk->id)->first();
        
        if ($item) {
            if (($item->jumlah + 1) > ($produk->stok + $item->jumlah)) {
                return response()->json(['status' => 'error', 'message' => "Stok tidak cukup!"], 400);
            }
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
        return $this->getCartResponse($transaksi, "{$produk->nama_produk} ditambahkan!");
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
                return redirect()->route('transaksi.show', ['transaksi' => $transaksi->id, 'show_modal' => 'true'])
                                 ->with('toast_success', 'Transaksi Selesai!');
            }

            // --- PERBAIKAN: MENGGUNAKAN CONFIG() ---
            Config::$serverKey = config('midtrans.server_key'); 
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
            // FIX: Bypass SSL certificate verification for local development (XAMPP)
            // Menambahkan CURLOPT_HTTPHEADER kosong untuk mencegah error "Undefined array key 10023" di library Midtrans
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [] 
            ];

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
        // --- PERBAIKAN: MENGGUNAKAN CONFIG() ---
        $serverKey = config('midtrans.server_key'); 
        
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
        return redirect()->route('transaksi.show', ['transaksi' => $transaksi->id, 'show_modal' => 'true'])
                         ->with('toast_success', 'Pembayaran Berhasil!');
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

    public function checkStatus(Transaksi $transaksi)
    {
        // 1. Jika di DB sudah selesai/batal, langsung return
        if ($transaksi->status == 'selesai' || $transaksi->status == 'batal') {
            return response()->json(['status' => $transaksi->status]);
        }

        // 2. Jika masih pending, Cek Langsung ke Midtrans (Server-to-Server)
        // Ini mengatasi masalah Webhook tidak masuk di Localhost
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            
            // Order ID tersimpan di kolom 'catatan' saat checkout
            $orderId = $transaksi->catatan; 
            
            if($orderId) {
                $status = \Midtrans\Transaction::status($orderId);
                $transactionStatus = $status->transaction_status;
                $fraudStatus = $status->fraud_status ?? null;

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'challenge') {
                        // Challenge
                    } else {
                        $this->markAsSuccess($transaksi, 'Midtrans-AutoCheck');
                    }
                } else if ($transactionStatus == 'settlement') {
                    $this->markAsSuccess($transaksi, 'Midtrans-AutoCheck');
                } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                    // Opsional: Bisa otomatis batalkan jika expire
                }
            }
        } catch (\Exception $e) {
            // Abaikan error koneksi ke Midtrans, gunakan status DB saja
        }

        return response()->json(['status' => $transaksi->status->fresh()->status ?? $transaksi->status]);
    }

    private function markAsSuccess($transaksi, $ref) {
        if ($transaksi->status != 'selesai') {
            $transaksi->update([
                'status' => 'selesai',
                'metode_bayar' => 'Midtrans'
            ]);
            
            Pembayaran::create([
                'transaksi_id' => $transaksi->id,
                'metode' => 'Midtrans',
                'jumlah' => $transaksi->total,
                'referensi' => $ref
            ]);
        }
    }

    private function getCartResponse($transaksi, $message = '')
    {
        $transaksi->load('details.produk.kategori');
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => [
                'total_format' => number_format($transaksi->total, 0, ',', '.'),
                'cart_count' => $transaksi->details->sum('jumlah'),
                'details' => $transaksi->details->map(function($item) {
                    return [
                        'id' => $item->id,
                        'produk_id' => $item->produk_id,
                        'nama_produk' => $item->produk->nama_produk,
                        'kategori' => $item->produk->kategori->nama ?? '-',
                        'stok_asli' => $item->produk->stok + $item->jumlah,
                        'harga_satuan' => number_format($item->harga_satuan, 0, ',', '.'),
                        'jumlah' => $item->jumlah,
                        'subtotal' => number_format($item->subtotal, 0, ',', '.')
                    ];
                })
            ]
        ]);
    }
}