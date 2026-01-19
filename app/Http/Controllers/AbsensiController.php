<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = null;

            // 1. Cek Login via PIN
            if ($request->has('pin')) {
                $pin = $request->pin;
                if (!$pin) throw new \Exception("PIN wajib diisi");
                
                $user = User::where('pin', $pin)->first();
                if (!$user) {
                    return response()->json(['status' => 'error', 'message' => 'PIN Salah atau Tidak Dikenal!']);
                }
            } 
            // 2. Cek Login via QR Code (Fallback)
            elseif ($request->has('qr_code')) {
                $qrCode = $request->qr_code;
                if (!$qrCode) throw new \Exception("QR Code tidak terbaca");

                $userId = str_replace('ID-', '', $qrCode);
                $user = User::find($userId);

                if (!$user) {
                    return response()->json(['status' => 'error', 'message' => 'Kartu tidak valid!']);
                }
            } else {
                throw new \Exception("Metode absensi tidak dikenali");
            }

            // Validasi Role Kasir wajib pakai PIN (Opsional, sesuai permintaan user "diganti jadi pin")
            // if ($user->role === 'kasir' && !$request->has('pin')) {
            //      return response()->json(['status' => 'error', 'message' => 'Kasir wajib absen menggunakan PIN!']);
            // }

            $now = Carbon::now('Asia/Jakarta');
            $todayDate = $now->format('Y-m-d');

            // Ambil Pengaturan
            $jamMasukSetting = Setting::where('key', 'jam_masuk_kantor')->value('value') ?? '08:00';
            $jamPulangSetting = Setting::where('key', 'jam_pulang_kantor')->value('value') ?? '17:00'; // Default jam 5 sore
            $toleransiMenit = (int) (Setting::where('key', 'toleransi_telat')->value('value') ?? 0);

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
                    $keterlambatan = $now->diffInMinutes($jadwalMasuk); // Hitung selisih dari jadwal masuk asli
                }

                Absensi::create([
                    'user_id' => $user->id,
                    'tanggal' => $todayDate,
                    'waktu_masuk' => $now->format('H:i:s'),
                    'status' => $status,
                    'keterlambatan' => $keterlambatan 
                ]);

                $pesan = "Halo {$user->name}, Selamat Bekerja!";
                if ($status == 'Telat') {
                    // Convert minutes to readable format if needed, but minutes is fine
                    $pesan = "Halo {$user->name}, Anda terlambat {$keterlambatan} menit dari jadwal masuk ({$jamMasukSetting}) + toleransi ({$toleransiMenit} menit).";
                }

                return response()->json([
                    'status' => 'success',
                    'tipe' => 'masuk',
                    'message' => $pesan
                ]);

            } else {
                // === ABSEN PULANG ===

                if ($absen->waktu_keluar) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Anda SUDAH absen pulang hari ini.'
                    ]);
                }

                // Cek Jadwal Pulang
                $jadwalPulang = Carbon::createFromFormat('Y-m-d H:i', $todayDate . ' ' . substr($jamPulangSetting, 0, 5), 'Asia/Jakarta');
                
                if ($now->lessThan($jadwalPulang)) {
                    $sisaWaktu = $now->diffInMinutes($jadwalPulang);
                    $jam = floor($sisaWaktu / 60);
                    $menit = $sisaWaktu % 60;
                    $keteranganWaktu = $jam > 0 ? "{$jam} jam {$menit} menit" : "{$menit} menit";

                    return response()->json([
                        'status' => 'error', 
                        'message' => "Belum waktunya pulang! Jadwal pulang pukul {$jamPulangSetting}. Kurang {$keteranganWaktu} lagi."
                    ]);
                }

                $absen->update([
                    'waktu_keluar' => $now->format('H:i:s')
                ]);

                return response()->json([
                    'status' => 'success',
                    'tipe' => 'pulang',
                    'message' => "Hati-hati di jalan, {$user->name}! Terima kasih atas kerja kerasmu hari ini."
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