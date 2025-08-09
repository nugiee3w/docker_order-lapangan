<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = [
            [
                'order_number' => 'ORD-001-' . date('Ymd'),
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi.santoso@email.com',
                'customer_phone' => '08123456789',
                'lapangan_id' => 10,
                'tanggal_booking' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:00',
                'total_harga' => 200000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking untuk latihan tim futsal'
            ],
            [
                'order_number' => 'ORD-002-' . date('Ymd'),
                'customer_name' => 'Siti Nurhaliza',
                'customer_email' => 'siti.nurhaliza@email.com',
                'customer_phone' => '08234567890',
                'lapangan_id' => 11,
                'tanggal_booking' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'jam_mulai' => '14:00',
                'jam_selesai' => '16:00',
                'total_harga' => 160000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Booking untuk turnamen badminton'
            ],
            [
                'order_number' => 'ORD-003-' . date('Ymd'),
                'customer_name' => 'Ahmad Wijaya',
                'customer_email' => 'ahmad.wijaya@email.com',
                'customer_phone' => '08345678901',
                'lapangan_id' => 12,
                'tanggal_booking' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'jam_mulai' => '19:00',
                'jam_selesai' => '21:00',
                'total_harga' => 240000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Latihan basket reguler'
            ],
            [
                'order_number' => 'ORD-004-' . date('Ymd'),
                'customer_name' => 'Maya Sari',
                'customer_email' => 'maya.sari@email.com',
                'customer_phone' => '08456789012',
                'lapangan_id' => 10,
                'tanggal_booking' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'jam_mulai' => '16:00',
                'jam_selesai' => '18:00',
                'total_harga' => 200000,
                'status' => 'confirmed',
                'payment_status' => 'unpaid',
                'notes' => 'Acara gathering kantor'
            ],
            [
                'order_number' => 'ORD-005-' . date('Ymd'),
                'customer_name' => 'Eko Prasetyo',
                'customer_email' => 'eko.prasetyo@email.com',
                'customer_phone' => '08567890123',
                'lapangan_id' => 11,
                'tanggal_booking' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'total_harga' => 160000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Tournament badminton sekolah'
            ],
            [
                'order_number' => 'ORD-006-' . date('Ymd'),
                'customer_name' => 'Dewi Lestari',
                'customer_email' => 'dewi.lestari@email.com',
                'customer_phone' => '08678901234',
                'lapangan_id' => 12,
                'tanggal_booking' => Carbon::now()->addDays(6)->format('Y-m-d'),
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:00',
                'total_harga' => 240000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Latihan tim basket putri'
            ],
            [
                'order_number' => 'ORD-007-' . date('Ymd'),
                'customer_name' => 'Rudi Hartono',
                'customer_email' => 'rudi.hartono@email.com',
                'customer_phone' => '08789012345',
                'lapangan_id' => 10,
                'tanggal_booking' => Carbon::now()->addDays(7)->format('Y-m-d'),
                'jam_mulai' => '20:00',
                'jam_selesai' => '22:00',
                'total_harga' => 200000,
                'status' => 'cancelled',
                'payment_status' => 'unpaid',
                'notes' => 'Dibatalkan karena cuaca buruk'
            ],
            [
                'order_number' => 'ORD-008-' . date('Ymd'),
                'customer_name' => 'Linda Sari',
                'customer_email' => 'linda.sari@email.com',
                'customer_phone' => '08890123456',
                'lapangan_id' => 11,
                'tanggal_booking' => Carbon::now()->addDays(8)->format('Y-m-d'),
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'total_harga' => 160000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Booking untuk les badminton'
            ],
            [
                'order_number' => 'ORD-009-' . date('Ymd'),
                'customer_name' => 'Agus Salim',
                'customer_email' => 'agus.salim@email.com',
                'customer_phone' => '08901234567',
                'lapangan_id' => 12,
                'tanggal_booking' => Carbon::now()->addDays(9)->format('Y-m-d'),
                'jam_mulai' => '17:00',
                'jam_selesai' => '19:00',
                'total_harga' => 240000,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => 'Reservasi untuk acara ulang tahun'
            ],
            [
                'order_number' => 'ORD-010-' . date('Ymd'),
                'customer_name' => 'Fitri Handayani',
                'customer_email' => 'fitri.handayani@email.com',
                'customer_phone' => '08012345678',
                'lapangan_id' => 10,
                'tanggal_booking' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'jam_mulai' => '15:00',
                'jam_selesai' => '17:00',
                'total_harga' => 200000,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'notes' => 'Latihan rutin club futsal wanita'
            ]
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
