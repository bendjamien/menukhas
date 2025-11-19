<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request; // 1. TAMBAHKAN IMPORT

class PelangganController extends Controller
{
    public function index(Request $request) 
    {
        $search = $request->query('search');

        $query = Pelanggan::query();

        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $pelanggans = $query->orderBy('id', 'asc') 
                            ->paginate(5)
                            ->withQueryString(); 
        
        return view('pelanggan.index', compact('pelanggans', 'search'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'member_level' => 'nullable|string|max:50',
            'poin' => 'nullable|integer',
        ]);
        Pelanggan::create($request->all());
        return redirect()->route('pelanggan.index')
                         ->with('toast_success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function show(Pelanggan $pelanggan)
    {
        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|max:100',
            'member_level' => 'nullable|string|max:50',
            'poin' => 'nullable|integer',
        ]);
        $pelanggan->update($request->all());
        return redirect()->route('pelanggan.index')
                         ->with('toast_success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return redirect()->route('pelanggan.index')
                         ->with('toast_danger', 'Data pelanggan berhasil dihapus!');
    }
}