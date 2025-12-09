<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $fillable = [
        'murid_id', 
        'tanggal', 
        'status', 
        'kelas', 
        'keterangan',
        'proof_document',
        'verification_status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'check_in_time',
        'is_late',
        'late_duration',
        'qr_scan_done',
        'qr_scan_time',
        'manual_checkin_done',
        'manual_checkin_time',
        'is_complete',
        'first_method'
    ];

    protected $casts = [
        'is_late' => 'boolean',
        'verified_at' => 'datetime',
        'tanggal' => 'date',
        'qr_scan_done' => 'boolean',
        'qr_scan_time' => 'datetime',
        'manual_checkin_done' => 'boolean',
        'manual_checkin_time' => 'datetime',
        'is_complete' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function murid()
    {
        return $this->belongsTo(Murid::class, 'murid_id');
    }
    public function collection()
    {
        return Absensi::select('id', 'murid_id', 'tanggal', 'status')->get();
    }
    public function headings(): array
    {
        return ['ID', 'Nama Murid', 'Tanggal', 'Status'];
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isLate(): bool
    {
        return $this->is_late;
    }

    public function getLateDuration(): ?int
    {
        return $this->late_duration;
    }

    public function hasProof(): bool
    {
        return !empty($this->proof_document);
    }

    /**
     * Scope a query to only include attendance records for a specific student.
     */
    public function scopeForStudent($query, int $muridId)
    {
        return $query->where('murid_id', $muridId);
    }
}