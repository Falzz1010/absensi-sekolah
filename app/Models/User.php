<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access a Filament panel.
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if ($panel->getId() === 'student') {
            // ONLY students with Murid record can access student panel
            // Admin and Guru should NOT access this panel
            return $this->hasAnyRole(['student', 'murid'])
                && !$this->hasAnyRole(['admin', 'guru'])
                && \App\Models\Murid::where('user_id', $this->id)->exists();
        }

        if ($panel->getId() === 'admin') {
            // Admin panel - ONLY admin or guru can access
            // Explicitly block students/murid
            if ($this->hasAnyRole(['student', 'murid'])) {
                return false;
            }
            return $this->hasAnyRole(['admin', 'guru']);
        }

        // Default: deny access
        return false;
    }

    /**
     * Get the murid record associated with this user.
     */
    public function murid()
    {
        return $this->hasOne(\App\Models\Murid::class, 'user_id');
    }
}