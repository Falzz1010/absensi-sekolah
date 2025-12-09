<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class GuruImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Guru([
            'name' => $row['nama'],
            'mata_pelajaran' => $row['mata_pelajaran'],
            'kelas' => $row['kelas'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'mata_pelajaran' => 'required|string',
            'kelas' => 'required|string',
        ];
    }
}
