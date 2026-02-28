<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing data: minutes * 60 = seconds
        DB::table('absensis')->update([
            'keterlambatan' => DB::raw('keterlambatan * 60')
        ]);

        // 2. Update column comment
        Schema::table('absensis', function (Blueprint $table) {
            $table->integer('keterlambatan')->default(0)->comment('Dalam detik')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Update existing data: seconds / 60 = minutes
        DB::table('absensis')->update([
            'keterlambatan' => DB::raw('keterlambatan / 60')
        ]);

        // 2. Revert column comment
        Schema::table('absensis', function (Blueprint $table) {
            $table->integer('keterlambatan')->default(0)->comment('Dalam menit')->change();
        });
    }
};
