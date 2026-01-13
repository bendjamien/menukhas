<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $qrCode = $request->qr_code;
            if (!$qrCode) throw new \Exception("QR Code tidak terbaca");

            $userId = str_replace('ID-', '', $qrCode);
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Kartu tidak valid!']);
            }

            $now = Carbon::now('Asia/Jakarta');
            $todayDate = $now->format('Y-m-d');

            // Ambil Pengaturan
            $jamMasukSetting = DB::table('settings')->where('key', 'jam_masuk_kantor')->value('value') ?? '08:00';
            $toleransiMenit = (int) (DB::table('settings')->where('key', 'toleransi_telat')->value('value') ?? 0);

            $absen = Absensi::where('user_id', $user->id)
                            ->where('tanggal', $todayDate)
                            ->first();

            if (!$absen) {
                // === ABSEN MASUK ===
                $jadwalMasuk = Carbon::createFromFormat('Y-m-d H:i', $todayDate . ' ' . substr($jamMasukSetting, 0, 5), 'Asia/Jakarta');
                $batasTelat = $jadwalMasuk->copy()->addMinutes($toleransiMenit);

                $status = 'Hadir';
                $keterlambatan = 0;

                if ($now->greaterThan($batasTelat)) {
                    $status = 'Telat';
                    $keterlambatan = $now->diffInMinutes($jadwalMasuk);
                }

                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $todayDate,
                    'jam_masuk' => $now->format('H:i:s'),
                    'status' => $status,
                    'keterlambatan' => $keterlambatan
                ]);

                $pesan = "Halo {$user->name}, Selamat Bekerja!";
                if ($status == 'Telat') {
                    $pesan = "Halo {$user->name}, Kamu Telat {$keterlambatan} Menit!";
                }

                return response()->json([
                    'status' => 'success',
                    'tipe' => 'masuk',
                    'message' => $pesan
                ]);

            } else {
                // === ABSEN PULANG ===

                if ($absen->jam_pulang) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Anda SUDAH absen pulang hari ini.'
                    ]);
                }

                // --- BAGIAN INI SAYA MATIKAN DULU BIAR BISA TEST BEBAS ---
                // $waktuMasuk = Carbon::parse($absen->tanggal . ' ' . $absen->jam_masuk, 'Asia/Jakarta');
                // if ($now->diffInMinutes($waktuMasuk) < 1) {
                //     return response()->json([
                //         'status' => 'error', 
                //         'message' => 'Baru saja absen masuk! Tunggu 1 menit.'
                //     ]);
                // }
                // ---------------------------------------------------------

                $absen->update([
                    'jam_pulang' => $now->format('H:i:s')
                ]);

                return response()->json([
                    'status' => 'success',
                    'tipe' => 'pulang',
                    'message' => "Hati-hati di jalan, {$user->name}!"
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}