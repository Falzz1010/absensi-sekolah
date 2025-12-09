<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'nama_sekolah',
                'value' => 'SMA Negeri 1',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Nama Sekolah',
                'description' => 'Nama sekolah yang akan ditampilkan di sistem',
            ],
            [
                'key' => 'jam_masuk',
                'value' => '07:00',
                'type' => 'time',
                'group' => 'jadwal',
                'label' => 'Jam Masuk',
                'description' => 'Jam masuk sekolah',
            ],
            [
                'key' => 'jam_pulang',
                'value' => '15:00',
                'type' => 'time',
                'group' => 'jadwal',
                'label' => 'Jam Pulang',
                'description' => 'Jam pulang sekolah',
            ],
            [
                'key' => 'batas_waktu_absensi',
                'value' => '07:30',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Batas Waktu Absensi',
                'description' => 'Batas waktu untuk absensi, setelah ini dianggap terlambat',
            ],
            [
                'key' => 'toleransi_terlambat',
                'value' => '15',
                'type' => 'number',
                'group' => 'absensi',
                'label' => 'Toleransi Keterlambatan',
                'description' => 'Toleransi keterlambatan dalam menit',
            ],
            [
                'key' => 'check_in_start',
                'value' => '06:00:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Waktu Mulai Check-in',
                'description' => 'Waktu paling awal untuk melakukan check-in',
            ],
            [
                'key' => 'check_in_end',
                'value' => '08:00:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Waktu Akhir Check-in',
                'description' => 'Waktu paling akhir untuk melakukan check-in',
            ],
            [
                'key' => 'late_threshold',
                'value' => '07:30:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Batas Waktu Terlambat',
                'description' => 'Batas waktu sebelum dianggap terlambat',
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
