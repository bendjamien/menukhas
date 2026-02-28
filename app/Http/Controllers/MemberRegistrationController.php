<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\PendingMember;
use App\Mail\MemberVerificationMail;
use App\Mail\MemberCardMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Milon\Barcode\DNS1D;

class MemberRegistrationController extends Controller
{
    private $whatsapp_token;

    public function __construct() {
        $this->whatsapp_token = env('WHATSAPP_TOKEN', '');
    }

    public function index()
    {
        return view('member.registration');
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'metode' => 'required|in:email,whatsapp',
            'target' => 'required|string',
        ]);

        $exists = Pelanggan::where('email', $request->target)
                          ->orWhere('no_hp', $request->target)
                          ->exists();
        
        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Email atau Nomor HP sudah terdaftar sebagai member.'], 422);
        }

        $otp = rand(100000, 999999);

        $pending = PendingMember::updateOrCreate(
            ['target' => $request->target],
            [
                'nama' => $request->nama,
                'metode' => $request->metode,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        if ($request->metode === 'email') {
            try {
                Mail::to($request->target)->send(new MemberVerificationMail($otp, $request->nama));
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $this->sendWhatsAppOTP($request->target, $otp, $request->nama);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengirim WhatsApp: ' . $e->getMessage()], 500);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode verifikasi telah dikirim ke ' . $request->target,
            'pending_id' => $pending->id
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'target' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $pending = PendingMember::where('target', $request->target)
                                ->where('otp', $request->otp)
                                ->first();

        if (!$pending) {
            return response()->json(['status' => 'error', 'message' => 'Kode verifikasi salah!'], 422);
        }

        if (Carbon::now()->greaterThan($pending->expires_at)) {
            return response()->json(['status' => 'error', 'message' => 'Kode verifikasi sudah kadaluarsa.'], 422);
        }

        $pelanggan = Pelanggan::create([
            'nama' => $pending->nama,
            'email' => $pending->metode === 'email' ? $pending->target : null,
            'no_hp' => $pending->metode === 'whatsapp' ? $pending->target : null,
            'member_level' => 'Member',
            'poin' => 0
        ]);

        if ($pending->metode === 'email') {
            try { Mail::to($pending->target)->send(new MemberCardMail($pelanggan)); } catch (\Exception $e) {}
        } else {
            try { $this->sendWhatsAppBarcode($pelanggan); } catch (\Exception $e) {}
        }

        $pending->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil! Member ' . $pelanggan->nama . ' telah aktif.',
            'member' => $pelanggan
        ]);
    }

    private function sendWhatsAppOTP($no_hp, $otp, $nama)
    {
        if (!$this->whatsapp_token) throw new \Exception("WhatsApp Token belum diatur di .env");

        $message = "Halo *{$nama}*,\n\nKode verifikasi pendaftaran member Anda adalah: *{$otp}*\n\nKode ini berlaku selama 10 menit. Mohon tidak memberikan kode ini kepada siapapun.\n\nTerima kasih,\n*" . config('app.name') . "*";

        // Ditambahkan withoutVerifying() untuk mengatasi error SSL di XAMPP/Localhost
        Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $no_hp,
            'message' => $message,
            'delay' => '2',
            'countryCode' => '62',
        ]);
    }

    private function sendWhatsAppBarcode($pelanggan)
    {
        if (!$this->whatsapp_token) return;

        $nama_toko = config('app.name');
        
        // 1. KIRIM GAMBAR BARCODE
        $barcodeUrl = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . $pelanggan->kode_member . "&scale=3&rotate=N&includetext=true";
        
        Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $pelanggan->no_hp,
            'url' => $barcodeUrl,
            'delay' => '2',
            'countryCode' => '62',
        ]);

        // 2. KIRIM DATA MEMBER (TEKS)
        $message = "Selamat! *{$pelanggan->nama}*,\n\nPendaftaran member Anda di *{$nama_toko}* telah berhasil.\n\n*DATA MEMBER:*\nID: *{$pelanggan->kode_member}*\nLevel: {$pelanggan->member_level}\nPoin: " . number_format($pelanggan->poin) . "\n\nSimpan gambar barcode di atas sebagai kartu member digital Anda. Tunjukkan kepada kasir saat bertransaksi untuk mendapatkan poin.\n\nTerima kasih!";

        Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $pelanggan->no_hp,
            'message' => $message,
            'delay' => '3', // Delay sedikit lebih lama agar urutannya pas
            'countryCode' => '62',
        ]);
    }
}
