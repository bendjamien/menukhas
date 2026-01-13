<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanPendapatanController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');
        $startOfMonth = Carbon::now('Asia/Jakarta')->startOfMonth();

        // Statistik Ringkas (Tetap menggunakan logika tanggal hari ini/bulan ini real-time)
        $totalSemua = Transaksi::where('status', 'selesai')->sum('total');
        $totalBulanIni = Transaksi::where('status', 'selesai')
                                  ->where('tanggal', '>=', $startOfMonth)
                                  ->sum('total');
        $totalHariIni = Transaksi::where('status', 'selesai')
                                 ->whereDate('tanggal', $today)
                                 ->sum('total');

        // Logic Filter Laporan (Bulan & Tahun)
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', Carbon::now()->year); // Default tahun ini

        $query = Transaksi::with(['kasir', 'pelanggan'])
                          ->where('status', 'selesai');

        if ($bulan && $bulan != 'all') {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $totalFiltered = (clone $query)->sum('total');
        $jumlahFiltered = (clone $query)->count();

        $transaksis = $query->orderBy('tanggal', 'desc') // Urutkan terbaru dulu
                            ->paginate(15)
                            ->withQueryString(); 
        
        return view('laporan.pendapatan', compact(
            'totalSemua',
            'totalBulanIni',
            'totalHariIni',
            'transaksis',
            'totalFiltered',  
            'jumlahFiltered',
            'bulan', 
            'tahun'
        ));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil data sesuai filter (sama seperti index, tapi get() bukan paginate())
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', Carbon::now()->year);

        $query = Transaksi::with(['kasir', 'pelanggan'])->where('status', 'selesai');

        if ($bulan && $bulan != 'all') {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $transaksis = $query->orderBy('tanggal', 'asc')->get(); // Urutkan terlama ke terbaru untuk laporan
        $totalPendapatan = $transaksis->sum('total');

        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        return view('laporan.cetak-pendapatan', compact('transaksis', 'bulan', 'tahun', 'totalPendapatan', 'settings'));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', Carbon::now()->year);

        $query = Transaksi::with(['kasir', 'pelanggan'])->where('status', 'selesai');

        if ($bulan && $bulan != 'all') {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $transaksis = $query->orderBy('tanggal', 'asc')->get();
        $totalPendapatan = $transaksis->sum('total');
        
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        
        $periode = ($bulan && $bulan != 'all' ? Carbon::createFromDate(null, $bulan, 1)->isoFormat('MMMM') : 'Semua Bulan') . ' ' . $tahun;
        $filename = "Laporan_Pendapatan_" . str_replace(' ', '_', $periode) . ".xls";

        return response()->streamDownload(function() use ($transaksis, $totalPendapatan, $settings, $periode) {
            echo view('laporan.excel-pendapatan', compact('transaksis', 'totalPendapatan', 'settings', 'periode'));
        }, $filename);
    }
}