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
            if (!Schema::hasColumn('transaksi', 'poin_earned')) {
                $table->integer('poin_earned')->default(0)->after('kembalian');
            }
            if (!Schema::hasColumn('transaksi', 'poin_used')) {
                $table->integer('poin_used')->default(0)->after('poin_earned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['poin_earned', 'poin_used']);
        });
    }
};