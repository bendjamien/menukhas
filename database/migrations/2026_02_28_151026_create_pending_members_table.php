<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_members', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('metode', ['email', 'whatsapp']);
            $table->string('target'); // berisi email atau no hp
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_members');
    }
};
