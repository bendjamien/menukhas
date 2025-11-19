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

        $totalSemua = Transaksi::where('status', 'selesai')->sum('total');
        
        $totalBulanIni = Transaksi::where('status', 'selesai')
                                  ->where('tanggal', '>=', $startOfMonth)
                                  ->sum('total');
        
        $totalHariIni = Transaksi::where('status', 'selesai')
                                 ->whereDate('tanggal', $today)
                                 ->sum('total');


        
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Transaksi::with(['kasir', 'pelanggan'])
                          ->where('status', 'selesai');

        if ($startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('tanggal', [$start, $end]);
            } catch (\Exception $e) {
            }
        }
        $totalFiltered = (clone $query)->sum('total');
        $jumlahFiltered = (clone $query)->count();

        $transaksis = $query->orderBy('id', 'asc')
                            ->paginate(15)
                            ->withQueryString(); 
        
        return view('laporan.pendapatan', compact(
            'totalSemua',
            'totalBulanIni',
            'totalHariIni',
            'transaksis',
            'totalFiltered',  
            'jumlahFiltered',
            'startDate', 
            'endDate'
        ));
    }
}