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

class PosController extends Controller
{
    public function index(Request $request, Transaksi $transaksi = null)
    {
        $user = Auth::user();
        $kasirId = $user->id;

        $activeDraft = null;

        if ($transaksi && $transaksi->exists) { 
            
            if ($user->role == 'admin') {
                if ($transaksi->status == 'draft') {
                    $activeDraft = $transaksi;
                }
            } else {
                if ($transaksi->status == 'draft' && $transaksi->kasir_id == $kasirId) {
                    $activeDraft = $transaksi;
                }
            }
            
            if (!$activeDraft) {
                 return redirect()->route('pos.index')->with('toast_danger', 'Draft tidak valid atau bukan milik Anda.');
            }

        } else {

            $activeDraft = Transaksi::where('kasir_id', $kasirId)
                                    ->where('status', 'draft')
                                    ->latest('tanggal')
                                    ->first();
        }

        if (!$activeDraft) {
            $activeDraft = $this->createEmptyDraft($kasirId);
        }

        $activeDraft->load(['details.produk', 'pelanggan', 'kasir']);

        $pendingDraftsQuery = Transaksi::with(['details', 'pelanggan', 'kasir'])
                                      ->where('status', 'draft')
                                      ->where('id', '!=', $activeDraft->id);
        
        if ($user->role == 'kasir') {
            $pendingDraftsQuery->where('kasir_id', $kasirId);
        }
        
        $pendingDrafts = $pendingDraftsQuery->latest('tanggal')->get();

        $search = $request->query('search');
        $produksQuery = Produk::with('kategori');
        if ($search) {
            $produksQuery->where('nama_produk', 'like', "%{$search}%")
                         ->orWhere('kode_barcode', 'like', "%{$search}%");
        }
        $produks = $produksQuery->orderBy('nama_produk', 'asc')->get();
        
        $pelanggans = Pelanggan::orderBy('nama', 'asc')->get();

        return view('pos.index', compact(
            'produks', 
            'pelanggans', 
            'activeDraft',
            'pendingDrafts', 
            'search'
        ));
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
                         ->with('toast_success', 'Transaksi baru dibuat. (Draft sebelumnya ditahan)');
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|integer|exists:produk,id',
            'transaksi_id' => 'required|integer|exists:transaksi,id',
        ]);

        $produk = Produk::find($validated['produk_id']);
        $transaksi = Transaksi::find($validated['transaksi_id']);

        if ($produk->stok <= 0) {
            return redirect()->back()->with('toast_danger', 'Stok barang kosong, tidak bisa dipilih.');
        }

        $item = $transaksi->details()->where('produk_id', $produk->id)->first();

        $jumlahDiKeranjang = $item ? $item->jumlah : 0;
        
        if (($jumlahDiKeranjang + 1) > $produk->stok) {
            return redirect()->back()->with('toast_danger', 'Stok tidak mencukupi! Sisa stok hanya: ' . $produk->stok);
        }

        if ($item) {
            $item->jumlah++;
            $item->subtotal = $item->jumlah * $item->harga_satuan;
            $item->save();
        } else {
            $transaksi->details()->create([
                'produk_id' => $produk->id,
                'jumlah' => 1,
                'harga_satuan' => $produk->harga_jual,
                'diskon_item' => 0,
                'subtotal' => $produk->harga_jual * 1,
            ]);
        }

        $this->recalculateTransactionTotal($transaksi);
        return redirect()->route('pos.index', ['transaksi' => $transaksi->id]);
    }

    public function updateItem(Request $request)
    {
        $validated = $request->validate([
            'transaksi_detail_id' => 'required|integer|exists:transaksi_detail,id',
            'qty' => 'required|integer|min:1',
        ]);

        $item = TransaksiDetail::find($validated['transaksi_detail_id']);
        
        if ($validated['qty'] > $item->produk->stok) {
            return redirect()->back()->with('toast_danger', 'Jumlah melebihi stok tersedia! Sisa: ' . $item->produk->stok);
        }

        $item->jumlah = $validated['qty'];
        $item->subtotal = $item->jumlah * $item->harga_satuan;
        $item->save();

        $this->recalculateTransactionTotal($item->transaksi);
        return redirect()->back();
    }

    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'transaksi_detail_id' => 'required|integer|exists:transaksi_detail,id',
        ]);

        $item = TransaksiDetail::find($validated['transaksi_detail_id']);
        $transaksi = $item->transaksi;
        $item->delete();
        $this->recalculateTransactionTotal($transaksi);

        return redirect()->back();
    }

    public function saveCustomerToDraft(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|integer|exists:transaksi,id',
            'pelanggan_id' => 'nullable|integer|exists:pelanggan,id',
        ]);

        $transaksi = Transaksi::find($validated['transaksi_id']);
        $transaksi->pelanggan_id = $validated['pelanggan_id'];
        $transaksi->save();

        return redirect()->back();
    }

    public function cancelDraft(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => 'required|integer|exists:transaksi,id',
        ]);

        $transaksi = Transaksi::find($validated['transaksi_id']);
        if ($transaksi->status == 'draft') {
            $transaksi->delete();
        }

        return redirect()->route('pos.index')
                         ->with('toast_danger', 'Transaksi draft telah dibatalkan.');
    }

    public function showCheckoutForm(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->status != 'draft') {
            return redirect()->route('pos.index')->with('toast_danger', 'Transaksi ini sudah selesai.');
        }
        $transaksi->load(['details.produk', 'pelanggan']);
        if ($transaksi->details->isEmpty()) {
            return redirect()->route('pos.index', ['transaksi' => $transaksi->id])
                             ->with('toast_danger', 'Keranjang kosong, tidak bisa checkout.');
        }
        $subtotal = $transaksi->details->sum('subtotal');
        $taxRate = Cache::rememberForever('ppn_tax_rate', function () {
            $setting = Setting::firstOrCreate(['key' => 'ppn_tax_rate'], ['value' => '0.11']);
            return (float) $setting->value;
        });
        return view('pos.checkout', [
            'transaksi' => $transaksi,
            'cart' => $transaksi->details,
            'pelanggan' => $transaksi->pelanggan,
            'subtotal' => $subtotal,
            'taxRate' => $taxRate
        ]);
    }

    public function storeCheckout(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'metode_bayar' => 'required|string|max:50',
            'nominal_bayar' => 'required|numeric|min:0',
            'diskon_amount' => 'required|numeric|min:0',
        ]);
        $transaksi->load('details');
        if ($transaksi->details->isEmpty()) {
            return redirect()->route('pos.index')->with('toast_danger', 'Keranjang kosong!');
        }
        $subtotal = $transaksi->details->sum('subtotal');
        $diskon = $validated['diskon_amount'];
        if ($diskon > $subtotal) {
            return redirect()->back()->withErrors(['diskon_amount' => 'Diskon tidak boleh melebihi subtotal.']);
        }
        $taxRate = Cache::rememberForever('ppn_tax_rate', function () {
            $setting = Setting::firstOrCreate(['key' => 'ppn_tax_rate'], ['value' => '0.11']);
            return (float) $setting->value;
        });
        $totalSetelahDiskon = $subtotal - $diskon;
        $pajak = $totalSetelahDiskon * $taxRate;
        $grandTotal = $totalSetelahDiskon + $pajak;
        if ($validated['metode_bayar'] == 'Tunai' && $validated['nominal_bayar'] < $grandTotal) {
            return redirect()->back()->withErrors(['nominal_bayar' => 'Nominal bayar tunai kurang dari Grand Total.']);
        }
        try {
            $kasirId = Auth::id();
            $tanggalSekarang = Carbon::now('Asia/Jakarta');
            DB::transaction(function () use ($validated, $grandTotal, $diskon, $pajak, $transaksi, $kasirId, $tanggalSekarang) {
                $transaksi->update([
                    'tanggal' => $tanggalSekarang,
                    'kasir_id' => $kasirId,
                    'total' => $grandTotal,
                    'diskon' => $diskon,
                    'pajak' => $pajak,
                    'metode_bayar' => $validated['metode_bayar'],
                    'nominal_bayar' => $validated['nominal_bayar'],
                    'kembalian' => $validated['nominal_bayar'] - $grandTotal,
                    'status' => 'selesai',
                ]);
                foreach ($transaksi->details as $item) {
                    $produk = $item->produk;
                    
                    if($produk->stok < $item->jumlah) {
                         throw new \Exception("Stok produk {$produk->nama_produk} tidak mencukupi untuk checkout.");
                    }

                    $produk->decrement('stok', $item->jumlah);
                    StokLog::create([
                        'produk_id' => $produk->id,
                        'tanggal' => $tanggalSekarang,
                        'tipe' => 'keluar',
                        'jumlah' => $item->jumlah,
                        'sumber' => 'Penjualan',
                        'keterangan' => 'Penjualan via POS, Transaksi #' . $transaksi->id,
                        'user_id' => $kasirId,
                    ]);
                }
                Pembayaran::create([
                    'transaksi_id' => $transaksi->id,
                    'metode' => $validated['metode_bayar'],
                    'jumlah' => $grandTotal,
                    'referensi' => 'Selesai via POS',
                ]);
            });
            return redirect()->route('transaksi.show', $transaksi)
                             ->with('toast_success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->route('pos.index')
                             ->with('toast_danger', 'Terjadi kesalahan! Transaksi gagal disimpan. ' . $e->getMessage());
        }
    }

    public function checkVoucher(Request $request)
    {
        $code = $request->input('voucher_code');
        $subtotal = $request->input('subtotal');

        $voucher = Voucher::where('kode', $code)->where('is_active', true)->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak ditemukan atau tidak aktif.']);
        }

        $discountAmount = 0;
        if ($voucher->tipe == 'nominal') {
            $discountAmount = $voucher->nilai;
        } else {
            $discountAmount = $subtotal * ($voucher->nilai / 100);
        }

        if ($discountAmount > $subtotal) {
            $discountAmount = $subtotal;
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $discountAmount,
            'message' => 'Voucher berhasil digunakan!'
        ]);
    }

}