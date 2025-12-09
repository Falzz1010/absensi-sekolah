<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Guru;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jadwals = [
            // Senin
            ['guru_id' => 1, 'kelas' => '10 IPA', 'mata_pelajaran' => 'Matematika', 'hari' => 'Senin', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00'],
            ['guru_id' => 2, 'kelas' => '10 IPS', 'mata_pelajaran' => 'Bahasa Indonesia', 'hari' => 'Senin', 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00'],
            ['guru_id' => 3, 'kelas' => '11 IPA', 'mata_pelajaran' => 'Fisika', 'hari' => 'Senin', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00'],
            ['guru_id' => 4, 'kelas' => '11 IPS', 'mata_pelajaran' => 'Sejarah', 'hari' => 'Senin', 'jam_mulai' => '12:30:00', 'jam_selesai' => '14:00:00'],
            
            // Selasa
            ['guru_id' => 5, 'kelas' => '12 IPA', 'mata_pelajaran' => 'Kimia', 'hari' => 'Selasa', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00'],
            ['guru_id' => 6, 'kelas' => '12 IPS', 'mata_pelajaran' => 'Ekonomi', 'hari' => 'Selasa', 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00'],
            ['guru_id' => 1, 'kelas' => '11 IPA', 'mata_pelajaran' => 'Matematika', 'hari' => 'Selasa', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00'],
            ['guru_id' => 2, 'kelas' => '11 IPS', 'mata_pelajaran' => 'Bahasa Indonesia', 'hari' => 'Selasa', 'jam_mulai' => '12:30:00', 'jam_selesai' => '14:00:00'],
            
            // Rabu
            ['guru_id' => 3, 'kelas' => '10 IPA', 'mata_pelajaran' => 'Fisika', 'hari' => 'Rabu', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00'],
            ['guru_id' => 4, 'kelas' => '10 IPS', 'mata_pelajaran' => 'Sejarah', 'hari' => 'Rabu', 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00'],
            ['guru_id' => 5, 'kelas' => '11 IPA', 'mata_pelajaran' => 'Kimia', 'hari' => 'Rabu', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00'],
            ['guru_id' => 6, 'kelas' => '11 IPS', 'mata_pelajaran' => 'Ekonomi', 'hari' => 'Rabu', 'jam_mulai' => '12:30:00', 'jam_selesai' => '14:00:00'],
            
            // Kamis
            ['guru_id' => 1, 'kelas' => '12 IPA', 'mata_pelajaran' => 'Matematika', 'hari' => 'Kamis', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00'],
            ['guru_id' => 2, 'kelas' => '12 IPS', 'mata_pelajaran' => 'Bahasa Indonesia', 'hari' => 'Kamis', 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00'],
            ['guru_id' => 3, 'kelas' => '10 IPA', 'mata_pelajaran' => 'Fisika', 'hari' => 'Kamis', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00'],
            ['guru_id' => 4, 'kelas' => '10 IPS', 'mata_pelajaran' => 'Sejarah', 'hari' => 'Kamis', 'jam_mulai' => '12:30:00', 'jam_selesai' => '14:00:00'],
            
            // Jumat
            ['guru_id' => 5, 'kelas' => '10 IPA', 'mata_pelajaran' => 'Kimia', 'hari' => 'Jumat', 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00'],
            ['guru_id' => 6, 'kelas' => '10 IPS', 'mata_pelajaran' => 'Ekonomi', 'hari' => 'Jumat', 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00'],
            ['guru_id' => 1, 'kelas' => '11 IPA', 'mata_pelajaran' => 'Matematika', 'hari' => 'Jumat', 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00'],
        ];

        foreach ($jadwals as $jadwal) {
            Jadwal::create($jadwal);
        }
    }
}