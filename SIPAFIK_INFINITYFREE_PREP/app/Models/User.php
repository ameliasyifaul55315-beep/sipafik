<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Campus;

class User extends Authenticatable
{
   public function campus()
{
    return $this->belongsTo(\App\Models\Campus::class);
}
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'campus_id',
'username',
'role',
'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    public function facilityRequests(): HasMany
    {
        return $this->hasMany(FacilityRequest::class);
    }

    public function labSchedules(): HasMany
    {
        return $this->hasMany(LabSchedule::class, 'lecturer_id');
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public const ROLE_LABELS = [
        'super_admin' => 'Super Admin',
        'admin_kampus' => 'Admin Kampus',
        'organisasi' => 'Organisasi',
        'dosen' => 'Dosen',
        'petugas_fasilitas' => 'Petugas Fasilitas',
        'mahasiswa' => 'Mahasiswa',
        'admin' => 'Admin (lama)',
        'petugas_laboratorium' => 'Petugas Laboratorium (lama)',
    ];

    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst(str_replace('_', ' ', (string) $this->role));
    }

    public function isFacilityManager(): bool
    {
        return in_array($this->role, ['admin_kampus', 'petugas_fasilitas'], true);
    }

    public function isEventManager(): bool
    {
        return in_array($this->role, ['admin_kampus', 'organisasi', 'dosen'], true);
    }
}
