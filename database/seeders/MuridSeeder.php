<?php

namespace Database\Seeders;

use App\Models\Murid;
use Illuminate\Database\Seeder;

class MuridSeeder extends Seeder
{
    public function run()
    {
        $murids = [
            // Kelas 10 IPA
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@student.com', 'kelas' => '10 IPA', 'is_active' => true],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nur@student.com', 'kelas' => '10 IPA', 'is_active' => true],
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@student.com', 'kelas' => '10 IPA', 'is_active' => true],
            ['name' => 'Dewi Lestari', 'email' => 'dewi.lestari@student.com', 'kelas' => '10 IPA', 'is_active' => true],
            ['name' => 'Rizki Pratama', 'email' => 'rizki.pratama@student.com', 'kelas' => '10 IPA', 'is_active' => true],
            
            // Kelas 10 IPS
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@student.com', 'kelas' => '10 IPS', 'is_active' => true],
            ['name' => 'Maya Sari', 'email' => 'maya.sari@student.com', 'kelas' => '10 IPS', 'is_active' => true],
            ['name' => 'Doni Setiawan', 'email' => 'doni.setiawan@student.com', 'kelas' => '10 IPS', 'is_active' => true],
            ['name' => 'Rina Wati', 'email' => 'rina.wati@student.com', 'kelas' => '10 IPS', 'is_active' => true],
            
            // Kelas 11 IPA
            ['name' => 'Fajar Ramadhan', 'email' => 'fajar.ramadhan@student.com', 'kelas' => '11 IPA', 'is_active' => true],
            ['name' => 'Indah Permata', 'email' => 'indah.permata@student.com', 'kelas' => '11 IPA', 'is_active' => true],
            ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@student.com', 'kelas' => '11 IPA', 'is_active' => true],
            ['name' => 'Lina Marlina', 'email' => 'lina.marlina@student.com', 'kelas' => '11 IPA', 'is_active' => true],
            
            // Kelas 11 IPS
            ['name' => 'Agus Salim', 'email' => 'agus.salim@student.com', 'kelas' => '11 IPS', 'is_active' => true],
            ['name' => 'Putri Ayu', 'email' => 'putri.ayu@student.com', 'kelas' => '11 IPS', 'is_active' => true],
            ['name' => 'Rudi Hartono', 'email' => 'rudi.hartono@student.com', 'kelas' => '11 IPS', 'is_active' => true],
            
            // Kelas 12 IPA
            ['name' => 'Bambang Susilo', 'email' => 'bambang.susilo@student.com', 'kelas' => '12 IPA', 'is_active' => true],
            ['name' => 'Citra Dewi', 'email' => 'citra.dewi@student.com', 'kelas' => '12 IPA', 'is_active' => true],
            ['name' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@student.com', 'kelas' => '12 IPA', 'is_active' => true],
            
            // Kelas 12 IPS
            ['name' => 'Fitri Handayani', 'email' => 'fitri.handayani@student.com', 'kelas' => '12 IPS', 'is_active' => true],
            ['name' => 'Gilang Ramadhan', 'email' => 'gilang.ramadhan@student.com', 'kelas' => '12 IPS', 'is_active' => true],
            ['name' => 'Hani Safitri', 'email' => 'hani.safitri@student.com', 'kelas' => '12 IPS', 'is_active' => true],
        ];

        foreach ($murids as $murid) {
            Murid::create($murid);
        }
    }
}