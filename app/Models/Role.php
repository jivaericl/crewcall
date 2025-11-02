<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_system',
        'is_active',
        'can_add',
        'can_edit',
        'can_view',
        'can_delete',
        'sort_order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'can_add' => 'boolean',
        'can_edit' => 'boolean',
        'can_view' => 'boolean',
        'can_delete' => 'boolean',
    ];

    /**
     * Boot the model and set up event observers.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    /**
     * Get the users assigned to this role for events.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withPivot(['event_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Get the events that have this role assigned.
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')
            ->withPivot(['user_id', 'is_admin'])
            ->withTimestamps();
    }

    /**
     * Scope to get only system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to get only active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return match($permission) {
            'add', 'create' => $this->can_add,
            'edit', 'update' => $this->can_edit,
            'view', 'read' => $this->can_view,
            'delete', 'destroy' => $this->can_delete,
            default => false,
        };
    }
}
