<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'event_id',
        'name',
        'color',
        'model_type', // 'event', 'session', 'segment', 'cue'
    ];

    /**
     * Get the events associated with the tag.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withTimestamps();
    }

    /**
     * Get the sessions associated with the tag.
     */
    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(Session::class)->withTimestamps();
    }

    /**
     * Get the segments associated with the tag.
     */
    public function segments(): BelongsToMany
    {
        return $this->belongsToMany(Segment::class)->withTimestamps();
    }

    /**
     * Get the cues associated with the tag.
     */
    public function cues(): BelongsToMany
    {
        return $this->belongsToMany(Cue::class)->withTimestamps();
    }

    /**
     * Get the speakers associated with the tag.
     */
    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class)->withTimestamps();
    }
}
