<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing test orders
        Order::where('customer_name', 'LIKE', 'Test User%')->delete();
        
        // Create test orders for today and tomorrow
        $testOrders = [
            [
                'order_number' => 'TEST-001',
                'lapangan_id' => 1,
                'jadwal_lapangan_id' => 1,
                'customer_name' => 'Test User 1',
                'customer_email' => 'testuser1@example.com',
                'customer_phone' => '081234567890',
                'tanggal_booking' => Carbon::today(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '10:00',
                'total_harga' => 100000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Test booking 1 - Lapangan Futsal'
            ],
            [
                'order_number' => 'TEST-002',
                'lapangan_id' => 1,
                'jadwal_lapangan_id' => 1,
                'customer_name' => 'Test User 2',
                'customer_email' => 'testuser2@example.com',
                'customer_phone' => '081234567891',
                'tanggal_booking' => Carbon::today(),
                'jam_mulai' => '14:00',
                'jam_selesai' => '16:00',
                'total_harga' => 200000,
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'notes' => 'Test booking 2 - Lapangan Futsal (2 jam)'
            ],
            [
                'order_number' => 'TEST-003',
                'lapangan_id' => 2,
                'jadwal_lapangan_id' => 1,
                'customer_name' => 'Test User 3',
                'customer_email' => 'testuser3@example.com',
                'customer_phone' => '081234567892',
                'tanggal_booking' => Carbon::today(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:00',
                'total_harga' => 80000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Test booking 3 - Lapangan Badminton A'
            ],
            [
                'order_number' => 'TEST-004',
                'lapangan_id' => 2,
                'jadwal_lapangan_id' => 1,
                'customer_name' => 'Test User 4',
                'customer_email' => 'testuser4@example.com',
                'customer_phone' => '081234567893',
                'tanggal_booking' => Carbon::tomorrow(),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'total_harga' => 160000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Test booking 4 - Lapangan Badminton A besok (2 jam)'
            ],
            [
                'order_number' => 'TEST-005',
                'lapangan_id' => 3,
                'jadwal_lapangan_id' => 1,
                'customer_name' => 'Test User 5',
                'customer_email' => 'testuser5@example.com',
                'customer_phone' => '081234567894',
                'tanggal_booking' => Carbon::today(),
                'jam_mulai' => '19:00',
                'jam_selesai' => '20:00',
                'total_harga' => 80000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Test booking 5 - Lapangan Badminton B malam'
            ]
        ];

        foreach ($testOrders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('✅ Created ' . count($testOrders) . ' test orders for double booking demo');
        $this->command->info('📅 Test bookings created for today (' . Carbon::today()->format('d/m/Y') . ') and tomorrow');
        $this->command->info('🎾 Lapangan 1 (Futsal): 09:00-10:00, 14:00-16:00 (today)');
        $this->command->info('🏸 Lapangan 2 (Badminton A): 10:00-11:00 (today), 08:00-10:00 (tomorrow)');
        $this->command->info('🏸 Lapangan 3 (Badminton B): 19:00-20:00 (today)');
    }
}
