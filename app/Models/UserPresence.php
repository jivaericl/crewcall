<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserPresence extends Model
{
    use HasFactory;

    protected $table = 'user_presence';

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'current_page',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
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

    public function scopeOnline($query)
    {
        return $query->where('status', 'online')
                    ->where('last_seen_at', '>=', now()->subMinutes(5));
    }

    public function scopeAway($query)
    {
        return $query->where('status', 'away');
    }

    // Helper methods
    public function updatePresence($status = 'online', $currentPage = null)
    {
        $this->update([
            'status' => $status,
            'current_page' => $currentPage,
            'last_seen_at' => now(),
        ]);
    }

    public function isOnline()
    {
        return $this->status === 'online' && 
               $this->last_seen_at >= now()->subMinutes(5);
    }

    public function getStatusColorAttribute()
    {
        if ($this->isOnline()) {
            return 'bg-green-500';
        } elseif ($this->status === 'away') {
            return 'bg-yellow-500';
        }
        return 'bg-gray-400';
    }

    public function getStatusTextAttribute()
    {
        if ($this->isOnline()) {
            return 'Online';
        } elseif ($this->status === 'away') {
            return 'Away';
        }
        return 'Offline';
    }

    public function getLastSeenTextAttribute()
    {
        if ($this->isOnline()) {
            return 'Active now';
        }
        return 'Last seen ' . $this->last_seen_at->diffForHumans();
    }
}
