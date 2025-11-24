<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarItem;
use App\Models\Session;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getEvents(Request $request, $eventId)
    {
        // Verify user has access to this event
        $event = Event::findOrFail($eventId);
        
        // Get date range from request (FullCalendar sends start and end)
        $start = $request->input('start');
        $end = $request->input('end');
        
        $events = [];
        
        // Get calendar items
        $calendarItems = CalendarItem::forEvent($eventId)
            ->active()
            ->when($start && $end, function($query) use ($start, $end) {
                $query->betweenDates($start, $end);
            })
            ->with(['users', 'speakers', 'tags'])
            ->get();
        
        foreach ($calendarItems as $item) {
            $events[] = [
                'id' => 'calendar-' . $item->id,
                'title' => $item->title,
                'start' => $item->start_date->format('Y-m-d\TH:i:s'),
                'end' => $item->end_date->format('Y-m-d\TH:i:s'),
                'allDay' => $item->all_day,
                'backgroundColor' => $item->type_color,
                'borderColor' => $item->type_color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => $item->type,
                    'description' => $item->description,
                    'location' => $item->location,
                    'attendees' => $item->users->pluck('name')->toArray(),
                    'speakers' => $item->speakers->pluck('name')->toArray(),
                    'tags' => $item->tags->pluck('name')->toArray(),
                    'url' => route('events.calendar.show', [$eventId, $item->id]),
                ],
            ];
        }
        
        // Get sessions
        $sessions = Session::where('event_id', $eventId)
            ->when($start && $end, function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();
        
        foreach ($sessions as $session) {
            $events[] = [
                'id' => 'session-' . $session->id,
                'title' => '📅 ' . $session->name,
                'start' => $session->start_date ? Carbon::parse($session->start_date)->format('Y-m-d\TH:i:s') : null,
                'end' => $session->end_date ? Carbon::parse($session->end_date)->format('Y-m-d\TH:i:s') : null,
                'backgroundColor' => '#8B5CF6', // Purple for sessions
                'borderColor' => '#8B5CF6',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'session',
                    'description' => $session->description,
                    'url' => route('events.sessions.show', [$eventId, $session->id]),
                ],
            ];
        }
        
        return response()->json($events);
    }
    
    public function updateEvent(Request $request, $eventId, $calendarItemId)
    {
        $calendarItem = CalendarItem::findOrFail($calendarItemId);
        
        // Verify calendar item belongs to this event
        if ($calendarItem->event_id != $eventId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Update dates from drag-and-drop
        if ($request->has('start')) {
            $calendarItem->start_date = Carbon::parse($request->input('start'));
        }
        
        if ($request->has('end')) {
            $calendarItem->end_date = Carbon::parse($request->input('end'));
        }
        
        $calendarItem->updated_by = auth()->id();
        $calendarItem->save();
        
        return response()->json(['success' => true, 'message' => 'Calendar item updated']);
    }
}
