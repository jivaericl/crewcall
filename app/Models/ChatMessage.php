<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'user_id',
        'message_type',
        'message',
        'metadata',
        'is_pinned',
        'is_broadcast',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_pinned' => 'boolean',
        'is_broadcast' => 'boolean',
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
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeBroadcast($query)
    {
        return $query->where('is_broadcast', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Helper methods
    public function getTypeColorAttribute()
    {
        return match($this->message_type) {
            'announcement' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'system' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
            default => '',
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->message_type) {
            'announcement' => '📢',
            'system' => '⚙️',
            default => '💬',
        };
    }
}
