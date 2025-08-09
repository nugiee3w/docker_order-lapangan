<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Order Service API",
 *     version="1.0.0",
 *     description="API documentation untuk Order Service - Sistem Booking Lapangan Olahraga
 * 
 * ## Test Accounts
 * Gunakan akun berikut untuk testing API:
 * 
 * ### Admin Account
 * - Email: admin@booking.com  
 * - Password: admin123
 * - Role: admin
 * 
 * ### Staff Account  
 * - Email: staff@booking.com
 * - Password: staff123
 * - Role: staff
 * 
 * ### Customer Account
 * - Email: john@example.com
 * - Password: customer123  
 * - Role: customer
 * 
 * ## Authentication Flow
 * 1. Login menggunakan salah satu akun di atas
 * 2. Copy token dari response
 * 3. Klik tombol 'Authorize' di Swagger UI
 * 4. Masukkan token dengan format: Bearer {your_token}
 * 5. Test endpoint yang memerlukan authentication",
 *     @OA\Contact(
 *         email="admin@order-service.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Development Server"
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8001/api",
 *     description="Lapangan Service Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Laravel Sanctum Authentication"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints untuk autentikasi pengguna"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Endpoints untuk manajemen pemesanan lapangan"
 * )
 * 
 * @OA\Tag(
 *     name="Lapangan",
 *     description="Endpoints untuk mengakses data lapangan dari Lapangan Service"
 * )
 * 
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     title="Order",
 *     description="Model data pemesanan lapangan",
 *     required={"id", "user_id", "lapangan_id", "tanggal", "jam_mulai", "jam_selesai", "total_harga", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID unik pemesanan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID pengguna yang memesan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="lapangan_id",
 *         type="integer",
 *         description="ID lapangan yang dipesan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="tanggal",
 *         type="string",
 *         format="date",
 *         description="Tanggal pemesanan",
 *         example="2024-01-15"
 *     ),
 *     @OA\Property(
 *         property="jam_mulai",
 *         type="string",
 *         format="time",
 *         description="Jam mulai pemesanan",
 *         example="08:00"
 *     ),
 *     @OA\Property(
 *         property="jam_selesai",
 *         type="string",
 *         format="time",
 *         description="Jam selesai pemesanan",
 *         example="10:00"
 *     ),
 *     @OA\Property(
 *         property="total_harga",
 *         type="number",
 *         format="float",
 *         description="Total harga pemesanan dalam Rupiah",
 *         example=300000
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status pemesanan",
 *         enum={"pending", "confirmed", "paid", "cancelled", "completed"},
 *         example="confirmed"
 *     ),
 *     @OA\Property(
 *         property="payment_status",
 *         type="string",
 *         description="Status pembayaran",
 *         enum={"unpaid", "paid", "refunded"},
 *         example="paid"
 *     ),
 *     @OA\Property(
 *         property="nama_pemesan",
 *         type="string",
 *         description="Nama pemesan",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email_pemesan",
 *         type="string",
 *         format="email",
 *         description="Email pemesan",
 *         example="john@example.com"
 *     ),
 *     @OA\Property(
 *         property="telepon_pemesan",
 *         type="string",
 *         description="Nomor telepon pemesan",
 *         example="081234567890"
 *     ),
 *     @OA\Property(
 *         property="catatan",
 *         type="string",
 *         description="Catatan tambahan untuk pemesanan",
 *         example="Mohon disiapkan air minum"
 *     ),
 *     @OA\Property(
 *         property="lapangan",
 *         type="object",
 *         description="Data lapangan yang dipesan",
 *         @OA\Property(property="nama", type="string", example="Lapangan Futsal A"),
 *         @OA\Property(property="jenis", type="string", example="futsal"),
 *         @OA\Property(property="harga_per_jam", type="number", example=150000)
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal pembuatan pemesanan",
 *         example="2024-01-15T10:30:00.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal update terakhir pemesanan",
 *         example="2024-01-15T14:45:00.000000Z"
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="Model data pengguna sistem",
 *     required={"id", "name", "email", "role"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID unik pengguna",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nama lengkap pengguna",
 *         example="John Doe"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="Email pengguna",
 *         example="john@example.com"
 *     ),
 *     @OA\Property(
 *         property="role",
 *         type="string",
 *         description="Peran pengguna dalam sistem",
 *         enum={"admin", "customer"},
 *         example="customer"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Nomor telepon pengguna",
 *         example="081234567890"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Alamat pengguna",
 *         example="Jl. Contoh No. 123, Jakarta"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal pembuatan akun",
 *         example="2024-01-15T10:30:00.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal update terakhir akun",
 *         example="2024-01-15T14:45:00.000000Z"
 *     )
 * )
 */
abstract class Controller
{
    //
}
