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
use Midtrans\Config;
use Midtrans\Snap;

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
        
        // CEK: Apakah semua karyawan sudah dibayar untuk periode ini?
        $sudahDibayarCount = Penggajian::whereIn('user_id', $users->pluck('id'))
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status_bayar', 'dibayar')
            ->count();

        if ($users->count() > 0 && $sudahDibayarCount === $users->count()) {
            return back()->with('toast_danger', 'Semua karyawan sudah digaji bulan ini, maka tidak bisa generate lagi. Tunggu bulan yang akan datang.');
        }

        try {
            DB::beginTransaction();
            foreach ($users as $user) {
                // Cari data yang sudah ada untuk periode ini
                $existing = Penggajian::where('user_id', $user->id)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
                
                // Jika sudah dibayar, jangan diapa-apakan
                if ($existing && $existing->status_bayar === 'dibayar') {
                    continue;
                }

                // Ambil nilai lembur yang sudah diinput sebelumnya (jika ada)
                $lemburSekarang = $existing ? (float)$existing->lembur : 0;

                $config = PengaturanGaji::where('user_id', $user->id)->first();
                $gajiPokok = $config ? (float)$config->gaji_pokok : 0;

                // Recalculate Kasbon yang berstatus 'pending'
                $totalKasbon = (float)Kasbon::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('nominal');

                // Hitung total diterima dengan menyertakan lembur yang sudah ada
                $totalDiterima = ($gajiPokok + $lemburSekarang) - $totalKasbon;

                Penggajian::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bulan' => $bulan,
                        'tahun' => $tahun,
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'lembur' => $lemburSekarang,
                        'potongan_kasbon' => $totalKasbon,
                        'total_diterima' => $totalDiterima,
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
            
            $metode = $request->metode_bayar ?? 'Tunai';

            $penggajian->update([
                'status_bayar' => 'dibayar',
                'metode_bayar' => $metode,
                'tanggal_bayar' => now()
            ]);

            // Tandai kasbon lunas
            Kasbon::where('user_id', $penggajian->user_id)
                ->where('status', 'pending')
                ->whereMonth('tanggal', $penggajian->bulan)
                ->whereYear('tanggal', $penggajian->tahun)
                ->update(['status' => 'lunas']);

            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Gaji telah dibayarkan via ' . $metode
                ]);
            }

            return back()->with('toast_success', 'Gaji telah dibayarkan via ' . $metode);

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return back()->with('toast_danger', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function handlePaymentSuccess(Penggajian $penggajian)
    {
        if ($penggajian->status_bayar == 'pending') {
            $penggajian->update([
                'status_bayar' => 'dibayar',
                'tanggal_bayar' => now()
            ]);

            // Tandai kasbon lunas
            Kasbon::where('user_id', $penggajian->user_id)
                ->where('status', 'pending')
                ->whereMonth('tanggal', $penggajian->bulan)
                ->whereYear('tanggal', $penggajian->tahun)
                ->update(['status' => 'lunas']);
        }
        return redirect()->route('gaji.index')->with('toast_success', 'Pembayaran Gaji Berhasil!');
    }

    public function checkStatus(Penggajian $penggajian)
    {
        if ($penggajian->status_bayar == 'dibayar') {
            return response()->json(['status' => 'dibayar']);
        }

        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            
            $orderId = $penggajian->order_id;
            
            if($orderId) {
                $status = \Midtrans\Transaction::status($orderId);
                $transactionStatus = $status->transaction_status;

                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $this->markAsSuccess($penggajian);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error koneksi
        }

        return response()->json(['status' => $penggajian->fresh()->status_bayar]);
    }

    private function markAsSuccess($penggajian) {
        if ($penggajian->status_bayar != 'dibayar') {
            $penggajian->update([
                'status_bayar' => 'dibayar',
                'tanggal_bayar' => now()
            ]);
            
            Kasbon::where('user_id', $penggajian->user_id)
                ->where('status', 'pending')
                ->whereMonth('tanggal', $penggajian->bulan)
                ->whereYear('tanggal', $penggajian->tahun)
                ->update(['status' => 'lunas']);
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
        $settings = PengaturanGaji::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('role', ['kasir', 'karyawan']);
            })
            ->get();
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
