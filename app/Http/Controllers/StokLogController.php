<?php

namespace App\Http\Controllers;

use App\Models\StokLog;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokLogController extends Controller
{
    public function index(Request $request) 
    {
        $query = StokLog::with(['produk', 'user']);

        // Filter Produk
        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        // Filter Tipe (Masuk/Keluar)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $logs = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        // Statistik Ringkasan (Bulan Ini)
        $bulanIni = Carbon::now()->startOfMonth();
        $totalMasuk = StokLog::where('tipe', 'masuk')->where('tanggal', '>=', $bulanIni)->sum('jumlah');
        $totalKeluar = StokLog::where('tipe', 'keluar')->where('tanggal', '>=', $bulanIni)->sum('jumlah');
        $aktivitasBulanIni = StokLog::where('tanggal', '>=', $bulanIni)->count();

        // Ambil data produk terpilih untuk fitur "Cek Stok"
        $selectedProduk = null;
        if ($request->filled('produk_id')) {
            $selectedProduk = Produk::find($request->produk_id);
        }

        $produksForSearch = Produk::orderBy('nama_produk', 'asc')->get();

        return view('stok_log.index', compact(
            'logs', 
            'produksForSearch', 
            'totalMasuk',
            'totalKeluar',
            'aktivitasBulanIni',
            'selectedProduk'
        ));
    }

    public function create()
    {
        $produks = Produk::orderBy('nama_produk', 'asc')->get();
        return view('stok_log.create', compact('produks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_id' => 'required|integer|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
            'sumber' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                
                $produk = Produk::find($validated['produk_id']);

                StokLog::create([
                    'produk_id' => $produk->id,
                    'tanggal' => Carbon::now('Asia/Jakarta'),
                    'tipe' => 'masuk',
                    'jumlah' => $validated['jumlah'],
                    'sumber' => $validated['sumber'],
                    'keterangan' => $validated['keterangan'],
                    'user_id' => Auth::id(),
                ]);

                $produk->increment('stok', $validated['jumlah']);
            });

            return redirect()->route('stok_log.index')
                             ->with('toast_success', 'Stok masuk berhasil dicatat!');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('toast_danger', 'Gagal mencatat stok: ' . $e->getMessage())
                             ->withInput();
        }
    }
}