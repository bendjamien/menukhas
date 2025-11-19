<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{

    public function index()
    {
        $pembayarans = Pembayaran::with(['transaksi.pelanggan'])
                                ->orderBy('id', 'asc')
                                ->paginate(20);
                                
        return view('pembayaran.index', compact('pembayarans'));
    }
}