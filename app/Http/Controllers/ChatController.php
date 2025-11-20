<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Import Semua Model Database
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
        // 1. TARIK DATA REAL-TIME (LANGSUNG DARI DATABASE)
        // Tidak ada Cache sama sekali agar 100% Akurat
        // ============================================================

        // A. INFO TOKO & KARYAWAN
        $namaToko = Setting::where('key', 'company_name')->value('value') ?? 'MenuKhas';
        $listStaff = User::select('name', 'role')->get()->map(fn($u) => "$u->name ($u->role)")->join(', ');

        // B. DATA PRODUK & STOK (DETIK INI JUGA)
        // Mengambil data produk yang statusnya aktif (1)
        $dataGudang = Produk::with('kategori')->where('status', 1)->get()
            ->map(function($p) {
                // Format: [ID] Nama Barang (Kategori) - Rp Harga - Sisa Stok
                return "- [{$p->kode_barcode}] {$p->nama_produk} ({$p->kategori->nama}): Rp ".number_format($p->harga_jual)." [SISA: {$p->stok} {$p->satuan}]";
            })->join("\n");

        // C. KEUANGAN HARI INI (DETIK INI JUGA)
        $hariIni = now()->toDateString();
        $transaksiHariIni = Transaksi::whereDate('tanggal', $hariIni)->where('status', 'selesai');
        
        $omzet = $transaksiHariIni->sum('total');
        $jumlahTrx = $transaksiHariIni->count();

        // D. METODE PEMBAYARAN (Analisa Tunai vs Transfer)
        // Mengambil ID transaksi hari ini untuk dicek metode bayarnya
        $idTrxHariIni = $transaksiHariIni->pluck('id');
        $metodeBayar = Pembayaran::select('metode', DB::raw('count(*) as total'))
            ->whereIn('transaksi_id', $idTrxHariIni)
            ->groupBy('metode')
            ->get()
            ->map(fn($m) => "{$m->metode}: {$m->total}x")
            ->join(', ');

        // E. LOG AKTIVITAS TERAKHIR (SPY MODE)
        // Mengambil 5 aktivitas terakhir (Jual, Beli, Stok Masuk)
        $logAktivitas = StokLog::with(['user', 'produk'])
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(function($l) {
                $jam = date('H:i', strtotime($l->tanggal));
                $user = $l->user->name ?? 'Sistem';
                $produk = $l->produk->nama_produk ?? '-';
                // Contoh: [10:30] Budi: Penjualan Le Minerale (-1)
                return "- [$jam] $user: {$l->keterangan} -> $produk ({$l->tipe} {$l->jumlah})";
            })->join("\n");

        // F. DATA MEMBER & PROMO
        $topMember = Pelanggan::orderByDesc('poin')->limit(3)->get()
            ->map(fn($m) => "{$m->nama} ({$m->poin} Poin)")->join(', ');
        
        $promoAktif = Voucher::where('is_active', 1)->get()
            ->map(fn($v) => "Kode {$v->kode} (Nilai: ".number_format($v->nilai).")")->join(', ');

        // G. BEST SELLER (Produk Paling Laku)
        $bestSeller = TransaksiDetail::select('produk_id', DB::raw('SUM(jumlah) as total_jual'))
            ->groupBy('produk_id')
            ->orderByDesc('total_jual')
            ->limit(3)
            ->with('produk')
            ->get()
            ->map(fn($item) => $item->produk ? $item->produk->nama_produk." ({$item->total_jual} terjual)" : "-")
            ->join(', ');

        // ============================================================
        // 2. RAKIT PROMPT (OTAK BOT)
        // ============================================================
        $finalPrompt = "
        PERAN: Kamu adalah 'Mks Bot', Manajer Toko '$namaToko'.
        USER: $userName
        WAKTU SEKARANG: " . now()->format('d M Y H:i') . "

        === LAPORAN REAL-TIME (DETIK INI) ===
        💰 Omzet Hari Ini: Rp " . number_format($omzet, 0, ',', '.') . "
        🧾 Jumlah Transaksi: $jumlahTrx struk
        💳 Metode Bayar: " . ($metodeBayar ?: 'Belum ada transaksi') . "
        🏆 Best Seller: $bestSeller

        === LOG AKTIVITAS TERAKHIR ===
        (Gunakan ini jika user tanya 'apa yang baru terjadi?')
        $logAktivitas

        === DATA GUDANG & HARGA ===
        $dataGudang

        === INFO LAINNYA ===
        👥 Staff: $listStaff
        👑 Top Member: " . ($topMember ?: '-') . "
        🎟️ Promo: " . ($promoAktif ?: 'Tidak ada') . "

        === INSTRUKSI KHUSUS ===
        1. Data di atas adalah MUTLAK dan TERBARU. Jangan ragu dengan angkanya.
        2. Jika Log Aktivitas menunjukkan ada 'Penjualan', itu berarti stok SUDAH berkurang (karena data gudang sudah update).
        3. Jika ditanya 'Siapa member terbaik?', lihat Top Member.
        4. Jika ditanya 'Siapa kasir yang bertugas?', lihat log aktivitas atau daftar staff.
        5. Jawab dengan gaya profesional, membantu, dan to-the-point.

        PERTANYAAN USER:
        $userMessage
        ";

        // ============================================================
        // 3. KIRIM KE GOOGLE GEMINI (ANTI-MACET MODE)
        // ============================================================
        $apiKey = env('GEMINI_API_KEY');
        
        // Urutan prioritas model: Paling Baru -> Stabil -> Ringan
        $models = ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash'];

        foreach ($models as $model) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [['parts' => [['text' => $finalPrompt]]]]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    if ($reply) return response()->json(['reply' => $reply]);
                }
            } catch (\Exception $e) {
                continue; // Jika error, coba model berikutnya
            }
        }

        return response()->json(['reply' => 'Maaf, semua jalur AI sedang sibuk. Mohon tunggu 1 menit lagi.']);
    }
}