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
            
            // Regular users only see events they're assigned to
            if ($user) {
                $eventIds = $user->events()->pluck('events.id');
                $builder->whereIn('event_id', $eventIds);
            } else {
                // Not authenticated - see nothing
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
