<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Murid;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $murids = Murid::all();
        $statuses = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Sakit', 'Izin', 'Alfa']; // Lebih banyak hadir
        
        // Generate absensi untuk 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            
            foreach ($murids as $murid) {
                // Random status dengan probabilitas lebih tinggi untuk hadir
                $status = $statuses[array_rand($statuses)];
                
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => $tanggal,
                    'status' => $status,
                    'kelas' => $murid->kelas,
                ]);
            }
        }
    }
}
