<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ShiftController extends Controller
{
    // Halaman Input Modal Awal
    public function openIndex()
    {
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($activeShift) {
            return redirect()->route('pos.index')->with('toast_success', 'Shift Anda masih aktif.');
        }
        return view('shift.open');
    }

    // Proses Simpan Buka Shift
    public function openStore(Request $request)
    {
        $request->validate([
            'saldo_awal' => 'required|numeric|min:0',
        ]);

        Shift::create([
            'user_id' => Auth::id(),
            'waktu_buka' => Carbon::now('Asia/Jakarta'),
            'saldo_awal' => $request->saldo_awal,
            'status' => 'open'
        ]);

        return redirect()->route('pos.index')->with('toast_success', 'Shift dibuka. Selamat bertugas!');
    }

    // Halaman Hitung Uang Laci (Tutup Shift)
    public function closeIndex(Request $request)
    {
        $user = Auth::user();
        $shift = Shift::where('user_id', $user->id)->where('status', 'open')->firstOrFail();

        // Cek apakah sudah waktunya pulang
        $jamPulangUser = Carbon::createFromFormat('H:i:s', $user->jam_pulang);
        $sekarang = Carbon::now('Asia/Jakarta');
        
        // Buat objek carbon jam pulang untuk hari ini
        $batasWaktuPulang = Carbon::today('Asia/Jakarta')->setTime($jamPulangUser->hour, $jamPulangUser->minute, $jamPulangUser->second);

        $isWaktunyaPulang = $sekarang->greaterThanOrEqualTo($batasWaktuPulang);
        $isEmergency = $request->has('emergency');

        if (!$isWaktunyaPulang && !$isEmergency) {
            return view('shift.early-warning', compact('user', 'batasWaktuPulang'));
        }

        // PERBAIKAN: Gunakan format yang eksplisit untuk datetime guna menghindari masalah perbandingan di SQL
        $totalTunaiPenjualan = Transaksi::where('kasir_id', $user->id)
            ->where('status', 'selesai')
            ->where('metode_bayar', 'Tunai')
            ->where('tanggal', '>=', $shift->waktu_buka->toDateTimeString())
            ->sum('total');

        $diharapkan = $shift->saldo_awal + $totalTunaiPenjualan;

        return view('shift.close', compact('shift', 'totalTunaiPenjualan', 'diharapkan', 'isEmergency'));
    }

    // Proses Tutup Shift & Hitung Selisih
    public function closeStore(Request $request)
    {
        $request->validate([
            'total_tunai_aktual' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        $user = Auth::user();
        $shift = Shift::where('user_id', $user->id)->where('status', 'open')->firstOrFail();
        
        // Konsistensi hitungan dengan tampilan index
        $totalTunaiPenjualan = Transaksi::where('kasir_id', $user->id)
            ->where('status', 'selesai')
            ->where('metode_bayar', 'Tunai')
            ->where('tanggal', '>=', $shift->waktu_buka->toDateTimeString())
            ->sum('total');

        $diharapkan = $shift->saldo_awal + $totalTunaiPenjualan;
        $aktual = $request->total_tunai_aktual;
        $selisih = $aktual - $diharapkan;

        $shift->update([
            'waktu_tutup' => Carbon::now('Asia/Jakarta'),
            'total_tunai_diharapkan' => $diharapkan,
            'total_tunai_aktual' => $aktual,
            'selisih' => $selisih,
            'catatan' => $request->catatan,
            'status' => 'closed'
        ]);

        return redirect()->route('dashboard')->with('toast_success', 'Shift berhasil ditutup. Laporan selisih telah disimpan.');
    }

    // Riwayat Shift untuk Admin/Owner
    public function history()
    {
        $shifts = Shift::with('user')->orderBy('id', 'desc')->paginate(15);
        return view('shift.history', compact('shifts'));
    }
}
