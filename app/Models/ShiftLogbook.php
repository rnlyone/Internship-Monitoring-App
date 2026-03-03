<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ShiftLogbook extends Model
{
    use HasUuids;

    protected $fillable = [
        'schedule_slot_id',
        'content',
    ];

    public function scheduleSlot()
    {
        return $this->belongsTo(ScheduleSlot::class);
    }
}
