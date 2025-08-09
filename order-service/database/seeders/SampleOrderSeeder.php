<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class SampleOrderSeeder extends Seeder
{
    public function run()
    {
        // Create sample users if they don't exist
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'phone' => '081234567890',
                'role' => 'customer'
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@gmail.com',
                'phone' => '081234567891',
                'role' => 'customer'
            ],
            [
                'name' => 'Ahmad Dahlan',
                'email' => 'ahmad@gmail.com',
                'phone' => '081234567892',
                'role' => 'customer'
            ],
            [
                'name' => 'Rina Wijaya',
                'email' => 'rina@gmail.com',
                'phone' => '081234567893',
                'role' => 'customer'
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi@gmail.com',
                'phone' => '081234567894',
                'role' => 'customer'
            ]
        ];

        foreach ($customers as $customerData) {
            User::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'password' => bcrypt('password'),
                    'role' => $customerData['role']
                ]
            );
        }

        // Sample orders data
        $sampleOrders = [
            [
                'order_number' => 'ORD-' . date('Ymd') . '-001',
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi@gmail.com',
                'customer_phone' => '081234567890',
                'lapangan_id' => 1,
                'jadwal_lapangan_id' => 1,
                'tanggal_booking' => Carbon::today()->addDays(1),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'total_harga' => 150000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking untuk turnamen futsal kantor'
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-002',
                'customer_name' => 'Siti Nurhaliza',
                'customer_email' => 'siti@gmail.com',
                'customer_phone' => '081234567891',
                'lapangan_id' => 2,
                'jadwal_lapangan_id' => 2,
                'tanggal_booking' => Carbon::today()->addDays(2),
                'jam_mulai' => '14:00',
                'jam_selesai' => '16:00',
                'total_harga' => 120000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Latihan badminton rutin'
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-003',
                'customer_name' => 'Ahmad Dahlan',
                'customer_email' => 'ahmad@gmail.com',
                'customer_phone' => '081234567892',
                'lapangan_id' => 3,
                'jadwal_lapangan_id' => 3,
                'tanggal_booking' => Carbon::today()->addDays(3),
                'jam_mulai' => '19:00',
                'jam_selesai' => '21:00',
                'total_harga' => 180000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking untuk latihan basket tim sekolah'
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-004',
                'customer_name' => 'Rina Wijaya',
                'customer_email' => 'rina@gmail.com',
                'customer_phone' => '081234567893',
                'lapangan_id' => 4,
                'jadwal_lapangan_id' => 4,
                'tanggal_booking' => Carbon::today()->addDays(4),
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'total_harga' => 80000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Turnamen tenis meja keluarga'
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-005',
                'customer_name' => 'Dedi Kurniawan',
                'customer_email' => 'dedi@gmail.com',
                'customer_phone' => '081234567894',
                'lapangan_id' => 1,
                'jadwal_lapangan_id' => 5,
                'tanggal_booking' => Carbon::today()->addDays(5),
                'jam_mulai' => '16:00',
                'jam_selesai' => '18:00',
                'total_harga' => 150000,
                'status' => 'cancelled',
                'payment_status' => 'unpaid',
                'notes' => 'Dibatalkan karena cuaca buruk'
            ],
            // Additional orders for yesterday and today
            [
                'order_number' => 'ORD-' . date('Ymd', strtotime('-1 day')) . '-001',
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi@gmail.com',
                'customer_phone' => '081234567890',
                'lapangan_id' => 2,
                'jadwal_lapangan_id' => 6,
                'tanggal_booking' => Carbon::yesterday(),
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'total_harga' => 120000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking kemarin yang sudah selesai',
                'created_at' => Carbon::yesterday()
            ],
            [
                'order_number' => 'ORD-' . date('Ymd') . '-006',
                'customer_name' => 'Siti Nurhaliza',
                'customer_email' => 'siti@gmail.com',
                'customer_phone' => '081234567891',
                'lapangan_id' => 3,
                'jadwal_lapangan_id' => 7,
                'tanggal_booking' => Carbon::today(),
                'jam_mulai' => '20:00',
                'jam_selesai' => '22:00',
                'total_harga' => 180000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking hari ini'
            ]
        ];

        foreach ($sampleOrders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('Sample orders created successfully!');
    }
}
