<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            ['nama' => 'X IPA 1', 'tingkat' => '10', 'jurusan' => 'IPA', 'nomor_kelas' => 1, 'wali_kelas_id' => 1, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'X IPA 2', 'tingkat' => '10', 'jurusan' => 'IPA', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'X IPS 1', 'tingkat' => '10', 'jurusan' => 'IPS', 'nomor_kelas' => 1, 'wali_kelas_id' => 2, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'X IPS 2', 'tingkat' => '10', 'jurusan' => 'IPS', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
            
            ['nama' => 'XI IPA 1', 'tingkat' => '11', 'jurusan' => 'IPA', 'nomor_kelas' => 1, 'wali_kelas_id' => 3, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XI IPA 2', 'tingkat' => '11', 'jurusan' => 'IPA', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XI IPS 1', 'tingkat' => '11', 'jurusan' => 'IPS', 'nomor_kelas' => 1, 'wali_kelas_id' => 4, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XI IPS 2', 'tingkat' => '11', 'jurusan' => 'IPS', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
            
            ['nama' => 'XII IPA 1', 'tingkat' => '12', 'jurusan' => 'IPA', 'nomor_kelas' => 1, 'wali_kelas_id' => 5, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XII IPA 2', 'tingkat' => '12', 'jurusan' => 'IPA', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XII IPS 1', 'tingkat' => '12', 'jurusan' => 'IPS', 'nomor_kelas' => 1, 'wali_kelas_id' => 6, 'kapasitas' => 30, 'is_active' => true],
            ['nama' => 'XII IPS 2', 'tingkat' => '12', 'jurusan' => 'IPS', 'nomor_kelas' => 2, 'wali_kelas_id' => null, 'kapasitas' => 30, 'is_active' => true],
        ];

        foreach ($kelas as $k) {
            \App\Models\Kelas::create($k);
        }
    }
}
