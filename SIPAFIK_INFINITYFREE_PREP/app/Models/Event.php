<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    public const CATEGORIES = [
        'seminar' => 'Seminar',
        'workshop' => 'Workshop',
        'pelatihan' => 'Pelatihan',
        'lomba' => 'Lomba',
        'bakti_sosial' => 'Bakti Sosial',
        'olahraga' => 'Olahraga & Seni',
        'lainnya' => 'Lainnya',
    ];

    public const STATUSES = [
        'draft' => 'Draf',
        'published' => 'Dibuka',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = [
        'campus_id',
        'organizer_id',
        'title',
        'description',
        'category',
        'location',
        'event_date',
        'start_time',
        'end_time',
        'quota',
        'visibility',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'quota' => 'integer',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /** Peserta yang masih memakai kuota (belum membatalkan). */
    public function activeRegistrations(): HasMany
    {
        return $this->registrations()->whereIn('status', ['registered', 'attended']);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        // Kegiatan kampus sendiri (semua status untuk pengelola) atau kegiatan PUBLIK kampus lain yang sudah dibuka.
        return $query->where(function (Builder $q) use ($user) {
            $q->where('campus_id', $user->campus_id)
                ->orWhere(function (Builder $q2) {
                    $q2->where('visibility', 'public')
                        ->where('status', 'published')
                        ->whereHas('campus', fn (Builder $c) => $c->where('status', 'approved'));
                });
        });
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function remainingQuota(): int
    {
        $used = $this->relationLoaded('activeRegistrations')
            ? $this->activeRegistrations->count()
            : $this->activeRegistrations()->count();

        return max(0, $this->quota - $used);
    }
}
