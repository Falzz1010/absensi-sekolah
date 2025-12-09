<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumumen';

    protected $fillable = [
        'judul',
        'isi',
        'prioritas',
        'target',
        'kelas_target',
        'created_by',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTargetKelasArray(): array
    {
        if (empty($this->kelas_target)) {
            return [];
        }
        return explode(',', $this->kelas_target);
    }
}
