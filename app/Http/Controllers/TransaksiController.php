<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request; 
use Carbon\Carbon; 

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaksi::with(['kasir', 'pelanggan'])
                          ->where('status', 'selesai');

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
        $transaksis = $query->orderBy('id', 'asc')
                            ->paginate(5)
                            ->withQueryString(); 
        
        // 6. Kirim data ke view
        return view('transaksi.index', compact(
            'transaksis', 
            'search',     
            'startDate', 
            'endDate'
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