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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('lapangan_id'); // ID dari lapangan service
            $table->unsignedBigInteger('jadwal_lapangan_id'); // ID dari jadwal lapangan service
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('tanggal_booking');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'paid', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->json('lapangan_info')->nullable(); // Cache info lapangan untuk referensi
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['customer_email', 'status']);
            $table->index(['tanggal_booking', 'status']);
            $table->index(['lapangan_id', 'jadwal_lapangan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
