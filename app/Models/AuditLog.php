<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model (polymorphic).
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get a human-readable description of the change.
     */
    public function getDescriptionAttribute(): string
    {
        $modelName = class_basename($this->auditable_type);
        $userName = $this->user ? $this->user->name : 'System';
        
        return match($this->event) {
            'created' => "{$userName} created {$modelName} #{$this->auditable_id}",
            'updated' => "{$userName} updated {$modelName} #{$this->auditable_id}",
            'deleted' => "{$userName} deleted {$modelName} #{$this->auditable_id}",
            'restored' => "{$userName} restored {$modelName} #{$this->auditable_id}",
            default => "{$userName} performed {$this->event} on {$modelName} #{$this->auditable_id}",
        };
    }

    /**
     * Get the changes made in this audit log.
     */
    public function getChangesAttribute(): array
    {
        if ($this->event === 'created') {
            return $this->new_values ?? [];
        }

        if ($this->event === 'deleted') {
            return $this->old_values ?? [];
        }

        // For updates, show what changed
        $changes = [];
        $old = $this->old_values ?? [];
        $new = $this->new_values ?? [];

        foreach ($new as $key => $value) {
            if (!isset($old[$key]) || $old[$key] !== $value) {
                $changes[$key] = [
                    'old' => $old[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }
}
