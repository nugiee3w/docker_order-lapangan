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
        Schema::create('lapangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['futsal', 'badminton', 'basket', 'tenis_meja']);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_per_jam', 10, 2);
            $table->enum('status', ['tersedia', 'tidak_tersedia', 'maintenance'])->default('tersedia');
            $table->json('fasilitas')->nullable(); // JSON untuk menyimpan array fasilitas
            $table->string('lokasi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lapangans');
    }
};
