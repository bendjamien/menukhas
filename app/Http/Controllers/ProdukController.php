<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori; 
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D; 

class ProdukController extends Controller
{
    public function checkBarcode($barcode)
    {
        // 1. Cek di database lokal dulu
        $produk = Produk::where('kode_barcode', $barcode)->first();
        if ($produk) {
            return response()->json([
                'status' => 'success',
                'source' => 'local',
                'data' => [
                    'nama_produk' => $produk->nama_produk,
                    'kategori_id' => $produk->kategori_id,
                    'harga_beli' => $produk->harga_beli,
                    'harga_jual' => $produk->harga_jual,
                    'satuan' => $produk->satuan,
                ]
            ]);
        }

        // 2. Cek di API Open Food Facts (Public database - Mostly Food)
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json", [
                'timeout' => 5,
                'http_errors' => false
            ]);
            
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                if (isset($body['status']) && $body['status'] == 1 && isset($body['product']['product_name'])) {
                    return response()->json([
                        'status' => 'success',
                        'source' => 'external',
                        'data' => [
                            'nama_produk' => $body['product']['product_name'],
                            'satuan' => 'PCS'
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Barcode API Error: " . $e->getMessage());
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Produk tidak ditemukan di database makanan (OpenFoodFacts).'
        ]);
    }

    public function index(Request $request) 
    {
        $search = $request->query('search');

        $query = Produk::with('kategori');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_barcode', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $produks = $query->orderBy('stok', 'asc') // Urutkan stok terkecil dulu agar admin aware
                         ->paginate(10)
                         ->withQueryString(); 

        // Statistik untuk Header Dashboard Stok
        $totalProduk = Produk::count();
        $totalAset = Produk::sum(\Illuminate\Support\Facades\DB::raw('harga_beli * stok'));
        
        $batasStokMenipis = \App\Models\Setting::where('key', 'stok_minimum')->value('value') ?? 5;
        $stokMenipis = Produk::where('stok', '<=', $batasStokMenipis)->where('stok', '>', 0)->count();
        
        $stokHabis = Produk::where('stok', 0)->count();
        
        return view('produk.index', compact('produks', 'search', 'totalProduk', 'totalAset', 'stokMenipis', 'stokHabis', 'batasStokMenipis'));
    }

 
    public function create()
    {
        $kategoris = Kategori::all(); 
        
        // Manual Barcode (Kosongkan default value)
        $autoBarcode = ''; 

        return view('produk.create', compact('kategoris', 'autoBarcode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'required|integer|exists:kategori,id', 
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:30',
            'kode_barcode' => 'nullable|string|max:50|unique:produk,kode_barcode', 
            'deskripsi' => 'nullable|string',
        ]);

        Produk::create($request->all());

        return redirect()->route('produk.index')
                         ->with('toast_success', 'Produk berhasil ditambahkan!');
    }


    public function show(Produk $produk)
    {
        return view('produk.show', compact('produk'));
    }

 
    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all(); 
        
        return view('produk.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'required|integer|exists:kategori,id', 
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'satuan' => 'nullable|string|max:30',
            'kode_barcode' => 'nullable|string|max:50|unique:produk,kode_barcode,' . $produk->id,
            'deskripsi' => 'nullable|string',
        ]);

        $produk->update($request->all());

        return redirect()->route('produk.index')
                         ->with('toast_success', 'Produk berhasil diperbarui!');
    }

    
    public function destroy(Produk $produk)
    {
        $produk->delete();

        return redirect()->route('produk.index')
                         ->with('toast_danger', 'Produk berhasil dihapus!');
    }
}