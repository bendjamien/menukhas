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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('role');
            // Kita gunakan DB::statement karena mengubah enum secara langsung di Laravel lewat blueprint sering bermasalah di MySQL tertentu
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'owner', 'karyawan') NOT NULL DEFAULT 'kasir'");
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('jabatan');
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kasir', 'owner') NOT NULL DEFAULT 'kasir'");
        });
    }
};
