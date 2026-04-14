<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KitchenController extends Controller
{
    public function index()
    {
        // Ambil transaksi yang statusnya selesai dibayar tapi belum selesai di dapur
        $orders = Transaksi::with(['details.produk.kategori', 'pelanggan'])
            ->where('status', 'selesai')
            ->whereIn('status_produksi', ['pending', 'processing', 'ready'])
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('kitchen.index', compact('orders'));
    }

    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        $newStatus = $request->status;
        $data = ['status_produksi' => $newStatus];

        if ($newStatus === 'processing' && !$transaksi->waktu_mulai_produksi) {
            $data['waktu_mulai_produksi'] = now();
        }

        if ($newStatus === 'completed') {
            $data['waktu_selesai_produksi'] = now();
        }

        $transaksi->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan #' . $transaksi->id . ' diperbarui ke ' . $newStatus
        ]);
    }

    public function getActiveOrders()
    {
        // API untuk auto-refresh layar dapur tanpa reload
        $orders = Transaksi::with(['details.produk.kategori', 'pelanggan'])
            ->where('status', 'selesai')
            ->whereIn('status_produksi', ['pending', 'processing', 'ready'])
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'pelanggan' => $order->pelanggan->nama ?? 'Umum',
                    'waktu' => $order->tanggal->format('H:i:s'),
                    'tanggal_raw' => $order->tanggal->toIso8601String(),
                    'status_produksi' => $order->status_produksi,
                    'items' => $order->details->map(function($detail) {
                        return [
                            'nama' => $detail->produk->nama_produk,
                            'jumlah' => $detail->jumlah,
                            'kategori' => $detail->produk->kategori->nama ?? 'Lainnya'
                        ];
                    })
                ];
            });

        return response()->json($orders);
    }
}
