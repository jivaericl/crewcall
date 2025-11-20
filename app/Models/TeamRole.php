<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'color',
        'sort_order',
    ];

    /**
     * Get the event that owns this role
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the users assigned to this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user_roles')
            ->withPivot('event_id')
            ->withTimestamps();
    }

    /**
     * Get the cue types that use this role as default operator
     */
    public function cueTypes(): BelongsToMany
    {
        return $this->belongsToMany(CueType::class, 'cue_type_team_role')
            ->withTimestamps();
    }
}
