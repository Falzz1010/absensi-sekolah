<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'kelas', 'kelas_id', 'is_active', 'photo', 'qr_code_id', 'user_id'];
    
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'murid_id');
    }

    public function kelasRelation()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id');
    }

    public function notifications()
    {
        return $this->hasMany(StudentNotification::class, 'murid_id');
    }
}