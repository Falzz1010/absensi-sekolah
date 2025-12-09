<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QrCode;
use App\Models\Kelas;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        // QR Code Global
        QrCode::create([
            'nama' => 'QR Global Sekolah',
            'tipe' => 'global',
            'is_active' => true,
            'berlaku_dari' => now(),
            'keterangan' => 'QR Code untuk absensi semua kelas',
        ]);

        // QR Code per Kelas (contoh untuk beberapa kelas)
        $kelasContoh = ['X-A', 'X-B', 'XI-IPA-1', 'XII-IPA-1'];
        
        foreach ($kelasContoh as $namaKelas) {
            QrCode::create([
                'nama' => 'QR Code ' . $namaKelas,
                'tipe' => 'kelas',
                'kelas' => $namaKelas,
                'is_active' => true,
                'berlaku_dari' => now(),
                'keterangan' => 'QR Code khusus untuk kelas ' . $namaKelas,
            ]);
        }
    }
}
