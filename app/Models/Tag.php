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
}
