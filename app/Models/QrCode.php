<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QrCode extends Model
{
    protected $fillable = [
        'nama',
        'tipe',
        'kelas',
        'code',
        'qr_image',
        'is_active',
        'berlaku_dari',
        'berlaku_sampai',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = Str::random(32);
            }
        });
    }
}
