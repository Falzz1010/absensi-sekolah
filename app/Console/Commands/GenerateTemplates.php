<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:generate';
    protected $description = 'Generate Excel templates for import';

    public function handle()
    {
        $this->info('Generating Excel templates...');

        // Template Murid
        $muridData = [
            ['nama', 'email', 'kelas', 'status'],
            ['Ahmad Fauzi', 'ahmad.fauzi@student.com', 'X IPA 1', '1'],
            ['Siti Nurhaliza', 'siti.nur@student.com', 'X IPA 1', '1'],
            ['Budi Santoso', 'budi.santoso@student.com', 'X IPS 1', '1'],
            ['Dewi Lestari', 'dewi.lestari@student.com', 'XI IPA 1', '1'],
            ['Rizki Pratama', 'rizki.pratama@student.com', 'XI IPS 1', '1'],
        ];

        $muridExport = new \Maatwebsite\Excel\Concerns\FromArray();
        \Maatwebsite\Excel\Facades\Excel::store(
            new class($muridData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function array(): array { return $this->data; }
            },
            'templates/template_murid.xlsx',
            'public'
        );

        // Template Guru
        $guruData = [
            ['nama', 'mata_pelajaran', 'kelas'],
            ['Pak Budi Santoso', 'Matematika', '10 IPA'],
            ['Bu Siti Aisyah', 'Bahasa Indonesia', '10 IPS'],
            ['Pak Joko Widodo', 'Fisika', '11 IPA'],
            ['Bu Rina Wati', 'Sejarah', '11 IPS'],
        ];

        \Maatwebsite\Excel\Facades\Excel::store(
            new class($guruData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function array(): array { return $this->data; }
            },
            'templates/template_guru.xlsx',
            'public'
        );

        $this->info('✓ Template Murid: public/templates/template_murid.xlsx');
        $this->info('✓ Template Guru: public/templates/template_guru.xlsx');
        $this->info('Templates generated successfully!');
    }
}
