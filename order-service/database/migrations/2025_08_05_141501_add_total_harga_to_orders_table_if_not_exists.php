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
        Schema::table('orders', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('orders', 'total_harga')) {
                $table->decimal('total_harga', 10, 2)->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('orders', 'jam_mulai')) {
                $table->time('jam_mulai')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'tanggal_booking')) {
                $table->date('tanggal_booking')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', ['pending', 'confirmed', 'paid', 'cancelled', 'completed'])->default('pending');
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            }
            
            if (!Schema::hasColumn('orders', 'lapangan_id')) {
                $table->unsignedBigInteger('lapangan_id')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'lapangan_info')) {
                $table->json('lapangan_info')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'total_harga')) {
                $table->dropColumn('total_harga');
            }
        });
    }
};
