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
        Schema::create('jadwal_lapangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapangan_id')->constrained('lapangans')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['tersedia', 'dipesan', 'sedang_digunakan', 'selesai'])->default('tersedia');
            $table->decimal('harga', 10, 2); // Harga bisa berbeda dari harga per jam lapangan untuk waktu tertentu
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['lapangan_id', 'tanggal', 'jam_mulai']);
            $table->unique(['lapangan_id', 'tanggal', 'jam_mulai', 'jam_selesai'], 'unique_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_lapangans');
    }
};
