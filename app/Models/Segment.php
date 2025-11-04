<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Commentable;
use App\Traits\EventScoped;

class Segment extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable, EventScoped;

    protected $fillable = [
        'session_id',
        'name',
        'code',
        'start_time',
        'end_time',
        'producer_id',
        'client_id',
        'sort_order',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($segment) {
            $segment->created_by = auth()->id();
            $segment->updated_by = auth()->id();
            
            // Auto-set sort_order if not provided
            if ($segment->sort_order === 0 || $segment->sort_order === null) {
                $maxOrder = static::where('session_id', $segment->session_id)->max('sort_order');
                $segment->sort_order = ($maxOrder ?? -1) + 1;
            }
        });

        static::updating(function ($segment) {
            $segment->updated_by = auth()->id();
        });
    }

    // Relationships
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function producer()
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function cues()
    {
        return $this->hasMany(Cue::class);
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('start_time', 'asc');
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Helper methods
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        $diff = $start->diff($end);
        
        $parts = [];
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' ' . ($diff->h === 1 ? 'hour' : 'hours');
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . ' ' . ($diff->i === 1 ? 'minute' : 'minutes');
        }
        
        return !empty($parts) ? implode(', ', $parts) : '0 minutes';
    }
}
