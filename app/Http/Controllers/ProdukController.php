<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori; 
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D; 

class ProdukController extends Controller
{
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

        $produks = $query->orderBy('id', 'asc') 
                         ->paginate(10)
                         ->withQueryString(); 
        
        return view('produk.index', compact('produks', 'search'));
    }

 
    public function create()
    {
        $kategoris = Kategori::all(); 
        
        return view('produk.create', compact('kategoris'));
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