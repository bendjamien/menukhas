<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use Carbon\Carbon; 

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kasirId = $request->query('kasir_id');

        $query = Transaksi::with(['kasir', 'pelanggan'])
                          ->where('status', 'selesai');

        // ROLE CHECK: Kasir hanya bisa lihat transaksi miliknya sendiri
        if ($user->role === 'kasir') {
            $query->where('kasir_id', $user->id);
        } else {
            // Admin/Owner bisa filter berdasarkan Kasir tertentu
            if ($kasirId) {
                $query->where('kasir_id', $kasirId);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('kasir', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pelanggan', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('tanggal', [$start, $end]);
            } catch (\Exception $e) {
            }
        }
        $transaksis = $query->orderBy('id', 'desc') // Diubah ke desc agar yang terbaru di atas
                            ->paginate(10)
                            ->withQueryString(); 
        
        // Ambil daftar kasir untuk filter Admin (Role 'kasir' atau semua user)
        $kasirs = ($user->role !== 'kasir') ? User::where('role', 'kasir')->get() : collect();

        return view('transaksi.index', compact(
            'transaksis', 
            'search',     
            'startDate', 
            'endDate',
            'kasirId',
            'kasirs'
        ));
    }
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['kasir', 'pelanggan', 'details.produk', 'pembayaran']);

        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        return view('transaksi.show', compact('transaksi', 'settings'));
    }

    public function cetakStruk(Transaksi $transaksi)
    {
        $transaksi->load(['kasir', 'pelanggan', 'details.produk', 'pembayaran']);

        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        return view('transaksi.cetak-struk', compact('transaksi', 'settings'));
    }
}