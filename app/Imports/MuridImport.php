<?php

namespace App\Imports;

use App\Models\Murid;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MuridImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Cari kelas berdasarkan nama
        $kelas = Kelas::where('nama', $row['kelas'])->first();
        
        return new Murid([
            'name' => $row['nama'],
            'email' => $row['email'],
            'kelas' => $row['kelas'],
            'kelas_id' => $kelas?->id,
            'is_active' => $row['status'] ?? true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string',
            'email' => 'required|email|unique:murids,email',
            'kelas' => 'required|string',
        ];
    }
}
