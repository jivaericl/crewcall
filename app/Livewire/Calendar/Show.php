<?php

namespace App\Livewire\Calendar;

use App\Models\CalendarItem;
use App\Models\Event;
use Livewire\Component;

class Show extends Component
{
    public $eventId;
    public $event;
    public $calendarItemId;
    public $calendarItem;

    public function mount($eventId, $calendarItemId)
    {
        $this->eventId = $eventId;
        $this->event = Event::findOrFail($eventId);
        
        // Set selected event in session
        session(['selected_event_id' => $this->eventId]);

        $this->calendarItemId = $calendarItemId;
        $this->calendarItem = CalendarItem::with([
            'creator',
            'updater',
            'users',
            'speakers',
            'tags'
        ])->findOrFail($calendarItemId);
        
        // Check if calendar item belongs to this event
        if ($this->calendarItem->event_id != $this->eventId) {
            abort(403);
        }
    }

    public function delete()
    {
        $this->calendarItem->delete();
        
        session()->flash('message', 'Calendar item deleted successfully.');
        
        return redirect()->route('events.calendar.index', $this->eventId);
    }

    public function render()
    {
        return view('livewire.calendar.show');
    }
}
