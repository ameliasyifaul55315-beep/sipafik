<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'address',
        'responsible_person',
        'identity_document',
        'status',
        'rejection_reason',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function laboratories(): HasMany
    {
        return $this->hasMany(Laboratory::class);
    }

    public function labSchedules(): HasMany
    {
        return $this->hasMany(LabSchedule::class);
    }

    public function facilityRequests(): HasMany
    {
        return $this->hasMany(FacilityRequest::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}