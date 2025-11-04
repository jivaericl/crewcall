<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventSelector extends Component
{
    public $selectedEventId;
    public $events;
    public $showDropdown = false;

    public function mount()
    {
        // Get selected event from session or default to first event
        $this->selectedEventId = session('selected_event_id');
        
        if (!$this->selectedEventId) {
            $firstEvent = Event::whereHas('assignedUsers', function ($query) {
                $query->where('user_id', auth()->id());
            })->orWhere('created_by', auth()->id())->first();
            
            if ($firstEvent) {
                $this->selectedEventId = $firstEvent->id;
                session(['selected_event_id' => $firstEvent->id]);
            }
        }
    }

    public function selectEvent($eventId)
    {
        $this->selectedEventId = $eventId;
        session(['selected_event_id' => $eventId]);
        $this->showDropdown = false;
        
        // Dispatch event for other components to listen
        $this->dispatch('eventChanged', eventId: $eventId);
        
        // Redirect to event sessions or dashboard
        return redirect()->route('events.sessions.index', $eventId);
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function render()
    {
        // Get events user has access to
        $this->events = Event::whereHas('assignedUsers', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->orWhere('created_by', auth()->id())
        ->orderBy('start_date', 'desc')
        ->get();

        $selectedEvent = null;
        if ($this->selectedEventId) {
            $selectedEvent = Event::find($this->selectedEventId);
        }

        return view('livewire.event-selector', [
            'selectedEvent' => $selectedEvent,
        ]);
    }
}
