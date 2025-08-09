<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Lapangan Service API",
 *     version="1.0.0",
 *     description="API documentation untuk Lapangan Service - Sistem Booking Lapangan Olahraga",
 *     @OA\Contact(
 *         email="admin@lapangan-service.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8001/api",
 *     description="Local Development Server"
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
 *     name="Lapangan",
 *     description="Endpoints untuk manajemen lapangan olahraga"
 * )
 * 
 * @OA\Tag(
 *     name="Jadwal Lapangan",
 *     description="Endpoints untuk manajemen jadwal lapangan"
 * )
 * 
 * @OA\Schema(
 *     schema="Lapangan",
 *     type="object",
 *     title="Lapangan",
 *     description="Model data lapangan olahraga",
 *     required={"id", "nama", "jenis", "harga_per_jam", "kapasitas", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID unik lapangan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="nama",
 *         type="string",
 *         description="Nama lapangan",
 *         example="Lapangan Futsal A"
 *     ),
 *     @OA\Property(
 *         property="jenis",
 *         type="string",
 *         description="Jenis lapangan",
 *         enum={"futsal", "badminton", "basket", "voli", "tennis"},
 *         example="futsal"
 *     ),
 *     @OA\Property(
 *         property="deskripsi",
 *         type="string",
 *         description="Deskripsi detail lapangan",
 *         example="Lapangan futsal dengan rumput sintetis berkualitas tinggi dan sistem pencahayaan LED"
 *     ),
 *     @OA\Property(
 *         property="harga_per_jam",
 *         type="number",
 *         format="float",
 *         description="Harga sewa per jam dalam Rupiah",
 *         example=150000
 *     ),
 *     @OA\Property(
 *         property="kapasitas",
 *         type="integer",
 *         description="Kapasitas maksimal pemain",
 *         example=14
 *     ),
 *     @OA\Property(
 *         property="fasilitas",
 *         type="string",
 *         description="Daftar fasilitas yang tersedia",
 *         example="AC, Sound System, Kamar Ganti, Toilet, Kantin"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status ketersediaan lapangan",
 *         enum={"tersedia", "maintenance", "tidak_tersedia"},
 *         example="tersedia"
 *     ),
 *     @OA\Property(
 *         property="gambar",
 *         type="string",
 *         description="Path file gambar lapangan",
 *         example="lapangan/futsal-a.jpg"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal pembuatan record",
 *         example="2024-01-15T10:30:00.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal update terakhir record",
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
 *         property="email_verified_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal verifikasi email",
 *         example="2024-01-15T10:30:00.000000Z"
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
 * 
 * @OA\Schema(
 *     schema="JadwalLapangan",
 *     type="object",
 *     title="JadwalLapangan",
 *     description="Model data jadwal lapangan",
 *     required={"id", "lapangan_id", "tanggal", "jam_mulai", "jam_selesai", "harga", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID unik jadwal lapangan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="lapangan_id",
 *         type="integer",
 *         description="ID lapangan yang dijadwalkan",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="tanggal",
 *         type="string",
 *         format="date",
 *         description="Tanggal jadwal",
 *         example="2024-01-15"
 *     ),
 *     @OA\Property(
 *         property="jam_mulai",
 *         type="string",
 *         format="time",
 *         description="Jam mulai sesi",
 *         example="08:00"
 *     ),
 *     @OA\Property(
 *         property="jam_selesai",
 *         type="string",
 *         format="time",
 *         description="Jam selesai sesi",
 *         example="10:00"
 *     ),
 *     @OA\Property(
 *         property="harga",
 *         type="number",
 *         format="float",
 *         description="Harga untuk sesi ini dalam Rupiah",
 *         example=300000
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status jadwal",
 *         enum={"tersedia", "dibooking", "selesai", "dibatalkan"},
 *         example="tersedia"
 *     ),
 *     @OA\Property(
 *         property="lapangan",
 *         ref="#/components/schemas/Lapangan",
 *         description="Data lapangan yang terkait"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal pembuatan jadwal",
 *         example="2024-01-15T10:30:00.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Tanggal update terakhir jadwal",
 *         example="2024-01-15T14:45:00.000000Z"
 *     )
 * )
 */
abstract class Controller
{
    //
}
