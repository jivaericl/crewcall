<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEventAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $eventId = $request->route('event');
        
        if (!$eventId) {
            return $next($request);
        }
        
        $user = auth()->user();
        
        // Super admins have access to all events
        if ($user && $user->is_super_admin) {
            return $next($request);
        }
        
        // Check if user has access to this event
        $event = Event::find($eventId);
        
        if (!$event) {
            abort(404, 'Event not found');
        }
        
        // Check if user is assigned to this event or is the creator
        if ($user && ($event->created_by === $user->id || $event->assignedUsers()->where('user_id', $user->id)->exists())) {
            return $next($request);
        }
        
        abort(403, 'You do not have access to this event');
    }
}
