<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjarans = [
            [
                'nama' => '2024/2025',
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2024-12-20',
                'is_active' => false,
            ],
            [
                'nama' => '2024/2025',
                'semester' => 'Genap',
                'tanggal_mulai' => '2025-01-06',
                'tanggal_selesai' => '2025-06-20',
                'is_active' => true, // Tahun ajaran aktif
            ],
            [
                'nama' => '2025/2026',
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2025-12-19',
                'is_active' => false,
            ],
        ];

        foreach ($tahunAjarans as $ta) {
            \App\Models\TahunAjaran::create($ta);
        }
    }
}
