<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateGuruExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Pak Budi Santoso', 'Matematika', '10 IPA'],
            ['Bu Siti Aisyah', 'Bahasa Indonesia', '10 IPS'],
            ['Pak Joko Widodo', 'Fisika', '11 IPA'],
            ['Bu Rina Wati', 'Sejarah', '11 IPS'],
            ['Pak Anto Wijaya', 'Kimia', '12 IPA'],
        ];
    }

    public function headings(): array
    {
        return ['nama', 'mata_pelajaran', 'kelas'];
    }
}
