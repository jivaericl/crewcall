<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type',
        'title',
        'description',
        'start_date',
        'end_date',
        'all_day',
        'location',
        'color',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'all_day' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'calendar_item_user')
            ->withTimestamps();
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'calendar_item_client')
            ->withTimestamps();
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'calendar_item_speaker')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'calendar_item_tag')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    // Helper methods
    public function getTypeColorAttribute()
    {
        if ($this->color) {
            return $this->color;
        }

        // Default colors by type
        return match($this->type) {
            'milestone' => '#10B981', // Green
            'out_of_office' => '#F59E0B', // Amber
            'call' => '#3B82F6', // Blue
            default => '#6B7280', // Gray
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'milestone' => 'Production Milestone',
            'out_of_office' => 'Out of Office',
            'call' => 'Call',
            default => ucfirst($this->type),
        };
    }

    public function getDurationInMinutesAttribute()
    {
        return $this->start_date->diffInMinutes($this->end_date);
    }

    public function getFormattedDateRangeAttribute()
    {
        if ($this->all_day) {
            if ($this->start_date->isSameDay($this->end_date)) {
                return $this->start_date->format('M d, Y') . ' (All Day)';
            }
            return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y') . ' (All Day)';
        }

        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('M d, Y g:i A') . ' - ' . $this->end_date->format('g:i A');
        }

        return $this->start_date->format('M d, Y g:i A') . ' - ' . $this->end_date->format('M d, Y g:i A');
    }
}
