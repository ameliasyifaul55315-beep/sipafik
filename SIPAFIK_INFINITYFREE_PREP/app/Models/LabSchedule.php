<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabSchedule extends Model
{
    protected $fillable = [
        'campus_id',
        'laboratory_id',
        'lecturer_id',
        'course_name',
        'class_name',
        'schedule_date',
        'start_time',
        'end_time',
        'schedule_type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }
}
