<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\PendingMember;
use App\Mail\MemberVerificationMail;
use App\Mail\MemberCardMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $this->sendWhatsAppBarcode($pelanggan);
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

        if (str_starts_with($no_hp, '0')) {
            $no_hp = '62' . substr($no_hp, 1);
        }

        $message = "Halo *{$nama}*,\n\nKode verifikasi pendaftaran member Anda adalah: *{$otp}*\n\nKode ini berlaku selama 10 menit. Mohon tidak memberikan kode ini kepada siapapun.\n\nTerima kasih,\n*" . config('app.name') . "*";

        $response = Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $no_hp,
            'message' => $message,
            'delay' => '2',
            'countryCode' => '62',
        ]);

        $resBody = $response->json();
        
        if (!$response->successful() || (isset($resBody['status']) && $resBody['status'] === false)) {
            $errorMsg = $resBody['reason'] ?? 'Terjadi kesalahan pada server WhatsApp gateway.';
            throw new \Exception($errorMsg);
        }
    }

    private function sendWhatsAppBarcode($pelanggan)
    {
        if (!$this->whatsapp_token) return;

        $no_hp = $pelanggan->no_hp;
        if (str_starts_with($no_hp, '0')) {
            $no_hp = '62' . substr($no_hp, 1);
        }

        $nama_toko = config('app.name');
        
        // Gunakan URL Barcode Publik yang pasti bisa diakses Fonnte dari internet
        $barcodeUrlPublic = "https://barcodeapi.org/api/128/" . $pelanggan->kode_member . ".png";

        // 1. Simpan salinan ke storage lokal (untuk arsip internal)
        try {
            $barcodeContent = file_get_contents($barcodeUrlPublic);
            $filename = 'barcode-' . $pelanggan->kode_member . '.png';
            Storage::disk('public')->put('barcodes/' . $filename, $barcodeContent);
        } catch (\Exception $e) {
            Log::error("Gagal simpan barcode: " . $e->getMessage());
        }
        
        $caption = "Selamat! *{$pelanggan->nama}*,\n\nPendaftaran member Anda di *{$nama_toko}* telah berhasil.\n\n*DATA MEMBER:*\nID: *{$pelanggan->kode_member}*\nLevel: {$pelanggan->member_level}\n\nSimpan gambar barcode ini sebagai kartu member digital Anda. Tunjukkan kepada kasir saat bertransaksi untuk mendapatkan poin.\n\nTerima kasih!";

        // 2. Kirim ke Fonnte menggunakan URL publik yang stabil
        $response = Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $no_hp,
            'url' => $barcodeUrlPublic,
            'message' => $caption,
            'delay' => '2',
            'countryCode' => '62',
        ]);

        $resBody = $response->json();
        
        if (!$response->successful() || (isset($resBody['status']) && $resBody['status'] === false)) {
            $errorMsg = $resBody['reason'] ?? 'Gagal mengirim gambar barcode member.';
            Log::error("WhatsApp Barcode Error: " . $errorMsg);
        }
    }
}
