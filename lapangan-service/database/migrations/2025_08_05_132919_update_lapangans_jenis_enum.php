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
        Schema::table('lapangans', function (Blueprint $table) {
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('jenis');
        });
        
        Schema::table('lapangans', function (Blueprint $table) {
            $table->enum('jenis', ['Futsal', 'Badminton', 'Basket', 'Tenis', 'Voli'])->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lapangans', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
        
        Schema::table('lapangans', function (Blueprint $table) {
            $table->enum('jenis', ['futsal', 'badminton', 'basket', 'tenis_meja'])->after('nama');
        });
    }
};
