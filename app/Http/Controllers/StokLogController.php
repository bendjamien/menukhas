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

        $logs = StokLog::with(['produk', 'user'])
                        ->orderBy('tanggal', 'desc')
                        ->paginate(20);

        $produksForSearch = Produk::orderBy('nama_produk', 'asc')->get();

        $selectedProdukId = $request->query('produk_search_id'); 
        $selectedProduk = null;
        if ($selectedProdukId) {
            $selectedProduk = Produk::find($selectedProdukId);
        }
        
        return view('stok_log.index', compact(
            'logs', 
            'produksForSearch', 
            'selectedProdukId',
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