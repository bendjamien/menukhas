<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Ubah kolom status agar bisa menampung 'pending', 'selesai', 'batal', dll.
            // Kita ubah jadi string agar fleksibel menerima status dari Midtrans
            $table->string('status', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Kembalikan ke ENUM jika perlu rollback (asumsi nilai lama)
            // $table->enum('status', ['selesai', 'batal'])->change();
        });
    }
};