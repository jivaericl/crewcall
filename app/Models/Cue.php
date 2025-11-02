<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\Commentable;

class Cue extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable;

    protected $fillable = [
        'segment_id',
        'cue_type_id',
        'name',
        'code',
        'description',
        'time',
        'status',
        'notes',
        'filename',
        'operator_id',
        'priority',
        'sort_order',
    ];

    protected $casts = [
        'time' => 'datetime:H:i',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cue) {
            $cue->created_by = auth()->id();
            $cue->updated_by = auth()->id();
            
            // Auto-set sort_order if not provided
            if ($cue->sort_order === 0 || $cue->sort_order === null) {
                $maxOrder = static::where('segment_id', $cue->segment_id)->max('sort_order');
                $cue->sort_order = ($maxOrder ?? -1) + 1;
            }
        });

        static::updating(function ($cue) {
            $cue->updated_by = auth()->id();
        });
    }

    // Relationships
    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function cueType()
    {
        return $this->belongsTo(CueType::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
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

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('time', 'asc');
    }

    public function scopeForSegment($query, $segmentId)
    {
        return $query->where('segment_id', $segmentId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Helper methods
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'standby' => 'gray',
            'go' => 'green',
            'complete' => 'blue',
            'skip' => 'red',
            default => 'gray',
        };
    }

    public function getPriorityBadgeColorAttribute()
    {
        return match($this->priority) {
            'low' => 'gray',
            'normal' => 'blue',
            'high' => 'amber',
            'critical' => 'red',
            default => 'blue',
        };
    }
}
