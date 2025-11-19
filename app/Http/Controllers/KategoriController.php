<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{

    public function index()
    {
        $kategoris = Kategori::orderBy('id', 'asc')->paginate(10);
        
        return view('kategori.index', compact('kategoris'));
    }


    public function create()
    {
        return view('kategori.create');
    }


    public function store(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all());

        return redirect()->route('kategori.index')
                         ->with('toast_success', 'Kategori berhasil ditambahkan!');
    }

    
    public function show(Kategori $kategori)
    {
        return view('kategori.show', compact('kategori'));
    }

    
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    
    public function update(Request $request, Kategori $kategori)
    {
        
        $request->validate([
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return redirect()->route('kategori.index')
                         ->with('toast_success', 'Kategori berhasil diperbarui!');
    }

   
    public function destroy(Kategori $kategori)
    {
        $kategori->delete();

        return redirect()->route('kategori.index')
                         ->with('toast_danger', 'Kategori berhasil dihapus!');
    }
}