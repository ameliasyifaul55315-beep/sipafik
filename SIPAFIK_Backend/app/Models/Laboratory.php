<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratory extends Model
{
    protected $fillable = [
        'campus_id',
        'code',
        'name',
        'location',
        'description',
        'capacity',
        'facilities',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'facilities' => 'array',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(LabSchedule::class);
    }

    public function facilityRequests(): HasMany
    {
        return $this->hasMany(FacilityRequest::class);
    }
}