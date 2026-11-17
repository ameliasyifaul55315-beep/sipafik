<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityRequest extends Model
{
    protected $fillable = [
        'campus_id',
        'user_id',
        'laboratory_id',
        'approved_by',
        'title',
        'purpose',
        'request_date',
        'start_time',
        'end_time',
        'participants_count',
        'status',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'participants_count' => 'integer',
        ];
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laboratory(): BelongsTo
    {
        return $this->belongsTo(Laboratory::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
