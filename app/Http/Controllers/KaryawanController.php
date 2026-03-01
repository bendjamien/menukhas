<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Http;

class KaryawanController extends Controller
{
    private $whatsapp_token;

    public function __construct() {
        $this->whatsapp_token = env('WHATSAPP_TOKEN', '');
    }

    public function index()
    {
        $karyawans = User::where('role', 'karyawan')->latest()->paginate(10);
        return view('karyawan.index', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20', // Wajib karena untuk kirim PIN
            'pin' => 'required|string|size:6|unique:users,pin',
        ]);

        // Generate dummy email & username agar tidak error di level database
        $uniqueId = Str::random(5);
        $username = Str::slug($request->name) . '-' . $uniqueId;
        $email = $username . '@menukhas.staff';

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make('staff123'),
            'no_hp' => $request->no_hp,
            'role' => 'karyawan',
            'jabatan' => $request->jabatan,
            'pin' => $request->pin,
            'status' => true
        ]);

        // Kirim WhatsApp otomatis
        try {
            $this->sendWhatsAppPin($user);
            $msg = 'Karyawan berhasil ditambahkan & PIN terkirim ke WhatsApp.';
        } catch (\Exception $e) {
            $msg = 'Karyawan ditambahkan, namun gagal mengirim WhatsApp: ' . $e->getMessage();
        }

        return back()->with('toast_success', $msg);
    }

    private function sendWhatsAppPin($user)
    {
        if (!$this->whatsapp_token) return;

        $message = "Halo *{$user->name}*,\n\nSelamat! Anda telah terdaftar sebagai *{$user->jabatan}* di *Menu Khas*.\n\nBerikut adalah *PIN Absensi* Anda:\nPIN: *{$user->pin}*\n\nMohon simpan PIN ini baik-baik untuk melakukan Absen Masuk dan Pulang di mesin kasir.\n\nTerima kasih!";

        Http::withHeaders([
            'Authorization' => $this->whatsapp_token,
        ])->withoutVerifying()->post('https://api.fonnte.com/send', [
            'target' => $user->no_hp,
            'message' => $message,
            'countryCode' => '62',
        ]);
    }

    public function update(Request $request, User $karyawan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'pin' => 'required|string|size:6|unique:users,pin,'.$karyawan->id,
        ]);

        $karyawan->update([
            'name' => $request->name,
            'jabatan' => $request->jabatan,
            'no_hp' => $request->no_hp,
            'pin' => $request->pin,
        ]);

        return back()->with('toast_success', 'Data karyawan diperbarui.');
    }

    public function destroy(User $karyawan)
    {
        // Pastikan dia memang role karyawan sebelum hapus
        if ($karyawan->role !== 'karyawan') {
            return back()->with('toast_danger', 'Tidak dapat menghapus user ini dari sini.');
        }
        
        $karyawan->delete();
        return back()->with('toast_success', 'Karyawan telah dihapus.');
    }
}
