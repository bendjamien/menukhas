<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengaturanGaji;
use App\Models\Kasbon;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->query('bulan', date('m'));
        $tahun = $request->query('tahun', date('Y'));

        $penggajians = Penggajian::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('role', ['kasir', 'karyawan']);
            })
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('gaji.index', compact('penggajians', 'bulan', 'tahun'));
    }

    public function history(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', date('Y'));

        $query = Penggajian::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('role', ['kasir', 'karyawan']);
            })
            ->where('status_bayar', 'dibayar');

        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        // Hitung Total Keseluruhan untuk filter yang aktif
        $totalKeseluruhan = $query->sum('total_diterima');

        $riwayats = $query->orderBy('tahun', 'desc')
                          ->orderBy('bulan', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        return view('gaji.history', compact('riwayats', 'bulan', 'tahun', 'totalKeseluruhan'));
    }

    public function exportHistoryPdf(Request $request)
    {
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun', date('Y'));

        $query = Penggajian::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('role', ['kasir', 'karyawan']);
            })
            ->where('status_bayar', 'dibayar');

        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $riwayats = $query->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        $total = $riwayats->sum('total_diterima');
        
        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        $pdf = Pdf::loadView('gaji.history-pdf', compact('riwayats', 'bulan', 'tahun', 'total', 'settings'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Laporan-Gaji-' . ($bulan ? date('F', mktime(0,0,0,$bulan,10)) : 'Semua') . '-' . $tahun . '.pdf';
        return $pdf->download($filename);
    }

    public function generate(Request $request)
    {
        $bulan = (int)$request->bulan;
        $tahun = (int)$request->tahun;

        // VALIDASI: Hanya boleh generate untuk bulan dan tahun berjalan
        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');

        if ($tahun < $currentYear || ($tahun == $currentYear && $bulan < $currentMonth)) {
            return back()->with('toast_danger', 'Tidak dapat generate ulang gaji untuk periode yang sudah lewat.');
        }

        if ($tahun > $currentYear || ($tahun == $currentYear && $bulan > $currentMonth)) {
            return back()->with('toast_danger', 'Tidak dapat generate gaji untuk periode masa depan.');
        }

        $users = User::whereIn('role', ['kasir', 'karyawan'])->get();

        try {
            DB::beginTransaction();
            foreach ($users as $user) {
                $config = PengaturanGaji::where('user_id', $user->id)->first();
                $gajiPokok = $config ? $config->gaji_pokok : 0;

                // Total Kasbon yang berstatus 'pending' pada bulan & tahun terpilih
                $totalKasbon = Kasbon::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('nominal');

                // Gunakan updateOrCreate agar data lama bisa diperbarui (misal ada tambahan kasbon baru)
                Penggajian::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'lembur' => 0,
                        'potongan_kasbon' => $totalKasbon,
                        'total_diterima' => $gajiPokok - $totalKasbon,
                        'status_bayar' => 'pending'
                    ]
                );
            }
            DB::commit();
            return back()->with('toast_success', 'Laporan gaji berhasil diperbarui/digenerate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('toast_danger', 'Gagal generate: ' . $e->getMessage());
        }
    }

    public function bayar(Request $request, Penggajian $penggajian)
    {
        try {
            DB::beginTransaction();
            
            $penggajian->update([
                'status_bayar' => 'dibayar',
                'metode_bayar' => $request->metode_bayar,
                'tanggal_bayar' => now()
            ]);

            // Tandai kasbon lunas
            Kasbon::where('user_id', $penggajian->user_id)
                ->where('status', 'pending')
                ->whereMonth('tanggal', $penggajian->bulan)
                ->whereYear('tanggal', $penggajian->tahun)
                ->update(['status' => 'lunas']);

            DB::commit();
            return back()->with('toast_success', 'Gaji telah dibayarkan via ' . $request->metode_bayar);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('toast_danger', 'Gagal bayar: ' . $e->getMessage());
        }
    }

    public function edit(Penggajian $penggajian)
    {
        return view('gaji.edit', compact('penggajian'));
    }

    public function update(Request $request, Penggajian $penggajian)
    {
        $request->validate(['lembur' => 'required|numeric|min:0']);
        
        $totalDiterima = ($penggajian->gaji_pokok + $request->lembur) - $penggajian->potongan_kasbon;

        $penggajian->update([
            'lembur' => $request->lembur,
            'total_diterima' => $totalDiterima
        ]);

        return redirect()->route('gaji.index')->with('toast_success', 'Data gaji diperbarui.');
    }

    public function cetakStruk(Penggajian $penggajian)
    {
        $penggajian->load('user');
        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        return view('gaji.cetak-struk', compact('penggajian', 'settings'));
    }

    // MANAJEMEN PENGATURAN GAJI
    public function settingIndex()
    {
        $users = User::whereIn('role', ['kasir', 'karyawan'])->get();
        $settings = PengaturanGaji::with('user')->get();
        return view('gaji.setting', compact('users', 'settings'));
    }

    public function settingStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        PengaturanGaji::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'gaji_pokok' => $request->gaji_pokok,
                'nomor_rekening' => $request->nomor_rekening,
                'bank' => $request->bank
            ]
        );

        return back()->with('toast_success', 'Pengaturan gaji disimpan.');
    }
}
