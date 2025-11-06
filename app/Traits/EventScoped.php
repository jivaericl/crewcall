<?php

namespace App\Traits;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

trait EventScoped
{
    /**
     * Boot the event scoped trait for a model.
     */
    protected static function bootEventScoped()
    {
        // Apply global scope to all queries
        static::addGlobalScope('event_scoped', function (Builder $builder) {
            $user = auth()->user();
            
            // Super admins see everything
            if ($user && $user->is_super_admin) {
                return;
            }
            
            // Regular users only see events they're assigned to OR created
            if ($user) {
                $eventIds = $user->events()->pluck('events.id');
                $builder->where(function($q) use ($eventIds, $user, $builder) {
                    // Check if model has event_id column
                    $model = $builder->getModel();
                    $table = $model->getTable();
                    
                    if ($model->getConnection()->getSchemaBuilder()->hasColumn($table, 'event_id')) {
                        // Model has direct event_id column
                        $q->whereIn('event_id', $eventIds)
                          ->orWhereHas('event', function($eq) use ($user) {
                              $eq->where('events.created_by', $user->id);
                          });
                    } else {
                        // Model doesn't have event_id, use event relationship
                        $q->whereHas('event', function($eq) use ($eventIds, $user) {
                            $eq->whereIn('events.id', $eventIds)
                               ->orWhere('events.created_by', $user->id);
                        });
                    }
                });
            } else {
                // Not authenticated - see nothing
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
