<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class OrderDisplayController extends Controller
{
    public function index()
    {
        // Tampilkan halaman utama display antrian pelanggan
        return view('display.index');
    }

    public function getQueueData()
    {
        // Ambil data transaksi hari ini yang masih aktif diproses
        $orders = Transaksi::with('pelanggan')
            ->where('status', 'selesai') // Sudah dibayar
            ->whereIn('status_produksi', ['pending', 'processing', 'ready'])
            ->orderBy('id', 'asc')
            ->get();

        $preparing = $orders->whereIn('status_produksi', ['pending', 'processing'])->map(function($o) {
            return [
                'id' => $o->id,
                'nama' => $o->pelanggan->nama ?? 'Guest'
            ];
        })->values();

        $ready = $orders->where('status_produksi', 'ready')->map(function($o) {
            return [
                'id' => $o->id,
                'nama' => $o->pelanggan->nama ?? 'Guest'
            ];
        })->values();

        return response()->json([
            'preparing' => $preparing,
            'ready' => $ready
        ]);
    }
}
