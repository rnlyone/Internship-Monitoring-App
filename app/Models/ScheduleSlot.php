<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduleSlot extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'start_shift',
        'end_shift',
        'caption',
        'status',
        'approval_status',
        'assigned_by',
        'pending_start',
        'pending_end',
        'pending_caption',
        'reschedule_status',
    ];

    protected function casts(): array
    {
        return [
            'start_shift'   => 'datetime',
            'end_shift'     => 'datetime',
            'pending_start' => 'datetime',
            'pending_end'   => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isAssigned(): bool
    {
        return $this->assigned_by !== null;
    }

    public function presenceStamps()
    {
        return $this->hasMany(PresenceStamp::class);
    }

    public function entryStamp()
    {
        return $this->hasOne(PresenceStamp::class)->where('type', 'entry');
    }

    public function exitStamp()
    {
        return $this->hasOne(PresenceStamp::class)->where('type', 'exit');
    }

    public function shiftLogbooks()
    {
        return $this->hasMany(ShiftLogbook::class);
    }

    /**
     * Duration in minutes.
     */
    public function getDurationMinutesAttribute(): float
    {
        return $this->start_shift->diffInMinutes($this->end_shift);
    }

    /**
     * Duration in hours (decimal).
     */
    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_minutes / 60, 2);
    }
}
