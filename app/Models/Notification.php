<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'url',
        'related_type',
        'related_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a notification for a specific user.
     */
    public static function notify(int $userId, string $type, array $data): self
    {
        return static::create([
            'user_id'      => $userId,
            'type'         => $type,
            'title'        => $data['title'],
            'message'      => $data['message'],
            'url'          => $data['url'] ?? null,
            'related_type' => $data['related_type'] ?? null,
            'related_id'   => $data['related_id'] ?? null,
        ]);
    }

    /**
     * Create a notification for all admin users (excluding the current user).
     */
    public static function notifyAdmins(string $type, array $data): void
    {
        $currentId = Auth::id();

        User::where('role', 'admin')
            ->when($currentId, fn($q) => $q->where('id', '!=', $currentId))
            ->each(fn($admin) => static::notify($admin->id, $type, $data));
    }
}
