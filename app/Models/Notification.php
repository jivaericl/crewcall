<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'cue_change' => '🎬',
            'session_start' => '🎭',
            'announcement' => '📢',
            'mention' => '@',
            'assignment' => '👤',
            default => '🔔',
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'cue_change' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'session_start' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'announcement' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'mention' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'assignment' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };
    }
}
