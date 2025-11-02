<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable()
    {
        // Log when a model is created
        static::created(function ($model) {
            $model->auditCreated();
        });

        // Log when a model is updated
        static::updated(function ($model) {
            $model->auditUpdated();
        });

        // Log when a model is deleted
        static::deleted(function ($model) {
            $model->auditDeleted();
        });

        // Log when a soft-deleted model is restored
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->auditRestored();
            });
        }
    }

    /**
     * Get the attributes that should be audited.
     * Override this in your model to exclude certain attributes.
     */
    public function getAuditableAttributes(): array
    {
        // By default, audit all fillable attributes
        if (property_exists($this, 'auditable')) {
            return $this->auditable;
        }

        // Exclude common attributes that don't need auditing
        $exclude = ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];
        
        return array_diff($this->fillable, $exclude);
    }

    /**
     * Get the old values for auditing.
     */
    protected function getOldAuditValues(): array
    {
        $attributes = $this->getAuditableAttributes();
        $values = [];

        foreach ($attributes as $attribute) {
            if (isset($this->original[$attribute])) {
                $values[$attribute] = $this->original[$attribute];
            }
        }

        return $values;
    }

    /**
     * Get the new values for auditing.
     */
    protected function getNewAuditValues(): array
    {
        $attributes = $this->getAuditableAttributes();
        $values = [];

        foreach ($attributes as $attribute) {
            if (isset($this->attributes[$attribute])) {
                $values[$attribute] = $this->attributes[$attribute];
            }
        }

        return $values;
    }

    /**
     * Create an audit log entry.
     */
    protected function createAuditLog(string $event, ?array $oldValues = null, ?array $newValues = null): void
    {
        AuditLog::create([
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'event' => $event,
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Audit the creation of a model.
     */
    protected function auditCreated(): void
    {
        $this->createAuditLog('created', null, $this->getNewAuditValues());
    }

    /**
     * Audit the update of a model.
     */
    protected function auditUpdated(): void
    {
        // Only log if there are actual changes to auditable attributes
        $oldValues = $this->getOldAuditValues();
        $newValues = $this->getNewAuditValues();

        $changes = array_diff_assoc($newValues, $oldValues);

        if (!empty($changes)) {
            $this->createAuditLog('updated', $oldValues, $newValues);
        }
    }

    /**
     * Audit the deletion of a model.
     */
    protected function auditDeleted(): void
    {
        $this->createAuditLog('deleted', $this->getOldAuditValues(), null);
    }

    /**
     * Audit the restoration of a soft-deleted model.
     */
    protected function auditRestored(): void
    {
        $this->createAuditLog('restored', null, $this->getNewAuditValues());
    }

    /**
     * Get all audit logs for this model.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderBy('created_at', 'desc');
    }
}
