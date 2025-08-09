<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lapangan;

class LapanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lapangans = [
            [
                'nama' => 'Lapangan Futsal A',
                'jenis' => 'futsal',
                'deskripsi' => 'Lapangan futsal indoor dengan rumput sintetis berkualitas tinggi',
                'harga_per_jam' => 150000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Sound System', 'Toilet', 'Kantin', 'Parkir'],
                'lokasi' => 'Gedung A Lantai 1',
                'gambar' => 'futsal-a.jpg'
            ],
            [
                'nama' => 'Lapangan Futsal B',
                'jenis' => 'futsal',
                'deskripsi' => 'Lapangan futsal indoor dengan pencahayaan LED',
                'harga_per_jam' => 140000,
                'status' => 'tersedia',
                'fasilitas' => ['Sound System', 'Toilet', 'Kantin', 'Parkir'],
                'lokasi' => 'Gedung A Lantai 2',
                'gambar' => 'futsal-b.jpg'
            ],
            [
                'nama' => 'Lapangan Badminton Court 1',
                'jenis' => 'badminton',
                'deskripsi' => 'Lapangan badminton dengan lantai vinyl berkualitas',
                'harga_per_jam' => 80000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Toilet', 'Ruang Ganti', 'Parkir'],
                'lokasi' => 'Gedung B Lantai 1',
                'gambar' => 'badminton-1.jpg'
            ],
            [
                'nama' => 'Lapangan Badminton Court 2',
                'jenis' => 'badminton',
                'deskripsi' => 'Lapangan badminton dengan standar BWF',
                'harga_per_jam' => 85000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Toilet', 'Ruang Ganti', 'Parkir', 'Tribun'],
                'lokasi' => 'Gedung B Lantai 1',
                'gambar' => 'badminton-2.jpg'
            ],
            [
                'nama' => 'Lapangan Basket Indoor',
                'jenis' => 'basket',
                'deskripsi' => 'Lapangan basket indoor dengan ring FIBA standard',
                'harga_per_jam' => 200000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Sound System', 'Toilet', 'Ruang Ganti', 'Kantin', 'Parkir', 'Tribun'],
                'lokasi' => 'Gedung C Lantai 1',
                'gambar' => 'basket-indoor.jpg'
            ],
            [
                'nama' => 'Meja Tenis Meja 1',
                'jenis' => 'tenis_meja',
                'deskripsi' => 'Meja tenis meja Butterfly dengan net resmi',
                'harga_per_jam' => 40000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Toilet', 'Parkir'],
                'lokasi' => 'Gedung D Lantai 1',
                'gambar' => 'tenis-meja-1.jpg'
            ],
            [
                'nama' => 'Meja Tenis Meja 2',
                'jenis' => 'tenis_meja',
                'deskripsi' => 'Meja tenis meja STIGA tournament grade',
                'harga_per_jam' => 45000,
                'status' => 'tersedia',
                'fasilitas' => ['AC', 'Toilet', 'Parkir', 'Sound System'],
                'lokasi' => 'Gedung D Lantai 1',
                'gambar' => 'tenis-meja-2.jpg'
            ],
            [
                'nama' => 'Meja Tenis Meja 3',
                'jenis' => 'tenis_meja',
                'deskripsi' => 'Meja tenis meja untuk pemula',
                'harga_per_jam' => 35000,
                'status' => 'tersedia',
                'fasilitas' => ['Toilet', 'Parkir'],
                'lokasi' => 'Gedung D Lantai 2',
                'gambar' => 'tenis-meja-3.jpg'
            ]
        ];

        foreach ($lapangans as $lapangan) {
            Lapangan::create($lapangan);
        }
    }
}
