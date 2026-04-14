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
            // Status Produksi: pending, processing, ready, completed
            $table->string('status_produksi', 20)->default('pending')->after('status');
            $table->timestamp('waktu_mulai_produksi')->nullable()->after('status_produksi');
            $table->timestamp('waktu_selesai_produksi')->nullable()->after('waktu_mulai_produksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['status_produksi', 'waktu_mulai_produksi', 'waktu_selesai_produksi']);
        });
    }
};
