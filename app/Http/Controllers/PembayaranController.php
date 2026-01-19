<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Pembayaran::with(['transaksi.pelanggan']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $cleanSearch = str_replace('#', '', $search);
                $q->where('transaksi_id', 'like', "%{$cleanSearch}%");
                if (is_numeric($cleanSearch)) {
                    $intValue = (int) $cleanSearch;
                    $q->orWhere('transaksi_id', $intValue);
                }
            });
        }

        $pembayarans = $query->orderBy('id', 'desc')
                             ->paginate(20)
                             ->withQueryString();

        // Statistik Pembayaran (Bulan Ini)
        $bulanIni = \Carbon\Carbon::now()->startOfMonth();
        $totalPendapatan = Pembayaran::where('created_at', '>=', $bulanIni)->sum('jumlah');
        $transaksiTunai = Pembayaran::where('created_at', '>=', $bulanIni)->where('metode', 'Tunai')->count();
        $transaksiDigital = Pembayaran::where('created_at', '>=', $bulanIni)->where('metode', '!=', 'Tunai')->count();
                                
        return view('pembayaran.index', compact('pembayarans', 'totalPendapatan', 'transaksiTunai', 'transaksiDigital'));
    }
}