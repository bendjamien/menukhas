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
        === DOKUMENTASI LENGKAP APLIKASI MENUKHAS ===
        MenuKhas adalah aplikasi POS (Point of Sales) dan manajemen toko / restoran yang sangat komprehensif.
        
        **Modul & Fitur Utama:**
        1. **Manajemen Penjualan (Kasir/POS):** Mencatat pesanan, menghitung total, menerapkan diskon (voucher/poin), mencetak struk, dan mencatat metode pembayaran (Tunai, Transfer via simulasi Midtrans). Mendukung pemotongan stok otomatis.
        2. **Manajemen Inventaris (Gudang):** Mengelola master data produk, kategori, harga modal & jual. Melacak stok (masuk/keluar) beserta riwayat/log perubahannya.
        3. **Manajemen Pelanggan (Member):** Mendaftarkan pelanggan reguler menjadi member, mengelola level member, dan sistem poin loyalitas (dapat poin tiap belanja, poin bisa ditukar diskon).
        4. **Sistem Shift (Buka/Tutup Kasir):** Kasir wajib membuka shift dengan modal awal sebelum bertransaksi, dan menutup shift di akhir jam kerja dengan laporan selisih kas fisik vs sistem.
        5. **Sistem Absensi (Kehadiran):** Karyawan dapat melakukan absen masuk dan pulang. Sistem menghitung keterlambatan berdasarkan jam operasional yang ditentukan admin.
        6. **Manajemen Kasbon (Pinjaman Karyawan):** Karyawan bisa meminjam uang (kasbon) yang nantinya akan otomatis memotong gaji bulanan mereka.
        7. **Penggajian (Payroll):** Mengatur gaji pokok karyawan. Sistem secara otomatis membuat slip gaji bulanan = (Gaji Pokok + Lembur) - Potongan Kasbon. Pembayaran gaji bisa via Tunai atau Transfer.
        8. **Laporan Keuangan & Operasional:** Laporan omzet pendapatan, pengeluaran operasional (sewa, listrik, dll), rekap absensi, riwayat gaji, dan rekap shift kasir. Dapat diekspor ke PDF.

        **Peran Pengguna (Role):**
        - **Owner/Admin:** Akses penuh ke seluruh fitur (pengaturan gaji, laporan keuangan, hapus data, dll).
        - **Kasir:** Hanya bisa akses POS, buka/tutup shift, absensi, dan kasbon.
        - **Karyawan:** Bisa absensi, kasbon, dan melihat riwayat gajinya sendiri.
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
        PERAN: Kamu adalah 'Mks Bot', asisten AI tingkat lanjut, sangat cerdas, dan ahli untuk sistem 'MenuKhas' (POS, HR, & ERP) sekaligus asisten ahli berpengetahuan luas.
        NAMA USER YANG BERTANYA: $userName
        WAKTU SERVER SEKARANG: " . now()->format('d M Y H:i:s') . "

        PETUNJUK UTAMA (PENTING):
        1. **Jadilah Ahli & Analitis:** Berikan jawaban yang komprehensif, akurat, profesional, namun tetap mudah dipahami. Jangan berikan jawaban singkat yang terkesan 'robotik', gunakan gaya bahasa layaknya konsultan bisnis & IT.
        2. **Gunakan Konteks Aplikasi MenuKhas:** Jika ditanya seputar cara penggunaan, alur kerja, kasbon, gaji, absensi, atau POS, rujuk pada 'DOKUMENTASI LENGKAP APLIKASI MENUKHAS'.
        3. **Analisis Data Real-Time:** Jika user menanyakan laporan hari ini (penjualan, omzet, stok, kasbon, dsb), gunakan data dari 'LAPORAN REAL-TIME' dengan cerdas. Berikan insight tambahan (misal: 'Omzet hari ini cukup baik dengan best seller X...').
        4. **Jawab Pertanyaan Umum Apa Saja:** Jika user bertanya tentang topik di luar MenuKhas (misal: coding, matematika, sejarah, resep masakan, tips bisnis), jawablah dengan cerdas layaknya ChatGPT / Gemini versi penuh. Jangan pernah bilang 'Saya hanya bisa menjawab tentang MenuKhas'.
        5. **Format Jawaban:** Gunakan Markdown (bold, italic, list) agar rapi dan mudah dibaca di dalam UI chat.

        --- MULAI KONTEKS MENUKHAS ---
        
        $dokumentasiAplikasi

        $laporanRealTime

        --- AKHIR KONTEKS MENUKHAS ---

        **Pertanyaan dari User ($userName):**
        $userMessage
        ";

        // ============================================================
        // 3. KIRIM KE AI
        // ============================================================
        $apiKey = env('GEMINI_API_KEY');
        
        // Gunakan model yang paling stabil dan cepat (Flash) sebagai utama
        $models = ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash'];
        $lastError = 'Unknown error';

        foreach ($models as $model) {
            try {
                $response = Http::withOptions([
                    'verify' => false,
                    'connect_timeout' => 10,
                    'timeout' => 30
                ])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $finalPrompt]]]]
                ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    if ($reply) {
                        return response()->json(['reply' => $reply]);
                    }
                } else {
                    $lastError = "Model {$model} gagal: " . $response->body();
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                continue;
            }
        }

        // Jika semua gagal, kembalikan error spesifik agar kita tahu masalahnya
        $errorMessage = "Gagal menghubungi AI Google. ";
        if (str_contains($lastError, 'API_KEY_INVALID')) {
            $errorMessage .= "Penyebab: API Key di file .env tidak valid atau salah.";
        } elseif (str_contains($lastError, 'RESOURCE_EXHAUSTED')) {
            $errorMessage .= "Penyebab: Kuota API Key Anda sudah habis (Limit harian tercapai).";
        } elseif (str_contains($lastError, 'MODEL_NOT_FOUND')) {
            $errorMessage .= "Penyebab: Nama model AI tidak ditemukan atau tidak didukung oleh API Key ini.";
        } else {
            $errorMessage .= "Detail Error: " . substr($lastError, 0, 200);
        }
        
        return response()->json(['reply' => $errorMessage]);
    }
}