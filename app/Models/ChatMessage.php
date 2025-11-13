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
        'recipient_id',
        'is_direct_message',
        'message_type',
        'message',
        'mentions',
        'metadata',
        'is_pinned',
        'is_broadcast',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'mentions' => 'array',
        'is_pinned' => 'boolean',
        'is_broadcast' => 'boolean',
        'is_direct_message' => 'boolean',
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

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function mentionedUsers()
    {
        if (!$this->mentions) return collect();
        return User::whereIn('id', $this->mentions)->get();
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

    public function scopeDirectMessages($query)
    {
        return $query->where('is_direct_message', true);
    }

    public function scopeForConversation($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where(function($q2) use ($userId1, $userId2) {
                $q2->where('user_id', $userId1)->where('recipient_id', $userId2);
            })->orWhere(function($q2) use ($userId1, $userId2) {
                $q2->where('user_id', $userId2)->where('recipient_id', $userId1);
            });
        })->where('is_direct_message', true);
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
