<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('waktu_buka');
            $table->dateTime('waktu_tutup')->nullable();
            $table->bigInteger('saldo_awal')->default(0);
            $table->bigInteger('total_tunai_diharapkan')->default(0); // Hitungan sistem
            $table->bigInteger('total_tunai_aktual')->default(0);    // Hitungan fisik kasir
            $table->bigInteger('selisih')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
