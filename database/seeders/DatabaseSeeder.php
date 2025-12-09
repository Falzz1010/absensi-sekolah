<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            TahunAjaranSeeder::class,
            MuridSeeder::class,
            JadwalSeeder::class,
            AbsensiSeeder::class,
            JamPelajaranSeeder::class,
            QrCodeSeeder::class,
        ]);
    }
}
