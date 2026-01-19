<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Default ke bulan & tahun sekarang jika tidak ada filter
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Ambil hanya user dengan role kasir
        $users = User::where('role', 'kasir')
                     ->with(['absensis' => function($query) use ($bulan, $tahun) {
                         $query->whereMonth('tanggal', $bulan)
                               ->whereYear('tanggal', $tahun);
                     }])
                     ->get();

        // Proses data untuk summary view
        $laporan = $users->map(function($user) {
            $totalHadir = $user->absensis->count();
            $totalTelat = $user->absensis->where('status', 'Telat')->count();
            $totalMenitTelat = $user->absensis->sum('keterlambatan');
            
            return [
                'user' => $user,
                'total_hadir' => $totalHadir,
                'total_telat' => $totalTelat,
                'total_menit_telat' => $totalMenitTelat,
            ];
        });

        return view('laporan.absensi', compact('laporan', 'bulan', 'tahun'));
    }

    public function show(Request $request, $userId)
    {
        // Digunakan untuk AJAX Detail View
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $user = User::findOrFail($userId);
        
        // Ambil absensi detail
        $absensis = Absensi::where('user_id', $userId)
                           ->whereMonth('tanggal', $bulan)
                           ->whereYear('tanggal', $tahun)
                           ->orderBy('tanggal', 'asc')
                           ->get();
        
        $jamMasukSetting = Setting::where('key', 'jam_masuk_kantor')->value('value') ?? '08:00';
        $jamPulangSetting = Setting::where('key', 'jam_pulang_kantor')->value('value') ?? '17:00';

        // Render partial view atau return JSON
        // Kita return JSON biar gampang di consume JS frontend
        return response()->json([
            'user' => $user->name,
            'role' => $user->role,
            'periode' => Carbon::create($tahun, $bulan)->translatedFormat('F Y'),
            'data' => $absensis,
            'settings' => [
                'jam_masuk' => $jamMasukSetting,
                'jam_pulang' => $jamPulangSetting
            ]
        ]);
    }

    public function print(Request $request, $userId)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $user = User::findOrFail($userId);
        $absensis = Absensi::where('user_id', $userId)
                           ->whereMonth('tanggal', $bulan)
                           ->whereYear('tanggal', $tahun)
                           ->orderBy('tanggal', 'asc')
                           ->get();
                           
        $summary = [
            'hadir' => $absensis->count(),
            'telat' => $absensis->where('status', 'Telat')->count(),
            'total_menit_telat' => $absensis->sum('keterlambatan'),
        ];

        $jamMasuk = Setting::where('key', 'jam_masuk_kantor')->value('value') ?? '08:00';
        $jamPulang = Setting::where('key', 'jam_pulang_kantor')->value('value') ?? '17:00';
        $logo = Setting::where('key', 'company_logo')->value('value');
        $companyName = Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';

        return view('laporan.cetak-absensi', compact('user', 'absensis', 'bulan', 'tahun', 'summary', 'jamMasuk', 'jamPulang', 'logo', 'companyName'));
    }
}