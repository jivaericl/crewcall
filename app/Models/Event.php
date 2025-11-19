<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Commentable;
use App\Traits\HasCustomFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory, SoftDeletes, Auditable, Commentable, HasCustomFields;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'timezone',
        'chat_enabled',
        'chat_notifications_enabled',
        'chat_sound_enabled',
        'chat_desktop_notifications',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'chat_enabled' => 'boolean',
        'chat_notifications_enabled' => 'boolean',
        'chat_sound_enabled' => 'boolean',
        'chat_desktop_notifications' => 'boolean',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the event.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the tags associated with the event.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Get the users assigned to this event.
     */
    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot(['role_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Alias for assignedUsers() for consistency.
     */
    public function users(): BelongsToMany
    {
        return $this->assignedUsers();
    }

    /**
     * Get the roles assigned to this event.
     */
    public function assignedRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'event_user')
            ->withPivot(['user_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Check if a user is assigned to this event.
     */
    public function hasUser(User $user): bool
    {
        return $this->assignedUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is an admin for this event.
     */
    public function isAdmin(User $user): bool
    {
        // Event creator is always an admin
        if ($this->created_by === $user->id) {
            return true;
        }

        return $this->assignedUsers()
            ->where('user_id', $user->id)
            ->wherePivot('is_admin', true)
            ->exists();
    }

    /**
     * Get the sessions for this event.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Get the custom fields for this event.
     */
    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (auth()->check()) {
                $event->created_by = auth()->id();
                $event->updated_by = auth()->id();
            }
        });

        static::updating(function ($event) {
            if (auth()->check()) {
                $event->updated_by = auth()->id();
            }
        });
    }
}
