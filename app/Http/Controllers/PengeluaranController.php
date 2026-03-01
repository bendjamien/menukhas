<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = Pengeluaran::with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $pengeluarans = $query->latest('tanggal')->paginate(10);
        $totalPengeluaran = $query->sum('nominal');

        return view('pengeluaran.index', compact('pengeluarans', 'startDate', 'endDate', 'totalPengeluaran'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Pengeluaran::with('user');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $pengeluarans = $query->orderBy('tanggal', 'asc')->get();
        $total = $pengeluarans->sum('nominal');
        
        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        $pdf = Pdf::loadView('pengeluaran.pdf', compact('pengeluarans', 'startDate', 'endDate', 'total', 'settings'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Pengeluaran-'.($startDate ?? 'All').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Pengeluaran::with('user');
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        $pengeluarans = $query->orderBy('tanggal', 'asc')->get();
        
        // Export via HTML Table (Excel-friendly)
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Laporan-Pengeluaran.xls");
        
        return view('pengeluaran.excel', compact('pengeluarans', 'startDate', 'endDate'));
    }

    public function create()
    {
        $kategoris = ['Bahan Baku', 'Operasional', 'Gaji Karyawan', 'Sewa Tempat', 'Lainnya'];
        return view('pengeluaran.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string',
            'keterangan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        Pengeluaran::create([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'nominal' => $request->nominal,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('pengeluaran.index')->with('toast_success', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        return back()->with('toast_success', 'Data pengeluaran dihapus.');
    }
}
