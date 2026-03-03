<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanbanCard extends Model
{
    protected $fillable = [
        'title',
        'description',
        'column_name',
        'position',
        'color',
        'priority',
        'due_date',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'position' => 'integer',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
