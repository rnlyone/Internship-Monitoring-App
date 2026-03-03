<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PresenceStamp extends Model
{
    use HasUuids;

    protected $fillable = [
        'schedule_slot_id',
        'stamped_at',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'stamped_at' => 'datetime',
        ];
    }

    public function scheduleSlot()
    {
        return $this->belongsTo(ScheduleSlot::class);
    }
}
