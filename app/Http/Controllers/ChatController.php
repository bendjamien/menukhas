<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Pelanggan;
use App\Models\Voucher;
use App\Models\Setting;
use App\Models\StokLog;
use App\Models\Pembayaran;
use App\Models\User;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $userMessage = $request->input('message');
        $user = Auth::user();
        $userName = $user ? "$user->name ($user->role)" : 'Tamu';

        // ============================================================
        // 1. SIAPKAN DATA REAL-TIME (SAMA SEPERTI SEBELUMNYA)
        // ============================================================
        $namaToko = Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';
        $listStaff = User::select('name', 'role')->get()->map(fn($u) => "$u->name ($u->role)")->join(', ');

        $dataGudang = Produk::with('kategori')->where('status', 1)->get()
            ->map(function($p) {
                return "- [{$p->kode_barcode}] {$p->nama_produk} ({$p->kategori->nama}): Rp ".number_format($p->harga_jual)." [SISA: {$p->stok} {$p->satuan}]";
            })->join("\n");

        $hariIni = now()->toDateString();
        $transaksiHariIni = Transaksi::whereDate('tanggal', $hariIni)->where('status', 'selesai');
        
        $omzet = $transaksiHariIni->sum('total');
        $jumlahTrx = $transaksiHariIni->count();

        $idTrxHariIni = $transaksiHariIni->pluck('id');
        $metodeBayar = Pembayaran::select('metode', DB::raw('count(*) as total'))
            ->whereIn('transaksi_id', $idTrxHariIni)
            ->groupBy('metode')
            ->get()
            ->map(fn($m) => "{$m->metode}: {$m->total}x")
            ->join(', ');

        $logAktivitas = StokLog::with(['user', 'produk'])
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function($l) {
                $jam = date('H:i', strtotime($l->tanggal));
                $user = $l->user->name ?? 'Sistem';
                $produk = $l->produk->nama_produk ?? '-';
                return "- [$jam] $user: {$l->keterangan} -> $produk ({$l->tipe} {$l->jumlah})";
            })->join("\n");

        $topMember = Pelanggan::orderByDesc('poin')->limit(3)->get()
            ->map(fn($m) => "{$m->nama} ({$m->poin} Poin)")->join(', ');
        
        $promoAktif = Voucher::where('is_active', 1)->get()
            ->map(fn($v) => "Kode {$v->kode} (Nilai: ".number_format($v->nilai).")")->join(', ');

        $bestSeller = TransaksiDetail::select('produk_id', DB::raw('SUM(jumlah) as total_jual'))
            ->groupBy('produk_id')
            ->orderByDesc('total_jual')
            ->limit(3)
            ->with('produk')
            ->get()
            ->map(fn($item) => $item->produk ? $item->produk->nama_produk." ({$item->total_jual} terjual)" : "-")
            ->join(', ');

        // ============================================================
        // 2. RAKIT PROMPT YANG LEBUH CERDAS DAN TERSTRUKTUR
        // ============================================================

        // Bagian 1: Basis Pengetahuan tentang Aplikasi MenuKhas
        // Ini adalah "dokumentasi" yang diberikan kepada AI agar memahami aplikasi Anda.
        $dokumentasiAplikasi = "
        === DOKUMENTASI APLIKASI MENUKHAS ===
        MenuKhas adalah aplikasi kasir dan manajemen toko yang komprehensif.
        
        **Fitur Utama:**
        - **Manajemen Penjualan:** Mencatat transaksi, mencetak struk, dan mengelola berbagai metode pembayaran (Tunai, Transfer, E-Wallet).
        - **Manajemen Inventaris:** Melacak stok produk, menambah produk baru, dan mencatat log perubahan stok (masuk/keluar).
        - **Manajemen Pelanggan:** Menyimpan data pelanggan dan sistem poin untuk loyalitas.
        - **Sistem Voucher:** Membuat dan mengelola kode promo/voucher untuk diskon.
        - **Laporan:** Menyediakan laporan penjualan, omzet, dan produk terlaris.
        - **Manajemen Pengguna (User):** Sistem memiliki beberapa peran (role) dengan akses yang berbeda.

        **Peran Pengguna (User Roles):**
        - **Admin:** Akses penuh ke semua fitur, termasuk manajemen pengguna, pengaturan toko, melihat laporan lengkap, dan mengelola master data (produk, pelanggan, voucher).
        - **Kasir:** Fokus pada fitur penjualan. Bisa membuat transaksi, menggunakan voucher, dan melihat laporan sederhana harian.
        - **Gudang:** Fokus pada manajemen stok. Bisa menambahkan produk baru, memperbarui stok, dan melihat log aktivitas stok.
        ";

        // Bagian 2: Gabungkan semua data real-time ke dalam satu blok
        $laporanRealTime = "
        === LAPORAN REAL-TIME (DETIK INI) ===
        💰 Omzet Hari Ini: Rp " . number_format($omzet, 0, ',', '.') . "
        🧾 Jumlah Transaksi: $jumlahTrx struk
        💳 Metode Bayar: " . ($metodeBayar ?: 'Belum ada transaksi') . "
        🏆 Best Seller: $bestSeller
        📜 Log Stok Terakhir:\n$logAktivitas
        📦 Data Stok Gudang:\n$dataGudang
        👥 Staff: $listStaff
        👑 Top Member: " . ($topMember ?: '-') . "
        🎟️ Promo Aktif: " . ($promoAktif ?: 'Tidak ada') . "
        ";

        // Bagian 3: Rangkai semua bagian menjadi prompt akhir
        $finalPrompt = "
        PERAN: Kamu adalah 'Mks Bot', asisten AI cerdas untuk aplikasi kasir 'MenuKhas' dan juga asisten umum.
        USER: $userName
        WAKTU SEKARANG: " . now()->format('d M Y H:i') . "

        PETUNJUK:
        1. **Jawab berdasarkan Konteks Aplikasi:** Jika pertanyaan user seputar cara kerja, fitur, atau 'how-to' aplikasi MenuKhas, gunakan 'DOKUMENTASI APLIKASI' di bawah ini sebagai sumber utama jawabanmu.
        2. **Jawab berdasarkan Data Real-Time:** Jika pertanyaan user seputar laporan toko (omzet, stok, transaksi, best seller, dll), gunakan data dari 'LAPORAN REAL-TIME' di bawah.
        3. **Jawab sebagai Asisten Umum:** Jika pertanyaan tidak relevan dengan aplikasi atau data toko, jawablah dengan pengetahuan umummu sebaik mungkin.
        4. **Bersikaplah Ramah dan Bermanfaat:** Selalu jawab dengan nada yang ramah dan membantu.

        --- MULAI KONTEKS ---
        
        $dokumentasiAplikasi

        $laporanRealTime

        --- AKHIR KONTEKS ---

        **Pertanyaan dari User:**
        $userMessage
        ";

        // ============================================================
        // 3. KIRIM KE AI (SAMA SEPERTI SEBELUMNYA)
        // ============================================================
        $apiKey = env('GEMINI_API_KEY');
        
        // Daftar model untuk dicoba, dari yang terbaru ke yang lama
        $models = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash'];

        foreach ($models as $model) {
            try {
                $response = Http::withOptions(['verify' => false]) // Non-aktifkan SSL verify jika ada masalah cert lokal
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [['parts' => [['text' => $finalPrompt]]]]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    // Pastikan balasan tidak kosong
                    if ($reply && trim($reply) !== '') {
                        return response()->json(['reply' => $reply]);
                    }
                }
            } catch (\Exception $e) {
                // Log error jika perlu, tapi lanjut ke model berikutnya
                // \Log::error("Gemini API Error for model {$model}: " . $e->getMessage());
                continue;
            }
        }

        // Jika semua model gagal
        return response()->json(['reply' => 'Maaf, semua jalur AI sedang sibuk atau mengalami gangguan. Mohon tunggu sebentar lagi.']);
    }
}