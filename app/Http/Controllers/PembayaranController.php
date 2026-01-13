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
                // 1. Bersihkan input dari '#'
                $cleanSearch = str_replace('#', '', $search);

                // 2. Pencarian LIKE normal (agar input "1" bisa ketemu "1", "10", "100")
                $q->where('transaksi_id', 'like', "%{$cleanSearch}%");

                // 3. Pencarian Exact Match untuk menangani '0001' -> '1'
                // Cek apakah input bersih adalah angka valid
                if (is_numeric($cleanSearch)) {
                    $intValue = (int) $cleanSearch;
                    $q->orWhere('transaksi_id', $intValue);
                }
            });
        }

        $pembayarans = $query->orderBy('id', 'desc')
                             ->paginate(20)
                             ->withQueryString();
                                
        return view('pembayaran.index', compact('pembayarans'));
    }
}