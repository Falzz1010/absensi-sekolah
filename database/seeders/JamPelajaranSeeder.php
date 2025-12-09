<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JamPelajaran;

class JamPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $jamPelajaran = [
            ['nama' => 'Jam ke-1', 'jam_mulai' => '07:00', 'jam_selesai' => '07:45', 'urutan' => 1],
            ['nama' => 'Jam ke-2', 'jam_mulai' => '07:45', 'jam_selesai' => '08:30', 'urutan' => 2],
            ['nama' => 'Jam ke-3', 'jam_mulai' => '08:30', 'jam_selesai' => '09:15', 'urutan' => 3],
            ['nama' => 'Istirahat 1', 'jam_mulai' => '09:15', 'jam_selesai' => '09:30', 'urutan' => 4, 'keterangan' => 'Istirahat'],
            ['nama' => 'Jam ke-4', 'jam_mulai' => '09:30', 'jam_selesai' => '10:15', 'urutan' => 5],
            ['nama' => 'Jam ke-5', 'jam_mulai' => '10:15', 'jam_selesai' => '11:00', 'urutan' => 6],
            ['nama' => 'Jam ke-6', 'jam_mulai' => '11:00', 'jam_selesai' => '11:45', 'urutan' => 7],
            ['nama' => 'Istirahat 2', 'jam_mulai' => '11:45', 'jam_selesai' => '12:15', 'urutan' => 8, 'keterangan' => 'Istirahat & Sholat'],
            ['nama' => 'Jam ke-7', 'jam_mulai' => '12:15', 'jam_selesai' => '13:00', 'urutan' => 9],
            ['nama' => 'Jam ke-8', 'jam_mulai' => '13:00', 'jam_selesai' => '13:45', 'urutan' => 10],
        ];

        foreach ($jamPelajaran as $jam) {
            JamPelajaran::create($jam);
        }
    }
}
