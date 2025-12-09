<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateMuridExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Ahmad Fauzi', 'ahmad.fauzi@student.com', 'X IPA 1', '1'],
            ['Siti Nurhaliza', 'siti.nur@student.com', 'X IPA 1', '1'],
            ['Budi Santoso', 'budi.santoso@student.com', 'X IPS 1', '1'],
            ['Dewi Lestari', 'dewi.lestari@student.com', 'XI IPA 1', '1'],
            ['Rizki Pratama', 'rizki.pratama@student.com', 'XI IPS 1', '1'],
        ];
    }

    public function headings(): array
    {
        return ['nama', 'email', 'kelas', 'status'];
    }
}
