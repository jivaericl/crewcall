<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CueType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'is_system',
        'is_active',
        'event_id',
        'sort_order',
        'default_team_role_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cueType) {
            if (empty($cueType->slug)) {
                $cueType->slug = Str::slug($cueType->name);
            }
        });

        static::updating(function ($cueType) {
            if ($cueType->isDirty('name') && empty($cueType->slug)) {
                $cueType->slug = Str::slug($cueType->name);
            }
        });
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function cues()
    {
        return $this->hasMany(Cue::class);
    }

    public function defaultTeamRole()
    {
        return $this->belongsTo(TeamRole::class, 'default_team_role_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true)->whereNull('event_id');
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where(function ($q) use ($eventId) {
            $q->whereNull('event_id')
              ->orWhere('event_id', $eventId);
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }
}
